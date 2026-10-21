<?php
/**
 * JFAフェスティバル掲載依頼フォーム（デザイン request-jfa.html）。scratchpad の conv2.py で生成。
 *
 * @package NFA2026
 */

get_header();
?>

<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><a href="<?php echo esc_url( nfa_u( 'request.html' ) ); ?>">掲載依頼</a><span aria-hidden="true">›</span><span aria-current="page">JFAフェスティバル掲載依頼フォーム</span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span>JFA FESTIVAL</span></span></p><h1 class="pg-ja">JFAフェスティバル掲載依頼フォーム</h1></div><div class="pg-sub"><p>JFAキッズ／レディース・ガールズ／ファミリーフットサルフェスティバル専用の掲載依頼フォームです。<br class="pc"/>必要事項を入力のうえ、送信してください。</p></div></div></section>
<section class="blk" id="form"><div class="wrap">
<div class="art ct-art">
<div class="ct-main">
<div class="rj-note">
<p>一度送信した掲載依頼を修正する場合も、このフォームから再度送信してください。その際は、以前の掲載依頼の修正であることがわかるよう、<b>掲載内容の冒頭にその旨を記載</b>してください。</p>
</div>
<?php echo nfa_page_form( 'JFAフェスティバル掲載依頼', 'form rj-form' ); // フォーム＝固定ページの本文の Contact Form 7 ショートコード（空なら「JFAフェスティバル掲載依頼」。記述は inc/request-jfa-form.txt） phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
<aside aria-label="送信の前に" class="side ct-side">
<div>
<h3>送信の前に</h3>
<p><b>office_kouhou@nagano-fa.or.jp</b> からのメールを受信できるよう設定してください。</p>
<p>半角カナは使用しないでください。</p>
</div>
<div>
<h3>掲載までのスケジュール</h3>
<p>月曜9:00までの依頼 → 火曜午前に掲載<br/>水曜9:00までの依頼 → 木曜午前に掲載</p>
<a class="ct-lk" href="<?php echo esc_url( nfa_u( 'request.html#rotation' ) ); ?>">ローテーションの詳細</a>
</div>
<div>
<h3>うまく送信できない場合</h3>
<p>下記のフォーマットに必要な情報を記載のうえ、<a href="mailto:office_kouhou@nagano-fa.or.jp">office_kouhou@nagano-fa.or.jp</a> へ添付してお送りください。</p>
<a class="ct-lk rj-fl" <?php nfa_href( 'https://www.nagano-fa.or.jp/wp-content/uploads/2017/03/game-results-format.xls' ); ?>>試合結果用のフォーマット（Excel）</a>
<a class="ct-lk rj-fl" <?php nfa_href( 'https://www.nagano-fa.or.jp/wp-content/uploads/2017/03/request-hp.docx' ); ?>>試合結果以外のフォーマット（Word）</a>
</div>
</aside>
</div>
</div></section>
<?php
get_footer();
