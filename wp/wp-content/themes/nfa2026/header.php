<?php
/**
 * ヘッダー（デザイン NFA_site_design の共通ヘッダー・SPメニュー）。
 *
 * @package NFA2026
 */
?><!doctype html>
<html <?php language_attributes(); ?> data-video-base="<?php echo esc_url( nfa_asset( 'video/' ) ); ?>" data-search-url="<?php echo esc_url( nfa_u( 'search.html' ) ); ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="pt" aria-hidden="true"><i></i></div>
<div class="sprog" aria-hidden="true"></div>
<?php if ( is_front_page() ) : ?><div class="curtain" aria-hidden="true"><i></i><b>LIFE + 1</b></div><?php endif; ?>
<header class="hd">
  <div class="wrap hd-top">
    <button class="menu-btn" type="button" aria-controls="drawer" aria-expanded="false" aria-label="メニュー"><span class="bars"><i></i><i></i><i></i></span><span class="lb">MENU</span></button>
    <a class="logo" href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>" aria-label="長野県サッカー協会 トップ">
      
      <span class="ab" aria-hidden="true">NFA</span><span class="nm"><b>長野県サッカー協会</b><small>NAGANO FOOTBALL ASSOCIATION</small></span>
    </a>
    <div class="hd-util">
      <a class="u-q" href="<?php echo esc_url( nfa_u( 'search.html' ) ); ?>"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>情報を探す</a>
      <a class="u-reg" href="<?php echo esc_url( nfa_u( 'grow.html#register' ) ); ?>">チーム・選手登録</a>
      <a class="u-spon" href="<?php echo esc_url( nfa_u( 'partner.html' ) ); ?>">協賛について</a>
    </div>
    <a class="hd-search" href="<?php echo esc_url( nfa_u( 'search.html' ) ); ?>" aria-label="情報を探す"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></a>
  </div>
  <nav class="nav" aria-label="メインメニュー">
    <ul><li><a href="<?php echo esc_url( nfa_u( 'about.html' ) ); ?>" <?php nfa_current( 'about.html' ); ?>><span>協会について</span></a></li><li data-mega><button type="button" aria-expanded="false" aria-controls="mega-cat"><span>カテゴリ・日程</span></button><div class="mega" id="mega-cat" hidden>
      <div class="wrap">
        <div class="mega-lead"><div class="e">CATEGORY</div><p>年代・種目ごとに、日程・試合結果・お知らせをまとめています。</p><a href="<?php echo esc_url( nfa_u( 'index.html#category' ) ); ?>">日程・結果を見る →</a></div>
        <div class="mega-grp"><div><h3>年代別</h3><ul><li><a href="<?php echo esc_url( nfa_u( 'cat-4.html' ) ); ?>"><span class="nm">小学生</span><span class="mt"><span class=tg>4種</span><em>U-12</em></span></a></li><li><a href="<?php echo esc_url( nfa_u( 'cat-3.html' ) ); ?>"><span class="nm">中学生</span><span class="mt"><span class=tg>3種</span><em>U-15</em></span></a></li><li><a href="<?php echo esc_url( nfa_u( 'cat-2.html' ) ); ?>"><span class="nm">高校生</span><span class="mt"><span class=tg>2種</span><em>U-18</em></span></a></li><li><a href="<?php echo esc_url( nfa_u( 'cat-1.html' ) ); ?>"><span class="nm">社会人・大学</span><span class="mt"><span class=tg>1種</span><em>ADULT</em></span></a></li></ul></div><div><h3>種目・対象別</h3><ul><li><a href="<?php echo esc_url( nfa_u( 'cat-w.html' ) ); ?>"><span class="nm">女子</span><span class="mt"><em>WOMEN</em></span></a></li><li><a href="<?php echo esc_url( nfa_u( 'cat-f.html' ) ); ?>"><span class="nm">フットサル</span><span class="mt"><em>FUTSAL</em></span></a></li><li><a href="<?php echo esc_url( nfa_u( 'cat-s.html' ) ); ?>"><span class="nm">シニア</span><span class="mt"><em>SENIOR</em></span></a></li><li><a href="<?php echo esc_url( nfa_u( 'grassroots.html' ) ); ?>"><span class="nm">キッズ</span><span class="mt"><em>KIDS</em></span></a></li></ul></div><div><h3>その他</h3><ul><li><a href="<?php echo esc_url( nfa_u( 'referee.html' ) ); ?>"><span class="nm">審判</span><span class="mt"><em>REFEREE</em></span></a></li><li><a href="<?php echo esc_url( nfa_u( 'technical.html' ) ); ?>"><span class="nm">指導者</span><span class="mt"><em>COACH</em></span></a></li><li><a href="<?php echo esc_url( nfa_u( 'medical.html' ) ); ?>"><span class="nm">医学</span><span class="mt"><em>MEDICAL</em></span></a></li></ul></div></div>
      </div>
    </div></li><li><a href="<?php echo esc_url( nfa_u( 'start.html' ) ); ?>" <?php nfa_current( 'start.html' ); ?>><span>はじめる</span></a></li><li><a href="<?php echo esc_url( nfa_u( 'grow.html' ) ); ?>" <?php nfa_current( 'grow.html' ); ?>><span>育成・強化</span></a></li><li><a href="<?php echo esc_url( nfa_u( 'partner.html' ) ); ?>" <?php nfa_current( 'partner.html' ); ?>><span>関わる</span></a></li><li><a href="<?php echo esc_url( nfa_u( 'list.html' ) ); ?>" <?php nfa_current( 'list.html' ); ?>><span>お知らせ</span></a></li></ul>
  </nav>
</header>
<div class="drawer" id="drawer" data-lenis-prevent hidden>
  <div class="dr-cta"><a href="<?php echo esc_url( nfa_u( 'grow.html#register' ) ); ?>">チーム・選手登録</a><a class="g" href="<?php echo esc_url( nfa_u( 'partner.html' ) ); ?>">協賛について</a></div>
  <form class="dr-q" action="<?php echo esc_url( nfa_u( 'search.html' ) ); ?>" role="search"><label class="vh" for="drq">サイト内を探す</label><input id="drq" name="q" type="search" placeholder="知りたいことを入力"><button type="submit" aria-label="探す"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></button></form>
  <button class="acc-h" type="button" data-acc aria-expanded="true" aria-controls="dr-cat">カテゴリ・日程</button>
  <div class="dr-cats" id="dr-cat"><a href="<?php echo esc_url( nfa_u( 'cat-4.html' ) ); ?>">小学生<small>4種</small></a><a href="<?php echo esc_url( nfa_u( 'cat-3.html' ) ); ?>">中学生<small>3種</small></a><a href="<?php echo esc_url( nfa_u( 'cat-2.html' ) ); ?>">高校生<small>2種</small></a><a href="<?php echo esc_url( nfa_u( 'cat-1.html' ) ); ?>">社会人・大学<small>1種</small></a><a href="<?php echo esc_url( nfa_u( 'cat-w.html' ) ); ?>">女子<small>女子委員会</small></a><a href="<?php echo esc_url( nfa_u( 'cat-f.html' ) ); ?>">フットサル<small>フットサル委員会</small></a><a href="<?php echo esc_url( nfa_u( 'cat-s.html' ) ); ?>">シニア<small>シニア委員会</small></a><a href="<?php echo esc_url( nfa_u( 'grassroots.html' ) ); ?>">キッズ<small>グラスルーツ</small></a><a href="<?php echo esc_url( nfa_u( 'referee.html' ) ); ?>">審判<small>審判委員会</small></a><a href="<?php echo esc_url( nfa_u( 'technical.html' ) ); ?>">指導者<small>技術委員会</small></a><a href="<?php echo esc_url( nfa_u( 'medical.html' ) ); ?>">医学<small>医学委員会</small></a></div>
  <button class="acc-h" type="button" data-acc aria-expanded="false" aria-controls="dr-about">協会について</button><ul class="dr-sub" id="dr-about" hidden><li><a href="<?php echo esc_url( nfa_u( 'about.html' ) ); ?>" <?php nfa_current( 'about.html' ); ?>>協会概要・ビジョン</a></li><li><a href="<?php echo esc_url( nfa_u( 'about.html#plan' ) ); ?>">中期経営計画</a></li><li><a href="<?php echo esc_url( nfa_u( 'about.html#org' ) ); ?>">組織・体制</a></li><li><a href="<?php echo esc_url( nfa_u( 'about.html#report' ) ); ?>">事業報告・決算</a></li></ul><button class="acc-h" type="button" data-acc aria-expanded="false" aria-controls="dr-start">はじめる</button><ul class="dr-sub" id="dr-start" hidden><li><a href="<?php echo esc_url( nfa_u( 'start.html' ) ); ?>" <?php nfa_current( 'start.html' ); ?>>はじめる</a></li><li><a href="<?php echo esc_url( nfa_u( 'start.html#steps' ) ); ?>">はじめかた</a></li><li><a href="<?php echo esc_url( nfa_u( 'start.html#faq' ) ); ?>">よくある質問</a></li><li><a href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>">チームを探す</a></li></ul><button class="acc-h" type="button" data-acc aria-expanded="false" aria-controls="dr-grow">育成・強化</button><ul class="dr-sub" id="dr-grow" hidden><li><a href="<?php echo esc_url( nfa_u( 'grow.html' ) ); ?>" <?php nfa_current( 'grow.html' ); ?>>育成・強化</a></li><li><a href="<?php echo esc_url( nfa_u( 'grow.html#register' ) ); ?>">選手・チーム登録</a></li><li><a href="<?php echo esc_url( nfa_u( 'referee.html' ) ); ?>">審判</a></li><li><a href="<?php echo esc_url( nfa_u( 'technical.html' ) ); ?>">指導者</a></li></ul><button class="acc-h" type="button" data-acc aria-expanded="false" aria-controls="dr-partner">関わる</button><ul class="dr-sub" id="dr-partner" hidden><li><a href="<?php echo esc_url( nfa_u( 'partner.html' ) ); ?>" <?php nfa_current( 'partner.html' ); ?>>協賛のご案内</a></li><li><a href="<?php echo esc_url( nfa_u( 'partner.html#plans' ) ); ?>">協賛のかたち</a></li><li><a href="<?php echo esc_url( nfa_u( 'partner.html#flow' ) ); ?>">お申し込みの流れ</a></li></ul>
  <div class="dr-plain"><a href="<?php echo esc_url( nfa_u( 'list.html' ) ); ?>" <?php nfa_current( 'list.html' ); ?>>お知らせ一覧</a><a href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>">チームを探す</a><a href="<?php echo esc_url( nfa_u( 'contact.html' ) ); ?>">お問い合わせ</a></div>
  <div class="dr-sns" aria-label="SNS"><span>X</span><span>IG</span><span>YT</span><span>FB</span></div>
</div>
<main<?php echo is_front_page() ? ' id="top"' : ''; ?>>
