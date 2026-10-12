<?php
/**
Template Name: GROW

 * <meta content="charset=UTF-8">
 * @package nfa
 */

$url=get_bloginfo('url');
$assets_url=get_assets_url();
$mypageinfo=get_mypageinfo();

get_header();
while ( have_posts() ) : the_post();?>
<section class="hero">
  <div class="visual"><div class="ph onDark"><span>写真｜育成年代のトレーニング、または指導者・審判の姿</span></div></div>
  <div class="wrap">
    <div class="copy">
      <p class="crumb"><a href="<?= getPageUrl('home');?>">ホーム</a> ／ 強くなる・関わる</p>
      <p class="kicker en">DEVELOPMENT</p>
      <h1>うまくなりたい人と、<br>それを支える人のために。</h1>
      <p class="lead">選手が育つには、指導者と審判が必要です。長野県サッカー協会は、プレーする人だけでなく、関わるすべての人が成長できる仕組みをつくっています。</p>
    </div>
  </div>
</section>

<!-- 分岐 -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">01 / WHO</span>
      <h2>どの立場で、関わりますか。</h2>
    </div>
    <div class="who">
      <a href="#" class="whoCard">
        <p class="ic en">PLAYER</p>
        <h3>選手として、<br>うまくなりたい</h3>
        <p>県トレセン、各年代のリーグ戦、代表チーム。上を目指すための道すじがあります。</p>
        <span class="go">育成について</span>
      </a>
      <a href="#" class="whoCard">
        <p class="ic en">COACH</p>
        <h3>指導者として、<br>関わりたい</h3>
        <p>ライセンス取得から更新まで。指導をはじめる方も、続けている方も。</p>
        <span class="go">指導者について</span>
      </a>
      <a href="#" class="whoCard">
        <p class="ic en">REFEREE</p>
        <h3>審判として、<br>関わりたい</h3>
        <p>4 級からはじめられます。試合を成立させる、もうひとつの関わりかた。</p>
        <span class="go">審判について</span>
      </a>
      <a href="#" class="whoCard">
        <p class="ic en">SAFETY</p>
        <h3>安全な環境を、<br>つくりたい</h3>
        <p>ウェルフェアオフィサー、熱中症・脳振盪への対応。守るための知識。</p>
        <span class="go">医学・安全について</span>
      </a>
    </div>
  </div>
</section>

<!-- 長野スタイル -->
<section class="sec pale">
  <div class="wrap">
    <div class="styleIn">
      <div>
        <div class="secHead" style="margin-bottom:0">
          <span class="no en">02 / NAGANO STYLE</span>
          <h2>長野スタイル</h2>
          <p>年代ごとにばらばらに指導するのではなく、小学生から大人まで一貫した考え方で育てる。それが長野県の育成の基本です。</p>
        </div>
        <div class="pillars">
          <div class="pil">
            <h4>一貫した指導</h4>
            <p>4 種から 1 種まで、年代が変わっても指導の考え方が変わらないようにする。指導者間で共通の言葉を持ちます。</p>
          </div>
          <div class="pil">
            <h4>個を育てる</h4>
            <p>チームの勝敗より、一人ひとりが何を身につけたかを重視します。育成年代では特にこの原則を優先します。</p>
          </div>
          <div class="pil">
            <h4>環境を整える</h4>
            <p>選手・指導者・審判の三者がそろってはじめて成立します。どれか一つが欠けないよう、同時に育てます。</p>
          </div>
        </div>
      </div>
      <div class="ph"><span>写真または図｜長野スタイルの考え方<br>（年代をまたぐ一貫指導のイメージ）</span></div>
    </div>
  </div>
</section>

<!-- 育成の階段 -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">03 / PATHWAY</span>
      <h2>選手が上を目指す、道すじ</h2>
      <p>所属チームでの活動を土台に、段階的に選抜されていきます。</p>
    </div>
    <div class="ladder">
      <div class="rung">
        <div class="hd"><p class="lv en">STAGE 01</p><h3>所属チーム</h3></div>
        <div class="bd">
          <p>すべての土台。日常のトレーニングと公式戦が、評価の基礎になります。</p>
          <p class="req"><b>対象</b>登録しているすべての選手</p>
        </div>
      </div>
      <div class="rung">
        <div class="hd"><p class="lv en">STAGE 02</p><h3>地区トレセン</h3></div>
        <div class="bd">
          <p>北信・東信・中信・南信の 4 地区ごとに選抜。地区内での合同トレーニングを行います。</p>
          <p class="req"><b>選考</b>所属チームの推薦・選考会</p>
        </div>
      </div>
      <div class="rung">
        <div class="hd"><p class="lv en">STAGE 03</p><h3>県トレセン</h3></div>
        <div class="bd">
          <p>地区トレセンから選抜された選手が集まります。県代表の母体となります。</p>
          <p class="req"><b>選考</b>地区トレセンからの推薦</p>
        </div>
      </div>
      <div class="rung">
        <div class="hd"><p class="lv en">STAGE 04</p><h3>北信越・全国</h3></div>
        <div class="bd">
          <p>北信越トレセン、ナショナルトレセンへ。全国の舞台につながります。</p>
          <p class="req"><b>選考</b>県トレセンからの推薦</p>
        </div>
      </div>
    </div>
    <p class="ladderNote">選考の時期・方法は年代ごとに異なります。詳細は<a href="#">各年代のトレセン情報</a>をご確認ください。所属チームの指導者を通じてご案内しています。</p>
  </div>
</section>

<!-- 領域一覧 -->
<section class="sec pale">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">04 / AREAS</span>
      <h2>それぞれの、関わりかた</h2>
    </div>
    <div class="acts">

      <a href="#" class="actRow">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="cat">指導者</p>
          <h3>指導者ライセンス</h3>
          <p>D 級からはじまり、C 級・B 級・A 級と段階があります。D 級は指導経験がなくても受講でき、県内で年に複数回開催しています。取得後は 4 年ごとのリフレッシュ研修が必要です。</p>
        </div>
        <div class="side">
          <p class="mt"><b>入口</b>D 級コーチ養成講習会</p>
          <p class="mt"><b>更新</b>4 年ごとの研修</p>
          <span class="go">くわしく見る →</span>
        </div>
      </a>

      <a href="#" class="actRow">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="cat">審判</p>
          <h3>審判資格</h3>
          <p>4 級から取得できます。講習を受ければ、その日のうちに資格が得られます。3 級・2 級・1 級と上位資格があり、上位ほど担当できる試合の規模が大きくなります。毎年の登録更新が必要です。</p>
        </div>
        <div class="side">
          <p class="mt"><b>入口</b>4 級審判員 新規講習会</p>
          <p class="mt"><b>更新</b>毎年度の登録更新</p>
          <span class="go">くわしく見る →</span>
        </div>
      </a>

      <a href="#" class="actRow">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="cat">育成</p>
          <h3>各年代のリーグ・大会</h3>
          <p>4 種（小学生）から 1 種（社会人）まで、年代ごとにリーグ戦と大会が組まれています。年間を通じた公式戦の経験が、選手の成長の土台になります。</p>
        </div>
        <div class="side">
          <p class="mt"><b>対象</b>登録チーム</p>
          <p class="mt"><b>時期</b>年間を通じて</p>
          <span class="go">くわしく見る →</span>
        </div>
      </a>

    </div>
  </div>
</section>

<!-- 安全 -->
<section class="sec dark">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">05 / SAFETY</span>
      <h2>安全でなければ、続けられません。</h2>
      <p>強くなることと同じくらい、安全に関わることを重視しています。</p>
    </div>
    <div class="safe">
      <div>
        <h4>ウェルフェアオフィサー</h4>
        <p>暴力・暴言・ハラスメントのない環境を守る担当者です。各種大会・チームへの配置を進めています。相談窓口としても機能します。</p>
      </div>
      <div>
        <h4>熱中症への対応</h4>
        <p>気温・湿度にもとづく開催判断の基準を定めています。飲水タイムの運用、日程変更の判断も基準にしたがって行います。</p>
      </div>
      <div>
        <h4>脳振盪への対応</h4>
        <p>疑いがある場合は、その時点でプレーを中止します。復帰までの手順を定めており、指導者・審判に周知しています。</p>
      </div>
    </div>
    <p class="safeFoot">各種ガイドラインと、緊急時の対応手順は<a href="#">医学・安全のページ</a>にまとめています。指導者・保護者の方はご確認ください。</p>
  </div>
</section>

<!-- FAQ -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">06 / FAQ</span>
      <h2>よくいただくご質問</h2>
    </div>
    <div class="faq">
      <details open>
        <summary>指導経験がありませんが、ライセンスは取得できますか。</summary>
        <div class="a">D 級コーチ養成講習会は、指導経験がない方でも受講できます。お子さんのチームを手伝うことになった保護者の方の受講も多くあります。</div>
      </details>
      <details>
        <summary>審判の資格を取ると、必ず試合を担当しなければなりませんか。</summary>
        <div class="a">義務ではありません。所属チームの試合でのみ担当される方も多くいらっしゃいます。まず 4 級を取得し、できる範囲で関わっていただくかたちで問題ありません。</div>
      </details>
      <details>
        <summary>トレセンに入るには、どうすればよいですか。</summary>
        <div class="a">まず所属チームでの活動が前提となります。地区トレセンの選考は、所属チームの推薦または選考会を通じて行われます。時期や方法は年代ごとに異なりますので、所属チームの指導者にご確認ください。</div>
      </details>
      <details>
        <summary>ライセンスや審判資格の更新を忘れてしまいました。</summary>
        <div class="a">更新期限を過ぎた場合の手続きは、資格の種類と経過期間によって異なります。協会事務局までご連絡ください。再取得が必要になる場合もあります。</div>
      </details>
    </div>
  </div>
</section>

<!-- 関連 -->
<section class="sec pale">
  <div class="wrap">
    <div class="secHead" style="margin-bottom:40px">
      <span class="no en">07 / NEWS</span>
      <h2>この分野の最新情報</h2>
    </div>
    <div class="rel">
      <a href="detail.html"><span class="d en">2026.08.19</span><span class="c">講習会</span><span class="t">JFA公認 D級コーチ養成講習会（北信会場）受講者募集</span></a>
      <a href="detail.html"><span class="d en">2026.08.08</span><span class="c">講習会</span><span class="t">4級審判員 新規取得講習会（南信会場）の開催について</span></a>
      <a href="detail.html"><span class="d en">2026.08.05</span><span class="c">登録・手続き</span><span class="t">審判員登録の更新手続きについて（2026年度）</span></a>
      <a href="detail.html"><span class="d en">2026.07.10</span><span class="c">お知らせ</span><span class="t">U-12 トレセン選考会 実施要項および参加方法</span></a>
    </div>
    <div class="relMore"><a href="list.html">この分野の情報をすべて見る</a></div>
  </div>
</section>

<section class="cta">
  <div class="wrap">
    <h2>関わりかたは、ひとつではありません。</h2>
    <p>選手として、指導者として、審判として。どのかたちでも、長野県のサッカーを支えることになります。</p>
    <div class="acts2">
      <a href="<?= getPageUrl('search');?>" class="btnMain">講習会・研修を探す</a>
      <a href="<?= getPageUrl('contact');?>" class="btnLine">事務局に問い合わせる</a>
    </div>
  </div>
</section>

<script>document.getElementById('tg').onclick=()=>document.body.classList.toggle('noNotes');</script>

<?php endwhile;
get_footer();?>
