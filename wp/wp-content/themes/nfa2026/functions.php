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

/* ---------- CSS・JS ---------- */
add_action(
	'wp_enqueue_scripts',
	function () {
		$ver = fn( $f ) => NFA_THEME_VERSION . '.' . filemtime( get_theme_file_path( $f ) );
		wp_enqueue_style( 'nfa-fonts', 'https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,600;0,800;1,800&family=Noto+Sans+JP:wght@400;500;700;900&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_style( 'nfa-style', get_theme_file_uri( 'assets/css/style.css' ), array( 'nfa-fonts' ), $ver( 'assets/css/style.css' ) );
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
		if ( is_tax( 'nfa_committee' ) ) {
			$ja             = (string) nfa_term_meta( get_queried_object(), 'ja' );
			$parts['title'] = $ja ? $ja : $parts['title'];
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
		echo '<meta name="description" content="' . esc_attr( wp_html_excerpt( $desc, 120, '…' ) ) . '">' . "\n";
	},
	1
);

/* ---------- お知らせ一覧：委員会で絞り込み（?c=） ---------- */
function nfa_news_filter() {
	$f = isset( $_GET['c'] ) ? sanitize_key( wp_unslash( $_GET['c'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return ( 'cat' === $f || ( $f && term_exists( $f, 'nfa_committee' ) ) ) ? $f : '';
}
add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( is_admin() || ! $q->is_main_query() ) {
			return;
		}
		if ( $q->is_home() ) {
			$q->set( 'posts_per_page', 30 );
			$f = nfa_news_filter();
			if ( 'cat' === $f ) {
				$q->set( 'tax_query', array( array( 'taxonomy' => 'nfa_committee', 'field' => 'slug', 'terms' => array( 'cat-4', 'cat-3', 'cat-2', 'cat-1', 'cat-w', 'cat-f', 'cat-s' ) ) ) );
			} elseif ( $f ) {
				$q->set( 'tax_query', array( array( 'taxonomy' => 'nfa_committee', 'field' => 'slug', 'terms' => $f ) ) );
			}
		}
		if ( $q->is_tax( 'nfa_committee' ) ) {
			// 委員会ページの本文はテンプレートで個別に問い合わせるので、メインクエリは1件で足りる
			$q->set( 'posts_per_page', 1 );
		}
	}
);

/* ---------- AI検索：テーマの画像を「写真なし」の記事に使う ---------- */
add_filter( 'nfa_ais_placeholder_image', fn() => nfa_asset( 'img/ph-photo.jpg' ) );
