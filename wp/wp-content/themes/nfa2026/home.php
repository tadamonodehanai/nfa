<?php
/**
 * お知らせ一覧（デザイン list.html）。表示設定の「投稿ページ」に割り当てた固定ページで表示される。
 *
 * 絞り込みは2段で、掛け合わせられる（どちらも URL のクエリ。サーバー側で絞り込んでページ送りする）。
 *   区分       …/committee/{担当委員会のスラッグ}/（association＝協会、technical、cat-3 …）
 *   カテゴリー /news/news-info/ ・ /news/news-important/ ・ /action_report/（投稿のカテゴリー）
 *   URL の形は inc/urls.php の nfa_news_url()。古い ?c= ?type= はパスの形へ転送する（functions.php）。
 *
 * @package NFA2026
 */

get_header();

// 区分の並び（デザインどおり）
$areas = array(
	''            => 'すべて',
	'association' => '協会',
	'technical'   => '技術',
	'referee'     => '審判',
	'medical'     => '医学',
	'grassroots'  => 'グラスルーツ',
	'cat-1'       => '1種',
	'cat-2'       => '2種',
	'cat-3'       => '3種',
	'cat-4'       => '4種',
	'cat-w'       => '女子',
	'cat-f'       => 'フットサル',
	'cat-s'       => 'シニア',
);
$types = array( '' => 'すべて' ) + ( class_exists( 'NFA_Com_Categories' ) ? NFA_Com_Categories::filters() : array() );
$cur_c = nfa_news_filter();
$cur_t = nfa_news_type();
$link  = fn( $c, $t ) => nfa_news_url( $c, $t );   // パスの形（/news/news-info/committee/referee/）
global $wp_query;
$total = (int) wp_count_posts( 'post' )->publish;
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><span aria-current="page">お知らせ</span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span>NEWS</span></span></p><h1 class="pg-ja">お知らせ</h1></div><div class="pg-sub"><p>協会からのお知らせ、大会・講習会の案内、活動報告を掲載しています。<br class="pc"/>委員会・カテゴリごとに絞り込めます。</p><div class="meta"><span>掲載 <b><?php echo esc_html( number_format( $total ) ); ?>件</b></span></div></div></div></section>
<section class="blk"><div class="wrap">
<?php $alert = nfa_important_post(); if ( $alert && ! $cur_c && ! $cur_t && ! is_paged() ) : ?><a class="alert" href="<?php echo esc_url( nfa_post_url( $alert ) ); ?>"><b>重要なお知らせ</b><span><?php echo esc_html( get_the_title( $alert ) ); ?></span></a><?php endif; ?>
<div aria-label="絞り込み" class="filt" role="group">
<div class="row"><span>区分</span><?php foreach ( $areas as $k => $label ) : ?><a aria-selected="<?php echo $cur_c === $k ? 'true' : 'false'; ?>" class="pill" href="<?php echo esc_url( $link( $k, $cur_t ) ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?></div>
<div class="row"><span>カテゴリー</span><?php foreach ( $types as $k => $label ) : ?><a aria-selected="<?php echo $cur_t === $k ? 'true' : 'false'; ?>" class="pill" href="<?php echo esc_url( $link( $cur_c, $k ) ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?></div>
</div>
<p aria-live="polite" class="lp-n nl-count">該当 <b id="nlist-n"><?php echo esc_html( number_format( (int) $wp_query->found_posts ) ); ?></b> 件</p>
<div id="nlist">
<?php if ( have_posts() ) : ?>
<ul class="nl" id="nlist-ul"><?php while ( have_posts() ) : the_post(); echo nfa_news_li( get_post() ); endwhile; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
<?php else : ?>
<p class="lead2">この条件に当てはまるお知らせはありません。</p>
<?php endif; ?>
</div>
<?php echo nfa_pager(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div></section>
<?php
get_footer();
