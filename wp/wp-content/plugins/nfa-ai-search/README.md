# NFA AI Search（段階1）

長野県サッカー協会サイトの情報探索（AI検索）プラグインです。設計書「NFAサイト AI検索 設計書」の段階1を実装しています。

| 段階 | 内容 | 状態 |
| --- | --- | --- |
| 1 | 3軸タクソノミー（対象・目的・地域）、言い換え辞書、質問→検索条件の変換、検索API、探索画面 | **このバージョン** |
| 2 | 記事本文・PDFのベクトル検索（Voyage AI＋ベクトル保存先の切り替え） | 未着手 |
| 3 | 出典付きの案内文（`/answer`）、0件時の言い換え提案をAIで | 未着手 |

APIキーがなくても動きます。そのあいだは、言い換え辞書によるルールで入力文を解釈します。

## テーマ制作の方へ

### 1. 探索画面

固定ページを作り、本文にショートコードを置きます（ページのスラッグは自由）。

```
[nfa_ai_search]
```

出力は、ワイヤー `NFA_wireframes_site/search.html` の本文と**同じマークアップ・クラス名**です。

```
.nfa-ais
├─ section.pHead（パンくず・見出し・説明文）
└─ div.stage > div.cols
   ├─ aside.side（条件から絞り込む／.facet > .opts > .opt）
   └─ main（.condBar / .thread > .entry・.turn / .dock）
```

- テーマの `page.php` は `the_content()` だけでよく、余計な外枠で囲まないでください（`.stage` が幅と余白を持ちます）
- ページ見出し（`.pHead`）もプラグインが出します。テーマ側で見出しを出すときは `header="off"`
- テーマ側の `search.css`（ワイヤーと同じCSS）があればそれも当たり、同じ見た目になります。プラグインのCSSは `.nfa-ais` の中だけに効きます
- 色・幅の変数（`--navy`, `--maxw` など）は、テーマの `:root` に定義があればそちらが優先されます
- 条件パネルの固定位置は、固定ヘッダーの高さ＋16px にしてください（初期値 92px＝ヘッダー76px＋16px）：`.nfa-ais{ --nfa-ais-sticky-top: 92px; }`
- 「条件パネルを隠す」は search.html と同じく `body` に `sideOff` クラスを付けます

文言は属性で変えられます。

```
[nfa_ai_search title="情報を探す" description="知りたいことを言葉のまま入力するか…" lead="何を知りたいですか？" sub="例えばこんな聞き方ができます。" placeholder="知りたいことを入力してください"]
```

`title` を省くと、固定ページのタイトルを使います。

- 「この条件のN件をすべて見る」は、タクソノミーで絞り込んだ一覧（例：`/?nfa_target=…&nfa_purpose=…`）に移動します。`archive.php` などで表示してください（行き先はフィルター `nfa_ais_list_url` で変更可）

### 2. ヘッダーの検索ボックス

```php
<?php if ( function_exists( 'nfa_ai_search_form' ) ) nfa_ai_search_form(); ?>
```

入力内容を `?q=` に付けて探索画面へ移動し、その言葉ですぐ検索します。独自のフォームを作る場合は、`nfa_ai_search_url()` を `action` にして、入力欄の `name` を `q` にしてください。

### 3. 記事詳細ページ（single.php など）

```php
<?php if ( function_exists( 'nfa_ai_search_the_axes' ) ) nfa_ai_search_the_axes(); ?>      // 3軸のチップ
<?php if ( function_exists( 'nfa_ai_search_source_notice' ) ) nfa_ai_search_source_notice(); ?> // 「AIはこのページを根拠に案内しています」
```

3軸の値を自分で表示したいときは `nfa_ai_search_axes()` が `['target'=>[...], 'purpose'=>[...], 'area'=>[...]]` を返します。

### 4. タクソノミー

| 軸 | スラッグ | 公開 |
| --- | --- | --- |
| 対象 | `nfa_target` | 探索画面の絞り込みに使う |
| 目的 | `nfa_purpose` | 同上 |
| 地域 | `nfa_area` | 同上 |
| 発信元（担当委員会） | `nfa_committee` | 管理用。公開側には出さない |

選択肢（用語）は「言い換え辞書」の内容から作られます。

テーマを作り替えても分類が消えないよう、プラグイン側で登録しています。テーマ側で `register_taxonomy` しないでください。

## 管理画面

「設定 → AI検索」

- **設定**：検索対象の投稿タイプ（許可リスト）、個別に除外するID、AIの利用、1日の上限、回数制限、ログの保存日数
- **取り込み**：差分の再取り込み（未取り込み・更新あり・削除待ちだけを処理）と全件の作り直し、分類が未設定の記事の一覧
- **言い換え辞書**：3軸の選択肢と、入力文を条件に読み替える言い換え（JSON）。初期値はワイヤー `search.html` から
- **検索ログ**：0件だった入力の多い順。辞書を育てるのに使います
- **状態**：検索対象の件数、APIキーの有無、今日のAI呼び出し回数

各投稿の編集画面のサイドバーに「AI検索の対象外にする」があります。

## 取り込み（差分の再取り込み）

記事ごとに「本文のハッシュ」と「取り込んだときの更新日時」をテーブル `wp_nfa_ais_docs` に記録し、次の3種類だけを処理します。

| 種類 | 内容 |
| --- | --- |
| 未取り込み | 検索対象なのに記録がない（新しい記事、移行してきた記事） |
| 更新あり | 記録より後に更新された記事。本文が同じなら「変更なし」で済ませ、段階2の処理（API費用がかかる）は呼ばない |
| 削除待ち | 非公開・削除・除外になった記事 |

- 記事の公開・更新・削除、「AI検索の対象外にする」の変更のたびに、その記事だけ自動で待ち行列に入り、WP-Cron が処理します
- まとめて記事を入れたあと（移行・インポート）は、管理画面「設定 → AI検索 → 取り込み」の **差分を取り込む** を押します。**SSH は不要**です。1回50件・15秒ずつ処理し、画面を開いている間は画面から、閉じても WP-Cron が続きを進めます
- 1日1回、自動で差分を確認します（直接データベースに入れた記事などの取りこぼし対策）
- 処理中にエラーが出た記事は記録され、次回の差分取り込みでやり直します
- 出典URLは保存せず、表示のたびに記事IDから作ります（移行でドメインが変わっても壊れません）

手元（wp-env）でまとめて実行するときは：

```bash
npx @wordpress/env run cli wp nfa reindex          # 差分
npx @wordpress/env run cli wp nfa reindex --all    # 全件を作り直す
npx @wordpress/env run cli wp nfa ingest-status    # 件数の確認
```

段階2（本文・PDFのベクトル化）は、次のアクションに処理を足して実装します。

```php
add_action( 'nfa_ais_document_changed', function ( $post_id, $text ) { /* 分割・ベクトル化して保存 */ }, 10, 2 );
add_action( 'nfa_ais_document_removed', function ( $post_id ) { /* その記事の断片を削除 */ } );
```

## 検索対象と除外ルール

許可リスト方式です。設定で許可した投稿タイプの「公開済み・パスワードなし」の投稿だけが対象です。

- Contact Form 7 のフォーム定義（`wpcf7_contact_form`）、Flamingo の送信内容・連絡先（`flamingo_*`）は、設定に関係なく対象外
- お問い合わせフォーム（`[contact-form-7 …]` やそのブロック）を含むページ、探索画面のページは自動で対象外
- 結果を返す直前にも、元の記事がいまも公開中で対象かを確認します
- 検索ログは入力文と件数だけを保存し、メールアドレス・電話番号は伏せ字、IPアドレスは保存しません

## APIキー（あとで設定）

`wp-config.php` に追加します。データベースやこのリポジトリには保存しません。

```php
define( 'NFA_ANTHROPIC_API_KEY', 'sk-ant-...' );
// 任意：モデルを変える場合
// define( 'NFA_ANTHROPIC_MODEL', 'claude-haiku-5-5' );
```

キーを設定すると、辞書で2つ以上の軸を読み取れなかった入力だけを Claude で解釈します（費用を抑えるため）。

## REST API

`GET /wp-json/nfa/v1/search?q=…&via=text|set&cond[target]=…&cond[purpose]=…&cond[area]=…`

- `via=text`：入力文を読み取って条件に足す（同じ軸は置き換え） / `set`：`cond` をそのまま使う
- ヘッダー `X-NFA-Nonce` が必要です（探索画面が自動で付けます）
- 応答：`cond`, `total`, `say`（AIの返事）, `results`（5件）, `more`, `next`（さらに絞り込むなら）, `rescue`（0件時に外す条件）, `popular`, `facets`（パネルの件数）

## 開発環境（wp-env）

リポジトリのルートで：

```bash
npx @wordpress/env start
```

- サイト：http://localhost:8888 （管理画面 http://localhost:8888/wp-admin 、ユーザー名 `admin`／パスワード `password`）
- サンプル記事の作成：`npx @wordpress/env run cli wp nfa sample`
- 動作確認：`npx @wordpress/env run cli wp nfa selftest`
- 検索を試す：`npx @wordpress/env run cli wp nfa search "小学生の大会っていつ？"`
- サンプルの削除：`npx @wordpress/env run cli wp nfa sample --delete`

## 本番への配置

`wp/wp-content/plugins/nfa-ai-search/` をそのまま本番の `wp-content/plugins/` に置き、管理画面で有効化します。ビルドは不要です。
