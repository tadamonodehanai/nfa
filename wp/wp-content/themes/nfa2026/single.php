<?php
/**
 * お知らせ1件（デザイン detail.html）。関連カテゴリ＝担当委員会。
 * 本文は投稿の本文（ブロックエディター）。開催概要の表・申込ボタンなどは本文の中で作る。
 *
 * @package NFA2026
 */

get_header();
the_post();

$post      = get_post();
$committee = nfa_committee_of( $post );
$c_ja      = $committee ? (string) nfa_term_meta( $committee, 'ja' ) : '';
$c_en      = $committee ? (string) nfa_term_meta( $committee, 'en' ) : '';
list( $badge, $color ) = nfa_badge( $post );
$files     = get_attached_media( 'application/pdf', $post );
$source    = (string) get_post_meta( $post->ID, '_nfa_source_url', true );
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><a href="<?php echo esc_url( nfa_u( 'list.html' ) ); ?>">お知らせ</a><span aria-hidden="true">›</span><span aria-current="page"><?php the_title(); ?></span></nav>
<section class="blk dt-blk"><div class="wrap">
<div class="art">
<article class="dt-main">
<header class="art-h">
<div class="mt"><span class="dt"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span><?php $ev = nfa_event_label( $post ); if ( $ev ) : ?><span class="dt"><?php echo esc_html( $ev ); ?></span><?php endif; ?></div>
<h1><?php the_title(); ?></h1>
<div class="dt-tags"><span class="badge" style="background:<?php echo esc_attr( $color ); ?>"><?php echo esc_html( $badge ); ?></span><?php if ( $committee ) : ?><a class="dt-cat" href="<?php echo esc_url( get_term_link( $committee ) ); ?>"><?php echo esc_html( $c_ja ? $c_ja : $committee->name ); ?></a><?php endif; ?></div>
</header>
<?php if ( has_post_thumbnail() ) : ?><div class="art-eye"><?php the_post_thumbnail( 'large', array( 'alt' => '' ) ); ?></div><?php endif; ?>
<?php if ( has_excerpt() ) : ?><div class="prose dt-lead"><p><?php echo esc_html( get_the_excerpt() ); ?></p></div><?php endif; ?>
<div class="prose dt-body"><?php the_content(); ?></div>
<?php $link = function_exists( 'nfa_field' ) ? (string) nfa_field( 'nfa_link_url', $post->ID ) : ''; if ( $link ) : ?><p><a class="btn btn-g" href="<?php echo esc_url( $link ); ?>" rel="noopener" target="_blank">リンク先を開く<?php echo nfa_btn_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></p><?php endif; ?>
<?php if ( $files ) : ?>
<section class="dt-sec"><h2 class="dt-sh" id="dt-files">関連資料</h2><ul class="dt-files"><?php foreach ( $files as $f ) : ?><li><a class="fbtn" href="<?php echo esc_url( wp_get_attachment_url( $f->ID ) ); ?>" rel="noopener" target="_blank"><i>PDF</i><?php echo esc_html( get_the_title( $f ) ); ?></a></li><?php endforeach; ?></ul></section>
<?php endif; ?>
<?php if ( $source ) : ?><p class="dt-src">出典：<a href="<?php echo esc_url( $source ); ?>" rel="noopener" target="_blank">長野県サッカー協会（既存サイト）の記事</a></p><?php endif; ?>
<?php if ( function_exists( 'nfa_ai_search_source_notice' ) ) { nfa_ai_search_source_notice(); } ?>
<div class="center"><a class="btn btn-o" href="<?php echo esc_url( nfa_u( 'list.html' ) ); ?>">お知らせ一覧に戻る<?php echo nfa_btn_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
</article>
<aside aria-label="関連情報" class="side">
<?php if ( $committee ) : ?>
<div><h3>関連カテゴリ</h3><a class="dt-catlink" href="<?php echo esc_url( get_term_link( $committee ) ); ?>"><span class="en"><?php echo esc_html( $c_en ); ?></span><b><?php echo esc_html( $c_ja ? $c_ja : $committee->name ); ?></b><span class="go">お知らせ・資料</span></a></div>
<div><h3><?php echo esc_html( $badge ); ?>の新着</h3><ul class="nl no-th dt-new"><?php foreach ( nfa_news( array( 'committee' => $committee->slug, 'n' => 3, 'exclude' => array( $post->ID ) ) ) as $p ) { echo nfa_news_li_noth( $p ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul></div>
<?php endif; ?>
</aside>
</div>
</div></section>
<?php
get_footer();
