<?php
/**
Template Name: search

 * <meta content="charset=UTF-8">
 * @package nfa
 */
$search_word= get_search_query();


$url=get_bloginfo('url');
$assets_url=get_assets_url();


get_header();
?>
        <section class="section section_cont section_content_title">
            <div class="section_body_wrap box_wrap">
                <p>Search</p>
                <div class="h2">「<?= $search_word ?>」が含まれる記事一覧</div>
            </div>
        </section>
        <section class="section section_cont section_archives">
            <div class="section_body_wrap box_wrap">
<?php if ( have_posts() ) :?>  
                <div class="columns archives column3">
                    <?php while ( have_posts() ) : the_post();?>
                    <?php get_template_part('column','article');?>
                    <?php endwhile;?>
                </div>
                <?php get_template_part("part",'pagenation',array(
                    'option_tail'=>'?s='.$search_word
                ));?>
<?php else:?>
                <div class="block">
                    <header class="head">
                        <div class="h3">記事が見つかりません</div>
                    </header>
                    <div class="body">
                        <div class="text">
                            他のキーワードを検索するか、各カテゴリーからお探しください。
                        </div>
                    </div>
                </div>
<?php endif;?>
            </div>
        </section>
<?php get_footer();?>