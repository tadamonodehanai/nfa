<?php
/**
 * プラグイン削除時の後片付け。設定・辞書・検索ログ・キャッシュを消す。
 * 3軸と発信元の分類（タクソノミーの用語と記事への付与）は記事のデータなので残す。
 *
 * @package NFA_AI_Search
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

foreach ( array( 'nfa_ais_settings', 'nfa_ais_dictionary', 'nfa_ais_dictionary_v2', 'nfa_ais_index_version', 'nfa_ais_ai_usage', 'nfa_ais_log_db', 'nfa_ais_search_page', 'nfa_ais_docs_db', 'nfa_ais_ingest_queue', 'nfa_ais_ingest_job' ) as $opt ) {
	delete_option( $opt );
}
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\\_transient\\_nfa\\_ais\\_%' OR option_name LIKE '\\_transient\\_timeout\\_nfa\\_ais\\_%'" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}nfa_ais_log" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}nfa_ais_docs" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
foreach ( array( 'nfa_ais_log_cleanup', 'nfa_ais_ingest_step', 'nfa_ais_ingest_scan' ) as $hook ) {
	wp_clear_scheduled_hook( $hook );
}
