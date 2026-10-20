<?php
/**
 * 投稿のカテゴリー（記事の種類）。
 *
 *   お知らせ（news）
 *     ├ お知らせ・募集（news-info）   … 大会・講習会・イベントの案内、募集
 *     └ 重要なお知らせ（news-important）
 *   活動報告（action_report）          … 開催報告・活動報告
 *
 * 発信元（協会・各委員会）は担当委員会（nfa_committee）で持つ。カテゴリーは記事の種類だけ。
 * 記事詳細のテンプレート（お知らせ・開催報告・重要）は、まだカテゴリーと結び付けていない（投稿の「テンプレート」で選ぶ）。
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

class NFA_Com_Categories {

	const NEWS      = 'news';
	const INFO      = 'news-info';
	const IMPORTANT = 'news-important';
	const REPORT    = 'action_report';

	/** スラッグ => [名前, 親のスラッグ] */
	public static function definitions() {
		return array(
			self::NEWS      => array( 'お知らせ', '' ),
			self::INFO      => array( 'お知らせ・募集', self::NEWS ),
			self::IMPORTANT => array( '重要なお知らせ', self::NEWS ),
			self::REPORT    => array( '活動報告', '' ),
		);
	}

	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_seed' ), 20 );
	}

	public static function maybe_seed() {
		if ( get_option( 'nfa_com_categories_seeded' ) === NFA_COM_VERSION ) {
			return;
		}
		self::seed();
	}

	public static function seed() {
		foreach ( self::definitions() as $slug => $d ) {
			if ( get_term_by( 'slug', $slug, 'category' ) ) {
				continue;
			}
			$parent = $d[1] ? get_term_by( 'slug', $d[1], 'category' ) : null;
			wp_insert_term( $d[0], 'category', array( 'slug' => $slug, 'parent' => $parent ? (int) $parent->term_id : 0 ) );
		}
		// 新しい記事は「お知らせ」に入る
		$news = get_term_by( 'slug', self::NEWS, 'category' );
		if ( $news ) {
			update_option( 'default_category', (int) $news->term_id );
		}
		update_option( 'nfa_com_categories_seeded', NFA_COM_VERSION, false );
	}

	/**
	 * カテゴリーのスラッグ → 用語ID（なければ 0）。
	 */
	public static function id( $slug ) {
		$t = get_term_by( 'slug', $slug, 'category' );
		return $t ? (int) $t->term_id : 0;
	}

	/**
	 * 一覧の「カテゴリー」で選べるもの（スラッグ => 名前）。
	 */
	public static function filters() {
		return array(
			self::INFO      => 'お知らせ・募集',
			self::IMPORTANT => '重要なお知らせ',
			self::REPORT    => '活動報告',
		);
	}
}
