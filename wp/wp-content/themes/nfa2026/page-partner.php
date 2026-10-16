<?php
/**
 * 関わる（協賛）（デザイン partner.html）。
 *
 * @package NFA2026
 */

get_header();
?>

<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><span aria-current="page">関わる</span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span>PARTNERSHIP</span></span></p><h1 class="pg-ja">協賛のご案内</h1></div><div class="pg-sub"><p>長野県サッカー協会では、スポンサーを募集しています。<br class="pc"/>申込概要、協賛企業一覧、お問い合わせ先をご案内します。</p></div></div></section>
<nav aria-label="ページ内メニュー" class="lnav"><div class="lnav-in"><span class="lnav-lb">CONTENTS</span><ul><li><a href="#about"><span class="no">01</span><span class="t">スポンサー募集</span></a></li><li><a href="#outline"><span class="no">02</span><span class="t">申込概要</span></a></li><li><a href="#partners"><span class="no">03</span><span class="t">協賛企業</span></a></li><li><a href="#apply"><span class="no">04</span><span class="t">お申し込み・お問い合わせ</span></a></li></ul></div></nav>
<!-- 映像をイメージとして：スクロールで左右が縮む -->
<section aria-hidden="true" class="ph-hero"><div class="ph-img"><video autoplay="" loop="" muted="" playsinline="" poster="<?php echo esc_url( nfa_asset( 'img/mv4_poster.jpg' ) ); ?>" preload="metadata" src="<?php echo esc_url( nfa_asset( 'video/mv4.mp4' ) ); ?>"></video></div></section>
<!-- スポンサー募集のお願い（出典：/office_top/office_member/office_member_contents） -->
<section class="blk" id="about"><div class="wrap">
<div class="bh"><div><div class="e">SPONSORSHIP</div><h2>スポンサー募集のお願い</h2></div></div>
<div class="prose pt-body">
<p>一般社団法人長野県サッカー協会では、キッズ（幼児）から女子およびシニアまで幅広い分野でサッカー競技の普及強化に取り組んでおります。</p>
<p>サッカーを通して、県民の心身の健全な発達とスポーツ文化の発展に寄与する事を目標に、夢と希望と感動を与える長野県を作るために少しでも貢献したいと願い活動しております。</p>
<p>つきましては、<a href="<?php echo esc_url( nfa_u( 'about.html#profile' ) ); ?>">本協会の趣旨（協会組織の概要）</a>をご理解いただき、スポンサーに申込いただきますよう、お願い申し上げます。</p>
</div>
</div></section>
<!-- 申込概要（出典：同上） -->
<section class="blk pale" id="outline"><div class="wrap">
<div class="bh"><div><div class="e">OUTLINE</div><h2>スポンサー申込概要</h2></div></div>
<dl class="pt-out">
<div><dt>広告掲載期間</dt><dd>1年単位とする。</dd></div>
<div><dt>スポンサーへの特典</dt><dd>ご協賛金額に合わせた各種メリットをご用意しております。詳しくお知りになりたい方は、<a href="#apply">お問い合わせ先</a>までご連絡ください。</dd></div>
<div><dt>掲載位置</dt><dd>具体的な掲載位置は、当協会に一任ください。</dd></div>
</dl>
</div></section>
<!-- 協賛企業（出典：/office_top/office_member） -->
<section class="blk" id="partners"><div class="wrap">
<div class="bh"><div><div class="e">PARTNERS</div><h2>協賛企業一覧</h2></div></div>
<?php
$sp   = nfa_sponsor_list();
$item = fn( $r, $logo = true ) => ( ! empty( $r['url'] ) ? '<a href="' . esc_url( $r['url'] ) . '" rel="noopener" target="_blank">' : '' ) . ( $logo ? '<span class="lg">LOGO</span>' : '' ) . esc_html( $r['name'] ) . ( ! empty( $r['url'] ) ? '</a>' : '' );
?>
<h3 class="pt-tier">GOLD PARTNER<span>ゴールドパートナー</span></h3>
<ul class="pt-logos gold"><?php foreach ( $sp['gold'] as $r ) { echo '<li>' . $item( $r ) . '</li>'; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
<h3 class="pt-tier">OFFICIAL PARTNER<span>オフィシャルパートナー</span></h3>
<ul class="pt-logos"><?php foreach ( $sp['official'] as $r ) { echo '<li>' . $item( $r ) . '</li>'; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
<h3 class="pt-tier">SUPPORTERS</h3>
<ul class="pt-sup"><?php foreach ( $sp['supporters'] as $r ) { echo '<li>' . $item( $r, false ) . '</li>'; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
</div></section>
<!-- お問い合わせ先（出典：/office_top/office_member/office_member_contents） -->
<section class="cband" id="apply"><div class="wrap"><div class="cband-tx"><p class="cband-e">CONTACT</p><h2>お申し込み・お問い合わせ</h2><p class="cband-info">（一社）長野県サッカー協会　事務局（担当：下条）<br/>TEL <a href="tel:0263878565">0263-87-8565</a>　FAX 0263-87-8587</p><p class="cband-note">お電話でのお問い合わせは、平日の10:00～16:00までの間にてお願いいたします。</p></div><div class="cband-act"><a class="btn btn-g" href="https://www.nagano-fa.or.jp/office_top/office_member/office_member_form" rel="noopener" target="_blank"><span>スポンサー申込</span><span aria-hidden="true" class="arr"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a><a class="btn btn-l" href="<?php echo esc_url( nfa_u( 'contact.html' ) ); ?>"><span>お問い合わせフォーム</span><span aria-hidden="true" class="arr"><svg aria-hidden="true" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></div></div></section>
<?php
get_footer();
