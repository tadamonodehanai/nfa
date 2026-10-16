# NFA 2026（テーマ）

デザインデータ `NFA_site_design`（静的モック23ページ）を WordPress に置き換えたテーマです。実装方針は「NFAサイト WordPress実装方針（委員会ごとの運用）」に沿っています。

## 必要なプラグイン

| プラグイン | 役割 |
| --- | --- |
| NFA AI Search（`plugins/nfa-ai-search`） | 分類の登録（担当委員会・対象・目的・地域）、AI検索、取り込み |
| NFA Committee（`plugins/nfa-committee`） | 委員会ごとの権限、大会・リーグ資料（`nfa_doc`）、「重要」「注意喚起」「TOPに掲載」、委員会ごとの入力欄、協賛企業 |
| ACF Pro | 大会資料の行・協賛企業の入力画面（入力欄の定義は NFA Committee にある） |
| Contact Form 7 | お問い合わせフォーム |

プラグインが無効でもテーマは落ちません（表示が空になるだけ）。

## テンプレートとデザインの対応

| デザイン | テンプレート | データ |
| --- | --- | --- |
| index.html | `front-page.php` | ヒーロー・PICK UP＝「TOPに掲載」の記事、日程タブ＝大会資料の最新の更新、NEWS＝お知らせ |
| cat-1〜4・女子・フットサル・シニア・キッズ・審判・指導者・医学 | `taxonomy-nfa_committee.php` | 構成（大会＋リーグ／大会のみ／説明中心）・説明文・英字ラベル・電話番号は委員会の入力欄。説明セクションは `template-parts/committee/{スラッグ}.php` |
| list.html | `home.php` | お知らせ。`?c=` で委員会を絞り込み |
| detail.html | `single.php` | お知らせ1件。関連カテゴリ＝担当委員会 |
| （なし） | `single-nfa_doc.php` | 大会・リーグ資料1件 |
| about・start・grow・partner・contact・privacy・teams | `page-{スラッグ}.php` | 固定ページ（中身はテンプレートに固定。協賛企業の一覧は設定から） |
| search.html | `page-search.php` ＋ 本文の `[nfa_ai_search]` | AI検索（NFA AI Search） |
| 404.html | `404.php` | — |
| ヘッダー・フッター・協賛帯 | `header.php` / `footer.php` / `template-parts/spband.php` | 協賛企業＝設定 |

- `assets/` はデザインのものをそのまま入れています。`assets/js/main.js` だけ、WordPress の URL でページ遷移の演出が動くように調整しました。
- デザインの `X.html` へのリンクは `nfa_u( 'X.html' )`（`inc/urls.php`）で WordPress の URL に読み替えています。
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

## お問い合わせ（Contact Form 7）

`setup-pages` が、デザインと同じ項目・クラス名のフォーム「お問い合わせ」を作ります（記述は `inc/contact-form.txt`）。メールの送信先・文面は Contact Form 7 の画面で設定してください。

## テーマで使える関数（抜粋）

- `nfa_news( [ 'committee' => 'cat-4', 'n' => 5, 'flag' => 'top' ] )` … お知らせ
- `nfa_docs( [ 'committee' => 'cat-4', 'kind' => 'league' ] )` … 大会・リーグ資料
- `nfa_latest_rows( 'cat-4', 10 )` … 資料の最新の更新
- `nfa_term_meta( $term, 'lead' )` … 委員会の入力欄
- `nfa_badge( $post )` … 一覧のバッジ（表記と色）
