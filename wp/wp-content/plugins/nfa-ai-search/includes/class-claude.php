<?php
/**
 * Claude（Anthropic Messages API）の呼び出し。
 *
 * WordPress プラグインとして依存を増やさないため、公式 PHP SDK ではなく wp_remote_post で
 * REST API（POST /v1/messages）を直接呼ぶ。APIキーは wp-config.php の NFA_ANTHROPIC_API_KEY。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Claude {

	const ENDPOINT      = 'https://api.anthropic.com/v1/messages';
	const DEFAULT_MODEL = 'claude-haiku-5-5';
	const USAGE_OPTION  = 'nfa_ais_ai_usage';

	public static function model() {
		return defined( 'NFA_ANTHROPIC_MODEL' ) && NFA_ANTHROPIC_MODEL ? NFA_ANTHROPIC_MODEL : self::DEFAULT_MODEL;
	}

	/**
	 * いまAIを使ってよいか（キーあり・設定で有効・日次上限内）。
	 */
	public static function available() {
		if ( ! NFA_AIS_Settings::has_api_key() || ! NFA_AIS_Settings::value( 'ai_enabled' ) ) {
			return false;
		}
		$cap = (int) NFA_AIS_Settings::value( 'daily_ai_cap' );
		return $cap > 0 && self::today_count() < $cap;
	}

	public static function today_count() {
		$u = get_option( self::USAGE_OPTION, array() );
		return ( is_array( $u ) && ( $u['date'] ?? '' ) === wp_date( 'Y-m-d' ) ) ? (int) $u['count'] : 0;
	}

	private static function count_call( $usage ) {
		$u = get_option( self::USAGE_OPTION, array() );
		if ( ! is_array( $u ) || ( $u['date'] ?? '' ) !== wp_date( 'Y-m-d' ) ) {
			$u = array( 'date' => wp_date( 'Y-m-d' ), 'count' => 0, 'input_tokens' => 0, 'output_tokens' => 0 );
		}
		$u['count']++;
		$u['input_tokens']  += (int) ( $usage['input_tokens'] ?? 0 ) + (int) ( $usage['cache_read_input_tokens'] ?? 0 );
		$u['output_tokens'] += (int) ( $usage['output_tokens'] ?? 0 );
		update_option( self::USAGE_OPTION, $u, false );
	}

	/**
	 * JSON スキーマで形を決めた応答を得る（構造化出力）。
	 *
	 * @param string $system  指示文（毎回同じ内容にしてプロンプトキャッシュを効かせる）
	 * @param string $user    利用者の入力
	 * @param array  $schema  JSON Schema（additionalProperties:false / required を含む）
	 * @return array|WP_Error 解析済みの連想配列
	 */
	public static function structured( $system, $user, array $schema, $max_tokens = 1024 ) {
		if ( ! self::available() ) {
			return new WP_Error( 'nfa_ai_unavailable', 'AI is not available.' );
		}
		$body = array(
			'model'         => self::model(),
			'max_tokens'    => $max_tokens,
			// 条件の抽出は短い作業なので、思考は浅く速くする。
			'output_config' => array(
				'effort' => 'low',
				'format' => array( 'type' => 'json_schema', 'schema' => $schema ),
			),
			'system'        => array(
				array( 'type' => 'text', 'text' => $system, 'cache_control' => array( 'type' => 'ephemeral' ) ),
			),
			'messages'      => array(
				array( 'role' => 'user', 'content' => $user ),
			),
		);
		$res = wp_remote_post(
			self::ENDPOINT,
			array(
				'timeout' => 8,
				'headers' => array(
					'content-type'      => 'application/json',
					'x-api-key'         => NFA_ANTHROPIC_API_KEY,
					'anthropic-version' => '2023-06-01',
				),
				'body'    => wp_json_encode( $body ),
			)
		);
		if ( is_wp_error( $res ) ) {
			return $res;
		}
		$code = wp_remote_retrieve_response_code( $res );
		$data = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( 200 !== $code || ! is_array( $data ) ) {
			$msg = is_array( $data ) ? ( $data['error']['message'] ?? '' ) : '';
			return new WP_Error( 'nfa_ai_http', sprintf( 'Claude API error %d %s', $code, $msg ) );
		}
		self::count_call( $data['usage'] ?? array() );
		if ( 'refusal' === ( $data['stop_reason'] ?? '' ) ) {
			return new WP_Error( 'nfa_ai_refusal', 'The request was declined.' );
		}
		if ( 'max_tokens' === ( $data['stop_reason'] ?? '' ) ) {
			return new WP_Error( 'nfa_ai_truncated', 'The response was cut off.' );
		}
		foreach ( (array) ( $data['content'] ?? array() ) as $block ) {
			if ( 'text' === ( $block['type'] ?? '' ) ) {
				$parsed = json_decode( $block['text'], true );
				return is_array( $parsed ) ? $parsed : new WP_Error( 'nfa_ai_json', 'Invalid JSON from model.' );
			}
		}
		return new WP_Error( 'nfa_ai_empty', 'No text block in response.' );
	}
}
