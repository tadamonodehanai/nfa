<?php
/**
 * REST API。
 *
 *   GET /wp-json/nfa/v1/search?q=...&via=text|set&cond[target]=...&cond[purpose]=...&cond[area]=...   1ターン分の探索結果
 *   GET /wp-json/nfa/v1/nonce                                        ページキャッシュで古くなった nonce の取り直し
 *
 *   管理者用（取り込み）：
 *   GET  /wp-json/nfa/v1/ingest?counts=1   進み具合（counts=1 で差分の件数も）
 *   POST /wp-json/nfa/v1/ingest/start      mode=diff（差分）| all（全件を作り直す）
 *   POST /wp-json/nfa/v1/ingest/step       待ち行列を少し進める（管理画面が開いている間に呼ぶ）
 *   POST /wp-json/nfa/v1/ingest/cancel     中止
 *
 * 段階3で POST /wp-json/nfa/v1/answer（出典付きの案内文）を追加する。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Rest {

	const NS           = 'nfa/v1';
	const NONCE_ACTION = 'nfa_ais_search';

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	public static function routes() {
		register_rest_route(
			self::NS,
			'/search',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'search' ),
				'permission_callback' => array( __CLASS__, 'check_nonce' ),
				'args'                => array(
					'q'     => array(
						'type'              => 'string',
						'default'           => '',
						'sanitize_callback' => fn( $v ) => mb_substr( sanitize_text_field( $v ), 0, 200 ),
					),
					'via'  => array(
						'type'    => 'string',
						'default' => 'text',
						'enum'    => array( 'text', 'set' ),
					),
					'cond' => array(
						'type'              => 'object',
						'default'           => array(),
						'sanitize_callback' => fn( $v ) => is_array( $v ) ? array_map( fn( $c ) => mb_substr( sanitize_text_field( (string) $c ), 0, 40 ), array_intersect_key( $v, array_flip( array( 'target', 'purpose', 'area', 'keyword' ) ) ) ) : array(),
					),
				),
			)
		);
		register_rest_route(
			self::NS,
			'/nonce',
			array(
				'methods'             => 'GET',
				'callback'            => fn() => self::no_cache( new WP_REST_Response( array( 'nonce' => wp_create_nonce( self::NONCE_ACTION ) ) ) ),
				'permission_callback' => '__return_true',
			)
		);
		$admin = fn() => current_user_can( 'manage_options' );
		register_rest_route(
			self::NS,
			'/ingest',
			array(
				'methods'             => 'GET',
				'callback'            => fn( WP_REST_Request $r ) => self::no_cache( new WP_REST_Response( self::ingest_status( (bool) $r->get_param( 'counts' ) ) ) ),
				'permission_callback' => $admin,
			)
		);
		register_rest_route(
			self::NS,
			'/ingest/start',
			array(
				'methods'             => 'POST',
				'callback'            => function ( WP_REST_Request $r ) {
					NFA_AIS_Ingest::start( 'all' === $r->get_param( 'mode' ) ? 'all' : 'diff' );
					return self::no_cache( new WP_REST_Response( self::ingest_status( false ) ) );
				},
				'permission_callback' => $admin,
			)
		);
		register_rest_route(
			self::NS,
			'/ingest/step',
			array(
				'methods'             => 'POST',
				'callback'            => function () {
					NFA_AIS_Ingest::step();
					return self::no_cache( new WP_REST_Response( self::ingest_status( false ) ) );
				},
				'permission_callback' => $admin,
			)
		);
		register_rest_route(
			self::NS,
			'/ingest/cancel',
			array(
				'methods'             => 'POST',
				'callback'            => function () {
					NFA_AIS_Ingest::cancel();
					return self::no_cache( new WP_REST_Response( self::ingest_status( false ) ) );
				},
				'permission_callback' => $admin,
			)
		);
	}

	public static function ingest_status( $with_counts ) {
		$out = array(
			'job'   => NFA_AIS_Ingest::job(),
			'queue' => NFA_AIS_Ingest::queue_size(),
		);
		if ( $with_counts ) {
			$scan          = NFA_AIS_Ingest::scan();
			$out['counts'] = array(
				'eligible'     => $scan['eligible'],
				'indexed'      => $scan['indexed'],
				'new'          => count( $scan['new'] ),
				'changed'      => count( $scan['changed'] ) - $scan['errors'],
				'errors'       => $scan['errors'],
				'removed'      => count( $scan['removed'] ),
				'unclassified' => count( NFA_AIS_Ingest::unclassified() ),
			);
		}
		return $out;
	}

	/**
	 * 探索画面から来た呼び出しか（他サイトからの大量呼び出しを防ぐ）。無効化は filter で。
	 */
	public static function check_nonce( WP_REST_Request $req ) {
		if ( ! apply_filters( 'nfa_ais_require_nonce', true ) ) {
			return true;
		}
		$nonce = $req->get_header( 'x_nfa_nonce' );
		if ( $nonce && wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return true;
		}
		return new WP_Error( 'nfa_bad_nonce', 'Invalid or expired nonce.', array( 'status' => 403 ) );
	}

	public static function search( WP_REST_Request $req ) {
		$limited = self::rate_limited();
		if ( $limited ) {
			return $limited;
		}
		$q     = (string) $req->get_param( 'q' );
		$via   = (string) $req->get_param( 'via' );
		$cond = (array) $req->get_param( 'cond' );
		ksort( $cond );

		$ck  = NFA_AIS_Index::cache_key( 'search|' . $via . '|' . $q . '|' . wp_json_encode( $cond ) );
		$res = get_transient( $ck );
		if ( ! is_array( $res ) ) {
			$res = NFA_AIS_Search::run( $q, $via, $cond );
			set_transient( $ck, $res, 10 * MINUTE_IN_SECONDS );
		}
		NFA_AIS_Log::record( $via, $q, $res );
		return self::no_cache( new WP_REST_Response( $res ) );
	}

	private static function no_cache( WP_REST_Response $r ) {
		$r->header( 'Cache-Control', 'no-store' );
		return $r;
	}

	/**
	 * IPごとの回数制限。IPアドレスはハッシュにして一時キーに使うだけで、保存しない。
	 */
	private static function rate_limited() {
		$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$key   = 'nfa_ais_rl_' . md5( wp_salt( 'nonce' ) . $ip . gmdate( 'YmdHi' ) );
		$count = (int) get_transient( $key );
		if ( $count >= (int) NFA_AIS_Settings::value( 'rate_per_min' ) ) {
			return new WP_Error( 'nfa_rate_limited', '短い時間に検索が集中しています。少し待ってからお試しください。', array( 'status' => 429 ) );
		}
		set_transient( $key, $count + 1, 2 * MINUTE_IN_SECONDS );
		return null;
	}
}
