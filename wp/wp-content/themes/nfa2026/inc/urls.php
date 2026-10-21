<?php
/**
 * URL の読み替え。デザインの「X.html」へのリンクを WordPress の URL にする。
 *
 *   index.html                 → TOP
 *   cat-4.html … medical.html  → 担当委員会のページ（/committee/cat-4/ など）
 *   archive-cat-4.html など    → 過去の日程・試合結果（/committee/cat-4/archive/）
 *   team-34149.html など       → チーム（/team/{スラッグ}/）
 *   about.html など            → 同じスラッグの固定ページ
 *   list.html                  → お知らせ一覧（表示設定の「投稿ページ」）。?c=office_news など旧カテゴリーの区分は担当委員会に読み替え
 *   detail*.html               → お知らせ一覧（記事はデザインの見本なので）
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
 * お知らせ一覧の URL。絞り込みもパスで表す（GA などでページとして数えられるように）。
 *
 *   すべて                 /news/
 *   カテゴリー             /news/news-info/ ・ /action_report/（記事の URL /%category%/%post_id%/ と同じ並び）
 *   区分（担当委員会）     /news/committee/referee/
 *   カテゴリー × 区分     /news/news-info/committee/referee/
 *   ページ送りは後ろに /page/2/（paginate_links が付ける）
 * パスの読み取りは functions.php の nfa_news_rewrite()。
 *
 * @param string $committee 担当委員会のスラッグ（'' ならすべて）
 * @param string $type      投稿のカテゴリーのスラッグ（'' ならすべて）
 */
function nfa_news_url( $committee = '', $type = '' ) {
	$path = $type ? nfa_category_path( $type ) : '';
	if ( $path ) {
		$url = home_url( '/' . $path . '/' );
	} else {
		$id  = (int) get_option( 'page_for_posts' );
		$url = $id ? get_permalink( $id ) : home_url( '/news/' );
	}
	return $committee ? trailingslashit( $url ) . 'committee/' . rawurlencode( $committee ) . '/' : $url;
}

/**
 * カテゴリーのパス（親のスラッグ／自分のスラッグ。例：news/news-info）。お知らせ一覧の「すべて」にあたる
 * 親カテゴリー（投稿ページと同じスラッグ。news）は '' を返す。
 */
function nfa_category_path( $slug ) {
	$term = get_term_by( 'slug', $slug, 'category' );
	if ( ! $term ) {
		return '';
	}
	$slugs = array( $term->slug );
	foreach ( get_ancestors( $term->term_id, 'category', 'taxonomy' ) as $aid ) {
		$a = get_term( $aid, 'category' );
		if ( $a && ! is_wp_error( $a ) ) {
			array_unshift( $slugs, $a->slug );
		}
	}
	$path  = implode( '/', $slugs );
	$posts = get_post( (int) get_option( 'page_for_posts' ) );
	return ( $posts && $path === $posts->post_name ) ? '' : $path;
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
	} elseif ( preg_match( '/^archive-(.+)$/', $name, $m ) && in_array( $m[1], $committees, true ) ) {
		$url = function_exists( 'nfa_committee_archive_url' ) ? nfa_committee_archive_url( $m[1] ) : nfa_committee_url( $m[1] );
	} elseif ( preg_match( '/^team-(.+)$/', $name, $m ) ) {
		$team = get_page_by_path( $m[1], OBJECT, 'team' );
		$team = $team ? $team : get_page_by_path( $name, OBJECT, 'team' );
		$url  = $team ? get_permalink( $team ) : nfa_page_url( 'teams' );
	} elseif ( 'list' === $name ) {
		$url   = nfa_list_url( $query );
		$query = '';
	} elseif ( 'search' === $name ) {
		$url = function_exists( 'nfa_ai_search_url' ) && nfa_ai_search_url() ? nfa_ai_search_url() : nfa_page_url( 'search' );
	} elseif ( 0 === strpos( $name, 'detail' ) || '404' === $name ) {
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
 * デザインのお知らせ一覧の絞り込み（list.html?c=office_news など旧カテゴリー）→ お知らせ一覧の URL（パスの形）。
 */
function nfa_list_url( $query ) {
	parse_str( (string) $query, $q );
	$map = array(
		'office_news'    => 'association',
		'technical_news' => 'technical',
		'referee_news'   => 'referee',
		'medical_news'   => 'medical',
		'kids_news'      => 'grassroots',
		'adults_news'    => 'cat-1',
		'cat_2_news'     => 'cat-2',
		'cat_3_news'     => 'cat-3',
		'cat_4_news'     => 'cat-4',
		'cat_w_news'     => 'cat-w',
		'cat_f_news'     => 'cat-f',
		'cat_s_news'     => 'cat-s',
	);
	$c = (string) ( $q['c'] ?? '' );
	if ( 'important' === $c ) {
		return nfa_news_url( '', 'news-important' );
	}
	return nfa_news_url( $map[ $c ] ?? '' );
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
