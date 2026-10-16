<?php
/**
 * 探索画面のショートコードと、テーマ向けの表示部品。
 *
 *   [nfa_ai_search]          AI検索（対話型）。デザイン NFA_site_design/search.html の section.sr-app と同じマークアップ
 *   [nfa_ai_source_notice]   記事ページの「AIの案内はこのページを根拠にしています」
 *
 * 見た目はテーマ（nfa2026 の assets/css/style.css の .sr-*）が持つ。プラグインは構造と動きだけを出す。
 * ページ見出し（パンくず・pg-hero）とカテゴリ一覧はテーマの固定ページテンプレートで出す。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Frontend {

	const PAGE_OPTION = 'nfa_ais_search_page';

	public static function init() {
		add_shortcode( 'nfa_ai_search', array( __CLASS__, 'shortcode' ) );
		add_shortcode( 'nfa_ai_source_notice', array( __CLASS__, 'source_notice' ) );
		// ブロックテーマはテンプレート（＝ショートコード）を wp_head より前に描画するので、init で登録しておく。
		add_action( 'init', array( __CLASS__, 'register_assets' ) );
		add_action( 'save_post_page', array( __CLASS__, 'remember_search_page' ), 10, 2 );
	}

	public static function register_assets() {
		$ver = fn( $f ) => NFA_AIS_VERSION . '.' . filemtime( NFA_AIS_DIR . $f );
		wp_register_style( 'nfa-ai-search', NFA_AIS_URL . 'assets/nfa-ai-search.css', array(), $ver( 'assets/nfa-ai-search.css' ) );
		wp_register_script( 'nfa-ai-search', NFA_AIS_URL . 'assets/nfa-ai-search.js', array(), $ver( 'assets/nfa-ai-search.js' ), true );
	}

	/**
	 * 探索画面を置いた固定ページを覚えておく（ヘッダーの検索欄の送り先に使う）。
	 */
	public static function remember_search_page( $post_id, $post ) {
		if ( 'publish' === $post->post_status && has_shortcode( $post->post_content, 'nfa_ai_search' ) ) {
			update_option( self::PAGE_OPTION, $post_id, false );
		}
	}

	public static function search_page_url() {
		$id = (int) get_option( self::PAGE_OPTION );
		if ( $id && 'publish' === get_post_status( $id ) ) {
			return get_permalink( $id );
		}
		$p = get_page_by_path( 'search' );
		return $p && 'publish' === $p->post_status ? get_permalink( $p ) : '';
	}

	private static function page_url( $path, $fallback = '' ) {
		$p = get_page_by_path( $path );
		return $p ? get_permalink( $p ) : ( $fallback ? $fallback : home_url( '/' . $path . '/' ) );
	}

	public static function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'placeholder' => '例）小学生の大会はいつ？　審判の資格を更新したい',
			),
			$atts,
			'nfa_ai_search'
		);
		// テーマが .sr-* のスタイルを持たないときだけ、プラグインの最低限のスタイルを使う
		if ( ! current_theme_supports( 'nfa-ai-search' ) ) {
			wp_enqueue_style( 'nfa-ai-search' );
		}
		wp_enqueue_script( 'nfa-ai-search' );

		$axes  = array();
		$pages = array();
		foreach ( NFA_AIS_Dictionary::axes() as $ax ) {
			$axes[ $ax['key'] ] = array(
				'name'  => $ax['name'],
				'items' => array_map( fn( $it ) => array( 'v' => $it['v'], 'l' => $it['l'], 'short' => $it['short'] ), $ax['items'] ),
			);
		}
		// 対象 → 委員会ページ（同じスラッグの担当委員会があれば）
		foreach ( NFA_AIS_Dictionary::axis( 't' )['items'] as $it ) {
			$term = get_term_by( 'slug', $it['v'], NFA_AIS_Taxonomies::COMMITTEE );
			if ( $term ) {
				$link = get_term_link( $term );
				if ( ! is_wp_error( $link ) ) {
					$pages[ $it['v'] ] = $link;
				}
			}
		}
		$grow   = self::page_url( 'grow' );
		$config = array(
			'api'         => esc_url_raw( rest_url( NFA_AIS_Rest::NS . '/' ) ),
			'nonce'       => wp_create_nonce( NFA_AIS_Rest::NONCE_ACTION ),
			'axes'        => $axes,
			'pages'       => $pages,
			'urls'        => array(
				'teams'    => self::page_url( 'teams' ),
				'register' => $grow . '#register',
			),
			'placeholder' => apply_filters( 'nfa_ais_placeholder_image', NFA_AIS_URL . 'assets/ph-photo.jpg' ),
		);

		ob_start();
		?>
<section class="sr-app is-entry" id="result" data-nfa-ais="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"><div class="wrap">
<div class="sr-grid">
<aside aria-label="条件から絞り込む" class="sr-side">
<button aria-controls="sr-pnl" aria-expanded="false" class="sr-side-b" data-acc="" type="button">条件から絞り込む<span id="sr-sel">0件 選択中</span></button>
<div class="sr-pnl" hidden="" id="sr-pnl">
<div class="sr-now" id="sr-now"></div>
<div class="sr-side-h"><h2>条件から絞り込む</h2></div>
<?php foreach ( NFA_AIS_Dictionary::axes() as $ax ) : ?>
<fieldset class="sr-ax">
<legend><?php echo esc_html( $ax['name'] ); ?><small><?php echo esc_html( $ax['sub'] ?? '' ); ?></small></legend>
<div class="sr-ops"><?php foreach ( NFA_AIS_Dictionary::display_items( $ax['key'] ) as $it ) : ?><button aria-pressed="false" class="pill" data-ax="<?php echo esc_attr( $ax['key'] ); ?>" data-v="<?php echo esc_attr( $it['v'] ); ?>" type="button"><?php echo esc_html( $it['l'] ); ?></button><?php endforeach; ?></div>
</fieldset>
<?php endforeach; ?>
<p class="sr-side-n">該当する情報がない条件は選べません。</p>
</div>
</aside>
<div class="sr-main" id="sr-main">
<div class="sr-entry" id="sr-entry">
<form action="<?php echo esc_url( self::search_page_url() ); ?>" aria-label="AIに質問する" class="sr-form" id="sr-form" role="search">
<label class="sr-lb" for="sr-q">何を知りたいですか？</label>
<div class="sr-box">
<svg aria-hidden="true" class="sr-ico" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path></svg>
<input autocomplete="off" class="sr-in" id="sr-q" name="q" maxlength="200" placeholder="<?php echo esc_attr( $atts['placeholder'] ); ?>" type="text" value=""/>
<button aria-label="質問する" class="sr-send" type="submit"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 19V5M6 11l6-6 6 6"></path></svg></button>
</div>
</form>
<p class="sr-ex-h">あなたが興味がありそうな情報</p>
<div class="sr-chips"><?php foreach ( NFA_AIS_Dictionary::examples() as $ex ) : ?><button data-sq="" type="button"><span>#</span><?php echo esc_html( $ex ); ?></button><?php endforeach; ?></div>
<p class="sr-help">話しかけるように入力するか、条件から選んでください。続けて質問すると、条件を重ねて絞り込んでいきます。</p>
</div>
<div aria-live="polite" class="sr-thread" id="sr-thread"></div>
<div class="sr-dock">
<form aria-label="続けて質問する" class="sr-cmp" id="sr-cmp" role="search">
<label class="vh" for="sr-q2">続けて質問する</label>
<input autocomplete="off" id="sr-q2" maxlength="200" placeholder="続けて聞く。例）北信だけ　結果も見たい　中学生は？" type="text"/>
<button aria-label="送信" class="sr-send" type="submit"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 19V5M6 11l6-6 6 6"></path></svg></button>
</form>
<p class="sr-cmp-n">タグを選ぶか、ことばで続けてください。案内には必ず出典がつきます。<button class="sr-reset" type="button">最初から聞きなおす</button></p>
</div>
</div>
</div>
</div></section>
		<?php
		return ob_get_clean();
	}

	public static function source_notice( $atts = array() ) {
		$post = get_post();
		if ( ! $post || ! NFA_AIS_Eligibility::is_eligible( $post ) ) {
			return '';
		}
		if ( ! current_theme_supports( 'nfa-ai-search' ) ) {
			wp_enqueue_style( 'nfa-ai-search' );
		}
		return '<div class="nfa-ais-ground"><span class="nfa-ais-ground__ck" aria-hidden="true">✓</span><p>このページは長野県サッカー協会が掲載した情報です。「情報を探す」のAIは、このページを根拠として案内しています。</p></div>';
	}
}
