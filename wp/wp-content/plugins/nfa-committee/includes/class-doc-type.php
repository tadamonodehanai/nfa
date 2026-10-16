<?php
/**
 * 大会・リーグ資料（nfa_doc）。1大会＝1投稿。
 *
 *   年度        nfa_doc_year      例：2026
 *   種別        nfa_doc_kind      tournament（大会）| league（リーグ）
 *   開催時期    nfa_doc_period    例：8月〜9月（任意・表示用）
 *   行の繰り返し nfa_doc_rows      区分 section / 対象 target / 資料名 label / ファイル file / 外部URL url / 更新日 updated
 *
 * 委員会は「行を足してPDFを上げる」だけ。カテゴリトップの LATEST UPDATE・TOURNAMENT・LEAGUE・ARCHIVE、
 * TOPの日程タブ、AI検索の「該当する資料」は、すべてこの投稿から自動で並ぶ。
 * 行が更新されたら、投稿の更新日時も進む（並び順と「最新の更新」に使う）。
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

class NFA_Com_Doc_Type {

	const TYPE = 'nfa_doc';

	const KINDS = array(
		'tournament' => '大会',
		'league'     => 'リーグ',
	);

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_filter( 'manage_' . self::TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . self::TYPE . '_posts_custom_column', array( __CLASS__, 'column' ), 10, 2 );
		// AI検索の検索対象に大会資料を加える（AI検索プラグインの許可リストの初期値）
		add_filter( 'option_nfa_ais_settings', array( __CLASS__, 'add_to_search' ) );
		add_filter( 'default_option_nfa_ais_settings', array( __CLASS__, 'add_to_search' ) );
	}

	public static function register() {
		register_post_type(
			self::TYPE,
			array(
				'labels'        => array(
					'name'               => '大会・リーグ資料',
					'singular_name'      => '大会・リーグ資料',
					'menu_name'          => '大会・リーグ資料',
					'add_new'            => '新規追加',
					'add_new_item'       => '大会・リーグ資料を追加',
					'edit_item'          => '大会・リーグ資料を編集',
					'all_items'          => '大会・リーグ資料の一覧',
					'search_items'       => '大会・リーグ資料を検索',
					'not_found'          => '見つかりませんでした',
				),
				'public'        => true,
				'has_archive'   => false,
				'show_in_rest'  => true,
				'menu_position' => 6,
				'menu_icon'     => 'dashicons-media-spreadsheet',
				'supports'      => array( 'title', 'editor', 'revisions', 'author' ),
				'capability_type' => 'post',
				'map_meta_cap'  => true,
				'rewrite'       => array( 'slug' => 'docs', 'with_front' => false ),   // 記事の /news/ を付けない
			)
		);
		foreach ( array( 'nfa_committee', 'nfa_target', 'nfa_purpose', 'nfa_area' ) as $tax ) {
			if ( taxonomy_exists( $tax ) ) {
				register_taxonomy_for_object_type( $tax, self::TYPE );
			}
		}
	}

	public static function add_to_search( $value ) {
		$value = is_array( $value ) ? $value : array();
		$types = $value['post_types'] ?? array( 'post', 'page' );
		if ( ! in_array( self::TYPE, $types, true ) && empty( $value['nfa_doc_opt_out'] ) ) {
			$types[] = self::TYPE;
		}
		$value['post_types'] = $types;
		return $value;
	}

	public static function columns( $cols ) {
		$new = array();
		foreach ( $cols as $k => $v ) {
			$new[ $k ] = $v;
			if ( 'title' === $k ) {
				$new['nfa_doc_year'] = '年度';
				$new['nfa_doc_kind'] = '種別';
				$new['nfa_doc_rows'] = '資料';
			}
		}
		return $new;
	}

	public static function column( $col, $post_id ) {
		if ( 'nfa_doc_year' === $col ) {
			echo esc_html( (string) nfa_field( 'nfa_doc_year', $post_id ) );
		} elseif ( 'nfa_doc_kind' === $col ) {
			echo esc_html( self::KINDS[ nfa_field( 'nfa_doc_kind', $post_id ) ] ?? '' );
		} elseif ( 'nfa_doc_rows' === $col ) {
			$rows = nfa_doc_rows( $post_id );
			printf( '%d件（準備中 %d）', count( $rows ), count( array_filter( $rows, fn( $r ) => $r['pending'] ) ) );
		}
	}

	public static function kind( $post = null ) {
		$k = nfa_field( 'nfa_doc_kind', get_post( $post )->ID ?? null );
		return isset( self::KINDS[ $k ] ) ? $k : 'tournament';
	}
}
