<?php
/**
 * Template Name: 重要なお知らせ
 * Template Post Type: post
 *
 * お知らせ1件：重要なお知らせ型（デザイン detail-important.html）。注意書きの帯と関連資料を出す。
 *
 * @package NFA2026
 */

get_header();
the_post();
get_template_part( 'template-parts/single/article', null, array( 'variant' => 'important' ) );
get_footer();
