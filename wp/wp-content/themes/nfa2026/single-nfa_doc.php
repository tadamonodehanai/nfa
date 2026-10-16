<?php
/**
 * 大会・リーグ資料1件。委員会ページの「大会」「リーグ」と同じ表で、資料の一覧を出す。
 *
 * @package NFA2026
 */

get_header();
the_post();

$doc       = get_post();
$committee = nfa_committee_of( $doc );
$c_ja      = $committee ? (string) nfa_term_meta( $committee, 'ja' ) : '';
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><?php if ( $committee ) : ?><a href="<?php echo esc_url( get_term_link( $committee ) ); ?>"><?php echo esc_html( $c_ja ); ?></a><span aria-hidden="true">›</span><?php endif; ?><span aria-current="page"><?php the_title(); ?></span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span><?php echo 'league' === ( function_exists( 'nfa_field' ) ? nfa_field( 'nfa_doc_kind', $doc->ID ) : '' ) ? 'LEAGUE' : 'TOURNAMENT'; ?></span></span></p><h1 class="pg-ja"><?php the_title(); ?></h1></div><div class="pg-sub"><p><?php echo esc_html( $c_ja ? $c_ja . 'の大会・リーグ資料です。' : '大会・リーグ資料です。' ); ?></p></div></div></section>
<section class="blk"><div class="wrap">
<div class="acc"><?php echo nfa_league_acc( $doc, 0 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
<?php if ( get_the_content() ) : ?><div class="prose" style="margin-top:32px"><?php the_content(); ?></div><?php endif; ?>
<?php if ( $committee ) : ?><div class="center"><a class="btn btn-o" href="<?php echo esc_url( get_term_link( $committee ) ); ?>"><?php echo esc_html( $c_ja ); ?>のページへ<?php echo nfa_btn_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div><?php endif; ?>
</div></section>
<?php
get_footer();
