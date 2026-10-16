<?php
/**
 * 情報を探す（デザイン search.html）。本文の [nfa_ai_search] が AI検索（NFA AI Search プラグイン）を出す。
 *
 * @package NFA2026
 */

get_header();
the_post();
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><span aria-current="page">情報を探す</span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span>AI SEARCH</span></span></p><h1 class="pg-ja">情報を探す</h1></div><div class="pg-sub"><p>知りたいことを、話しかけるように聞いてください。<br class="pc"/>続けて質問すると、条件を重ねて絞り込みます。</p></div></div></section>
<?php
if ( has_shortcode( get_the_content(), 'nfa_ai_search' ) ) {
	the_content();
} elseif ( shortcode_exists( 'nfa_ai_search' ) ) {
	echo do_shortcode( '[nfa_ai_search]' );
} else {
	echo '<section class="blk"><div class="wrap"><p class="lead2">AI検索は準備中です（NFA AI Search プラグインを有効にしてください）。</p></div></section>';
}
?>
<section class="blk pale" id="category"><div class="wrap">
<div class="bh"><div><div class="e">CATEGORY</div><h2>カテゴリから探す</h2></div></div>
<div class="catgrid"><?php echo nfa_catgrid(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</div></section>
<?php
get_footer();
