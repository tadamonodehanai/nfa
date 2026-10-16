<?php
/**
 * 固定ページ（専用テンプレートのないもの）。デザインの下層ページの見出しと本文。
 *
 * @package NFA2026
 */

get_header();
the_post();
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><span aria-current="page"><?php the_title(); ?></span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><h1 class="pg-ja"><?php the_title(); ?></h1></div><?php if ( has_excerpt() ) : ?><div class="pg-sub"><p><?php echo esc_html( get_the_excerpt() ); ?></p></div><?php endif; ?></div></section>
<section class="blk"><div class="wrap"><div class="prose"><?php the_content(); ?></div></div></section>
<?php
get_footer();
