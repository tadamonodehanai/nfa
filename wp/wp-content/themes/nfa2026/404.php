<?php
/**
 * 404（デザイン 404.html）。
 *
 * @package NFA2026
 */

get_header();
?>

<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><span aria-current="page">ページが見つかりません</span></nav>
<section class="nf-hero"><div class="wrap">
<div aria-hidden="true" class="nf-code">404</div>
<div class="e">PAGE NOT FOUND</div>
<h1>ページが見つかりません</h1>
<p>お探しのページは、移動または削除された可能性があります。<br/>サイトのリニューアルにより、以前のアドレスから新しいアドレスに変わったページがあります。</p>
<form action="<?php echo esc_url( nfa_u( 'search.html' ) ); ?>" class="nf-sbox" role="search">
<label class="vh" for="nfq">サイト内を探す</label>
<input class="inp" id="nfq" placeholder="探している情報を入力" type="search"/>
<button class="btn btn-g" type="submit">探す</button>
</form>
</div></section>
<section class="blk"><div class="wrap">
<div class="bh"><div><div class="e">LINKS</div><h2>主なページ</h2></div></div>
<div class="cards nf-links">
<a class="cd" href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>"><div class="bd"><div class="lab">TOP</div><h3>トップページ</h3><p>サイトのトップページから探す</p><span class="go">トップページへ</span></div></a>
<a class="cd" href="<?php echo esc_url( nfa_u( 'list.html' ) ); ?>"><div class="bd"><div class="lab">NEWS</div><h3>お知らせ・大会情報</h3><p>最新のお知らせ・大会情報・試合結果の一覧</p><span class="go">一覧を見る</span></div></a>
<a class="cd" href="<?php echo esc_url( nfa_u( 'contact.html' ) ); ?>"><div class="bd"><div class="lab">CONTACT</div><h3>お問い合わせ</h3><p>ページが見つからない場合はご連絡ください</p><span class="go">お問い合わせ</span></div></a>
</div>
</div></section>
<section class="catsec"><div class="wrap">
<div class="bh"><div><div class="e">CATEGORY</div><h2>カテゴリから探す</h2></div></div>
<div class="catgrid"><h3 class="cg-h">年代別</h3><a class="ct" href="<?php echo esc_url( nfa_u( 'cat-4.html' ) ); ?>"><span class="lb"><span class="en">U-12</span><span class="tg">4種</span></span><b>小学生</b><span class="ft2">日程・結果・お知らせ<i>→</i></span></a><a class="ct" href="<?php echo esc_url( nfa_u( 'cat-3.html' ) ); ?>"><span class="lb"><span class="en">U-15</span><span class="tg">3種</span></span><b>中学生</b><span class="ft2">日程・結果・お知らせ<i>→</i></span></a><a class="ct" href="<?php echo esc_url( nfa_u( 'cat-2.html' ) ); ?>"><span class="lb"><span class="en">U-18</span><span class="tg">2種</span></span><b>高校生</b><span class="ft2">日程・結果・お知らせ<i>→</i></span></a><a class="ct" href="<?php echo esc_url( nfa_u( 'cat-1.html' ) ); ?>"><span class="lb"><span class="en">ADULT</span><span class="tg">1種</span></span><b>社会人・大学</b><span class="ft2">日程・結果・お知らせ<i>→</i></span></a><h3 class="cg-h">種目・対象別</h3><a class="ct" href="<?php echo esc_url( nfa_u( 'cat-w.html' ) ); ?>"><span class="lb"><span class="en">WOMEN</span></span><b>女子</b><span class="ft2">日程・結果・お知らせ<i>→</i></span></a><a class="ct" href="<?php echo esc_url( nfa_u( 'cat-f.html' ) ); ?>"><span class="lb"><span class="en">FUTSAL</span></span><b>フットサル</b><span class="ft2">日程・結果・お知らせ<i>→</i></span></a><a class="ct" href="<?php echo esc_url( nfa_u( 'cat-s.html' ) ); ?>"><span class="lb"><span class="en">SENIOR</span></span><b>シニア</b><span class="ft2">日程・結果・お知らせ<i>→</i></span></a><a class="ct" href="<?php echo esc_url( nfa_u( 'grassroots.html' ) ); ?>"><span class="lb"><span class="en">KIDS</span></span><b>キッズ</b><span class="ft2">お知らせ・資料<i>→</i></span></a><h3 class="cg-h">その他</h3><a class="ct" href="<?php echo esc_url( nfa_u( 'referee.html' ) ); ?>"><span class="lb"><span class="en">REFEREE</span></span><b>審判</b><span class="ft2">講習会・資格・お知らせ<i>→</i></span></a><a class="ct" href="<?php echo esc_url( nfa_u( 'technical.html' ) ); ?>"><span class="lb"><span class="en">COACH</span></span><b>指導者</b><span class="ft2">講習会・資格・お知らせ<i>→</i></span></a><a class="ct" href="<?php echo esc_url( nfa_u( 'medical.html' ) ); ?>"><span class="lb"><span class="en">MEDICAL</span></span><b>医学</b><span class="ft2">お知らせ・資料<i>→</i></span></a></div>
</div></section>
<?php
get_footer();
