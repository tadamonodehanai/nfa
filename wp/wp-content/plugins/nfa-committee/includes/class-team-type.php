<?php
/**
 * チーム（team）。「チームを探す」（/teams/）の一覧に並ぶ。
 *
 * 投稿タイプ名・URL（/team/{スラッグ}/）は旧サイトと同じにしている（旧URLからの転送が要らない）。
 * 分類：競技 team_sport / 性別 team_gender / 年代 team_age / 地域 team_area
 * 入力欄（ACF）：市町村 team_city / 活動日 team_activity_days / 活動頻度 team_activity_frequency /
 *               活動場所 team_activity_location / Webサイト team_website_url / PR team_pr_comment
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

class NFA_Com_Team_Type {

	const TYPE = 'team';

	/** 分類：スラッグ => [表示名, 初期の用語（スラッグ => 名前）] */
	public static function taxonomies() {
		return array(
			'team_area'   => array( '活動地域', array( 'hokushin' => '北信', 'toushin' => '東信', 'chushin' => '中信', 'nanshin' => '南信' ) ),
			'team_sport'  => array( '活動競技', array( 'soccer' => 'サッカー', 'futsal' => 'フットサル' ) ),
			'team_age'    => array(
				'募集年代',
				array(
					'preschool' => '未就学児',
					'elem-low'  => '小学校低学年',
					'elem-high' => '小学校高学年',
					'junior'    => '中学生年代',
					'high'      => '高校生年代',
					'adult'     => '社会人（年齢制限なし）',
					'over40'    => '40歳以上',
					'over50'    => '50歳以上',
					'over60'    => '60歳以上',
					'over70'    => '70歳以上',
				),
			),
			'team_gender' => array( '募集性別', array( 'male' => '男子', 'female' => '女子', 'both' => '両方' ) ),
		);
	}

	const DAYS = array( '月', '火', '水', '木', '金', '土', '日' );

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'init', array( __CLASS__, 'maybe_seed' ), 20 );
	}

	public static function register() {
		register_post_type(
			self::TYPE,
			array(
				'labels'          => array(
					'name'          => 'チーム',
					'singular_name' => 'チーム',
					'menu_name'     => 'チーム',
					'add_new'       => '新規追加',
					'add_new_item'  => 'チームを追加',
					'edit_item'     => 'チームを編集',
					'all_items'     => 'チームの一覧',
					'search_items'  => 'チームを検索',
					'not_found'     => '見つかりませんでした',
				),
				'public'          => true,
				'has_archive'     => false,
				'show_in_rest'    => true,
				'menu_position'   => 7,
				'menu_icon'       => 'dashicons-groups',
				'supports'        => array( 'title', 'editor', 'thumbnail', 'revisions' ),
				'capability_type' => 'page',   // 事務局（編集者）以上が管理する。委員会担当は触れない
				'map_meta_cap'    => true,
				'rewrite'         => array( 'slug' => 'team', 'with_front' => false ),
			)
		);
		foreach ( self::taxonomies() as $slug => $t ) {
			register_taxonomy(
				$slug,
				self::TYPE,
				array(
					'labels'             => array( 'name' => $t[0], 'singular_name' => $t[0], 'menu_name' => $t[0] ),
					'public'             => false,
					'show_ui'            => true,
					'show_in_rest'       => true,
					'hierarchical'       => true,
					'show_admin_column'  => true,
					'rewrite'            => false,
					'capabilities'       => array(
						'manage_terms' => 'manage_categories',
						'edit_terms'   => 'manage_categories',
						'delete_terms' => 'manage_categories',
						'assign_terms' => 'edit_pages',
					),
				)
			);
		}
	}

	public static function maybe_seed() {
		if ( get_option( 'nfa_com_team_seeded' ) === NFA_COM_VERSION ) {
			return;
		}
		foreach ( self::taxonomies() as $tax => $t ) {
			$order = 0;
			foreach ( $t[1] as $slug => $name ) {
				$order++;
				if ( ! get_term_by( 'slug', $slug, $tax ) ) {
					$r = wp_insert_term( $name, $tax, array( 'slug' => $slug ) );
					if ( ! is_wp_error( $r ) ) {
						update_term_meta( $r['term_id'], 'nfa_order', $order );
					}
				}
			}
		}
		update_option( 'nfa_com_team_seeded', NFA_COM_VERSION, false );
	}

	/**
	 * 分類の用語（並び順どおり）。
	 *
	 * @return WP_Term[]
	 */
	public static function terms( $tax ) {
		$order = array_keys( self::taxonomies()[ $tax ][1] ?? array() );
		$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) );
		$terms = is_wp_error( $terms ) ? array() : $terms;
		usort(
			$terms,
			function ( $a, $b ) use ( $order ) {
				$ia = array_search( $a->slug, $order, true );
				$ib = array_search( $b->slug, $order, true );
				return ( false === $ia ? 99 : $ia ) <=> ( false === $ib ? 99 : $ib );
			}
		);
		return $terms;
	}
}
