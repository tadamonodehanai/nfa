<?php
/**
 * アクセス解析のタグ（Google アナリティクス 4 の gtag.js、または Google タグマネージャー）。
 *
 * ↓ の「ここに貼る」の位置に、GA（または GTM）から渡されたタグをそのまま貼ってください。
 * header.php の <head> の直後で読み込みます（header.php はデザインから変換したファイルなので、タグは直接貼らない）。
 *
 * 開発環境（localhost など）では読み込みません。本番のアクセスだけを記録するためです。
 * 本番で読み込まれないときは、wp-config.php の WP_ENVIRONMENT_TYPE が production（または未設定）か確かめてください。
 *
 * @package NFA2026
 */

if ( ! nfa_analytics_enabled() ) {
	echo '<!-- アクセス解析のタグは出力していません：' . esc_html( nfa_analytics_off_reason() ) . " -->\n";
	return;
}
?>
<!-- ここに貼る（GA4 の gtag.js、または GTM のタグ） -->
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-L01K50JJ0X"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-L01K50JJ0X');
</script>
<!-- ここまで -->
