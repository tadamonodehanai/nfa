<?php
/**
 * <meta content="charset=UTF-8">
 * @package nfa
 */
$url=get_bloginfo('url');
$assets_url=get_assets_url();
$mypageinfo=get_mypageinfo();

?>

<?php get_header();
while ( have_posts() ) : the_post();?>
<div class="stage">
<?php the_content();?>
</div>
<?php endwhile;
get_footer();?>
