<?php
/**
 * <meta content="charset=UTF-8">
 * @package nfa
 */

//固定ページに抜粋を表示
add_post_type_support( 'page', 'excerpt' );

// アイキャッチ画像を有効にする。
add_theme_support('post-thumbnails'); 

function create_list($label,$cont){
	$li=<<<EOT
		<li class="clearfix list">
			<div class="label">$label</div>
			<div class="cont">$cont</div>
		</li>
EOT;
	return $li;
}


//stringsからobjectを生成 (1次のみ対応)
function get_object_from_strings($arrays,$depart,$cut){
	$obj;
	if(empty($arrays) || empty($depart) || empty($cut)) return $obj;
	foreach($arrays as $array){
		$myobj=explode($cut,$array);
		$key=trim($myobj[0]);
		$val=trim($myobj[1]);
		$obj[$key]=$val;
	}
	return $obj;
}

function change_posts_per_page($query) {
    if ( is_admin() || ! $query->is_main_query() ) return;
	if($query->is_home()||$query->is_front_page()){
        $query->set('post_type','post');
	}elseif($query->is_search()){
		//frontページ、問い合わせ完了ページ等を除外
		$query->set('post__not_in',array(getPageNumber('home'),getPageNumber('contact'),getPageNumber('contact_confirm'),getPageNumber('contact_complete')));
	}elseif($query->is_date()){
		//$query->set('cat','-7,-8');
		//$query->set( 'posts_per_page', '10');
	}else if($query->is_post_type_archive()){
		// カスタム投稿一覧ページか
		
	}elseif($query->is_archive()){
      
    }elseif($query->is_category()){
        //年度指定
        $year=$_GET['yyy'];
        if($year){
            $date_query = array(
                 array(
                   'year'  => $year,
                 ),
              );
            $query->set('date_query',$date_query);
        }
	}
	//query_posts('cat=-7,-8&posts_per_page=2&paged='.$paged );
}
add_action('pre_get_posts', 'change_posts_per_page' );


function check_my_post($id,$array){
	$flag=false;
	for($i=0;$i<count($array);$i++){
		$num=$array[$i];
		if((int)$num===(int)$id) $flag=true;
	}
	return $flag;
}

function get_assets_url(){
	$url=get_bloginfo('url')."/assets/";

	$assets_url=array(
		"css"	=>$url."css/",
		"img"=>$url."images/",
		"js"=>$url."js/",
		"pdf"=>$url."pdf/",
		"module"=>$url."module/",
        "plugin"=>$url."plugins/",
        "video"=>$url."video/",
        "library"=>$url."library/",
	);
	return $assets_url;
}

function get_mypageinfo(){
	global $post;
	$assets_url=get_assets_url();
	$mypageinfo = (object) [];
	$mypageinfo->title=get_bloginfo("description") ." | ". get_bloginfo("name");
	$mypageinfo->dc=get_the_excerpt();
	$mypageinfo->lang="jp";
	
	$mypageinfo->url=get_permalink();
	
	/*カテゴリー
	
	*/
	if(getIsHome()){
		$mypageinfo->category="home";
		$mypageinfo->page="top";
		$mypageinfo->css='home';
        $mypageinfo->dc='';
		$mypageinfo->url=get_bloginfo('url')."/";
        $mypageinfo->title=$post->post_title;
		if(getPageNumber('home_en')){
			$mypageinfo->url=get_bloginfo('url')."/en/";
		}
	}else{
		if(is_tag()){
			$ts=get_queried_object();
			$mypageinfo->url=get_category_link(($ts->tag_ID));
			
			$mypageinfo->title=single_cat_title('',false);
			
			$mypageinfo->category="tag";
            $mypageinfo->eng='TAG';
            
            $mypageinfo->css="archives";
		}elseif(is_search()){
            $mypageinfo->title="「".get_search_query()."」の検索結果";
			
			$mypageinfo->category="search";
            $mypageinfo->eng='SEARCH RESULTS';

            $mypageinfo->css= "archives";
            
            //$mypageinfo->css="archives";
		}elseif(is_archive()){
			if(is_post_type_archive()){
				$mypageinfo->category =$mypageinfo->css= get_query_var('post_type');
			}else{
				$c=get_query_var('cat');
				$cs=get_category($c);

				$mypageinfo->title=$cs->name;
				$mypageinfo->category = $cs->slug;
				$mypageinfo->css= "archives";

				//子カテゴリーの場合
				if($cs){
					if($cs->parent){
						$parent=get_category($cs->parent);
						$mypageinfo->stitle=$cs->name;
						$mypageinfo->category =$mypageinfo->css= $parent->slug;
					}
				}

				$mypageinfo->page="list";
				$mypageinfo->url=get_category_link(($cs->cat_ID));
				//$mypageinfo->css="archives";
			}
		}elseif(is_single()){
			if(is_singular()){
				
			}else{
				$categories=get_the_category();
				$mycat=$categories[0];
				$mycat_id=$mycat->term_id;

				if($mycat->category_parent) $parent_cat_id=$mycat->category_parent;
				else $parent_cat_id=$mycat_id;

				$parent_cat=get_category($parent_cat_id);

				$mypageinfo->category = $parent_cat->slug;
				$mypageinfo->title=single_cat_title('',false);

				$mypageinfo->stitle=get_the_title();
				$mypageinfo->page="detail";
				$mypageinfo->css="article";
			}
            
		}elseif(is_page()){
			$mypageinfo->title=get_the_title();
			$mypageinfo->page="top";
			$mypageinfo->url=get_permalink();
			
			$mypageinfo->category=$mypageinfo->css=$post->post_name;
			$mypageinfo->title=$post->post_title;
            if($post->post_parent){
                $parent=get_post($post->post_parent);
                $mypageinfo->category=$mypageinfo->css = $parent->post_name;
				$mypageinfo->page=$post->post_name;
                $mypageinfo->title=$parent->post_title;
                $mypageinfo->stitle=$post->post_title;
			}
            if(is_page(getPageNumber('contact'))||is_page(getPageNumber('contact_confirm'))||is_page(getPageNumber('contact_complete'))){
                $mypageinfo->css="contact";
            }
		}elseif(is_404()){
            $mypageinfo->category=$mypageinfo->css="404";
            $mypageinfo->eng="404";
        }
	 }
	
	return $mypageinfo;
}

//URLから画像を取得
function get_image_from_url($url,$size="full"){
	$img=wp_get_attachment_image_src($url, $size );
	return $img[0];
}

function get_image_from_id($id,$size="large"){
	$img=wp_get_attachment_image_src($id, $size );
	return $img[0];
}

/*remove_filter('do_feed_rss2', 'do_feed_rss2', 10);
function custom_feed_rss2(){
	$template_file = '/feed-rss2.php';
	load_template(get_template_directory() . $template_file);
}
add_action('do_feed_rss2', 'custom_feed_rss2', 10);*/
remove_action('do_feed_rdf', 'do_feed_rdf');
remove_action('do_feed_rss', 'do_feed_rss');
remove_action('do_feed_rss2', 'do_feed_rss2');
remove_action('do_feed_atom', 'do_feed_atom');



//タグやカテゴリも検索に含む
function custom_search($search, $wp_query) {
    global $wpdb;
  
    //サーチページ以外だったら終了
    if (!$wp_query->is_search)
        return $search;
    if (!isset($wp_query->query_vars))
        return $search;
  
    // ユーザー名とか、タグ名・カテゴリ名も検索対象に
    $search_words = explode(' ', isset($wp_query->query_vars['s']) ? $wp_query->query_vars['s'] : '');
    if ( count($search_words) > 0 ) {
        $search = '';
        foreach ( $search_words as $word ) {
            if ( !empty($word) ) {
                $search_word = $wpdb->escape("%{$word}%");
                $search .= " AND (
 {$wpdb->posts}.post_title LIKE '{$search_word}'
 OR {$wpdb->posts}.post_content LIKE '{$search_word}'
 OR {$wpdb->posts}.post_author IN (
   SELECT distinct ID
   FROM {$wpdb->users}
   WHERE display_name LIKE '{$search_word}'
   )
 OR {$wpdb->posts}.ID IN (
   SELECT distinct r.object_id
   FROM {$wpdb->term_relationships} AS r
   INNER JOIN {$wpdb->term_taxonomy} AS tt ON r.term_taxonomy_id = tt.term_taxonomy_id
   INNER JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id
   WHERE t.name LIKE '{$search_word}'
     OR t.slug LIKE '{$search_word}'
     OR tt.description LIKE '{$search_word}'
   )
) ";
            }
        }
    }
  
    return $search;
}
add_filter('posts_search','custom_search', 10, 2);



register_sidebar(array(
     'name' => '検索されているキーワード' ,
     'id' => 'search_keywords' ,
     'description' => 'Widgets in this area will be shown on the article top.',
     'before_widget' => '',
     'after_widget' => '</div>',
     'before_title' => '<h3>',
     'after_title' => '</h3>'
));

function is_parent_slug() {
  global $post;
  if ($post->post_parent) {
    $post_data = get_post($post->post_parent);
    return $post_data->post_name;
  }
}

if ( ! function_exists( 'post_is_in_descendant_category' ) ) {
	function post_is_in_descendant_category( $cats, $_post = null ) {
		foreach ( (array) $cats as $cat ) {
			// get_term_children() は整数の ID しか受け付けない
			$descendants = get_term_children( (int) $cat, 'category' );
			if ( $descendants && in_category( $descendants, $_post ) )
				return true;
		}
		return false;
	}
}

function category_is_in_descendant_category($cid,$parent){
	$flag=false;
	$children=get_term_children($parent,"category");
	if($children){
		foreach($children as $child){
			if($cid == $child) $flag = true;
		}
	}
	
	return $flag;
}

function getMyIndexNumber($index,$cat=2){
    $args=array(
        'sort_order' => 'ASC',
        'posts_per_page' => -1,
        'cat'	=>$cat
    );
    $postlist=get_posts($args);
    $posts = array();
    foreach ($postlist as $p) {
       $posts[] += $p->ID;
    }
    $current = array_search($index, $posts);
	$current++;
	$total=count($posts);
	if(strlen($total)>1){
		$length="%0".strlen($total)."d";
		$current=sprintf($length, $current);
	}
    return array($current,$total);
}

function shortcode_link($attr){
	extract(shortcode_atts(array (
        'id' => 0,
		'category'	=>0,
		'name'	=> "リンク名"
    ), $attr));
	if($id!=0){
		$aaa=get_permalink($id);
	}elseif($category!=0){
		$aaa=get_category_link($category);
	}
	$alink='<div class="to_link tc"><a href="'.$aaa.'" class="a"><span class="txt">'.$name.'</span></a></div>';
	return $alink;
}
add_shortcode('link', 'shortcode_link');

function shortcode_link_limited_post($attr){
	extract(shortcode_atts(array (
        'id' => 0
    ), $attr));
	if($id!=0){
		$aaa=get_permalink($id);
	}
	return $alink;
}
add_shortcode('link_limited_post', 'shortcode_link_limited_post');


remove_filter( 'the_excerpt', 'wpautop' );

add_filter('aioseop_prev_link', '__return_empty_string' );
add_filter('aioseop_next_link', '__return_empty_string' );


function adjustTitle($titlesrc,$flag=false){
	$title_arr=explode('--',$titlesrc);
	$title="";
	foreach($title_arr as $src){
		$t_arr=explode('  ',$src);
		$title_part="";
		foreach($t_arr as $t){
			if($flag){
				$title_part.=$t;
			}
			else{
				$title_part.="<span>".$t."</span>";
			}	
		}
		if($flag){
			if($title==="") $title=$title_part;
			else $title="　".$title_part;
		}else{
			if($title==="") $title=$title_part;
			else $title="<br>".$title_part;
		}
	}
	return $title;
}

function adjustText($src){
    $src_arr=explode('  ',$src);
    return implode('<br class="not_for_sp">',$src_arr);
}

function adjustTitlePlain($titlesrc){
    $src_arr=explode('  ',$titlesrc);
    return implode('',$src_arr);
}

function getAdjustTextLength($txt,$leng=44){
    if(mb_strlen($txt, 'UTF-8')>$leng){
        $txt=mb_substr($txt, 0, $leng, 'UTF-8');
        return $txt."...";
    }else return $txt;
}

function getMaxPostNum($c,$opt=null){
	$args=array(
		'category'=>$c,
		'posts_per_page'=>-1,
	);
    if($opt){
        $args['meta_query']=$opt;
    }
	$posts=get_posts($args);
	return count($posts);
}

function getCategoryMonthlyList($c){
    $args=array(
		'cat'=>$c,
		'posts_per_page'=>-1,
	);
    $posts=get_posts($args);
    //var_dump($posts);
    foreach($posts as $p){
        $archive_list[ get_the_time( 'Y', $p->ID ) ][get_the_time( 'n', $p->ID )][]=$p->ID;
    }
    return $archive_list;
}
function getCategoryYearlyList($c){
    $args=array(
		'cat'=>$c,
		'posts_per_page'=>-1,
	);
    $posts=get_posts($args);
    //var_dump($posts);
    foreach($posts as $p){
        $archive_list[ get_the_time( 'Y', $p->ID ) ][]=$p->ID;
    }
    return $archive_list;
}

function checkPostNew( $days = 7 ){
  $today = date_i18n('U');
  $entry = get_the_time('U');
  $diff = date( 'U', ( $today - $entry ) ) / 86400;
  return ($days > $diff);
}

function checkCategoryNew( $category, $days = 7 ){
    $flag=false;
	$args = array(
	    'numberposts' => -1,
	    'offset'        => 0,
	    'orderby'   =>'date',
	    'order'     =>'DESC',
	    'cat'       => $category,
	    'date_query' => array(
	        array(
	            'after'     => date("Y-m-d", strtotime("-$days day")),
	            'before'    => date("Y-m-d"),
	            'inclusive' => true,
	        ),
	    ),
	);
	$myposts = get_posts( $args );
	if($myposts){
		$flag=true;
	}
	return $flag;
}


function checkMainServer(){
    return ($_SERVER['HTTP_HOST'] === 'nagano-fa.com');
}

function mvwpform_autop_filter() {
  if (class_exists('MW_WP_Form_Admin')) {
    $mw_wp_form_admin = new MW_WP_Form_Admin();
    $forms = $mw_wp_form_admin->get_forms();
    foreach ($forms as $form) {
      add_filter('mwform_content_wpautop_mw-wp-form-' . $form->ID, '__return_false');
    }
  }
}
mvwpform_autop_filter();

function getPageNumber($key){
	$num=0;
	switch($key){
		case "home":
			$num=2;
			break;
		case "privacy":
			$num=3;
			break;
		case "about":
			$num=34;
			break;
		case "start":
			$num=37;
			break;
		case "grow":
			$num=39;
			break;
		case "partner":
			$num=42;
			break;
		case "contact":
			$num=68;
			break;
		case "contact_confirm":
			$num=45;
			break;
		case "contact_confirm":
			$num=47;
			break;
		case "search":
			$num=71;
			break;
	}
	return $num;
}

function getCategoryNum($key){
	$num=4;
	switch($key){
		case "news":
			$num=3;
			break;
		case "column":
			$num=4;
			break;
	}
	return $num;
}

function getPageUrl($key,$type="page"){
    $url=get_bloginfo('url');
	if($type=="category"){
		$url=get_category_link(getCategoryNum($key));
	}else{
		$url=get_page_link(getPageNumber($key));
	}
    return $url;
}


function getFileSize($fileID){
    $fileMeta=wp_get_attachment_metadata($fileID);
    $fileSize=$fileMeta['filesize'];
    $formatted = size_format( $fileSize );
    return $formatted;
}

function getCategoryChildren($parent_id){
    $args=array(
        'parent'=>$parent_id,
        'hide_empty'=>false,
        'orderby'=>"id",
        'order'=>'ASC'
    );
    $categories=get_categories($args);
    return $categories;
}

function checkPostStatus($key){
	$status=get_post_status(getPageNumber($key));
	return $status === "publish";
}


function get_format_date($date_str){
	if($date_str){
		$date = new DateTimeImmutable($date_str);
		return $date->format('Y年n月j日');
	}else return "";	
}

function getIsHome(){
	$isHome=(is_home()||is_front_page());
	return $isHome;
}


function my_theme_support() {
    add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'my_theme_support' );


//ajax用のURL出力
/*function my_ajaxurl() {
    $script='<script>';
    $script.='var ajaxurl = "'.admin_url( 'admin-ajax.php') .'";';
    $script.='var myurl = "'.get_bloginfo('url').'/";';
    $script.='</script>';
    
    echo $script;
}
add_action( 'wp_head', 'my_ajaxurl', 1 );*/

// Contact Form 7で自動挿入されるPタグ、brタグを削除
add_filter('wpcf7_autop_or_not', 'wpcf7_autop_return_false');
function wpcf7_autop_return_false() {
  return false;
}

?>