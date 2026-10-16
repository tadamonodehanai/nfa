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
	public static function default_sponsors() {
		return array(
			'gold'       => array(
				array( 'name' => 'マクセルイズミ株式会社', 'url' => 'https://www.izumi.maxell.co.jp/' ),
			),
			'official'   => array(
				array( 'name' => 'アルピコグループ', 'url' => 'https://holdings.alpico.co.jp/' ),
				array( 'name' => 'テレビ信州', 'url' => 'https://www.tsb.jp/' ),
				array( 'name' => 'ながのアド・ビューロ', 'url' => 'https://www.adbureau.co.jp/' ),
				array( 'name' => 'リッチエイド', 'url' => 'http://www.richade.co.jp/' ),
				array( 'name' => 'abn長野放送', 'url' => 'https://www.abn-tv.co.jp/' ),
				array( 'name' => 'Tutti', 'url' => 'http://tutti-armonia.jp/' ),
				array( 'name' => 'みずほ証券', 'url' => 'https://www.mizuho-sc.com/index.html' ),
				array( 'name' => '明治安田生命', 'url' => 'https://www.meijiyasuda.co.jp/' ),
				array( 'name' => 'アディダスジャパン', 'url' => 'https://www.adidas.jp/' ),
				array( 'name' => 'レアルスポーツ', 'url' => 'https://www.realsports.jp/' ),
			),
			'supporters' => array(
				array( 'name' => 'トンボ学生服', 'url' => 'http://www.tombow.gr.jp/' ),
				array( 'name' => 'ファーストビジネス株式会社', 'url' => '' ),
				array( 'name' => '株式会社松本総合保険', 'url' => 'https://www.m-t-i.jp/' ),
			),
		);
	}
}
