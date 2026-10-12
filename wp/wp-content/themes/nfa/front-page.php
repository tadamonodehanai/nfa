<?php
/**
 * <meta content="charset=UTF-8">
 * @package nfa
 */
$url=get_bloginfo('url');
$assets_url=get_assets_url();

get_header();?>
<!-- ================= A. ファーストビュー ================= -->
<section class="fv">
  <div class="visual">
    <div class="ph"><span>グラフィック／ムービー<br>長野の風景と、世代の広がり（16:9 ループ再生）</span></div>
  </div>
  <div class="wrap">
    <div class="copy">
      <p class="kicker en">LIFE + 1</p>
      <h1>サッカーが、<br>人生に<em>ひとつ</em>を足していく。</h1>
      <p class="lead">長野県サッカー協会は、勝つためだけの場所ではありません。夢を持ち、挑戦し、成長し、そして誰かに貢献する。その循環をこの県のすみずみに広げていくために、私たちは活動しています。</p>
      <div class="fvActs">
        <a href="<?= getPageUrl('about');?>" class="btnMain">Life+1 とは<span class="en">→</span></a>
        <a href="<?= getPageUrl('start');?>" class="btnGhost">はじめての方へ<span class="en">→</span></a>
      </div>
      <div class="fvTopics">
        <a href="detail.html" class="fvT">
          <span class="thumb">写真</span>
          <span class="bd">
            <span class="lb">イベント</span>
            <span class="t">キッズサッカーフェスティバル（中信）参加受付中</span>
            <span class="d en">9.23 TUE ─ 松本市</span>
          </span>
        </a>
        <a href="detail.html" class="fvT">
          <span class="thumb">写真</span>
          <span class="bd">
            <span class="lb">セミナー</span>
            <span class="t">指導者リフレッシュ研修会（東信会場）申込受付</span>
            <span class="d en">10.4 SUN ─ 上田市</span>
          </span>
        </a>
        <a href="detail.html" class="fvT">
          <span class="thumb">写真</span>
          <span class="bd">
            <span class="lb">イベント</span>
            <span class="t">なでしこひろば 秋季開催のご案内</span>
            <span class="d en">10.12 MON ─ 県内各地</span>
          </span>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ================= B. コンセプト ================= -->
<section class="concept">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">01 / CONCEPT</span>
      <h2>夢を持ち、挑戦し、成長し、貢献する。</h2>
      <p>NFA のビジョンに書かれている 4 つの言葉です。この循環を生み出すことが、私たちの目指すかたちそのものです。</p>
    </div>

    <div class="cycle">
      <div class="cell">
        <p class="idx en">01</p>
        <h3>夢</h3>
        <p>ボールに触れたその日から、なりたい自分が生まれる。年齢も経験も問わず、はじめられる場所をつくります。</p>
        <span class="arw"></span>
      </div>
      <div class="cell">
        <p class="idx en">02</p>
        <h3>挑戦</h3>
        <p>県大会、全国、その先へ。うまくいかない日も含めて、挑み続けられる環境を整えます。</p>
        <span class="arw"></span>
      </div>
      <div class="cell">
        <p class="idx en">03</p>
        <h3>成長</h3>
        <p>技術だけでなく、人として育つ。指導者・審判・運営に関わる人も、同じように成長していきます。</p>
        <span class="arw"></span>
      </div>
      <div class="cell">
        <p class="idx en">04</p>
        <h3>貢献</h3>
        <p>受け取ったものを、次の世代へ返していく。サッカーが地域に還っていく循環をつくります。</p>
      </div>
    </div>

    <div class="conceptFoot">
      <p>この理念は、JFA の掲げる考え方を県協会として受け継いだものです。中期経営計画（2027–2031）に、具体的な計画として書かれています。</p>
      <a href="about.html" class="linkArw">NFA について、くわしく</a>
    </div>
  </div>
</section>

<!-- ================= C. 3つの入口 ================= -->
<section class="gates">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">02 / FOR YOU</span>
      <h2>あなたは、どの立場でサッカーに関わっていますか。</h2>
      <p>中期計画の 3 本柱に沿って、入口を分けています。ご自身に近いものからお進みください。</p>
    </div>

    <div class="gateGrid">
      <a href="<?= getPageUrl('grow');?>" class="gate">
        <div class="ph"><span>写真｜育成年代の練習風景</span></div>
        <div class="body">
          <p class="cat">育成強化</p>
          <h3>うちの子に、<br>何ができるだろう。</h3>
          <p class="who">小中学生の保護者／選手本人／指導者・審判の方へ</p>
          <ul>
            <li>長野スタイル（育成の考え方）</li>
            <li>トレセン・選抜の仕組み</li>
            <li>指導者・審判のライセンス講習</li>
          </ul>
          <span class="go">育成・強化について</span>
        </div>
      </a>

      <a href="<?= getPageUrl('start');?>" class="gate">
        <div class="ph"><span>写真｜フェスティバルの様子</span></div>
        <div class="body">
          <p class="cat">普及活動</p>
          <h3>はじめて<br>みたいけれど。</h3>
          <p class="who">未経験の子どもとご家族／女子・シニア世代／障がい者サッカー関係の方へ</p>
          <ul>
            <li>はじめての方へ（体験・教室）</li>
            <li>なでしこひろば・シニアの活動</li>
            <li>障がい者サッカーの取り組み</li>
          </ul>
          <span class="go">普及活動について</span>
        </div>
      </a>

      <a href="<?= getPageUrl('partner');?>" class="gate">
        <div class="ph"><span>写真｜地域の大会・自治体連携</span></div>
        <div class="body">
          <p class="cat">地域活動</p>
          <h3>一緒に、<br>何ができるだろう。</h3>
          <p class="who">自治体職員／企業・スポンサー候補／地域クラブ・郡市協会の方へ</p>
          <ul>
            <li>フットボールセンター整備</li>
            <li>大会の招致・共催</li>
            <li>協賛・パートナーシップ</li>
          </ul>
          <span class="go">地域活動について</span>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- ================= D. 情報を探す ================= -->
<section class="search">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">03 / SEARCH</span>
      <h2>探しているものを、<br>そのまま聞いてください。</h2>
      <p>どの委員会の担当かを考える必要はありません。知りたいことを言葉のまま入力すると、必要な情報にたどり着けます。</p>
    </div>
<form action="<?= nfa_ai_search_url();?>" method="get">
    <div class="searchBox">
		
      <input type="text" name="q" placeholder="例）小学生の大会っていつ？　審判の更新はどうすれば？">
      <button type="submit" class="send">探す</button>
		
    </div>
</form>
    <p class="tagLead">よく見られている条件</p>
    <div class="tags">
      <a href="<?= getPageUrl('search');?>" class="tag">小学生（4種）</a>
      <a href="<?= getPageUrl('search');?>" class="tag">中学生（3種）</a>
      <a href="<?= getPageUrl('search');?>" class="tag">高校生（2種）</a>
      <a href="<?= getPageUrl('search');?>" class="tag">大人・社会人（1種）</a>
      <a href="<?= getPageUrl('search');?>" class="tag">女子</a>
      <a href="<?= getPageUrl('search');?>" class="tag">シニア</a>
      <a href="<?= getPageUrl('search');?>" class="tag">審判</a>
      <a href="<?= getPageUrl('search');?>" class="tag">指導者</a>
      <a href="<?= getPageUrl('search');?>" class="tag">フットサル</a>
    </div>

    <div class="searchFeat">
      <div>
        <h4>質問から答えへ</h4>
        <p>話し言葉のまま入力できます。カテゴリを選び直す必要はありません。</p>
      </div>
      <div>
        <h4>条件を重ねて絞る</h4>
        <p>タグを選ぶほど候補が絞られます。逆に増えることはありません。</p>
      </div>
      <div>
        <h4>段階的に育てる</h4>
        <p>公開時はルールベースで稼働し、運用しながら精度を上げていきます。</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= E. 最新のうごき ================= -->
<section class="news">
  <div class="wrap">
    <div class="secHead" style="margin-bottom:38px">
      <span class="no en">04 / NEWS</span>
      <h2>最新のうごき</h2>
    </div>

    <div class="alert">
      <span class="lb">重要なお知らせ</span>
      <p>【注意喚起】協会名を騙る不審な連絡についてのご報告と、ご確認のお願い</p>
    </div>

    <div class="newsTop">
      <div class="newsTabs">
        <button class="on">すべて</button>
        <button>お知らせ</button>
        <button>大会情報</button>
        <button>試合結果</button>
        <button>イベント・セミナー</button>
        <button>登録・手続き</button>
      </div>
    </div>

    <div class="newsList">
      <a href="detail.html">
        <span class="date en">2026.08.27</span>
        <span class="cat">イベント・セミナー</span>
        <span class="ttl">キッズサッカーフェスティバル（中信会場）参加チーム受付を開始しました</span>
      </a>
      <a href="detail.html">
        <span class="date en">2026.08.26</span>
        <span class="cat">大会情報</span>
        <span class="ttl">第◯回 長野県ユース（U-15）サッカー選手権大会 組み合わせ決定のお知らせ</span>
      </a>
      <a href="detail.html">
        <span class="date en">2026.08.24</span>
        <span class="cat">試合結果</span>
        <span class="ttl">長野県中学校総合体育大会 サッカー競技 最終結果</span>
      </a>
      <a href="detail.html">
        <span class="date en">2026.08.21</span>
        <span class="cat">登録・手続き</span>
        <span class="ttl">2026年度 チーム登録の受付期間および手続き方法について</span>
      </a>
      <a href="detail.html">
        <span class="date en">2026.08.19</span>
        <span class="cat">お知らせ</span>
        <span class="ttl">JFA公認 D級コーチ養成講習会（北信会場）の開催と受講者募集について</span>
      </a>
      <a href="detail.html">
        <span class="date en">2026.08.15</span>
        <span class="cat">お知らせ</span>
        <span class="ttl">なでしこひろば 秋季開催のご案内 ─ 参加者を募集しています</span>
      </a>
    </div>

    <div class="newsMore"><a href="list.html">お知らせ・大会情報をすべて見る</a></div>
  </div>
</section>

<!-- ================= F. ともに創る ================= -->
<!-- ================= コラム ================= -->
<section class="column">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">COLUMN / REPORT</span>
      <h2>コラム・レポート</h2>
      <p>支援の使いみち、活動の結果報告、私たちが考えていること。お知らせには収まらない話を、コラムとして残しています。</p>
    </div>
    <div class="colGrid">
      <a href="#" class="colCard">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="cat">使いみち報告</p>
          <h3>協賛金はどこへ行くのか ─ 2026年度・4種の大会運営の内訳</h3>
          <span class="d en">2026.08.10</span>
        </div>
      </a>
      <a href="#" class="colCard">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="cat">活動レポート</p>
          <h3>信州国スポまであと2年。トレセンの現場で起きていること</h3>
          <span class="d en">2026.07.28</span>
        </div>
      </a>
      <a href="#" class="colCard">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="cat">協会の考え</p>
          <h3>「勝つこと」と「続けること」のあいだで ─ 育成年代の指導方針について</h3>
          <span class="d en">2026.07.15</span>
        </div>
      </a>
    </div>
    <div class="colMore"><a href="#">コラム・レポートをすべて見る</a></div>
  </div>
</section>

<section class="spons">
  <div class="wrap">
    <div class="sponsIn">
      <div>
        <span class="no en">05 / PARTNERSHIP</span>
        <h2>その支援が、<br>どこに届くのか。</h2>
        <p class="lead">私たちは 2020 年宣言として、5 つの約束を掲げています。いただいた協賛は、この約束を実現するための活動に使われます。使いみちが見えるかたちで、企業のみなさまとご一緒したいと考えています。</p>
        <ul class="promise">
          <li><span class="en">01</span>県民の 10 人に 1 人がサッカーファミリーに</li>
          <li><span class="en">02</span>県内 4 地区にフットボールセンターを整備</li>
          <li><span class="en">03</span>代表チームが全国大会で上位進出</li>
          <li><span class="en">04</span>大会・チームにウェルフェアオフィサーを配置</li>
          <li><span class="en">05</span>2027 国民スポーツ大会で開催成功と優勝</li>
        </ul>
        <div class="sponsActs">
          <a href="<?= getPageUrl('partner');?>" class="btnNavy">協賛について</a>
          <a href="<?= getPageUrl('partner');?>" class="btnLine">活動レポートを見る</a>
        </div>
      </div>
      <div class="ph"><span>写真｜協賛企業ロゴ一覧<br>または支援が活きている現場の様子</span></div>
    </div>
  </div>
</section>



<!-- ===== 関連リンク（JFA・県内クラブ／TOPのみ） ===== -->
<section class="jfaZone">
  <div class="jzIn">
    <div class="jzHead">
      <p class="jzTtl">RELATED LINKS</p>
      <p class="jzDs">日本サッカー協会（JFA）の各種活動・県内クラブの公式サイトへのリンクです。すべて別タブで開きます。</p>
    </div>
    <p class="jzLb">JFA ─ 日本サッカー協会</p>
    <div class="jzGrid">
      <a class="jzCell" href="https://www.jfa.jp/" target="_blank" rel="noopener"><span class="nm"><small>JFA</small>日本サッカー協会</span></a>
      <a class="jzCell" href="https://www.jfa.jp/samuraiblue/" target="_blank" rel="noopener"><span class="nm"><small>SAMURAI BLUE</small>サッカー日本代表</span></a>
      <a class="jzCell" href="https://www.jfa.jp/nadeshikojapan/" target="_blank" rel="noopener"><span class="nm"><small>NADESHIKO JAPAN</small>なでしこジャパン</span></a>
      <a class="jzCell" href="https://www.jfa.jp/youth_development/players_first/" target="_blank" rel="noopener"><span class="nm"><small>PLAYERS FIRST</small>プレーヤーズファースト</span></a>
      <a class="jzCell" href="https://www.jfa.jp/respect/" target="_blank" rel="noopener"><span class="nm"><small>RESPECT</small>リスペクトプロジェクト</span></a>
      <a class="jzCell" href="https://www.jfa.jp/violence_eradication/" target="_blank" rel="noopener"><span class="nm"><small>DECLARATION</small>暴力等根絶宣言</span></a>
      <a class="jzCell" href="http://www.jiff.football/" target="_blank" rel="noopener"><span class="nm"><small>JIFF</small>日本障がい者サッカー連盟</span></a>
      <a class="jzCell" href="https://jfaid.jfa.jp/uniid-server/login" target="_blank" rel="noopener"><span class="nm"><small>KICKOFF</small>JFA ID／登録システム</span></a>
    </div>
    <p class="jzLb">県内クラブ</p>
    <div class="jzGrid club">
      <a class="jzCell" href="http://www.yamaga-fc.com/" target="_blank" rel="noopener"><span class="nm"><small>MATSUMOTO</small>松本山雅FC</span></a>
      <a class="jzCell" href="http://parceiro.co.jp/" target="_blank" rel="noopener"><span class="nm"><small>NAGANO</small>AC長野パルセイロ</span></a>
      <a class="jzCell" href="http://boaluz-nagano.com/" target="_blank" rel="noopener"><span class="nm"><small>FUTSAL</small>ボアルース長野</span></a>
    </div>
  </div>
</section>
<?php get_footer(); ?>
