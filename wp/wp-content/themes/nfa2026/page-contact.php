<?php
/**
 * お問い合わせ（デザイン contact.html）。
 *
 * @package NFA2026
 */

get_header();
?>

<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><span aria-current="page">お問い合わせ</span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span>CONTACT</span></span></p><h1 class="pg-ja">お問い合わせ</h1></div><div class="pg-sub"><p>長野県サッカー協会への各種お問い合わせは、以下のフォームからお願いいたします。<br class="pc"/>ウェブサイトへの掲載依頼は、掲載依頼フォームから受け付けています。</p></div></div></section>
<nav aria-label="ページ内メニュー" class="lnav"><div class="lnav-in"><span class="lnav-lb">CONTENTS</span><ul><li><a href="#form"><span class="no">01</span><span class="t">お問い合わせフォーム</span></a></li><li><a href="#request"><span class="no">02</span><span class="t">掲載依頼</span></a></li><li><a href="#tel"><span class="no">03</span><span class="t">所在地・連絡先</span></a></li></ul></div></nav>
<!-- お問い合わせフォーム（出典：/contact） -->
<section class="blk" id="form"><div class="wrap">
<div class="bh"><div><div class="e">CONTACT FORM</div><h2>お問い合わせフォーム</h2></div></div>
<div class="art ct-art">
<div class="ct-main">
<?php
// Contact Form 7 のフォーム「お問い合わせ」（項目・クラス名はデザインと同じ。記述は inc/contact-form.txt）
$cf7 = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'title' => 'お問い合わせ', 'posts_per_page' => 1 ) );
if ( $cf7 && shortcode_exists( 'contact-form-7' ) ) {
	echo do_shortcode( '[contact-form-7 id="' . (int) $cf7[0]->ID . '" html_class="form"]' );
} else {
	echo '<p class="lead2">お問い合わせフォームは準備中です。お急ぎの方はお電話でお問い合わせください。</p>';
}
?>
</div>
<aside aria-label="お問い合わせの前に" class="side ct-side">
<div>
<h3>ご了承ください</h3>
<p>いただきましたお問い合わせ全てに対して、返信が出来ないことがありますことを予めご了承下さい。</p>
<p>匿名、もしくはフルネームではない場合、回答出来かねます。</p>
<p>なお、回答までには多くのお時間をいただく事もございます。ご迷惑をおかけいたしますが、何卒ご理解を賜りますようお願い申し上げます。</p>
</div>
<div>
<h3>暴力等の相談</h3>
<p>指導者以外による暴力等の相談は、このお問い合わせフォームからご連絡ください。</p>
</div>
<div>
<h3>審判関係のお問い合わせ</h3>
<p>審判関係のお問い合わせは、審判委員会へご連絡ください。</p>
<a class="ct-lk" href="#tel">審判委員会の連絡先</a>
</div>
</aside>
</div>
</div></section>
<!-- 掲載依頼（出典：/office-form） -->
<section class="blk pale" id="request"><div class="wrap">
<div class="bh"><div><div class="e">REQUEST</div><h2>掲載依頼</h2></div></div>
<p class="lead2">下記のフォームから必要事項を入力の上、掲載依頼を送信ください。試合結果・星取表は必ずPDF化し、掲載依頼フォームよりアップロードしてください。</p>
<div class="ct-rq">
<div class="ct-rq-f">
<a class="ct-rq-b" href="https://forms.gle/NwtuuFpxL4AaL17t7" rel="noopener" target="_blank"><small>日程｜試合結果、NEWS</small>掲載依頼フォーム（Googleフォーム）<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a>
<a class="ct-rq-b" href="https://www.nagano-fa.or.jp/office-form-jfa" rel="noopener" target="_blank"><small>JFAフェスティバル</small>掲載依頼フォーム<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a>
</div>
<div class="ct-rq-s">
<h3 class="ct-rq-h">掲載依頼から掲載までのローテーション</h3>
<dl class="ct-rot">
<div><dt>月曜の9:00までの依頼</dt><dd>火曜AM掲載</dd></div>
<div><dt>水曜の9:00までの依頼</dt><dd>木曜AM掲載</dd></div>
</dl>
<p class="ct-hint">※運用は全て平日とし、締切日が祝祭日及び休日の場合は翌営業日が締切日になります。ただし、掲載日を変更する場合があります。</p>
</div>
</div>
<h3 class="h3j">関連資料</h3>
<ul class="dl">
<li><a href="https://www.nagano-fa.or.jp/wp-content/uploads/2017/03/df65d8a55fb60012ebcca948a5f9bdd7.xlsx"><span class="fbtn-i x">XLSX</span><span>試合結果用のフォーマット【リーグ戦用】</span></a></li>
<li><a href="https://www.nagano-fa.or.jp/wp-content/uploads/2017/03/game-results-format.xls"><span class="fbtn-i x">XLS</span><span>試合結果用のフォーマット【リーグ戦以外用】</span></a></li>
<li><a href="https://www.nagano-fa.or.jp/wp-content/uploads/2025/04/bb1236bdcf13ec57628dbb882b699f8a.xlsx"><span class="fbtn-i x">XLSX</span><span>星取表フォーマット</span></a></li>
<li><a href="https://www.nagano-fa.or.jp/wp-content/uploads/2017/03/request-hp.docx"><span class="fbtn-i w">DOCX</span><span>試合結果以外のフォーマット</span></a></li>
</ul>
</div></section>
<!-- 所在地・連絡先（出典：/office_top、サイト共通フッター） -->
<section class="blk" id="tel"><div class="wrap">
<div class="bh"><div><div class="e">ADDRESS</div><h2>所在地・連絡先</h2></div></div>
<div class="ct-addr">
<div class="contact">
<div><div class="e">OFFICE</div><h3>事務局</h3><p>一般社団法人 長野県サッカー協会<br/>〒390-8565 <span class="ib">長野県松本市深志2丁目6-9</span> <span class="ib">LON BLDG. 3F</span><br/>FAX：0263-87-8587<br/>E-mail：<a href="mailto:info@nagano-fa.or.jp">info@nagano-fa.or.jp</a></p></div>
<div class="ctas"><a class="ct-tel" href="tel:0263878565"><small>TEL</small>0263-87-8565</a></div>
</div>
<div class="contact">
<div><div class="e">REFEREE</div><h3>審判委員会<small>（審判関係の問い合わせ）</small></h3><p>〒390-8565 <span class="ib">長野県松本市深志2丁目6-9</span> <span class="ib">LON BLDG. 3F</span><br/>FAX：0263-50-8383<br/>E-mail：<a href="mailto:referee@nagano-fa.or.jp">referee@nagano-fa.or.jp</a></p></div>
<div class="ctas"><a class="ct-tel" href="tel:0263508330"><small>TEL</small>0263-50-8330</a></div>
</div>
</div>
</div></section>
<?php
get_footer();
