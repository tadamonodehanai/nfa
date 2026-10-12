<?php
/**
 * <meta content="charset=UTF-8">
 * @package nfa
 */
$url=get_bloginfo('url');
$assets_url=get_assets_url();
?>

<?php get_header();?>
        <section class="section section_cont">
			<div class="section_title_wrap box_wrap">
				<div class="title_wrap">
					<div class="sub_title">NOT FOUND.</div>
					<p class="title h0">404</p>
				</div>
                
            </div>
            <div class="section_body_wrap box_wrap_minimum">
                <div class="block">
                    <div class="body">
                        <div class="text tc tl_for_sp">
							<p>お探しのページが見つかりません。</p>
							<p>ご指定のURLは、削除されたか、アドレスが変更された可能性があります。</p>
							<p>お手数ですが、以下のいずれかの方法で目的のページをお探しください。</p>
							<p class="mt40">URLをご確認ください： アドレスバーに入力されたURLに間違いがないか、ご確認ください。</p>
							<p>トップページへお戻りください： 以下のボタンからトップページへ移動し、メニューからお探しください。</p>
                		</div>
                    </div>
                    <footer class="foot mt80">
                        <div class="to_link ">
                            <a href="<?= $url;?>" class="a">
								<span class="arrow back"></span>
								<span class="txt">トップページへ戻る</span>
							</a>
                        </div>
                    </footer>
                </div>
            </div>
        </section>
<?php get_footer();?>
