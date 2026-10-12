<?php
/**
Template Name: PARTNER

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
  <div class="visual"><div class="ph onDark"><span>写真｜支援が活きている現場<br>（大会運営・施設・遠征のいずれか）</span></div></div>
  <div class="wrap">
    <div class="copy">
      <p class="crumb"><a href="<?= getPageUrl('home');?>">ホーム</a> ／ ともに創る</p>
      <p class="kicker en">PARTNERSHIP</p>
      <h1>その支援が、<br>どこに届くのか。</h1>
      <p class="lead">長野県サッカー協会は、2020年宣言として 5 つの約束を掲げています。いただいた協賛は、この約束を実現するための活動に使われます。何に使われ、何が生まれたのか。見えるかたちで、企業のみなさまとご一緒したいと考えています。</p>
    </div>
  </div>
</section>

<!-- ===== 規模の数字 ===== -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">01 / SCALE</span>
      <h2>長野県のサッカーは、<br>これだけの人が関わっています。<span class="todo">数値は要確認</span></h2>
      <p>県内の登録者・チーム・大会の規模です。協賛は、この人数との接点を持つことを意味します。</p>
    </div>
    <div class="stats">
      <div class="stat"><p class="n en">◯◯,◯◯◯<small>名</small></p><p class="lb">県内サッカー登録者数<br>（選手・指導者・審判）</p></div>
      <div class="stat"><p class="n en">◯,◯◯◯<small>チーム</small></p><p class="lb">県内登録チーム数<br>（1種〜4種・女子・シニア）</p></div>
      <div class="stat"><p class="n en">◯◯◯<small>大会</small></p><p class="lb">年間の主催・主管大会数</p></div>
      <div class="stat"><p class="n en">◯◯<small>万PV</small></p><p class="lb">公式サイト 年間閲覧数<br>（大会シーズンに集中）</p></div>
    </div>
  </div>
</section>

<!-- ===== 5つの約束 ===== -->
<section class="sec pale">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">02 / PROMISE</span>
      <h2>5 つの約束が、支援の届く先です。</h2>
      <p>2020年宣言として掲げた 5 つの目標。それぞれに、支援が向かう活動と、企業にとっての接点があります。</p>
    </div>
    <div class="promise">
      <div class="pRow hd">
        <div>No.</div><div>2020年宣言（協会の約束）</div><div>支援が向かう活動</div><div>企業にとっての接点</div>
      </div>
      <div class="pRow">
        <div class="num en">01</div>
        <div class="what">県民の 10 人に 1 人が<br>サッカーファミリーに</div>
        <div class="where">普及活動・フェスティバル運営、巡回指導</div>
        <div class="value">県内全域・全世代へのリーチ</div>
      </div>
      <div class="pRow">
        <div class="num en">02</div>
        <div class="what">県内 4 地区に<br>フットボールセンターを整備</div>
        <div class="where">施設整備・環境づくり、備品の更新</div>
        <div class="value">地域拠点での継続的な接点</div>
      </div>
      <div class="pRow">
        <div class="num en">03</div>
        <div class="what">代表チームが<br>全国大会で上位進出</div>
        <div class="where">遠征費用・強化活動、トレセン運営</div>
        <div class="value">成果とともに露出する機会</div>
      </div>
      <div class="pRow">
        <div class="num en">04</div>
        <div class="what">大会・チームに<br>ウェルフェアオフィサーを配置</div>
        <div class="where">安全・安心な環境の担保、講習の実施</div>
        <div class="value">社会的責任の共同発信</div>
      </div>
      <div class="pRow">
        <div class="num en">05</div>
        <div class="what">2027 国民スポーツ大会で<br>開催成功と優勝</div>
        <div class="where">大会運営・県外への発信</div>
        <div class="value">全国規模での認知</div>
      </div>
    </div>
  </div>
</section>

<!-- ===== 使いみち ===== -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">03 / ALLOCATION</span>
      <h2>いただいた協賛の、使いみち。<span class="todo">公開範囲は要判断</span></h2>
      <p>どの活動にどれだけ使われたかを、毎年度お知らせしています。</p>
    </div>
    <div class="useIn">
      <div class="bars">
        <div class="bar">
          <div class="top"><span class="lb">普及活動・フェスティバル</span><span class="pc en">◯◯%</span></div>
          <div class="track"><div class="fill" style="width:38%"></div></div>
          <p class="ds">キッズフェスティバル、巡回指導、なでしこひろばの運営費</p>
        </div>
        <div class="bar">
          <div class="top"><span class="lb">代表チームの強化・遠征</span><span class="pc en">◯◯%</span></div>
          <div class="track"><div class="fill c2" style="width:27%"></div></div>
          <p class="ds">全国大会・北信越大会への遠征費、トレセン活動費</p>
        </div>
        <div class="bar">
          <div class="top"><span class="lb">大会運営・施設整備</span><span class="pc en">◯◯%</span></div>
          <div class="track"><div class="fill c3" style="width:22%"></div></div>
          <p class="ds">会場使用料、審判員派遣、備品の更新</p>
        </div>
        <div class="bar">
          <div class="top"><span class="lb">指導者・審判の育成</span><span class="pc en">◯◯%</span></div>
          <div class="track"><div class="fill c4" style="width:13%"></div></div>
          <p class="ds">ライセンス講習会、研修会の開催費</p>
        </div>
      </div>
      <div class="ph"><span>写真｜活動レポートの表紙<br>または前年度の実績ダイジェスト</span></div>
    </div>
  </div>
</section>

<!-- ===== 協賛メニュー ===== -->
<section class="sec pale">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">04 / PLAN</span>
      <h2>協賛のかたち<span class="todo">区分・金額・特典すべて仮</span></h2>
      <p>ご予算とご希望に応じて、3 つの区分をご用意しています。記載以外のかたちについても、ご相談ください。</p>
    </div>
    <div class="plans">
      <div class="plan">
        <div class="tp">
          <p class="nm">サポート</p>
          <p class="en-nm">SUPPORT</p>
          <p class="amt en">◯◯<small>万円／年〜</small></p>
        </div>
        <div class="bd">
          <ul>
            <li>公式サイトへの社名掲載</li>
            <li>年次活動レポートへの掲載</li>
            <li>主催大会でのアナウンス</li>
            <li class="no">大会会場でのバナー掲出</li>
            <li class="no">冠大会としての名称使用</li>
          </ul>
        </div>
        <div class="ft"><a href="<?= getPageUrl('contact');?>">このかたちで相談する</a></div>
      </div>

      <div class="plan rec">
        <div class="tp">
          <span class="rib">推奨</span>
          <p class="nm">パートナー</p>
          <p class="en-nm">PARTNER</p>
          <p class="amt en">◯◯<small>万円／年〜</small></p>
        </div>
        <div class="bd">
          <ul>
            <li>公式サイトへのロゴ掲載</li>
            <li>年次活動レポートへの掲載</li>
            <li>主催大会でのアナウンス</li>
            <li>大会会場でのバナー掲出</li>
            <li class="no">冠大会としての名称使用</li>
          </ul>
        </div>
        <div class="ft"><a href="<?= getPageUrl('contact');?>">このかたちで相談する</a></div>
      </div>

      <div class="plan">
        <div class="tp">
          <p class="nm">オフィシャル</p>
          <p class="en-nm">OFFICIAL</p>
          <p class="amt en">◯◯◯<small>万円／年〜</small></p>
        </div>
        <div class="bd">
          <ul>
            <li>公式サイトへのロゴ掲載（上位）</li>
            <li>年次活動レポートへの掲載</li>
            <li>主催大会でのアナウンス</li>
            <li>大会会場でのバナー掲出</li>
            <li>冠大会としての名称使用</li>
          </ul>
        </div>
        <div class="ft"><a href="<?= getPageUrl('contact');?>">このかたちで相談する</a></div>
      </div>
    </div>
  </div>
</section>


<!-- ===== 協賛企業 ===== -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">05 / PARTNERS</span>
      <h2>ご協賛いただいている皆さま</h2>
      <p>長野県のサッカーを、ともに支えていただいています。</p>
    </div>
    <div class="logos">
      <div class="ph"><span>ロゴ</span></div><div class="ph"><span>ロゴ</span></div>
      <div class="ph"><span>ロゴ</span></div><div class="ph"><span>ロゴ</span></div>
      <div class="ph"><span>ロゴ</span></div><div class="ph"><span>ロゴ</span></div>
      <div class="ph"><span>ロゴ</span></div><div class="ph"><span>ロゴ</span></div>
      <div class="ph"><span>ロゴ</span></div><div class="ph"><span>ロゴ</span></div>
    </div>
  </div>
</section>

<!-- ===== 流れ ===== -->
<section class="sec pale">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">06 / FLOW</span>
      <h2>お申し込みまでの流れ</h2>
    </div>
    <div class="flow">
      <div class="step">
        <p class="n en">STEP 01</p>
        <h3>ご相談</h3>
        <p>フォームまたはお電話でご連絡ください。ご予算やご希望をお聞かせいただきます。</p>
      </div>
      <div class="step">
        <p class="n en">STEP 02</p>
        <h3>ご提案</h3>
        <p>ご希望に沿った協賛のかたちをご提案します。区分にとらわれない設計も可能です。</p>
      </div>
      <div class="step">
        <p class="n en">STEP 03</p>
        <h3>ご契約</h3>
        <p>内容確定後、協賛契約を締結します。ロゴデータ等をご提供ください。</p>
      </div>
      <div class="step">
        <p class="n en">STEP 04</p>
        <h3>掲出・ご報告</h3>
        <p>速やかに掲出を開始します。年度末には活動レポートをお届けします。</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== FAQ ===== -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">07 / FAQ</span>
      <h2>よくいただくご質問</h2>
    </div>
    <div class="faq">
      <details open>
        <summary>年度の途中からでも協賛できますか。</summary>
        <div class="a">可能です。年度途中からのお申し込みの場合、掲出期間に応じた金額でご相談させていただきます。まずはお問い合わせください。</div>
      </details>
      <details>
        <summary>特定の大会や活動を指定して支援することはできますか。</summary>
        <div class="a">できます。特定の大会への冠協賛、遠征費用への支援など、使途を限定したかたちにも対応しています。ご希望をお聞かせください。</div>
      </details>
      <details>
        <summary>金銭ではなく、物品や役務の提供でも協賛になりますか。<span class="todo">要判断</span></summary>
        <div class="a">用具・飲料の提供、輸送や会場の手配など、金銭以外のかたちについてもご相談を承っています。内容に応じて掲出のかたちをご提案します。</div>
      </details>
      <details>
        <summary>協賛金はどのように使われますか。</summary>
        <div class="a">普及活動、代表チームの強化・遠征、大会運営、指導者・審判の育成に充てられます。年度ごとの構成比は本ページの「使いみち」でご確認いただけます。</div>
      </details>
      <details>
        <summary>個人でも支援できますか。<span class="todo">要判断</span></summary>
        <div class="a">個人の皆さまからのご支援についても、受け付けのかたちを検討しています。詳細が決まりしだい、このページでお知らせいたします。</div>
      </details>
    </div>
  </div>
</section>

<!-- ===== CTA ===== -->
<section class="cta">
  <div class="wrap">
    <h2>まずは、お話を聞かせてください。</h2>
    <p>ご予算やご希望のかたちが決まっていない段階でも構いません。長野県のサッカーに何ができるか、ご一緒に考えさせてください。</p>
    <div class="acts">
      <a href="<?= getPageUrl('contact');?>" class="btnMain">協賛について相談する</a>
      <a href="#" class="btnGhost">活動レポートを見る</a>
    </div>
    <p class="tel">一般社団法人 長野県サッカー協会 事務局<br>TEL ◯◯◯-◯◯◯-◯◯◯◯（平日 9:00–17:00）</p>
  </div>
</section>

<script>
document.getElementById('tg').onclick=()=>document.body.classList.toggle('noNotes');
</script>
<?php endwhile;
get_footer();?>
