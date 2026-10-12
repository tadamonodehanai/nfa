<?php
/**
 * 投稿から検索・表示用のテキストを取り出す。
 *
 * ショートコード（[contact-form-7 …] など）とブロックのコメントは取り除き、フォームの中身を拾わない。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Text {

	public static function plain( $html ) {
		$text = strip_shortcodes( (string) $html );
		$text = preg_replace( '/<!--.*?-->/s', '', $text );
		$text = wp_strip_all_tags( $text );
		$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
		return trim( preg_replace( '/\s+/u', ' ', $text ) );
	}

	/**
	 * 一覧に出す短い説明（抜粋があれば抜粋、なければ本文の先頭）。
	 */
	public static function snippet( $post, $len = 90 ) {
		$post = get_post( $post );
		$src  = '' !== trim( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;
		$text = self::plain( $src );
		return mb_strlen( $text ) > $len ? mb_substr( $text, 0, $len ) . '…' : $text;
	}

	/**
	 * 回答カードに出す、少し長めの説明。
	 */
	public static function lead( $post, $len = 180 ) {
		$post = get_post( $post );
		$text = self::plain( $post->post_content );
		if ( '' === $text ) {
			$text = self::plain( $post->post_excerpt );
		}
		return mb_strlen( $text ) > $len ? mb_substr( $text, 0, $len ) . '…' : $text;
	}

	/**
	 * 検索ログやAIに送る前に、メールアドレスと電話番号を伏せ字にする。
	 */
	public static function mask_personal( $text ) {
		$text = preg_replace( '/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/u', '［メール］', (string) $text );
		// 全角数字も含め、10〜11桁程度の電話番号らしき並び
		$text = preg_replace( '/(?:[0-9０-９][\-－ー\s]?){9,12}[0-9０-９]/u', '［電話番号］', $text );
		return $text;
	}
}
