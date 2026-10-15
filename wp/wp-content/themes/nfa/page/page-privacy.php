<?php
/**
Template Name: PRIVACY POLICY

 * <meta content="charset=UTF-8">
 * @package nfa
 */

$url=get_bloginfo('url');
$assets_url=get_assets_url();
$mypageinfo=get_mypageinfo();

get_header();
while ( have_posts() ) : the_post();?>
<div class="stage">
<div class="view" id="vDoc">
    <p class="crumb"><a href="<?= getPageUrl('home');?>">ホーム</a> ／ プライバシーポリシー</p>
    <div class="docCols">
      <aside class="tocBox">
        <div class="hd">目次</div>
        <ol>
          
        </ol>
      </aside>
      <main class="doc">
        <h1>プライバシーポリシー</h1>
        <?php the_content();?>
      </main>
    </div>
</div>
</div>
<script>
const ol=document.querySelector('aside.tocBox ol');
const anchorTargets=document.querySelectorAll('main.doc .anchor_tgt');
anchorTargets.forEach(item=>{
	const li=document.createElement("li");
	ol.appendChild(li);
	let t=item.getAttribute("id");
	let anchor=document.createElement("a");
	li.appendChild(anchor);
	anchor.append(item.textContent);
	anchor.setAttribute("href","#"+t);	
});
</script>

<?php endwhile;
get_footer();?>
