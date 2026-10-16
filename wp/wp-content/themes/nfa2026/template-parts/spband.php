<?php
/**
 * 協賛帯（下層ページ共通）。並びは「協賛企業」の設定（NFA Committee）から。
 *
 * @package NFA2026
 */
?>
<section class="spband"><div class="wrap"><div class="bh"><div><div class="e">SPONSOR</div><h2>協賛企業</h2></div><a class="more" href="<?php echo esc_url( nfa_u( 'partner.html' ) ); ?>">協賛について</a></div><?php echo nfa_sponsors_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
  </div></section>
