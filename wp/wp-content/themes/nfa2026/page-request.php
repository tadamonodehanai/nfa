<?php
/**
 * 掲載依頼（デザイン request.html）。scratchpad の conv2.py で生成。
 *
 * @package NFA2026
 */

get_header();
?>

<nav aria-label="パンくず" class="bc wrap"><a href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>">TOP</a><span aria-hidden="true">›</span><span aria-current="page">掲載依頼</span></nav>
<section class="pg-hero"><div class="wrap"><div class="pg-ttl"><p aria-hidden="true" class="pg-en"><span class="ln"><span>REQUEST</span></span></p><h1 class="pg-ja">掲載依頼</h1></div><div class="pg-sub"><p>日程・試合結果・お知らせなど、当サイトへの掲載を依頼する方法です。<br class="pc"/>試合結果・星取表は必ずPDFにして、掲載依頼フォームからアップロードしてください。</p></div></div></section>
<section class="blk" id="form"><div class="wrap">
<div class="bh"><div><div class="e">FORM</div><h2>掲載依頼フォーム</h2></div></div>
<div class="cards c2 fx-rq">
<a class="cd" href="https://forms.gle/NwtuuFpxL4AaL17t7" rel="noopener" target="_blank"><div class="bd"><div class="lab">GOOGLE FORM</div><h3>日程・試合結果・NEWS<br/>掲載依頼フォーム</h3><p>大会の日程、試合結果、星取表、お知らせの掲載はこちらから依頼してください。</p><span class="go">フォームを開く</span></div></a>
<a class="cd" href="#jfa"><div class="bd"><div class="lab">JFA FESTIVAL</div><h3>JFAフェスティバル<br/>掲載依頼フォーム</h3><p>JFAキッズ／レディース・ガールズ／ファミリーフットサルフェスティバル専用の掲載依頼です。</p><span class="go">ご案内を見る</span></div></a>
</div>
</div></section>
<section class="blk pale" id="rotation"><div class="wrap">
<div class="bh"><div><div class="e">SCHEDULE</div><h2>掲載までのローテーション</h2></div></div>
<div class="tbl-w"><table class="tbl fx-tbl fx-rt"><thead><tr><th scope="col">依頼の締切</th><th scope="col">掲載日</th></tr></thead><tbody>
<tr><td data-label="依頼の締切"><b>月曜 9:00</b> までの依頼</td><td data-label="掲載日"><b>火曜</b> 午前に掲載</td></tr>
<tr><td data-label="依頼の締切"><b>水曜 9:00</b> までの依頼</td><td data-label="掲載日"><b>木曜</b> 午前に掲載</td></tr>
</tbody></table></div>
<p class="fx-note">運用はすべて平日です。締切日が祝祭日・休日の場合は、翌営業日が締切日になります。ただし、掲載日を変更する場合があります。</p>
</div></section>
<section class="blk" id="format"><div class="wrap">
<div class="bh"><div><div class="e">FORMAT</div><h2>フォーマット</h2></div></div>
<ul class="dl"><li><a <?php nfa_href( 'https://www.nagano-fa.or.jp/wp-content/uploads/2017/03/df65d8a55fb60012ebcca948a5f9bdd7.xlsx' ); ?>><span class="fbtn-i x">XLS</span>試合結果用のフォーマット【リーグ戦用】</a></li><li><a <?php nfa_href( 'https://www.nagano-fa.or.jp/wp-content/uploads/2017/03/game-results-format.xls' ); ?>><span class="fbtn-i x">XLS</span>試合結果用のフォーマット【リーグ戦以外用】</a></li><li><a <?php nfa_href( 'https://www.nagano-fa.or.jp/wp-content/uploads/2025/04/bb1236bdcf13ec57628dbb882b699f8a.xlsx' ); ?>><span class="fbtn-i x">XLS</span>星取表フォーマット</a></li><li><a <?php nfa_href( 'https://www.nagano-fa.or.jp/wp-content/uploads/2017/03/request-hp.docx' ); ?>><span class="fbtn-i w">DOC</span>試合結果以外のフォーマット</a></li></ul>
</div></section>
<section class="blk pale" id="jfa"><div class="wrap">
<div class="bh"><div><div class="e">JFA FESTIVAL</div><h2>JFAフェスティバルの掲載依頼</h2></div></div>
<div class="prose">
<p>JFAフェスティバル専用の掲載依頼フォームです。一度送信した掲載依頼を修正する場合も、こちらから再度送信してください。その際は、以前の掲載依頼の修正であることがわかるよう、掲載内容の冒頭にその旨を記載してください。</p>
<ul>
<li>office_kouhou@nagano-fa.or.jp からのメールを受信できるよう設定してください。</li>
<li>半角カナは使用しないでください。</li>
<li>添付できるファイルは Windows系ファイル（ワード、エクセル、パワーポイント、PDF、イラストレーター、フォトショップ、写真：jpeg・jpg・gif・png、テキスト）で、合計20MBまでです。</li>
</ul>
</div>
<div class="tbl-w"><table class="tbl fx-tbl"><caption class="fx-cap">フォームの入力項目</caption><tbody>
<tr><th scope="row">掲載依頼者名・電話番号・メールアドレス<span class="req">必須</span></th><td>電話番号は市外局番から（携帯可）</td></tr>
<tr><th scope="row">所属<span class="req">必須</span></th><td>事務局／技術／審判／施設／医学／国体／規律／<span class="nw">1種</span>社会人／<span class="nw">1種</span>学生／<span class="nw">2種</span>／<span class="nw">3種</span>／<span class="nw">4種</span>／女子／フットサル／シニア／グラスルーツ</td></tr>
<tr><th scope="row">タイトル<span class="req">必須</span></th><td>キッズサッカー／レディース・ガールズサッカー／ファミリーフットサル フェスティバル20××長野in（会場名）</td></tr>
<tr><th scope="row">主催・共催・主管・後援<span class="req">必須</span></th><td>主催：一般社団法人長野県サッカー協会／共催：開催自治体等／主管：地区・市区郡町村サッカー協会、各種連盟の都道府県組織等／後援：公益財団法人日本サッカー協会</td></tr>
<tr><th scope="row">協力</th><td>チーム名等</td></tr>
<tr><th scope="row">開催日程・会場・参加者<span class="req">必須</span></th><td>参加者は対象となる参加者を記入</td></tr>
<tr><th scope="row">掲載内容・資料</th><td>募集内容など。資料は5点まで添付できます</td></tr>
</tbody></table></div>
<p class="fx-note">うまく送信できない場合は、フォーマット（試合結果、試合結果以外）に必要な情報を記載のうえ、<a href="mailto:office_kouhou@nagano-fa.or.jp">office_kouhou@nagano-fa.or.jp</a> へ添付してお送りください。</p>
<div class="fx-btns"><a class="btn btn-g" href="<?php echo esc_url( nfa_u( 'request-jfa.html' ) ); ?>">JFAフェスティバル掲載依頼フォーム<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></div>
</div></section>
<section class="blk pale fx-ct"><div class="wrap">
<div class="fx-ctb"><div><div class="e">CONTACT</div><h2>お問い合わせ</h2><p>（一社）長野県サッカー協会 事務局<br/>TEL 0263-87-8565</p></div><a class="btn btn-n" href="<?php echo esc_url( nfa_u( 'contact.html' ) ); ?>">お問い合わせフォーム<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></div>
</div></section>
<?php
get_footer();
