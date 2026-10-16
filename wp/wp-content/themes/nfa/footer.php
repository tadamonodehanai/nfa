<?php
/**
 * <meta content="charset=UTF-8">
 * @package nfa
 */
$url=get_bloginfo('url');
$isHome=getIsHome();
$mypageinfo=get_mypageinfo();

$assets_url = get_assets_url();

?>
<!-- ===== スポンサー帯（全ページ共通テンプレート） ===== -->
<section class="sponsorBand">
  <div class="sbIn">
    <div class="sbHead">
      <p class="sbTtl">SPONSOR</p>
      <a href="<?= getPageUrl('partner');?>" class="sbMore">協賛について ─ ともに創る</a>
    </div>
    <div class="sbGrid">
      <a class="sbCell gold" href="https://www.izumi.maxell.co.jp/" target="_blank" rel="noopener"><span class="lb">GOLD PARTNER</span><span class="nm">マクセルイズミ株式会社</span></a>
      <a class="sbCell" href="https://holdings.alpico.co.jp/" target="_blank" rel="noopener"><span class="lb">OFFICIAL</span><span class="nm">アルピコグループ</span></a>
      <a class="sbCell" href="https://www.tsb.jp/" target="_blank" rel="noopener"><span class="lb">OFFICIAL</span><span class="nm">テレビ信州</span></a>
      <a class="sbCell" href="https://www.adbureau.co.jp/" target="_blank" rel="noopener"><span class="lb">OFFICIAL</span><span class="nm">ながのアド・ビューロ</span></a>
      <a class="sbCell" href="http://www.richade.co.jp/" target="_blank" rel="noopener"><span class="lb">OFFICIAL</span><span class="nm">リッチエイド</span></a>
      <a class="sbCell" href="https://www.abn-tv.co.jp/" target="_blank" rel="noopener"><span class="lb">OFFICIAL</span><span class="nm">abn長野放送</span></a>
      <a class="sbCell" href="http://tutti-armonia.jp/" target="_blank" rel="noopener"><span class="lb">OFFICIAL</span><span class="nm">Tutti</span></a>
      <a class="sbCell" href="https://www.mizuho-sc.com/index.html" target="_blank" rel="noopener"><span class="lb">OFFICIAL</span><span class="nm">みずほ証券</span></a>
      <a class="sbCell" href="https://www.meijiyasuda.co.jp/" target="_blank" rel="noopener"><span class="lb">OFFICIAL</span><span class="nm">明治安田生命</span></a>
      <a class="sbCell" href="https://www.adidas.jp/" target="_blank" rel="noopener"><span class="lb">OFFICIAL</span><span class="nm">アディダスジャパン</span></a>
      <a class="sbCell" href="https://www.realsports.jp/" target="_blank" rel="noopener"><span class="lb">OFFICIAL</span><span class="nm">レアルスポーツ</span></a>
    </div>
    <div class="sbFoot">
      <span class="lb">SUPPORTERS</span>
      <a href="http://www.tombow.gr.jp/" target="_blank" rel="noopener">トンボ学生服</a>
      <span class="co">ファーストビジネス株式会社</span>
      <a href="https://www.m-t-i.jp/" target="_blank" rel="noopener">株式会社松本総合保険</a>
    </div>
  </div>
</section>

<!-- ================= フッター ================= -->
<footer>
  <div class="wrap">
    <div class="fCols">
      <div class="fBrand">
        <div class="logo">
          <span class="mark en">NFA</span>
          <span class="txt">長野県サッカー協会<small class="en">NAGANO FOOTBALL ASSOCIATION</small></span>
        </div>
        <p>〒◯◯◯-◯◯◯◯　長野県◯◯市◯◯<br>TEL ◯◯◯-◯◯◯-◯◯◯◯（平日 9:00–17:00）</p>
      </div>
      <div>
        <h5>NFA について</h5>
        <ul>
          <li><a href="<?= getPageUrl('about');?>">Life+1 コンセプト</a></li>
          <li><a href="<?= getPageUrl('about');?>">2020年宣言／2030計画</a></li>
          <li><a href="<?= getPageUrl('about');?>">協会概要・組織</a></li>
          <li><a href="<?= getPageUrl('about');?>">事業報告・決算</a></li>
        </ul>
      </div>
      <div>
        <h5>目的から探す</h5>
        <ul>
          <li><a href="<?= getPageUrl('start');?>">はじめる・つづける</a></li>
          <li><a href="<?= getPageUrl('grow');?>">強くなる・関わる</a></li>
          <li><a href="<?= getPageUrl('partner');?>">ともに創る</a></li>
          <li><a href="<?= getPageUrl('search');?>">情報を探す（AI）</a></li>
        </ul>
      </div>
      <div>
        <h5>関連リンク</h5>
        <ul>
          <li><a href="https://www.jfa.jp/" target="_blank" rel="noopener">日本サッカー協会（JFA）</a></li>
          <li><a href="http://www.hokushinetsu-fa.football/site/" target="_blank" rel="noopener">北信越サッカー協会</a></li>
          <li><a href="#">郡市サッカー協会</a></li>
          <li><a href="<?= getPageUrl('contact');?>#form">お問い合わせ</a></li>
        </ul>
      </div>
    </div>
    <div class="fBot">
      <p class="en">© Nagano Football Association</p>
      <div class="links">
        <a href="<?= getPageUrl('privacy');?>">プライバシーポリシー</a>
        <a href="<?= getPageUrl('sitemap');?>">サイトマップ</a>
      </div>
    </div>
  </div>
</footer>
<div class="spMenu" id="spMenu">
  <a href="<?= getPageUrl('about');?>"><small>ABOUT</small>NFAについて</a>
  <a href="<?= getPageUrl('start');?>"><small>GET STARTED</small>はじめる・つづける</a>
  <a href="<?= getPageUrl('grow');?>"><small>DEVELOPMENT</small>強くなる・関わる</a>
  <a href="<?= getPageUrl('partner');?>"><small>PARTNERSHIP</small>ともに創る</a>
  <a href="<?= getPageUrl('search');?>"><small>AI SEARCH</small>情報を探す</a>
  <a href="<?= getPageUrl('partner');?>" class="cta">協賛について</a>
</div>

<script>
(function(){
  var b=document.getElementById('menuBtn');
  if(b){b.addEventListener('click',function(){document.body.classList.toggle('menuOpen');});}
  /* 絞り込みサイドバーを SP では開閉式にする */
  var hd=document.querySelector('.sideHd h2');
  if(hd){hd.addEventListener('click',function(){
    if(window.matchMedia('(max-width:640px)').matches){
      document.querySelector('.side').classList.toggle('open');
    }
  });}
})();
</script>
<?php wp_footer();?>
</body>
</html>