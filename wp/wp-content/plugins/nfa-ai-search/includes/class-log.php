<?php
/**
 * 検索ログ（辞書を育てるため）。
 *
 * 保存するのは入力文（メールアドレス・電話番号は伏せ字）、条件、件数だけ。
 * IPアドレスなど個人を特定できる情報は保存しない。保存期間は設定の日数（初期値90日）。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Log {

	const DB_VERSION = '1';
	const CRON_HOOK  = 'nfa_ais_log_cleanup';

	public static function init() {
		add_action( self::CRON_HOOK, array( __CLASS__, 'cleanup' ) );
		add_action(
			'init',
			function () {
				if ( get_option( 'nfa_ais_log_db' ) !== self::DB_VERSION ) {
					self::install();
				}
				if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
					wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::CRON_HOOK );
				}
			}
		);
	}

	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'nfa_ais_log';
	}

	public static function install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table   = self::table();
		$charset = $wpdb->get_charset_collate();
		dbDelta(
			"CREATE TABLE $table (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				created_at datetime NOT NULL,
				via varchar(8) NOT NULL DEFAULT '',
				query varchar(200) NOT NULL DEFAULT '',
				conds varchar(255) NOT NULL DEFAULT '',
				total int(11) NOT NULL DEFAULT 0,
				parser varchar(16) NOT NULL DEFAULT '',
				PRIMARY KEY  (id),
				KEY created_at (created_at),
				KEY total (total)
			) $charset;"
		);
		update_option( 'nfa_ais_log_db', self::DB_VERSION, false );
	}

	/**
	 * 利用者が入力した文章のときだけ記録する（タグ操作は記録しない）。
	 */
	public static function record( $via, $query, array $result ) {
		if ( 'text' !== $via || '' === trim( (string) $query ) ) {
			return;
		}
		global $wpdb;
		$wpdb->insert(
			self::table(),
			array(
				'created_at' => current_time( 'mysql', true ),
				'via'        => $via,
				'query'      => mb_substr( NFA_AIS_Text::mask_personal( $query ), 0, 200 ),
				'conds'      => mb_substr( implode( ' / ', array_filter( array_merge( $result['c']['t'], $result['c']['p'], $result['c']['r'], array( $result['c']['join'] ? 'チーム探し' : '', $result['c']['kw'] ) ) ) ), 0, 255 ),
				'total'      => (int) $result['total'],
				'parser'     => $result['parser'],
			),
			array( '%s', '%s', '%s', '%s', '%d', '%s' )
		);
	}

	public static function cleanup() {
		global $wpdb;
		$days  = (int) NFA_AIS_Settings::value( 'log_days' );
		$table = self::table();
		$wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE created_at < ( UTC_TIMESTAMP() - INTERVAL %d DAY )", $days ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * 0件だった入力文の多い順（直近の日数）。
	 */
	public static function zero_hits( $days = 30, $limit = 50 ) {
		global $wpdb;
		$table = self::table();
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT query, COUNT(*) AS times, MAX(created_at) AS last_at FROM $table
				WHERE total = 0 AND created_at >= ( UTC_TIMESTAMP() - INTERVAL %d DAY )
				GROUP BY query ORDER BY times DESC, last_at DESC LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$days,
				$limit
			)
		);
	}

	public static function recent( $limit = 50 ) {
		global $wpdb;
		$table = self::table();
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table ORDER BY id DESC LIMIT %d", $limit ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}
