<?php
/**
 * 設定値（管理画面で編集）。APIキーは含めない（wp-config.php の定数で管理）。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Settings {

	const OPTION = 'nfa_ais_settings';

	public static function defaults() {
		return array(
			// 検索対象にする投稿タイプ（許可リスト）。ここにないものは一切取り込まない。
			'post_types'     => array( 'post', 'page' ),
			// 個別に除外する固定ページ・投稿のID（お問い合わせページ、送信完了ページなど）
			'excluded_ids'   => array(),
			// 検索対象にするカスタムフィールド（許可した項目のみ。段階2の取り込みで使用）
			'meta_keys'      => array(),
			// AI（Claude）で入力文を解釈するか。APIキー未設定なら常にルールベース。
			'ai_enabled'     => true,
			// 1日あたりのAI呼び出し上限。超えたらルールベースに切り替える。
			'daily_ai_cap'   => 2000,
			// 1分あたりの検索回数の上限（IPごと）
			'rate_per_min'   => 30,
			// 検索ログの保存日数
			'log_days'       => 90,
			// アクセス解析（Google アナリティクス 4）にイベントを送るか（タグは別に埋め込む）
			'analytics'      => true,
		);
	}

	public static function get() {
		$saved = get_option( self::OPTION, array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), self::defaults() );
	}

	public static function value( $key ) {
		$s = self::get();
		return $s[ $key ] ?? null;
	}

	public static function post_types() {
		$types = (array) self::value( 'post_types' );
		// 添付・リビジョン・Contact Form 7 / Flamingo などは、設定に紛れ込んでも対象にしない。
		return array_values( array_diff( $types, NFA_AIS_Eligibility::NEVER_TYPES ) );
	}

	public static function sanitize( $input ) {
		$d   = self::defaults();
		$out = array();

		$public          = get_post_types( array( 'public' => true ) );
		$out['post_types'] = array_values(
			array_intersect( array_map( 'sanitize_key', (array) ( $input['post_types'] ?? array() ) ), $public )
		);
		$out['post_types'] = array_values( array_diff( $out['post_types'], NFA_AIS_Eligibility::NEVER_TYPES ) );

		$ids                 = is_array( $input['excluded_ids'] ?? null ) ? $input['excluded_ids'] : preg_split( '/[\s,、]+/u', (string) ( $input['excluded_ids'] ?? '' ) );
		$out['excluded_ids'] = array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );

		$keys             = is_array( $input['meta_keys'] ?? null ) ? $input['meta_keys'] : preg_split( '/[\s,、]+/u', (string) ( $input['meta_keys'] ?? '' ) );
		$out['meta_keys'] = array_values( array_unique( array_filter( array_map( 'sanitize_key', $keys ) ) ) );

		$out['ai_enabled']   = ! empty( $input['ai_enabled'] );
		$out['daily_ai_cap'] = max( 0, absint( $input['daily_ai_cap'] ?? $d['daily_ai_cap'] ) );
		$out['rate_per_min'] = max( 1, absint( $input['rate_per_min'] ?? $d['rate_per_min'] ) );
		$out['log_days']     = min( 365, max( 1, absint( $input['log_days'] ?? $d['log_days'] ) ) );
		$out['analytics']    = ! empty( $input['analytics'] );
		return $out;
	}

	public static function has_api_key() {
		return defined( 'NFA_ANTHROPIC_API_KEY' ) && '' !== NFA_ANTHROPIC_API_KEY;
	}
}
