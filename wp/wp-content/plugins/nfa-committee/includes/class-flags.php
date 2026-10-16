<?php
/**
 * 「重要」「注意喚起」「TOPに掲載」。分類ではなく投稿のチェックボックス（メタ）にする。
 * 決められるのは事務局（権限 nfa_manage_flags を持つ人）だけ。委員会担当には欄を出さず、保存もさせない。
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

class NFA_Com_Flags {

	const CAP = 'nfa_manage_flags';

	const META = array(
		'important' => '_nfa_important',
		'alert'     => '_nfa_alert',
		'top'       => '_nfa_top',
	);

	const LABELS = array(
		'important' => '重要',
		'alert'     => '注意喚起',
		'top'       => 'TOPに掲載',
	);

	public static function post_types() {
		return array( 'post', NFA_Com_Doc_Type::TYPE );
	}

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_meta' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
	}

	public static function register_meta() {
		foreach ( self::post_types() as $type ) {
			foreach ( self::META as $key ) {
				register_post_meta(
					$type,
					$key,
					array(
						'type'          => 'boolean',
						'single'        => true,
						'default'       => false,
						'show_in_rest'  => true,
						'auth_callback' => fn() => current_user_can( self::CAP ),
					)
				);
			}
		}
	}

	public static function add_box() {
		if ( ! current_user_can( self::CAP ) ) {
			return;
		}
		foreach ( self::post_types() as $type ) {
			add_meta_box( 'nfa-flags', '掲載の扱い（事務局）', array( __CLASS__, 'render' ), $type, 'side', 'high' );
		}
	}

	public static function render( $post ) {
		wp_nonce_field( 'nfa_flags', 'nfa_flags_nonce' );
		foreach ( self::META as $flag => $key ) {
			printf(
				'<p><label><input type="checkbox" name="nfa_flags[%1$s]" value="1" %2$s> %3$s</label></p>',
				esc_attr( $flag ),
				checked( (bool) get_post_meta( $post->ID, $key, true ), true, false ),
				esc_html( self::LABELS[ $flag ] )
			);
		}
		echo '<p class="description">「TOPに掲載」はトップのヒーローとPICK UPに、「重要」「注意喚起」は一覧の目印とお知らせ帯に使います。</p>';
	}

	public static function save( $post_id, $post ) {
		if ( ! in_array( $post->post_type, self::post_types(), true ) ) {
			return;
		}
		if ( ! isset( $_POST['nfa_flags_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nfa_flags_nonce'] ), 'nfa_flags' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( self::CAP ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$in = isset( $_POST['nfa_flags'] ) ? (array) wp_unslash( $_POST['nfa_flags'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( self::META as $flag => $key ) {
			if ( empty( $in[ $flag ] ) ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, true );
			}
		}
	}
}
