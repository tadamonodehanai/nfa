<?php
/**
 * はじめる（デザイン start.html）。
 *
 * @package NFA2026
 */

get_header();
?>

<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><span aria-current="page">はじめる</span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span>GET STARTED</span></span></p><h1 class="pg-ja">はじめる</h1></div><div class="pg-sub"><p>サッカー・フットサルをはじめたい方に向けて、対象別のページ、チームの探し方と選手登録、キッズ年代の活動をご案内します。</p></div></div></section>
<nav aria-label="ページ内メニュー" class="lnav"><div class="lnav-in"><span class="lnav-lb">CONTENTS</span><ul><li><a href="#who"><span class="no">01</span><span class="t">対象別のご案内</span></a></li><li><a href="#how"><span class="no">02</span><span class="t">はじめかた</span></a></li><li><a href="#programs"><span class="no">03</span><span class="t">キッズ年代の活動</span></a></li><li><a href="#find"><span class="no">04</span><span class="t">チームを探す</span></a></li><li><a href="#news"><span class="no">05</span><span class="t">お知らせ</span></a></li></ul></div></nav>
<section aria-hidden="true" class="ph-hero"><div class="ph-img"><video autoplay="" loop="" muted="" playsinline="" poster="<?php echo esc_url( nfa_asset( 'img/mv1_poster.jpg' ) ); ?>" preload="metadata" src="<?php echo esc_url( nfa_asset( 'video/mv1.mp4' ) ); ?>"></video></div></section>
<!-- WHO -->
<section class="blk" id="who"><div class="wrap">
<div class="bh"><div><div class="e">FOR <i>YOU</i></div><h2>対象別のご案内</h2></div></div>
<p class="lead2">該当するものをお選びください。各種別のページで、日程・試合結果・お知らせを確認できます。</p>
<div class="cards sg-who sg-who-l">
<article class="cd"><div class="th"><img alt="ボールで遊ぶ子どもたち" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/></div><div class="bd"><div class="lab">KIDS / STUDENTS</div><h3>子ども・学生</h3>
<ul class="sg-tg">
<li><a href="<?php echo esc_url( nfa_u( 'grassroots.html' ) ); ?>"><span>キッズ<small>（グラスルーツ）</small></span></a></li>
<li><a href="<?php echo esc_url( nfa_u( 'cat-4.html' ) ); ?>"><span>小学生<small>（4種）</small></span></a></li>
<li><a href="<?php echo esc_url( nfa_u( 'cat-3.html' ) ); ?>"><span>中学生<small>（3種）</small></span></a></li>
<li><a href="<?php echo esc_url( nfa_u( 'cat-2.html' ) ); ?>"><span>高校生<small>（2種）</small></span></a></li>
</ul></div></article>
<article class="cd"><div class="th"><img alt="女子選手のプレー" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/></div><div class="bd"><div class="lab">WOMEN</div><h3>女子</h3>
<ul class="sg-tg">
<li><a href="<?php echo esc_url( nfa_u( 'cat-w.html' ) ); ?>"><span>女子<small>（女子委員会）</small></span></a></li>
<li><a href="<?php echo esc_url( nfa_u( 'teams.html?sex=f' ) ); ?>"><span>女子を募集しているチーム</span></a></li>
</ul></div></article>
<article class="cd"><div class="th"><img alt="社会人・シニアの試合" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/></div><div class="bd"><div class="lab">ADULTS / SENIOR</div><h3>大人・シニア</h3>
<ul class="sg-tg">
<li><a href="<?php echo esc_url( nfa_u( 'cat-1.html' ) ); ?>"><span>社会人・大学<small>（1種）</small></span></a></li>
<li><a href="<?php echo esc_url( nfa_u( 'cat-s.html' ) ); ?>"><span>シニア<small>（シニア委員会）</small></span></a></li>
<li><a href="<?php echo esc_url( nfa_u( 'cat-f.html' ) ); ?>"><span>フットサル<small>（フットサル委員会）</small></span></a></li>
</ul></div></article>
</div>
</div></section>
<!-- HOW -->
<section class="blk pale" id="how"><div class="wrap">
<div class="bh"><div><div class="e">HOW TO <i>START</i></div><h2>はじめかた（3ステップ）</h2></div></div>
<ol class="steps sg-steps">
<li><h3>チームを探す</h3><p>活動地域・活動競技・募集年代・募集性別などの条件から、長野県のサッカーチーム情報を探せます。</p><a class="sg-lk" href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>">チームを探す</a></li>
<li><h3>チームに問い合わせる</h3><p>各チームへのお問い合わせは、長野県サッカー協会までご連絡ください。協会にて内容を確認のうえ、該当チームよりご連絡を差し上げます。</p><a class="sg-lk" href="#contact">問い合わせ先</a></li>
<li><h3>チーム登録・選手登録</h3><p>JFA主催大会や北信越地域／長野県の各種大会へ出場するためには、JFAへのチーム登録および選手登録が必要になります。</p><a class="sg-lk" href="<?php echo esc_url( nfa_u( 'grow.html#register' ) ); ?>">選手・チーム登録について</a></li>
</ol>
</div></section>
<!-- PROGRAMS -->
<section class="blk" id="programs"><div class="wrap">
<div class="bh"><div><div class="e">GRASS<i>ROOTS</i></div><h2>キッズ年代の活動（グラスルーツ）</h2></div><a class="more" href="<?php echo esc_url( nfa_u( 'grassroots.html' ) ); ?>">グラスルーツのページ</a></div>
<p class="lead2"><span class="nw">U-10</span>年代を対象に、サッカーを通じて心身ともに健全な発育発達を促すことにより、身体を動かすことの「楽しさ」「大切さ」を体験してもらい、スポーツをはじめる機会、サッカーを本格的にはじめる機会を提供します。</p>
<ul class="sg-act">
<li><a href="https://www.nagano-fa.or.jp/news_top/kids_news/39645" rel="noopener" target="_blank">
<div class="th"><img alt="キッズサッカーフェスティバルの様子" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/></div>
<div class="bd"><span class="cat">JFAフェスティバル</span><h3><span class="ib">JFA キッズサッカー</span><span class="ib">フェスティバル</span></h3><p class="tx">キッズ年代の誰でも参加ができるサッカーのイベントです。様々なステーションで練習とゲームを経験するフェスティバル形式です。サッカー未経験の方も参加できます。</p></div>
<div class="mt"><span><b>開催</b>2026.09.22（火・祝）</span><span><b>会場</b>千曲川リバーフロント C面</span><span><b>対象</b>未就学児（年中・年長）・小学1〜3年生</span><span class="go">開催案内を見る</span></div>
</a></li>
<li><a href="<?php echo esc_url( nfa_u( 'grassroots.html' ) ); ?>">
<div class="th"><img alt="保育園でのサッカー教室の様子" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/></div>
<div class="bd"><span class="cat">グラスルーツ</span><h3>巡回指導</h3><p class="tx">郡市協会と法人格を持っているチーム及び団体と連携を図り、保育園・幼稚園への巡回サッカー教室を行い、幼児の運動能力の向上に寄与します。</p></div>
<div class="mt"><span><b>対象</b>保育園・幼稚園</span><span><b>連携</b>郡市協会・チーム及び団体</span><span class="go">詳しく見る</span></div>
</a></li>
<li><a href="<?php echo esc_url( nfa_u( 'grassroots.html' ) ); ?>">
<div class="th"><img alt="サッカースクールの様子" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/></div>
<div class="bd"><span class="cat">グラスルーツ</span><h3><span class="ib">サッカーフェスティバル・</span><span class="ib">サッカースクール</span></h3><p class="tx">郡市協会と法人格を持っているチーム及び団体と連携を図り、各地区でキッズフェスティバルやサッカースクールを開催し、サッカーを通してスポーツ好きな子どもの育成につなげます。</p></div>
<div class="mt"><span><b>種類</b>グラスルーツフェスティバル（初心者参加型）・フェスティバル・サッカースクール</span><span class="go">詳しく見る</span></div>
</a></li>
</ul>
<p class="sg-note">グラスルーツ委員会は、旧キッズ委員会が<span class="nw">2016年度</span>より新たにスタートした委員会です。開催案内は<a href="https://www.nagano-fa.or.jp/category/news_top/kids_news" rel="noopener" target="_blank">グラスルーツからのお知らせ</a>に掲載しています。</p>
</div></section>
<!-- FIND -->
<section class="blk pale" id="find"><div class="wrap">
<div class="bh"><div><div class="e">FIND A <i>TEAM</i></div><h2>チームを探す</h2></div><a class="more" href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>">チーム一覧</a></div>
<div class="sg-find">
<form action="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>" class="sg-fbox">
<p>長野県のサッカーチーム情報を、条件から探せます。</p>
<div class="sg-row"><span class="sg-k">活動地域</span><div class="sg-pills">
<button aria-pressed="false" class="pill" type="button">北信</button><button aria-pressed="false" class="pill" type="button">東信</button><button aria-pressed="false" class="pill" type="button">中信</button><button aria-pressed="false" class="pill" type="button">南信</button>
</div></div>
<div class="sg-row"><span class="sg-k">活動競技</span><div class="sg-pills">
<button aria-pressed="false" class="pill" type="button">サッカー</button><button aria-pressed="false" class="pill" type="button">フットサル</button>
</div></div>
<div class="sg-row"><span class="sg-k">募集年代</span><div class="sg-pills">
<button aria-pressed="false" class="pill" type="button">未就学児</button><button aria-pressed="false" class="pill" type="button">小学校低学年</button><button aria-pressed="false" class="pill" type="button">小学校高学年</button><button aria-pressed="false" class="pill" type="button">中学生年代</button><button aria-pressed="false" class="pill" type="button">高校生年代</button><button aria-pressed="false" class="pill" type="button">社会人<small>（年齢制限なし）</small></button><button aria-pressed="false" class="pill" type="button">40歳以上</button>
</div></div>
<a class="btn btn-g sg-btn" href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>">チームを探す<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a>
</form>
<div class="sg-map"><img alt="県内4地区（北信・東信・中信・南信）のイメージ" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/><ul aria-hidden="true"><li>北信</li><li>東信</li><li>中信</li><li>南信</li></ul></div>
</div>
</div></section>
<!-- NEWS -->
<section class="blk" id="news"><div class="wrap">
<div class="bh"><div><div class="e">NEWS</div><h2>グラスルーツからのお知らせ</h2></div><a class="more" href="https://www.nagano-fa.or.jp/category/news_top/kids_news" rel="noopener" target="_blank">一覧を見る</a></div>
<ul class="nl">
<li data-c="event"><a href="https://www.nagano-fa.or.jp/news_top/kids_news/39645" rel="noopener" target="_blank"><span class="th"><img alt="" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/><span class="badge" style="background:var(--tag-event)">参加募集</span></span><span><span class="mt"><span class="dt">2026.09.08</span></span><h3>JFA キッズサッカーフェスティバル 2026 長野 in 千曲川リバーフロント 開催（開催日：2026.09.22）</h3></span><span aria-hidden="true" class="ar"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></li>
<li data-c="event"><a href="https://www.nagano-fa.or.jp/news_top/kids_news/38528" rel="noopener" target="_blank"><span class="th"><img alt="" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/><span class="badge" style="background:var(--tag-event)">イベント</span></span><span><span class="mt"><span class="dt">2026.05.13</span></span><h3>JFA キッズ（<span class="nw">U-8</span>）サッカーフェスティバル2026 in 佐久（開催日：2026.06.13）</h3></span><span aria-hidden="true" class="ar"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></li>
<li data-c="event"><a href="https://www.nagano-fa.or.jp/news_top/kids_news/38105" rel="noopener" target="_blank"><span class="th"><img alt="" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/><span class="badge" style="background:var(--tag-event)">イベント</span></span><span><span class="mt"><span class="dt">2026.04.08</span></span><h3>JFA キッズサッカーフェスティバル 2026 長野 in 千曲川リバーフロント 開催（開催日：2026.05.05）</h3></span><span aria-hidden="true" class="ar"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></li>
<li data-c="event"><a href="https://www.nagano-fa.or.jp/news_top/kids_news/37580" rel="noopener" target="_blank"><span class="th"><img alt="" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/><span class="badge" style="background:var(--tag-event)">イベント</span></span><span><span class="mt"><span class="dt">2026.03.02</span></span><h3>NFA ウォーキングフットボールフェスティバル in 高森町山吹ほたるパーク 開催（開催日：2026.03.22）</h3></span><span aria-hidden="true" class="ar"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></li>
<li data-c="event"><a href="https://www.nagano-fa.or.jp/news_top/kids_news/37342" rel="noopener" target="_blank"><span class="th"><img alt="" loading="lazy" src="<?php echo esc_url( nfa_asset( 'img/ph-photo.jpg' ) ); ?>"/><span class="badge" style="background:var(--tag-event)">参加募集</span></span><span><span class="mt"><span class="dt">2026.01.07</span></span><h3>「BLUE DREAM みらいスクール in 長野」参加者募集</h3></span><span aria-hidden="true" class="ar"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></li>
</ul>
</div></section>
<section class="cband" id="contact"><div class="wrap"><div class="cband-tx"><p class="cband-e">CONTACT</p><h2>チームへのお問い合わせ</h2><p>各チームへのお問い合わせは、長野県サッカー協会までご連絡ください。協会にて内容を確認のうえ、該当チームよりご連絡を差し上げます。ご返信までにお時間をいただく場合があります。</p><p class="cband-info">TEL 0263-87-8565<br/>E-mail <a href="mailto:info@nagano-fa.or.jp">info@nagano-fa.or.jp</a></p></div><div class="cband-act"><a class="btn btn-g" href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>"><span>チームを探す</span><span aria-hidden="true" class="arr"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a><a class="btn btn-l" href="<?php echo esc_url( nfa_u( 'contact.html' ) ); ?>"><span>事務局に問い合わせる</span><span aria-hidden="true" class="arr"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></div></div></section>
<?php
get_footer();
