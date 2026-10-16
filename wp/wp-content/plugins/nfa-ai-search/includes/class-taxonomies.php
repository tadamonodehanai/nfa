<?php
/**
 * 分類の登録：担当委員会（nfa_committee）と、AI検索の3軸（対象・目的・地域）。
 *
 * 担当委員会は、権限・委員会ページ（カテゴリトップ）・TOPの日程タブ・AI検索のすべての軸になる。
 * テーマを作り替えても分類データが残るよう、テーマではなくプラグインで登録する。
 * ACF の画面ではなくコードで登録するのは、ACF が無効になっても分類が消えないようにするため。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Taxonomies {

	/** 絞り込みに使う軸：画面のキー（t/p/r）=> [スラッグ, 表示名] */
	const FACETS = array(
		't' => array( 'nfa_target', '対象' ),
		'p' => array( 'nfa_purpose', '目的' ),
		'r' => array( 'nfa_area', '地域' ),
	);

	const COMMITTEE = 'nfa_committee';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ), 5 );
		// 辞書が変わったら、足りない用語を作る
		add_action(
			'init',
			function () {
				$sig = md5( wp_json_encode( NFA_AIS_Dictionary::axes() ) );
				if ( get_option( 'nfa_ais_terms_sig' ) !== $sig ) {
					self::seed_terms();
					update_option( 'nfa_ais_terms_sig', $sig, false );
				}
			},
			20
		);
	}

	public static function slug( $key ) {
		return self::FACETS[ $key ][0] ?? '';
	}

	public static function post_types() {
		return array_values( array_unique( array_merge( NFA_AIS_Settings::post_types(), array( 'post', 'nfa_doc' ) ) ) );
	}

	public static function register() {
		$types = self::post_types();
		register_taxonomy(
			self::COMMITTEE,
			$types,
			array(
				'labels'            => self::labels( '担当委員会' ),
				'public'            => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'query_var'         => 'committee',
				// 委員会ページの URL（仮）。決まったら変更し、パーマリンク設定を保存し直す
				'rewrite'           => array( 'slug' => apply_filters( 'nfa_committee_rewrite_slug', 'committee' ), 'with_front' => false ),
				'capabilities'      => array(
					'manage_terms' => 'manage_categories',
					'edit_terms'   => 'manage_categories',
					'delete_terms' => 'manage_categories',
					'assign_terms' => 'edit_posts',
				),
			)
		);
		foreach ( self::FACETS as $axis ) {
			list( $slug, $label ) = $axis;
			register_taxonomy(
				$slug,
				$types,
				array(
					'labels'            => self::labels( $label ),
					'public'            => true,
					'publicly_queryable' => false,
					// 階層ありにすると投稿画面がチェックボックスになり、表記ゆれが起きにくい。
					'hierarchical'      => true,
					'show_admin_column' => true,
					'show_in_rest'      => true,
					'rewrite'           => false,
					'capabilities'      => array(
						'manage_terms' => 'manage_categories',
						'edit_terms'   => 'manage_categories',
						'delete_terms' => 'manage_categories',
						'assign_terms' => 'edit_posts',
					),
				)
			);
		}
	}

	private static function labels( $label ) {
		return array(
			'name'          => $label,
			'singular_name' => $label,
			'menu_name'     => $label,
			'all_items'     => $label . 'の一覧',
			'edit_item'     => $label . 'を編集',
			'add_new_item'  => $label . 'を追加',
			'search_items'  => $label . 'を検索',
		);
	}

	/**
	 * 語彙（辞書の選択肢）と同じ用語を用意する。対象・目的はスラッグ＝値、地域は名前＝値。
	 */
	public static function seed_terms() {
		foreach ( NFA_AIS_Dictionary::axes() as $ax ) {
			if ( ! taxonomy_exists( $ax['tax'] ) ) {
				continue;
			}
			foreach ( $ax['items'] as $it ) {
				if ( ! NFA_AIS_Dictionary::term( $ax['key'], $it['v'] ) ) {
					$args = preg_match( '/^[a-z0-9\-]+$/', $it['v'] ) ? array( 'slug' => $it['v'] ) : array();
					wp_insert_term( $it['l'] === $it['v'] || 'r' === $ax['key'] ? $it['v'] : $it['l'], $ax['tax'], $args );
				}
			}
		}
	}

	/**
	 * 投稿の3軸を [t => [値...], p => [...], r => [...]] で返す（値はスラッグ、地域は名前）。
	 */
	public static function values_of( $post_id ) {
		$out = array();
		foreach ( self::FACETS as $key => $axis ) {
			$terms       = get_the_terms( $post_id, $axis[0] );
			$out[ $key ] = array();
			foreach ( ( $terms && ! is_wp_error( $terms ) ) ? $terms : array() as $t ) {
				$out[ $key ][] = 'r' === $key ? $t->name : $t->slug;
			}
		}
		return $out;
	}

	/**
	 * 投稿の3軸を表示名で返す（テーマのチップ用）。
	 */
	public static function axes_of( $post_id ) {
		$out = array();
		foreach ( self::FACETS as $key => $axis ) {
			$terms       = get_the_terms( $post_id, $axis[0] );
			$out[ $key ] = ( $terms && ! is_wp_error( $terms ) ) ? wp_list_pluck( $terms, 'name' ) : array();
		}
		return $out;
	}
}
