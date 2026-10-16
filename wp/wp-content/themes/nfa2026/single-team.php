<?php
/**
 * チーム1件（「チームを探す」の「チーム情報」から開く）。記事詳細（detail.html）と同じ組み方で、チームの情報を表にする。
 *
 * @package NFA2026
 */

get_header();
the_post();

$team = get_post();
$days = nfa_team_field( $team, 'team_activity_days' );
$days = is_array( $days ) ? implode( '・', $days ) : (string) $days;
$rows = array(
	'活動地域' => implode( '・', nfa_team_terms( $team, 'team_area' ) ),
	'市町村'   => (string) nfa_team_field( $team, 'team_city' ),
	'活動競技' => implode( '・', nfa_team_terms( $team, 'team_sport' ) ),
	'募集年代' => implode( '・', nfa_team_terms( $team, 'team_age' ) ),
	'募集性別' => implode( '・', nfa_team_terms( $team, 'team_gender' ) ),
	'活動日'   => $days,
	'活動頻度' => (string) nfa_team_field( $team, 'team_activity_frequency' ),
	'活動場所' => (string) nfa_team_field( $team, 'team_activity_location' ),
);
$site = (string) nfa_team_field( $team, 'team_website_url' );
$pr   = (string) nfa_team_field( $team, 'team_pr_comment' );
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><a href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>">チームを探す</a><span aria-hidden="true">›</span><span aria-current="page"><?php the_title(); ?></span></nav>
<section class="blk dt-blk"><div class="wrap">
<div class="art">
<article class="dt-main">
<header class="art-h">
<div class="mt"><span class="dt">TEAM</span></div>
<h1><?php the_title(); ?></h1>
<div class="dt-tags"><?php foreach ( array_merge( nfa_team_terms( $team, 'team_sport' ), nfa_team_terms( $team, 'team_age' ), nfa_team_terms( $team, 'team_gender' ) ) as $t ) : ?><span class="badge" style="background:var(--navy)"><?php echo esc_html( $t ); ?></span><?php endforeach; ?></div>
</header>
<?php if ( has_post_thumbnail() ) : ?><div class="art-eye"><?php the_post_thumbnail( 'large', array( 'alt' => '' ) ); ?></div><?php endif; ?>
<?php if ( $pr ) : ?><div class="prose dt-lead"><p><?php echo nl2br( esc_html( $pr ) ); ?></p></div><?php endif; ?>
<section class="dt-sec"><h2 class="dt-sh">チーム情報</h2><div class="tbl-w"><table class="tbl dt-tbl"><tbody>
<?php foreach ( $rows as $label => $value ) : if ( '' === trim( $value ) ) { continue; } ?>
<tr><th scope="row"><?php echo esc_html( $label ); ?></th><td><?php echo nl2br( esc_html( $value ) ); ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php if ( $site ) : ?><p><a class="btn btn-g" href="<?php echo esc_url( $site ); ?>" rel="noopener" target="_blank">チームWebサイト<?php echo nfa_btn_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></p><?php endif; ?>
</section>
<?php if ( get_the_content() ) : ?><div class="prose dt-body"><?php the_content(); ?></div><?php endif; ?>
<div class="center"><a class="btn btn-o" href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>">チームを探すに戻る<?php echo nfa_btn_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
</article>
<aside aria-label="関連情報" class="side">
<div><h3>入団・選手登録</h3><a class="dt-catlink" href="<?php echo esc_url( nfa_u( 'grow.html#register' ) ); ?>"><span class="en">REGISTER</span><b>選手・チーム登録</b><span class="go">登録の手順</span></a></div>
<div><h3>はじめる</h3><a class="dt-catlink" href="<?php echo esc_url( nfa_u( 'start.html' ) ); ?>"><span class="en">GET STARTED</span><b>はじめる</b><span class="go">はじめかた・よくある質問</span></a></div>
</aside>
</div>
</div></section>
<?php
get_footer();
