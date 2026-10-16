<?php
/**
 * TOP（デザイン index.html）。
 *
 * ヒーロー・PICK UP …「TOPに掲載」の記事（なければ新しい順）
 * SCHEDULE & RESULT … 大会・リーグ資料（nfa_doc）の最新の更新。タブは担当委員会
 * NEWS …お知らせ（重要なお知らせは「重要」の最新1件）
 * VISION・GATES・PARTNERSHIP・RELATED LINKS … デザインどおりの固定の内容
 *
 * @package NFA2026
 */

get_header();
?>
<!-- A: FIRST VIEW -->
<section aria-label="注目のトピック" aria-roledescription="carousel" class="hero">
<?php
$hero_imgs = array( 'img/ph-hero1.jpg', 'img/ph-hero2.jpg', 'img/ph-hero3.jpg', 'img/ph-hero4.jpg' );
$hero      = nfa_top_posts( 4 );
foreach ( $hero_imgs as $i => $img ) :
	$p                     = $hero[ $i ] ?? null;
	list( $cap )           = $p ? nfa_badge( $p ) : array( 'お知らせ' );
	$src                   = $p && has_post_thumbnail( $p ) ? get_the_post_thumbnail_url( $p, 'full' ) : nfa_asset( $img );
	?>
<div class="slide<?php echo 0 === $i ? ' is-on' : ''; ?>" data-cap="<?php echo esc_attr( $cap ); ?>" data-txt="<?php echo esc_attr( $p ? get_the_title( $p ) : '' ); ?>"<?php echo $p ? ' data-href="' . esc_url( nfa_post_url( $p ) ) . '"' : ''; ?> data-k="<?php echo (int) $i; ?>"><img alt="" src="<?php echo esc_url( $src ); ?>"/></div>
<?php endforeach; ?>
<div class="hero-copy">
<div class="wrap">
<h1><span class="ib">ピッチの夢と</span><span class="ib">地域への貢献が、</span><br/><span class="ib">好循環で結ばれる</span><span class="ib">信州。</span></h1>
</div>
</div>
<div class="hero-ui">
<?php $h0 = $hero[0] ?? null; ?><a class="hero-cap" href="<?php echo esc_url( $h0 ? nfa_post_url( $h0 ) : nfa_news_url() ); ?>" id="heroCap"><b><?php echo esc_html( $h0 ? nfa_badge( $h0 )[0] : 'お知らせ' ); ?></b><span><?php echo esc_html( $h0 ? get_the_title( $h0 ) : '' ); ?></span></a>
<div class="hero-nav"><div aria-label="スライド切替" class="dots" role="tablist">
<button aria-label="スライド1" role="tab"><i></i></button>
<button aria-label="スライド2" role="tab"><i></i></button>
<button aria-label="スライド3" role="tab"><i></i></button>
<button aria-label="スライド4" role="tab"><i></i></button>
</div>
<div class="count en"><span id="cnt">01</span><small> / 04</small></div></div>
</div>
</section>
<!-- Schedule & Result -->
<section aria-label="日程・結果（カテゴリ別）" class="sched sched-top" data-scroller="" id="category">
<div class="sched-bar"><div class="wrap">
<h2>SCHEDULE &amp; RESULT</h2>
<div aria-label="カテゴリ" class="sched-tabs" data-filter="schedList" role="tablist"><button aria-selected="true" class="stab" data-f="all" type="button">すべて</button><button aria-selected="false" class="stab" data-f="cat-4" data-go="<?php echo esc_url( nfa_u( 'cat-4.html' ) ); ?>" data-go-label="小学生" type="button">小学生<small>4種</small></button><button aria-selected="false" class="stab" data-f="cat-3" data-go="<?php echo esc_url( nfa_u( 'cat-3.html' ) ); ?>" data-go-label="中学生" type="button">中学生<small>3種</small></button><button aria-selected="false" class="stab" data-f="cat-2" data-go="<?php echo esc_url( nfa_u( 'cat-2.html' ) ); ?>" data-go-label="高校生" type="button">高校生<small>2種</small></button><button aria-selected="false" class="stab" data-f="cat-1" data-go="<?php echo esc_url( nfa_u( 'cat-1.html' ) ); ?>" data-go-label="社会人・大学" type="button">社会人・大学<small>1種</small></button><button aria-selected="false" class="stab" data-f="cat-w" data-go="<?php echo esc_url( nfa_u( 'cat-w.html' ) ); ?>" data-go-label="女子" type="button">女子<small></small></button><button aria-selected="false" class="stab" data-f="cat-f" data-go="<?php echo esc_url( nfa_u( 'cat-f.html' ) ); ?>" data-go-label="フットサル" type="button">フットサル<small></small></button><button aria-selected="false" class="stab" data-f="cat-s" data-go="<?php echo esc_url( nfa_u( 'cat-s.html' ) ); ?>" data-go-label="シニア" type="button">シニア<small></small></button></div>
</div></div>
<div class="wrap sched-body">
<div class="sched-list" id="schedList"><?php foreach ( nfa_latest_rows( '', 20 ) as $r ) { echo nfa_sch( $r ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><button aria-label="前へ" class="sc-nav sc-prev" type="button"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M15 5l-7 7 7 7"></path></svg></button><button aria-label="次へ" class="sc-nav sc-next" type="button"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg></button>
</div>
<a class="sched-go" hidden="" href="<?php echo esc_url( nfa_u( 'cat-4.html' ) ); ?>" id="schedGo"><span class="wrap"><b><span>小学生</span></b><em>INDEX</em>へ<i aria-hidden="true">→</i></span></a>
</section>
<!-- PICK UP -->
<section class="sec">
<div class="wrap">
<div class="sh"><div class="e">PICK <i>UP</i></div><span class="j">ピックアップ</span></div>
<div class="pick"><?php
$pick = nfa_news( array( 'flag' => 'top', 'n' => 5, 'fresh' => true ) );
$pick = $pick ? $pick : nfa_news( array( 'n' => 5, 'fresh' => true ) );
foreach ( $pick as $p ) :
	list( $label, $color ) = nfa_badge( $p );
	?><a class="pk rv" href="<?php echo esc_url( nfa_post_url( $p ) ); ?>"><img alt="" src="<?php echo esc_url( nfa_thumb( $p ) ); ?>"/><div class="tx"><span class="badge" style="background:<?php echo esc_attr( $color ); ?>"><?php echo esc_html( $label ); ?></span><div class="date en"><?php echo esc_html( get_the_date( 'n.j', $p ) ); ?><small><?php echo esc_html( strtoupper( get_the_date( 'D', $p ) ) ); ?></small></div><h3><?php echo esc_html( get_the_title( $p ) ); ?></h3></div></a><?php endforeach; ?></div>
</div>
</section>
<!-- B: VISION（映像はイメージとして使用） -->
<section class="vision" id="vision">
<div class="vm-pin" id="vmPin">
<div class="vm-sticky">
<div class="vm" id="vm"><div class="vm-media" id="vmMedia"><video aria-hidden="true" autoplay="" id="vmVideo" muted="" playsinline="" poster="<?php echo esc_url( nfa_asset( 'img/mv1_poster.jpg' ) ); ?>" preload="auto" src="<?php echo esc_url( nfa_asset( 'video/mv1.mp4' ) ); ?>"></video><div aria-hidden="true" class="vm-word">LIFE<i>+</i>1</div></div></div>
<div class="vm-copy" id="vmCopy">
<div class="e">VISION</div><span class="j">ビジョン</span>
<h2><b>Life+1</b><span class="ib">長野県から全国、</span><span class="ib">そして世界へ</span></h2>
<p>「夢」から「挑戦」、「成長」そして「貢献」へ。NFAは、Life+1のコンセプトのもと、この好循環を生み出すことで、長野県サッカーの新たな歴史を創造します。</p>
<ol><li><span>01</span>夢</li><li><span>02</span>挑戦</li><li><span>03</span>成長</li><li><span>04</span>貢献</li></ol>
</div>
</div>
</div>
<div class="wrap">
<div class="pillars">
<div class="pl">
<div class="mv"><img alt="" src="<?php echo esc_url( nfa_asset( 'img/mv1_poster.jpg' ) ); ?>"/><video aria-hidden="true" data-src="<?php echo esc_url( nfa_asset( 'video/mv1.mp4' ) ); ?>" loop="" muted="" playsinline="" preload="none"></video><span class="no">01</span></div>
<div class="bd"><div class="k">夢<small>DREAM</small></div><p>サッカーに情熱を燃やす全ての人々の「夢」を育みます。</p></div>
</div>
<div class="pl">
<div class="mv"><img alt="" src="<?php echo esc_url( nfa_asset( 'img/mv2_poster.jpg' ) ); ?>"/><video aria-hidden="true" data-src="<?php echo esc_url( nfa_asset( 'video/mv2.mp4' ) ); ?>" loop="" muted="" playsinline="" preload="none"></video><span class="no">02</span></div>
<div class="bd"><div class="k">挑戦<small>CHALLENGE</small></div><p>質の高い指導と充実した環境を提供し、未来への「挑戦」をサポートします。</p></div>
</div>
<div class="pl">
<div class="mv"><img alt="" src="<?php echo esc_url( nfa_asset( 'img/mv3_poster.jpg' ) ); ?>"/><video aria-hidden="true" data-src="<?php echo esc_url( nfa_asset( 'video/mv3.mp4' ) ); ?>" loop="" muted="" playsinline="" preload="none"></video><span class="no">03</span></div>
<div class="bd"><div class="k">成長<small>GROWTH</small></div><p>選手たちは、挑戦を通して「成長」します。</p></div>
</div>
<div class="pl">
<div class="mv"><img alt="" src="<?php echo esc_url( nfa_asset( 'img/mv4_poster.jpg' ) ); ?>"/><video aria-hidden="true" data-src="<?php echo esc_url( nfa_asset( 'video/mv4.mp4' ) ); ?>" loop="" muted="" playsinline="" preload="none"></video><span class="no">04</span></div>
<div class="bd"><div class="k">貢献<small>CONTRIBUTION</small></div><p>地域社会へ「貢献」できる人材へと育ち、次の夢を育みます。</p></div>
</div>
</div>
</div>
</section>
<!-- C: GATEWAY -->
<section class="sec" id="gates">
<div class="wrap">
<div class="sh"><div class="e">FOR <i>YOU</i></div><span class="j">目的別のご案内</span><p>目的に合わせて、必要な情報をまとめています。</p></div>
<div class="gates">
<article class="gt">
<div class="ph"><img alt="少年大会でドリブルする選手" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/></div>
<div class="bd"><div class="lab">DEVELOPMENT</div><h3>育成・強化</h3><p class="who">選手、保護者、指導者・審判</p>
<ul><li><a href="https://www.nagano-fa.or.jp/news_top/technical_news/28084" rel="noopener" target="_blank">長野スタイル（育成コンセプト）<span>→</span></a></li><li><a href="<?php echo esc_url( nfa_u( 'technical.html' ) ); ?>">指導者ライセンス講習会<span>→</span></a></li><li><a href="<?php echo esc_url( nfa_u( 'referee.html' ) ); ?>">審判員の資格・講習会<span>→</span></a></li></ul>
<a aria-label="育成・強化について" class="gt-go" href="<?php echo esc_url( nfa_u( 'grow.html' ) ); ?>"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></a></div>
</article>
<article class="gt">
<div class="ph"><img alt="雪の上でリフティングする子ども" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/></div>
<div class="bd"><div class="lab">GET STARTED</div><h3>普及・体験</h3><p class="who">はじめての方とご家族、女子・シニア、障がい者サッカー</p>
<ul><li><a href="<?php echo esc_url( nfa_u( 'grassroots.html' ) ); ?>">キッズ・グラスルーツ<span>→</span></a></li><li><a href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>">チームを探す<span>→</span></a></li><li><a href="<?php echo esc_url( nfa_u( 'grow.html#register' ) ); ?>">選手・チーム登録<span>→</span></a></li></ul>
<a aria-label="普及活動について" class="gt-go" href="<?php echo esc_url( nfa_u( 'start.html' ) ); ?>"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></a></div>
</article>
<article class="gt">
<div class="ph"><img alt="校庭にラインを引く保護者" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/></div>
<div class="bd"><div class="lab">COMMUNITY</div><h3>地域・連携</h3><p class="who">自治体、企業・協賛社、地域クラブ・郡市協会</p>
<ul><li><a href="https://www.nagano-fa.or.jp/download" rel="noopener" target="_blank">共催・後援申請<span>→</span></a></li><li><a href="https://www.nagano-fa.or.jp/link" rel="noopener" target="_blank">郡市サッカー協会<span>→</span></a></li><li><a href="<?php echo esc_url( nfa_u( 'partner.html' ) ); ?>">協賛・パートナーシップ<span>→</span></a></li></ul>
<a aria-label="地域活動について" class="gt-go" href="<?php echo esc_url( nfa_u( 'partner.html' ) ); ?>"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></a></div>
</article>
</div>
</div>
</section>
<!-- D: AI SEARCH -->
<section class="ai" id="search">
<div aria-hidden="true" class="bgword">ASK NFA</div>
<div class="wrap">
<div class="sh"><div class="e">AI <i style="color:#7FD3FF">SEARCH</i></div><span class="j">探しているものを、そのまま聞いてください</span><p>どの委員会の担当かを考える必要はありません。知りたいことを言葉のまま入力すると、必要な情報にたどり着けます。</p></div>
<form action="<?php echo esc_url( nfa_u( 'search.html' ) ); ?>" class="sbox" id="sform" role="search">
<label class="sr" for="q" style="position:absolute;left:-9999px">質問を入力</label>
<input id="q" name="q" placeholder="例）小学生の大会っていつ？　審判の更新はどうすれば？" type="text"/>
<button type="submit">探す</button>
</form>
<div class="chips">
<button data-q="q" type="button">小学生（4種）</button><button data-q="q" type="button">中学生（3種）</button><button data-q="q" type="button">高校生（2種）</button><button data-q="q" type="button">社会人・大学（1種）</button><button data-q="q" type="button">女子</button><button data-q="q" type="button">シニア</button><button data-q="q" type="button">審判</button><button data-q="q" type="button">指導者</button><button data-q="q" type="button">フットサル</button>
</div>
</div>
</section>
<!-- E: NEWS -->
<section class="sec" id="news">
<div class="wrap">
<div class="sh"><div class="e">NEWS</div><span class="j">お知らせ</span></div>
<?php $alert = nfa_important_post(); if ( $alert ) : ?><a class="alert" href="<?php echo esc_url( get_permalink( $alert ) ); ?>"><b>重要なお知らせ</b><span><?php echo esc_html( get_the_title( $alert ) ); ?></span></a><?php endif; ?>
<div aria-label="ニュースの種類" class="tabs" data-filter="ngrid" role="tablist"><button aria-selected="true" data-f="all" role="tab">すべて</button><button aria-selected="false" data-f="info" role="tab">協会</button><button aria-selected="false" data-f="match" role="tab">大会・カテゴリ</button><button aria-selected="false" data-f="tech" role="tab">技術・医学</button><button aria-selected="false" data-f="ref" role="tab">審判</button><button aria-selected="false" data-f="kids" role="tab">グラスルーツ</button></div>
<div class="ngrid" id="ngrid"><?php foreach ( nfa_news( array( 'n' => 16, 'fresh' => true ) ) as $p ) { echo nfa_nc( $p ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
<div class="center"><a class="btn btn-o" href="<?php echo esc_url( nfa_u( 'list.html' ) ); ?>">お知らせ・大会情報をすべて見る<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></div>
</div>
</section>
<!-- COLUMN / REPORT -->
<section class="sec colw">
<div class="wrap">
<div class="sh"><div class="e">ACTIVITY <i>REPORT</i></div><span class="j">活動レポート</span><p>トレセン・講習会・フェスティバルなどの開催報告を掲載しています。</p></div>
<div class="cgrid"><?php
$reports = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 3, 's' => '報告', 'ignore_sticky_posts' => true ) );
$reports = $reports ? $reports : nfa_news( array( 'n' => 3 ) );
foreach ( $reports as $p ) :
	?><a class="cc" href="<?php echo esc_url( nfa_post_url( $p ) ); ?>"><div class="th"><img alt="" src="<?php echo esc_url( nfa_thumb( $p ) ); ?>"/></div><div class="bd"><div class="cat"><?php echo esc_html( nfa_badge( $p )[0] ); ?></div><h3><?php echo esc_html( get_the_title( $p ) ); ?></h3><div class="dt"><?php echo esc_html( get_the_date( 'Y.m.d', $p ) ); ?></div></div></a><?php endforeach; ?></div>
<div class="center"><a class="btn btn-o" href="<?php echo esc_url( nfa_u( 'list.html' ) ); ?>">お知らせ・活動報告をすべて見る<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></div>
</div>
</section>
<!-- F: PARTNERSHIP -->
<section class="sec" id="partner">
<div class="wrap part">
<div class="ph rv"><img alt="夕暮れの土手を並んで歩く親子" class="px" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/></div>
<div>
<div class="kicker" style="color:var(--cyan)">PARTNERSHIP</div>
<h2>スポンサー募集の<br/>お願い</h2>
<p class="lead">長野県サッカー協会では、キッズ（幼児）から女子およびシニアまで幅広い分野でサッカー競技の普及強化に取り組んでおります。本協会の趣旨をご理解いただき、スポンサーにお申し込みいただきますよう、お願い申し上げます。</p>
<ol class="prom"><li><span>01</span>広告掲載期間：1年単位</li><li><span>02</span>特典：ご協賛金額に合わせた各種メリットをご用意しています</li><li><span>03</span>掲載位置：当協会に一任</li></ol>
<div class="ctas"><a class="btn btn-g" href="<?php echo esc_url( nfa_u( 'partner.html' ) ); ?>">協賛について<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a><a class="btn btn-o" href="https://www.nagano-fa.or.jp/office_top/office_member/office_member_contents" rel="noopener" target="_blank">申込概要（既存サイト）<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></div>
</div>
</div>
</section>
<!-- RELATED LINKS -->
<section class="sec" style="padding-top:0">
<div class="wrap">
<div class="sh"><div class="e">RELATED <i>LINKS</i></div><span class="j">関連リンク</span></div>
<div class="lsub">JFA ─ 日本サッカー協会</div>
<div class="lnk">
<a href="https://www.jfa.jp/"><b>JFA</b><span>日本サッカー協会</span></a>
<a href="https://www.jfa.jp/samuraiblue/"><b>SAMURAI BLUE</b><span>サッカー日本代表</span></a>
<a href="https://www.jfa.jp/nadeshikojapan/"><b>NADESHIKO JAPAN</b><span>なでしこジャパン</span></a>
<a href="https://www.jfa.jp/"><b>PLAYERS FIRST</b><span>プレーヤーズファースト</span></a>
<a href="https://www.jfa.jp/"><b>RESPECT</b><span>リスペクトプロジェクト</span></a>
<a href="https://www.jfa.jp/"><b>DECLARATION</b><span>暴力等根絶宣言</span></a>
<a href="https://www.jiff.or.jp/"><b>JIFF</b><span>日本障がい者サッカー連盟</span></a>
<a href="https://www.jfa.jp/"><b>KICKOFF</b><span>JFA ID／登録システム</span></a>
</div>
<div class="lsub">県内クラブ</div>
<div class="lnk c3">
<a href="https://www.yamaga-fc.com/"><b>MATSUMOTO</b><span>松本山雅FC</span></a>
<a href="https://parceiro.co.jp/"><b>NAGANO</b><span>AC長野パルセイロ</span></a>
<a href="https://www.boaluz.jp/"><b>FUTSAL</b><span>ボアルース長野</span></a>
</div>
<div class="lsub" style="margin-top:44px">SPONSOR ─ 協賛企業</div>
<?php echo nfa_sponsors_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
</section>
<?php
get_footer();
