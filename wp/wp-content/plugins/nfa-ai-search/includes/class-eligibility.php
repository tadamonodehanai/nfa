<?php
/**
 * 検索対象の判定（許可リスト方式）。
 *
 * 取り込むのは「許可した投稿タイプ」の「公開済み・パスワードなし」の投稿だけ。
 * お問い合わせ（Contact Form 7 / Flamingo など）や下書き・非公開、ユーザー情報は対象にならない。
 * 検索結果を返す直前にも is_eligible() で再確認する（設計書 9章「検索対象と除外ルール」）。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Eligibility {

	const META_EXCLUDE = '_nfa_ais_exclude';

	/** 設定に関係なく、絶対に対象にしない投稿タイプ */
	const NEVER_TYPES = array(
		'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request',
		'wp_block', 'wp_template', 'wp_template_part', 'wp_global_styles', 'wp_navigation', 'wp_font_family', 'wp_font_face',
		// Contact Form 7 のフォーム定義（送信先アドレス・メール文面を含む）
		'wpcf7_contact_form',
		// Flamingo（Contact Form 7 の送信内容・連絡先の保存）
		'flamingo_inbound', 'flamingo_contact', 'flamingo_outbound',
	);

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_meta' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	public static function register_meta() {
		foreach ( NFA_AIS_Settings::post_types() as $type ) {
			register_post_meta(
				$type,
				self::META_EXCLUDE,
				array(
					'type'          => 'boolean',
					'single'        => true,
					'show_in_rest'  => true,
					'default'       => false,
					'auth_callback' => fn( $allowed, $meta_key, $post_id ) => current_user_can( 'edit_post', $post_id ),
				)
			);
		}
	}

	public static function add_meta_box() {
		foreach ( NFA_AIS_Settings::post_types() as $type ) {
			add_meta_box( 'nfa-ais-exclude', 'AI検索', array( __CLASS__, 'render_meta_box' ), $type, 'side', 'low' );
		}
	}

	public static function render_meta_box( $post ) {
		wp_nonce_field( 'nfa_ais_exclude', 'nfa_ais_exclude_nonce' );
		$checked = (bool) get_post_meta( $post->ID, self::META_EXCLUDE, true );
		echo '<label><input type="checkbox" name="nfa_ais_exclude" value="1" ' . checked( $checked, true, false ) . '> AI検索の対象外にする</label>';
		if ( self::has_form( $post->post_content ) ) {
			echo '<p class="description">お問い合わせフォームを含むため、自動的に対象外です。</p>';
		}
	}

	public static function save_meta_box( $post_id, $post ) {
		if ( ! isset( $_POST['nfa_ais_exclude_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nfa_ais_exclude_nonce'] ), 'nfa_ais_exclude' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( empty( $_POST['nfa_ais_exclude'] ) ) {
			delete_post_meta( $post_id, self::META_EXCLUDE );
		} else {
			update_post_meta( $post_id, self::META_EXCLUDE, true );
		}
	}

	/**
	 * お問い合わせフォーム（Contact Form 7 のショートコード／ブロック）を含むか。
	 */
	public static function has_form( $content ) {
		return false !== strpos( (string) $content, '[contact-form-7' ) || false !== strpos( (string) $content, 'wp:contact-form-7/' );
	}

	/**
	 * 探索画面そのもの（[nfa_ai_search] を置いたページ）は検索結果に出さない。
	 */
	public static function is_search_page( $content ) {
		return false !== strpos( (string) $content, '[nfa_ai_search' );
	}

	/**
	 * 1件の投稿が検索対象か。
	 */
	public static function is_eligible( $post ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return false;
		}
		if ( 'publish' !== $post->post_status || '' !== $post->post_password ) {
			return false;
		}
		if ( ! in_array( $post->post_type, NFA_AIS_Settings::post_types(), true ) ) {
			return false;
		}
		if ( in_array( (int) $post->ID, NFA_AIS_Settings::value( 'excluded_ids' ), true ) ) {
			return false;
		}
		if ( get_post_meta( $post->ID, self::META_EXCLUDE, true ) ) {
			return false;
		}
		return ! self::has_form( $post->post_content ) && ! self::is_search_page( $post->post_content );
	}

	/**
	 * 検索対象の投稿IDの一覧（索引のバージョンごとにキャッシュ）。
	 *
	 * @param bool $fresh キャッシュを使わずに数え直す（取り込みの差分判定用）
	 * @return int[]
	 */
	public static function eligible_ids( $fresh = false ) {
		$ck  = NFA_AIS_Index::cache_key( 'eligible' );
		$ids = $fresh ? false : get_transient( $ck );
		if ( is_array( $ids ) ) {
			return $ids;
		}
		global $wpdb;
		$types = NFA_AIS_Settings::post_types();
		if ( ! $types ) {
			return array();
		}
		$type_in = implode( ',', array_fill( 0, count( $types ), '%s' ) );
		$sql     = "SELECT p.ID FROM {$wpdb->posts} p
			WHERE p.post_status = 'publish' AND p.post_password = '' AND p.post_type IN ($type_in)
			AND p.post_content NOT LIKE %s AND p.post_content NOT LIKE %s AND p.post_content NOT LIKE %s
			AND NOT EXISTS ( SELECT 1 FROM {$wpdb->postmeta} m WHERE m.post_id = p.ID AND m.meta_key = %s AND m.meta_value NOT IN ('', '0') )";
		$args    = array_merge( $types, array( '%[contact-form-7%', '%wp:contact-form-7/%', '%[nfa\_ai\_search%', self::META_EXCLUDE ) );
		$ids     = array_map( 'intval', $wpdb->get_col( $wpdb->prepare( $sql, $args ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$ids     = array_values( array_diff( $ids, NFA_AIS_Settings::value( 'excluded_ids' ) ) );
		set_transient( $ck, $ids, DAY_IN_SECONDS );
		return $ids;
	}
}
