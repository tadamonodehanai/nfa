<?php
/**
 * WP-CLI コマンド（wp nfa …）。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

/**
 * NFA AI Search の管理コマンド。
 */
class NFA_AIS_CLI {


	/**
	 * 状態を表示する。
	 *
	 * ## EXAMPLES
	 *     wp nfa status
	 */
	public function status() {
		WP_CLI::log( '検索対象の件数 : ' . count( NFA_AIS_Eligibility::eligible_ids() ) );
		WP_CLI::log( '対象の投稿タイプ: ' . implode( ', ', NFA_AIS_Settings::post_types() ) );
		WP_CLI::log( '探索画面       : ' . ( NFA_AIS_Frontend::search_page_url() ? NFA_AIS_Frontend::search_page_url() : '未設定' ) );
		WP_CLI::log( 'APIキー        : ' . ( NFA_AIS_Settings::has_api_key() ? '設定済み' : '未設定（ルールで動作）' ) );
		WP_CLI::log( 'AI利用可        : ' . ( NFA_AIS_Claude::available() ? 'はい' : 'いいえ' ) . '（今日 ' . NFA_AIS_Claude::today_count() . ' 回）' );
		WP_CLI::log( '索引バージョン   : ' . NFA_AIS_Index::version() );
	}

	/**
	 * 探索APIと同じ処理を実行して結果を表示する。
	 *
	 * ## OPTIONS
	 *
	 * [<text>]
	 * : 入力文
	 *
	 * [--via=<via>]
	 * : text（入力文を読み取る）| set（--c をそのまま使う）
	 * ---
	 * default: text
	 * ---
	 *
	 * [--c=<c>]
	 * : いまの条件。例：t=cat-4,p=schedule,r=北信,join=1,kw=トレセン
	 *
	 * [--json]
	 * : JSON で全体を出力する
	 *
	 * ## EXAMPLES
	 *     wp nfa search "小学生の大会はいつありますか？"
	 *     wp nfa search --via=set --c="t=technical,p=course"
	 */
	public function search( $args, $assoc ) {
		$c = self::parse_c( $assoc['c'] ?? '' );
		$res = NFA_AIS_Search::run( $args[0] ?? '', $assoc['via'] ?? 'text', $c );
		if ( isset( $assoc['json'] ) ) {
			WP_CLI::log( wp_json_encode( $res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
			return;
		}
		$fmt = fn( $c ) => implode( ' / ', array_filter( array( $c['t'] ? 't=' . $c['t'][0] : '', $c['p'] ? 'p=' . $c['p'][0] : '', $c['r'] ? 'r=' . $c['r'][0] : '', $c['join'] ? 'join' : '', $c['kw'] ? 'kw=' . $c['kw'] : '' ) ) );
		WP_CLI::log( '条件 : ' . ( $fmt( $res['c'] ) ? $fmt( $res['c'] ) : '（なし）' ) . '  [' . $res['parser'] . ']' . ( $res['unread'] ? '  読み取れず' : '' ) );
		WP_CLI::log( '件数 : ' . $res['total'] . '（資料 ' . $res['npdf'] . '・記事 ' . $res['nnews'] . '）' . ( $res['relaxed'] ? '  ※「' . $res['relaxed'] . '」をゆるめた結果' : '' ) );
		foreach ( array_slice( $res['docs'], 0, 8 ) as $d ) {
			WP_CLI::log( '  ' . $d['d'] . '  [' . $d['k'] . '] ' . ( isset( $d['g'] ) ? $d['g'] . ' ／ ' : '' ) . $d['t'] );
		}
	}

	private static function parse_c( $str ) {
		$c = array();
		foreach ( array_filter( explode( ',', (string) $str ) ) as $pair ) {
			$kv = array_map( 'trim', explode( '=', $pair, 2 ) );
			if ( 2 !== count( $kv ) ) {
				continue;
			}
			if ( in_array( $kv[0], array( 't', 'p', 'r' ), true ) ) {
				$c[ $kv[0] ] = array( $kv[1] );
			} else {
				$c[ $kv[0] ] = $kv[1];
			}
		}
		return $c;
	}

	/**
	 * 差分を取り込む（--all で全件を作り直す）。時間制限なしで最後まで実行する。
	 *
	 * 管理画面「設定 → AI検索 → 取り込み」のボタンと同じ処理。手元（wp-env）で一括実行するときに使う。
	 *
	 * ## OPTIONS
	 *
	 * [--all]
	 * : 変更がなくても全件を処理し直す
	 *
	 * ## EXAMPLES
	 *     wp nfa reindex
	 *     wp nfa reindex --all
	 */
	public function reindex( $args, $assoc ) {
		$job = NFA_AIS_Ingest::start( isset( $assoc['all'] ) ? 'all' : 'diff' );
		if ( ! $job['total'] ) {
			WP_CLI::success( '差分はありません。' );
			return;
		}
		$bar = \WP_CLI\Utils\make_progress_bar( '取り込み', $job['total'] );
		$done = 0;
		while ( NFA_AIS_Ingest::queue_size() ) {
			delete_transient( NFA_AIS_Ingest::LOCK );
			$j = NFA_AIS_Ingest::step( 200, 60 );
			$bar->tick( $j['done'] - $done );
			$done = $j['done'];
		}
		$bar->finish();
		$j = NFA_AIS_Ingest::job();
		WP_CLI::log( sprintf( '取り込み %d・変更なし %d・削除 %d・エラー %d', $j['indexed'], $j['unchanged'], $j['removed'], count( $j['errors'] ) ) );
		foreach ( $j['errors'] as $e ) {
			WP_CLI::warning( 'ID ' . $e['id'] . '：' . $e['message'] );
		}
		WP_CLI::success( '完了しました。' );
	}

	/**
	 * 取り込みの状態（差分の件数）を表示する。
	 *
	 * ## EXAMPLES
	 *     wp nfa ingest-status
	 *
	 * @subcommand ingest-status
	 */
	public function ingest_status() {
		$c = NFA_AIS_Rest::ingest_status( true )['counts'];
		$labels = array( 'eligible' => '検索対象', 'indexed' => '取り込み済み', 'new' => '未取り込み', 'changed' => '更新あり', 'removed' => '削除待ち', 'errors' => 'エラー', 'unclassified' => '分類が未設定' );
		foreach ( $labels as $k => $l ) {
			WP_CLI::log( sprintf( '%-12s %5d', $l, $c[ $k ] ) );
		}
		WP_CLI::log( '待ち行列     ' . sprintf( '%5d', NFA_AIS_Ingest::queue_size() ) );
	}

	/**
	 * 索引のキャッシュを消して作り直させる。
	 *
	 * ## EXAMPLES
	 *     wp nfa flush
	 */
	public function flush() {
		NFA_AIS_Index::flush();
		WP_CLI::success( 'キャッシュを削除しました。' );
	}

	/**
	 * サンプル記事を使って、探索の主な流れと除外ルールを確認する。
	 *
	 * ## EXAMPLES
	 *     wp nfa selftest
	 */
	public function selftest() {
		$fail  = 0;
		$check = function ( $label, $ok, $detail = '' ) use ( &$fail ) {
			if ( $ok ) {
				WP_CLI::log( "  ok   $label" );
			} else {
				$fail++;
				WP_CLI::log( "  FAIL $label  $detail" );
			}
		};
		$titles = fn( $r ) => array_column( $r['results'], 'title' );
		$cond   = fn( $r ) => (array) $r['cond'];

		$run_text = fn( $q, $c = array() ) => NFA_AIS_Search::run( $q, 'text', $c );
		$C        = fn( $r ) => $r['c'];

		WP_CLI::log( '1. 例「小学生の大会はいつありますか？」→ 対象と目的を読み取り、大会資料が出る' );
		$r = $run_text( '小学生の大会はいつありますか？' );
		$check( '条件 = cat-4 / schedule', array( 'cat-4' ) === $C( $r )['t'] && array( 'schedule' ) === $C( $r )['p'], wp_json_encode( $C( $r ) ) );
		// 大会・リーグ資料がまだ1件もないあいだは確かめない
		$has_doc = post_type_exists( 'nfa_doc' ) && (int) wp_count_posts( 'nfa_doc' )->publish > 0;
		$check( '資料（pdf）がある' . ( $has_doc ? '' : '（大会資料が未登録のため省略）' ), ! $has_doc || $r['npdf'] > 0, (string) $r['npdf'] );
		$check( '新しい順', ! $r['docs'] || count( $r['docs'] ) < 2 || $r['docs'][0]['d'] >= $r['docs'][1]['d'] );

		WP_CLI::log( '2. 例「審判の資格を更新したい」' );
		$r = $run_text( '審判の資格を更新したい' );
		$check( '条件 = referee / course', array( 'referee' ) === $C( $r )['t'] && array( 'course' ) === $C( $r )['p'], wp_json_encode( $C( $r ) ) );

		WP_CLI::log( '3. 例「女子のチームに入りたい」→ チーム探し' );
		$r = $run_text( '女子のチームに入りたい' );
		$check( 'join・目的なし・cat-w', $C( $r )['join'] && ! $C( $r )['p'] && array( 'cat-w' ) === $C( $r )['t'], wp_json_encode( $C( $r ) ) );

		WP_CLI::log( '4. 例「指導者の講習会を探している」→ 続けて「北信だけ」' );
		$r  = $run_text( '指導者の講習会を探している' );
		$check( '条件 = technical / course', array( 'technical' ) === $C( $r )['t'] && array( 'course' ) === $C( $r )['p'], wp_json_encode( $C( $r ) ) );
		$r2 = $run_text( '東信だけ', $C( $r ) );
		$check( '条件が重なる（地域が足される）', array( 'technical' ) === $C( $r2 )['t'] && array( '東信' ) === $C( $r2 )['r'], wp_json_encode( $C( $r2 ) ) );

		WP_CLI::log( '5. 読み取れない言葉' );
		$r = $run_text( 'zzzzqqqq', array( 't' => array( 'cat-w' ) ) );
		$check( 'unread・いまの条件は変わらない', $r['unread'] && array( 'cat-w' ) === $C( $r )['t'] );

		WP_CLI::log( '6. 0件：目的をゆるめる' );
		$found = false;
		foreach ( array( 'cat-f', 'cat-s', 'medical', 'para' ) as $t ) {
			foreach ( array( 'course', 'reg', 'event' ) as $p ) {
				$r = NFA_AIS_Search::run( '', 'set', array( 't' => array( $t ), 'p' => array( $p ) ) );
				if ( 0 === $r['total'] && $r['docs'] ) {
					$found = true;
					$check( "{$t}×{$p} は0件 → 目的をゆるめた結果を返す", 'p' === $r['relaxed'] && ! $r['st']['p'] );
					break 2;
				}
			}
		}
		if ( ! $found ) {
			WP_CLI::log( '  （0件になる組み合わせがサンプルにないため省略）' );
		}

		WP_CLI::log( '7. 件数：パネル・候補タグ用' );
		$r = NFA_AIS_Search::run( '', 'set', array( 't' => array( 'cat-4' ) ) );
		$check( '対象を cat-4 に置いたときの件数 = いまの件数', $r['counts']['t']['cat-4'] === $r['total'], $r['counts']['t']['cat-4'] . ' / ' . $r['total'] );
		$sum = array_sum( $r['counts']['p'] );
		$check( '目的ごとの件数が出る', $sum > 0 );

		WP_CLI::log( '8. 担当委員会（バッジ）と記事のリンク' );
		$news = array_values( array_filter( $r['docs'], fn( $d ) => 'news' === $d['k'] ) );
		$check( '記事にバッジとURLがある', ! $news || ( '' !== $news[0]['l'] && 0 === strpos( $news[0]['u'], home_url() ) ) );

		WP_CLI::log( '9. 除外ルール（お問い合わせ・下書き・非公開・探索画面は出ない）' );
		// 「お問い合わせ」ページそのもの（題名が一致）と、テスト用の下書き・非公開の記事が出ないこと。
		// 「よくあるお問い合わせ」のような普通の記事は出てよい
		$forbidden = array( '個人情報テスト', '内部メモ' );
		foreach ( array( 'お問い合わせ', '個人情報テスト', '内部メモ', '山田太郎', 'フォーム' ) as $q ) {
			$r   = NFA_AIS_Search::run( '', 'set', array( 'kw' => $q ) );
			$hit = array_filter( array_column( $r['docs'], 't' ), fn( $t ) => 'お問い合わせ' === $t || (bool) array_filter( $forbidden, fn( $f ) => false !== mb_strpos( $t, $f ) ) );
			$check( "「{$q}」に除外対象が出ない", ! $hit, implode( ',', $hit ) );
		}
		$cf = get_page_by_path( 'contact' );
		if ( $cf ) {
			$check( 'お問い合わせページは is_eligible = false', ! NFA_AIS_Eligibility::is_eligible( $cf ) );
		}
		$sp = get_page_by_path( 'search' );
		if ( $sp ) {
			$check( '探索画面のページは is_eligible = false', ! NFA_AIS_Eligibility::is_eligible( $sp ) );
		}

		WP_CLI::log( '10. 伏せ字' );
		$m = NFA_AIS_Text::mask_personal( '山田 taro@example.com 090-1234-5678 に連絡' );
		$check( 'メール・電話番号が伏せ字になる', false === strpos( $m, 'example.com' ) && false === strpos( $m, '5678' ), $m );

		WP_CLI::log( '11. 条件をすべて外す' );
		$r = NFA_AIS_Search::run( '', 'set', array() );
		$check( '0件・docs なし', 0 === $r['total'] && ! $r['docs'] );

		WP_CLI::log( '13. 差分の再取り込み' );
		$draft = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'selftest 取り込み確認', 'post_content' => '最初の本文', 'meta_input' => array( '_nfa_demo' => 1 ) ) );
		NFA_AIS_Ingest::cancel();
		$run = function ( $mode = 'diff' ) {
			NFA_AIS_Ingest::start( $mode );
			while ( NFA_AIS_Ingest::queue_size() ) {
				delete_transient( NFA_AIS_Ingest::LOCK );
				NFA_AIS_Ingest::step( 500, 60 );
			}
			return NFA_AIS_Ingest::job();
		};
		$run();
		$s = NFA_AIS_Ingest::scan();
		$check( '取り込み後は差分なし', ! $s['new'] && ! $s['changed'] && ! $s['removed'], wp_json_encode( array( count( $s['new'] ), count( $s['changed'] ), count( $s['removed'] ) ) ) );
		$check( '取り込み済み = 検索対象', $s['indexed'] === $s['eligible'], $s['indexed'] . ' / ' . $s['eligible'] );

		global $wpdb;
		// 更新日時だけ進める（本文は同じ）→「更新あり」になり、処理は「変更なし」で済む
		$wpdb->update( $wpdb->posts, array( 'post_modified_gmt' => gmdate( 'Y-m-d H:i:s', time() + 60 ) ), array( 'ID' => $draft ) );
		clean_post_cache( $draft );
		$s = NFA_AIS_Ingest::scan();
		$check( '更新日時が進んだ記事は「更新あり」', array( $draft ) === $s['changed'] );
		$j = $run();
		$check( '本文が同じなら「変更なし」（段階2の処理を呼ばない）', 1 === $j['unchanged'] && 0 === $j['indexed'] );

		// 本文を変える → 段階2の差し込み口が呼ばれる
		$called = array();
		$hook   = function ( $id, $text ) use ( &$called ) { $called[] = array( $id, $text ); };
		add_action( 'nfa_ais_document_changed', $hook, 10, 2 );
		$wpdb->update( $wpdb->posts, array( 'post_content' => '書き換えた本文 [gallery ids="1"]', 'post_modified_gmt' => gmdate( 'Y-m-d H:i:s', time() + 120 ) ), array( 'ID' => $draft ) );
		clean_post_cache( $draft );
		$run();
		$check( '本文が変わると取り込み処理が呼ばれる', 1 === count( $called ) && $draft === $called[0][0] && false !== strpos( $called[0][1], '書き換えた本文' ) );
		$check( '取り込むテキストにショートコードは含めない', $called && false === strpos( $called[0][1], 'gallery' ) );
		remove_action( 'nfa_ais_document_changed', $hook, 10 );

		// 除外にする → 削除待ち → 記録から消える
		$removed = array();
		$hook2   = function ( $id ) use ( &$removed ) { $removed[] = $id; };
		add_action( 'nfa_ais_document_removed', $hook2 );
		update_post_meta( $draft, NFA_AIS_Eligibility::META_EXCLUDE, true );
		NFA_AIS_Index::bump();
		$check( '除外にすると「削除待ち」', array( $draft ) === NFA_AIS_Ingest::scan()['removed'] );
		$run();
		$check( '削除の差し込み口が呼ばれ、記録から消える', array( $draft ) === $removed && ! NFA_AIS_Ingest::scan()['removed'] );
		remove_action( 'nfa_ais_document_removed', $hook2 );

		// エラーは記録され、次回やり直す
		delete_post_meta( $draft, NFA_AIS_Eligibility::META_EXCLUDE );
		NFA_AIS_Index::bump();
		$fail_hook = function () { throw new RuntimeException( 'テスト用のエラー' ); };
		add_action( 'nfa_ais_document_changed', $fail_hook );
		$j = $run();
		remove_action( 'nfa_ais_document_changed', $fail_hook );
		$check( 'エラーは記録される', 1 === count( $j['errors'] ) && 'テスト用のエラー' === $j['errors'][0]['message'] );
		$check( 'エラーの記事は次回の差分に入る', in_array( $draft, NFA_AIS_Ingest::scan()['changed'], true ) );
		$run();
		$check( 'やり直すと解消', ! NFA_AIS_Ingest::scan()['changed'] );

		wp_delete_post( $draft, true );
		$run();

		if ( $fail ) {
			WP_CLI::error( "$fail 件の確認に失敗しました。" );
		}
		WP_CLI::success( 'すべての確認に通りました。' );
	}
}

WP_CLI::add_command( 'nfa', 'NFA_AIS_CLI' );
