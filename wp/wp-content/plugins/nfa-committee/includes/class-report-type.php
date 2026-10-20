<?php
/**
 * 事業計画・事業報告（nfa_report）。
 *
 * 資料1つ（2026事業計画.pdf など）を1件として登録する。「掲載年」ごとに自動でまとまり、
 * 「事業計画・事業報告」ページ（固定ページ reports）と「協会について」の DOCUMENTS（最新の2年）に並ぶ。
 * 入力欄（ACF・無料版で使える）：掲載年 nfa_report_year／ファイル nfa_report_file／注記 nfa_report_note
 * 同じ年の並び順は「順序」（ページ属性）、同じなら登録順。ファイルのない資料は「掲載なし」と表示する。
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

class NFA_Com_Report_Type {

	const TYPE = 'nfa_report';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_filter( 'manage_' . self::TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . self::TYPE . '_posts_custom_column', array( __CLASS__, 'column' ), 10, 2 );
	}

	public static function register() {
		register_post_type(
			self::TYPE,
			array(
				'labels'             => array(
					'name'          => '事業計画・事業報告',
					'singular_name' => '事業計画・事業報告',
					'menu_name'     => '事業計画・報告',
					'add_new'       => '新規追加',
					'add_new_item'  => '資料を追加',
					'edit_item'     => '資料を編集',
					'all_items'     => '資料の一覧',
					'not_found'     => '見つかりませんでした',
				),
				'public'             => false,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'publicly_queryable' => false,
				'exclude_from_search' => true,
				'menu_position'      => 21,
				'menu_icon'          => 'dashicons-media-document',
				'supports'           => array( 'title', 'page-attributes' ),
				'capability_type'    => 'page',   // 事務局（編集者）以上が管理する
				'map_meta_cap'       => true,
			)
		);
	}

	public static function columns( $cols ) {
		return array_slice( $cols, 0, 2, true ) + array( 'nfa_year' => '掲載年', 'nfa_file' => 'ファイル' ) + $cols;
	}

	public static function column( $col, $post_id ) {
		if ( 'nfa_year' === $col ) {
			echo esc_html( (string) nfa_field( 'nfa_report_year', $post_id ) );
		} elseif ( 'nfa_file' === $col ) {
			$url = nfa_file_url( nfa_field( 'nfa_report_file', $post_id ) );
			echo $url ? '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( wp_basename( $url ) ) . '</a>' : '—';
		}
	}

	/**
	 * 掲載年ごとの資料（新しい年から）。
	 *
	 * @param int $years 何年分（0 は全部）
	 * @return array[] [ [ 'year' => 2026, 'items' => [ [ 'title', 'url', 'ext' ] ], 'note' => '' ], ... ]
	 *                 ファイルのない資料は url が空（「掲載なし」と表示する）
	 */
	public static function by_year( $years = 0 ) {
		$posts = get_posts(
			array(
				'post_type'      => self::TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
			)
		);
		$out = array();
		foreach ( $posts as $p ) {
			$y   = (int) nfa_field( 'nfa_report_year', $p->ID );
			$url = nfa_file_url( nfa_field( 'nfa_report_file', $p->ID ) );
			if ( ! $y ) {
				continue;
			}
			$out[ $y ]['year']    = $y;
			$out[ $y ]['items'][] = array(
				'title' => get_the_title( $p ),
				'url'   => $url,
				'ext'   => strtoupper( (string) pathinfo( (string) wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) ),
			);
			$note = trim( (string) nfa_field( 'nfa_report_note', $p->ID ) );
			if ( $note ) {
				$out[ $y ]['note'] = $note;
			}
		}
		krsort( $out );
		$out = array_values( array_map( fn( $g ) => $g + array( 'note' => '' ), $out ) );
		return $years ? array_slice( $out, 0, $years ) : $out;
	}
}
