<?php
/**
 * どのテンプレートにも当てはまらないとき（アーカイブ・検索結果など）。お知らせ一覧と同じ並び。
 * サイト内検索（?s=）は、AI検索の画面に送る。
 *
 * @package NFA2026
 */

if ( is_search() && function_exists( 'nfa_ai_search_url' ) && nfa_ai_search_url() ) {
	wp_safe_redirect( add_query_arg( 'q', rawurlencode( get_search_query( false ) ), nfa_ai_search_url() ) );
	exit;
}

get_header();
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><span aria-current="page"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><h1 class="pg-ja"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1></div></div></section>
<section class="blk"><div class="wrap">
<?php if ( have_posts() ) : ?>
<ul class="nl"><?php while ( have_posts() ) : the_post(); echo nfa_news_li( get_post() ); endwhile; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
<?php echo nfa_pager(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<?php else : ?>
<p class="lead2">該当する情報はありません。</p>
<?php endif; ?>
</div></section>
<?php
get_footer();
