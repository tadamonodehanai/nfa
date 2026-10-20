<?php
/**
 * チーム1件（デザイン team-34149.html など）。「チームを探す」の「チーム情報」から開く。
 * パンくずは デザインどおり「はじめる › チームを探す」の下（URL は /team/{スラッグ}/ のまま）。
 *
 * @package NFA2026
 */

get_header();
the_post();

$team  = get_post();
$days  = nfa_team_field( $team, 'team_activity_days' );
$days  = is_array( $days ) ? implode( '・', $days ) : (string) $days;
$areas = nfa_team_terms( $team, 'team_area' );
$city  = (string) nfa_team_field( $team, 'team_city' );
$site  = (string) nfa_team_field( $team, 'team_website_url' );
$pr    = (string) nfa_team_field( $team, 'team_pr_comment' );
$en    = array( 'hokushin' => 'HOKUSHIN', 'toushin' => 'TOSHIN', 'chushin' => 'CHUSHIN', 'nanshin' => 'NANSHIN' );
$slugs = nfa_team_terms( $team, 'team_area', 'slugs' );
$rows  = array(
	'活動競技'       => implode( '・', nfa_team_terms( $team, 'team_sport' ) ),
	'募集性別'       => nfa_team_gender( $team ),
	'募集年代'       => implode( '、', nfa_team_terms( $team, 'team_age' ) ),
	'活動地域'       => implode( '・', $areas ),
	'主な活動市町村' => $city,
	'活動日'         => $days,
	'活動頻度'       => (string) nfa_team_field( $team, 'team_activity_frequency' ),
	'活動場所'       => (string) nfa_team_field( $team, 'team_activity_location' ),
);
// 同じ地域のチーム（最大6件）
$same = array();
if ( $slugs ) {
	foreach ( nfa_teams() as $t ) {
		if ( $t->ID !== $team->ID && in_array( $slugs[0], nfa_team_terms( $t, 'team_area', 'slugs' ), true ) ) {
			$same[] = $t;
		}
		if ( count( $same ) >= 6 ) {
			break;
		}
	}
}
$arrow = '<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span>';
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><a href="<?php echo esc_url( nfa_u( 'start.html' ) ); ?>">はじめる</a><span aria-hidden="true">›</span><a href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>">チームを探す</a><span aria-hidden="true">›</span><span aria-current="page"><?php the_title(); ?></span></nav>
<section class="blk dt-blk tmd"><div class="wrap">
<div class="art">
<article class="dt-main">
<header class="art-h">
<div class="mt"><?php if ( $slugs ) : ?><span class="e tmd-area"><?php echo esc_html( $en[ $slugs[0] ] ?? '' ); ?></span><?php endif; ?><span class="dt-up">更新 <span class="dt"><?php echo esc_html( get_the_modified_date( 'Y.m.d' ) ); ?></span></span></div>
<h1><?php the_title(); ?></h1>
<div class="dt-tags"><?php if ( $areas ) : ?><span class="badge" style="background:var(--navy)"><?php echo esc_html( implode( '・', $areas ) . ( $city ? '・' . $city : '' ) ); ?></span><?php endif; ?><?php foreach ( array_filter( array_merge( nfa_team_terms( $team, 'team_sport' ), array( nfa_team_gender( $team ) ) ) ) as $t ) : ?><span class="dt-cat is-plain"><?php echo esc_html( $t ); ?></span><?php endforeach; ?></div>
</header>
<?php if ( has_post_thumbnail() ) : ?><div class="art-eye"><?php the_post_thumbnail( 'large', array( 'alt' => '' ) ); ?></div><?php endif; ?>
<?php if ( $pr ) : ?><div class="tmd-pr"><div class="e">MESSAGE</div><p><?php echo nl2br( esc_html( $pr ) ); ?></p></div><?php endif; ?>
<section class="dt-sec"><h2 class="dt-sh">チーム情報</h2><div class="tbl-w"><table class="tbl dt-tbl"><tbody>
<?php foreach ( $rows as $label => $value ) : if ( '' === trim( $value ) ) { continue; } ?>
<tr><th scope="row"><?php echo esc_html( $label ); ?></th><td><?php echo nl2br( esc_html( $value ) ); ?></td></tr>
<?php endforeach; ?>
<?php if ( $site ) : ?><tr><th scope="row">チームWebサイト</th><td><a href="<?php echo esc_url( $site ); ?>" rel="noopener" target="_blank"><?php echo esc_html( $site ); ?></a></td></tr><?php endif; ?>
</tbody></table></div></section>
<?php if ( get_the_content() ) : ?><div class="prose dt-body"><?php the_content(); ?></div><?php endif; ?>
<div class="dt-cta"><?php if ( $site ) : ?><a class="btn btn-n" href="<?php echo esc_url( $site ); ?>" rel="noopener" target="_blank">チームWebサイト <span class="arr">↗</span></a><?php endif; ?><a class="btn btn-o" href="#tmd-contact">お問い合わせの方法<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
<section class="dt-sec tmd-contact" id="tmd-contact"><h2 class="dt-sh">チームへのお問い合わせ</h2><p>各チームへのお問い合わせをご希望の方は、長野県サッカー協会までご連絡ください。協会にて内容を確認のうえ、該当チームよりご連絡を差し上げます。</p><p><a class="btn btn-g" href="<?php echo esc_url( nfa_u( 'contact.html' ) ); ?>">長野県サッカー協会に問い合わせる<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></p></section>
<div class="dt-back"><a class="btn btn-o" href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>">チーム一覧へ戻る<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
</article>
<aside aria-label="関連情報" class="side">
<?php if ( $same ) : ?><div><h3><?php echo esc_html( $areas[0] ); ?>のチーム</h3><ul class="tm tm-side"><?php foreach ( $same as $t ) { echo nfa_team_li( $t ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul></div><?php endif; ?>
</aside>
</div>
</div></section>
<?php
get_footer();
