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

	const SAMPLE_META = '_nfa_ais_sample';

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
	 * : text（入力文を読み取る）| set（--cond をそのまま使う）
	 * ---
	 * default: text
	 * ---
	 *
	 * [--cond=<cond>]
	 * : いまの条件。例：target=小学生（4種）,purpose=大会日程を知る,keyword=トレセン
	 *
	 * [--json]
	 * : JSON で全体を出力する
	 *
	 * ## EXAMPLES
	 *     wp nfa search "小学生の大会っていつ？"
	 *     wp nfa search --via=set --cond="target=指導者,purpose=講習会に参加"
	 */
	public function search( $args, $assoc ) {
		$cond = array();
		foreach ( array_filter( explode( ',', $assoc['cond'] ?? '' ) ) as $pair ) {
			$kv = array_map( 'trim', explode( '=', $pair, 2 ) );
			if ( 2 === count( $kv ) ) {
				$cond[ $kv[0] ] = $kv[1];
			}
		}
		$res = NFA_AIS_Search::run( $args[0] ?? '', $assoc['via'] ?? 'text', $cond );
		if ( isset( $assoc['json'] ) ) {
			WP_CLI::log( wp_json_encode( $res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
			return;
		}
		$c = (array) $res['cond'];
		WP_CLI::log( '条件 : ' . ( $c ? implode( ' / ', array_map( fn( $k, $v ) => "$k=$v", array_keys( $c ), $c ) ) : '（なし）' ) . '  [' . $res['parser'] . ']' );
		WP_CLI::log( 'AI   : ' . wp_strip_all_tags( $res['say'] ) );
		foreach ( $res['results'] as $r ) {
			WP_CLI::log( '  ' . $r['date'] . '  ' . $r['title'] . '  [' . implode( ' / ', $r['meta'] ) . ']' );
		}
		if ( $res['more'] ) {
			WP_CLI::log( '  → この条件の' . $res['more']['total'] . '件をすべて見る：' . $res['more']['url'] );
		}
		if ( $res['next'] ) {
			WP_CLI::log( 'さらに絞り込むなら（' . $res['next']['name'] . '）: ' . implode( '  ', array_map( fn( $o ) => $o['value'] . '(' . $o['n'] . ')', $res['next']['options'] ) ) );
		}
		if ( $res['rescue'] ) {
			WP_CLI::log( 'ひとつ条件を外すと: ' . implode( '  ', array_map( fn( $r ) => '「' . $r['value'] . '」を外す', $res['rescue'] ) ) );
		}
		if ( $res['popular'] ) {
			WP_CLI::log( 'よく選ばれている条件: ' . implode( '  ', $res['popular'] ) );
		}
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
	 * 動作確認用のサンプル記事を作成（または削除）する。
	 *
	 * サンプル記事に加えて、除外ルールの確認用に「お問い合わせ」ページ（Contact Form 7 のショートコード入り）、
	 * 非公開の下書き、探索画面のページも作る。
	 *
	 * ## OPTIONS
	 *
	 * [--delete]
	 * : 作成したサンプルを削除する
	 *
	 * ## EXAMPLES
	 *     wp nfa sample
	 *     wp nfa sample --delete
	 */
	public function sample( $args, $assoc ) {
		$existing = get_posts(
			array(
				'post_type'      => 'any',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => self::SAMPLE_META, // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		if ( isset( $assoc['delete'] ) ) {
			foreach ( $existing as $id ) {
				wp_delete_post( $id, true );
			}
			NFA_AIS_Index::bump();
			WP_CLI::success( count( $existing ) . ' 件のサンプルを削除しました。' );
			return;
		}
		if ( $existing ) {
			WP_CLI::error( 'サンプルはすでにあります。作り直すときは先に `wp nfa sample --delete` を実行してください。' );
		}
		NFA_AIS_Taxonomies::seed_terms();
		$items = require NFA_AIS_DIR . 'includes/sample-data.php';
		foreach ( $items as $it ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'post',
					'post_status'  => 'publish',
					'post_title'   => $it['t'],
					'post_content' => "<!-- wp:paragraph -->\n<p>" . esc_html( $it['t'] ) . "の詳細です（サンプル）。</p>\n<!-- /wp:paragraph -->",
					'post_date'    => $it['d'] . ' 10:00:00',
					'meta_input'   => array( self::SAMPLE_META => 1 ),
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				WP_CLI::warning( $it['t'] . ': ' . $id->get_error_message() );
				continue;
			}
			wp_set_object_terms( $id, $it['target'], 'nfa_target' );
			wp_set_object_terms( $id, $it['purpose'], 'nfa_purpose' );
			wp_set_object_terms( $id, $it['area'], 'nfa_area' );
			wp_set_object_terms( $id, $it['committee'], NFA_AIS_Taxonomies::COMMITTEE );
		}

		// 除外ルールの確認用
		$extras = array(
			array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'contact', 'post_title' => 'お問い合わせ', 'post_content' => "お問い合わせは下のフォームからどうぞ。小学生の大会や審判についてのご質問も受け付けます。\n\n[contact-form-7 id=\"1\" title=\"お問い合わせ\"]" ),
			array( 'post_type' => 'post', 'post_status' => 'draft', 'post_title' => '【下書き】小学生 大会 審判 個人情報テスト', 'post_content' => '山田太郎 090-1234-5678 taro@example.com' ),
			array( 'post_type' => 'post', 'post_status' => 'private', 'post_title' => '【非公開】小学生 大会 審判 内部メモ', 'post_content' => '非公開のメモ' ),
			array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'search', 'post_title' => '情報をさがす', 'post_content' => '[nfa_ai_search]' ),
		);
		foreach ( $extras as $p ) {
			$p['meta_input'] = array( self::SAMPLE_META => 1 );
			$id              = wp_insert_post( $p );
			// 下書き・非公開にも分類を付けておく（除外が効いていなければ検索に出てしまう状態で確かめる）
			if ( $id && 'post' === $p['post_type'] ) {
				wp_set_object_terms( $id, '小学生（4種）', 'nfa_target' );
				wp_set_object_terms( $id, '大会日程を知る', 'nfa_purpose' );
				wp_set_object_terms( $id, '県全域', 'nfa_area' );
			}
		}
		NFA_AIS_Index::bump();
		WP_CLI::success( count( $items ) . ' 件のサンプル記事と、確認用のページを作成しました。探索画面：' . NFA_AIS_Frontend::search_page_url() );
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

		WP_CLI::log( '1. 例「小学生の大会っていつ？」→ 対象と目的を読み取り、1件' );
		$r = NFA_AIS_Search::run( '小学生の大会っていつ？', 'text', array() );
		$check( '条件 = 小学生（4種）/ 大会日程を知る', array( 'target' => '小学生（4種）', 'purpose' => '大会日程を知る' ) === $cond( $r ), wp_json_encode( $cond( $r ), JSON_UNESCAPED_UNICODE ) );
		$check( '1件', 1 === $r['total'], (string) $r['total'] );
		$check( '返事が「〜ですね。1件の情報があります。」', false !== strpos( $r['say'], 'ですね。<b>1件</b>の情報があります' ), $r['say'] );

		WP_CLI::log( '2. 例「審判の資格更新はどうすれば？」' );
		$r = NFA_AIS_Search::run( '審判の資格更新はどうすれば？', 'text', array() );
		$check( '審判員登録の更新手続きが出る', array( '審判員登録の更新手続きについて（2026年度）' ) === $titles( $r ), implode( ',', $titles( $r ) ) );

		WP_CLI::log( '3. 例「女子のチームに入りたい」→ 2件、次は目的で絞れる' );
		$r = NFA_AIS_Search::run( '女子のチームに入りたい', 'text', array() );
		$check( '2件', 2 === $r['total'], (string) $r['total'] );
		$check( 'さらに絞り込むなら（目的）', $r['next'] && 'purpose' === $r['next']['key'] && 2 === count( $r['next']['options'] ) );

		WP_CLI::log( '4. 例「指導者の講習会を探しています」→ 2件、次は地域で絞れる' );
		$r = NFA_AIS_Search::run( '指導者の講習会を探しています', 'text', array() );
		$check( '2件', 2 === $r['total'], (string) $r['total'] );
		$check( 'さらに絞り込むなら（地域）北信・東信', $r['next'] && 'area' === $r['next']['key'] && array( '北信', '東信' ) === array_column( $r['next']['options'], 'value' ) );
		$r2 = NFA_AIS_Search::run( '東信だと？', 'text', $cond( $r ) );
		$check( '続けて「東信だと？」→ 条件が累積して1件', 1 === $r2['total'] && '東信' === ( $cond( $r2 )['area'] ?? '' ), wp_json_encode( $cond( $r2 ), JSON_UNESCAPED_UNICODE ) );

		WP_CLI::log( '5. 読み取れない言葉' );
		$r = NFA_AIS_Search::run( 'zzzzqqqq', 'text', array( 'target' => '女子' ) );
		$check( 'unread・よく選ばれている条件', $r['unread'] && $r['popular'] );
		$check( 'いまの条件は変わらない', array( 'target' => '女子' ) === $cond( $r ) );

		WP_CLI::log( '6. 0件：ひとつ条件を外す提案' );
		$r = NFA_AIS_Search::run( '', 'set', array( 'target' => 'シニア', 'purpose' => '試合結果を見る' ) );
		$check( '0件で「外す」が2つ', 0 === $r['total'] && 2 === count( $r['rescue'] ) );

		WP_CLI::log( '7. パネル：0件になる選択肢は押せない' );
		$r    = NFA_AIS_Search::run( '', 'set', array( 'target' => '審判' ) );
		$opts = array_column( array_values( array_filter( $r['facets'], fn( $f ) => 'purpose' === $f['key'] ) )[0]['options'], null, 'value' );
		$check( '審判×試合結果を見る は dead', $opts['試合結果を見る']['dead'] && 0 === $opts['試合結果を見る']['n'] );
		$check( '審判×講習会に参加 は 1件', 1 === $opts['講習会に参加']['n'] && ! $opts['講習会に参加']['dead'] );

		WP_CLI::log( '8. 6件以上は「すべて見る」' );
		$r = NFA_AIS_Search::run( '', 'set', array( 'area' => '県全域' ) );
		$check( '結果は5件まで＋すべて見るリンク', 5 === count( $r['results'] ) && $r['more'] && $r['more']['total'] > 5 );
		$check( '新しい順', $r['results'][0]['date'] >= $r['results'][1]['date'] );

		WP_CLI::log( '9. 担当部署（発信元）は公開側に出さない' );
		$all = wp_json_encode( NFA_AIS_Search::run( '', 'set', array( 'area' => '県全域' ) ), JSON_UNESCAPED_UNICODE );
		$check( '応答に委員会名が含まれない', false === strpos( $all, '委員会' ) );

		WP_CLI::log( '10. 除外ルール（お問い合わせ・下書き・非公開・探索画面は出ない）' );
		$forbidden = array( 'お問い合わせ', '個人情報テスト', '内部メモ', '情報をさがす' );
		foreach ( array( 'お問い合わせ', '個人情報テスト', '内部メモ', '情報をさがす', '山田太郎', '小学生の大会' ) as $q ) {
			foreach ( array( NFA_AIS_Search::run( $q, 'text', array() ), NFA_AIS_Search::run( '', 'set', array( 'keyword' => $q ) ) ) as $r ) {
				$hit = array_filter( $titles( $r ), fn( $t ) => (bool) array_filter( $forbidden, fn( $f ) => false !== mb_strpos( $t, $f ) ) );
				$check( "「{$q}」に除外対象が出ない", ! $hit, implode( ',', $hit ) );
			}
		}
		$cf = get_page_by_path( 'contact' );
		$check( 'お問い合わせページは is_eligible = false', $cf && ! NFA_AIS_Eligibility::is_eligible( $cf ) );

		WP_CLI::log( '11. 伏せ字' );
		$m = NFA_AIS_Text::mask_personal( '山田 taro@example.com 090-1234-5678 に連絡' );
		$check( 'メール・電話番号が伏せ字になる', false === strpos( $m, 'example.com' ) && false === strpos( $m, '5678' ), $m );

		WP_CLI::log( '12. 条件をすべて外す' );
		$r = NFA_AIS_Search::run( '', 'set', array() );
		$check( '仕切り直しの案内', false !== strpos( $r['say'], '条件をすべて外しました' ) && 0 === $r['total'] );

		WP_CLI::log( '13. 差分の再取り込み' );
		$draft = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'selftest 取り込み確認', 'post_content' => '最初の本文', 'meta_input' => array( self::SAMPLE_META => 1 ) ) );
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
