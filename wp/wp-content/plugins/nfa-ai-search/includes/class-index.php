<?php
/**
 * 条件ごとの該当投稿ID集合（段階1の索引）。
 *
 * 条件は「軸ごとにひとつの選択肢」と「キーワード（固有名詞など）」。
 * 条件1つにつき該当する投稿IDの集合を求めてキャッシュし、複数条件は集合の積（AND）で絞り込む。
 * 足すほど件数は減り、増えることはない（ワイヤーの設計メモ「絞り込みのロジック」）。
 * パネルの件数表示もこの集合で計算するので、選択肢が多くても問い合わせは増えない。
 *
 * 投稿や分類、設定が変わると索引のバージョンを上げ、古いキャッシュは使われなくなる。
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
	 * 軸の選択肢に該当する投稿（同名のタクソノミー用語が付いているもの）。
	 *
	 * @return int[]
	 */
	public static function option_ids( $axis_key, $value ) {
		return self::cached(
			'opt|' . $axis_key . '|' . $value,
			function () use ( $axis_key, $value ) {
				$tax = NFA_AIS_Taxonomies::slug( $axis_key );
				if ( ! $tax || ! taxonomy_exists( $tax ) ) {
					return array();
				}
				$o        = NFA_AIS_Dictionary::option( $axis_key, $value );
				$names    = array_unique( array_merge( array( $value ), $o ? $o['terms'] : array() ) );
				$term_ids = array();
				foreach ( $names as $name ) {
					$t = get_term_by( 'name', $name, $tax );
					if ( $t ) {
						$term_ids[] = (int) $t->term_id;
					}
				}
				if ( ! $term_ids ) {
					return array();
				}
				$objs = get_objects_in_term( $term_ids, $tax );
				return is_wp_error( $objs ) ? array() : array_map( 'intval', $objs );
			}
		);
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
	 * 条件（axis => value、keyword => 語）をすべて満たす投稿ID。条件なしなら空。
	 *
	 * @return int[]
	 */
	public static function match( array $cond ) {
		$ids = null;
		foreach ( $cond as $k => $v ) {
			if ( '' === (string) $v ) {
				continue;
			}
			$set = 'keyword' === $k ? self::keyword_ids( $v ) : self::option_ids( $k, $v );
			$ids = null === $ids ? $set : array_values( array_intersect( $ids, $set ) );
			if ( ! $ids ) {
				return array();
			}
		}
		return $ids ?? array();
	}

	public static function count( array $cond ) {
		return count( self::match( $cond ) );
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
