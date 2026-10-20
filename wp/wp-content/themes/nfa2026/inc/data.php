<?php
/**
 * テンプレートで使うデータの取り出し。プラグイン（NFA Committee / NFA AI Search）の関数があれば使い、
 * なければデザインの初期値で表示が崩れないようにする。
 *
 * @package NFA2026
 */

defined( 'ABSPATH' ) || exit;

const NFA_COMMITTEE_TAX = 'nfa_committee';
const NFA_DOC_TYPE      = 'nfa_doc';

/**
 * 委員会ごとの入力欄（ja / tg / en / badge / color / layout / lead / tel / legacy / archive / target）。
 */
function nfa_term_meta( $term, $key ) {
	if ( function_exists( 'nfa_committee_meta' ) ) {
		return nfa_committee_meta( $term, $key );
	}
	return '';
}

/**
 * 担当委員会（並び順どおり）。
 *
 * @return WP_Term[]
 */
function nfa_committees() {
	if ( class_exists( 'NFA_Com_Committees' ) ) {
		return NFA_Com_Committees::all();
	}
	$terms = taxonomy_exists( NFA_COMMITTEE_TAX ) ? get_terms( array( 'taxonomy' => NFA_COMMITTEE_TAX, 'hide_empty' => false ) ) : array();
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * 投稿の担当委員会（最初の1つ）。
 */
function nfa_committee_of( $post = null ) {
	$terms = get_the_terms( get_post( $post ), NFA_COMMITTEE_TAX );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}
	// 委員会と「協会」の両方が付いた記事は、委員会の方を代表にする
	foreach ( $terms as $t ) {
		if ( ! in_array( $t->slug, array( 'association', 'office' ), true ) ) {
			return $t;
		}
	}
	return $terms[0];
}

/**
 * 一覧のバッジ [表記, 色]。
 */
function nfa_badge( $post = null ) {
	$post = get_post( $post );
	if ( $post && function_exists( 'nfa_flag' ) && nfa_flag( 'important', $post ) ) {
		return array( '重要', 'var(--alert)' );
	}
	$t = nfa_committee_of( $post );
	if ( ! $t ) {
		return array( 'お知らせ', 'var(--navy)' );
	}
	$label = (string) nfa_term_meta( $t, 'badge' );
	$color = (string) nfa_term_meta( $t, 'color' );
	return array( $label ? $label : $t->name, $color ? $color : 'var(--navy)' );
}

/**
 * サムネイル（なければデザインの仮画像）。
 */
function nfa_thumb( $post = null, $size = 'medium_large', $fallback = 'img/thumb-default.jpg' ) {
	$post = get_post( $post );
	$url  = get_the_post_thumbnail_url( $post, $size );
	if ( ! $url && $post ) {
		// 旧サイトから移したアイキャッチ（メディアに取り込むまでは旧サイトの URL）
		$url = (string) get_post_meta( $post->ID, '_nfa_thumb_url', true );
	}
	return $url ? $url : nfa_asset( $fallback );
}

/**
 * 記事の行き先。「リンク先URL」があればそのURL（外部ページの紹介記事など）。
 */
function nfa_post_url( $post = null ) {
	$post = get_post( $post );
	$link = $post && function_exists( 'nfa_field' ) ? (string) nfa_field( 'nfa_link_url', $post->ID ) : '';
	return $link ? $link : get_permalink( $post );
}

/**
 * 開催日の表示（「開催日の表記」があればそれ、なければ開催日を Y.m.d で）。
 */
function nfa_event_label( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || ! function_exists( 'nfa_field' ) ) {
		return '';
	}
	$text = trim( (string) nfa_field( 'nfa_event_text', $post->ID ) );
	if ( '' !== $text ) {
		return $text;
	}
	$date = (string) nfa_field( 'nfa_event_date', $post->ID );
	return $date && function_exists( 'nfa_date_ymd' ) ? '開催日：' . nfa_date_ymd( $date ) : '';
}

/**
 * お知らせ（投稿）。
 *
 * @param array $args committee（スラッグ・配列可）, n, flag（important|alert|top）, exclude
 * @return WP_Post[]
 */
function nfa_news( array $args = array() ) {
	$args = wp_parse_args( $args, array( 'committee' => '', 'n' => 5, 'flag' => '', 'exclude' => array(), 'fresh' => false ) );
	$q    = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $args['n'],
		'ignore_sticky_posts' => true,
		'post__not_in'        => (array) $args['exclude'],
		'no_found_rows'       => true,
	);
	if ( $args['committee'] ) {
		$q['tax_query'] = array( array( 'taxonomy' => NFA_COMMITTEE_TAX, 'field' => 'slug', 'terms' => (array) $args['committee'] ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	$today = wp_date( 'Ymd' );
	$meta  = array();
	if ( $args['flag'] && class_exists( 'NFA_Com_Flags' ) ) {
		$meta[] = array( 'key' => NFA_Com_Flags::META[ $args['flag'] ], 'value' => '1' );
		if ( 'top' === $args['flag'] ) {
			// 「TOPに掲載する期限」を過ぎたものは外す
			$meta[] = array(
				'relation' => 'OR',
				array( 'key' => 'nfa_top_until', 'compare' => 'NOT EXISTS' ),
				array( 'key' => 'nfa_top_until', 'value' => '' ),
				array( 'key' => 'nfa_top_until', 'value' => $today, 'compare' => '>=' ),
			);
		}
	}
	if ( $args['fresh'] ) {
		// 「新着」掲載終了日を過ぎたものは、TOPの NEWS・PICK UP から外す
		$meta[] = array(
			'relation' => 'OR',
			array( 'key' => 'nfa_new_until', 'compare' => 'NOT EXISTS' ),
			array( 'key' => 'nfa_new_until', 'value' => '' ),
			array( 'key' => 'nfa_new_until', 'value' => $today, 'compare' => '>=' ),
		);
	}
	if ( $meta ) {
		$q['meta_query'] = array_merge( array( 'relation' => 'AND' ), $meta ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	return get_posts( $q );
}

/**
 * 大会・リーグ資料（nfa_doc）。
 *
 * @param array $args committee, kind（tournament|league）, n
 * @return WP_Post[]
 */
function nfa_docs( array $args = array() ) {
	if ( ! post_type_exists( NFA_DOC_TYPE ) ) {
		return array();
	}
	$args = wp_parse_args( $args, array( 'committee' => '', 'kind' => '', 'year' => 0, 'n' => -1 ) );
	$q    = array(
		'post_type'      => NFA_DOC_TYPE,
		'post_status'    => 'publish',
		'posts_per_page' => $args['n'],
		'orderby'        => 'modified',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	);
	if ( $args['committee'] ) {
		$q['tax_query'] = array( array( 'taxonomy' => NFA_COMMITTEE_TAX, 'field' => 'slug', 'terms' => (array) $args['committee'] ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	$meta = array();
	if ( $args['kind'] ) {
		$meta[] = array( 'key' => 'nfa_doc_kind', 'value' => $args['kind'] );
	}
	if ( $args['year'] ) {
		$meta[] = array( 'key' => 'nfa_doc_year', 'value' => (int) $args['year'] );
	}
	if ( $meta ) {
		$q['meta_query'] = $meta; // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	return get_posts( $q );
}

/**
 * 委員会の大会・リーグ資料がある年度（新しい順）。
 *
 * @return int[]
 */
function nfa_doc_years( $committee ) {
	global $wpdb;
	$term = get_term_by( 'slug', $committee, NFA_COMMITTEE_TAX );
	if ( ! $term ) {
		return array();
	}
	$years = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT DISTINCT CAST(m.meta_value AS UNSIGNED) y FROM {$wpdb->postmeta} m
			 JOIN {$wpdb->posts} p ON p.ID = m.post_id AND p.post_type = %s AND p.post_status = 'publish'
			 JOIN {$wpdb->term_relationships} r ON r.object_id = p.ID
			 JOIN {$wpdb->term_taxonomy} t ON t.term_taxonomy_id = r.term_taxonomy_id AND t.term_id = %d
			 WHERE m.meta_key = 'nfa_doc_year' AND m.meta_value <> '' ORDER BY y DESC",
			NFA_DOC_TYPE,
			$term->term_id
		)
	);
	return array_values( array_filter( array_map( 'intval', $years ) ) );
}

/**
 * 資料の行（プラグインの nfa_doc_rows()。区分・対象・資料名・URL・更新日・準備中）。
 */
function nfa_rows( $post ) {
	return function_exists( 'nfa_doc_rows' ) ? nfa_doc_rows( $post ) : array();
}

/**
 * 「最新の更新」：資料の行を更新日の新しい順に。TOPの日程タブと委員会ページの LATEST UPDATE に使う。
 *
 * @return array[] { committee, kind（結果|資料）, date, title, sc（区分・対象）, url }
 */
function nfa_latest_rows( $committee = '', $n = 10 ) {
	$out = array();
	foreach ( nfa_docs( array( 'committee' => $committee, 'n' => 40 ) ) as $doc ) {
		$c = nfa_committee_of( $doc );
		foreach ( nfa_rows( $doc ) as $r ) {
			// 準備中の行と、更新日が入っていない行は「最新の更新」に出さない
			if ( $r['pending'] || '' === $r['updated'] ) {
				continue;
			}
			$txt   = $r['section'] . ' ' . $r['label'];
			$out[] = array(
				'committee' => $c ? $c->slug : '',
				'kind'      => preg_match( '/結果|星取|順位|成績/u', $txt ) ? '結果' : '資料',
				'date'      => $r['updated'],
				'title'     => get_the_title( $doc ),
				'sc'        => trim( $r['section'] . ' ' . $r['target'] ),
				'url'       => $r['url'],
			);
		}
	}
	usort( $out, fn( $a, $b ) => strcmp( $b['date'], $a['date'] ) );
	return array_slice( $out, 0, $n );
}

/**
 * 委員会の最終更新日（お知らせ・資料の新しいほう）。
 */
function nfa_last_updated( $committee ) {
	$dates = array();
	foreach ( nfa_news( array( 'committee' => $committee, 'n' => 1 ) ) as $p ) {
		$dates[] = get_the_date( 'Y.m.d', $p );
	}
	foreach ( nfa_latest_rows( $committee, 1 ) as $r ) {
		$dates[] = $r['date'];
	}
	rsort( $dates );
	return $dates[0] ?? '';
}

/**
 * 「TOPに掲載」の記事（なければ新しい順）。
 */
function nfa_top_posts( $n = 4 ) {
	$posts = nfa_news( array( 'flag' => 'top', 'n' => $n ) );
	if ( count( $posts ) < $n ) {
		$posts = array_merge( $posts, nfa_news( array( 'n' => $n - count( $posts ), 'exclude' => wp_list_pluck( $posts, 'ID' ), 'fresh' => true ) ) );
	}
	return $posts;
}

/**
 * 重要なお知らせ（最新の1件）。
 */
function nfa_important_post() {
	$p = nfa_news( array( 'flag' => 'important', 'n' => 1 ) );
	return $p[0] ?? null;
}

/**
 * 協賛企業（プラグインの設定。なければデザインの並び）。
 */
function nfa_sponsor_list() {
	if ( function_exists( 'nfa_sponsors' ) ) {
		return nfa_sponsors();
	}
	return array( 'gold' => array(), 'official' => array(), 'supporters' => array() );
}

/**
 * チーム（活動地域の順、同じ地域は名前順）。
 *
 * @return WP_Post[]
 */
function nfa_teams() {
	if ( ! post_type_exists( 'team' ) ) {
		return array();
	}
	$teams = get_posts( array( 'post_type' => 'team', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
	$order = array( 'hokushin', 'toushin', 'chushin', 'nanshin' );
	$area  = function ( $p ) use ( $order ) {
		$t = get_the_terms( $p, 'team_area' );
		$i = ( $t && ! is_wp_error( $t ) ) ? array_search( $t[0]->slug, $order, true ) : false;
		return false === $i ? 99 : $i;
	};
	usort( $teams, fn( $a, $b ) => ( $area( $a ) <=> $area( $b ) ) ?: strcmp( $a->post_title, $b->post_title ) );
	return $teams;
}

/**
 * チームの分類（名前の配列）。
 */
function nfa_team_terms( $post, $tax, $field = 'names' ) {
	$t = wp_get_object_terms( get_post( $post )->ID, $tax );
	if ( is_wp_error( $t ) ) {
		return array();
	}
	// 定義の並び順（未就学児 → 社会人 など）
	$order = class_exists( 'NFA_Com_Team_Type' ) ? array_keys( NFA_Com_Team_Type::taxonomies()[ $tax ][1] ?? array() ) : array();
	$pos = function ( $slug ) use ( $order ) {
		$i = array_search( $slug, $order, true );
		return false === $i ? 99 : $i;
	};
	usort( $t, fn( $a, $b ) => $pos( $a->slug ) <=> $pos( $b->slug ) );
	return wp_list_pluck( $t, 'slugs' === $field ? 'slug' : 'name' );
}

/**
 * チームの入力欄（ACF。なければ保存値を直接読む）。
 */
function nfa_team_field( $post, $key ) {
	return function_exists( 'nfa_field' ) ? nfa_field( $key, get_post( $post )->ID ) : get_post_meta( get_post( $post )->ID, $key, true );
}
