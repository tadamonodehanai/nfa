<?php
/**
 * テーマから使う関数。プラグインが無効でもテーマが壊れないよう、呼び出し側では
 * function_exists() で確認してから使ってください。
 *
 *   <?php if ( function_exists( 'nfa_ai_search_form' ) ) nfa_ai_search_form(); ?>
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

/**
 * 探索画面（[nfa_ai_search] を置いた固定ページ）のURL。未設定なら空文字。
 */
function nfa_ai_search_url() {
	return NFA_AIS_Frontend::search_page_url();
}

/**
 * ヘッダーなどに置く検索ボックス。入力内容を ?q= に付けて探索画面へ移動する。
 *
 * @param array $args placeholder, class, button_label
 */
function nfa_ai_search_form( $args = array() ) {
	$args   = wp_parse_args(
		$args,
		array(
			'placeholder'  => '例）小学生の大会っていつ？　審判の資格更新はどうすれば？',
			'class'        => 'nfa-ais-form',
			'button_label' => '検索',
		)
	);
	$action = nfa_ai_search_url();
	if ( ! $action ) {
		return;
	}
	printf(
		'<form class="%1$s" role="search" method="get" action="%2$s"><input type="search" name="q" placeholder="%3$s" aria-label="探したい情報を入力" maxlength="200"><button type="submit">%4$s</button></form>',
		esc_attr( $args['class'] ),
		esc_url( $action ),
		esc_attr( $args['placeholder'] ),
		esc_html( $args['button_label'] )
	);
}

/**
 * 投稿の3軸を [axis => [term name, ...]] で返す。axis = target | purpose | area（発信元は含めない）
 */
function nfa_ai_search_axes( $post = null ) {
	$post = get_post( $post );
	return $post ? NFA_AIS_Taxonomies::axes_of( $post->ID ) : array();
}

/**
 * 記事ページに3軸のチップを表示する（例：小学生（4種） / 大会日程を知る / 県全域）。
 */
function nfa_ai_search_the_axes( $post = null ) {
	$axes = nfa_ai_search_axes( $post );
	if ( ! $axes ) {
		return;
	}
	$chips = array();
	foreach ( array_keys( NFA_AIS_Taxonomies::FACETS ) as $key ) {
		foreach ( $axes[ $key ] as $name ) {
			$chips[] = '<span class="nfa-ais-chip">' . esc_html( $name ) . '</span>';
		}
	}
	if ( $chips ) {
		wp_enqueue_style( 'nfa-ai-search' );
		echo '<div class="nfa-ais-chips">' . implode( '', $chips ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * 記事ページの「AIの案内はこのページを根拠にしています」表示。
 */
function nfa_ai_search_source_notice() {
	echo NFA_AIS_Frontend::source_notice(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
