<?php
/**
 * <meta content="charset=UTF-8">
 * @package nfa
 */
$url=get_bloginfo('url');
$assets_url=get_assets_url();
?>

<?php get_header();
while ( have_posts() ) : the_post();?>
<section class="section section_cont section_with_ctitle">
    <div class="section_title_wrap">
        <div class="container">
            <div class="title_wrap">
                <p class="jp"><?php the_title();?></p>
                <h1><?php the_field('eng');?></h1>
            </div>
            <?php get_template_part("breadcrumb");?>
        </div>
    </div>
    <div class="section_body_wrap box_wrap">
        <?php the_content();?>
    </div>
</section>
<?php endwhile;
get_footer();?>
