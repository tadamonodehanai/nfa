<?php
/**
 * 取り込み（差分の再取り込み）。
 *
 * 記事ごとに「本文のハッシュ」と「取り込んだときの更新日時」を記録し、次の3種類だけを処理する。
 *   未取り込み … 検索対象なのに記録がない（新しい記事、移行してきた記事）
 *   更新あり   … 記録より後に更新された記事
 *   削除待ち   … 記録はあるが、非公開・削除・除外などで検索対象でなくなった記事
 *
 * SSH がなくても動くよう、処理は小分け（1回 最大 BATCH 件・TIME_BUDGET 秒）にし、
 * 管理画面からの呼び出しと WP-Cron の両方で続きを進める。
 *
 * 段階2（記事本文・PDFのベクトル検索）は、次のアクションに処理を足して実装する：
 *   do_action( 'nfa_ais_document_changed', $post_id, $text )  … 分割・ベクトル化して保存
 *   do_action( 'nfa_ais_document_removed', $post_id )         … その記事の断片を削除
 * 例外を投げると、その記事は「エラー」として記録され、次回の差分取り込みでやり直す。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Ingest {

	const DB_VERSION  = '1';
	const QUEUE       = 'nfa_ais_ingest_queue';
	const JOB         = 'nfa_ais_ingest_job';
	const LOCK        = 'nfa_ais_ingest_lock';
	const CRON_STEP   = 'nfa_ais_ingest_step';
	const CRON_SCAN   = 'nfa_ais_ingest_scan';
	const BATCH       = 50;
	const TIME_BUDGET = 15;

	public static function init() {
		add_action(
			'init',
			function () {
				if ( get_option( 'nfa_ais_docs_db' ) !== self::DB_VERSION ) {
					self::install();
				}
				if ( ! wp_next_scheduled( self::CRON_SCAN ) ) {
					wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::CRON_SCAN );
				}
			}
		);
		add_action( self::CRON_STEP, array( __CLASS__, 'cron_step' ) );
		// 1日1回、取りこぼし（直接DBに入れた記事など）を拾う
		add_action( self::CRON_SCAN, fn() => self::queue_size() ? null : self::start( 'diff' ) );

		// 記事が変わったら、その記事だけを待ち行列に入れる
		add_action( 'save_post', array( __CLASS__, 'on_save' ), 30, 2 );
		add_action( 'deleted_post', fn( $id ) => self::enqueue( array( $id ) ) );
		add_action( 'transition_post_status', fn( $new, $old, $post ) => $new !== $old ? self::on_save( $post->ID, $post ) : null, 10, 3 );
		add_action( 'updated_post_meta', array( __CLASS__, 'on_meta' ), 10, 3 );
		add_action( 'added_post_meta', array( __CLASS__, 'on_meta' ), 10, 3 );
		add_action( 'deleted_post_meta', array( __CLASS__, 'on_meta' ), 10, 3 );
	}

	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'nfa_ais_docs';
	}

	public static function install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table   = self::table();
		$charset = $wpdb->get_charset_collate();
		// 出典URLは保存しない（表示のたびに記事IDから作る。移行でドメインが変わっても壊れない）
		dbDelta(
			"CREATE TABLE $table (
				post_id bigint(20) unsigned NOT NULL,
				content_hash char(32) NOT NULL DEFAULT '',
				source_modified datetime NOT NULL,
				indexed_at datetime NOT NULL,
				status varchar(16) NOT NULL DEFAULT 'ok',
				message varchar(255) NOT NULL DEFAULT '',
				PRIMARY KEY  (post_id),
				KEY status (status)
			) $charset;"
		);
		update_option( 'nfa_ais_docs_db', self::DB_VERSION, false );
	}

	/* ---------- 自動（記事が変わったとき） ---------- */

	public static function on_save( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		if ( in_array( $post->post_type, NFA_AIS_Eligibility::NEVER_TYPES, true ) ) {
			return;
		}
		self::enqueue( array( $post_id ) );
	}

	public static function on_meta( $meta_id, $post_id, $meta_key ) {
		$watch = array_merge( array( NFA_AIS_Eligibility::META_EXCLUDE ), (array) NFA_AIS_Settings::value( 'meta_keys' ) );
		if ( in_array( $meta_key, $watch, true ) ) {
			self::enqueue( array( $post_id ) );
		}
	}

	/* ---------- 差分の判定 ---------- */

	/**
	 * いまの差分。
	 *
	 * @return array{new:int[], changed:int[], removed:int[], indexed:int, eligible:int, errors:int}
	 */
	public static function scan() {
		global $wpdb;
		$eligible = NFA_AIS_Eligibility::eligible_ids( true );
		$table    = self::table();
		$rows     = $wpdb->get_results( "SELECT post_id, source_modified, status FROM $table", OBJECT_K ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$modified = array();
		foreach ( array_chunk( $eligible, 1000 ) as $chunk ) {
			$in = implode( ',', array_map( 'intval', $chunk ) );
			foreach ( $wpdb->get_results( "SELECT ID, post_modified_gmt FROM {$wpdb->posts} WHERE ID IN ($in)" ) as $p ) { // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$modified[ (int) $p->ID ] = $p->post_modified_gmt;
			}
		}

		$new     = array();
		$changed = array();
		$errors  = 0;
		foreach ( $eligible as $id ) {
			if ( ! isset( $rows[ $id ] ) ) {
				$new[] = $id;
			} elseif ( 'ok' !== $rows[ $id ]->status ) {
				$changed[] = $id;
				$errors++;
			} elseif ( ( $modified[ $id ] ?? '' ) > $rows[ $id ]->source_modified ) {
				$changed[] = $id;
			}
		}
		$removed = array_values( array_diff( array_map( 'intval', array_keys( $rows ) ), $eligible ) );
		return array(
			'new'      => $new,
			'changed'  => $changed,
			'removed'  => $removed,
			'indexed'  => count( $rows ) - count( $removed ),
			'eligible' => count( $eligible ),
			'errors'   => $errors,
		);
	}

	/**
	 * 3軸のどれかが未設定の検索対象記事（分類の確認用）。
	 *
	 * @return int[]
	 */
	public static function unclassified() {
		$eligible = NFA_AIS_Eligibility::eligible_ids();
		$missing  = array();
		foreach ( NFA_AIS_Taxonomies::FACETS as $axis ) {
			$terms = get_terms( array( 'taxonomy' => $axis[0], 'hide_empty' => false, 'fields' => 'ids' ) );
			$has   = ( $terms && ! is_wp_error( $terms ) ) ? array_map( 'intval', (array) get_objects_in_term( $terms, $axis[0] ) ) : array();
			$missing = array_merge( $missing, array_diff( $eligible, $has ) );
		}
		$missing = array_values( array_unique( $missing ) );
		rsort( $missing );
		return $missing;
	}

	/* ---------- 待ち行列 ---------- */

	public static function queue() {
		return array_map( 'intval', (array) get_option( self::QUEUE, array() ) );
	}

	public static function queue_size() {
		return count( self::queue() );
	}

	public static function job() {
		$job = get_option( self::JOB );
		return is_array( $job ) ? $job : null;
	}

	/**
	 * 取り込みを始める。
	 *
	 * @param string $mode diff（差分だけ）| all（全件を作り直す）
	 */
	public static function start( $mode = 'diff' ) {
		$scan = self::scan();
		if ( 'all' === $mode ) {
			global $wpdb;
			$table = self::table();
			$ids   = array_merge( NFA_AIS_Eligibility::eligible_ids(), $scan['removed'] );
			// 全件のときは記録を消して、ハッシュが同じでも処理し直す
			$wpdb->query( "UPDATE $table SET content_hash = ''" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		} else {
			$ids = array_merge( $scan['new'], $scan['changed'], $scan['removed'] );
		}
		$ids = array_values( array_unique( array_map( 'intval', $ids ) ) );
		update_option( self::QUEUE, $ids, false );
		update_option(
			self::JOB,
			array(
				'mode'        => $mode,
				'total'       => count( $ids ),
				'done'        => 0,
				'indexed'     => 0,
				'unchanged'   => 0,
				'removed'     => 0,
				'errors'      => array(),
				'started_at'  => time(),
				'finished_at' => $ids ? 0 : time(),
			),
			false
		);
		self::schedule();
		return self::job();
	}

	/**
	 * 個別の記事を待ち行列に足す（記事の保存時など）。
	 */
	public static function enqueue( array $ids ) {
		$ids = array_map( 'intval', $ids );
		$q   = self::queue();
		$add = array_values( array_diff( $ids, $q ) );
		if ( ! $add ) {
			return;
		}
		update_option( self::QUEUE, array_merge( $q, $add ), false );
		$job = self::job();
		if ( ! $job || ! empty( $job['finished_at'] ) ) {
			$job = array( 'mode' => 'auto', 'total' => 0, 'done' => 0, 'indexed' => 0, 'unchanged' => 0, 'removed' => 0, 'errors' => array(), 'started_at' => time(), 'finished_at' => 0 );
		}
		$job['total'] += count( $add );
		update_option( self::JOB, $job, false );
		self::schedule( 10 );
	}

	public static function cancel() {
		update_option( self::QUEUE, array(), false );
		$job = self::job();
		if ( $job && empty( $job['finished_at'] ) ) {
			$job['finished_at'] = time();
			$job['canceled']    = true;
			update_option( self::JOB, $job, false );
		}
		wp_clear_scheduled_hook( self::CRON_STEP );
	}

	private static function schedule( $delay = 0 ) {
		if ( self::queue_size() && ! wp_next_scheduled( self::CRON_STEP ) ) {
			wp_schedule_single_event( time() + $delay, self::CRON_STEP );
		}
	}

	public static function cron_step() {
		self::step();
		self::schedule( 5 );
	}

	/**
	 * 待ち行列から少しずつ処理する。同時に2か所から動かないようにロックする。
	 *
	 * @return array|null いまの job（ロック中で何もしなかったときは null）
	 */
	public static function step( $max = self::BATCH, $budget = self::TIME_BUDGET ) {
		if ( get_transient( self::LOCK ) ) {
			return null;
		}
		set_transient( self::LOCK, 1, $budget + 30 );
		$t0  = microtime( true );
		$job = self::job();
		$n   = 0;
		while ( $n < $max && ( microtime( true ) - $t0 ) < $budget ) {
			$q = self::queue();
			if ( ! $q ) {
				break;
			}
			$id = array_shift( $q );
			update_option( self::QUEUE, $q, false );
			$r = self::process( $id, $job && 'all' === $job['mode'] );
			if ( $job ) {
				$job['done']++;
				if ( 'error' === $r['result'] ) {
					$job['errors'][] = array( 'id' => $id, 'message' => $r['message'] );
					$job['errors']   = array_slice( $job['errors'], -50 );
				} else {
					$job[ $r['result'] ] = ( $job[ $r['result'] ] ?? 0 ) + 1;
				}
			}
			$n++;
		}
		if ( $job ) {
			if ( ! self::queue_size() && empty( $job['finished_at'] ) ) {
				$job['finished_at'] = time();
			}
			update_option( self::JOB, $job, false );
		}
		if ( $n ) {
			NFA_AIS_Index::bump();
		}
		delete_transient( self::LOCK );
		return self::job();
	}

	/* ---------- 1件の処理 ---------- */

	/**
	 * 検索用のテキスト（タイトル・抜粋・本文・許可したカスタムフィールド）。
	 * ショートコード（お問い合わせフォームなど）は取り除く。
	 */
	public static function text_of( $post ) {
		$post  = get_post( $post );
		$parts = array( get_the_title( $post ), NFA_AIS_Text::plain( $post->post_excerpt ), NFA_AIS_Text::plain( $post->post_content ) );
		foreach ( (array) NFA_AIS_Settings::value( 'meta_keys' ) as $key ) {
			foreach ( (array) get_post_meta( $post->ID, $key, false ) as $v ) {
				if ( is_scalar( $v ) ) {
					$parts[] = NFA_AIS_Text::plain( (string) $v );
				}
			}
		}
		return trim( implode( "\n", array_filter( $parts, 'strlen' ) ) );
	}

	/**
	 * @return array{result:string, message:string} result = indexed | unchanged | removed | error
	 */
	public static function process( $post_id, $force = false ) {
		global $wpdb;
		$table = self::table();
		$post  = get_post( $post_id );

		if ( ! $post || ! NFA_AIS_Eligibility::is_eligible( $post ) ) {
			$had = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $table WHERE post_id = %d", $post_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			if ( $had ) {
				do_action( 'nfa_ais_document_removed', (int) $post_id );
				$wpdb->delete( $table, array( 'post_id' => $post_id ), array( '%d' ) );
			}
			return array( 'result' => 'removed', 'message' => '' );
		}

		$text = self::text_of( $post );
		$hash = md5( $text );
		$row  = $wpdb->get_row( $wpdb->prepare( "SELECT content_hash, status FROM $table WHERE post_id = %d", $post_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$now  = current_time( 'mysql', true );
		$data = array(
			'post_id'         => $post_id,
			'content_hash'    => $hash,
			'source_modified' => $post->post_modified_gmt,
			'indexed_at'      => $now,
			'status'          => 'ok',
			'message'         => '',
		);

		// 本文が変わっていなければ、段階2の処理（API費用がかかる）は飛ばして記録だけ更新する
		if ( ! $force && $row && 'ok' === $row->status && $row->content_hash === $hash ) {
			$wpdb->replace( $table, $data );
			return array( 'result' => 'unchanged', 'message' => '' );
		}

		try {
			do_action( 'nfa_ais_document_changed', (int) $post_id, $text );
		} catch ( Throwable $e ) {
			$data['status']  = 'error';
			$data['message'] = mb_substr( $e->getMessage(), 0, 250 );
			$wpdb->replace( $table, $data );
			return array( 'result' => 'error', 'message' => $data['message'] );
		}
		$wpdb->replace( $table, $data );
		return array( 'result' => 'indexed', 'message' => '' );
	}
}
