<?php
/**
 * 旧サイトのカテゴリー → 新しい分類の読み替え表（移行用）。
 *
 * 旧カテゴリーは新サイトでは使わない。取り込むときに、次の値に読み替えるだけに使う。
 *   committee  担当委員会のスラッグ（空なら決めない）
 *   purpose    目的のスラッグ（schedule / result / reg / course / event / news）
 *   flag       important（重要）| alert（注意喚起）。取り込みでは使わない（新サイトの運用で付け直す）
 *   category   投稿のカテゴリー（活動報告 action_report）。なければ「お知らせ」＞「お知らせ・募集」
 * 担当委員会がどのカテゴリーからも決まらない記事は「協会」にする。目的が決まらない記事は「お知らせを読む」。
 * 対象は担当委員会の初期値（例：3種 → 中学生（3種））、地域はタイトルの地区名から付ける。
 *
 * @package NFA_Committee
 */

defined( 'ABSPATH' ) || exit;

return array(
	// 年代別
	'cat_4_results'              => array( 'committee' => 'cat-4', 'purpose' => 'result' ),
	'cat_4_news'                 => array( 'committee' => 'cat-4', 'purpose' => 'news' ),
	'cat_3_results'              => array( 'committee' => 'cat-3', 'purpose' => 'result' ),
	'cat_3_news'                 => array( 'committee' => 'cat-3', 'purpose' => 'news' ),
	'cat_3c_results'             => array( 'committee' => 'cat-3', 'purpose' => 'result' ),   // 3種（クラブ）
	'cat_3c_news'                => array( 'committee' => 'cat-3', 'purpose' => 'news' ),
	'cat_2_results_result'       => array( 'committee' => 'cat-2', 'purpose' => 'result' ),
	'cat_2_news'                 => array( 'committee' => 'cat-2', 'purpose' => 'news' ),
	'adults_results'             => array( 'committee' => 'cat-1', 'purpose' => 'result' ),
	'adults_news'                => array( 'committee' => 'cat-1', 'purpose' => 'news' ),
	'cat_1u_results'             => array( 'committee' => 'cat-1', 'purpose' => 'result' ),   // 1種（大学）
	'cat_1u_news'                => array( 'committee' => 'cat-1', 'purpose' => 'news' ),
	'emperorscup_news'           => array( 'committee' => 'cat-1', 'purpose' => 'news' ),     // 天皇杯
	'天皇杯-試合結果'                  => array( 'committee' => 'cat-1', 'purpose' => 'result' ),
	// 種目・対象別
	'cat_w_results_result'       => array( 'committee' => 'cat-w', 'purpose' => 'result' ),
	'cat_w_news'                 => array( 'committee' => 'cat-w', 'purpose' => 'news' ),
	'empressscup_news'           => array( 'committee' => 'cat-w', 'purpose' => 'news' ),     // 皇后杯
	'cat_s_results_result'       => array( 'committee' => 'cat-s', 'purpose' => 'result' ),
	'cat_s_news'                 => array( 'committee' => 'cat-s', 'purpose' => 'news' ),
	'cat_f_results_result'       => array( 'committee' => 'cat-f', 'purpose' => 'result' ),
	'cat_f_news'                 => array( 'committee' => 'cat-f', 'purpose' => 'news' ),
	'futsal_news'                => array( 'committee' => 'referee', 'purpose' => 'course' ),     // フットサル講習会（審判 News の下）
	'kids_news'                  => array( 'committee' => 'grassroots', 'purpose' => 'news' ),
	'festival_news'              => array( 'committee' => 'grassroots', 'purpose' => 'event' ),
	// 審判・指導者・医学
	'referee_news'               => array( 'committee' => 'referee', 'purpose' => 'news' ),
	'referee-seminar'            => array( 'committee' => 'referee', 'purpose' => 'course' ),
	'soccer_news'                => array( 'committee' => 'referee', 'purpose' => 'course' ),     // サッカー講習会（審判 News の下）
	'technical_news'             => array( 'committee' => 'technical', 'purpose' => 'news' ),
	'technical_refreshment_news' => array( 'committee' => 'technical', 'purpose' => 'course' ),  // リフレッシュ研修
	'technical_cd_news'          => array( 'committee' => 'technical', 'purpose' => 'course' ),  // C級・D級
	'technical_gk_news'          => array( 'committee' => 'technical', 'purpose' => 'course' ),
	'technical_kids_news'        => array( 'committee' => 'technical', 'purpose' => 'course' ),  // キッズリーダー
	'selection_news'             => array( 'committee' => 'technical', 'purpose' => 'event' ),   // トレセン選考
	'medical_news'               => array( 'committee' => 'medical', 'purpose' => 'news' ),
	// 事務局・協会全体
	'office_news'                => array( 'committee' => 'association', 'purpose' => 'news' ),   // 協会 News
	'registration_news'          => array( 'committee' => 'association', 'purpose' => 'reg' ),    // 登録 News（協会 News の下）
	'70-anniversary'             => array( 'committee' => 'association', 'purpose' => 'news' ),
	// 委員会を決めないもの（目的・フラグだけ）
	'news_top'                   => array(),
	'entry'                      => array( 'purpose' => 'reg' ),     // エントリー
	'wanted'                     => array( 'purpose' => 'event' ),   // 募集
	'report'                     => array( 'purpose' => 'news', 'category' => 'action_report' ),   // 開催報告
	'activity-reports'           => array( 'purpose' => 'news', 'category' => 'action_report' ),   // 活動報告
	'students-voice'             => array( 'purpose' => 'news' ),
	'important'                  => array( 'flag' => 'important' ),
	'attention'                  => array( 'flag' => 'alert' ),
);
