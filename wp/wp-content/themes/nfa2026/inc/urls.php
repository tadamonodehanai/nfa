<?php
/**
 * URL の読み替え。デザインの「X.html」へのリンクを WordPress の URL にする。
 *
 *   index.html                 → TOP
 *   cat-4.html … medical.html  → 担当委員会のページ（/committee/cat-4/ など）
 *   about.html など            → 同じスラッグの固定ページ
 *   list.html                  → お知らせ一覧（表示設定の「投稿ページ」）
 *   search.html                → 情報を探す（[nfa_ai_search] を置いた固定ページ）
 *
 * @package NFA2026
 */

defined( 'ABSPATH' ) || exit;

function nfa_asset( $path ) {
	return get_theme_file_uri( 'assets/' . ltrim( $path, '/' ) );
}

/**
 * 固定ページの URL（なければ /slug/）。
 */
function nfa_page_url( $slug ) {
	static $memo = array();
	if ( ! isset( $memo[ $slug ] ) ) {
		$p             = get_page_by_path( $slug );
		$memo[ $slug ] = $p ? get_permalink( $p ) : home_url( '/' . $slug . '/' );
	}
	return $memo[ $slug ];
}

/**
 * 担当委員会のページの URL。
 */
function nfa_committee_url( $slug ) {
	$link = taxonomy_exists( 'nfa_committee' ) ? get_term_link( $slug, 'nfa_committee' ) : '';
	return ( $link && ! is_wp_error( $link ) ) ? $link : home_url( '/' );
}

/**
 * お知らせ一覧の URL（委員会で絞り込むときは $committee にスラッグ）。
 */
function nfa_news_url( $committee = '' ) {
	$id  = (int) get_option( 'page_for_posts' );
	$url = $id ? get_permalink( $id ) : home_url( '/news/' );
	return $committee ? add_query_arg( 'c', $committee, $url ) : $url;
}

/**
 * デザインのファイル名（ハッシュ・クエリ付き可）→ URL。
 */
function nfa_u( $target ) {
	$hash  = '';
	$query = '';
	if ( false !== strpos( $target, '#' ) ) {
		list( $target, $hash ) = explode( '#', $target, 2 );
		$hash                  = '#' . $hash;
	}
	if ( false !== strpos( $target, '?' ) ) {
		list( $target, $query ) = explode( '?', $target, 2 );
	}
	$name = preg_replace( '/\.html$/', '', $target );
	$committees = array( 'cat-4', 'cat-3', 'cat-2', 'cat-1', 'cat-w', 'cat-f', 'cat-s', 'grassroots', 'referee', 'technical', 'medical' );
	if ( 'index' === $name ) {
		$url = home_url( '/' );
	} elseif ( in_array( $name, $committees, true ) ) {
		$url = nfa_committee_url( $name );
	} elseif ( 'list' === $name ) {
		$url = nfa_news_url();
	} elseif ( 'search' === $name ) {
		$url = function_exists( 'nfa_ai_search_url' ) && nfa_ai_search_url() ? nfa_ai_search_url() : nfa_page_url( 'search' );
	} elseif ( 'detail' === $name || '404' === $name ) {
		$url = nfa_news_url();
	} else {
		$url = nfa_page_url( $name );
	}
	if ( '' !== $query ) {
		$url .= ( false === strpos( $url, '?' ) ? '?' : '&' ) . $query;
	}
	return $url . $hash;
}

/**
 * いまのページなら aria-current="page" を出す（ヘッダーのナビ用）。
 */
function nfa_current( $target ) {
	$name = preg_replace( '/\.html$/', '', $target );
	$on   = false;
	if ( 'list' === $name ) {
		$on = is_home() || is_singular( 'post' );
	} elseif ( in_array( $name, array( 'about', 'start', 'grow', 'partner' ), true ) ) {
		$on = is_page( $name );
	}
	if ( $on ) {
		echo 'aria-current="page"';
	}
}
