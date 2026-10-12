<?php
/**
Template Name: CONTACT COMPLETE

 * <meta content="charset=UTF-8">
 * @package nfa
 */

$url=get_bloginfo('url');
$assets_url=get_assets_url();
$mypageinfo=get_mypageinfo();

get_header();
while ( have_posts() ) : the_post();?>
<div class="stage">
<!-- ============ お問い合わせ ============ -->
  <div class="view" id="vForm">
    <div class="ctHead">
      <p class="crumb"><a href="<?= getPageUrl('home');?>">ホーム</a> ／ お問い合わせ</p>
      <h1>お問い合わせ</h1>
      <p>大会・登録・講習会など、用件によって回答できる部署が異なります。用件の種類をお選びいただくと、担当へ直接届きます。</p>
    </div>

    <div class="ctCols">
      <main>
        <?php get_template_part('parts/step','contact',array('step'=>3));?>

        <!-- 完了 -->
        <div id="fDone" style="display:none">
          <div class="done">
            <div class="ic"></div>
            <h1>お問い合わせを受け付けました。</h1>
            <p>受付番号：<span class="num en">C-2026-08-00123</span></p>
            <p>確認のメールをお送りしています。届かない場合は、迷惑メールフォルダをご確認のうえ、事務局までお電話ください。<br>回答まで、通常 3 営業日ほどいただいています。</p>
            <a href="<?= getPageUrl('home');?>" class="backTop">トップページへ戻る</a>
          </div>
        </div>
      </main>

      <?php get_template_part('parts/aside','contact');?>
    </div>

  </div>
	
</div>
<?php endwhile;
get_footer();?>
