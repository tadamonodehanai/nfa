<?php
/**
 * 事業計画・事業報告（デザイン reports.html）。scratchpad の conv2.py で生成。
 *
 * @package NFA2026
 */

get_header();
?>

<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><a href="<?php echo esc_url( nfa_u( 'about.html' ) ); ?>">協会について</a><span aria-hidden="true">›</span><span aria-current="page">事業計画・事業報告</span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span>BUSINESS REPORTS</span></span></p><h1 class="pg-ja">事業計画・事業報告</h1></div><div class="pg-sub"><p>一般社団法人 長野県サッカー協会の事業計画・収支予算・事業報告・決算報告を、2013年から年度別に掲載しています。</p></div></div></section>
<?php echo nfa_reports_lnav_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<section class="blk" id="latest"><div class="wrap">
<div class="bh"><div><div class="e">LATEST</div><h2>最新の事業計画・事業報告</h2></div></div>
<?php echo nfa_reports_grid_html( 0, 2, 'rp-g' ); // 最新の2年 phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div></section>
<section class="blk pale" id="archive"><div class="wrap">
<div class="bh"><div><div class="e">ARCHIVE</div><h2>過去の事業計画・事業報告</h2></div></div>
<?php echo nfa_reports_grid_html( 2, 0, 'rp-g rp-g3' ); // 3年目から phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div></section>
<section class="blk"><div class="wrap">
<div class="fx-rel"><a href="<?php echo esc_url( nfa_u( 'about.html#business' ) ); ?>"><span class="e">BUSINESS</span><b>事業内容（定款より）</b></a><a href="<?php echo esc_url( nfa_u( 'about.html#profile' ) ); ?>"><span class="e">PROFILE</span><b>協会概要・定款・役員名簿</b></a></div>
</div></section>
<?php
get_footer();
