<?php
/**
 * 探索の語彙（3軸の選択肢と言い換え）。
 *
 * ワイヤー NFA_wireframes_site/search.html の AXES / LEX / utterance を初期値とし、
 * 管理画面で JSON として編集できる。
 *
 *   axes[].key      target（対象）| purpose（目的）| area（地域）
 *   axes[].name     パネルの見出し
 *   axes[].axis     見出しの下の説明（「誰に向けた情報か」など）
 *   axes[].options[]
 *       value    選択肢の名前。同名のタクソノミー用語が付いた記事が該当する
 *       terms    同じ選択肢として扱う別の用語名（任意）
 *       pattern  入力文から、この選択肢を読み取るための正規表現
 *       say      会話での言い回し（「小学生（4種）のこと」など）
 *   examples   入口に出す聞き方の例
 *   popular    読み取れなかったときに出す「よく選ばれている条件」（対象の value）
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Dictionary {

	const OPTION = 'nfa_ais_dictionary_v2';

	private static $cache = null;

	public static function defaults() {
		$o = fn( $value, $pattern, $say ) => array( 'value' => $value, 'pattern' => $pattern, 'say' => $say );
		return array(
			'axes'     => array(
				array(
					'key'     => 'target',
					'name'    => '対象',
					'axis'    => '誰に向けた情報か',
					'options' => array(
						$o( '小学生（4種）', '小学|4種|少年|低学年|高学年|キッズ|U-?12', '小学生（4種）のこと' ),
						$o( '中学生（3種）', '中学|3種|ジュニアユース|U-?15', '中学生（3種）のこと' ),
						$o( '高校生（2種）', '高校|2種|(?<!ジュニア)ユース|U-?18', '高校生（2種）のこと' ),
						$o( '大人・社会人（1種）', '社会人|1種|大人', '大人・社会人のこと' ),
						$o( '女子', '女子|女の子|なでしこ|girls', '女子サッカーのこと' ),
						$o( 'シニア', 'シニア|O-?40|O-?50|オーバー', 'シニア世代のこと' ),
						$o( 'フットサル', 'フットサル', 'フットサルのこと' ),
						$o( '障がい者サッカー', '障がい|障害', '障がい者サッカーのこと' ),
						$o( '審判', '審判|レフェリー|笛', '審判のこと' ),
						$o( '指導者', '指導者|コーチ|監督', '指導者のこと' ),
					),
				),
				array(
					'key'     => 'purpose',
					'name'    => '目的',
					'axis'    => '何をしたいか',
					'options' => array(
						$o( '大会日程を知る', '大会|日程|いつ|開催|予定|組み合わせ|組合せ', '大会の日程が知りたい' ),
						$o( '試合結果を見る', '結果|順位|スコア|勝敗|勝った|負けた', '試合の結果が見たい' ),
						$o( '登録・手続き', '登録|手続き|申込|申し込|エントリー|更新|書類|様式', '登録や手続きについて' ),
						$o( '講習会に参加', '講習|研修|ライセンス|資格|養成', '講習会に参加したい' ),
						$o( 'イベント・セミナーに参加', 'イベント|セミナー|フェスティバル|交流会|体験|参加したい', 'イベントやセミナーに参加したい' ),
						$o( 'お知らせを読む', 'お知らせ|案内|連絡|中止|順延', 'お知らせが読みたい' ),
					),
				),
				array(
					'key'     => 'area',
					'name'    => '地域',
					'axis'    => 'どの地区か',
					'options' => array(
						$o( '北信', '北信|長野市|須坂|中野|飯山|千曲', '北信エリアで' ),
						$o( '東信', '東信|上田|佐久|小諸|東御', '東信エリアで' ),
						$o( '中信', '中信|松本|安曇野|塩尻|大町|木曽', '中信エリアで' ),
						$o( '南信', '南信|飯田|伊那|諏訪|岡谷|茅野|駒ヶ根', '南信エリアで' ),
						$o( '県全域', '県全域|県全体|全県', '県全域で' ),
					),
				),
			),
			'examples' => array( '小学生の大会っていつ？', '審判の資格更新はどうすれば？', '女子のチームに入りたい', '指導者の講習会を探しています' ),
			'popular'  => array( '小学生（4種）', '中学生（3種）', '女子', '審判', '指導者' ),
		);
	}

	public static function get() {
		if ( null !== self::$cache ) {
			return self::$cache;
		}
		$saved = get_option( self::OPTION );
		$dict  = is_array( $saved ) && ! empty( $saved['axes'] ) ? $saved : self::defaults();
		foreach ( $dict['axes'] as &$ax ) {
			$ax['options'] = array_map(
				fn( $o ) => wp_parse_args( $o, array( 'terms' => array(), 'pattern' => '', 'say' => $o['value'] ) ),
				$ax['options']
			);
		}
		unset( $ax );
		$dict += array( 'examples' => array(), 'popular' => array() );
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

	public static function option( $axis_key, $value ) {
		$ax = self::axis( $axis_key );
		foreach ( $ax ? $ax['options'] : array() as $o ) {
			if ( $o['value'] === $value ) {
				return $o;
			}
		}
		return null;
	}

	public static function values( $axis_key ) {
		$ax = self::axis( $axis_key );
		return $ax ? array_column( $ax['options'], 'value' ) : array();
	}

	public static function examples() {
		return self::get()['examples'];
	}

	public static function popular() {
		return array_values( array_filter( self::get()['popular'], fn( $v ) => self::option( 'target', $v ) ) );
	}

	/**
	 * 入力文から、軸ごとに最初に当てはまる選択肢を読み取る（各軸ひとつまで）。
	 *
	 * @return array axis key => value
	 */
	public static function read( $text ) {
		$out = array();
		foreach ( self::axes() as $ax ) {
			foreach ( $ax['options'] as $o ) {
				if ( '' !== $o['pattern'] && preg_match( '/' . $o['pattern'] . '/iu', $text ) ) {
					$out[ $ax['key'] ] = $o['value'];
					break;
				}
			}
		}
		return $out;
	}

	/**
	 * 保存前の検証。問題があれば WP_Error を返す。
	 */
	public static function validate( $dict ) {
		if ( ! is_array( $dict ) || empty( $dict['axes'] ) || ! is_array( $dict['axes'] ) ) {
			return new WP_Error( 'nfa_dict', 'axes（配列）が必要です。' );
		}
		$allowed = array_keys( NFA_AIS_Taxonomies::FACETS );
		foreach ( $dict['axes'] as $ax ) {
			if ( empty( $ax['key'] ) || ! in_array( $ax['key'], $allowed, true ) ) {
				return new WP_Error( 'nfa_dict', '軸の key は target / purpose / area のいずれかにしてください。' );
			}
			if ( empty( $ax['name'] ) || empty( $ax['options'] ) || ! is_array( $ax['options'] ) ) {
				return new WP_Error( 'nfa_dict', sprintf( '軸「%s」に name と options が必要です。', $ax['key'] ) );
			}
			$seen = array();
			foreach ( $ax['options'] as $o ) {
				if ( empty( $o['value'] ) ) {
					return new WP_Error( 'nfa_dict', sprintf( '軸「%s」に value のない選択肢があります。', $ax['key'] ) );
				}
				if ( isset( $seen[ $o['value'] ] ) ) {
					return new WP_Error( 'nfa_dict', sprintf( '「%s」が重複しています。', $o['value'] ) );
				}
				if ( ! empty( $o['pattern'] ) && false === @preg_match( '/' . $o['pattern'] . '/iu', '' ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
					return new WP_Error( 'nfa_dict', sprintf( '「%s」の pattern が正規表現として不正です。', $o['value'] ) );
				}
				$seen[ $o['value'] ] = true;
			}
		}
		return true;
	}
}
