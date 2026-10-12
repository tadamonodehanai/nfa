<?php
/**
 * Plugin Name:       NFA AI Search
 * Description:       長野県サッカー協会サイトの情報探索（AI検索）。3軸タクソノミー（対象・目的・地域）、言い換え辞書、検索API、探索画面（[nfa_ai_search]）を提供します。
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            tadamono
 * Text Domain:       nfa-ai-search
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

define( 'NFA_AIS_VERSION', '0.1.0' );
define( 'NFA_AIS_FILE', __FILE__ );
define( 'NFA_AIS_DIR', plugin_dir_path( __FILE__ ) );
define( 'NFA_AIS_URL', plugin_dir_url( __FILE__ ) );

/*
 * APIキー等は wp-config.php で定義する（このファイルやDBには保存しない）。
 *   define( 'NFA_ANTHROPIC_API_KEY', '...' );   // 未定義なら、AIを使わないルールベースで動く
 *   define( 'NFA_ANTHROPIC_MODEL', 'claude-haiku-5-5' );
 */

require_once NFA_AIS_DIR . 'includes/class-taxonomies.php';
require_once NFA_AIS_DIR . 'includes/class-dictionary.php';
require_once NFA_AIS_DIR . 'includes/class-settings.php';
require_once NFA_AIS_DIR . 'includes/class-eligibility.php';
require_once NFA_AIS_DIR . 'includes/class-text.php';
require_once NFA_AIS_DIR . 'includes/class-index.php';
require_once NFA_AIS_DIR . 'includes/class-claude.php';
require_once NFA_AIS_DIR . 'includes/class-query-parser.php';
require_once NFA_AIS_DIR . 'includes/class-search.php';
require_once NFA_AIS_DIR . 'includes/class-log.php';
require_once NFA_AIS_DIR . 'includes/class-rest.php';
require_once NFA_AIS_DIR . 'includes/class-frontend.php';
require_once NFA_AIS_DIR . 'includes/class-admin.php';
require_once NFA_AIS_DIR . 'includes/template-tags.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once NFA_AIS_DIR . 'includes/class-cli.php';
}

NFA_AIS_Taxonomies::init();
NFA_AIS_Eligibility::init();
NFA_AIS_Index::init();
NFA_AIS_Rest::init();
NFA_AIS_Frontend::init();
NFA_AIS_Admin::init();
NFA_AIS_Log::init();

register_activation_hook( __FILE__, 'nfa_ais_activate' );
register_deactivation_hook( __FILE__, 'nfa_ais_deactivate' );

/**
 * 有効化：タクソノミーと初期語彙、ログ用テーブルを用意する。
 */
function nfa_ais_activate() {
	NFA_AIS_Taxonomies::register();
	NFA_AIS_Taxonomies::seed_terms();
	NFA_AIS_Log::install();
	NFA_AIS_Index::bump();
	flush_rewrite_rules();
}

function nfa_ais_deactivate() {
	wp_clear_scheduled_hook( NFA_AIS_Log::CRON_HOOK );
}
