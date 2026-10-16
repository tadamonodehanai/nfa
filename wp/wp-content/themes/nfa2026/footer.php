<?php
/**
 * フッター（協賛帯・共通フッター）。
 *
 * @package NFA2026
 */
?>
</main>
<?php
// 協賛帯：TOP は本文の中に関連リンクと一緒に出す。協賛ページは本文と重複するので出さない。
if ( ! is_front_page() && ! is_page( 'partner' ) ) {
	get_template_part( 'template-parts/spband' );
}
?>
<footer class="ft">
  <div class="wrap">
    <div class="ft-g">
      <div>
        <a class="logo" href="<?php echo esc_url( nfa_u( 'index.html' ) ); ?>"><span class="ab" aria-hidden="true">NFA</span><span class="nm"><b>長野県サッカー協会</b><small style="color:#9FB0DD">NAGANO FOOTBALL ASSOCIATION</small></span></a>
        <p style="margin:16px 0 0;line-height:1.9">〒390-8565 長野県松本市深志2丁目6-9 LON BLDG. 3F<br>TEL 0263-87-8565　FAX 0263-87-8587<br>審判委員会 TEL 0263-50-8330</p>
        <div class="sns" aria-label="SNS"><span>X</span><span>IG</span><span>YT</span><span>FB</span></div>
      </div>
      <div class="wide"><h4>CATEGORY</h4><ul class="ft-cats"><li><a href="<?php echo esc_url( nfa_u( 'cat-4.html' ) ); ?>">小学生<small>4種</small></a></li><li><a href="<?php echo esc_url( nfa_u( 'cat-3.html' ) ); ?>">中学生<small>3種</small></a></li><li><a href="<?php echo esc_url( nfa_u( 'cat-2.html' ) ); ?>">高校生<small>2種</small></a></li><li><a href="<?php echo esc_url( nfa_u( 'cat-1.html' ) ); ?>">社会人・大学<small>1種</small></a></li><li><a href="<?php echo esc_url( nfa_u( 'cat-w.html' ) ); ?>">女子<small>女子委員会</small></a></li><li><a href="<?php echo esc_url( nfa_u( 'cat-f.html' ) ); ?>">フットサル<small>フットサル委員会</small></a></li><li><a href="<?php echo esc_url( nfa_u( 'cat-s.html' ) ); ?>">シニア<small>シニア委員会</small></a></li><li><a href="<?php echo esc_url( nfa_u( 'grassroots.html' ) ); ?>">キッズ<small>グラスルーツ</small></a></li><li><a href="<?php echo esc_url( nfa_u( 'referee.html' ) ); ?>">審判<small>審判委員会</small></a></li><li><a href="<?php echo esc_url( nfa_u( 'technical.html' ) ); ?>">指導者<small>技術委員会</small></a></li><li><a href="<?php echo esc_url( nfa_u( 'medical.html' ) ); ?>">医学<small>医学委員会</small></a></li></ul></div>
      <div><h4>SITE</h4><ul><li><a href="<?php echo esc_url( nfa_u( 'about.html' ) ); ?>">協会について</a></li><li><a href="<?php echo esc_url( nfa_u( 'start.html' ) ); ?>">はじめる</a></li><li><a href="<?php echo esc_url( nfa_u( 'grow.html' ) ); ?>">育成・強化</a></li><li><a href="<?php echo esc_url( nfa_u( 'partner.html' ) ); ?>">関わる</a></li><li><a href="<?php echo esc_url( nfa_u( 'list.html' ) ); ?>">お知らせ一覧</a></li><li><a href="<?php echo esc_url( nfa_u( 'teams.html' ) ); ?>">チームを探す</a></li><li><a href="<?php echo esc_url( nfa_u( 'search.html' ) ); ?>">情報を探す（AI）</a></li></ul></div>
    </div>
    <div class="copy"><span>© Nagano Football Association</span><span><a href="<?php echo esc_url( nfa_u( 'contact.html' ) ); ?>">お問い合わせ</a>　<a href="<?php echo esc_url( nfa_u( 'privacy.html' ) ); ?>">プライバシーポリシー</a>　<a href="https://www.jfa.jp/">JFA</a></span></div>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
