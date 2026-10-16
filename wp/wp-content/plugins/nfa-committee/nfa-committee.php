<?php
/**
 * Plugin Name:       NFA Committee
 * Description:       長野県サッカー協会サイトの委員会ごとの運用。担当委員会による権限（自分の委員会の記事・大会資料だけを書ける）、大会・リーグ資料（nfa_doc）、チーム（team）、「重要」「注意喚起」「TOPに掲載」、委員会ごとの説明文・問い合わせ先、協賛企業の設定を提供します。
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            tadamono
 * Text Domain:       nfa-committee
 *
 * 分類（担当委員会 nfa_committee、対象・目的・地域）は NFA AI Search プラグインが登録する。
 * このプラグインは、その分類を使った運用（権限・データ・入力欄）を受け持つ。
 * テーマを作り替えてもデータと運用が残るよう、テーマではなくプラグインにしている。
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

define( 'NFA_COM_VERSION', '0.1.0' );
define( 'NFA_COM_DIR', plugin_dir_path( __FILE__ ) );
define( 'NFA_COM_URL', plugin_dir_url( __FILE__ ) );

require_once NFA_COM_DIR . 'includes/helpers.php';
require_once NFA_COM_DIR . 'includes/class-committees.php';
require_once NFA_COM_DIR . 'includes/class-doc-type.php';
require_once NFA_COM_DIR . 'includes/class-team-type.php';
require_once NFA_COM_DIR . 'includes/class-flags.php';
require_once NFA_COM_DIR . 'includes/class-access.php';
require_once NFA_COM_DIR . 'includes/class-settings.php';
require_once NFA_COM_DIR . 'includes/acf-fields.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once NFA_COM_DIR . 'includes/class-cli.php';
}

NFA_Com_Committees::init();
NFA_Com_Doc_Type::init();
NFA_Com_Team_Type::init();
NFA_Com_Flags::init();
NFA_Com_Access::init();
NFA_Com_Settings::init();
NFA_Com_ACF::init();

register_activation_hook( __FILE__, 'nfa_com_activate' );

function nfa_com_activate() {
	NFA_Com_Doc_Type::register();
	NFA_Com_Team_Type::register();
	NFA_Com_Access::install_roles();
	if ( taxonomy_exists( NFA_Com_Committees::TAX ) ) {
		NFA_Com_Committees::seed();
	}
	flush_rewrite_rules();
}
