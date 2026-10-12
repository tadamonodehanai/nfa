<?php
/**
 * <meta content="charset=UTF-8">
 * @package nfa
 */

$url=get_bloginfo('url');
$assets_url=get_assets_url();
$mypageinfo=get_mypageinfo();
$cat_id = get_query_var('cat');

$isHome=getIsHome();

?>
<!DOCTYPE html>
<html lang="ja">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">
<?php if(checkMainServer()):?>
<?php endif;?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="<?= get_template_directory_uri().'/assets/css/common.css';?>" rel="stylesheet">
<link href="<?= get_template_directory_uri().'/assets/css/'.$mypageinfo->category.'.css';?>" rel="stylesheet">
<?php wp_head(); ?>
</head>
<body>
<?php if(checkMainServer()):?>
<?php endif;?>
<!-- ================= ヘッダー ================= -->
<header class="gnav">
  <div class="inner">
    <a href="<?= getPageUrl('home');?>" class="logo">
      <span class="mark">NFA</span>
      <span class="txt">長野県サッカー協会<small class="en">NAGANO FOOTBALL ASSOCIATION</small></span>
    </a>
    <nav>
      <a href="<?= getPageUrl('about');?>">NFAについて</a>
      <a href="<?= getPageUrl('start');?>">はじめる・つづける</a>
      <a href="<?= getPageUrl('grow');?>">強くなる・関わる</a>
      <a href="<?= getPageUrl('partner');?>">ともに創る</a>
      <a href="<?= getPageUrl('search');?>">情報を探す</a>
    </nav>
    <a href="<?= getPageUrl('partner');?>" class="act">協賛について</a>
    <button class="menuBtn" id="menuBtn" aria-label="メニュー"><span></span><span></span><span></span></button>
  </div>
</header>