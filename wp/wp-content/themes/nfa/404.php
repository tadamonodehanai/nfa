<?php
/**
 * <meta content="charset=UTF-8">
 * @package nfa
 */
$url=get_bloginfo('url');
$assets_url=get_assets_url();
?>

<?php get_header();?>
<div class="stage">
<div class="view on" id="v404">
    <div class="nf">
      <p class="code en">404</p>
      <h1>お探しのページが見つかりませんでした。</h1>
      <p class="ds">ページが移動または削除されたか、URL が変更された可能性があります。<br>サイトのリニューアルに伴い、以前のアドレスは新しい場所に変わっています。</p>

      <div class="askBox">
        <p>探していたものを、言葉で聞いてみてください。</p>
        <div class="askIn">
          <input type="text" placeholder="例）小学生の大会日程、審判講習会の申込 など">
          <button onclick="location.href='<?= getPageUrl('search');?>'">探す</button>
        </div>
      </div>

      <div class="links">
        <a href="<?= getPageUrl('home');?>"><span class="t">トップページへ</span><span class="d">サイトの入口から探し直す</span></a>
        <a href="<?= getPageUrl('news','category');?>"><span class="t">お知らせ・大会情報</span><span class="d">最新の情報の一覧を見る</span></a>
        <a href="<?= getPageUrl('contact');?>" onclick="showView('vForm');return false"><span class="t">お問い合わせ</span><span class="d">見つからない場合はご連絡ください</span></a>
      </div>
    </div>

  </div>
</div>
<?php get_footer();?>
