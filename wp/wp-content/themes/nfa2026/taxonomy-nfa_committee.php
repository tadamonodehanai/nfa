<?php
/**
 * 委員会ページ（カテゴリトップ）。デザイン cat-1〜4・cat-w・cat-f・cat-s・grassroots・referee・technical・medical の11ページを1枚で出す。
 *
 * 構成は委員会の入力欄「ページの構成」で切り替える：
 *   full       … 最新の更新・大会・リーグ・お知らせ・過去の結果（cat-1〜4）
 *   tournament … 最新の更新・大会・お知らせ・過去の結果（女子・フットサル・シニア）
 *   info       … 最新のお知らせ・説明（template-parts/committee/{スラッグ}.php があれば）・お知らせ（キッズ・審判・指導者・医学）
 * 関連ページの欄は template-parts/committee/{スラッグ}-guide.php があれば出す（キッズ・審判・指導者・女子）。
 * 大会・リーグ・最新の更新は「大会・リーグ資料（nfa_doc）」から、お知らせは投稿から自動で並ぶ。
 * 大会・リーグは最新の年度だけ。前の年度は「過去の日程・試合結果」（/committee/{スラッグ}/archive/）。
 *
 * @package NFA2026
 */

get_header();

$term   = get_queried_object();
$slug   = $term->slug;
$layout = (string) nfa_term_meta( $term, 'layout' );
$layout = in_array( $layout, array( 'full', 'tournament', 'info' ), true ) ? $layout : 'info';
$ja     = (string) nfa_term_meta( $term, 'ja' );
$ja     = $ja ? $ja : $term->name;
$tg     = (string) nfa_term_meta( $term, 'tg' );
$en     = (string) nfa_term_meta( $term, 'en' );
$lead   = (string) nfa_term_meta( $term, 'lead' );
$upd    = nfa_last_updated( $slug );
$extra  = locate_template( 'template-parts/committee/' . $slug . '.php' );
$guide  = locate_template( 'template-parts/committee/' . $slug . '-guide.php' );
$news   = nfa_news( array( 'committee' => $slug, 'n' => 8 ) );

// 大会・リーグは最新の年度だけ。前の年度は「過去の日程・試合結果」のページへ
$years   = nfa_doc_years( $slug );
$year    = $years[0] ?? 0;
$past    = array_slice( $years, 1 );
$arc_url = function_exists( 'nfa_committee_archive_url' ) ? nfa_committee_archive_url( $slug ) : '';

// ページ内メニュー
$lnav = array( 'sched' => 'info' === $layout ? '最新のお知らせ' : '最新の更新' );
if ( 'info' !== $layout ) {
	$lnav['tournament'] = '大会';
	if ( 'full' === $layout ) {
		$lnav['league'] = 'リーグ';
	}
	if ( $guide ) {
		$lnav['guide'] = '関連ページ';
	}
	$lnav['news'] = 'お知らせ';
	if ( $past ) {
		$lnav['archive'] = '過去の結果';
	}
} else {
	$extra_titles = array(
		'grassroots' => array( 'about' => 'グラスルーツについて' ),
		'referee'    => array( 'about' => '審判員について', 'course' => '講習会・研修' ),
		'technical'  => array( 'about' => '指導者講習会について', 'license' => 'ライセンス' ),
		'medical'    => array( 'topics' => 'トピック別の情報' ),
	);
	$lnav = array_merge( $lnav, $extra_titles[ $slug ] ?? array(), $guide ? array( 'guide' => '関連ページ' ) : array(), array( 'news' => 'お知らせ' ) );
}
?>
<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><a href="<?php echo esc_url( nfa_u( 'index.html#category' ) ); ?>">カテゴリ・日程</a><span aria-hidden="true">›</span><span aria-current="page"><?php echo esc_html( $ja ); ?></span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p class="pg-pre">CATEGORY</p><p aria-hidden="true" class="pg-en"><span class="ln"><span><?php echo esc_html( $en ); ?></span></span></p><h1 class="pg-ja"><?php echo esc_html( $ja ); ?><?php if ( $tg ) : ?><span class="tg"><?php echo esc_html( $tg ); ?></span><?php endif; ?></h1></div><div class="pg-sub"><p><?php echo esc_html( $lead ); ?></p><div class="meta"><?php if ( $upd ) : ?><span>最終更新 <b><?php echo esc_html( $upd ); ?></b></span><?php endif; ?><?php if ( $past && $arc_url ) : ?><span><a href="<?php echo esc_url( $arc_url ); ?>">過去の日程・試合結果 →</a></span><?php endif; ?></div></div></div></section>
<nav aria-label="ページ内メニュー" class="lnav"><div class="lnav-in"><span class="lnav-lb">CONTENTS</span><ul><?php $no = 0; foreach ( $lnav as $id => $t ) : $no++; ?><li><a href="#<?php echo esc_attr( $id ); ?>"><span class="no"><?php echo esc_html( sprintf( '%02d', $no ) ); ?></span><span class="t"><?php echo esc_html( $t ); ?></span></a></li><?php endforeach; ?></ul></div></nav>

<?php // 01 最新の更新（資料）／最新のお知らせ ?>
<div id="sched">
<section aria-label="<?php echo esc_attr( $lnav['sched'] ); ?>" class="sched sched-top" data-scroller=""><div class="sched-bar"><div class="wrap"><h2><?php echo 'info' === $layout ? 'LATEST' : 'LATEST UPDATE'; ?></h2></div></div><div class="wrap sched-body"><div class="sched-list"><?php
if ( 'info' === $layout ) {
	list( $badge ) = array( (string) nfa_term_meta( $term, 'badge' ) );
	foreach ( array_slice( $news, 0, 6 ) as $p ) {
		printf(
			'<a class="sch" href="%s"><span class="t"><b class="pre">%s</b><span>%s</span></span><span class="m">%s</span>%s</a>',
			esc_url( get_permalink( $p ) ),
			esc_html( $badge ? $badge : $term->name ),
			esc_html( get_the_date( 'Y.m.d', $p ) ),
			esc_html( wp_html_excerpt( get_the_title( $p ), 70, '...' ) ),
			nfa_arrow() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
} else {
	foreach ( nfa_latest_rows( $slug, 10 ) as $r ) {
		echo nfa_sch( $r ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
?></div><button aria-label="前へ" class="sc-nav sc-prev" type="button"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"></path></svg></button><button aria-label="次へ" class="sc-nav sc-next" type="button"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg></button></div></section></div>

<?php if ( 'info' !== $layout ) : ?>
<?php $tournaments = nfa_docs( array( 'committee' => $slug, 'kind' => 'full' === $layout ? 'tournament' : '', 'year' => $year ) ); ?>
<section class="blk" id="tournament"><div class="wrap">
<div class="bh"><div><div class="e">TOURNAMENT</div><h2>大会</h2></div></div>
<?php if ( $tournaments ) : ?>
<div class="trn-grid"><?php foreach ( $tournaments as $doc ) { echo nfa_trn( $doc ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
<?php else : ?>
<p class="lead2">いま掲載している大会資料はありません。</p>
<?php endif; ?>
</div></section>

	<?php if ( 'full' === $layout ) : ?>
		<?php $leagues = nfa_docs( array( 'committee' => $slug, 'kind' => 'league', 'year' => $year ) ); ?>
<section class="blk pale" id="league"><div class="wrap">
<div class="bh"><div><div class="e">LEAGUE</div><h2>リーグ</h2></div></div><p class="lead2">日程表・星取表はPDFで掲載しています。「準備中」はまだ掲載されていない資料です。</p>
		<?php if ( $leagues ) : ?>
<div class="acc"><?php foreach ( $leagues as $i => $doc ) { echo nfa_league_acc( $doc, $i ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>
</div></section>
	<?php endif; ?>
<?php elseif ( $extra ) : ?>
	<?php include $extra; // 説明セクション（about・license など） ?>
	<?php if ( 'referee' === $slug ) : ?>
		<?php
		$courses = get_posts(
			array(
				'post_type'      => 'post',
				'posts_per_page' => 6,
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery
					'relation' => 'AND',
					array( 'taxonomy' => NFA_COMMITTEE_TAX, 'field' => 'slug', 'terms' => 'referee' ),
					array( 'taxonomy' => 'nfa_purpose', 'field' => 'slug', 'terms' => 'course' ),
				),
			)
		);
		?>
<section class="blk pale" id="course"><div class="wrap">
<div class="bh"><div><div class="e">COURSE</div><h2>講習会・研修のお知らせ</h2></div><a class="more" href="<?php echo esc_url( nfa_u( 'referee-schedule.html' ) ); ?>">年間計画</a></div><p class="lead2">各種講習会の正式な案内は、原則開催日の1か月前までに掲載します。</p><ul class="nl no-th"><?php
		foreach ( $courses as $p ) {
			list( $label, $color ) = nfa_badge( $p );
			printf(
				'<li data-c="referee"><a href="%s"><span><span class="mt"><span class="badge" style="background:%s">%s</span><span class="dt">%s</span></span><h3>%s</h3></span><span aria-hidden="true" class="ar"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></li>',
				esc_url( get_permalink( $p ) ),
				esc_attr( $color ),
				esc_html( $label ),
				esc_html( get_the_date( 'Y.m.d', $p ) ),
				esc_html( get_the_title( $p ) )
			);
		}
		?></ul></div></section>
	<?php endif; ?>
<?php endif; ?>
<?php if ( $guide ) { include $guide; } // 関連ページ ?>

<section class="blk" id="news"><div class="wrap">
<div class="bh"><div><div class="e">NEWS</div><h2><?php echo esc_html( $ja ); ?>のお知らせ</h2></div><a class="more" href="<?php echo esc_url( nfa_news_url( $slug ) ); ?>">一覧を見る</a></div>
<?php if ( $news ) : ?>
<ul class="nl"><?php foreach ( array_slice( $news, 0, 6 ) as $p ) { echo nfa_news_li( $p ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
<?php else : ?>
<p class="lead2">いま掲載しているお知らせはありません。</p>
<?php endif; ?>
</div></section>

<?php if ( 'info' !== $layout && $past ) : // 過去の日程・試合結果：前の年度（ページ内の年度の位置へ） ?>
<section class="blk" id="archive"><div class="wrap">
<div class="bh"><div><div class="e">ARCHIVE</div><h2>過去の日程・試合結果</h2></div></div><div class="yrs"><?php foreach ( $past as $y ) : ?><a href="<?php echo esc_url( $arc_url . '#y' . $y ); ?>"><?php echo esc_html( $y ); ?>年度</a><?php endforeach; ?></div></div></section>
<?php endif; ?>

<section class="catsec"><div class="wrap">
<div class="bh"><div><div class="e">OTHER CATEGORY</div><h2>ほかのカテゴリ</h2></div></div><div class="catgrid"><?php echo nfa_catgrid( $slug ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a class="ct ct-teams" href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>"><span class="lb"><span class="en">TEAMS</span></span><b>チームを探す</b><span class="ft2">県内の登録チーム<i>→</i></span></a></div></div></section>
<?php echo nfa_cband( $term ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<?php
get_footer();
