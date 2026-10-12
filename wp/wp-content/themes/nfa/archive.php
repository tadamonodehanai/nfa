<?php
/**
Template Name: archive

 * <meta content="charset=UTF-8">
 * @package nfa
 */
$cat_id = get_query_var('cat');

$url=get_bloginfo('url');
$category=get_the_category();

$mycat=get_category($cat_id);
if($mycat->category_parent) $parent_cat_id=$mycat->category_parent;
else $parent_cat_id=$cat_id;

$parent_cat=get_category($parent_cat_id);

$assets_url=get_assets_url();
get_header();

$maxposts=$wp_query->found_posts;
?>
<section class="section section_cont section_archives">
	<header class="section_title_wrap box_wrap">
		<div class="title_wrap mb40">
			<?php if($parent_cat_id==getCategoryNum('news')):
			// ニュース
			?>
			<div class="jp h6 animate">お知らせ</div>
            <div class="en h2 b animate popup_title"><p><span>N</span><span>E</span><span>W</span><span>S</span></p></div>
			<?php elseif($parent_cat_id==getCategoryNum('column')):
			// コラム?>
            <div class="jp h6 animate">コラム</div>
            <div class="en h2 b animate popup_title"><p><span>C</span><span>O</span><span>L</span><span>U</span><span>M</span><span>N</span></p></div>
			<?php endif;?>
        </div>
		<?php get_template_part('part/sub_categories','',array('parent_cat_id'=>$parent_cat_id));?>
	</header>
	<div class="section_body_wrap box_wrap">
		<?php if ( have_posts() ) :?>
		<div class="block">
			<div class="body">
				<?php if($parent_cat_id==getCategoryNum('news')):
				// ニュース
				?>
				<ul class="list_wrap list_news_wrap">
					<?php $i=1;
					while ( have_posts() ) : the_post();?>
                    <?php get_template_part('part/list','news',array('addClass'=>'delay'.$i));?>
                    <?php $i++;
					endwhile;?>
				</ul>
				<?php elseif($parent_cat_id==getCategoryNum('column')):
				// コラム?>
				<div class="columns column3 archives archives_column">
					<?php 
					$i=0;
					while ( have_posts() ) : the_post();?>
                    <?php get_template_part('part/column','column',array('addClass'=>'delay'.$i));?>
                    <?php $i++;
					endwhile;?>
				</div>
				<?php endif;?>
				<?php get_template_part('part/pagination');?>
			</div>
			<footer class="foot mt80">
				<?php get_template_part('part/pagination');?>
			</footer>
		</div>
		<?php else:?>
		<div class="block">
            <header class="head">
                <div class="h3 animate">記事が見つかりません</div>
            </header>
            <div class="body">
                <div class="text animate delay1">
                    他の各カテゴリーからお探しください。
                </div>
            </div>
        </div>	
		<?php endif;?>
	</div>
</section>
<?php get_footer();?>