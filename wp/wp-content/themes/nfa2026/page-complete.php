<?php
/**
 * フォームの送信完了ページ（/contact/complete/・/request-jfa/complete/）。
 * フォームを置いた固定ページの子ページ「complete」として作る。送信が済むと、フォームのページから自動でここへ移る（functions.php）。
 * 本文を入力すれば本文を、空なら親ページに合わせた初期の文を出す。検索エンジンには載せない（noindex）。
 *
 * @package NFA2026
 */

get_header();

$page   = get_queried_object();
$parent = $page->post_parent ? get_post( $page->post_parent ) : null;
$kind   = $parent ? $parent->post_name : '';
$texts  = array(
	'contact'     => array( 'CONTACT', 'お問い合わせを受け付けました', 'お問い合わせいただき、ありがとうございます。<br>内容を確認のうえ、担当者よりご連絡いたします。お返事までにお時間をいただく場合がございますので、あらかじめご了承ください。' ),
	'request-jfa' => array( 'REQUEST', '掲載依頼を受け付けました', 'JFAフェスティバルの掲載依頼をお送りいただき、ありがとうございます。<br>内容を確認のうえ、掲載いたします。確認のためにご連絡する場合があります。' ),
);
list( $en, $title, $default ) = $texts[ $kind ] ?? array( 'THANK YOU', '送信が完了しました', 'お送りいただき、ありがとうございます。' );
$content = trim( (string) $page->post_content );
$arrow   = '<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span>';
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><?php if ( $parent ) : ?><a href="<?php echo esc_url( get_permalink( $parent ) ); ?>"><?php echo esc_html( get_the_title( $parent ) ); ?></a><span aria-hidden="true">›</span><?php endif; ?><span aria-current="page">送信完了</span></nav>
<section class="nf-hero nf-complete"><div class="wrap">
<div aria-hidden="true" class="nf-code">THANKS</div>
<div class="e"><?php echo esc_html( $en ); ?></div>
<h1><?php echo esc_html( $title ); ?></h1>
<?php if ( '' !== $content ) : ?>
<div class="prose"><?php echo apply_filters( 'the_content', $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound ?></div>
<?php else : ?>
<p><?php echo wp_kses( $default, array( 'br' => array() ) ); ?></p>
<?php endif; ?>
<div class="ctas"><a class="btn btn-g" href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">トップページへ<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a><?php if ( $parent ) : ?><a class="btn btn-l" href="<?php echo esc_url( get_permalink( $parent ) ); ?>"><?php echo esc_html( get_the_title( $parent ) ); ?>へ戻る<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a><?php endif; ?></div>
</div></section>
<?php
get_footer();
