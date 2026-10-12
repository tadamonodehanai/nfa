<?php
/**
Template Name: single
 * <meta content="charset=UTF-8">
 * @package nfa
 */

$url=get_bloginfo('url');
$assets_url=get_assets_url();

get_header();?>

<?php if (have_posts()) :
while (have_posts()) : the_post();
$tags=get_the_tags();
$categories=get_the_category();
$mycat=$categories[0];
$mycat_id=$mycat->term_id;

if($mycat->category_parent) $parent_cat_id=$mycat->category_parent;
else $parent_cat_id=$mycat_id;

$parent_cat=get_category($parent_cat_id);

$myID=$post->ID;

?>
<section class="section section_cont section_article">
	<div class="section_body_wrap box_wrap_minimum">
		<?php get_template_part('part/article','default');?>
	</div>	
</section>
<?php 
endwhile;endif;
get_footer();?>