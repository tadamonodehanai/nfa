<?php
/**
 * 旧サイト（www.nagano-fa.or.jp）へのリンクを、新サイトの中のリンクに読み替える。
 *
 *   ファイル（wp-content/uploads/…）   → 取り込んだメディアの URL
 *   記事（…/{投稿ID}、?p={投稿ID}）     → 取り込んだ記事（投稿IDは旧サイトと同じ）
 *   チーム（/team/{スラッグ}）          → チーム
 *   固定ページ（/office_top/business など）→ 新しいページ（下の表 nfa_legacy_page_map()）
 *   カテゴリー（/category/news_top/…）  → お知らせ一覧（担当委員会・カテゴリーで絞り込み）
 * 読み替え先がないもの（旧サイトにしかないフォームなど）は、旧サイトの URL のまま返す。
 *
 * テーマのテンプレート（デザインから変換した固定ページ）は表示のたびに、
 * 記事の本文は `wp nfa-committee relink` で一度だけ置き換える。
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

/**
 * 旧サイトの固定ページのパス → 読み替え先。
 * 読み替え先：page:{固定ページのスラッグ} | committee:{担当委員会} | archive:{担当委員会}（過去の日程・試合結果）
 *            | news（お知らせ一覧）| home。「#…」はページ内の位置。
 * パスの前から一致する一番長いものを使う（「/」の区切りで一致。末尾の * は文字の途中でも一致）。
 */
function nfa_legacy_page_map() {
	return array(
		''                                  => 'home',
		'cat_1'                             => 'committee:cat-1',
		'cat_1u'                            => 'committee:cat-1',
		'page-11418'                        => 'committee:cat-1',
		'cat_2'                             => 'committee:cat-2',
		'cat_3'                             => 'committee:cat-3',
		'cat_old'                           => 'committee:cat-3',
		'cat_4'                             => 'committee:cat-4',
		'cat_w'                             => 'committee:cat-w',
		'cat_w/ntc'                         => 'page:tracen',
		'cat_f'                             => 'committee:cat-f',
		'cat_s'                             => 'committee:cat-s',
		'cat_1/cat_1-results'               => 'archive:cat-1',
		'cat_1u/cat_1-results'              => 'archive:cat-1',
		'cat_2/cat_2-results'               => 'archive:cat-2',
		'cat_2/cat_2-results-2'             => 'archive:cat-2',
		'cat_3/cat_3-results'               => 'archive:cat-3',
		'cat_4/cat_4-results'               => 'archive:cat-4',
		'cat_w/cat_w-results'               => 'archive:cat-w',
		'cat_f/cat_f-results'               => 'archive:cat-f',
		'cat_s/cat_s-results'               => 'archive:cat-s',
		'contact'                           => 'page:contact',
		'download'                          => 'page:download',
		'emperorscup'                       => 'page:emperorscup',
		'emperorscup-103'                   => 'page:emperorscup',
		'empressscup-2'                     => 'committee:cat-w',
		'facilities'                        => 'page:facilities',
		'festival'                          => 'page:festival',
		'grass-roots'                       => 'committee:grassroots',
		'grass-roots/festival-form*'        => 'page:festival',   // festival-form-skypark-idv など
		'link'                              => 'page:links',
		'medicine-2'                        => 'committee:medical',
		'office-form'                       => 'page:request',
		'office-form-jfa'                   => 'page:request-jfa',
		'office_top'                        => 'page:about#profile',
		'office_top/vision'                 => 'page:about#mission',
		'office_top/2020-2'                 => 'page:about#mission',
		'office_top/business'               => 'page:reports',
		'office_top/entry'                  => 'page:registration#team',
		'office_top/entry/kickoff'          => 'page:registration#team',
		'office_top/entry/coach_entry'      => 'page:registration#coach',
		'office_top/entry/referee_entry'    => 'page:registration#referee',
		'office_top/kiyaku'                 => 'page:terms',
		'office_top/office_member'          => 'page:partner',
		'office_top/privacy'                => 'page:privacy',
		'office_top/team'                   => 'page:teams',
		'team_search'                       => 'page:teams',
		'referee_top'                       => 'committee:referee',
		'referee_top/referee-seminer'       => 'page:academy',
		'referee_top/referee-seminer/needs' => 'page:academy#needs',
		'referee_top/referee-seminer/flow'  => 'page:academy#u25',
		'referee_top/referee-seminer/curriculum' => 'page:academy#curriculum',
		'referee_top/referee-seminer/graduate'   => 'page:academy#graduate',
		'referee_top/referee-seminer/requirements' => 'page:academy#guide',
		'referee_top/year'                  => 'page:referee-schedule',
		'referee-seminar'                   => 'page:academy',   // 旧サイトの中の別サイト「審判セミナー」（記事は旧サイトでも見られない）
		'sitemap'                           => 'page:sitemap',
		'technical'                         => 'committee:technical',
		'technical/tec_cd'                  => 'page:coach-course#license',
		'technical/tec_gk'                  => 'page:coach-course#gk',
		'technical/tec_kids'                => 'page:coach-course#kids',
		'technical/tec_refreshment'         => 'page:coach-course#refresh',
		'news_top'                          => 'news',
		'category/news_top'                 => 'news',
	);
}

/**
 * 旧サイトの URL か。
 */
function nfa_is_legacy_url( $url ) {
	return (bool) preg_match( '#^(?:https?:)?//(?:www\.)?nagano-fa\.or\.jp(?:[/?\#]|$)#i', (string) $url );
}

/**
 * 旧サイトの URL → 新サイトの URL（読み替え先がなければそのまま）。
 */
function nfa_legacy_url( $url ) {
	static $memo = array();
	$url = (string) $url;
	if ( ! nfa_is_legacy_url( $url ) ) {
		return $url;
	}
	if ( isset( $memo[ $url ] ) ) {
		return $memo[ $url ];
	}
	$new           = nfa_legacy_resolve( $url );
	$memo[ $url ] = $new ? $new : $url;
	return $memo[ $url ];
}

function nfa_legacy_resolve( $url ) {
	$parts = wp_parse_url( preg_replace( '#^//#', 'https://', $url ) );
	$path  = trim( rawurldecode( (string) ( $parts['path'] ?? '' ) ), '/' );
	$hash  = isset( $parts['fragment'] ) ? '#' . $parts['fragment'] : '';
	parse_str( (string) ( $parts['query'] ?? '' ), $q );

	// ファイル
	if ( false !== strpos( $path, 'wp-content/uploads/' ) ) {
		return nfa_legacy_media_url( $url );
	}
	// ?p=ID・…/ID：取り込んだ記事
	$id = ! empty( $q['p'] ) ? (int) $q['p'] : ( preg_match( '#(?:^|/)(\d+)$#', $path, $m ) ? (int) $m[1] : 0 );
	if ( $id && 0 !== strpos( $path, 'referee-seminar' ) ) {   // /referee-seminar/ は別サイト（記事は取り込んでいない）
		$post = get_post( $id );
		if ( $post && 'post' === $post->post_type && 'publish' === $post->post_status && get_post_meta( $id, '_nfa_legacy_id', true ) ) {
			return get_permalink( $post ) . $hash;
		}
	}
	// チーム
	if ( preg_match( '#^team/([^/]+)$#', $path, $m ) ) {
		$team = get_page_by_path( $m[1], OBJECT, 'team' );
		return $team ? get_permalink( $team ) : '';
	}
	// 過去の日程・試合結果（…-results/archives-2024 → その年度の位置）
	if ( preg_match( '#^(cat_\w+)/cat_\w+-results(?:-2)?/archives-(\d{4})#', $path, $m ) ) {
		$target = nfa_legacy_page_map()[ $m[1] ] ?? '';
		if ( 0 === strpos( $target, 'committee:' ) ) {
			return nfa_legacy_target_url( 'archive:' . substr( $target, 10 ) . '#y' . $m[2] );
		}
	}
	// カテゴリー：お知らせ一覧を担当委員会・カテゴリーで絞り込む
	if ( preg_match( '#^category/news_top/(?:.*/)?([^/]+)$#', $path, $m ) && 'news_top' !== $m[1] ) {
		return nfa_legacy_category_url( $m[1] );
	}
	// 固定ページ
	$map  = nfa_legacy_page_map();
	$best = null;
	foreach ( $map as $from => $to ) {
		$hit = '*' === substr( $from, -1 )
			? 0 === strpos( $path, substr( $from, 0, -1 ) )
			: ( $path === $from || ( '' !== $from && 0 === strpos( $path, $from . '/' ) ) );
		if ( $hit && ( null === $best || strlen( $from ) > strlen( $best ) ) ) {
			$best = $from;
		}
	}
	if ( null !== $best ) {
		$to = $map[ $best ];
		return nfa_legacy_target_url( false === strpos( $to, '#' ) && $hash ? $to . $hash : $to );
	}
	return '';
}

/**
 * 読み替え先（page:about#mission など）→ URL。
 */
function nfa_legacy_target_url( $target ) {
	$hash = '';
	if ( false !== strpos( $target, '#' ) ) {
		list( $target, $hash ) = explode( '#', $target, 2 );
		$hash                  = '#' . $hash;
	}
	list( $kind, $slug ) = array_pad( explode( ':', $target, 2 ), 2, '' );
	switch ( $kind ) {
		case 'home':
			return home_url( '/' ) . $hash;
		case 'news':
			$id = (int) get_option( 'page_for_posts' );
			return ( $id ? get_permalink( $id ) : home_url( '/news/' ) ) . $hash;
		case 'page':
			$p = get_page_by_path( $slug );
			return $p ? get_permalink( $p ) . $hash : '';
		case 'committee':
		case 'archive':
			$link = get_term_link( $slug, NFA_Com_Committees::TAX );
			if ( is_wp_error( $link ) ) {
				return '';
			}
			return ( 'archive' === $kind ? nfa_committee_archive_url( $slug ) : $link ) . $hash;
	}
	return '';
}

/**
 * 過去の日程・試合結果のページ（/committee/{スラッグ}/archive/）。
 */
function nfa_committee_archive_url( $slug ) {
	$link = get_term_link( $slug, NFA_Com_Committees::TAX );
	return is_wp_error( $link ) ? '' : trailingslashit( $link ) . 'archive/';
}

/**
 * 旧カテゴリー → お知らせ一覧（担当委員会 ?c= ・カテゴリー ?type= で絞り込み）。
 */
function nfa_legacy_category_url( $legacy_cat ) {
	static $map = null;
	$map        = $map ?? require NFA_COM_DIR . 'includes/legacy-map.php';
	$base       = nfa_legacy_target_url( 'news' );
	$m          = $map[ $legacy_cat ] ?? null;
	$build = function ( $c, $t ) use ( $base ) {
		// テーマの nfa_news_url()（パスの形）があればそれを使う
		return function_exists( 'nfa_news_url' ) ? nfa_news_url( (string) $c, (string) $t ) : add_query_arg( array_filter( array( 'c' => $c, 'type' => $t ) ), $base );
	};
	if ( 'important' === $legacy_cat ) {
		return $build( '', NFA_Com_Categories::IMPORTANT );
	}
	if ( ! $m ) {
		return $base;
	}
	return $build( $m['committee'] ?? '', $m['category'] ?? '' );
}

/**
 * 旧サイトのファイルの URL → 取り込んだメディアの URL（取り込んでいなければ空）。
 */
function nfa_legacy_media_url( $url ) {
	global $wpdb;
	$sub  = preg_match( '#nagano-fa\.or\.jp/(referee-seminar|main)/wp-content/uploads/#i', $url, $mm ) ? strtolower( $mm[1] ) . '/' : '';
	$path = $sub . rawurldecode( preg_replace( '#^.*?/wp-content/uploads/#', '', strtok( $url, '?#' ) ) );
	$key  = preg_replace( '/-\d+x\d+(\.\w+)$/', '$1', $path );
	$id   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_nfa_legacy_media' AND meta_value = %s LIMIT 1", $key ) );
	return $id ? (string) wp_get_attachment_url( $id ) : '';
}

/**
 * HTML の中の旧サイトへのリンク（href・src）を読み替える。
 * 新サイトの中に読み替えたリンクからは、別タブで開く指定（target="_blank" rel="noopener"）を外す。
 */
function nfa_legacy_relink_html( $html, &$count = 0 ) {
	return preg_replace_callback(
		'#<(a|img|source|iframe)\b([^>]*)>#i',
		function ( $m ) use ( &$count ) {
			$attrs   = $m[2];
			$changed = false;
			$attrs   = preg_replace_callback(
				'#\b(href|src)=(["\'])((?:https?:)?//(?:www\.)?nagano-fa\.or\.jp[^"\']*)\2#i',
				function ( $a ) use ( &$changed ) {
					$new = nfa_legacy_url( html_entity_decode( $a[3], ENT_QUOTES ) );
					if ( nfa_is_legacy_url( $new ) ) {
						return $a[0];
					}
					$changed = true;
					return $a[1] . '=' . $a[2] . esc_url( $new ) . $a[2];
				},
				$attrs
			);
			if ( ! $changed ) {
				return $m[0];
			}
			$count++;
			if ( 'a' === strtolower( $m[1] ) ) {
				$attrs = preg_replace( '#\s+target=(["\'])_blank\1#i', '', $attrs );
				$attrs = preg_replace( '#\s+rel=(["\'])noopener(?: noreferrer)?\1#i', '', $attrs );
			}
			return '<' . $m[1] . $attrs . '>';
		},
		(string) $html
	);
}
