<?php
/**
 * WP-CLI：wp nfa-committee …
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

/**
 * 委員会の運用の管理コマンド。
 */
class NFA_Com_CLI {

	const DEMO_META = '_nfa_demo';

	/** 既存サイトのバッジ（委員会ラベル）→ 担当委員会のスラッグ */
	const LABEL_TO_COMMITTEE = array(
		'4種'     => 'cat-4',
		'3種'     => 'cat-3',
		'2種'     => 'cat-2',
		'1種'     => 'cat-1',
		'女子'    => 'cat-w',
		'フットサル' => 'cat-f',
		'シニア'   => 'cat-s',
		'グラスルーツ' => 'grassroots',
		'審判'    => 'referee',
		'技術'    => 'technical',
		'医学'    => 'medical',
		'協会'    => 'office',
		'重要'    => 'office',
	);

	/**
	 * サイトに必要な固定ページを用意する（あれば何もしない）。
	 *
	 * TOP（front-page）、お知らせ一覧（home）、協会について・はじめる・育成・強化・関わる・お問い合わせ・
	 * プライバシー・チームを探す・情報を探す（[nfa_ai_search]）。Contact Form 7 があればフォームも作る。
	 *
	 * ## EXAMPLES
	 *     wp nfa-committee setup-pages
	 *
	 * @subcommand setup-pages
	 */
	public function setup_pages() {
		$pages = array(
			'home'    => array( 'TOP', '' ),
			'news'    => array( 'お知らせ', '' ),
			'about'   => array( '協会について', '' ),
			'start'   => array( 'はじめる', '' ),
			'grow'    => array( '育成・強化', '' ),
			'partner' => array( '関わる（協賛のご案内）', '' ),
			'contact' => array( 'お問い合わせ', '' ),
			'privacy' => array( 'プライバシーポリシー', '' ),
			'teams'   => array( 'チームを探す', '' ),
			'search'  => array( '情報を探す', '[nfa_ai_search]' ),
		);
		$ids = array();
		foreach ( $pages as $slug => $p ) {
			$page = get_page_by_path( $slug );
			if ( ! $page ) {
				$ids[ $slug ] = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $p[0], 'post_content' => $p[1] ) );
				WP_CLI::log( '固定ページを作成：' . $p[0] . '（/' . $slug . '/）' );
			} else {
				$ids[ $slug ] = $page->ID;
			}
		}
		// AI検索の対象外：フォームをテンプレートに埋め込むお問い合わせ、探索画面そのもの、中身のない TOP・お知らせ一覧
		foreach ( array( 'contact', 'search', 'home', 'news' ) as $slug ) {
			update_post_meta( $ids[ $slug ], '_nfa_ais_exclude', true );
		}
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
		update_option( 'page_for_posts', $ids['news'] );

		// お問い合わせ：Contact Form 7 のフォーム（デザインと同じ項目・クラス名）
		if ( class_exists( 'WPCF7_ContactForm' ) ) {
			$exists = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'title' => 'お問い合わせ', 'posts_per_page' => 1, 'post_status' => 'any' ) );
			if ( ! $exists ) {
				$file = get_theme_file_path( 'inc/contact-form.txt' );
				$form = WPCF7_ContactForm::get_template( array( 'title' => 'お問い合わせ' ) );
				if ( is_readable( $file ) ) {
					$form->set_properties( array( 'form' => file_get_contents( $file ) ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				}
				$form->save();
				WP_CLI::log( 'Contact Form 7 のフォーム「お問い合わせ」を作成しました。' );
			}
		}
		flush_rewrite_rules();
		WP_CLI::success( '固定ページを用意しました。TOP と お知らせ一覧 を表示設定に割り当てました。' );
	}

	/**
	 * デザインの実データ（既存サイトから収集した526件）を、お知らせ（投稿）と大会・リーグ資料（nfa_doc）として取り込む。開発用。
	 *
	 * お知らせ 337件 → 投稿。担当委員会・対象・目的・地域を付け、本文に既存サイトの記事へのリンクを入れる。
	 * 資料 189件 → 大会名ごとにまとめて nfa_doc（1大会＝1投稿、資料は行）。
	 * あわせて、除外ルールの確認用に下書き・非公開の記事を作る。
	 *
	 * ## OPTIONS
	 *
	 * [--delete]
	 * : 取り込んだデモデータを削除する
	 *
	 * ## EXAMPLES
	 *     wp nfa-committee import-demo
	 *     wp nfa-committee import-demo --delete
	 *
	 * @subcommand import-demo
	 */
	public function import_demo( $args, $assoc ) {
		$existing = get_posts( array( 'post_type' => array( 'post', 'page', NFA_Com_Doc_Type::TYPE ), 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => self::DEMO_META ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		if ( isset( $assoc['delete'] ) ) {
			foreach ( $existing as $id ) {
				wp_delete_post( $id, true );
			}
			WP_CLI::success( count( $existing ) . ' 件のデモデータを削除しました。' );
			return;
		}
		if ( $existing ) {
			WP_CLI::error( 'デモデータはすでにあります。作り直すときは先に --delete を実行してください。' );
		}
		$raw  = file_get_contents( NFA_COM_DIR . 'demo/search-data.js' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$json = substr( $raw, strpos( $raw, '[' ), strrpos( $raw, ']' ) - strpos( $raw, '[' ) + 1 );
		$docs = json_decode( $json, true );
		if ( ! is_array( $docs ) ) {
			WP_CLI::error( 'demo/search-data.js を読み取れません。' );
		}
		wp_defer_term_counting( true );
		$committee_of = function ( $d ) {
			if ( ! empty( $d['l'] ) && isset( self::LABEL_TO_COMMITTEE[ $d['l'] ] ) ) {
				return self::LABEL_TO_COMMITTEE[ $d['l'] ];
			}
			foreach ( (array) $d['c'] as $c ) {
				if ( isset( NFA_Com_Committees::definitions()[ $c ] ) ) {
					return $c;
				}
			}
			return 'office';
		};
		$set_terms = function ( $id, $d, $committee ) {
			wp_set_object_terms( $id, $committee, NFA_Com_Committees::TAX );
			$targets = array_values( array_filter( (array) $d['c'], fn( $c ) => 'office' !== $c ) );
			if ( $targets ) {
				wp_set_object_terms( $id, $targets, 'nfa_target' );
			}
			if ( ! empty( $d['p'] ) ) {
				wp_set_object_terms( $id, (array) $d['p'], 'nfa_purpose' );
			}
			foreach ( (array) $d['r'] as $area ) {
				$t = get_term_by( 'name', $area, 'nfa_area' );
				if ( $t ) {
					wp_set_object_terms( $id, (int) $t->term_id, 'nfa_area', true );
				}
			}
		};
		$date = fn( $d ) => preg_match( '/^\d{4}\.\d{2}\.\d{2}$/', (string) $d ) ? str_replace( '.', '-', $d ) . ' 09:00:00' : '2026-04-01 09:00:00';

		// お知らせ
		$news = array_values( array_filter( $docs, fn( $d ) => 'news' === $d['k'] ) );
		$bar  = \WP_CLI\Utils\make_progress_bar( 'お知らせ', count( $news ) );
		$made = array();
		foreach ( $news as $d ) {
			$body = ( ! empty( $d['x'] ) ? '<!-- wp:paragraph --><p>' . esc_html( $d['x'] ) . '</p><!-- /wp:paragraph -->' : '' )
				. '<!-- wp:paragraph --><p>この記事は既存サイトから移行したものです（デモデータ）。詳しくは<a href="' . esc_url( $d['u'] ) . '" target="_blank" rel="noopener">既存サイトの記事</a>をご覧ください。</p><!-- /wp:paragraph -->';
			$id   = wp_insert_post(
				array(
					'post_type'    => 'post',
					'post_status'  => 'publish',
					'post_title'   => $d['t'],
					'post_content' => $body,
					'post_excerpt' => (string) ( $d['x'] ?? '' ),
					'post_date'    => $date( $d['d'] ),
					'meta_input'   => array( self::DEMO_META => 1, '_nfa_source_url' => $d['u'] ),
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				$set_terms( $id, $d, $committee_of( $d ) );
				if ( '重要' === ( $d['l'] ?? '' ) ) {
					update_post_meta( $id, NFA_Com_Flags::META['important'], true );
				}
				$made[] = $id;
			}
			$bar->tick();
		}
		$bar->finish();
		// 新しい5件を「TOPに掲載」に（ヒーロー・PICK UP の確認用）
		$latest = get_posts( array( 'post__in' => $made, 'posts_per_page' => 5, 'orderby' => 'date', 'order' => 'DESC', 'fields' => 'ids' ) );
		foreach ( $latest as $id ) {
			update_post_meta( $id, NFA_Com_Flags::META['top'], true );
		}

		// 大会・リーグ資料：大会名ごとに1投稿
		$groups = array();
		foreach ( $docs as $d ) {
			if ( 'pdf' !== $d['k'] ) {
				continue;
			}
			$key = $committee_of( $d ) . '|' . $d['g'];
			$groups[ $key ][] = $d;
		}
		$bar = \WP_CLI\Utils\make_progress_bar( '大会・リーグ資料', count( $groups ) );
		foreach ( $groups as $key => $rows ) {
			list( $committee, $title ) = explode( '|', $key, 2 );
			usort( $rows, fn( $a, $b ) => strcmp( $b['d'], $a['d'] ) );
			$latest = $rows[0]['d'] ? $rows[0]['d'] : '';
			$meta   = array(
				self::DEMO_META   => 1,
				'nfa_doc_year'    => preg_match( '/(20\d\d)/', $title . ' ' . $latest, $m ) ? (int) $m[1] : 2026,
				'_nfa_doc_year'   => 'field_nfa_doc_year',
				'nfa_doc_kind'    => false !== mb_strpos( $title, 'リーグ' ) ? 'league' : 'tournament',
				'_nfa_doc_kind'   => 'field_nfa_doc_kind',
				'nfa_doc_rows'    => count( $rows ),
				'_nfa_doc_rows'   => 'field_nfa_doc_rows',
			);
			foreach ( $rows as $i => $r ) {
				$parts = preg_split( '/\s+/u', trim( $r['t'] ), 2 );
				$meta[ "nfa_doc_rows_{$i}_section" ]  = count( $parts ) > 1 ? $parts[0] : '';
				$meta[ "nfa_doc_rows_{$i}_target" ]   = '';
				$meta[ "nfa_doc_rows_{$i}_label" ]    = count( $parts ) > 1 ? $parts[1] : $r['t'];
				$meta[ "nfa_doc_rows_{$i}_file" ]     = '';
				$meta[ "nfa_doc_rows_{$i}_url" ]      = $r['u'];
				$meta[ "nfa_doc_rows_{$i}_updated" ]  = $r['d'] ? str_replace( '.', '', $r['d'] ) : '';
				foreach ( array( 'section', 'target', 'label', 'file', 'url', 'updated' ) as $sub ) {
					$meta[ "_nfa_doc_rows_{$i}_{$sub}" ] = 'field_nfa_doc_' . $sub;
				}
			}
			$id = wp_insert_post(
				array(
					'post_type'   => NFA_Com_Doc_Type::TYPE,
					'post_status' => 'publish',
					'post_title'  => $title,
					'post_date'   => $date( $latest ),
					'meta_input'  => $meta,
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				$merged = array( 'c' => array(), 'p' => array(), 'r' => array() );
				foreach ( $rows as $r ) {
					foreach ( array( 'c', 'p', 'r' ) as $k ) {
						$merged[ $k ] = array_values( array_unique( array_merge( $merged[ $k ], (array) $r[ $k ] ) ) );
					}
				}
				$set_terms( $id, $merged, $committee );
				// 一覧の「最新の更新」に使うので、更新日時を最新の行の日付にそろえる
				if ( $latest ) {
					global $wpdb;
					$wpdb->update( $wpdb->posts, array( 'post_modified' => $date( $latest ), 'post_modified_gmt' => get_gmt_from_date( $date( $latest ) ) ), array( 'ID' => $id ) );
				}
			}
			$bar->tick();
		}
		$bar->finish();

		// 除外ルールの確認用（検索・一覧に出てはいけないもの）
		foreach ( array(
			array( 'post_status' => 'draft', 'post_title' => '【下書き】小学生 大会 審判 個人情報テスト', 'post_content' => '山田太郎 090-1234-5678 taro@example.com' ),
			array( 'post_status' => 'private', 'post_title' => '【非公開】小学生 大会 審判 内部メモ', 'post_content' => '非公開のメモ' ),
		) as $p ) {
			$id = wp_insert_post( $p + array( 'post_type' => 'post', 'meta_input' => array( self::DEMO_META => 1 ) ) );
			$set_terms( $id, array( 'c' => array( 'cat-4' ), 'p' => array( 'schedule' ), 'r' => array( '県全域' ) ), 'cat-4' );
		}
		wp_defer_term_counting( false );
		if ( class_exists( 'NFA_AIS_Index' ) ) {
			NFA_AIS_Index::bump();
		}
		WP_CLI::success( sprintf( 'お知らせ %d件、大会・リーグ資料 %d件（資料 %d行）を取り込みました。', count( $made ), count( $groups ), count( $docs ) - count( $news ) ) );
	}

	/**
	 * 旧サイトの CSV（export-post / export-news / export-team）を取り込む。
	 *
	 * - post / news → お知らせ（投稿）。旧カテゴリーは使わず、includes/legacy-map.php で
	 *   担当委員会・目的に読み替える（旧サイトの「重要」「注意喚起」「TOPに掲載」は引き継がない）。対象は委員会の初期値、地域はタイトルの地区名から。
	 *   カスタムフィールドは ACF の入力欄へ（開催日・開催日の表記・リンク先URL・「新着」掲載終了日）。
	 *   投稿IDは、空いていれば旧サイトと同じ ID にする（旧URLからの転送に使う）。
	 * - team → チーム（team）。分類（地域・競技・年代・性別）と ACF の入力欄へ。
	 * 本文中の画像・PDF とアイキャッチは旧サイトの URL のまま入る（アイキャッチは _nfa_thumb_url）。
	 * そのあと import-media でメディアライブラリに取り込む。
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : CSV ファイル
	 *
	 * --type=<type>
	 * : post | news | team
	 *
	 * [--dry-run]
	 * : 取り込まずに、読み替えの結果（件数）だけを表示する
	 *
	 * [--delete]
	 * : この種類の取り込み済みデータを削除する
	 *
	 * ## EXAMPLES
	 *     wp nfa-committee import-csv /tmp/export-post.csv --type=post --dry-run
	 *     wp nfa-committee import-csv /tmp/export-team.csv --type=team
	 *
	 * @subcommand import-csv
	 */
	public function import_csv( $args, $assoc ) {
		$type = $assoc['type'] ?? '';
		if ( ! in_array( $type, array( 'post', 'news', 'team' ), true ) ) {
			WP_CLI::error( '--type は post / news / team のどれかです。' );
		}
		$ptype = 'team' === $type ? NFA_Com_Team_Type::TYPE : 'post';
		if ( isset( $assoc['delete'] ) ) {
			$ids = get_posts( array( 'post_type' => $ptype, 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_nfa_legacy_type', 'meta_value' => $type ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			foreach ( $ids as $id ) {
				wp_delete_post( $id, true );
			}
			WP_CLI::success( count( $ids ) . ' 件を削除しました。' );
			return;
		}
		$rows = self::read_csv( $args[0] );
		WP_CLI::log( count( $rows ) . ' 行を読み込みました。' );
		if ( 'team' === $type ) {
			self::import_teams( $rows, isset( $assoc['dry-run'] ) );
		} else {
			self::import_posts( $rows, $type, isset( $assoc['dry-run'] ) );
		}
	}

	/**
	 * 取り込んだお知らせの画像・PDF などを、旧サイトからメディアライブラリに取り込む。
	 *
	 * 本文中の旧サイトの wp-content/uploads の URL とアイキャッチ（_nfa_thumb_url）をダウンロードし、
	 * 本文の URL を新しい URL に置き換え、アイキャッチを設定する。
	 * 同じファイルは1回だけ取り込む（縮小版 -300x200 は元のファイルにまとめる）。
	 * 保存先は旧サイトと同じ年月のフォルダ（uploads/2016/02/ など）。
	 * 途中で止めても、もう一度実行すれば続きから進む（済んだ記事には _nfa_media_done が付く）。
	 *
	 * ## OPTIONS
	 *
	 * [--limit=<n>]
	 * : この件数の記事だけ処理する
	 *
	 * [--dry-run]
	 * : ダウンロードせずに、対象の記事数とファイル数だけを表示する
	 *
	 * [--retry-failed]
	 * : 前回取り込めなかったファイルのある記事だけを、もう一度処理する
	 *
	 * [--wait=<ms>]
	 * : 1ファイルごとの間隔（旧サイトに負荷をかけないため）。既定 200
	 *
	 * ## EXAMPLES
	 *     wp nfa-committee import-media --dry-run
	 *     wp nfa-committee import-media --limit=50
	 *
	 * @subcommand import-media
	 */
	public function import_media( $args, $assoc ) {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$dry   = isset( $assoc['dry-run'] );
		$limit = (int) ( $assoc['limit'] ?? 0 );
		$wait  = (int) ( $assoc['wait'] ?? 200 );
		if ( isset( $assoc['retry-failed'] ) ) {
			$wpdb->query( "DELETE d FROM {$wpdb->postmeta} d JOIN {$wpdb->postmeta} f ON f.post_id = d.post_id AND f.meta_key = '_nfa_media_failed' WHERE d.meta_key = '_nfa_media_done'" );
			$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_nfa_media_failed'" );
		}
		$ids   = $wpdb->get_col(
			"SELECT p.ID FROM {$wpdb->posts} p
			 JOIN {$wpdb->postmeta} t ON t.post_id = p.ID AND t.meta_key = '_nfa_legacy_type' AND t.meta_value IN ('post','news')
			 LEFT JOIN {$wpdb->postmeta} d ON d.post_id = p.ID AND d.meta_key = '_nfa_media_done'
			 WHERE d.meta_id IS NULL ORDER BY p.post_date DESC"
		);
		if ( $limit ) {
			$ids = array_slice( $ids, 0, $limit );
		}
		if ( $dry ) {
			$keys = array();
			foreach ( $ids as $id ) {
				foreach ( self::legacy_media_urls( get_post( $id ) ) as $u ) {
					$keys[ self::legacy_media_key( $u )[1] ] = true;
				}
			}
			WP_CLI::log( sprintf( '対象の記事 %d 件 ／ ファイル %d 個（取り込み済みのファイルも含む）', count( $ids ), count( $keys ) ) );
			return;
		}

		$bar  = \WP_CLI\Utils\make_progress_bar( 'メディア', count( $ids ) );
		$stat = array( 'new' => 0, 'reuse' => 0, 'fail' => 0 );
		foreach ( $ids as $id ) {
			$bar->tick();
			$post    = get_post( $id );
			$map     = array();
			$failed  = array();
			foreach ( self::legacy_media_urls( $post ) as $url ) {
				list( $path, $key ) = self::legacy_media_key( $url );
				$att                = self::legacy_attachment( $key );
				if ( $att ) {
					$stat['reuse']++;
				} else {
					$att = self::sideload_legacy( $key, $path, $id );
					usleep( $wait * 1000 );
					if ( ! $att ) {
						$stat['fail']++;
						$failed[] = $url;
						continue;
					}
					$stat['new']++;
				}
				$map[ $url ] = wp_get_attachment_url( $att );
				if ( $url === get_post_meta( $id, '_nfa_thumb_url', true ) && wp_attachment_is_image( $att ) ) {
					set_post_thumbnail( $id, $att );
					delete_post_meta( $id, '_nfa_thumb_url' );
				}
			}
			// strtr は長い方から1回だけ置き換えるので、置き換えた URL をもう一度置き換えることがない
			$content = strtr( $post->post_content, $map );
			if ( $content !== $post->post_content ) {
				$wpdb->update( $wpdb->posts, array( 'post_content' => $content ), array( 'ID' => $id ) );
				clean_post_cache( $id );
			}
			if ( $failed ) {
				update_post_meta( $id, '_nfa_media_failed', $failed );
			}
			update_post_meta( $id, '_nfa_media_done', 1 );
		}
		$bar->finish();
		if ( class_exists( 'NFA_AIS_Index' ) ) {
			NFA_AIS_Index::bump();
		}
		WP_CLI::success( sprintf( '新しく取り込んだファイル %d ／ 取り込み済みを使った %d ／ 取り込めなかった %d（記事の _nfa_media_failed に記録）', $stat['new'], $stat['reuse'], $stat['fail'] ) );
	}

	/**
	 * 記事の中の旧サイトのファイルの URL（本文に書かれたとおりの文字列）。
	 */
	private static function legacy_media_urls( $post ) {
		// 旧サイトの URL か、ドメインのない URL（"/wp-content/…"）。取り込み済みの新しい URL は拾わない
		preg_match_all( '#(?:(?:https?:)?//(?:www\.)?nagano-fa\.or\.jp|(?<=["\'(=\s]))/wp-content/uploads/[^\s"\'<>()]+#i', (string) $post->post_content, $m );
		$urls  = $m[0];
		$thumb = (string) get_post_meta( $post->ID, '_nfa_thumb_url', true );
		if ( $thumb ) {
			$urls[] = $thumb;
		}
		$urls = array_values( array_unique( $urls ) );
		return array_filter( $urls, fn( $u ) => (bool) preg_match( '#/wp-content/uploads/.+\.\w{2,5}$#', $u ) );
	}

	/**
	 * URL → [ uploads 以下のパス, 取り込みのキー（縮小版は元のファイル） ]
	 */
	private static function legacy_media_key( $url ) {
		$path = rawurldecode( preg_replace( '#^.*?/wp-content/uploads/#', '', $url ) );
		$key  = preg_replace( '/-\d+x\d+(\.\w+)$/', '$1', $path );
		return array( $path, $key );
	}

	private static function legacy_attachment( $key, $set = null ) {
		static $memo = array();
		if ( null !== $set ) {
			$memo[ $key ] = (int) $set;   // 取り込んだらすぐ覚える（同じファイルを2回取り込まない）
		}
		if ( empty( $memo[ $key ] ) ) {
			global $wpdb;
			$memo[ $key ] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_nfa_legacy_media' AND meta_value = %s LIMIT 1", $key ) );
		}
		return $memo[ $key ];
	}

	/**
	 * 旧サイトから1ファイルを取り込む（元のファイル → なければ縮小版）。
	 */
	private static function sideload_legacy( $key, $path, $parent ) {
		$base = 'https://www.nagano-fa.or.jp/wp-content/uploads/';
		$sub  = preg_match( '#^(\d{4}/\d{2})/#', $key, $mm ) ? '/' . $mm[1] : '';
		$dir  = function ( $u ) use ( $sub ) {
			if ( $sub ) {
				$u['subdir'] = $sub;
				$u['path']   = $u['basedir'] . $sub;
				$u['url']    = $u['baseurl'] . $sub;
			}
			return $u;
		};
		foreach ( array_unique( array( $key, $path ) ) as $try ) {
			$enc = implode( '/', array_map( 'rawurlencode', explode( '/', $try ) ) );
			$tmp = download_url( $base . $enc, 60 );
			if ( is_wp_error( $tmp ) ) {
				continue;
			}
			add_filter( 'upload_dir', $dir );
			$att = media_handle_sideload( array( 'name' => wp_basename( $try ), 'tmp_name' => $tmp ), $parent );
			remove_filter( 'upload_dir', $dir );
			if ( is_wp_error( $att ) ) {
				@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions.unlink_unlink
				WP_CLI::warning( $key . ': ' . $att->get_error_message() );
				return 0;
			}
			update_post_meta( $att, '_nfa_legacy_media', $key );
			self::legacy_attachment( $key, $att );
			return (int) $att;
		}
		return 0;
	}

	/** 旧サイトの固定ページのパスの1段目 → 担当委員会。null は取り込まない（バックアップのページ） */
	const LEGACY_PAGE_COMMITTEE = array(
		'cat_4'           => 'cat-4',
		'cat_3'           => 'cat-3',
		'cat_2'           => 'cat-2',
		'cat_1'           => 'cat-1',
		'cat_1u'          => 'cat-1',
		'page-11418'      => 'cat-1',   // 1種 大学
		'emperorscup'     => 'cat-1',
		'emperorscup-103' => 'cat-1',
		'cat_w'           => 'cat-w',
		'empressscup-2'   => 'cat-w',
		'cat_f'           => 'cat-f',
		'cat_s'           => 'cat-s',
		'referee_top'     => 'referee',
		'medicine-2'      => 'medical',
		'cat_old'         => null,
	);

	/**
	 * 旧サイトの固定ページ（REST API の pages を1つの JSON にしたもの）から、大会・リーグ資料を取り込む。
	 *
	 * - 「日程・試合結果」のページの大会ブロック（div.cups）1つを、大会・リーグ資料（nfa_doc）1件にする。
	 *   大会名＝h3、行の区分＝h4（北信地区・県 TOPリーグ など）、対象＝リンクの前の文字（1部・2部A など）、
	 *   資料名＝リンクの文字、更新日＝「(2025-10-20更新)」。担当委員会はページのパス、年度はページの題名から。
	 * - ファイルは旧サイトからメディアライブラリに取り込む（取り込み済みのファイルは使い回す）。
	 *   取り込めなかったファイルは旧サイトの URL を「外部URL」に入れる。
	 * - 大会ブロック以外のリンク（事業内容・書式ダウンロードなど）は、ファイルだけを取り込む。
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : 旧サイトの固定ページの JSON（/wp-json/wp/v2/pages の結果を1つの配列にしたもの）
	 *
	 * [--dry-run]
	 * : 取り込まずに、件数と例だけを表示する
	 *
	 * [--delete]
	 * : 取り込んだ大会・リーグ資料を削除する（メディアは残す）
	 *
	 * [--wait=<ms>]
	 * : 1ファイルごとの間隔。既定 300
	 *
	 * ## EXAMPLES
	 *     wp nfa-committee import-docs /tmp/old-pages.json --dry-run
	 *
	 * @subcommand import-docs
	 */
	public function import_docs( $args, $assoc ) {
		global $wpdb;
		if ( isset( $assoc['delete'] ) ) {
			$ids = get_posts( array( 'post_type' => NFA_Com_Doc_Type::TYPE, 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_nfa_legacy_type', 'meta_value' => 'doc' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			foreach ( $ids as $id ) {
				wp_delete_post( $id, true );
			}
			WP_CLI::success( count( $ids ) . ' 件を削除しました。' );
			return;
		}
		$pages = json_decode( (string) file_get_contents( $args[0] ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! is_array( $pages ) ) {
			WP_CLI::error( 'JSON を読み取れません：' . $args[0] );
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$dry  = isset( $assoc['dry-run'] );
		$wait = (int) ( $assoc['wait'] ?? 300 );

		$docs  = array();   // 大会ブロック
		$loose = array();   // 大会ブロックの外のファイル
		foreach ( $pages as $p ) {
			$seg = explode( '/', trim( (string) wp_parse_url( $p['link'], PHP_URL_PATH ), '/' ) )[0];
			if ( array_key_exists( $seg, self::LEGACY_PAGE_COMMITTEE ) && null === self::LEGACY_PAGE_COMMITTEE[ $seg ] ) {
				continue;
			}
			$parsed = self::parse_legacy_page( $p );
			$loose  = array_merge( $loose, $parsed['loose'] );
			$com    = self::LEGACY_PAGE_COMMITTEE[ $seg ] ?? '';
			if ( ! $com ) {
				// 委員会の決まらないページの大会ブロックは、ファイルだけ取り込む
				foreach ( $parsed['cups'] as $cup ) {
					$loose = array_merge( $loose, wp_list_pluck( $cup['rows'], 'url' ) );
				}
				continue;
			}
			foreach ( $parsed['cups'] as $i => $cup ) {
				$docs[] = $cup + array( 'committee' => $com, 'legacy' => 'doc:' . $p['id'] . ':' . $i, 'page' => $p );
			}
		}
		$loose = array_values( array_unique( $loose ) );
		$nrows = array_sum( array_map( fn( $d ) => count( $d['rows'] ), $docs ) );
		$per   = array_count_values( array_column( $docs, 'committee' ) );
		WP_CLI::log( sprintf( '大会・リーグ資料 %d 件（資料 %d 行）／ 大会ブロックの外のファイル %d 個', count( $docs ), $nrows, count( $loose ) ) );
		WP_CLI::log( '担当委員会：' . implode( '、', array_map( fn( $k, $v ) => $k . ' ' . $v, array_keys( $per ), $per ) ) );
		if ( $dry ) {
			foreach ( array_slice( $docs, 0, 3 ) as $d ) {
				WP_CLI::log( sprintf( '— %s（%d年度・%s・%s）', $d['title'], $d['year'], $d['committee'], $d['kind'] ) );
				foreach ( array_slice( $d['rows'], 0, 4 ) as $r ) {
					WP_CLI::log( sprintf( '    [%s] [%s] %s  %s  %s', $r['section'], $r['target'], $r['label'], $r['updated'], basename( $r['url'] ) ) );
				}
			}
			return;
		}

		$stat = array( 'made' => 0, 'skip' => 0, 'file' => 0, 'fail' => 0 );
		$get  = function ( $url ) use ( $wait, &$stat ) {
			list( $path, $key ) = self::legacy_media_key( $url );
			$att                = self::legacy_attachment( $key );
			if ( ! $att ) {
				$att = self::sideload_legacy( $key, $path, 0 );
				usleep( $wait * 1000 );
				$stat[ $att ? 'file' : 'fail' ]++;
			}
			return $att;
		};

		wp_defer_term_counting( true );
		$bar = \WP_CLI\Utils\make_progress_bar( '大会・リーグ資料', count( $docs ) );
		foreach ( $docs as $d ) {
			$bar->tick();
			if ( $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_nfa_legacy_id' AND meta_value = %s LIMIT 1", $d['legacy'] ) ) ) {
				$stat['skip']++;
				continue;
			}
			$meta = array(
				'_nfa_legacy_id'   => $d['legacy'],
				'_nfa_legacy_type' => 'doc',
				'_nfa_legacy_page' => $d['page']['link'],
				'nfa_doc_year'     => $d['year'],
				'_nfa_doc_year'    => 'field_nfa_doc_year',
				'nfa_doc_kind'     => $d['kind'],
				'_nfa_doc_kind'    => 'field_nfa_doc_kind',
				'nfa_doc_rows'     => count( $d['rows'] ),
				'_nfa_doc_rows'    => 'field_nfa_doc_rows',
			);
			$latest = '';
			foreach ( $d['rows'] as $i => $r ) {
				$att = $get( $r['url'] );
				$meta[ "nfa_doc_rows_{$i}_section" ] = $r['section'];
				$meta[ "nfa_doc_rows_{$i}_target" ]  = $r['target'];
				$meta[ "nfa_doc_rows_{$i}_label" ]   = $r['label'];
				$meta[ "nfa_doc_rows_{$i}_file" ]    = $att ? $att : '';
				$meta[ "nfa_doc_rows_{$i}_url" ]     = $att ? '' : $r['url'];   // 取り込めなかったときは旧サイトの URL
				$meta[ "nfa_doc_rows_{$i}_updated" ] = $r['updated'];
				foreach ( array( 'section', 'target', 'label', 'file', 'url', 'updated' ) as $sub ) {
					$meta[ "_nfa_doc_rows_{$i}_{$sub}" ] = 'field_nfa_doc_' . $sub;
				}
				$latest = max( $latest, $r['updated'] );
			}
			$date = $latest ? substr( $latest, 0, 4 ) . '-' . substr( $latest, 4, 2 ) . '-' . substr( $latest, 6, 2 ) . ' 09:00:00' : str_replace( 'T', ' ', $d['page']['modified'] );
			$id   = wp_insert_post(
				wp_slash(
					array(
						'post_type'   => NFA_Com_Doc_Type::TYPE,
						'post_status' => 'publish',
						'post_title'  => $d['title'],
						'post_date'   => $date,
						'meta_input'  => $meta,
					)
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				WP_CLI::warning( $d['title'] . ': ' . $id->get_error_message() );
				continue;
			}
			wp_set_object_terms( $id, $d['committee'], NFA_Com_Committees::TAX );
			wp_set_object_terms( $id, $d['purpose'], 'nfa_purpose' );
			$target = (string) nfa_committee_meta( $d['committee'], 'target' );
			if ( $target ) {
				wp_set_object_terms( $id, $target, 'nfa_target' );
			}
			$area = self::area_of( $d['title'] );
			if ( $area && ( $t = get_term_by( 'name', $area, 'nfa_area' ) ) ) {
				wp_set_object_terms( $id, (int) $t->term_id, 'nfa_area' );
			}
			// 取り込んだファイルの親をこの資料に（親のないファイルだけ）
			foreach ( $d['rows'] as $i => $r ) {
				$att = (int) $meta[ "nfa_doc_rows_{$i}_file" ];
				if ( $att && ! wp_get_post_parent_id( $att ) ) {
					$wpdb->update( $wpdb->posts, array( 'post_parent' => $id ), array( 'ID' => $att ) );
				}
			}
			$wpdb->update( $wpdb->posts, array( 'post_modified' => $date, 'post_modified_gmt' => get_gmt_from_date( $date ) ), array( 'ID' => $id ) );
			$stat['made']++;
		}
		$bar->finish();
		wp_defer_term_counting( false );

		$bar = \WP_CLI\Utils\make_progress_bar( 'そのほかのファイル', count( $loose ) );
		foreach ( $loose as $url ) {
			$bar->tick();
			$get( $url );
		}
		$bar->finish();
		if ( class_exists( 'NFA_AIS_Index' ) ) {
			NFA_AIS_Index::bump();
		}
		WP_CLI::success( sprintf( '大会・リーグ資料 %d 件（取り込み済みで飛ばした %d 件）／ 新しく取り込んだファイル %d ／ 取り込めなかったファイル %d（旧サイトの URL のまま）', $stat['made'], $stat['skip'], $stat['file'], $stat['fail'] ) );
	}

	/**
	 * 旧サイトの固定ページ1つを読む → [ cups: 大会ブロック[], loose: 大会ブロックの外のファイルの URL[] ]
	 */
	private static function parse_legacy_page( array $p ) {
		$html = (string) ( $p['content']['rendered'] ?? '' );
		$out  = array( 'cups' => array(), 'loose' => array() );
		if ( ! preg_match( '#/wp-content/uploads/#', $html ) ) {
			return $out;
		}
		$dom = new DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( '<?xml encoding="utf-8"?><div>' . $html . '</div>' );
		libxml_clear_errors();
		$xp     = new DOMXPath( $dom );
		$is_doc = fn( $href ) => (bool) preg_match( '#/wp-content/uploads/.+\.(pdf|xlsx?|docx?|pptx?|zip)$#i', $href );
		$text   = fn( $s ) => trim( preg_replace( '/[\s\x{3000}]+/u', ' ', html_entity_decode( (string) $s, ENT_QUOTES, 'UTF-8' ) ) );
		$ptitle = $text( $p['title']['rendered'] ?? '' );
		$pyear  = preg_match( '/(20\d\d)/', $ptitle, $m ) ? (int) $m[1] : 0;

		$in_cups = array();
		foreach ( $xp->query( '//div[contains(concat(" ", normalize-space(@class), " "), " cups ")]' ) as $cup ) {
			$h3    = $xp->query( './/h3', $cup )->item( 0 );
			$title = $h3 ? $text( $h3->textContent ) : '';
			$rows  = array();
			$sec   = '';
			// h4（区分）と、リンクを含む行（li / p）を、ページの順に読む
			foreach ( $xp->query( './/h4 | .//li | .//p', $cup ) as $node ) {
				if ( 'h4' === $node->nodeName ) {
					$sec = $text( $node->textContent );
					continue;
				}
				if ( 'p' === $node->nodeName && $xp->query( 'ancestor::li', $node )->length ) {
					continue;
				}
				$links = array();
				foreach ( $xp->query( './/a[@href]', $node ) as $a ) {
					if ( $is_doc( $a->getAttribute( 'href' ) ) ) {
						$links[] = $a;
					}
				}
				if ( ! $links ) {
					continue;
				}
				$whole   = $text( $node->textContent );
				$updated = preg_match( '/(20\d\d)[-.\/年](\d{1,2})[-.\/月](\d{1,2})/u', $whole, $m ) ? sprintf( '%04d%02d%02d', $m[1], $m[2], $m[3] ) : '';
				// 対象：最初のリンクより前の文字（区分と同じ文字は外す）
				$before = '';
				for ( $n = $node->firstChild; $n && $n !== $links[0] && ! ( $n instanceof DOMElement && $n->getElementsByTagName( 'a' )->length && $n->contains( $links[0] ) ); $n = $n->nextSibling ) {
					$before .= $n->textContent;
				}
				$target = trim( $text( $before ), " 　｜│|:：・" );
				if ( '' !== $sec && 0 === mb_strpos( $target, $sec ) ) {
					$target = trim( mb_substr( $target, mb_strlen( $sec ) ) );
				}
				foreach ( $links as $a ) {
					$href  = $a->getAttribute( 'href' );
					$label = $text( $a->textContent );
					$rows[] = array(
						'section' => $sec,
						'target'  => $target,
						'label'   => '' !== $label ? $label : rawurldecode( wp_basename( $href ) ),
						'url'     => $href,
						'updated' => $updated,
					);
					$in_cups[ $href ] = true;
				}
			}
			if ( ! $rows || '' === $title ) {
				continue;
			}
			$year = $pyear;
			if ( ! $year && preg_match( '/(20\d\d)/', $title, $m ) ) {
				$year = (int) $m[1];
			}
			if ( ! $year ) {
				$dates = array_filter( array_column( $rows, 'updated' ) );
				$year  = $dates ? (int) substr( max( $dates ), 0, 4 ) : (int) substr( (string) $p['modified'], 0, 4 );
			}
			if ( ! preg_match( '/20\d\d|第\d+回/u', $title ) ) {
				$title = $year . '年度 ' . $title;   // 毎年同じ大会名なので、年度を付けて見分ける
			}
			$labels  = implode( ' ', array_column( $rows, 'label' ) );
			$purpose = array();
			if ( preg_match( '/結果|星取|順位|成績/u', $labels ) ) {
				$purpose[] = 'result';
			}
			if ( preg_match( '/日程|要項|組合せ|組み合わせ|スケジュール|予定|トーナメント表/u', $labels ) || ! $purpose ) {
				$purpose[] = 'schedule';
			}
			$out['cups'][] = array(
				'title'   => $title,
				'year'    => $year,
				'kind'    => false !== mb_strpos( $title, 'リーグ' ) ? 'league' : 'tournament',
				'purpose' => $purpose,
				'rows'    => $rows,
			);
		}
		foreach ( $xp->query( '//a[@href]' ) as $a ) {
			$href = $a->getAttribute( 'href' );
			if ( $is_doc( $href ) && empty( $in_cups[ $href ] ) ) {
				$out['loose'][] = $href;
			}
		}
		return $out;
	}

	private static function read_csv( $file ) {
		if ( ! is_readable( $file ) ) {
			WP_CLI::error( '読み込めません：' . $file );
		}
		$fh   = fopen( $file, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		$head = fgetcsv( $fh, 0, ',', '"', '' );
		$head[0] = preg_replace( '/^\xEF\xBB\xBF/', '', $head[0] );
		$rows = array();
		while ( ( $r = fgetcsv( $fh, 0, ',', '"', '' ) ) !== false ) {
			if ( count( $r ) === count( $head ) ) {
				$rows[] = array_combine( $head, $r );
			}
		}
		fclose( $fh ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		return $rows;
	}

	/**
	 * 旧カテゴリー（カンマ区切り）→ 担当委員会・目的・フラグ。
	 */
	public static function map_categories( $cats ) {
		static $map = null;
		$map        = $map ?? require NFA_COM_DIR . 'includes/legacy-map.php';
		$out        = array( 'committee' => array(), 'purpose' => array(), 'flag' => array(), 'unknown' => array() );
		foreach ( array_filter( array_map( 'trim', explode( ',', (string) $cats ) ) ) as $c ) {
			if ( ! isset( $map[ $c ] ) ) {
				$out['unknown'][] = $c;
				continue;
			}
			foreach ( array( 'committee', 'purpose', 'flag' ) as $k ) {
				if ( ! empty( $map[ $c ][ $k ] ) ) {
					$out[ $k ][] = $map[ $c ][ $k ];
				}
			}
		}
		foreach ( $out as $k => $v ) {
			$out[ $k ] = array_values( array_unique( $v ) );
		}
		if ( ! $out['committee'] ) {
			$out['committee'] = array( 'office' );
		}
		if ( ! $out['purpose'] ) {
			$out['purpose'] = array( 'news' );
		}
		return $out;
	}

	private static function area_of( $text ) {
		if ( ! class_exists( 'NFA_AIS_Dictionary' ) ) {
			return '';
		}
		foreach ( NFA_AIS_Dictionary::axis( 'r' )['items'] ?? array() as $it ) {
			if ( '' !== $it['rx'] && preg_match( '/' . $it['rx'] . '/u', $text ) ) {
				return $it['v'];
			}
		}
		return '';
	}

	private static function ymd( $v ) {
		$v = trim( (string) $v );
		return preg_match( '/^\d{8}$/', $v ) ? $v : '';
	}

	private static function import_posts( array $rows, $type, $dry ) {
		$stat = array( 'committee' => array(), 'purpose' => array(), 'flag' => array(), 'unknown' => array(), 'area' => 0, 'link' => 0, 'top' => 0 );
		foreach ( $rows as $r ) {
			$m = 'news' === $type ? array( 'committee' => array( 'office' ), 'purpose' => array( 'news' ), 'flag' => array(), 'unknown' => array() ) : self::map_categories( $r['post_category'] ?? '' );
			foreach ( array( 'committee', 'purpose', 'flag', 'unknown' ) as $k ) {
				foreach ( $m[ $k ] as $v ) {
					$stat[ $k ][ $v ] = ( $stat[ $k ][ $v ] ?? 0 ) + 1;
				}
			}
			$stat['area'] += '' !== self::area_of( $r['post_title'] ) ? 1 : 0;
			$stat['link'] += '' !== trim( $r['vk-ltc-link'] ?? '' ) ? 1 : 0;
			$stat['top']  += 'on' === ( $r['header_slider'] ?? '' ) ? 1 : 0;
		}
		$names = array();
		foreach ( NFA_Com_Committees::definitions() as $slug => $d ) {
			$names[ $slug ] = $d['name'];
		}
		WP_CLI::log( '--- 読み替えの結果 ---' );
		WP_CLI::log( '担当委員会：' . implode( '、', array_map( fn( $k, $v ) => ( $names[ $k ] ?? $k ) . ' ' . $v, array_keys( $stat['committee'] ), $stat['committee'] ) ) );
		WP_CLI::log( '目的　　　：' . implode( '、', array_map( fn( $k, $v ) => $k . ' ' . $v, array_keys( $stat['purpose'] ), $stat['purpose'] ) ) );
		WP_CLI::log( '旧サイトの重要・注意喚起（引き継がない）：' . implode( '、', array_map( fn( $k, $v ) => $k . ' ' . $v, array_keys( $stat['flag'] ), $stat['flag'] ) ) );
		WP_CLI::log( '地域（タイトルから）：' . $stat['area'] . ' 件 ／ リンク先URL：' . $stat['link'] . ' 件 ／ TOPに掲載（引き継がない）：' . $stat['top'] . ' 件' );
		if ( $stat['unknown'] ) {
			WP_CLI::warning( '読み替え表にないカテゴリー：' . implode( '、', array_keys( $stat['unknown'] ) ) );
		}
		if ( $dry ) {
			return;
		}

		wp_defer_term_counting( true );
		$bar   = \WP_CLI\Utils\make_progress_bar( 'お知らせ', count( $rows ) );
		$made  = 0;
		$skip  = 0;
		global $wpdb;
		foreach ( $rows as $r ) {
			$bar->tick();
			$old = (int) $r['post_id'];
			if ( $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_nfa_legacy_id' AND meta_value = %s LIMIT 1", $type . ':' . $old ) ) ) {
				$skip++;
				continue;
			}
			$m    = 'news' === $type ? array( 'committee' => array( 'office' ), 'purpose' => array( 'news' ), 'flag' => array() ) : self::map_categories( $r['post_category'] ?? '' );
			$meta = array(
				'_nfa_legacy_id'   => $type . ':' . $old,
				'_nfa_legacy_type' => $type,
				'_nfa_legacy_cats' => (string) ( $r['post_category'] ?? '' ),
			);
			if ( ! empty( $r['post_thumbnail'] ) ) {
				$meta['_nfa_thumb_url'] = esc_url_raw( $r['post_thumbnail'] );
			}
			$acf = array(
				'nfa_event_date' => self::ymd( $r['開催日'] ?? '' ),
				'nfa_event_text' => trim( (string) ( $r['eventday'] ?? '' ) ),
				'nfa_link_url'   => esc_url_raw( trim( (string) ( $r['vk-ltc-link'] ?? '' ) ) ),
				'nfa_new_until'  => self::ymd( $r['hiddenday'] ?? '' ),
			);
			foreach ( $acf as $k => $v ) {
				if ( '' !== $v ) {
					$meta[ $k ]       = $v;
					$meta[ '_' . $k ] = 'field_' . $k;
				}
			}
			// 旧サイトの「重要」「注意喚起」「TOPに掲載」は引き継がない（新サイトの運用で付け直す）
			$postarr = array(
				'post_type'    => 'post',
				'post_status'  => 'publish' === $r['post_status'] ? 'publish' : 'draft',
				'post_name'    => $r['post_name'],
				'post_title'   => wp_strip_all_tags( str_ireplace( array( '<br>', '<br/>', '<br />' ), ' ', $r['post_title'] ) ),
				'post_content' => $r['post_content'],
				'post_date'    => $r['post_date'],
				'meta_input'   => $meta,
			);
			if ( ! get_post( $old ) ) {
				$postarr['import_id'] = $old;   // 旧サイトと同じ投稿ID（旧URLからの転送用）
			}
			$id = wp_insert_post( wp_slash( $postarr ), true );
			if ( is_wp_error( $id ) ) {
				WP_CLI::warning( $old . ': ' . $id->get_error_message() );
				continue;
			}
			wp_set_object_terms( $id, $m['committee'], NFA_Com_Committees::TAX );
			wp_set_object_terms( $id, $m['purpose'], 'nfa_purpose' );
			$targets = array_values( array_filter( array_map( fn( $c ) => (string) nfa_committee_meta( $c, 'target' ), $m['committee'] ) ) );
			if ( $targets ) {
				wp_set_object_terms( $id, $targets, 'nfa_target' );
			}
			$area = self::area_of( $postarr['post_title'] );
			if ( $area ) {
				$t = get_term_by( 'name', $area, 'nfa_area' );
				if ( $t ) {
					wp_set_object_terms( $id, (int) $t->term_id, 'nfa_area' );
				}
			}
			$wpdb->update( $wpdb->posts, array( 'post_modified' => $r['post_modified'], 'post_modified_gmt' => get_gmt_from_date( $r['post_modified'] ) ), array( 'ID' => $id ) );
			$made++;
		}
		$bar->finish();
		wp_defer_term_counting( false );
		if ( class_exists( 'NFA_AIS_Index' ) ) {
			NFA_AIS_Index::bump();
		}
		WP_CLI::success( sprintf( '%d 件を取り込みました（取り込み済みで飛ばした %d 件）。', $made, $skip ) );
	}

	private static function import_teams( array $rows, $dry ) {
		$pick = function ( $r, $key ) {
			foreach ( array( 'team_' . $key, 'cf_team_' . $key, 'cf__team_' . $key, $key ) as $col ) {
				if ( isset( $r[ $col ] ) && '' !== trim( $r[ $col ] ) ) {
					return trim( $r[ $col ] );
				}
			}
			return '';
		};
		$by_name = function ( $tax ) {
			$out = array();
			foreach ( NFA_Com_Team_Type::taxonomies()[ $tax ][1] as $slug => $name ) {
				$out[ $name ] = $slug;
			}
			return $out;
		};
		$stat = array();
		foreach ( $rows as $r ) {
			$stat[ $r['tax_team_area'] ] = ( $stat[ $r['tax_team_area'] ] ?? 0 ) + 1;
		}
		WP_CLI::log( '地域：' . implode( '、', array_map( fn( $k, $v ) => $k . ' ' . $v, array_keys( $stat ), $stat ) ) );
		if ( $dry ) {
			return;
		}
		global $wpdb;
		$made = 0;
		foreach ( $rows as $r ) {
			$old = (int) $r['post_id'];
			if ( $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_nfa_legacy_id' AND meta_value = %s LIMIT 1", 'team:' . $old ) ) ) {
				continue;
			}
			$days = array_values( array_intersect( NFA_Com_Team_Type::DAYS, array_map( 'trim', preg_split( '/[,、\s]+/u', $pick( $r, 'activity_days' ) ) ) ) );
			$meta = array( '_nfa_legacy_id' => 'team:' . $old, '_nfa_legacy_type' => 'team' );
			$acf  = array(
				'team_city'               => trim( (string) $r['tax_team_city'] ),
				'team_activity_days'      => $days,
				'team_activity_frequency' => $pick( $r, 'activity_frequency' ),
				'team_activity_location'  => $pick( $r, 'activity_location' ),
				'team_website_url'        => esc_url_raw( preg_replace( '/\s.*$/u', '', $pick( $r, 'website_url' ) ) ),
				'team_pr_comment'         => $pick( $r, 'pr_comment' ),
			);
			foreach ( $acf as $k => $v ) {
				$meta[ $k ]       = $v;
				$meta[ '_' . $k ] = 'field_' . $k;
			}
			$postarr = array(
				'post_type'    => NFA_Com_Team_Type::TYPE,
				'post_status'  => 'publish',
				'post_name'    => $r['post_name'],
				'post_title'   => $r['post_title'],
				'post_content' => (string) $r['post_content'],
				'meta_input'   => $meta,
			);
			if ( ! get_post( $old ) ) {
				$postarr['import_id'] = $old;
			}
			$id = wp_insert_post( wp_slash( $postarr ), true );
			if ( is_wp_error( $id ) ) {
				WP_CLI::warning( $old . ': ' . $id->get_error_message() );
				continue;
			}
			wp_set_object_terms( $id, array( $r['tax_team_area'] ), 'team_area' );
			foreach ( array( 'team_sport' => 'tax_team_sport', 'team_age' => 'tax_team_age', 'team_gender' => 'tax_team_gender' ) as $tax => $col ) {
				$map   = $by_name( $tax );
				$slugs = array_values( array_filter( array_map( fn( $n ) => $map[ trim( $n ) ] ?? '', explode( ',', $r[ $col ] ) ) ) );
				wp_set_object_terms( $id, $slugs, $tax );
			}
			$made++;
		}
		WP_CLI::success( $made . ' チームを取り込みました。' );
	}

	/**
	 * 委員会担当のテスト用ユーザーを作る（開発用）。パスワードは表示しない。
	 *
	 * ## OPTIONS
	 *
	 * <login>
	 * : ユーザー名
	 *
	 * <committees>
	 * : 担当委員会のスラッグ（カンマ区切り）
	 *
	 * ## EXAMPLES
	 *     wp nfa-committee add-user u12 cat-4
	 *
	 * @subcommand add-user
	 */
	public function add_user( $args ) {
		list( $login, $slugs ) = $args;
		$ids = array();
		foreach ( explode( ',', $slugs ) as $slug ) {
			$t = get_term_by( 'slug', trim( $slug ), NFA_Com_Committees::TAX );
			if ( ! $t ) {
				WP_CLI::error( '委員会が見つかりません：' . $slug );
			}
			$ids[] = (int) $t->term_id;
		}
		$user_id = username_exists( $login );
		if ( ! $user_id ) {
			$user_id = wp_insert_user( array( 'user_login' => $login, 'user_pass' => wp_generate_password( 24 ), 'role' => NFA_Com_Access::ROLE, 'user_email' => $login . '@example.test' ) );
			if ( is_wp_error( $user_id ) ) {
				WP_CLI::error( $user_id->get_error_message() );
			}
		}
		update_user_meta( $user_id, NFA_Com_Access::USER_META, $ids );
		WP_CLI::success( sprintf( 'ユーザー %s（ID %d）を委員会担当にし、担当委員会を設定しました。パスワードは wp user update で設定してください。', $login, $user_id ) );
	}
}

WP_CLI::add_command( 'nfa-committee', 'NFA_Com_CLI' );
