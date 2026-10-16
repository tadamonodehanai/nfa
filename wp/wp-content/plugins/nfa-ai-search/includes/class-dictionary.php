<?php
/**
 * 探索の語彙（3軸の選択肢と言い換え）。
 *
 * デザイン NFA_site_design/assets/js/search.js の AX / SHORT / toFacets を初期値とし、管理画面で JSON として編集できる。
 *
 *   axes[].key   t（対象）| p（目的）| r（地域）   … 画面と API で使う短い名前（search.js と同じ）
 *   axes[].tax   対応するタクソノミー（nfa_target / nfa_purpose / nfa_area）
 *   axes[].items[]
 *       v      値。タクソノミー用語のスラッグ（地域は用語の名前）と同じにする
 *       l      表示名
 *       short  会話で使う短い名前（任意。対象の「小学生」など）
 *       rx     入力文から読み取る正規表現
 *   join      「チームに入りたい」をチーム探しとして扱う正規表現
 *   examples  入口に出す質問の例
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Dictionary {

	const OPTION = 'nfa_ais_dictionary_v3';

	private static $cache = null;

	public static function defaults() {
		$i = fn( $v, $l, $rx, $short = '' ) => array_filter( array( 'v' => $v, 'l' => $l, 'short' => $short, 'rx' => $rx ), 'strlen' );
		return array(
			'axes'     => array(
				array(
					'key'   => 't',
					'name'  => '対象',
					'sub'   => '誰に向けた情報か',
					'tax'   => 'nfa_target',
					'items' => array(
						$i( 'cat-4', '小学生（4種）', '小学|4種|４種|U-?1[0-2](?!\d)|少年', '小学生' ),
						$i( 'cat-3', '中学生（3種）', '中学|3種|３種|U-?1[3-5](?!\d)', '中学生' ),
						$i( 'cat-2', '高校生（2種）', '高校|2種|２種|U-?1[6-8](?!\d)', '高校生' ),
						$i( 'cat-1', '社会人・大学（1種）', '社会人|大学|1種|１種|大人|一般', '社会人・大学' ),
						$i( 'cat-w', '女子', '女子|なでしこ|女の子|ガールズ|レディース' ),
						$i( 'cat-s', 'シニア', 'シニア|O-?[3-7]\d|４０歳|40歳|50歳|60歳' ),
						$i( 'cat-f', 'フットサル', 'フットサル' ),
						$i( 'grassroots', 'キッズ', 'キッズ|幼児|未就学|園児|U-?[68](?!\d)' ),
						$i( 'para', '障がい者サッカー', '障がい|障害|ブラインド|知的障|電動車椅子' ),
						$i( 'referee', '審判', '審判|レフェリー' ),
						$i( 'technical', '指導者', '指導者|コーチ|監督|ライセンス' ),
					),
				),
				array(
					'key'   => 'p',
					'name'  => '目的',
					'sub'   => '何をしたいか',
					'tax'   => 'nfa_purpose',
					// 読み取りは並び順（具体的な意図が先）。画面の並びは order の順
					'order' => array( 'schedule', 'result', 'reg', 'course', 'event', 'news' ),
					'items' => array(
						$i( 'course', '講習会に参加', '講習|資格|更新|ライセンス|研修|養成|受講|昇級' ),
						$i( 'reg', '登録・手続き', '登録|手続|申請|申込|申し込|移籍|入りたい|入会|加入|チーム.{0,3}(探|入|さが)' ),
						$i( 'result', '試合結果を見る', '結果|星取|順位|勝敗|スコア|優勝|勝った' ),
						$i( 'event', 'イベント・セミナーに参加', 'イベント|セミナー|フェスティバル|体験|教室|参加したい|募集|キャンプ' ),
						$i( 'schedule', '大会日程を知る', 'いつ|日程|予定|スケジュール|組み合わせ|組合せ|開催日|大会|試合' ),
						$i( 'news', 'お知らせを読む', 'お知らせ|ニュース|最新|新着' ),
					),
				),
				array(
					'key'   => 'r',
					'name'  => '地域',
					'sub'   => 'どの地区か',
					'tax'   => 'nfa_area',
					'items' => array(
						$i( '北信', '北信', '北信|長野市|千曲|須坂|中野|飯山' ),
						$i( '東信', '東信', '東信|佐久|上田|上小|小諸|東御' ),
						$i( '中信', '中信', '中信|松本|塩尻|安曇|大町|木曽' ),
						$i( '南信', '南信', '南信|諏訪|伊那|飯田' ),
						$i( '県全域', '県全域', '県全域|県全体|全県|県大会|県リーグ' ),
					),
				),
			),
			'join'     => 'チーム.{0,4}(入|探|さが)',
			'examples' => array( '小学生の大会はいつありますか？', '審判の資格を更新したい', '女子のチームに入りたい', '指導者の講習会を探している' ),
		);
	}

	public static function get() {
		if ( null !== self::$cache ) {
			return self::$cache;
		}
		$saved = get_option( self::OPTION );
		$dict  = is_array( $saved ) && ! empty( $saved['axes'] ) ? $saved : self::defaults();
		foreach ( $dict['axes'] as &$ax ) {
			$ax['items'] = array_map( fn( $it ) => $it + array( 'short' => '', 'rx' => '' ), $ax['items'] );
		}
		unset( $ax );
		$dict += array( 'join' => '', 'examples' => array() );
		self::$cache = $dict;
		return $dict;
	}

	public static function reset_cache() {
		self::$cache = null;
	}

	public static function axes() {
		return self::get()['axes'];
	}

	public static function axis( $key ) {
		foreach ( self::axes() as $ax ) {
			if ( $ax['key'] === $key ) {
				return $ax;
			}
		}
		return null;
	}

	/**
	 * 画面に出す並び（目的は order があればその順）。
	 */
	public static function display_items( $key ) {
		$ax = self::axis( $key );
		if ( ! $ax ) {
			return array();
		}
		if ( empty( $ax['order'] ) ) {
			return $ax['items'];
		}
		$by = array_column( $ax['items'], null, 'v' );
		$out = array();
		foreach ( $ax['order'] as $v ) {
			if ( isset( $by[ $v ] ) ) {
				$out[] = $by[ $v ];
				unset( $by[ $v ] );
			}
		}
		return array_merge( $out, array_values( $by ) );
	}

	public static function item( $key, $v ) {
		foreach ( self::axis( $key )['items'] ?? array() as $it ) {
			if ( $it['v'] === $v ) {
				return $it;
			}
		}
		return null;
	}

	public static function examples() {
		return self::get()['examples'];
	}

	/**
	 * 選択肢に対応するタクソノミー用語（スラッグ → 名前の順で探す）。
	 */
	public static function term( $key, $v ) {
		$ax = self::axis( $key );
		if ( ! $ax || ! taxonomy_exists( $ax['tax'] ) ) {
			return null;
		}
		$t = get_term_by( 'slug', sanitize_title( $v ), $ax['tax'] );
		return $t ? $t : ( get_term_by( 'name', $v, $ax['tax'] ) ?: null );
	}

	/**
	 * 入力文 → 条件（search.js の toFacets と同じ考え方）。
	 * 1つの軸につき1つだけ読み取る。目的は辞書の並び順（具体的な意図が先）、対象・地域は文中で先に出た語。
	 *
	 * @return array{t:string[], p:string[], r:string[], join:bool, kw:string[]}
	 */
	public static function read( $q ) {
		$f    = array( 't' => array(), 'p' => array(), 'r' => array(), 'join' => false, 'kw' => array() );
		$rest = $q;
		foreach ( self::axes() as $ax ) {
			$best = null;
			foreach ( $ax['items'] as $it ) {
				if ( '' === $it['rx'] ) {
					continue;
				}
				if ( preg_match( '/' . $it['rx'] . '/iu', $q, $m, PREG_OFFSET_CAPTURE ) ) {
					$pos = mb_strlen( substr( $q, 0, $m[0][1] ) );
					if ( null === $best || ( 'p' !== $ax['key'] && $pos < $best['i'] ) ) {
						$best = array( 'v' => $it['v'], 'i' => $pos );
					}
				}
				$rest = preg_replace( '/' . $it['rx'] . '/iu', ' ', $rest );
			}
			if ( $best ) {
				$f[ $ax['key'] ][] = $best['v'];
			}
		}
		$join = self::get()['join'];
		if ( $join && preg_match( '/' . $join . '/u', $q ) ) {
			$f['join'] = true;
			$f['p']    = array();
		}
		$rest    = preg_replace( '/[？?！!、。・「」（）()]/u', ' ', (string) $rest );
		$rest    = preg_replace( '/について|ですか|ますか|ありますか|知りたい|したい|たい|ている|いる|どう|すれば|どこ|なに|何|ください|教えて|情報|の|は|を|が|に|で|と|も|へ|や/u', ' ', $rest );
		$f['kw'] = array_values( array_filter( preg_split( '/\s+/u', trim( $rest ) ), fn( $w ) => mb_strlen( $w ) >= 2 ) );
		return $f;
	}

	/**
	 * 保存前の検証。問題があれば WP_Error を返す。
	 */
	public static function validate( $dict ) {
		if ( ! is_array( $dict ) || empty( $dict['axes'] ) || ! is_array( $dict['axes'] ) ) {
			return new WP_Error( 'nfa_dict', 'axes（配列）が必要です。' );
		}
		$taxes = array( 't' => 'nfa_target', 'p' => 'nfa_purpose', 'r' => 'nfa_area' );
		foreach ( $dict['axes'] as $ax ) {
			if ( empty( $ax['key'] ) || ! isset( $taxes[ $ax['key'] ] ) ) {
				return new WP_Error( 'nfa_dict', '軸の key は t / p / r のいずれかにしてください。' );
			}
			if ( empty( $ax['items'] ) || ! is_array( $ax['items'] ) ) {
				return new WP_Error( 'nfa_dict', sprintf( '軸「%s」に items が必要です。', $ax['key'] ) );
			}
			$seen = array();
			foreach ( $ax['items'] as $it ) {
				if ( empty( $it['v'] ) || empty( $it['l'] ) ) {
					return new WP_Error( 'nfa_dict', sprintf( '軸「%s」に v か l のない選択肢があります。', $ax['key'] ) );
				}
				if ( isset( $seen[ $it['v'] ] ) ) {
					return new WP_Error( 'nfa_dict', sprintf( '「%s」が重複しています。', $it['v'] ) );
				}
				if ( ! empty( $it['rx'] ) && false === @preg_match( '/' . $it['rx'] . '/iu', '' ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
					return new WP_Error( 'nfa_dict', sprintf( '「%s」の rx が正規表現として不正です。', $it['v'] ) );
				}
				$seen[ $it['v'] ] = true;
			}
		}
		return true;
	}
}
