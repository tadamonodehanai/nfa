<?php
/**
 * 探索画面のショートコードと、テーマ向けの表示部品。
 *
 *   [nfa_ai_search lead="…" sub="…"]   探索画面（ワイヤー NFA_wireframes_site/search.html を移植）
 *   [nfa_ai_source_notice]             記事ページの「AIの案内はこのページを根拠にしています」
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Frontend {

	const PAGE_OPTION = 'nfa_ais_search_page';

	public static function init() {
		add_shortcode( 'nfa_ai_search', array( __CLASS__, 'shortcode' ) );
		add_shortcode( 'nfa_ai_source_notice', array( __CLASS__, 'source_notice' ) );
		// ブロックテーマはテンプレート（＝ショートコード）を wp_head より前に描画するので、init で登録しておく。
		add_action( 'init', array( __CLASS__, 'register_assets' ) );
		add_action( 'save_post_page', array( __CLASS__, 'remember_search_page' ), 10, 2 );
	}

	public static function register_assets() {
		$ver = fn( $f ) => NFA_AIS_VERSION . '.' . filemtime( NFA_AIS_DIR . $f );
		wp_register_style( 'nfa-ai-search', NFA_AIS_URL . 'assets/nfa-ai-search.css', array(), $ver( 'assets/nfa-ai-search.css' ) );
		wp_register_script( 'nfa-ai-search', NFA_AIS_URL . 'assets/nfa-ai-search.js', array(), $ver( 'assets/nfa-ai-search.js' ), true );
	}

	/**
	 * 探索画面を置いた固定ページを覚えておく（ヘッダーの検索ボックスの送り先に使う）。
	 */
	public static function remember_search_page( $post_id, $post ) {
		if ( 'publish' === $post->post_status && has_shortcode( $post->post_content, 'nfa_ai_search' ) ) {
			update_option( self::PAGE_OPTION, $post_id, false );
		}
	}

	public static function search_page_url() {
		$id = (int) get_option( self::PAGE_OPTION );
		return $id && 'publish' === get_post_status( $id ) ? get_permalink( $id ) : '';
	}

	public static function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				// ページ見出し（.pHead）。テーマ側で見出しを出す場合は header="off"
				'header'      => 'on',
				'title'       => '',
				'description' => '知りたいことを言葉のまま入力するか、条件を選んでください。どの委員会の担当かを考える必要はありません。',
				// 入口
				'lead'        => '何を知りたいですか？',
				'sub'         => '例えばこんな聞き方ができます。',
				'placeholder' => '知りたいことを入力してください',
			),
			$atts,
			'nfa_ai_search'
		);
		wp_enqueue_style( 'nfa-ai-search' );
		wp_enqueue_script( 'nfa-ai-search' );
		// 設定は要素の属性で渡す（テーマの描画順に左右されないように）
		$config = array(
			'api'         => esc_url_raw( rest_url( NFA_AIS_Rest::NS . '/' ) ),
			'nonce'       => wp_create_nonce( NFA_AIS_Rest::NONCE_ACTION ),
			'examples'    => NFA_AIS_Dictionary::examples(),
			'header'      => 'off' !== $atts['header'],
			'title'       => '' !== $atts['title'] ? $atts['title'] : ( get_the_title() ? get_the_title() : '情報を探す' ),
			'description' => $atts['description'],
			'home'        => home_url( '/' ),
			'lead'        => $atts['lead'],
			'sub'         => $atts['sub'],
			'placeholder' => $atts['placeholder'],
			// 選択肢を押したときの言い回し（「小学生（4種）のこと」など）
			'axes'        => array_map(
				fn( $ax ) => array(
					'key'     => $ax['key'],
					'options' => array_map( fn( $o ) => array( 'value' => $o['value'], 'say' => $o['say'] ), $ax['options'] ),
				),
				NFA_AIS_Dictionary::axes()
			),
		);
		return sprintf(
			'<div class="nfa-ais" id="nfa-ais" data-nfa-ais="%s"><noscript>この検索画面を使うには JavaScript を有効にしてください。</noscript></div>',
			esc_attr( wp_json_encode( $config ) )
		);
	}

	public static function source_notice( $atts = array() ) {
		$post = get_post();
		if ( ! $post || ! NFA_AIS_Eligibility::is_eligible( $post ) ) {
			return '';
		}
		wp_enqueue_style( 'nfa-ai-search' );
		// 担当部署（発信元）は公開側に出さない
		return '<div class="nfa-ais-ground"><span class="nfa-ais-ground__ck" aria-hidden="true">✓</span><p>このページは長野県サッカー協会が掲載した情報です。「情報を探す」のAIは、このページを根拠として案内しています。</p></div>';
	}
}
