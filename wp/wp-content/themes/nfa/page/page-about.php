<?php
/**
Template Name: ABOUT

 * <meta content="charset=UTF-8">
 * @package nfa
 */

$url=get_bloginfo('url');
$assets_url=get_assets_url();
$mypageinfo=get_mypageinfo();

get_header();
while ( have_posts() ) : the_post();?>
<!-- ===== ヒーロー ===== -->
<section class="hero">
  <div class="wrap">
    <div class="copy">
      <p class="crumb"><a href="<?= getPageUrl('home');?>">ホーム</a> ／ NFAについて</p>
      <p class="mark en">Life+1</p>
      <h1>サッカーは、人生を豊かにする<br>ツールである。</h1>
      <p class="lead">勝つことだけが目的ではありません。ボールを蹴った経験が、その人の人生に何かをひとつ足していく。長野県サッカー協会は、そう考えて活動しています。</p>
    </div>
  </div>
</section>

<!-- ===== ミッション ===== -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">01 / MISSION</span>
      <h2>私たちの使命</h2>
    </div>
    <div class="mission">
      <div>
        <div class="mvBox">
          <p class="lb en">MISSION</p>
          <p class="tx">Life＋1</p>
        </div>
        <p class="ds">サッカーを通じて人々の人生をより豊かに、より輝かせたいと願っています。日々の生活に喜びや感動、健康、そして人とのつながりをプラスする。サッカーを単なるスポーツではなく、<b>人生を豊かにするツール</b>として捉え、無限の可能性を追求します。</p>
      </div>
      <div class="ph"><span>写真｜世代の広がりが伝わるもの<br>（キッズ・育成年代・シニアの並置）</span></div>
    </div>
  </div>
</section>

<!-- ===== ビジョン（4語の循環を統合） ===== -->
<section class="sec pale">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">02 / VISION</span>
      <h2>ピッチの夢と地域への貢献が、<br>好循環で結ばれる信州。</h2>
      <p>私たちは、サッカーに情熱を燃やす一人ひとりの「夢」を育み、「挑戦」を支え、たくましい「成長」へと導きます。そうして育った人材が、やがて地域社会に「貢献」し、信州の未来を切り拓く力となる──。このビジョンを、4 つの言葉の循環で表しています。</p>
    </div>
    <div class="cycle">
      <div class="cell">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="idx en">01</p>
          <p class="sub en">DREAM</p>
          <h3>夢</h3>
          <p>一人ひとりの「夢」を育む。ボールに触れたその日から、なりたい自分が生まれる場所をつくります。</p>
        </div>
        <span class="arw"></span>
      </div>
      <div class="cell">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="idx en">02</p>
          <p class="sub en">CHALLENGE</p>
          <h3>挑戦</h3>
          <p>「挑戦」を支える。県大会、全国、その先へ。うまくいかない日も含めて、挑み続けられる環境を整えます。</p>
        </div>
        <span class="arw"></span>
      </div>
      <div class="cell">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="idx en">03</p>
          <p class="sub en">GROWTH</p>
          <h3>成長</h3>
          <p>たくましい「成長」へ導く。技術だけでなく、人として。指導者・審判・運営に関わる人も、同じように育ちます。</p>
        </div>
        <span class="arw"></span>
      </div>
      <div class="cell">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="idx en">04</p>
          <p class="sub en">CONTRIBUTION</p>
          <h3>貢献</h3>
          <p>育った人材が地域社会に「貢献」し、信州の未来を切り拓く力になります。</p>
        </div>
      </div>
    </div>
    <div class="cycleFoot">
      この循環は、一方通行ではありません。豊かになった信州が、また次の世代の夢を育む。<b>「終わらない好循環」が機能している状態</b>を、私たちは目指しています。
    </div>
  </div>
</section>

<!-- ===== バリュー ===== -->
<section class="sec dark">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">03 / VALUE</span>
      <h2>私たちの行動基準</h2>
      <p>「強さ」と「楽しさ」を支える、揺るぎない品格。3 つの言葉で表しています。</p>
    </div>
    <div class="vals">
      <div class="val">
        <p class="en-t">RESPECT</p>
        <h3>リスペクト</h3>
        <p class="sub2">互いを「大切に思う」こと</p>
        <p>サッカーに関わる仲間、対戦相手、審判、そして支えてくれる家族や地域の人々。私たちは多様性を認め合い、すべての人に対して感謝と敬意を持ち、安心・安全な環境を築きます。</p>
      </div>
      <div class="val">
        <p class="en-t">FAIR PLAY</p>
        <h3>フェアプレー</h3>
        <p class="sub2">誠実で「高潔な」振る舞い</p>
        <p>ピッチ上のルールを守ることはもちろん、ピッチ外の事業活動や組織運営においても高い倫理観（インテグリティ）を持ち、暴力・暴言・差別のない、社会から信頼されるフェアな組織であり続けます。</p>
      </div>
      <div class="val">
        <p class="en-t">SUSTAINABLE</p>
        <h3>サステナブル</h3>
        <p class="sub2">次世代へ「つなぐ」責任</p>
        <p>サッカーの力を使って、地域の社会課題（健康、教育、環境など）の解決に挑戦します。豊かな自然環境や健全な組織基盤を維持し、今より良い状態のサッカー環境を未来の子どもたちへつなぎます。</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== アクションプラン ===== -->
<section class="sec pale">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">04 / ACTION PLAN</span>
      <h2>中期経営計画（2027–2031）</h2>
      <p>2026年度に策定した中期経営計画です。2031年に在りたい姿へ向けて、3 つの柱で取り組みます。</p>
    </div>
    <div class="apLead">
      <p class="en-lb">VISION 2031</p>
      <h3>長野県の未来に「Life+1」を届ける</h3>
      <p>2028 信州国スポを契機とした、地域共創型 FA（サッカー協会）への進化を目指します。</p>
    </div>
    <div class="pillars3">
      <div class="pil3">
        <span class="n">1</span>
        <div class="bd">
          <h3>強化と育成<small>「Life＋1 フットボール」の体現</small></h3>
          <div class="kw"><span>中長期育成プランの確立</span><span>2028 信州国スポ 上位入賞</span><span>地域共創による良質な環境整備</span><span>指導者・審判の育成と共創パートナー化</span></div>
        </div>
      </div>
      <div class="pil3">
        <span class="n">2</span>
        <div class="bd">
          <h3>地域共創と社会課題解決への挑戦<small>長野県の「公共財」としての進化</small></h3>
          <div class="kw"><span>サッカーを通じた地方創生</span><span>自律的収益確保への新規事業</span></div>
        </div>
      </div>
      <div class="pil3">
        <span class="n">3</span>
        <div class="bd">
          <h3>組織基盤の変革<small>内部課題の克服と「持続可能な組織」構築</small></h3>
          <div class="kw"><span>DX 推進と効率化</span><span>組織設計と人材開発</span><span>ガバナンス強化と透明性確保</span><span>財務基盤の安定化</span></div>
        </div>
      </div>
    </div>
    <a class="dlBox" href="docs/NFA_actionplan_2027-2031.pdf" target="_blank" rel="noopener">
      <span class="ic en">PDF</span>
      <span class="nm">アクションプラン 詳細<small>重点目標・2031年の目指す姿・KPI（全3ページ）</small></span>
      <span class="dl">PDFを開く</span>
    </a>
  </div>
</section>

<!-- ===== 組織・体制 ===== -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">05 / ORGANIZATION</span>
      <h2>組織・体制（2026年度）</h2>
    </div>
    <div class="org">
      <div class="orgTop">
        <span class="orgBox head">会長</span>
        <span class="orgBox sub">監事</span>
      </div>
      <div class="orgLine"></div>
      <div class="orgRow2">
        <span class="orgBox">専務理事</span>
        <span class="orgBox sub">副会長</span>
        <span class="orgBox sub">副会長</span>
        <span class="orgBox sub">副会長</span>
        <span class="orgBox sub">副会長</span>
      </div>
      <div class="orgGrid">
        <div class="orgCol">
          <p class="hd">事務局</p>
          <ul><li>財務・会計</li><li>選手登録管理</li><li>渉外活動</li></ul>
        </div>
        <div class="orgCol">
          <p class="hd">経営企画室</p>
          <ul><li>方針・事業計画策定</li><li>ブランディング</li><li>マーケティング</li><li>ＤＸ</li><li>普及</li><li>グラスルーツ</li></ul>
        </div>
        <div class="orgCol">
          <p class="hd">ガバナンス</p>
          <ul><li>ガバナンス</li><li>コンプライアンス</li><li>規律・フェアプレー</li><li>医学</li></ul>
        </div>
        <div class="orgCol">
          <p class="hd">委員会</p>
          <ul><li>1種〜4種 委員会</li><li>女子委員会</li><li>シニア委員会</li><li>フットサル委員会</li><li>技術委員会</li><li>審判委員会</li></ul>
        </div>
      </div>
      <div class="orgFoot">
        <a class="dlBox" href="#">
          <span class="ic en">PDF</span>
          <span class="nm">事業報告・決算<small>定時総会資料・決算報告書（年度ごとの PDF 一覧）</small></span>
          <span class="dl">一覧を見る</span>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ===== 導線 ===== -->
<section class="sec pale">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">06 / NEXT</span>
      <h2>理念を、かたちにしている場所へ。</h2>
    </div>
    <div class="gates">
      <a href="<?= getPageUrl('grow');?>" class="gate">
        <p class="cat">育成強化</p>
        <h3>選手が、育っていく場所</h3>
        <p>長野スタイルの考え方、トレセン制度、指導者と審判の育成。</p>
        <span class="go">育成・強化について</span>
      </a>
      <a href="<?= getPageUrl('start');?>" class="gate">
        <p class="cat">普及活動</p>
        <h3>はじめる人が、増えていく場所</h3>
        <p>キッズフェスティバル、なでしこひろば、シニア・障がい者サッカー。</p>
        <span class="go">普及活動について</span>
      </a>
      <a href="<?= getPageUrl('partner');?>" class="gate">
        <p class="cat">地域活動</p>
        <h3>地域と、ともに創る場所</h3>
        <p>フットボールセンター整備、自治体との連携、企業の協賛。</p>
        <span class="go">地域活動について</span>
      </a>
    </div>
  </div>
</section>

<!-- ===== 下層 ===== -->
<section class="sec">
  <div class="wrap">
    <div class="secHead" style="margin-bottom:44px">
      <span class="no en">07 / ABOUT NFA</span>
      <h2>協会について</h2>
    </div>
    <div class="subs">
      <a href="#"><span class="t">協会概要</span><span class="d">名称・所在地・設立・役員構成</span></a>
      <a href="#"><span class="t">事業報告・決算</span><span class="d">年度ごとの PDF ダウンロード一覧</span></a>
      <a href="#"><span class="t">規程・様式</span><span class="d">各種規程と申請書式のダウンロード</span></a>
      <a href="#"><span class="t">郡市サッカー協会</span><span class="d">県内各地区の協会一覧</span></a>
    </div>
  </div>
</section>
<?php endwhile;
get_footer();?>
