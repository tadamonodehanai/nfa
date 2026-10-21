<?php
/**
 * お知らせ1件の本体（デザイン detail.html／detail-report.html／detail-important.html）。
 * single.php と templates/post-*.php から $args['variant'] を渡して使う。
 *   info       お知らせ・募集型（申込ボタン＝「リンク先URL」）
 *   report     開催報告型（本文に入れた画像のほか、記事に添付した画像を「写真」に並べる）
 *   important  重要なお知らせ型（注意書きの帯・関連資料）
 * どの型を使うかは、投稿の編集画面の「テンプレート」で選ぶ（カテゴリーとはまだ結び付けていない）。
 *
 * @package NFA2026
 */

$variant = $args['variant'] ?? 'info';
$labels  = array( 'info' => 'お知らせ', 'report' => '開催報告', 'important' => '重要なお知らせ' );
$post    = get_post();
$com     = nfa_committee_of( $post );
$c_ja    = $com ? (string) nfa_term_meta( $com, 'ja' ) : '';
$c_en    = $com ? (string) nfa_term_meta( $com, 'en' ) : '';
$c_name  = $c_ja ? $c_ja : ( $com ? $com->name : '' );
list( $badge, $color ) = 'important' === $variant ? array( '重要', 'var(--alert)' ) : nfa_badge( $post );
$c_badge = $com ? (string) nfa_term_meta( $com, 'badge' ) : '';
$c_badge = $c_badge ? $c_badge : ( $com ? $com->name : '' );
$list    = $com ? nfa_news_url( $com->slug ) : nfa_news_url();
$link    = function_exists( 'nfa_field' ) ? (string) nfa_field( 'nfa_link_url', $post->ID ) : '';
$event   = nfa_event_label( $post );
$updated = get_the_modified_date( 'Ymd' ) > get_the_date( 'Ymd' );
$arrow   = '<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span>';
$share   = rawurlencode( get_permalink() );
$title_e = rawurlencode( get_the_title() );
$prev    = get_previous_post();

// 添付：画像（開催報告の写真）と、ファイル（関連資料）
$thumb_id = (int) get_post_thumbnail_id( $post );
$photos   = 'report' === $variant ? array_filter( get_attached_media( 'image', $post ), fn( $a ) => $a->ID !== $thumb_id ) : array();
$files    = array_filter( get_attached_media( '', $post ), fn( $a ) => 0 !== strpos( $a->post_mime_type, 'image/' ) );
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><a href="<?php echo esc_url( nfa_news_url() ); ?>">お知らせ</a><span aria-hidden="true">›</span><?php if ( 'important' === $variant ) : ?><a href="<?php echo esc_url( nfa_news_url( '', 'news-important' ) ); ?>">重要</a><span aria-hidden="true">›</span><?php elseif ( $com ) : ?><a href="<?php echo esc_url( $list ); ?>"><?php echo esc_html( $c_badge ); ?></a><span aria-hidden="true">›</span><?php endif; ?><span aria-current="page"><?php the_title(); ?></span></nav>
<section class="blk dt-blk dt-<?php echo esc_attr( $variant ); ?>"><div class="wrap">
<div class="art">
<article class="dt-main">
<header class="art-h">
<div class="mt"><span class="dt"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span><?php if ( $updated ) : ?><span class="dt-up">更新 <span class="dt"><?php echo esc_html( get_the_modified_date( 'Y.m.d' ) ); ?></span></span><?php endif; ?><?php if ( $event ) : ?><span class="dt"><?php echo esc_html( $event ); ?></span><?php endif; ?></div>
<h1><?php the_title(); ?></h1>
<div class="dt-tags"><span class="badge" style="background:<?php echo esc_attr( $color ); ?>"><?php echo esc_html( $badge ); ?></span><span class="dt-cat is-plain"><?php echo esc_html( $labels[ $variant ] ); ?></span><?php if ( $com ) : ?><a class="dt-cat" href="<?php echo esc_url( get_term_link( $com ) ); ?>"><?php echo esc_html( $c_name ); ?></a><?php endif; ?></div>
</header>
<?php if ( 'important' === $variant ) : ?><div class="dt-alert" role="note"><b>重要なお知らせ</b><span>ご来場・ご参加の前に必ずお読みください。</span></div><?php endif; ?>
<?php if ( 'important' !== $variant && has_post_thumbnail() ) : ?><div class="art-eye"><?php the_post_thumbnail( 'large', array( 'alt' => '' ) ); ?></div><?php endif; ?>
<?php if ( has_excerpt() ) : ?><div class="prose dt-lead"><p><?php echo esc_html( get_the_excerpt() ); ?></p></div><?php endif; ?>
<div class="prose dt-body"><?php the_content(); ?></div>
<?php if ( $photos ) : ?>
<section class="dt-sec"><h2 class="dt-sh">写真</h2><div class="dt-gal"><?php foreach ( $photos as $ph ) : ?><figure><?php echo wp_get_attachment_image( $ph->ID, 'medium_large', false, array( 'alt' => get_the_title() . 'の様子' ) ); ?></figure><?php endforeach; ?></div></section>
<?php endif; ?>
<?php if ( $files ) : ?>
<section class="dt-sec" id="dt-files"><h2 class="dt-sh">関連資料</h2><ul class="dt-files"><?php foreach ( $files as $f ) : list( $cls, $ext ) = nfa_file_kind( wp_get_attachment_url( $f->ID ) ); ?><li><a class="fbtn" href="<?php echo esc_url( wp_get_attachment_url( $f->ID ) ); ?>" target="_blank"><i class="<?php echo esc_attr( $cls ); ?>"><?php echo esc_html( $ext ); ?></i><?php echo esc_html( get_the_title( $f ) ); ?></a></li><?php endforeach; ?></ul></section>
<?php endif; ?>
<?php if ( $link ) : ?><div class="dt-cta"><a class="btn btn-g" href="<?php echo esc_url( $link ); ?>" rel="noopener" target="_blank"><?php echo 'info' === $variant ? 'お申し込み・詳細' : 'リンク先を開く'; ?><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div><?php endif; ?>
<?php if ( function_exists( 'nfa_ai_search_source_notice' ) ) { nfa_ai_search_source_notice(); } ?>
<div class="dt-share"><span>SHARE</span><div class="share"><a aria-label="Xで共有" href="https://x.com/intent/post?url=<?php echo esc_attr( $share ); ?>&amp;text=<?php echo esc_attr( $title_e ); ?>" rel="noopener" target="_blank">X</a><a aria-label="Facebookで共有" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $share ); ?>" rel="noopener" target="_blank">f</a><a aria-label="LINEで送る" href="https://social-plugins.line.me/lineit/share?url=<?php echo esc_attr( $share ); ?>" rel="noopener" target="_blank">LINE</a></div></div>
<?php if ( $prev ) : ?><nav aria-label="前後の記事" class="pn"><a href="<?php echo esc_url( get_permalink( $prev ) ); ?>"><small>PREV</small><?php echo esc_html( get_the_title( $prev ) ); ?></a><span></span></nav><?php endif; ?>
<div class="dt-back"><a class="btn btn-o" href="<?php echo esc_url( 'important' === $variant ? nfa_news_url( '', 'news-important' ) : $list ); ?>"><?php echo esc_html( ( 'important' === $variant ? '重要' : ( $com ? $c_badge : '' ) ) . 'のお知らせ一覧へ' ); ?><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
</article>
<aside aria-label="関連情報" class="side">
<?php if ( $com ) : ?>
<div><h3>関連カテゴリ</h3><a class="dt-catlink" href="<?php echo esc_url( get_term_link( $com ) ); ?>"><span class="en"><?php echo esc_html( $c_en ); ?></span><b><?php echo esc_html( $c_name ); ?><?php if ( (string) nfa_term_meta( $com, 'tg' ) ) : ?><small>（<?php echo esc_html( nfa_term_meta( $com, 'tg' ) ); ?>）</small><?php endif; ?></b><span class="go">お知らせ・大会情報</span></a></div>
<div><h3><?php echo esc_html( $c_name ); ?>の新着</h3><ul class="nl no-th dt-new"><?php foreach ( nfa_news( array( 'committee' => $com->slug, 'n' => 4, 'exclude' => array( $post->ID ) ) ) as $p ) { echo nfa_news_li_noth( $p ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul></div>
<?php endif; ?>
</aside>
</div>
</div></section>
