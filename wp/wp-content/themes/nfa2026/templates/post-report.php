<?php
/**
 * Template Name: 開催報告
 * Template Post Type: post
 *
 * お知らせ1件：開催報告型（デザイン detail-report.html）。記事に添付した画像を「写真」に並べる。
 *
 * @package NFA2026
 */

get_header();
the_post();
get_template_part( 'template-parts/single/article', null, array( 'variant' => 'report' ) );
get_footer();
