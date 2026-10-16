<?php
/**
 * テーマから使う読み出し用の関数。
 *
 * 入力欄は ACF Pro で作るが、ACF Pro が無効でもテーマが壊れず、保存済みのデータを読めるよう、
 * ACF と同じ保存形式（リピーターは「件数」と「名前_番号_項目」）を直接読む。
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

/**
 * 投稿のカスタムフィールド。ACF があれば get_field、なければ保存形式を直接読む。
 *
 * @param string   $name    フィールド名
 * @param int|null $post_id 投稿ID（'option' で設定ページ）
 * @param array    $subs    リピーターのときの項目名（ACF がないときに使う）
 */
function nfa_field( $name, $post_id = null, array $subs = array() ) {
	if ( function_exists( 'get_field' ) ) {
		return get_field( $name, $post_id ?? false );
	}
	$get = function ( $key ) use ( $post_id ) {
		if ( 'option' === $post_id || 'options' === $post_id ) {
			return get_option( 'options_' . $key, '' );
		}
		return get_post_meta( $post_id ?? get_the_ID(), $key, true );
	};
	$value = $get( $name );
	if ( ! $subs ) {
		return $value;
	}
	$rows = array();
	for ( $i = 0, $n = (int) $value; $i < $n; $i++ ) {
		$row = array();
		foreach ( $subs as $sub ) {
			$row[ $sub ] = $get( "{$name}_{$i}_{$sub}" );
		}
		$rows[] = $row;
	}
	return $rows;
}

/**
 * ファイル欄の値（添付ID・配列・URLのいずれか）から URL を得る。
 */
function nfa_file_url( $file ) {
	if ( is_array( $file ) ) {
		return $file['url'] ?? '';
	}
	if ( is_numeric( $file ) && $file ) {
		return (string) wp_get_attachment_url( (int) $file );
	}
	return is_string( $file ) ? $file : '';
}

/**
 * 大会・リーグ資料（nfa_doc）の行。区分・対象・ファイル（URL）・更新日・準備中かどうか。
 *
 * @return array<int, array{section:string, target:string, url:string, updated:string, pending:bool, label:string}>
 */
function nfa_doc_rows( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return array();
	}
	$rows = nfa_field( 'nfa_doc_rows', $post->ID, array( 'section', 'target', 'label', 'file', 'url', 'updated' ) );
	$out  = array();
	foreach ( (array) $rows as $r ) {
		$url   = nfa_file_url( $r['file'] ?? '' );
		$url   = $url ? $url : (string) ( $r['url'] ?? '' );
		$out[] = array(
			'section' => (string) ( $r['section'] ?? '' ),
			'target'  => (string) ( $r['target'] ?? '' ),
			'label'   => (string) ( $r['label'] ?? '' ),
			'url'     => $url,
			'updated' => nfa_date_ymd( $r['updated'] ?? '' ),
			'pending' => '' === $url,
		);
	}
	return $out;
}

/**
 * 日付欄（Ymd・Y-m-d・Y/m/d など）を Y.m.d にそろえる。
 */
function nfa_date_ymd( $value, $format = 'Y.m.d' ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	// 月までの日付（202608 / 2026.08）は Y.m のまま返す
	if ( preg_match( '/^(\d{4})[.\/-]?(\d{2})$/', $value, $m ) ) {
		return $m[1] . '.' . $m[2];
	}
	if ( preg_match( '/^(\d{4})(\d{2})(\d{2})$/', $value, $m ) ) {
		$value = "{$m[1]}-{$m[2]}-{$m[3]}";
	}
	if ( ! preg_match( '/^\d{4}[.\/-]\d{1,2}[.\/-]\d{1,2}/', $value ) ) {
		return $value;
	}
	$ts = strtotime( str_replace( array( '/', '.' ), '-', $value ) );
	return $ts ? wp_date( $format, $ts, new DateTimeZone( 'UTC' ) ) : $value;
}

/**
 * 投稿の担当委員会（最初の1つ）。
 */
function nfa_post_committee( $post = null ) {
	$terms = get_the_terms( get_post( $post ), NFA_Com_Committees::TAX );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
}

/**
 * 委員会ごとの入力欄（英字ラベル・説明文・問い合わせ先など）。
 *
 * @param WP_Term|int|string $term 用語・用語ID・スラッグ
 */
function nfa_committee_meta( $term, $key ) {
	if ( is_string( $term ) && ! is_numeric( $term ) ) {
		$term = get_term_by( 'slug', $term, NFA_Com_Committees::TAX );
	}
	$term = get_term( $term, NFA_Com_Committees::TAX );
	if ( ! $term || is_wp_error( $term ) ) {
		return '';
	}
	$value = get_term_meta( $term->term_id, 'nfa_' . $key, true );
	if ( '' === $value || null === $value ) {
		$def   = NFA_Com_Committees::definition( $term->slug );
		$value = $def[ $key ] ?? '';
	}
	return $value;
}

/**
 * 投稿のフラグ（important = 重要 / alert = 注意喚起 / top = TOPに掲載）。
 */
function nfa_flag( $flag, $post = null ) {
	$post = get_post( $post );
	return $post ? (bool) get_post_meta( $post->ID, NFA_Com_Flags::META[ $flag ] ?? '', true ) : false;
}

/**
 * 協賛企業（設定ページ）。未入力ならデザインの並びを返す。
 *
 * @return array{gold:array, official:array, supporters:array} 各要素は [name, url]
 */
function nfa_sponsors() {
	$out = array();
	foreach ( array( 'gold', 'official', 'supporters' ) as $kind ) {
		$rows         = nfa_field( 'nfa_sponsors_' . $kind, 'option', array( 'name', 'url' ) );
		$out[ $kind ] = array_values( array_filter( (array) $rows, fn( $r ) => ! empty( $r['name'] ) ) );
	}
	if ( ! $out['gold'] && ! $out['official'] && ! $out['supporters'] ) {
		return NFA_Com_Settings::default_sponsors();
	}
	return $out;
}
