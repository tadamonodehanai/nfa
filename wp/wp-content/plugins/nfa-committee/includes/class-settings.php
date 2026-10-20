<?php
/**
 * 設定 → 委員会の運用。公開の流れ（A：委員会がそのまま公開／B：事務局が承認）。
 * 協賛企業の並びは ACF Pro の設定ページ（acf-fields.php）で入力する。
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

class NFA_Com_Settings {

	const OPTION = 'nfa_com_settings';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
	}

	public static function defaults() {
		return array( 'publish_flow' => 'direct' );
	}

	public static function value( $key ) {
		$s = wp_parse_args( (array) get_option( self::OPTION, array() ), self::defaults() );
		return $s[ $key ] ?? null;
	}

	public static function menu() {
		add_options_page( '委員会の運用', '委員会の運用', 'manage_options', 'nfa-committee', array( __CLASS__, 'page' ) );
	}

	public static function register() {
		register_setting(
			'nfa_com',
			self::OPTION,
			array(
				'sanitize_callback' => fn( $in ) => array( 'publish_flow' => 'review' === ( $in['publish_flow'] ?? '' ) ? 'review' : 'direct' ),
			)
		);
	}

	public static function page() {
		$flow = self::value( 'publish_flow' );
		?>
		<div class="wrap">
			<h1>委員会の運用</h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'nfa_com' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">公開の流れ</th>
						<td>
							<label style="display:block;margin-bottom:8px"><input type="radio" name="<?php echo esc_attr( self::OPTION ); ?>[publish_flow]" value="direct" <?php checked( 'direct', $flow ); ?>> A：委員会担当がそのまま公開する</label>
							<label style="display:block"><input type="radio" name="<?php echo esc_attr( self::OPTION ); ?>[publish_flow]" value="review" <?php checked( 'review', $flow ); ?>> B：委員会担当は「レビュー待ち」で送信し、事務局が承認して公開する</label>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<h2>担当委員会の割り当て</h2>
			<p>「ユーザー」でユーザーを編集し、権限グループを「委員会担当」にして、担当委員会を選んでください。1人1アカウントを推奨します。</p>
			<h2>協賛企業</h2>
			<p><?php echo function_exists( 'acf_add_options_page' ) ? '左のメニュー「協賛企業」から入力します。' : 'ACF Pro を有効にすると、左のメニューに「協賛企業」の入力画面が出ます。未入力のあいだは、デザインの並びを表示します。'; ?></p>
		</div>
		<?php
	}

	/**
	 * 協賛企業の初期値（デザイン NFA_site_design の並び）。
	 */
	/**
	 * 協賛企業の初期値（デザインの並び）。key はテーマのロゴ画像 img/sp-{key}.png。
	 */
	public static function default_sponsors() {
		$r = fn( $name, $url, $key = '' ) => array( 'name' => $name, 'url' => $url, 'logo' => '', 'key' => $key );
		return array(
			'gold'       => array( $r( 'マクセルイズミ株式会社', 'https://www.izumi.maxell.co.jp/', 'maxell' ) ),
			'official'   => array(
				$r( 'アルピコグループ', 'https://holdings.alpico.co.jp/', 'alpico' ),
				$r( 'テレビ信州', 'https://www.tsb.jp/', 'tsb' ),
				$r( 'ながのアド・ビューロ', 'https://www.adbureau.co.jp/', 'adbureau' ),
				$r( 'リッチエード', 'http://www.richade.co.jp/', 'richade' ),
				$r( '長野朝日放送', 'https://www.abn-tv.co.jp/', 'abn' ),
				$r( 'Tutti', 'http://tutti-armonia.jp/', 'tutti' ),
				$r( 'みずほ証券', 'https://www.mizuho-sc.com/', 'mizuho' ),
				$r( '明治安田生命', 'https://www.meijiyasuda.co.jp/', 'meiji' ),
				$r( 'アディダスジャパン', 'https://www.adidas.jp/', 'adidas' ),
				$r( 'レアルスポーツ', 'https://www.realsports.jp/', 'real' ),
			),
			'supporters' => array(
				$r( 'トンボ学生服', 'http://www.tombow.gr.jp/' ),
				$r( 'ファーストビジネス株式会社', '' ),
				$r( '株式会社松本総合保険', 'https://www.m-t-i.jp/' ),
			),
		);
	}
}
