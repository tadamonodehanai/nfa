<?php
/**
 * 繰り返し使う部品の出力（マークアップはデザイン NFA_site_design と同じ）。
 *
 * @package NFA2026
 */

defined( 'ABSPATH' ) || exit;

function nfa_arrow() {
	return '<span aria-hidden="true" class="go"><svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span>';
}

function nfa_btn_arrow() {
	return '<span aria-hidden="true" class="arr"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span>';
}

/**
 * TOPのNEWSタブの区分（デザインの data-c）。
 */
function nfa_news_group( $post ) {
	$t = nfa_committee_of( $post );
	$s = $t ? $t->slug : 'office';
	$map = array( 'office' => 'info', 'referee' => 'ref', 'technical' => 'tech', 'medical' => 'tech', 'grassroots' => 'kids' );
	return $map[ $s ] ?? 'match';
}

/**
 * 「最新の更新」の1件（a.sch）。
 */
function nfa_sch( array $r ) {
	return sprintf(
		'<a class="sch" data-c="%1$s" href="%2$s" target="_blank" rel="noopener"><span class="t"><b class="%3$s">%4$s</b><span>%5$s 更新</span></span><span class="m">%6$s</span>%7$s%8$s</a>',
		esc_attr( $r['committee'] ),
		esc_url( $r['url'] ),
		'結果' === $r['kind'] ? 'res' : 'pre',
		esc_html( $r['kind'] ),
		esc_html( $r['date'] ),
		esc_html( $r['title'] ),
		'' !== $r['sc'] ? '<span class="sc">' . esc_html( $r['sc'] ) . '</span>' : '',
		nfa_arrow()
	);
}

/**
 * お知らせの1件（ul.nl > li）。
 */
function nfa_news_li( $post ) {
	list( $label, $color ) = nfa_badge( $post );
	return sprintf(
		'<li data-c="%1$s"><a href="%2$s"><span class="th"><img alt="" loading="lazy" src="%3$s"/><span class="badge" style="background:%4$s">%5$s</span></span><span><span class="mt"><span class="dt">%6$s</span></span><h3>%7$s</h3></span><span aria-hidden="true" class="ar"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></li>',
		esc_attr( nfa_committee_of( $post ) ? nfa_committee_of( $post )->slug : 'office' ),
		esc_url( nfa_post_url( $post ) ),
		esc_url( nfa_thumb( $post ) ),
		esc_attr( $color ),
		esc_html( $label ),
		esc_html( get_the_date( 'Y.m.d', $post ) ),
		esc_html( get_the_title( $post ) )
	);
}

/**
 * 写真なしのお知らせ（ul.nl.no-th > li）。
 */
function nfa_news_li_noth( $post ) {
	return sprintf(
		'<li><a href="%1$s"><span><span class="mt"><span class="dt">%2$s</span></span><h3>%3$s</h3></span><span aria-hidden="true" class="ar"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></span></a></li>',
		esc_url( nfa_post_url( $post ) ),
		esc_html( get_the_date( 'Y.m.d', $post ) ),
		esc_html( get_the_title( $post ) )
	);
}

/**
 * TOPのNEWSカード（a.nc）。
 */
function nfa_nc( $post ) {
	list( $label, $color ) = nfa_badge( $post );
	return sprintf(
		'<a class="nc" data-c="%1$s" href="%2$s"><div class="th"><img alt="" src="%3$s"/><span class="badge" style="background:%4$s">%5$s</span></div><div class="dt">%6$s</div><h3>%7$s</h3></a>',
		esc_attr( nfa_news_group( $post ) ),
		esc_url( nfa_post_url( $post ) ),
		esc_url( nfa_thumb( $post ) ),
		esc_attr( $color ),
		esc_html( $label ),
		esc_html( get_the_date( 'Y.m.d', $post ) ),
		esc_html( get_the_title( $post ) )
	);
}

/**
 * 資料ボタン（a.fbtn / 準備中は span.fbtn.is-pend）。
 */
function nfa_fbtn( array $r ) {
	$label = '' !== $r['label'] ? $r['label'] : ( '' !== $r['section'] ? $r['section'] : '資料' );
	if ( $r['pending'] ) {
		return '<span class="fbtn is-pend" title="まだ掲載されていない資料"><i class="l">準備中</i>' . esc_html( $label ) . '</span>';
	}
	$ext = strtoupper( (string) pathinfo( (string) wp_parse_url( $r['url'], PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	$ext = in_array( $ext, array( 'PDF', 'XLSX', 'XLS', 'DOCX', 'DOC' ), true ) ? $ext : 'LINK';
	return sprintf( '<a class="fbtn" href="%s" target="_blank" rel="noopener"><i class="">%s</i>%s</a>', esc_url( $r['url'] ), esc_html( $ext ), esc_html( $label ) );
}

/**
 * 大会（article.trn）。区分ごとにまとめてボタンを並べる。
 */
function nfa_trn( $doc ) {
	$rows   = nfa_rows( $doc );
	$groups = array();
	foreach ( $rows as $r ) {
		$groups[ $r['section'] ][] = $r;
	}
	$dates = array_filter( wp_list_pluck( $rows, 'updated' ) );
	rsort( $dates );
	$upd = $dates[0] ?? get_the_modified_date( 'Y.m.d', $doc );
	$li  = '';
	foreach ( $groups as $g ) {
		$li .= '<li><div class="gap">' . implode( '', array_map( 'nfa_fbtn', $g ) ) . '</div></li>';
	}
	return '<article class="trn"><div class="hd2"><div class="st"><span class="upd">' . esc_html( $upd ) . ' 更新</span></div><h3>' . esc_html( get_the_title( $doc ) ) . '</h3></div><ul class="trn-files">' . $li . '</ul></article>';
}

/**
 * リーグ（.acc-i）。区分・対象ごとに1行、資料ボタンを並べる。
 */
function nfa_league_acc( $doc, $i ) {
	$rows   = nfa_rows( $doc );
	$groups = array();
	foreach ( $rows as $r ) {
		$groups[ $r['section'] . "\t" . $r['target'] ][] = $r;
	}
	$dates = array_filter( wp_list_pluck( $rows, 'updated' ) );
	rsort( $dates );
	$upd = $dates[0] ?? get_the_modified_date( 'Y.m.d', $doc );
	$tr  = '';
	foreach ( $groups as $key => $g ) {
		list( $section, $target ) = explode( "\t", $key );
		$gd = array_filter( wp_list_pluck( $g, 'updated' ) );
		rsort( $gd );
		$tr .= sprintf(
			'<tr><th data-label="区分" scope="row">%s</th><td data-label="対象">%s</td><td data-label="資料"><div class="gap">%s</div></td><td class="num" data-label="更新">%s</td></tr>',
			esc_html( $section ),
			esc_html( $target ),
			implode( '', array_map( 'nfa_fbtn', $g ) ),
			esc_html( $gd ? substr( $gd[0], 5 ) : '' )
		);
	}
	$open = 0 === $i;
	return sprintf(
		'<div class="acc-i"><button aria-controls="lg%1$d" aria-expanded="%2$s" class="acc-b" data-acc="" type="button"><span class="t">%3$s</span><span class="s">%4$s 更新</span></button><div class="acc-p" id="lg%1$d"%5$s><div class="tbl-w"><table class="tbl"><thead><tr><th scope="col">区分</th><th scope="col">対象</th><th scope="col">資料</th><th scope="col">更新</th></tr></thead><tbody>%6$s</tbody></table></div></div></div>',
		(int) $i,
		$open ? 'true' : 'false',
		esc_html( get_the_title( $doc ) ),
		esc_html( $upd ),
		$open ? '' : ' hidden',
		$tr
	);
}

/**
 * 「ほかのカテゴリ」「カテゴリから探す」の一覧（.catgrid の中身）。
 */
function nfa_catgrid( $current = '' ) {
	$groups = array(
		'年代別'     => array( 'cat-4', 'cat-3', 'cat-2', 'cat-1' ),
		'種目・対象別' => array( 'cat-w', 'cat-f', 'cat-s', 'grassroots' ),
		'その他'     => array( 'referee', 'technical', 'medical' ),
	);
	$ft  = array( 'grassroots' => 'お知らせ・資料', 'medical' => 'お知らせ・資料', 'referee' => '講習会・資格・お知らせ', 'technical' => '講習会・資格・お知らせ' );
	$out = '';
	foreach ( $groups as $h => $slugs ) {
		$out .= '<h3 class="cg-h">' . esc_html( $h ) . '</h3>';
		foreach ( $slugs as $slug ) {
			$term = get_term_by( 'slug', $slug, NFA_COMMITTEE_TAX );
			$en   = $term ? nfa_term_meta( $term, 'en' ) : strtoupper( $slug );
			$tg   = $term ? nfa_term_meta( $term, 'tg' ) : '';
			$ja   = $term ? nfa_term_meta( $term, 'ja' ) : $slug;
			$cur  = $current === $slug;
			$out .= sprintf(
				'<a class="ct%1$s" href="%2$s"%3$s><span class="lb"><span class="en">%4$s</span>%5$s</span><b>%6$s</b><span class="ft2">%7$s%8$s</span></a>',
				$cur ? ' is-cur' : '',
				esc_url( nfa_committee_url( $slug ) ),
				$cur ? ' aria-current="page"' : '',
				esc_html( $en ),
				$tg ? '<span class="tg">' . esc_html( $tg ) . '</span>' : '',
				esc_html( $ja ),
				esc_html( $ft[ $slug ] ?? '日程・結果・お知らせ' ),
				$cur ? '' : '<i>→</i>'
			);
		}
	}
	return $out;
}

/**
 * お問い合わせ帯（section.cband）。
 */
function nfa_cband( $term = null ) {
	$tel = $term ? (string) nfa_term_meta( $term, 'tel' ) : '';
	$tel = $tel ? $tel : '0263-87-8565';
	if ( false === strpos( $tel, '（' ) ) {
		$tel .= '（事務局）';
	}
	return '<section class="cband"><div class="wrap"><div class="cband-tx"><p class="cband-e">CONTACT</p><h2>お問い合わせ</h2><p class="cband-info">（一社）長野県サッカー協会<br/>〒390-8565 <span class="ib">長野県松本市深志2丁目6-9 LON BLDG. 3F</span><br/>TEL ' . esc_html( $tel ) . '</p><p class="cband-note">大会・講習会の詳細は、各要項に記載の担当へお問い合わせください。</p></div><div class="cband-act"><a class="btn btn-g" href="' . esc_url( nfa_u( 'contact.html' ) ) . '"><span>お問い合わせ</span>' . nfa_btn_arrow() . '</a><a class="btn btn-l" href="' . esc_url( nfa_u( 'contact.html#request' ) ) . '"><span>掲載依頼</span>' . nfa_btn_arrow() . '</a></div></div></section>';
}

/**
 * 協賛企業（.spons と .supp）。
 */
function nfa_sponsors_html() {
	$s   = nfa_sponsor_list();
	$box = function ( $r, $class, $small ) {
		$name = esc_html( $r['name'] );
		$in   = '<small>' . esc_html( $small ) . '</small>' . $name;
		// デザインどおり文字だけ（リンクは協賛ページで出す）
		return '<div' . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . '>' . $in . '</div>';
	};
	$out = '<div class="spons">';
	foreach ( $s['gold'] as $r ) {
		$out .= $box( $r, 'gold', 'GOLD PARTNER' );
	}
	foreach ( $s['official'] as $r ) {
		$out .= $box( $r, '', 'OFFICIAL' );
	}
	$out .= '</div><div class="supp"><b>SUPPORTERS</b>';
	foreach ( $s['supporters'] as $r ) {
		$out .= '<span>' . esc_html( $r['name'] ) . '</span>';
	}
	return $out . '</div>';
}

/**
 * チームの1件（ul.tm > li）。data-c はデザインの絞り込みの値（地域・競技・年代・性別）。
 */
function nfa_team_li( $team ) {
	$codes = array(
		'hokushin' => 'hokushin', 'toushin' => 'toshin', 'chushin' => 'chushin', 'nanshin' => 'nanshin',
		'soccer' => 'soc', 'futsal' => 'fut',
		'preschool' => 'a0', 'elem-low' => 'a1', 'elem-high' => 'a2', 'junior' => 'a3', 'high' => 'a4', 'adult' => 'a5',
		'over40' => 'a40', 'over50' => 'a40', 'over60' => 'a40', 'over70' => 'a40',
		'male' => 'm', 'female' => 'f', 'both' => 'm f',
	);
	$c = array();
	foreach ( array( 'team_area', 'team_sport', 'team_age', 'team_gender' ) as $tax ) {
		foreach ( nfa_team_terms( $team, $tax, 'slugs' ) as $slug ) {
			if ( isset( $codes[ $slug ] ) ) {
				$c[] = $codes[ $slug ];
			}
		}
	}
	$area     = implode( '・', nfa_team_terms( $team, 'team_area' ) );
	$city     = (string) nfa_team_field( $team, 'team_city' );
	$location = (string) nfa_team_field( $team, 'team_activity_location' );
	$site     = (string) nfa_team_field( $team, 'team_website_url' );
	$tags     = array_merge( nfa_team_terms( $team, 'team_sport' ), nfa_team_terms( $team, 'team_age' ), nfa_team_terms( $team, 'team_gender' ) );
	$title    = get_the_title( $team );
	return sprintf(
		'<li data-c="%1$s"><span aria-hidden="true" class="em">%2$s</span><span><b>%3$s</b><span>%4$s</span><span class="tg">%5$s</span><span class="lp-lk"><a class="lp-tlink" href="%6$s">チーム情報（活動日・活動頻度）</a>%7$s</span></span></li>',
		esc_attr( implode( ' ', array_unique( explode( ' ', implode( ' ', $c ) ) ) ) ),
		esc_html( nfa_team_initials( $title ) ),
		esc_html( $title ),
		esc_html( trim( $area . ( $city ? '・' . $city : '' ) . ( $location ? '／' . $location : '' ), '・／' ) ),
		implode( '', array_map( fn( $t ) => '<i>' . esc_html( $t ) . '</i>', $tags ) ),
		esc_url( get_permalink( $team ) ),
		$site ? '<a class="lp-tlink" href="' . esc_url( $site ) . '" rel="noopener" target="_blank">チームWebサイト</a>' : ''
	);
}

/**
 * チーム名の頭文字（一覧の丸印）。記号と先頭の FC・SC を除いた最初の2文字。
 */
function nfa_team_initials( $title ) {
	$t = preg_replace( '/[^\p{L}\p{N}]/u', '', mb_convert_kana( (string) $title, 'as' ) );
	$t = preg_replace( '/^(FC|SC|AC)/i', '', $t );
	return mb_strtoupper( mb_substr( '' !== $t ? $t : (string) $title, 0, 2 ) );
}
