<?php
/**
 * ACF Pro の入力欄（コードで定義）。ACF Pro を有効にすると画面に出る。
 * 無効のあいだもテーマは helpers.php の nfa_field() で保存済みのデータを読める。
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

class NFA_Com_ACF {

	public static function init() {
		add_action( 'acf/init', array( __CLASS__, 'register' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notice' ) );
	}

	public static function notice() {
		$screen = get_current_screen();
		if ( function_exists( 'acf_add_local_field_group' ) || ! $screen || NFA_Com_Doc_Type::TYPE !== $screen->post_type || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>大会・リーグ資料の入力欄（年度・種別・資料の行）は ACF Pro で表示されます。ACF Pro を有効にしてください。</p></div>';
	}

	public static function register() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_nfa_doc',
				'title'    => '大会・リーグ資料',
				'fields'   => array(
					array( 'key' => 'field_nfa_doc_year', 'name' => 'nfa_doc_year', 'label' => '年度', 'type' => 'number', 'default_value' => (int) wp_date( 'Y' ), 'wrapper' => array( 'width' => 25 ) ),
					array(
						'key'           => 'field_nfa_doc_kind',
						'name'          => 'nfa_doc_kind',
						'label'         => '種別',
						'type'          => 'button_group',
						'choices'       => NFA_Com_Doc_Type::KINDS,
						'default_value' => 'tournament',
						'wrapper'       => array( 'width' => 25 ),
					),
					array( 'key' => 'field_nfa_doc_period', 'name' => 'nfa_doc_period', 'label' => '開催時期', 'type' => 'text', 'placeholder' => '例：8月〜9月', 'wrapper' => array( 'width' => 50 ) ),
					array(
						'key'          => 'field_nfa_doc_rows',
						'name'         => 'nfa_doc_rows',
						'label'        => '資料',
						'type'         => 'repeater',
						'layout'       => 'table',
						'button_label' => '行を追加',
						'instructions' => 'ファイルが未定の行は空のままにすると「準備中」と表示されます。',
						'sub_fields'   => array(
							array( 'key' => 'field_nfa_doc_section', 'name' => 'section', 'label' => '区分', 'type' => 'text', 'placeholder' => '要項・組み合わせ・結果 など' ),
							array( 'key' => 'field_nfa_doc_target', 'name' => 'target', 'label' => '対象', 'type' => 'text', 'placeholder' => 'U-12 など' ),
							array( 'key' => 'field_nfa_doc_label', 'name' => 'label', 'label' => '資料名', 'type' => 'text' ),
							array( 'key' => 'field_nfa_doc_file', 'name' => 'file', 'label' => 'ファイル', 'type' => 'file', 'return_format' => 'id' ),
							array( 'key' => 'field_nfa_doc_url', 'name' => 'url', 'label' => '外部URL', 'type' => 'url', 'instructions' => 'ファイルの代わりにリンクするとき' ),
							array( 'key' => 'field_nfa_doc_updated', 'name' => 'updated', 'label' => '更新日', 'type' => 'date_picker', 'display_format' => 'Y.m.d', 'return_format' => 'Ymd' ),
						),
					),
				),
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => NFA_Com_Doc_Type::TYPE ) ) ),
				'position' => 'acf_after_title',
			)
		);

		// お知らせ（投稿）の入力欄。旧サイトのカスタムフィールド（開催日・eventday・vk-ltc-link・slide-limit・hiddenday）の移行先
		acf_add_local_field_group(
			array(
				'key'      => 'group_nfa_post',
				'title'    => 'お知らせの詳細',
				'fields'   => array(
					array( 'key' => 'field_nfa_event_date', 'name' => 'nfa_event_date', 'label' => '開催日', 'type' => 'date_picker', 'display_format' => 'Y.m.d', 'return_format' => 'Ymd', 'wrapper' => array( 'width' => 30 ) ),
					array( 'key' => 'field_nfa_event_text', 'name' => 'nfa_event_text', 'label' => '開催日の表記', 'type' => 'text', 'instructions' => '「2026.10〜2026.11」のように日付1つで書けないとき', 'wrapper' => array( 'width' => 70 ) ),
					array( 'key' => 'field_nfa_link_url', 'name' => 'nfa_link_url', 'label' => 'リンク先URL', 'type' => 'url', 'instructions' => '入力すると、一覧から記事ではなくこのURLを直接開きます（外部ページの紹介など）' ),
					array( 'key' => 'field_nfa_top_until', 'name' => 'nfa_top_until', 'label' => 'TOPに掲載する期限', 'type' => 'date_picker', 'display_format' => 'Y.m.d', 'return_format' => 'Ymd', 'instructions' => '「TOPに掲載」の記事を、この日を過ぎたらTOPから外します（空欄なら外しません）', 'wrapper' => array( 'width' => 50 ) ),
					array( 'key' => 'field_nfa_new_until', 'name' => 'nfa_new_until', 'label' => '「新着」掲載終了日', 'type' => 'date_picker', 'display_format' => 'Y.m.d', 'return_format' => 'Ymd', 'instructions' => 'この日を過ぎたらTOPのNEWS・PICK UPから外します（一覧・検索には残ります）', 'wrapper' => array( 'width' => 50 ) ),
				),
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ) ),
				'position' => 'side',
			)
		);

		// チームの入力欄（旧サイトの team_activity_* ほか）
		acf_add_local_field_group(
			array(
				'key'      => 'group_nfa_team',
				'title'    => 'チームの情報',
				'fields'   => array(
					array( 'key' => 'field_team_city', 'name' => 'team_city', 'label' => '市町村', 'type' => 'text', 'placeholder' => '長野市' ),
					array( 'key' => 'field_team_activity_days', 'name' => 'team_activity_days', 'label' => '活動日', 'type' => 'checkbox', 'choices' => array_combine( NFA_Com_Team_Type::DAYS, NFA_Com_Team_Type::DAYS ), 'layout' => 'horizontal', 'return_format' => 'value' ),
					array( 'key' => 'field_team_activity_frequency', 'name' => 'team_activity_frequency', 'label' => '活動頻度', 'type' => 'textarea', 'rows' => 3 ),
					array( 'key' => 'field_team_activity_location', 'name' => 'team_activity_location', 'label' => '活動場所', 'type' => 'text' ),
					array( 'key' => 'field_team_website_url', 'name' => 'team_website_url', 'label' => 'Webサイト', 'type' => 'url' ),
					array( 'key' => 'field_team_pr_comment', 'name' => 'team_pr_comment', 'label' => 'PR', 'type' => 'textarea', 'rows' => 4 ),
				),
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => NFA_Com_Team_Type::TYPE ) ) ),
				'position' => 'acf_after_title',
			)
		);

		// 事業計画・事業報告（無料版の ACF で使える欄だけ）
		acf_add_local_field_group(
			array(
				'key'      => 'group_nfa_report',
				'title'    => '資料',
				'fields'   => array(
					array( 'key' => 'field_nfa_report_year', 'name' => 'nfa_report_year', 'label' => '掲載年', 'type' => 'number', 'required' => 1, 'default_value' => (int) wp_date( 'Y' ), 'instructions' => '「事業計画・事業報告」ページで、この年の欄に並びます（例：2026年の欄に「2026事業計画」「2025事業報告」）。', 'wrapper' => array( 'width' => 30 ) ),
					array( 'key' => 'field_nfa_report_file', 'name' => 'nfa_report_file', 'label' => 'ファイル', 'type' => 'file', 'return_format' => 'id', 'instructions' => '空欄なら「掲載なし」と表示します。', 'wrapper' => array( 'width' => 70 ) ),
					array( 'key' => 'field_nfa_report_note', 'name' => 'nfa_report_note', 'label' => '注記', 'type' => 'text', 'instructions' => 'その年の欄の下に表示します（例：※ファイルサイズが大きいため分割してあります。）' ),
				),
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => NFA_Com_Report_Type::TYPE ) ) ),
				'position' => 'acf_after_title',
			)
		);

		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title' => '協賛企業',
					'menu_title' => '協賛企業',
					'menu_slug'  => 'nfa-sponsors',
					'capability' => 'nfa_manage_sponsors',
					'icon_url'   => 'dashicons-awards',
					'position'   => 60,
				)
			);
			$repeater = fn( $key, $label ) => array(
				'key'          => 'field_nfa_sponsors_' . $key,
				'name'         => 'nfa_sponsors_' . $key,
				'label'        => $label,
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => '追加',
				'sub_fields'   => array(
					array( 'key' => 'field_nfa_sponsors_' . $key . '_name', 'name' => 'name', 'label' => '名前', 'type' => 'text' ),
					array( 'key' => 'field_nfa_sponsors_' . $key . '_url', 'name' => 'url', 'label' => 'URL', 'type' => 'url' ),
					array( 'key' => 'field_nfa_sponsors_' . $key . '_logo', 'name' => 'logo', 'label' => 'ロゴ', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'thumbnail', 'instructions' => 'サポーター（テキスト掲載）は不要' ),
				),
			);
			acf_add_local_field_group(
				array(
					'key'      => 'group_nfa_sponsors',
					'title'    => '協賛企業の並び',
					'fields'   => array(
						$repeater( 'gold', 'ゴールドパートナー' ),
						$repeater( 'official', 'オフィシャルパートナー' ),
						$repeater( 'supporters', 'サポーター（テキスト掲載）' ),
					),
					'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'nfa-sponsors' ) ) ),
				)
			);
		}
	}
}
