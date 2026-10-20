<?php
/**
 * お知らせ1件：お知らせ・募集型（デザイン detail.html）。
 * 開催報告型・重要なお知らせ型は、投稿の「テンプレート」で templates/post-report.php・post-important.php を選ぶ。
 *
 * @package NFA2026
 */

get_header();
the_post();
get_template_part( 'template-parts/single/article', null, array( 'variant' => 'info' ) );
get_footer();
