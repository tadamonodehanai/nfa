# NFA 2026（テーマ）

デザインデータ `NFA_site_design`（静的モック。2026-10-20 版）を WordPress に置き換えたテーマです。実装方針は「NFAサイト WordPress実装方針（委員会ごとの運用）」に沿っています。

## 必要なプラグイン

| プラグイン | 役割 |
| --- | --- |
| NFA AI Search（`plugins/nfa-ai-search`） | 分類の登録（担当委員会・対象・目的・地域）、AI検索、取り込み |
| NFA Committee（`plugins/nfa-committee`） | 委員会ごとの権限、大会・リーグ資料（`nfa_doc`）、チーム（`team`）、事業計画・事業報告（`nfa_report`）、記事の種類（カテゴリー）、「重要」「注意喚起」「TOPに掲載」、委員会ごとの入力欄、協賛企業、旧サイトへのリンクの読み替え |
| ACF（無料版）／ACF Pro | 入力画面（入力欄の定義は NFA Committee にある）。大会資料の行・協賛企業は Pro が必要 |
| Contact Form 7 | お問い合わせ・JFAフェスティバル掲載依頼のフォーム |

プラグインが無効でもテーマは落ちません（表示が空になるだけ）。

## テンプレートとデザインの対応

| デザイン | テンプレート | データ |
| --- | --- | --- |
| index.html | `front-page.php` | ヒーロー・PICK UP＝「TOPに掲載」の記事、日程タブ＝大会資料の最新の更新、NEWS＝お知らせ、ACTIVITY REPORT＝カテゴリー「活動報告」 |
| cat-1〜4・女子・フットサル・シニア・キッズ・審判・指導者・医学 | `taxonomy-nfa_committee.php` | 構成（大会＋リーグ／大会のみ／説明中心）・説明文・英字ラベル・電話番号は委員会の入力欄。大会・リーグは最新の年度だけ。説明の欄は `template-parts/committee/{スラッグ}.php`、関連ページの欄は `{スラッグ}-guide.php` |
| archive-cat-*.html | `taxonomy-nfa_committee-archive.php` | 過去の日程・試合結果（`/committee/{スラッグ}/archive/`）。大会資料を最新の年度を除いて年度別に |
| list.html | `home.php` | お知らせ。区分 `?c=`（担当委員会。協会＝`association`）× カテゴリー `?type=` で絞り込み |
| detail.html | `single.php` | お知らせ1件（お知らせ・募集型）。本体は `template-parts/single/article.php` |
| detail-report.html・detail-important.html | `templates/post-report.php`・`post-important.php` | 投稿の「テンプレート」で選ぶ（開催報告型・重要なお知らせ型）。カテゴリーとはまだ結び付けていない |
| team-*.html・teams.html | `single-team.php`・`page-teams.php` | チーム（`/team/{スラッグ}/`）。パンくずは「はじめる › チームを探す」 |
| （なし） | `single-nfa_doc.php` | 大会・リーグ資料1件 |
| about・start・grow・partner・contact・privacy・academy・coach-course・download・emperorscup・facilities・festival・links・nagano-style・referee-schedule・registration・reports・request・request-jfa・sitemap・terms・tracen | `page-{スラッグ}.php` | 固定ページ（中身はテンプレートに固定）。協賛企業＝設定、事業計画・事業報告＝`nfa_report`、フォーム＝Contact Form 7 |
| search.html | `page-search.php` ＋ 本文の `[nfa_ai_search]` | AI検索（NFA AI Search） |
| 404.html | `404.php` | — |
| ヘッダー・フッター・協賛帯 | `header.php` / `footer.php` / `template-parts/spband.php` | 協賛企業＝設定 |

- `assets/` はデザインのものをそのまま入れています。`assets/js/main.js` だけ、WordPress の URL でページ遷移の演出が動くように調整しました（先頭のコメント参照）。
- デザインの `X.html` へのリンクは `nfa_u( 'X.html' )`（`inc/urls.php`）で WordPress の URL に読み替えています。
- デザインに残っている旧サイト（www.nagano-fa.or.jp）へのリンクは `nfa_href( '…' )` で、表示のたびに新サイトの中のリンク（取り込んだファイル・記事・新しいページ）に読み替えます。読み替え表は NFA Committee の `includes/legacy-links.php`。
- ヘッダー・フッター・固定ページは、デザインから変換したものです。デザインを直したら、変換し直すか同じ箇所を直してください。
- 委員会ページの URL は仮に `/committee/{スラッグ}/`（スラッグはデザインのファイル名：`cat-4`、`referee` など）。変えるときは `nfa_committee_rewrite_slug` フィルターで変更し、パーマリンク設定を保存し直します。

## 初期設定（開発環境・本番とも）

```bash
wp nfa-committee setup-pages    # 固定ページ、TOP／お知らせ一覧の割り当て、Contact Form 7 のフォーム
```

開発環境で、デザインの実データ（既存サイトから収集した526件）を入れるとき：

```bash
wp nfa-committee import-demo          # お知らせ337件・大会資料（32大会・資料189件）
wp nfa reindex                        # AI検索の取り込み
wp nfa-committee import-demo --delete # 消すとき
```

## お問い合わせ・掲載依頼（Contact Form 7）

`setup-pages` が、デザインと同じ項目・クラス名のフォーム「お問い合わせ」（`inc/contact-form.txt`）と「JFAフェスティバル掲載依頼」（`inc/request-jfa-form.txt`）を作ります。メールの送信先・文面は Contact Form 7 の画面で設定してください。

## テーマで使える関数（抜粋）

- `nfa_news( [ 'committee' => 'cat-4', 'n' => 5, 'flag' => 'top' ] )` … お知らせ
- `nfa_docs( [ 'committee' => 'cat-4', 'kind' => 'league' ] )` … 大会・リーグ資料
- `nfa_latest_rows( 'cat-4', 10 )` … 資料の最新の更新
- `nfa_term_meta( $term, 'lead' )` … 委員会の入力欄
- `nfa_badge( $post )` … 一覧のバッジ（表記と色）
