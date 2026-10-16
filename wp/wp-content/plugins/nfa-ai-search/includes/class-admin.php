<?php
/**
 * 管理画面（設定 → AI検索）。設定・辞書・検索ログ・状態の4タブ。
 *
 * @package NFA_AI_Search
 */

defined( 'ABSPATH' ) || exit;

class NFA_AIS_Admin {

	const SLUG = 'nfa-ai-search';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_post_nfa_ais_save_dictionary', array( __CLASS__, 'save_dictionary' ) );
	}

	public static function menu() {
		add_options_page( 'AI検索', 'AI検索', 'manage_options', self::SLUG, array( __CLASS__, 'page' ) );
	}

	public static function register_settings() {
		register_setting(
			'nfa_ais',
			NFA_AIS_Settings::OPTION,
			array( 'sanitize_callback' => array( 'NFA_AIS_Settings', 'sanitize' ) )
		);
	}

	private static function url( $tab, $extra = array() ) {
		return add_query_arg( array_merge( array( 'page' => self::SLUG, 'tab' => $tab ), $extra ), admin_url( 'options-general.php' ) );
	}

	public static function page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$tabs = array( 'settings' => '設定', 'ingest' => '取り込み', 'dictionary' => '言い換え辞書', 'log' => '検索ログ', 'status' => '状態' );
		$tab  = isset( $_GET['tab'] ) && isset( $tabs[ $_GET['tab'] ] ) ? sanitize_key( $_GET['tab'] ) : 'settings'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="wrap"><h1>AI検索</h1><nav class="nav-tab-wrapper">';
		foreach ( $tabs as $k => $label ) {
			printf( '<a href="%s" class="nav-tab %s">%s</a>', esc_url( self::url( $k ) ), $k === $tab ? 'nav-tab-active' : '', esc_html( $label ) );
		}
		echo '</nav>';
		call_user_func( array( __CLASS__, 'tab_' . $tab ) );
		echo '</div>';
	}

	private static function tab_settings() {
		$s     = NFA_AIS_Settings::get();
		$name  = NFA_AIS_Settings::OPTION;
		$types = get_post_types( array( 'public' => true ), 'objects' );
		?>
		<form method="post" action="options.php">
			<?php settings_fields( 'nfa_ais' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">検索対象の投稿タイプ</th>
					<td>
						<?php foreach ( $types as $type ) : ?>
							<?php if ( in_array( $type->name, NFA_AIS_Eligibility::NEVER_TYPES, true ) ) { continue; } ?>
							<label style="display:block"><input type="checkbox" name="<?php echo esc_attr( $name ); ?>[post_types][]" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( in_array( $type->name, $s['post_types'], true ) ); ?>> <?php echo esc_html( $type->labels->name . '（' . $type->name . '）' ); ?></label>
						<?php endforeach; ?>
						<p class="description">チェックした種類の「公開済み・パスワードなし」の投稿だけが検索対象です（許可リスト方式）。お問い合わせ（Contact Form 7 / Flamingo）、メディア、下書き・非公開は対象になりません。</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="nfa-excluded">個別に除外するID</label></th>
					<td>
						<input id="nfa-excluded" class="regular-text" type="text" name="<?php echo esc_attr( $name ); ?>[excluded_ids]" value="<?php echo esc_attr( implode( ', ', $s['excluded_ids'] ) ); ?>">
						<p class="description">投稿・固定ページのIDをカンマ区切りで。各投稿の編集画面にある「AI検索の対象外にする」でも除外できます。お問い合わせフォームを含むページと探索画面のページは自動で除外されます。</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="nfa-meta">検索対象のカスタムフィールド</label></th>
					<td>
						<input id="nfa-meta" class="regular-text" type="text" name="<?php echo esc_attr( $name ); ?>[meta_keys]" value="<?php echo esc_attr( implode( ', ', $s['meta_keys'] ) ); ?>">
						<p class="description">許可するキーをカンマ区切りで（例：venue, tournament）。段階2の本文取り込みで使います。</p>
					</td>
				</tr>
				<tr>
					<th scope="row">AIによる解釈</th>
					<td>
						<label><input type="checkbox" name="<?php echo esc_attr( $name ); ?>[ai_enabled]" value="1" <?php checked( $s['ai_enabled'] ); ?>> 入力文の解釈に Claude を使う</label>
						<p class="description">
							<?php echo NFA_AIS_Settings::has_api_key() ? 'APIキー：設定済み（wp-config.php）' : 'APIキー：未設定。wp-config.php に <code>define( \'NFA_ANTHROPIC_API_KEY\', \'…\' );</code> を追加すると有効になります。未設定のあいだは辞書によるルールで解釈します。'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="nfa-cap">AI呼び出しの上限（1日）</label></th>
					<td><input id="nfa-cap" type="number" min="0" name="<?php echo esc_attr( $name ); ?>[daily_ai_cap]" value="<?php echo esc_attr( $s['daily_ai_cap'] ); ?>"> 回 <p class="description">超えた日は、AIを使わない解釈に自動で切り替えます。</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="nfa-rate">回数制限（1分・IPごと）</label></th>
					<td><input id="nfa-rate" type="number" min="1" name="<?php echo esc_attr( $name ); ?>[rate_per_min]" value="<?php echo esc_attr( $s['rate_per_min'] ); ?>"> 回</td>
				</tr>
				<tr>
					<th scope="row"><label for="nfa-days">検索ログの保存日数</label></th>
					<td><input id="nfa-days" type="number" min="1" max="365" name="<?php echo esc_attr( $name ); ?>[log_days]" value="<?php echo esc_attr( $s['log_days'] ); ?>"> 日</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
		<?php
	}

	private static function tab_dictionary() {
		$dict  = NFA_AIS_Dictionary::get();
		$saved = is_array( get_option( NFA_AIS_Dictionary::OPTION ) );
		$err   = get_transient( 'nfa_ais_dict_error' );
		$json  = get_transient( 'nfa_ais_dict_draft' );
		delete_transient( 'nfa_ais_dict_error' );
		delete_transient( 'nfa_ais_dict_draft' );
		if ( $err ) {
			printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $err ) );
		} elseif ( isset( $_GET['saved'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<div class="notice notice-success"><p>辞書を保存しました。</p></div>';
		}
		$json = $json ? $json : wp_json_encode( $dict, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		?>
		<p>AI検索の3軸（t 対象・p 目的・r 地域）の選択肢と、入力文を条件に読み替えるための言い換えです。<?php echo $saved ? '（編集済み）' : '（初期値：デザインの search.js と同じ）'; ?></p>
		<p class="description">
			各選択肢の <code>v</code> がタクソノミー用語のスラッグ（地域は用語の名前）、<code>l</code> が表示名、<code>short</code> が会話で使う短い名前、<code>rx</code> が入力文から読み取る正規表現です。
			検索ログの「0件だった入力」を見て、<code>rx</code> に言い換えを足していってください。選択肢を増やしたら、同じ用語も作られます。
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="nfa_ais_save_dictionary">
			<?php wp_nonce_field( 'nfa_ais_dictionary' ); ?>
			<textarea name="dictionary" rows="30" class="large-text code" spellcheck="false"><?php echo esc_textarea( $json ); ?></textarea>
			<p>
				<?php submit_button( '辞書を保存', 'primary', 'submit', false ); ?>
				<?php submit_button( '初期値に戻す', 'secondary', 'reset', false, array( 'onclick' => "return confirm('辞書を初期値に戻しますか？')" ) ); ?>
			</p>
		</form>
		<?php
	}

	public static function save_dictionary() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( '権限がありません。' );
		}
		check_admin_referer( 'nfa_ais_dictionary' );
		if ( isset( $_POST['reset'] ) ) {
			delete_option( NFA_AIS_Dictionary::OPTION );
			NFA_AIS_Index::bump();
			wp_safe_redirect( self::url( 'dictionary', array( 'saved' => 1 ) ) );
			exit;
		}
		$raw  = isset( $_POST['dictionary'] ) ? wp_unslash( $_POST['dictionary'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$dict = json_decode( $raw, true );
		$ok   = null === $dict ? new WP_Error( 'json', 'JSON として読み取れません：' . json_last_error_msg() ) : NFA_AIS_Dictionary::validate( $dict );
		if ( is_wp_error( $ok ) ) {
			set_transient( 'nfa_ais_dict_error', $ok->get_error_message(), 60 );
			set_transient( 'nfa_ais_dict_draft', $raw, 60 );
			wp_safe_redirect( self::url( 'dictionary' ) );
			exit;
		}
		$txt   = fn( $a ) => array_values( array_filter( array_map( 'sanitize_text_field', (array) $a ), 'strlen' ) );
		$taxes = array( 't' => 'nfa_target', 'p' => 'nfa_purpose', 'r' => 'nfa_area' );
		$clean = array(
			'axes'     => array_map(
				fn( $ax ) => array_filter(
					array(
						'key'   => $ax['key'],
						'name'  => sanitize_text_field( $ax['name'] ?? '' ),
						'sub'   => sanitize_text_field( $ax['sub'] ?? '' ),
						'tax'   => $taxes[ $ax['key'] ],
						'order' => isset( $ax['order'] ) ? $txt( $ax['order'] ) : null,
						'items' => array_map(
							fn( $it ) => array(
								'v'     => sanitize_text_field( $it['v'] ),
								'l'     => sanitize_text_field( $it['l'] ),
								'short' => sanitize_text_field( $it['short'] ?? '' ),
								'rx'    => (string) ( $it['rx'] ?? '' ), // validate() で正規表現として検査済み
							),
							$ax['items']
						),
					),
					fn( $v ) => null !== $v
				),
				$dict['axes']
			),
			'join'     => (string) ( $dict['join'] ?? '' ),
			'examples' => $txt( $dict['examples'] ?? array() ),
		);
		if ( '' !== $clean['join'] && false === @preg_match( '/' . $clean['join'] . '/u', '' ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
			set_transient( 'nfa_ais_dict_error', 'join が正規表現として不正です。', 60 );
			set_transient( 'nfa_ais_dict_draft', $raw, 60 );
			wp_safe_redirect( self::url( 'dictionary' ) );
			exit;
		}
		update_option( NFA_AIS_Dictionary::OPTION, $clean, false );
		NFA_AIS_Dictionary::reset_cache();
		NFA_AIS_Taxonomies::seed_terms();
		NFA_AIS_Index::bump();
		wp_safe_redirect( self::url( 'dictionary', array( 'saved' => 1 ) ) );
		exit;
	}

	private static function tab_ingest() {
		$unclassified = NFA_AIS_Ingest::unclassified();
		?>
		<p>検索対象の記事を取り込みます。記事を公開・更新したときは自動で取り込まれます。
		移行のあとや、まとめて記事を入れたあとは「差分を取り込む」を押してください。SSH は不要です（少しずつ処理し、この画面を閉じても WP-Cron が続きを進めます）。</p>

		<table class="widefat striped" style="max-width:640px" id="nfa-ingest-counts">
			<tbody>
				<tr><th style="width:260px">検索対象の記事</th><td data-k="eligible">…</td></tr>
				<tr><th>取り込み済み</th><td data-k="indexed">…</td></tr>
				<tr><th>未取り込み（新しい記事）</th><td data-k="new">…</td></tr>
				<tr><th>更新あり</th><td data-k="changed">…</td></tr>
				<tr><th>削除待ち（非公開・削除・除外）</th><td data-k="removed">…</td></tr>
				<tr><th>エラー（次回やり直し）</th><td data-k="errors">…</td></tr>
				<tr><th>分類（対象・目的・地域）が未設定</th><td data-k="unclassified">…</td></tr>
			</tbody>
		</table>

		<p style="margin-top:18px">
			<button type="button" class="button button-primary" id="nfa-ingest-diff">差分を取り込む</button>
			<button type="button" class="button" id="nfa-ingest-all">全件を作り直す</button>
			<button type="button" class="button-link" id="nfa-ingest-cancel" style="margin-left:12px;display:none">中止</button>
		</p>
		<div id="nfa-ingest-progress" style="max-width:640px;display:none">
			<div style="background:#dcdcde;height:10px;border-radius:5px;overflow:hidden"><div id="nfa-ingest-bar" style="background:#2271b1;height:10px;width:0"></div></div>
			<p id="nfa-ingest-text" class="description"></p>
		</div>
		<div id="nfa-ingest-errors"></div>

		<h2>分類が未設定の記事</h2>
		<?php if ( ! $unclassified ) : ?>
			<p>ありません。</p>
		<?php else : ?>
			<p class="description">対象・目的・地域のどれかが未設定の記事です（新しい順に最大50件）。探索画面の絞り込みに出てこないので、編集画面で設定してください。</p>
			<table class="widefat striped" style="max-width:820px"><thead><tr><th>記事</th><th>未設定の軸</th></tr></thead><tbody>
			<?php
			foreach ( array_slice( $unclassified, 0, 50 ) as $id ) {
				$axes    = NFA_AIS_Taxonomies::axes_of( $id );
				$missing = array();
				foreach ( NFA_AIS_Taxonomies::FACETS as $key => $axis ) {
					if ( ! $axes[ $key ] ) {
						$missing[] = $axis[1];
					}
				}
				printf( '<tr><td><a href="%s">%s</a></td><td>%s</td></tr>', esc_url( get_edit_post_link( $id ) ), esc_html( get_the_title( $id ) ), esc_html( implode( '・', $missing ) ) );
			}
			?>
			</tbody></table>
		<?php endif; ?>

		<script>
		(function () {
			var api = <?php echo wp_json_encode( esc_url_raw( rest_url( NFA_AIS_Rest::NS . '/ingest' ) ) ); ?>;
			var nonce = <?php echo wp_json_encode( wp_create_nonce( 'wp_rest' ) ); ?>;
			var $ = function (id) { return document.getElementById(id); };
			var running = false;
			function call(path, method) {
				return fetch(api + path, { method: method || 'GET', credentials: 'same-origin', headers: { 'X-WP-Nonce': nonce } }).then(function (r) { return r.json(); });
			}
			function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
			function showCounts(c) {
				if (!c) return;
				document.querySelectorAll('#nfa-ingest-counts [data-k]').forEach(function (td) { td.textContent = c[td.getAttribute('data-k')] + ' 件'; });
			}
			function showJob(d) {
				var j = d.job, busy = d.queue > 0;
				$('nfa-ingest-progress').style.display = j ? 'block' : 'none';
				$('nfa-ingest-cancel').style.display = busy ? 'inline' : 'none';
				$('nfa-ingest-diff').disabled = $('nfa-ingest-all').disabled = busy;
				if (!j) return;
				var pct = j.total ? Math.round(j.done / j.total * 100) : 100;
				$('nfa-ingest-bar').style.width = pct + '%';
				var label = { diff: '差分の取り込み', all: '全件の作り直し', auto: '記事更新による取り込み' }[j.mode] || '取り込み';
				$('nfa-ingest-text').textContent = label + '：' + j.done + ' / ' + j.total + ' 件（取り込み ' + (j.indexed || 0) + '・変更なし ' + (j.unchanged || 0) + '・削除 ' + (j.removed || 0) + '・エラー ' + (j.errors || []).length + '）' +
					(j.canceled ? ' 中止しました' : (busy ? ' 処理中…' : ' 完了'));
				$('nfa-ingest-errors').innerHTML = (j.errors || []).length ? '<h3>エラー</h3><ul>' + j.errors.map(function (e) { return '<li>ID ' + e.id + '：' + esc(e.message) + '</li>'; }).join('') + '</ul>' : '';
			}
			/* この画面を開いている間は、画面から続きを進める（閉じても WP-Cron が進める） */
			function loop() {
				if (running) return;
				running = true;
				call('/step', 'POST').then(function (d) {
					running = false;
					showJob(d);
					if (d.queue > 0) setTimeout(loop, 300);
					else call('?counts=1').then(function (d2) { showCounts(d2.counts); showJob(d2); });
				}).catch(function () { running = false; setTimeout(loop, 3000); });
			}
			function start(mode) {
				if (mode === 'all' && !confirm('全件を作り直します。段階2（ベクトル化）を入れたあとは、API費用がかかります。よろしいですか？')) return;
				call('/start?mode=' + mode, 'POST').then(function (d) { showJob(d); loop(); });
			}
			$('nfa-ingest-diff').addEventListener('click', function () { start('diff'); });
			$('nfa-ingest-all').addEventListener('click', function () { start('all'); });
			$('nfa-ingest-cancel').addEventListener('click', function () { call('/cancel', 'POST').then(function (d) { showJob(d); call('?counts=1').then(function (d2) { showCounts(d2.counts); }); }); });
			call('?counts=1').then(function (d) { showCounts(d.counts); showJob(d); if (d.queue > 0) loop(); });
		})();
		</script>
		<?php
	}

	private static function tab_log() {
		$days = (int) NFA_AIS_Settings::value( 'log_days' );
		echo '<p>入力された文章の記録です（メールアドレス・電話番号は伏せ字、IPアドレスは保存しません。保存期間 ' . esc_html( $days ) . ' 日）。</p>';
		echo '<h2>0件だった入力（直近30日・多い順）</h2>';
		$rows = NFA_AIS_Log::zero_hits();
		if ( ! $rows ) {
			echo '<p>まだありません。</p>';
		} else {
			echo '<table class="widefat striped"><thead><tr><th>入力</th><th>回数</th><th>最後</th></tr></thead><tbody>';
			foreach ( $rows as $r ) {
				printf( '<tr><td>%s</td><td>%d</td><td>%s</td></tr>', esc_html( $r->query ), (int) $r->times, esc_html( get_date_from_gmt( $r->last_at, 'Y-m-d H:i' ) ) );
			}
			echo '</tbody></table>';
		}
		echo '<h2>最近の入力</h2>';
		$rows = NFA_AIS_Log::recent();
		if ( ! $rows ) {
			echo '<p>まだありません。</p>';
			return;
		}
		echo '<table class="widefat striped"><thead><tr><th>日時</th><th>入力</th><th>条件</th><th>件数</th><th>解釈</th></tr></thead><tbody>';
		foreach ( $rows as $r ) {
			printf(
				'<tr><td>%s</td><td>%s</td><td>%s</td><td>%d</td><td>%s</td></tr>',
				esc_html( get_date_from_gmt( $r->created_at, 'Y-m-d H:i' ) ),
				esc_html( $r->query ),
				esc_html( $r->conds ),
				(int) $r->total,
				esc_html( 'claude' === $r->parser ? 'AI' : '辞書' )
			);
		}
		echo '</tbody></table>';
	}

	private static function tab_status() {
		$u    = get_option( NFA_AIS_Claude::USAGE_OPTION, array() );
		$rows = array(
			'検索対象の件数'     => count( NFA_AIS_Eligibility::eligible_ids() ) . ' 件',
			'探索画面のページ'   => NFA_AIS_Frontend::search_page_url() ? NFA_AIS_Frontend::search_page_url() : '未設定（固定ページに [nfa_ai_search] を置いてください）',
			'APIキー'          => NFA_AIS_Settings::has_api_key() ? '設定済み' : '未設定（辞書によるルールで動作中）',
			'モデル'           => NFA_AIS_Claude::model(),
			'今日のAI呼び出し'  => NFA_AIS_Claude::today_count() . ' 回' . ( ! empty( $u['date'] ) && $u['date'] === wp_date( 'Y-m-d' ) ? sprintf( '（入力 %s / 出力 %s トークン）', number_format( $u['input_tokens'] ), number_format( $u['output_tokens'] ) ) : '' ),
			'索引のバージョン'   => NFA_AIS_Index::version(),
			'プラグイン'        => NFA_AIS_VERSION,
		);
		echo '<table class="widefat striped" style="max-width:820px"><tbody>';
		foreach ( $rows as $k => $v ) {
			printf( '<tr><th style="width:220px">%s</th><td>%s</td></tr>', esc_html( $k ), esc_html( $v ) );
		}
		echo '</tbody></table>';
		echo '<p class="description">WP-CLI：<code>wp nfa status</code> / <code>wp nfa search "小学生の大会っていつ？"</code> / <code>wp nfa flush</code></p>';
	}
}
