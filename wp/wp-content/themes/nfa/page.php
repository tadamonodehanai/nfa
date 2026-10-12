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
<?php the_content();?>
<?php endwhile;
get_footer();?>
