<?php
/**
 * チームを探す（デザイン teams.html）。一覧は「チーム」（team）の投稿から。絞り込みはデザインのスクリプト（AND）のまま。
 *
 * @package NFA2026
 */

get_header();
$teams = nfa_teams();
?>

<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><a href="<?php echo esc_url( nfa_u( 'start.html' ) ); ?>">はじめる</a><span aria-hidden="true">›</span><span aria-current="page">チームを探す</span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span>TEAM SEARCH</span></span></p><h1 class="pg-ja">チームを探す</h1></div><div class="pg-sub"><p>長野県のサッカーチーム情報を掲載しています。<br class="pc"/>活動地域・活動競技・募集年代・募集性別・チーム名で絞り込めます。</p><div class="meta"><span>出典 <b>長野県サッカー協会 チーム情報</b></span><span>掲載 <b>14チーム（抜粋）</b></span></div></div></div></section>
<section class="blk lp-teams"><div class="wrap">
<form class="lp-kw sbar" data-demo="" onsubmit="return false" role="search">
<label class="vh" for="tlist-q">チーム名・市町村で探す</label>
<input class="inp" id="tlist-q" placeholder="チーム名・市町村を入力" type="search"/>
<button class="btn btn-n" type="submit">検索</button>
</form>
<div aria-label="絞り込み" class="filt lp-filt" role="group">
<div class="row" data-filter="tlist"><span>活動地域</span><button aria-selected="true" class="pill" data-f="all" type="button">すべて</button><button aria-selected="false" class="pill" data-f="hokushin" type="button">北信</button><button aria-selected="false" class="pill" data-f="toshin" type="button">東信</button><button aria-selected="false" class="pill" data-f="chushin" type="button">中信</button><button aria-selected="false" class="pill" data-f="nanshin" type="button">南信</button></div>
<div class="row" data-filter="tlist"><span>活動競技</span><button aria-selected="true" class="pill" data-f="all" type="button">すべて</button><button aria-selected="false" class="pill" data-f="soc" type="button">サッカー</button><button aria-selected="false" class="pill" data-f="fut" type="button">フットサル</button></div>
<div class="row" data-filter="tlist"><span>募集年代</span><button aria-selected="true" class="pill" data-f="all" type="button">すべて</button><button aria-selected="false" class="pill" data-f="a0" type="button">未就学児</button><button aria-selected="false" class="pill" data-f="a1" type="button">小学校低学年</button><button aria-selected="false" class="pill" data-f="a2" type="button">小学校高学年</button><button aria-selected="false" class="pill" data-f="a3" type="button">中学生年代</button><button aria-selected="false" class="pill" data-f="a4" type="button">高校生年代</button><button aria-selected="false" class="pill" data-f="a5" type="button">社会人<small>（年齢制限なし）</small></button><button aria-selected="false" class="pill" data-f="a40" type="button">40歳以上</button></div>
<div class="row" data-filter="tlist"><span>募集性別</span><button aria-selected="true" class="pill" data-f="all" type="button">すべて</button><button aria-selected="false" class="pill" data-f="m" type="button">男子</button><button aria-selected="false" class="pill" data-f="f" type="button">女子</button></div>
</div>
<div class="lp-bar">
<p aria-live="polite" class="lp-n">該当 <b id="tlist-n"><?php echo (int) count( $teams ); ?></b> チーム</p>
<p class="lp-note">活動地域の順に表示しています。</p>
</div>
<ul class="tm" id="tlist">
<?php
foreach ( $teams as $team ) {
	echo nfa_team_li( $team ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
?>
</ul>
<div class="lp-empty" hidden="" id="tlist-empty">
<p>この条件に当てはまるチームはありません。</p>
<button class="btn btn-o" data-reset="tlist" type="button">条件を解除する</button>
</div>
</div></section>
<section class="blk pale" id="contact"><div class="wrap">
<div class="bh"><div><div class="e">CONTACT</div><h2>チームへのお問い合わせについて</h2></div></div>
<div class="cards c2">
<article class="cd"><div class="bd"><div class="lab">HOW TO CONTACT</div><h3>お問い合わせの流れ</h3><p>各チームへのお問い合わせをご希望の方は、長野県サッカー協会までご連絡ください。協会にて内容を確認のうえ、該当チームよりご連絡を差し上げます。</p></div></article>
<a class="cd" href="<?php echo esc_url( nfa_u( 'contact.html' ) ); ?>"><div class="bd"><div class="lab">CONTACT</div><h3>お問い合わせ先</h3><p>長野県サッカー協会<br/>TEL：0263-87-8565<br/>メール：info@nagano-fa.or.jp</p><span class="go">お問い合わせ</span></div></a>
</div>
<p class="lp-small">ご返信までにお時間をいただく場合がございますので、あらかじめご了承ください。</p>
</div></section>
<script>
/* 複数の絞り込み行を AND で掛け合わせる（main.js の単一行絞り込みの後に実行） */
(function(d){
  var ID='tlist', T=d.getElementById(ID); if(!T) return;
  var groups=[].slice.call(d.querySelectorAll('[data-filter="'+ID+'"]'));
  var kw=d.getElementById(ID+'-q'), cnt=d.getElementById(ID+'-n'), empty=d.getElementById(ID+'-empty');
  function sel(g){ var b=g.querySelector('[aria-selected="true"]'); return b ? b.getAttribute('data-f') : 'all'; }
  function apply(){
    var fs=groups.map(sel), q=kw ? kw.value.trim() : '', n=0;
    [].forEach.call(T.querySelectorAll('[data-c]'), function(li){
      var c=' '+li.getAttribute('data-c')+' ';
      var ok=fs.every(function(f){ return f==='all' || c.indexOf(' '+f+' ')>-1; }) && (!q || li.textContent.indexOf(q)>-1);
      li.hidden=!ok; if(ok) n++;
    });
    if(cnt) cnt.textContent=n;
    if(empty) empty.hidden=n>0;
  }
  function pick(g, f){ [].forEach.call(g.querySelectorAll('[data-f]'), function(b){ b.setAttribute('aria-selected', b.getAttribute('data-f')===f); }); }
  d.addEventListener('click', function(e){
    var t=e.target.closest ? e.target.closest('[data-f],[data-reset="'+ID+'"]') : null; if(!t) return;
    if(t.hasAttribute('data-reset')){ groups.forEach(function(g){ pick(g,'all'); }); if(kw) kw.value=''; }
    else if(!t.closest('[data-filter="'+ID+'"]')) return;
    apply();
  });
  if(kw){ kw.addEventListener('input', apply); }
  var m=location.search.match(/[?&](cat|type|area|kind|sex|age|sport)=([\w-]+)/g);
  (m||[]).forEach(function(p){ var v=p.split('=')[1]; groups.forEach(function(g){ if(g.querySelector('[data-f="'+v+'"]')) pick(g,v); }); });
  apply();
})(document);
</script>
<?php
get_footer();
