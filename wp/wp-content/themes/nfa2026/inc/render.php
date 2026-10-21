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
	$map = array( 'association' => 'info', 'office' => 'info', 'referee' => 'ref', 'technical' => 'tech', 'medical' => 'tech', 'grassroots' => 'kids' );
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
function nfa_nc( $post, $group = null, $hidden = false ) {
	list( $label, $color ) = nfa_badge( $post );
	return sprintf(
		'<a class="nc" data-c="%1$s" href="%2$s"%8$s><div class="th"><img alt="" src="%3$s"/><span class="badge" style="background:%4$s">%5$s</span></div><div class="dt">%6$s</div><h3>%7$s</h3></a>',
		esc_attr( null === $group ? nfa_news_group( $post ) : $group ),
		esc_url( nfa_post_url( $post ) ),
		esc_url( nfa_thumb( $post ) ),
		esc_attr( $color ),
		esc_html( $label ),
		esc_html( get_the_date( 'Y.m.d', $post ) ),
		esc_html( get_the_title( $post ) ),
		$hidden ? ' hidden' : ''
	);
}

/**
 * TOP の NEWS：タブ（div.tabs）とカード（div.ngrid）。タブの中身は nfa_news_tabs()（inc/data.php）。
 * タブごとに最新の記事を読み込み、カードの data-c に「その記事が入るタブのキー」を並べる。
 * 切り替えはデザインの main.js（[data-filter] と data-f／data-c）がそのまま行う。最初のタブ以外のカードは hidden で始める。
 */
function nfa_news_tabs_html() {
	$tabs  = nfa_news_tabs();
	$first = (string) array_key_first( $tabs );
	$cards = array();   // 投稿ID => [ 投稿, タブのキー[] ]
	foreach ( $tabs as $key => $t ) {
		$posts = nfa_news(
			array(
				'committee' => $t['committee'] ?? array(),
				'type'      => $t['type'] ?? array(),
				'n'         => (int) ( $t['n'] ?? 8 ),
				'fresh'     => true,
			)
		);
		foreach ( $posts as $p ) {
			$cards[ $p->ID ][0]   = $p;
			$cards[ $p->ID ][1][] = $key;
		}
	}
	uasort( $cards, fn( $a, $b ) => strcmp( $b[0]->post_date, $a[0]->post_date ) );   // 新しい順

	$out = '<div aria-label="ニュースの種類" class="tabs" data-filter="ngrid" role="tablist">';
	foreach ( $tabs as $key => $t ) {
		$out .= sprintf( '<button aria-selected="%s" data-f="%s" role="tab">%s</button>', $key === $first ? 'true' : 'false', esc_attr( $key ), esc_html( $t['label'] ) );
	}
	$out .= '</div><div class="ngrid" id="ngrid">';
	foreach ( $cards as $c ) {
		$out .= nfa_nc( $c[0], implode( ' ', $c[1] ), ! in_array( $first, $c[1], true ) );
	}
	return $out . '</div>';
}

/**
 * 資料ボタン（a.fbtn / 準備中は span.fbtn.is-pend）。
 */
function nfa_fbtn( array $r ) {
	$label = '' !== $r['label'] ? $r['label'] : ( '' !== $r['section'] ? $r['section'] : '資料' );
	if ( $r['pending'] ) {
		return '<span class="fbtn is-pend" title="まだ掲載されていない資料"><i class="l">準備中</i>' . esc_html( $label ) . '</span>';
	}
	list( $cls, $ext ) = nfa_file_kind( $r['url'] );
	return sprintf( '<a class="fbtn" href="%s" target="_blank" rel="noopener"><i class="%s">%s</i>%s</a>', esc_url( $r['url'] ), esc_attr( $cls ), esc_html( $ext ), esc_html( $label ) );
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
	$out = '<div class="spons">';
	foreach ( $s['gold'] as $r ) {
		$out .= nfa_sponsor_a( $r, 'gold', '<small>GOLD PARTNER</small>' . nfa_sponsor_logo( $r ) );
	}
	foreach ( $s['official'] as $r ) {
		$out .= nfa_sponsor_a( $r, '', nfa_sponsor_logo( $r ) );
	}
	$out .= '</div><div class="supp"><b>SUPPORTERS</b>';
	foreach ( $s['supporters'] as $r ) {
		// URL が登録されていればリンクにする
		$name = esc_html( $r['name'] );
		$out .= '<span>' . ( ! empty( $r['url'] ) ? '<a href="' . esc_url( $r['url'] ) . '" target="_blank" rel="noopener">' . $name . '</a>' : $name ) . '</span>';
	}
	return $out . '</div>';
}

/**
 * 協賛企業のロゴ（設定のロゴ → テーマの img/sp-{key}.png → 名前の文字）。
 */
function nfa_sponsor_logo( array $r ) {
	$src = ! empty( $r['logo'] ) ? $r['logo'] : ( ! empty( $r['key'] ) ? nfa_asset( 'img/sp-' . $r['key'] . '.png' ) : '' );
	return $src ? '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $r['name'] ) . '" loading="lazy">' : '<span class="nm">' . esc_html( $r['name'] ) . '</span>';
}

/**
 * 協賛企業のリンク（URL がなければ div）。
 */
function nfa_sponsor_a( array $r, $class, $inner ) {
	$attr = ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . ( ! empty( $r['key'] ) ? ' data-sp="' . esc_attr( $r['key'] ) . '"' : '' );
	if ( empty( $r['url'] ) ) {
		return '<div' . $attr . '>' . $inner . '</div>';
	}
	return '<a' . $attr . ' href="' . esc_url( $r['url'] ) . '" target="_blank" rel="noopener">' . $inner . '</a>';
}

/**
 * 協賛ページのロゴの並び（ul.pt-logos の中）。$tier = gold | official
 */
function nfa_partner_logos_html( $tier ) {
	$out = '';
	foreach ( nfa_sponsor_list()[ $tier ] ?? array() as $r ) {
		$out .= '<li>' . nfa_sponsor_a( $r, '', nfa_sponsor_logo( $r ) . ( ! empty( $r['logo'] ) || ! empty( $r['key'] ) ? '<span class="nm">' . esc_html( $r['name'] ) . '</span>' : '' ) ) . '</li>';
	}
	return $out;
}

/**
 * 協賛ページのサポーター（ul.pt-sup の中）。
 */
function nfa_partner_sup_html() {
	$out = '';
	foreach ( nfa_sponsor_list()['supporters'] ?? array() as $r ) {
		$out .= '<li>' . ( ! empty( $r['url'] ) ? '<a href="' . esc_url( $r['url'] ) . '" rel="noopener" target="_blank">' . esc_html( $r['name'] ) . '</a>' : esc_html( $r['name'] ) ) . '</li>';
	}
	return $out;
}

/**
 * ページ送り（デザインの nav.pager）。一覧（お知らせ・カテゴリー・アーカイブ）で共通。
 */
function nfa_pager() {
	$pages = paginate_links( array( 'type' => 'array', 'prev_text' => '前へ', 'next_text' => '次へ', 'mid_size' => 1 ) );
	return $pages ? '<nav class="center pager" aria-label="ページ送り">' . implode( ' ', $pages ) . '</nav>' : '';
}

/* ---------- ファイル・旧サイトへのリンク ---------- */

/**
 * ファイルの種類の表示（[クラス, 表記]）。デザインの PDF／XLS（x）／DOC（w）／LINK（l）。
 */
function nfa_file_kind( $url ) {
	$ext = strtolower( (string) pathinfo( (string) wp_parse_url( (string) $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	if ( 'pdf' === $ext ) {
		return array( '', 'PDF' );
	}
	if ( in_array( $ext, array( 'xls', 'xlsx', 'csv' ), true ) ) {
		return array( 'x', 'XLS' );
	}
	if ( in_array( $ext, array( 'doc', 'docx' ), true ) ) {
		return array( 'w', 'DOC' );
	}
	if ( in_array( $ext, array( 'ppt', 'pptx', 'zip' ), true ) ) {
		return array( 'o', strtoupper( $ext ) );
	}
	return array( 'l', 'LINK' );
}

/**
 * 旧サイト（www.nagano-fa.or.jp）の URL を、新サイトの中の URL に読み替える（NFA Committee の読み替え表）。
 */
function nfa_legacy_link( $url ) {
	return function_exists( 'nfa_legacy_url' ) ? nfa_legacy_url( $url ) : $url;
}

/**
 * href 属性を出す。読み替えたあとも外部のサイトなら別タブで開く。
 */
function nfa_href( $url ) {
	$url  = nfa_legacy_link( $url );
	$host = wp_parse_url( $url, PHP_URL_HOST );
	$out  = 'href="' . esc_url( $url ) . '"';
	if ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) {
		$out .= ' rel="noopener" target="_blank"';
	} elseif ( preg_match( '#/wp-content/uploads/#', $url ) ) {
		$out .= ' target="_blank"';   // 取り込んだファイルも別タブで
	}
	echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Contact Form 7 のフォーム（題名で探す）。項目・クラス名はデザインと同じ（inc/*-form.txt）。
 */
function nfa_cf7( $title, $class = 'form' ) {
	$cf7 = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'title' => $title, 'posts_per_page' => 1 ) );
	if ( $cf7 && shortcode_exists( 'contact-form-7' ) ) {
		return do_shortcode( '[contact-form-7 id="' . (int) $cf7[0]->ID . '" html_class="' . esc_attr( $class ) . '"]' );
	}
	return current_user_can( 'manage_options' )
		? '<p class="lead2">Contact Form 7 のフォーム「' . esc_html( $title ) . '」が見つかりません。<code>wp nfa-committee setup-pages</code> で作成できます。</p>'
		: '<p class="lead2">ただいまフォームを準備しています。</p>';
}

/**
 * 固定ページのフォーム欄：固定ページの本文（Contact Form 7 のショートコード）を出す。
 * 本文が空のときだけ、題名 $title のフォームを出す（移行中に消えないように）。
 * 年度などでフォームを替えるときは、本文のショートコードを入れ替える。
 * ショートコードには、デザインのスタイルが当たるよう html_class を付ける（例：html_class="form rj-form"）。
 */
function nfa_page_form( $title, $class = 'form' ) {
	$content = (string) get_post_field( 'post_content', get_queried_object_id() );
	if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
		return nfa_cf7( $title, $class );
	}
	return apply_filters( 'the_content', $content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
}

/* ---------- 事業計画・事業報告 ---------- */

function nfa_reports( $years = 0 ) {
	return class_exists( 'NFA_Com_Report_Type' ) ? NFA_Com_Report_Type::by_year( $years ) : array();
}

/**
 * 資料の li（ファイルがなければ「掲載なし」）。
 */
function nfa_report_li( array $it ) {
	if ( '' === $it['url'] ) {
		return '<li><span class="rp-na"><span class="fbtn-i">—</span><span>' . esc_html( $it['title'] ) . '</span><small>掲載なし</small></span></li>';
	}
	list( $cls, $ext ) = nfa_file_kind( $it['url'] );
	return '<li><a href="' . esc_url( $it['url'] ) . '" target="_blank"><span class="fbtn-i' . ( $cls ? ' ' . esc_attr( $cls ) : '' ) . '">' . esc_html( $ext ) . '</span><span>' . esc_html( $it['title'] ) . '</span></a></li>';
}

/**
 * 協会についての DOCUMENTS（最新の2年）。
 */
function nfa_reports_about_html() {
	$out = '<div class="ab-yr">';
	foreach ( nfa_reports( 2 ) as $g ) {
		$out .= '<div><h3 class="ab-yh">' . esc_html( $g['year'] ) . '年</h3><ul class="dl">' . implode( '', array_map( 'nfa_report_li', $g['items'] ) ) . '</ul>';
		$out .= $g['note'] ? '<p class="ab-note">' . esc_html( $g['note'] ) . '</p>' : '';
		$out .= '</div>';
	}
	return $out . '</div>';
}

/**
 * 事業計画・事業報告ページのページ内メニュー（新しい6年は1年ずつ、それより前はまとめて）。
 */
function nfa_reports_lnav_html() {
	$years = wp_list_pluck( nfa_reports(), 'year' );
	$items = array();
	foreach ( array_slice( $years, 0, 6 ) as $y ) {
		$items[] = array( $y, $y . '年' );
	}
	$rest = array_slice( $years, 6 );
	if ( $rest ) {
		$items[] = array( $rest[0], ( count( $rest ) > 1 ? end( $rest ) . '〜' . $rest[0] : $rest[0] ) . '年' );
	}
	$out = '<nav aria-label="ページ内メニュー" class="lnav"><div class="lnav-in"><span class="lnav-lb">CONTENTS</span><ul>';
	foreach ( $items as $i => $it ) {
		$out .= sprintf( '<li><a href="#y%d"><span class="no">%02d</span><span class="t">%s</span></a></li>', $it[0], $i + 1, esc_html( $it[1] ) );
	}
	return $out . '</ul></div></nav>';
}

/**
 * 事業計画・事業報告の年ごとの欄。
 */
function nfa_reports_grid_html( $offset, $limit, $class ) {
	$groups = array_slice( nfa_reports(), $offset, $limit ? $limit : null );
	$out    = '<div class="' . esc_attr( $class ) . '">';
	foreach ( $groups as $g ) {
		$out .= '<div class="rp-y" id="y' . (int) $g['year'] . '"><h3 class="rp-yh">' . esc_html( $g['year'] ) . '<small>年</small></h3><ul class="dl fx-dl1">' . implode( '', array_map( 'nfa_report_li', $g['items'] ) ) . '</ul>';
		$out .= $g['note'] ? '<p class="fx-note">' . esc_html( $g['note'] ) . '</p>' : '';
		$out .= '</div>';
	}
	return $out . '</div>';
}

/**
 * チームの1件（ul.tm > li）。data-c はデザインの絞り込みの値（地域・競技・年代・性別）。
 */
function nfa_team_li( $team ) {
	$c        = nfa_team_codes( $team );
	$area     = implode( '・', nfa_team_terms( $team, 'team_area' ) );
	$city     = (string) nfa_team_field( $team, 'team_city' );
	$location = (string) nfa_team_field( $team, 'team_activity_location' );
	$site     = (string) nfa_team_field( $team, 'team_website_url' );
	$tags     = array_merge( nfa_team_terms( $team, 'team_sport' ), nfa_team_terms( $team, 'team_age' ), array_filter( array( nfa_team_gender( $team ) ) ) );
	$title    = get_the_title( $team );
	$url      = get_permalink( $team );
	return sprintf(
		'<li data-c="%1$s"><span aria-hidden="true" class="em">%2$s</span><span><b><a class="tm-nm" href="%6$s">%3$s</a></b><span>%4$s</span><span class="tg">%5$s</span><span class="lp-lk"><a class="lp-tlink" href="%6$s">チーム情報（活動日・活動頻度）</a>%7$s</span></span></li>',
		esc_attr( implode( ' ', $c ) ),
		esc_html( nfa_team_initials( $title ) ),
		esc_html( $title ),
		esc_html( trim( $area . ( $city ? '・' . $city : '' ) . ( $location ? '／' . $location : '' ), '・／' ) ),
		implode( '', array_map( fn( $t ) => '<i>' . esc_html( $t ) . '</i>', $tags ) ),
		esc_url( $url ),
		$site ? '<a class="lp-tlink" href="' . esc_url( $site ) . '" rel="noopener" target="_blank">チームWebサイト</a>' : ''
	);
}

/**
 * チームの絞り込みの値（デザインの data-c：地域・競技・年代・性別）。
 */
function nfa_team_codes( $team ) {
	$codes = array(
		'hokushin' => 'hokushin', 'toushin' => 'toshin', 'chushin' => 'chushin', 'nanshin' => 'nanshin',
		'soccer' => 'soc', 'futsal' => 'fut',
		'preschool' => 'a0', 'elem-low' => 'a1', 'elem-high' => 'a2', 'junior' => 'a3', 'high' => 'a4', 'adult' => 'a5',
		'over40' => 'a40', 'over50' => 'a50', 'over60' => 'a60', 'over70' => 'a70',
		'male' => 'm', 'female' => 'f', 'both' => 'm f',
	);
	$c = array();
	foreach ( array( 'team_area', 'team_sport', 'team_gender', 'team_age' ) as $tax ) {
		foreach ( nfa_team_terms( $team, $tax, 'slugs' ) as $slug ) {
			if ( isset( $codes[ $slug ] ) ) {
				$c = array_merge( $c, explode( ' ', $codes[ $slug ] ) );
			}
		}
	}
	return array_values( array_unique( $c ) );
}

/**
 * 募集性別の表記（両方 → 男子・女子）。
 */
function nfa_team_gender( $team ) {
	$slugs = nfa_team_terms( $team, 'team_gender', 'slugs' );
	if ( in_array( 'both', $slugs, true ) || ( in_array( 'male', $slugs, true ) && in_array( 'female', $slugs, true ) ) ) {
		return '男子・女子';
	}
	return implode( '・', nfa_team_terms( $team, 'team_gender' ) );
}

/**
 * チーム名の頭文字（一覧の丸印）。記号と先頭の FC・SC を除いた最初の2文字。
 */
function nfa_team_initials( $title ) {
	$t = preg_replace( '/[^\p{L}\p{N}]/u', '', mb_convert_kana( (string) $title, 'as' ) );
	$t = preg_replace( '/^(FC|SC|AC)/i', '', $t );
	return mb_strtoupper( mb_substr( '' !== $t ? $t : (string) $title, 0, 2 ) );
}
