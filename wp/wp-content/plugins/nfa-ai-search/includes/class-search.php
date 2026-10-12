<?php
/**
 * 探索エンジン（ワイヤー NFA_wireframes_site/search.html の会話ロジックをサーバー側へ移植）。
 *
 * 1回の問い合わせ（1ターン）について、条件・件数・AIの返事・結果（5件）・次の一手・0件時の提案・
 * パネルの件数をまとめて返す。画面側（assets/nfa-ai-search.js）はこの結果を描画するだけにする。
 *
 * 条件は「軸ごとにひとつ」＋「キーワード」。AND で累積し、足すほど件数は減る。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Search {

	const LIST_SIZE = 5;

	/**
	 * @param string $q    入力文（via=text のとき）
	 * @param string $via  text（入力文を読み取って条件に足す）| set（$cond をそのまま使う）
	 * @param array  $cond いまの条件 axis => value（keyword => 語 も可）
	 */
	public static function run( $q, $via, array $cond ) {
		$q      = trim( (string) $q );
		$via    = 'set' === $via ? 'set' : 'text';
		$cond   = self::clean( $cond );
		$parser = 'none';
		$unread = false;

		if ( 'text' === $via ) {
			$p      = NFA_AIS_Query_Parser::parse( $q );
			$parser = $p['parser'];
			if ( $p['cond'] ) {
				// 同じ軸は新しい読み取りで置き換える（軸ごとにひとつ）
				$cond = self::clean( array_merge( $cond, $p['cond'] ) );
			} else {
				$unread = true;
			}
		}

		$ids = $cond ? self::sorted( NFA_AIS_Index::match( $cond ) ) : array();
		$out = array(
			'cond'    => (object) $cond,
			'total'   => count( $ids ),
			'unread'  => $unread,
			'parser'  => $parser,
			'say'     => '',
			'results' => array(),
			'more'    => null,
			'next'    => null,
			'rescue'  => array(),
			'popular' => array(),
			'facets'  => self::facets( $cond ),
		);

		if ( $unread ) {
			$out['say']     = 'うまく読み取れませんでした。下の条件から選んでいただけますか。';
			$out['popular'] = NFA_AIS_Dictionary::popular();
			return $out;
		}
		if ( ! $cond ) {
			$out['say'] = '条件をすべて外しました。知りたいことを入力するか、条件から選んでください。';
			return $out;
		}
		if ( ! $ids ) {
			$out['say'] = '申し訳ありません。その条件に当てはまる情報は見つかりませんでした。';
			foreach ( $cond as $k => $v ) {
				$out['rescue'][] = array( 'key' => $k, 'value' => $v );
			}
			return $out;
		}

		$out['say']     = esc_html( implode( '、', self::phrases( $cond ) ) ) . 'ですね。<b>' . count( $ids ) . '件</b>の情報があります。';
		$out['results'] = array_values( array_filter( array_map( array( __CLASS__, 'item' ), array_slice( $ids, 0, self::LIST_SIZE ) ) ) );
		if ( count( $ids ) > self::LIST_SIZE ) {
			$out['more'] = array( 'url' => self::list_url( $cond ), 'total' => count( $ids ) );
		}
		$out['next'] = self::next_axis( $cond, $ids );
		return $out;
	}

	/**
	 * 条件の検証：知らない軸・選択肢は捨てる。キーワードは40文字まで。
	 */
	public static function clean( array $cond ) {
		$out = array();
		foreach ( NFA_AIS_Dictionary::axes() as $ax ) {
			$v = isset( $cond[ $ax['key'] ] ) ? (string) $cond[ $ax['key'] ] : '';
			if ( '' !== $v && NFA_AIS_Dictionary::option( $ax['key'], $v ) ) {
				$out[ $ax['key'] ] = $v;
			}
		}
		$kw = isset( $cond['keyword'] ) ? trim( (string) $cond['keyword'] ) : '';
		if ( '' !== $kw ) {
			$out['keyword'] = mb_substr( $kw, 0, 40 );
		}
		return $out;
	}

	/**
	 * 条件を会話の言い回しにする（「小学生（4種）のこと」「大会の日程が知りたい」など）。
	 */
	public static function phrases( array $cond ) {
		$words = array();
		foreach ( NFA_AIS_Dictionary::axes() as $ax ) {
			if ( isset( $cond[ $ax['key'] ] ) ) {
				$o       = NFA_AIS_Dictionary::option( $ax['key'], $cond[ $ax['key'] ] );
				$words[] = $o ? $o['say'] : $cond[ $ax['key'] ];
			}
		}
		if ( isset( $cond['keyword'] ) ) {
			$words[] = '「' . $cond['keyword'] . '」について';
		}
		return $words;
	}

	/**
	 * 新しい順（固定表示の投稿を先頭に）。
	 */
	private static function sorted( array $ids ) {
		if ( ! $ids ) {
			return array();
		}
		global $wpdb;
		$in     = implode( ',', array_map( 'intval', $ids ) );
		$sorted = array_map( 'intval', $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE ID IN ($in) ORDER BY post_date DESC, ID DESC" ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sticky = array_map( 'intval', (array) get_option( 'sticky_posts', array() ) );
		$top    = array_values( array_intersect( $sorted, $sticky ) );
		return array_merge( $top, array_values( array_diff( $sorted, $top ) ) );
	}

	/**
	 * 画面に渡す1件分の情報。返す直前に検索対象かを再確認する。
	 */
	public static function item( $id ) {
		$post = get_post( $id );
		if ( ! $post || ! NFA_AIS_Eligibility::is_eligible( $post ) ) {
			return null;
		}
		$axes = NFA_AIS_Taxonomies::axes_of( $post->ID );
		return array(
			'id'    => $post->ID,
			'title' => get_the_title( $post ),
			'url'   => get_permalink( $post ),
			'date'  => get_the_date( 'Y.m.d', $post ),
			'meta'  => array_merge( $axes['target'], $axes['purpose'], $axes['area'] ),
		);
	}

	/**
	 * 「この条件のN件をすべて見る」の行き先（タクソノミーの絞り込み一覧）。
	 */
	public static function list_url( array $cond ) {
		$args = array();
		foreach ( NFA_AIS_Taxonomies::FACETS as $key => $axis ) {
			if ( isset( $cond[ $key ] ) ) {
				$t = get_term_by( 'name', $cond[ $key ], $axis[0] );
				if ( $t ) {
					$args[ $axis[0] ] = $t->slug;
				}
			}
		}
		// 日本語の用語スラッグはすでにURLエンコード済みなので、そのまま使う
		if ( isset( $cond['keyword'] ) ) {
			$args['s'] = rawurlencode( $cond['keyword'] );
		}
		return apply_filters( 'nfa_ais_list_url', add_query_arg( $args, home_url( '/' ) ), $cond );
	}

	/**
	 * 左パネル：軸ごとの選択肢と、それを選んだときの件数。0件は押せなくする。
	 */
	public static function facets( array $cond ) {
		$out = array();
		foreach ( NFA_AIS_Dictionary::axes() as $ax ) {
			$opts = array();
			foreach ( $ax['options'] as $o ) {
				$test             = $cond;
				$test[ $ax['key'] ] = $o['value'];
				$n                = NFA_AIS_Index::count( $test );
				$on               = ( $cond[ $ax['key'] ] ?? null ) === $o['value'];
				$opts[]           = array( 'value' => $o['value'], 'n' => $n, 'on' => $on, 'dead' => ! $on && 0 === $n );
			}
			$out[] = array( 'key' => $ax['key'], 'name' => $ax['name'], 'axis' => $ax['axis'] ?? '', 'options' => $opts );
		}
		return $out;
	}

	/**
	 * 次に絞れる軸をひとつだけ提案する（まだ選んでいない軸のうち、結果が2つ以上の選択肢に分かれるもの）。
	 */
	public static function next_axis( array $cond, array $ids ) {
		foreach ( NFA_AIS_Dictionary::axes() as $ax ) {
			if ( isset( $cond[ $ax['key'] ] ) ) {
				continue;
			}
			$opts = array();
			foreach ( $ax['options'] as $o ) {
				$n = count( array_intersect( $ids, NFA_AIS_Index::option_ids( $ax['key'], $o['value'] ) ) );
				if ( $n > 0 ) {
					$opts[] = array( 'value' => $o['value'], 'n' => $n );
				}
			}
			if ( count( $opts ) > 1 ) {
				return array( 'key' => $ax['key'], 'name' => $ax['name'], 'options' => $opts );
			}
		}
		return null;
	}
}
