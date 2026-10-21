<?php
/**
 * NFA 2026 テーマ。
 *
 * デザインデータ NFA_site_design を WordPress に置き換えたもの。
 *   - 見た目・動き：assets/（デザインの CSS・JS・画像・映像をそのまま）
 *   - データ：NFA Committee（担当委員会・大会資料・フラグ・協賛企業）、NFA AI Search（分類・AI検索）
 * プラグインが無効でもテーマが落ちないよう、プラグインの関数は function_exists で確かめてから使う。
 *
 * @package NFA2026
 */

defined( 'ABSPATH' ) || exit;

define( 'NFA_THEME_VERSION', '0.1.0' );

require_once __DIR__ . '/inc/urls.php';
require_once __DIR__ . '/inc/data.php';
require_once __DIR__ . '/inc/render.php';

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		// AI検索の画面（.sr-*）のスタイルはこのテーマの style.css が持つ
		add_theme_support( 'nfa-ai-search' );
	}
);

/* ---------- アクセス解析のタグ（template-parts/analytics.php）を読み込むか ----------
 * 本番だけ。開発環境（wp-env の localhost・WP_ENVIRONMENT_TYPE が local／development／staging）では読み込まない。
 * 管理画面にログイン中の人も読み込まない（制作・更新の作業を数えないため）。変えるときはフィルター nfa_analytics_off_reason（'' を返すと読み込む）。
 */
function nfa_analytics_enabled() {
	return '' === nfa_analytics_off_reason();
}

/**
 * アクセス解析のタグを出さない理由（出すときは ''）。出さないときは、ページのソースにこの理由をコメントで残す。
 */
function nfa_analytics_off_reason() {
	if ( 'production' !== wp_get_environment_type() ) {
		$reason = '本番の環境ではないため（WP_ENVIRONMENT_TYPE＝' . wp_get_environment_type() . '）';
	} elseif ( in_array( wp_parse_url( home_url(), PHP_URL_HOST ), array( 'localhost', '127.0.0.1' ), true ) ) {
		$reason = '開発環境（localhost）のため';
	} elseif ( is_user_logged_in() ) {
		$reason = 'ログイン中のため（ログアウトするか、プライベートウィンドウで確認してください）';
	} else {
		$reason = '';
	}
	return (string) apply_filters( 'nfa_analytics_off_reason', $reason );
}

/* ---------- CSS・JS ---------- */
add_action(
	'wp_enqueue_scripts',
	function () {
		$ver = fn( $f ) => NFA_THEME_VERSION . '.' . filemtime( get_theme_file_path( $f ) );
		wp_enqueue_style( 'nfa-fonts', 'https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,600;0,800;1,800&family=Noto+Sans+JP:wght@400;500;700;900&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_style( 'nfa-style', get_theme_file_uri( 'assets/css/style.css' ), array( 'nfa-fonts' ), $ver( 'assets/css/style.css' ) );
		wp_enqueue_style( 'nfa-wp', get_theme_file_uri( 'assets/css/wp.css' ), array( 'nfa-style' ), $ver( 'assets/css/wp.css' ) );   // WordPress 版だけの調整
		wp_enqueue_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', array(), '3.12.5', true );
		wp_enqueue_script( 'gsap-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js', array( 'gsap' ), '3.12.5', true );
		wp_enqueue_script( 'lenis', 'https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js', array(), '1.1.13', true );
		wp_enqueue_script( 'nfa-main', get_theme_file_uri( 'assets/js/main.js' ), array( 'gsap', 'gsap-scrolltrigger', 'lenis' ), $ver( 'assets/js/main.js' ), true );
	}
);

// テーマの style.css はテーマ情報だけなので読み込まない。WordPress の不要な出力も外す。
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'wp-block-library-theme' );
	},
	100
);

/* ---------- タイトル・説明文 ---------- */
add_filter(
	'document_title_separator',
	fn() => '｜'
);
add_filter(
	'document_title_parts',
	function ( $parts ) {
		if ( is_front_page() ) {
			return array( 'title' => '長野県サッカー協会（NFA）' );
		}
		if ( is_home() ) {
			// 絞り込んだ一覧は「お知らせ（審判・活動報告）」のように（GA のページタイトルでも見分けられるよう）
			$names = array();
			$c     = nfa_news_filter();
			$t     = nfa_news_type();
			if ( $c && ( $term = get_term_by( 'slug', $c, 'nfa_committee' ) ) ) {
				$names[] = (string) nfa_term_meta( $term, 'badge' ) ?: $term->name;
			}
			if ( $t && ( $term = get_term_by( 'slug', $t, 'category' ) ) ) {
				$names[] = $term->name;
			}
			if ( $names ) {
				$parts['title'] .= '（' . implode( '・', $names ) . '）';
			}
		}
		if ( is_tax( 'nfa_committee' ) ) {
			$ja             = (string) nfa_term_meta( get_queried_object(), 'ja' );
			$parts['title'] = $ja ? $ja : $parts['title'];
			if ( get_query_var( 'nfa_archive' ) ) {
				$parts['title'] = '過去の日程・試合結果（' . $parts['title'] . '）';
			}
		}
		$parts['site'] = '長野県サッカー協会（NFA）';
		unset( $parts['tagline'] );
		return $parts;
	}
);
add_action(
	'wp_head',
	function () {
		$desc = '長野県サッカー協会の公式サイト。大会日程・試合結果、登録・講習、お知らせを掲載しています。';
		if ( is_tax( 'nfa_committee' ) ) {
			$desc = (string) nfa_term_meta( get_queried_object(), 'lead' );
		} elseif ( is_singular() && has_excerpt() ) {
			$desc = wp_strip_all_tags( get_the_excerpt() );
		}
		$desc  = wp_html_excerpt( $desc, 120, '…' );
		$title = wp_get_document_title();
		$image = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : nfa_asset( 'img/ogp.jpg' );
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<link rel="icon" href="' . esc_url( nfa_asset( 'img/favicon.svg' ) ) . '" type="image/svg+xml">' . "\n";
		echo '<link rel="icon" href="' . esc_url( nfa_asset( 'img/favicon-32.png' ) ) . '" sizes="32x32">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url( nfa_asset( 'img/apple-touch-icon.png' ) ) . '">' . "\n";
		echo '<meta property="og:type" content="' . ( is_front_page() ? 'website' : 'article' ) . '">' . "\n";
		echo '<meta property="og:site_name" content="長野県サッカー協会（NFA）">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	},
	1
);

// サイトアイコン（カスタマイザー）を設定していないときは、WordPress のアイコンを出さない（テーマのファビコンを使う）
add_filter( 'get_site_icon_url', fn( $url ) => $url );

/* ---------- フォームの送信完了ページ（フォームの固定ページの子ページ complete） ----------
 * 送信が済んだら（Contact Form 7 の wpcf7mailsent）、そのページの子ページ「complete」へ移る。
 * 完了ページがなければ、Contact Form 7 の「送信しました」の表示のまま。
 */
add_action(
	'wp_footer',
	function () {
		if ( ! is_page() ) {
			return;
		}
		$page = get_queried_object();
		$done = get_page_by_path( get_page_uri( $page ) . '/complete' );
		if ( ! $done || 'publish' !== $done->post_status ) {
			return;
		}
		printf(
			"<script>document.addEventListener('wpcf7mailsent',function(){location.href=%s;});</script>\n",
			wp_json_encode( get_permalink( $done ) )
		);
	},
	50
);
// 完了ページは検索エンジンに載せない
add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( is_page() && 'complete' === get_queried_object()->post_name ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}
		return $robots;
	}
);

/* ---------- Contact Form 7：フォームの記述（inc/*-form.txt）に段落を自動で付けない（デザインの並びが崩れるため） ---------- */
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/* ---------- 担当委員会「協会」のページは「協会について」 ---------- */
add_filter(
	'term_link',
	function ( $url, $term ) {
		return ( 'nfa_committee' === $term->taxonomy && 'association' === $term->slug ) ? nfa_page_url( 'about' ) : $url;
	},
	10,
	2
);

/* ---------- お知らせ一覧のパスの形へ転送 ----------
 * ・カテゴリーのページ（/category/news/news-info/）→ /news/news-info/
 * ・古い形（/news/?c=referee&type=action_report）→ /action_report/committee/referee/
 */
add_action(
	'template_redirect',
	function () {
		if ( is_feed() ) {
			return;
		}
		$query = is_home() && ( isset( $_GET['c'] ) || isset( $_GET['type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! is_category() && ! $query ) {
			return;
		}
		$type = is_category() ? get_queried_object()->slug : nfa_news_type();
		$url  = nfa_news_url( nfa_news_filter(), $type );
		$page = (int) get_query_var( 'paged' );
		wp_safe_redirect( $page > 1 ? trailingslashit( $url ) . 'page/' . $page . '/' : $url, 301 );
		exit;
	}
);

/* ---------- 過去の日程・試合結果（/committee/{スラッグ}/archive/） ---------- */
add_filter(
	'template_include',
	function ( $template ) {
		if ( is_tax( 'nfa_committee' ) && get_query_var( 'nfa_archive' ) ) {
			$t = locate_template( 'taxonomy-nfa_committee-archive.php' );
			return $t ? $t : $template;
		}
		return $template;
	}
);

/* ---------- お知らせ一覧：区分（担当委員会 ?c=）とカテゴリー（?type=）の掛け合わせ ---------- */
// 区分・カテゴリーは URL のパス（/news/news-info/committee/referee/）から。古い ?c= ?type= も読む（パスの形へ転送する）
function nfa_news_filter() {
	$f = (string) get_query_var( 'nfa_c' );
	$f = $f ? $f : ( isset( $_GET['c'] ) ? wp_unslash( $_GET['c'] ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$f = sanitize_key( $f );
	return ( 'cat' === $f || ( $f && term_exists( $f, 'nfa_committee' ) ) ) ? $f : '';
}
function nfa_news_type() {
	$t = (string) get_query_var( 'nfa_type' );
	$t = $t ? $t : ( isset( $_GET['type'] ) ? wp_unslash( $_GET['type'] ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$t = sanitize_title( $t );
	return ( $t && term_exists( $t, 'category' ) ) ? $t : '';
}

/**
 * お知らせ一覧の絞り込みのパス（inc/urls.php の nfa_news_url() の形）を読む書き換えルール。
 * カテゴリーごとにルールを作るので、カテゴリーを追加・変更したらルールを作り直す。
 */
function nfa_news_rewrite() {
	$posts_page = (int) get_option( 'page_for_posts' );
	if ( ! $posts_page ) {
		return;
	}
	$base = get_post_field( 'post_name', $posts_page );
	$to   = 'index.php?page_id=' . $posts_page;
	$tail = '(?:/committee/([^/]+))?(?:/page/([0-9]+))?/?$';
	add_rewrite_rule( '^' . preg_quote( $base, '#' ) . '/committee/([^/]+)(?:/page/([0-9]+))?/?$', $to . '&nfa_c=$matches[1]&paged=$matches[2]', 'top' );
	foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) ) as $t ) {
		$path = nfa_category_path( $t->slug );
		if ( '' === $path || 'uncategorized' === $t->slug ) {
			continue;
		}
		add_rewrite_rule( '^' . preg_quote( $path, '#' ) . $tail, $to . '&nfa_type=' . $t->slug . '&nfa_c=$matches[1]&paged=$matches[2]', 'top' );
	}
}
add_action( 'init', 'nfa_news_rewrite' );
add_filter( 'query_vars', fn( $v ) => array_merge( $v, array( 'nfa_c', 'nfa_type' ) ) );
foreach ( array( 'created_category', 'edited_category', 'delete_category' ) as $hook ) {
	add_action( $hook, fn() => update_option( 'nfa_news_rewrite_dirty', 1 ) );
}
add_action(
	'init',
	function () {
		if ( get_option( 'nfa_news_rewrite_dirty' ) ) {
			delete_option( 'nfa_news_rewrite_dirty' );
			flush_rewrite_rules( false );
		}
	},
	99
);
// 絞り込みのパスで開いたときに、WordPress が /news/ へ戻す転送をしない
add_filter( 'redirect_canonical', fn( $url ) => ( get_query_var( 'nfa_c' ) || get_query_var( 'nfa_type' ) ) ? false : $url );
add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( is_admin() || ! $q->is_main_query() ) {
			return;
		}
		if ( $q->is_home() ) {
			$q->set( 'posts_per_page', 30 );
			$f  = nfa_news_filter();
			$t  = nfa_news_type();
			$tq = array();
			if ( 'cat' === $f ) {
				$tq[] = array( 'taxonomy' => 'nfa_committee', 'field' => 'slug', 'terms' => array( 'cat-4', 'cat-3', 'cat-2', 'cat-1', 'cat-w', 'cat-f', 'cat-s' ) );
			} elseif ( $f ) {
				$tq[] = array( 'taxonomy' => 'nfa_committee', 'field' => 'slug', 'terms' => $f );
			}
			if ( $t ) {
				$tq[] = array( 'taxonomy' => 'category', 'field' => 'slug', 'terms' => $t, 'include_children' => true );
			}
			if ( $tq ) {
				$q->set( 'tax_query', array_merge( array( 'relation' => 'AND' ), $tq ) );
			}
		}
		if ( $q->is_tax( 'nfa_committee' ) ) {
			// 委員会ページの本文はテンプレートで個別に問い合わせるので、メインクエリは1件で足りる
			$q->set( 'posts_per_page', 1 );
		}
	}
);

/* ---------- AI検索：テーマの画像を「写真なし」の記事に使う ---------- */
add_filter( 'nfa_ais_placeholder_image', fn() => nfa_asset( 'img/thumb-default.jpg' ) );
