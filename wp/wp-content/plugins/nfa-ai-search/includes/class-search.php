<?php
/**
 * 探索エンジン。
 *
 * 画面（デザインの search.js を移植した assets/nfa-ai-search.js）は、会話の組み立て・描画を受け持つ。
 * サーバーは次を受け持つ：
 *   1. 入力文 → 条件の読み取り（辞書。APIキーがあれば Claude）
 *   2. 掲載データ（お知らせ＝投稿、大会・リーグ資料＝nfa_doc の行）の絞り込み（軸の中は OR、軸どうしは AND）
 *   3. 0件のときに条件をゆるめる（目的 → 地域の順に外す）
 *   4. 条件パネル・候補タグの件数（その選択肢に置き換えたときの件数）
 *
 * 掲載データは search-data.js（NFA_DOCS）と同じ形の配列にしてキャッシュする（索引のバージョンごと）。
 *   k: news | pdf   t: タイトル   g: 大会名（pdf）   u: URL   d: Y.m.d   l: 委員会のバッジ   col: バッジの色
 *   f: 資料の形式（pdf）  x: 抜粋（news）  img: サムネイル（news）  c/p/r: 3軸の値
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Search {

	const MAX_DOCS = 300;

	/* ---------- 掲載データ ---------- */

	/**
	 * @return array[]
	 */
	public static function corpus() {
		static $memo = null;
		$ck = NFA_AIS_Index::cache_key( 'corpus' );
		if ( null !== $memo && $memo[0] === $ck ) {
			return $memo[1];
		}
		$items = get_transient( $ck );
		if ( ! is_array( $items ) ) {
			$items = self::build_corpus();
			set_transient( $ck, $items, DAY_IN_SECONDS );
		}
		$memo = array( $ck, $items );
		return $items;
	}

	public static function build_corpus() {
		$items = array();
		$ids   = NFA_AIS_Eligibility::eligible_ids();
		foreach ( array_chunk( $ids, 200 ) as $chunk ) {
			_prime_post_caches( $chunk, true, true );
			foreach ( $chunk as $id ) {
				$post = get_post( $id );
				if ( ! $post ) {
					continue;
				}
				$ax  = NFA_AIS_Taxonomies::values_of( $id );
				$com = self::committee_badge( $id );
				if ( 'nfa_doc' === $post->post_type ) {
					foreach ( self::doc_rows( $post ) as $row ) {
						// 1つの大会資料に地域の違う行（北信リーグ・南信リーグ…）がまとまっていることがあるので、
						// 行の区分・資料名に地域名があれば、その行の地域はそれにする（なければ投稿の地域）
						$row_r   = self::area_of( $row['t'] );
						$items[] = array(
							'id' => $id,
							'k'  => 'pdf',
							'f'  => $row['f'],
							't'  => $row['t'],
							'g'  => get_the_title( $post ),
							'u'  => $row['u'],
							'd'  => $row['d'],
							'l'  => $com[0],
							'c'  => $ax['t'],
							'p'  => $ax['p'],
							'r'  => $row_r ? array( $row_r ) : $ax['r'],
						);
					}
					continue;
				}
				$thumb = get_the_post_thumbnail_url( $post, 'medium_large' );
				if ( ! $thumb ) {
					$thumb = (string) get_post_meta( $id, '_nfa_thumb_url', true ); // 旧サイトから移したアイキャッチ
				}
				$link    = function_exists( 'nfa_field' ) ? (string) nfa_field( 'nfa_link_url', $id ) : '';
				$items[] = array(
					'id'  => $id,
					'k'   => 'news',
					't'   => get_the_title( $post ),
					'u'   => $link ? $link : get_permalink( $post ),
					'd'   => get_the_date( 'Y.m.d', $post ),
					'l'   => $com[0],
					'col' => $com[1],
					'img' => $thumb ? $thumb : '',
					'x'   => NFA_AIS_Text::snippet( $post, 80 ),
					'c'   => $ax['t'],
					'p'   => $ax['p'],
					'r'   => $ax['r'],
				);
			}
		}
		// 新しい順（日付が空のものは最後）
		usort( $items, fn( $a, $b ) => strcmp( $b['d'], $a['d'] ) );
		return $items;
	}

	/**
	 * 文中の地域名（辞書の地域の rx）から地域を1つ読み取る。
	 */
	private static function area_of( $text ) {
		foreach ( NFA_AIS_Dictionary::axis( 'r' )['items'] ?? array() as $it ) {
			if ( '' !== $it['rx'] && preg_match( '/' . $it['rx'] . '/u', $text ) ) {
				return $it['v'];
			}
		}
		return '';
	}

	private static function committee_badge( $post_id ) {
		$terms = get_the_terms( $post_id, NFA_AIS_Taxonomies::COMMITTEE );
		if ( ! $terms || is_wp_error( $terms ) ) {
			return array( 'お知らせ', 'var(--navy)' );
		}
		$t = $terms[0];
		if ( function_exists( 'nfa_committee_meta' ) ) {
			return array( (string) nfa_committee_meta( $t, 'badge' ) ?: $t->name, (string) nfa_committee_meta( $t, 'color' ) ?: 'var(--navy)' );
		}
		return array( $t->name, 'var(--navy)' );
	}

	/**
	 * 大会・リーグ資料の行（ファイルのある行だけ）。NFA Committee の nfa_doc_rows() を使う。
	 */
	private static function doc_rows( $post ) {
		if ( ! function_exists( 'nfa_doc_rows' ) ) {
			return array();
		}
		$out = array();
		foreach ( nfa_doc_rows( $post ) as $r ) {
			if ( $r['pending'] ) {
				continue;
			}
			$ext   = strtoupper( (string) pathinfo( (string) wp_parse_url( $r['url'], PHP_URL_PATH ), PATHINFO_EXTENSION ) );
			$title = trim( implode( ' ', array_filter( array( $r['section'], $r['target'], $r['label'] ) ) ) );
			$out[] = array(
				't' => '' !== $title ? $title : '資料',
				'u' => $r['url'],
				'd' => $r['updated'] ? $r['updated'] : get_the_modified_date( 'Y.m.d', $post ),
				'f' => in_array( $ext, array( 'PDF', 'XLSX', 'XLS', 'DOCX', 'DOC' ), true ) ? $ext : 'LINK',
			);
		}
		return $out;
	}

	/* ---------- 絞り込み ---------- */

	/**
	 * 軸の中は OR、軸どうしは AND。キーワードはタイトル・大会名・抜粋・本文のどれかに含むもの。
	 */
	public static function find( array $st ) {
		$kw_ids = null;
		$kw     = (string) ( $st['kw'] ?? '' );
		if ( '' !== $kw ) {
			$kw_ids = array_flip( NFA_AIS_Index::keyword_ids( $kw ) );
		}
		$out = array();
		foreach ( self::corpus() as $d ) {
			foreach ( array( 't' => 'c', 'p' => 'p', 'r' => 'r' ) as $ax => $field ) {
				if ( ! empty( $st[ $ax ] ) && ! array_intersect( $st[ $ax ], $d[ $field ] ) ) {
					continue 2;
				}
			}
			if ( null !== $kw_ids && ! isset( $kw_ids[ $d['id'] ] ) && false === mb_strpos( $d['t'] . ' ' . ( $d['g'] ?? '' ), $kw ) ) {
				continue;
			}
			$out[] = $d;
		}
		return $out;
	}

	public static function count( array $st ) {
		return count( self::find( $st ) );
	}

	/**
	 * 条件パネル・候補タグの件数：その軸だけ別の選択肢に置き換えたときの件数。
	 */
	public static function counts( array $c ) {
		$out = array();
		foreach ( NFA_AIS_Dictionary::axes() as $ax ) {
			foreach ( $ax['items'] as $it ) {
				$o                              = $c;
				$o[ $ax['key'] ]                = array( $it['v'] );
				$out[ $ax['key'] ][ $it['v'] ] = self::count( $o );
			}
		}
		return $out;
	}

	/* ---------- 1ターン ---------- */

	/**
	 * @param string $q    入力文（via=text）
	 * @param string $via  text（入力文を読み取って条件に重ねる）| set（$c をそのまま使う）
	 * @param array  $c    いまの条件 {t:[], p:[], r:[], join:bool, kw:string}
	 */
	public static function run( $q, $via, array $c ) {
		$c      = self::clean( $c );
		$q      = trim( (string) $q );
		$unread = false;
		$parser = 'none';

		if ( 'text' === $via && '' !== $q ) {
			$p      = NFA_AIS_Query_Parser::parse( $q );
			$f      = $p['f'];
			$parser = $p['parser'];
			$got    = $f['t'] || $f['p'] || $f['r'] || $f['join'];
			// 軸ごとに置き換え（矛盾させない）
			foreach ( array( 't', 'p', 'r' ) as $ax ) {
				if ( $f[ $ax ] ) {
					$c[ $ax ] = array_slice( $f[ $ax ], 0, 1 );
				}
			}
			if ( $f['join'] ) {
				$c['join'] = true;
				$c['p']    = array();
			} elseif ( $f['p'] ) {
				$c['join'] = false;
			}
			// 軸が何も取れないときは、ことば（固有名詞など）で探す
			if ( $got ) {
				$c['kw'] = '';
			} else {
				$hit = '';
				foreach ( $f['kw'] as $w ) {
					if ( self::count( array( 'kw' => $w ) + $c ) ) {
						$hit = $w;
						break;
					}
				}
				if ( '' !== $hit ) {
					$c['kw'] = $hit;
				} else {
					$unread = true;
				}
			}
		}

		$any   = $c['t'] || $c['p'] || $c['r'] || '' !== $c['kw'];
		$docs  = $any ? self::find( $c ) : array();
		$total = count( $docs );
		$st    = $c;
		$relax = '';
		if ( ! $docs && ( $c['p'] || $c['r'] ) ) {
			$drop        = $c['p'] ? 'p' : 'r';
			$s2          = $c;
			$s2[ $drop ] = array();
			$d2          = self::find( $s2 );
			if ( $d2 ) {
				$relax = $drop;
				$docs  = $d2;
				$st    = $s2;
			}
		}

		return array(
			'c'       => $c,
			'st'      => $st,
			'relaxed' => $relax,
			'unread'  => $unread,
			'parser'  => $parser,
			'total'   => $total,
			'npdf'    => count( array_filter( $docs, fn( $d ) => 'pdf' === $d['k'] ) ),
			'nnews'   => count( array_filter( $docs, fn( $d ) => 'news' === $d['k'] ) ),
			'docs'    => array_map( array( __CLASS__, 'public_item' ), array_slice( $docs, 0, self::MAX_DOCS ) ),
			'counts'  => self::counts( array( 'kw' => '' ) + $c ),
		);
	}

	/**
	 * 画面に渡す形（内部の id は渡さない）。
	 */
	public static function public_item( $d ) {
		unset( $d['id'] );
		return $d;
	}

	/**
	 * 条件の検証：知らない値は捨てる。
	 */
	public static function clean( array $c ) {
		$out = array( 't' => array(), 'p' => array(), 'r' => array(), 'join' => ! empty( $c['join'] ), 'kw' => '' );
		foreach ( array( 't', 'p', 'r' ) as $ax ) {
			foreach ( (array) ( $c[ $ax ] ?? array() ) as $v ) {
				if ( NFA_AIS_Dictionary::item( $ax, (string) $v ) ) {
					$out[ $ax ] = array( (string) $v );
					break;
				}
			}
		}
		$kw        = trim( (string) ( $c['kw'] ?? '' ) );
		$out['kw'] = mb_substr( $kw, 0, 40 );
		return $out;
	}
}
