<?php
/**
 * 3軸のカスタムタクソノミー（対象・目的・地域）と、管理用の発信元。
 *
 * 3軸は探索画面の絞り込みに使う。発信元（担当委員会）は CMS 内部の管理項目として持ち、公開側には出さない。
 * テーマを作り替えても分類データが残るよう、テーマではなくプラグインで登録する。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Taxonomies {

	/** 絞り込みに使う軸：キー => [スラッグ, 表示名] */
	const FACETS = array(
		'target'  => array( 'nfa_target', '対象' ),
		'purpose' => array( 'nfa_purpose', '目的' ),
		'area'    => array( 'nfa_area', '地域' ),
	);

	/** 管理用（公開側に出さない） */
	const COMMITTEE = 'nfa_committee';

	const COMMITTEES = array( '1種委員会', '2種委員会', '3種委員会', '4種委員会', '女子委員会', 'シニア委員会', 'フットサル委員会', '審判委員会', '技術委員会', '医学委員会', '普及委員会', '事務局' );

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ), 5 );
	}

	public static function slug( $key ) {
		return self::FACETS[ $key ][0] ?? '';
	}

	public static function register() {
		$object_types = NFA_AIS_Settings::post_types();
		foreach ( self::FACETS as $axis ) {
			list( $slug, $label ) = $axis;
			register_taxonomy(
				$slug,
				$object_types,
				array(
					'labels'            => self::labels( $label ),
					'public'            => true,
					// 階層ありにすると投稿画面がチェックボックスになり、表記ゆれが起きにくい。
					'hierarchical'      => true,
					'show_admin_column' => true,
					'show_in_rest'      => true,
					'query_var'         => $slug,
					'rewrite'           => array( 'slug' => str_replace( '_', '-', $slug ) ),
				)
			);
		}
		register_taxonomy(
			self::COMMITTEE,
			$object_types,
			array(
				'labels'             => self::labels( '発信元（管理用）' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'hierarchical'       => true,
				'show_admin_column'  => true,
				'rewrite'            => false,
			)
		);
	}

	private static function labels( $label ) {
		return array(
			'name'          => $label,
			'singular_name' => $label,
			'menu_name'     => 'AI検索：' . $label,
			'all_items'     => $label . 'の一覧',
			'edit_item'     => $label . 'を編集',
			'add_new_item'  => $label . 'を追加',
			'search_items'  => $label . 'を検索',
		);
	}

	/**
	 * 語彙（辞書の選択肢）と同名の用語を用意する。
	 */
	public static function seed_terms() {
		foreach ( NFA_AIS_Dictionary::axes() as $ax ) {
			$tax = self::slug( $ax['key'] );
			foreach ( $ax['options'] as $o ) {
				if ( $tax && ! term_exists( $o['value'], $tax ) ) {
					wp_insert_term( $o['value'], $tax );
				}
			}
		}
		foreach ( self::COMMITTEES as $name ) {
			if ( ! term_exists( $name, self::COMMITTEE ) ) {
				wp_insert_term( $name, self::COMMITTEE );
			}
		}
	}

	/**
	 * 投稿の3軸を [axis => [term name, ...]] で返す（発信元は含めない）。
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
