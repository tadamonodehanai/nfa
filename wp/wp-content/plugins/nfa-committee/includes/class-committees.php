<?php
/**
 * 担当委員会（nfa_committee）の12個と、委員会ごとの入力欄（タームメタ）。
 *
 * 分類そのものは NFA AI Search が登録する。スラッグはデザイン NFA_site_design のページ名に合わせている
 * （cat-4.html → cat-4）。委員会ページの URL は仮に /committee/{スラッグ}/。
 *
 * 委員会ごとの入力欄（未入力ならここの初期値）：
 *   ja      ページの和名（小学生）       tg     種別の表記（4種）     en   英字ラベル（U-12）
 *   badge   一覧のバッジ（4種・審判など） color  バッジの色（CSS変数）
 *   layout  ページの構成：full（大会＋リーグ）| tournament（大会のみ）| info（説明文中心）
 *   lead    ページ見出しの説明文         tel    問い合わせ帯の電話番号
 *   legacy  既存サイトの該当ページ       archive 既存サイトの過去年度（年度|URL を改行区切り）
 *   target  「対象」の初期値（AI検索の対象のスラッグ。委員会担当が対象を選ばなかったときに入る）
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

class NFA_Com_Committees {

	const TAX = 'nfa_committee';

	const FIELDS = array(
		'ja'      => '和名',
		'tg'      => '種別の表記',
		'en'      => '英字ラベル',
		'badge'   => 'バッジの表記',
		'color'   => 'バッジの色（CSS変数）',
		'layout'  => 'ページの構成',
		'lead'    => 'ページ見出しの説明文',
		'tel'     => '問い合わせ帯の電話番号',
		'legacy'  => '既存サイトの該当ページ',
		'archive' => '過去年度（年度|URL を1行ずつ）',
		'target'  => '「対象」の初期値（スラッグ）',
	);

	const LAYOUTS = array(
		'full'       => '大会・リーグ・お知らせ・過去年度',
		'tournament' => '大会・お知らせ・過去年度',
		'info'       => '説明文・お知らせ',
	);

	public static function definitions() {
		$office = '0263-87-8565';
		$arch   = function ( $slug, $suffix = '年度' ) {
			$out = array();
			foreach ( array( 2025, 2024, 2023 ) as $y ) {
				$out[] = $y . $suffix . '|https://www.nagano-fa.or.jp/' . $slug . '/' . $slug . '-results/archives-' . $y;
			}
			return implode( "\n", $out );
		};
		$cat = fn( $ja, $tg, $en, $layout, $legacy, $archive, $badge, $color = 'var(--navy)', $lead = '' ) => array(
			'ja'      => $ja,
			'tg'      => $tg,
			'en'      => $en,
			'badge'   => $badge,
			'color'   => $color,
			'layout'  => $layout,
			'lead'    => $lead ? $lead : $ja . '（' . ( $tg ? $tg : $ja . '委員会' ) . '）の大会・リーグの日程・試合結果と、お知らせを掲載しています。',
			'tel'     => $office,
			'legacy'  => $legacy,
			'archive' => $archive,
		);
		return array(
			'cat-4'      => array( 'name' => '4種', 'order' => 1, 'target' => 'cat-4' ) + $cat( '小学生', '4種', 'U-12', 'full', 'https://www.nagano-fa.or.jp/cat_4', $arch( 'cat_4' ), '4種' ),
			'cat-3'      => array( 'name' => '3種', 'order' => 2, 'target' => 'cat-3' ) + $cat( '中学生', '3種', 'U-15', 'full', 'https://www.nagano-fa.or.jp/cat_3', $arch( 'cat_3' ), '3種' ),
			'cat-2'      => array( 'name' => '2種', 'order' => 3, 'target' => 'cat-2' ) + $cat( '高校生', '2種', 'U-18', 'full', 'https://www.nagano-fa.or.jp/cat_2', $arch( 'cat_2' ), '2種' ),
			'cat-1'      => array( 'name' => '1種', 'order' => 4, 'target' => 'cat-1' ) + $cat( '社会人・大学', '1種', 'ADULT', 'full', 'https://www.nagano-fa.or.jp/cat_1', $arch( 'cat_1' ), '1種' ),
			'cat-w'      => array( 'name' => '女子', 'order' => 5, 'target' => 'cat-w' ) + $cat( '女子', '', 'WOMEN', 'tournament', 'https://www.nagano-fa.or.jp/cat_w', $arch( 'cat_w' ), '女子' ),
			'cat-f'      => array( 'name' => 'フットサル', 'order' => 6, 'target' => 'cat-f' ) + $cat( 'フットサル', '', 'FUTSAL', 'tournament', 'https://www.nagano-fa.or.jp/cat_f', $arch( 'cat_f', '年' ), 'フットサル', 'var(--tag-result)' ),
			'cat-s'      => array( 'name' => 'シニア', 'order' => 7, 'target' => 'cat-s' ) + $cat( 'シニア', '', 'SENIOR', 'tournament', 'https://www.nagano-fa.or.jp/cat_s', $arch( 'cat_s', '年の' ), 'シニア' ),
			'grassroots' => array( 'name' => 'キッズ', 'order' => 8, 'target' => 'grassroots' ) + $cat( 'キッズ', '', 'KIDS', 'info', 'https://www.nagano-fa.or.jp/grass-roots', '', 'グラスルーツ', 'var(--tag-event)', 'キッズ・グラスルーツ（未就学児〜U-10年代）の活動と、フェスティバルなどのお知らせを掲載しています。' ),
			'referee'    => array( 'name' => '審判', 'order' => 9, 'target' => 'referee', 'tel' => '0263-50-8330（審判委員会）' ) + $cat( '審判', '', 'REFEREE', 'info', 'https://www.nagano-fa.or.jp/referee_top', '', '審判', 'var(--tag-match)', '審判（審判委員会）の講習会・資格のご案内と、お知らせを掲載しています。' ),
			'technical'  => array( 'name' => '技術', 'order' => 10, 'target' => 'technical' ) + $cat( '指導者', '', 'COACH', 'info', 'https://www.nagano-fa.or.jp/technical', '', '技術', 'var(--tag-reg)', '指導者（技術委員会）の講習会・資格のご案内と、お知らせを掲載しています。' ),
			'medical'    => array( 'name' => '医学', 'order' => 11, 'target' => '' ) + $cat( '医学', '', 'MEDICAL', 'info', 'https://www.nagano-fa.or.jp/category/news_top/medical_news', '', '医学', 'var(--alert)', '医学（医学委員会）からのお知らせを掲載しています。' ),
			'office'     => array( 'name' => '事務局', 'order' => 12, 'target' => '' ) + $cat( '協会', '', 'NFA', 'info', 'https://www.nagano-fa.or.jp/category/news_top/office_news', '', '協会', 'var(--tag-info)', '長野県サッカー協会（事務局）からのお知らせを掲載しています。' ),
		);
	}

	public static function definition( $slug ) {
		return self::definitions()[ $slug ] ?? array();
	}

	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_seed' ), 20 );
		add_action( self::TAX . '_edit_form_fields', array( __CLASS__, 'edit_fields' ) );
		add_action( 'edited_' . self::TAX, array( __CLASS__, 'save_fields' ) );
	}

	public static function maybe_seed() {
		if ( taxonomy_exists( self::TAX ) && get_option( 'nfa_com_seeded' ) !== NFA_COM_VERSION ) {
			self::seed();
		}
	}

	/**
	 * 12個の委員会を用意する（既にあれば名前・スラッグは変えない）。
	 */
	public static function seed() {
		foreach ( self::definitions() as $slug => $d ) {
			if ( ! get_term_by( 'slug', $slug, self::TAX ) ) {
				$r = wp_insert_term( $d['name'], self::TAX, array( 'slug' => $slug ) );
				if ( ! is_wp_error( $r ) ) {
					update_term_meta( $r['term_id'], 'nfa_order', $d['order'] );
				}
			}
		}
		update_option( 'nfa_com_seeded', NFA_COM_VERSION, false );
	}

	/**
	 * 委員会の一覧（並び順どおり）。
	 *
	 * @return WP_Term[]
	 */
	public static function all() {
		$terms = get_terms( array( 'taxonomy' => self::TAX, 'hide_empty' => false ) );
		if ( is_wp_error( $terms ) ) {
			return array();
		}
		$order = array_keys( self::definitions() );
		usort(
			$terms,
			function ( $a, $b ) use ( $order ) {
				$ia = array_search( $a->slug, $order, true );
				$ib = array_search( $b->slug, $order, true );
				return ( false === $ia ? 99 : $ia ) <=> ( false === $ib ? 99 : $ib );
			}
		);
		return $terms;
	}

	/**
	 * 対象が未選択なら、委員会の初期値を入れる（例：4種 → 小学生（4種））。
	 */
	public static function apply_default_target( $post_id, array $committee_ids ) {
		if ( ! taxonomy_exists( 'nfa_target' ) || ! $committee_ids ) {
			return;
		}
		$has = wp_get_object_terms( $post_id, 'nfa_target', array( 'fields' => 'ids' ) );
		if ( $has && ! is_wp_error( $has ) ) {
			return;
		}
		$targets = array();
		foreach ( $committee_ids as $cid ) {
			$slug = (string) nfa_committee_meta( $cid, 'target' );
			$t    = $slug ? get_term_by( 'slug', $slug, 'nfa_target' ) : null;
			if ( $t ) {
				$targets[] = (int) $t->term_id;
			}
		}
		if ( $targets ) {
			wp_set_object_terms( $post_id, $targets, 'nfa_target' );
		}
	}

	/* ---------- 委員会の編集画面 ---------- */

	public static function edit_fields( $term ) {
		$def = self::definition( $term->slug );
		wp_nonce_field( 'nfa_com_term', 'nfa_com_term_nonce' );
		foreach ( self::FIELDS as $key => $label ) {
			$value = get_term_meta( $term->term_id, 'nfa_' . $key, true );
			echo '<tr class="form-field"><th scope="row"><label for="nfa_' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td>';
			if ( 'layout' === $key ) {
				echo '<select name="nfa_term[layout]" id="nfa_layout"><option value="">（初期値：' . esc_html( self::LAYOUTS[ $def['layout'] ?? 'info' ] ?? '' ) . '）</option>';
				foreach ( self::LAYOUTS as $k => $l ) {
					printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $value, $k, false ), esc_html( $l ) );
				}
				echo '</select>';
			} elseif ( in_array( $key, array( 'lead', 'archive' ), true ) ) {
				printf( '<textarea name="nfa_term[%1$s]" id="nfa_%1$s" rows="3" placeholder="%2$s">%3$s</textarea>', esc_attr( $key ), esc_attr( (string) ( $def[ $key ] ?? '' ) ), esc_textarea( (string) $value ) );
			} else {
				printf( '<input type="text" name="nfa_term[%1$s]" id="nfa_%1$s" value="%2$s" placeholder="%3$s">', esc_attr( $key ), esc_attr( (string) $value ), esc_attr( (string) ( $def[ $key ] ?? '' ) ) );
			}
			echo '<p class="description">空欄なら初期値（薄い文字）を使います。</p></td></tr>';
		}
	}

	public static function save_fields( $term_id ) {
		if ( ! isset( $_POST['nfa_com_term_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nfa_com_term_nonce'] ), 'nfa_com_term' ) || ! current_user_can( 'manage_categories' ) ) {
			return;
		}
		$in = isset( $_POST['nfa_term'] ) ? (array) wp_unslash( $_POST['nfa_term'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( array_keys( self::FIELDS ) as $key ) {
			$v = isset( $in[ $key ] ) ? ( in_array( $key, array( 'lead', 'archive' ), true ) ? sanitize_textarea_field( $in[ $key ] ) : sanitize_text_field( $in[ $key ] ) ) : '';
			if ( '' === $v ) {
				delete_term_meta( $term_id, 'nfa_' . $key );
			} else {
				update_term_meta( $term_id, 'nfa_' . $key, $v );
			}
		}
	}
}
