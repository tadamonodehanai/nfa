<?php
/**
 * 索引のバージョンとキャッシュ。
 *
 * 投稿や分類、設定が変わると索引のバージョンを上げ、古いキャッシュ（掲載データ・検索結果）は使われなくなる。
 * キーワード（固有名詞など）に当てはまる投稿の集合もここで求めてキャッシュする。
 * 段階2では、ここにベクトル検索（記事本文・PDF）を加える。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Index {

	const VERSION_OPTION = 'nfa_ais_index_version';

	private static $memo = array();

	public static function init() {
		add_action( 'save_post', array( __CLASS__, 'on_post_change' ), 20, 2 );
		add_action( 'deleted_post', array( __CLASS__, 'bump' ) );
		add_action( 'transition_post_status', array( __CLASS__, 'on_status' ), 10, 3 );
		add_action( 'set_object_terms', array( __CLASS__, 'bump' ) );
		foreach ( array( 'created_term', 'edited_term', 'delete_term' ) as $hook ) {
			add_action( $hook, array( __CLASS__, 'bump' ) );
		}
		add_action( 'update_option_' . NFA_AIS_Settings::OPTION, array( __CLASS__, 'bump' ) );
		add_action( 'update_option_' . NFA_AIS_Dictionary::OPTION, array( __CLASS__, 'bump' ) );
	}

	public static function on_post_change( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		if ( in_array( $post->post_type, NFA_AIS_Eligibility::NEVER_TYPES, true ) ) {
			return;
		}
		self::bump();
	}

	public static function on_status( $new, $old, $post ) {
		if ( $new !== $old && ! in_array( $post->post_type, NFA_AIS_Eligibility::NEVER_TYPES, true ) ) {
			self::bump();
		}
	}

	public static function bump() {
		update_option( self::VERSION_OPTION, (int) get_option( self::VERSION_OPTION, 0 ) + 1, false );
		self::$memo = array();
		NFA_AIS_Dictionary::reset_cache();
	}

	public static function version() {
		return (int) get_option( self::VERSION_OPTION, 0 );
	}

	public static function cache_key( $name ) {
		return 'nfa_ais_' . md5( self::version() . '|' . $name );
	}

	private static function cached( $name, callable $build ) {
		if ( isset( self::$memo[ $name ] ) ) {
			return self::$memo[ $name ];
		}
		$ck  = self::cache_key( $name );
		$hit = get_transient( $ck );
		if ( ! is_array( $hit ) ) {
			$hit = array_values( array_intersect( $build(), NFA_AIS_Eligibility::eligible_ids() ) );
			set_transient( $ck, $hit, DAY_IN_SECONDS );
		}
		return self::$memo[ $name ] = $hit;
	}

	/**
	 * キーワード（固有名詞など）をタイトル・抜粋・本文に含む投稿。
	 *
	 * @return int[]
	 */
	public static function keyword_ids( $word ) {
		return self::cached(
			'kw|' . $word,
			function () use ( $word ) {
				global $wpdb;
				$like = '%' . $wpdb->esc_like( $word ) . '%';
				return array_map(
					'intval',
					$wpdb->get_col(
						$wpdb->prepare(
							"SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND ( post_title LIKE %s OR post_excerpt LIKE %s OR post_content LIKE %s )",
							$like,
							$like,
							$like
						)
					)
				);
			}
		);
	}

	/**
	 * 古いキャッシュをまとめて削除する（WP-CLI の flush から）。
	 */
	public static function flush() {
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\\_transient\\_nfa\\_ais\\_%' OR option_name LIKE '\\_transient\\_timeout\\_nfa\\_ais\\_%'" );
		self::bump();
	}
}
