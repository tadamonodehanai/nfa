<?php
/**
 * お知らせ一覧（デザイン list.html）。表示設定の「投稿ページ」に割り当てた固定ページで表示される。
 * 委員会での絞り込みは ?c=office|cat|technical|referee|medical|grassroots（または担当委員会のスラッグ）。
 *
 * @package NFA2026
 */

get_header();

$filters = array(
	''           => 'すべて',
	'office'     => '協会',
	'cat'        => '大会・カテゴリ',
	'technical'  => '技術',
	'referee'    => '審判',
	'medical'    => '医学',
	'grassroots' => 'グラスルーツ',
);
$cur     = nfa_news_filter();
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><span aria-current="page">お知らせ</span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span>NEWS</span></span></p><h1 class="pg-ja">お知らせ</h1></div><div class="pg-sub"><p>協会からのお知らせ、大会・講習会の案内、活動報告を掲載しています。<br class="pc"/>委員会ごとに絞り込めます。</p></div></div></section>
<section class="blk"><div class="wrap">
<?php $alert = nfa_important_post(); if ( $alert && ! $cur && ! is_paged() ) : ?><a class="alert" href="<?php echo esc_url( get_permalink( $alert ) ); ?>"><b>重要なお知らせ</b><span><?php echo esc_html( get_the_title( $alert ) ); ?></span></a><?php endif; ?>
<div aria-label="絞り込み" class="filt" role="group"><div class="row"><span>委員会</span><?php foreach ( $filters as $k => $label ) : ?><a aria-selected="<?php echo $cur === $k ? 'true' : 'false'; ?>" class="pill" href="<?php echo esc_url( nfa_news_url( $k ) ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?></div></div>
<div id="nlist">
<?php if ( have_posts() ) : ?>
<ul class="nl" id="nlist-ul"><?php while ( have_posts() ) : the_post(); echo nfa_news_li( get_post() ); endwhile; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
<?php else : ?>
<p class="lead2">該当するお知らせはありません。</p>
<?php endif; ?>
</div>
<?php
$links = paginate_links( array( 'type' => 'array', 'prev_text' => '前へ', 'next_text' => '次へ', 'mid_size' => 1 ) );
if ( $links ) :
	?>
<nav class="center pager" aria-label="ページ送り"><?php echo implode( ' ', $links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></nav>
<?php endif; ?>
<div class="center"><a class="btn btn-o" href="https://www.nagano-fa.or.jp/category/news_top" rel="noopener" target="_blank">過去のお知らせ（既存サイト）<?php echo nfa_btn_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
</div></section>
<?php
get_footer();
