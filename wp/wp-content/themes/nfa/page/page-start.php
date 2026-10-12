<?php
/**
Template Name: START

 * <meta content="charset=UTF-8">
 * @package nfa
 */

$url=get_bloginfo('url');
$assets_url=get_assets_url();
$mypageinfo=get_mypageinfo();

get_header();
while ( have_posts() ) : the_post();?>
<section class="hero">
  <div class="visual"><div class="ph onDark"><span>写真｜はじめての体験の場面<br>（キッズフェスティバル・なでしこひろば等）</span></div></div>
  <div class="wrap">
    <div class="copy">
      <p class="crumb"><a href="index.html">ホーム</a> ／ はじめる・つづける</p>
      <p class="kicker en">GET STARTED</p>
      <h1>はじめるのに、<br>遅すぎることはありません。</h1>
      <p class="lead">サッカーをやったことがなくても、しばらく離れていても、体力に自信がなくても大丈夫です。長野県には、それぞれの世代・立場に合わせた場所があります。</p>
    </div>
  </div>
</section>

<!-- 分岐 -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">01 / WHO</span>
      <h2>どなたが、はじめますか。</h2>
      <p>ご自身に近いものからお進みください。</p>
    </div>
    <div class="who">
      <a href="#" class="whoCard">
        <p class="ic en">FOR KIDS</p>
        <h3>子どもに、<br>やらせてみたい</h3>
        <p>未就学児から小学生まで。まずは体験から。用具がなくても参加できるプログラムがあります。</p>
        <span class="go">子どもの、はじめかた</span>
      </a>
      <a href="#" class="whoCard">
        <p class="ic en">FOR WOMEN</p>
        <h3>女子サッカーを、<br>やってみたい</h3>
        <p>小学生から大人まで。経験がなくても参加できる「なでしこひろば」を県内各地で開催しています。</p>
        <span class="go">女子サッカーについて</span>
      </a>
      <a href="#" class="whoCard">
        <p class="ic en">FOR ADULTS</p>
        <h3>また、<br>プレーしたい</h3>
        <p>社会人・シニア世代の方へ。長く続けられるリーグと、無理のない環境があります。</p>
        <span class="go">大人・シニアの活動</span>
      </a>
    </div>
  </div>
</section>

<!-- はじめかた -->
<section class="sec pale">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">02 / HOW</span>
      <h2>はじめかたは、3 つの段階です。</h2>
      <p>いきなりチームに入る必要はありません。まず体験してから決められます。</p>
    </div>
    <div class="steps">
      <div class="stp">
        <p class="n en">STEP 01</p>
        <h3>体験してみる</h3>
        <p>県内各地で開催しているフェスティバル・体験教室に参加します。申込不要のものもあります。用具の貸し出しがあります。</p>
        <a href="list.html" class="lk">開催予定を見る</a>
      </div>
      <div class="stp">
        <p class="n en">STEP 02</p>
        <h3>チームを探す</h3>
        <p>お住まいの地域と年代から、活動しているチームを探せます。見学や体験入部を受け付けているチームが多くあります。</p>
        <a href="teams.html" class="lk">チームを探す</a>
      </div>
      <div class="stp">
        <p class="n en">STEP 03</p>
        <h3>登録する</h3>
        <p>チームが決まったら、選手登録の手続きをします。手続きはチームを通じて行うため、個人での作業はほとんどありません。</p>
        <a href="#" class="lk">登録について</a>
      </div>
    </div>
  </div>
</section>

<!-- 活動一覧 -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">03 / PROGRAMS</span>
      <h2>県内で行われている活動</h2>
      <p>年代や立場に応じて、さまざまな取り組みがあります。</p>
    </div>
    <div class="acts">

      <a href="#" class="actRow">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="cat">キッズ・グラスルーツ</p>
          <h3>キッズサッカーフェスティバル</h3>
          <p>未就学児・小学校低学年を対象とした体験イベント。ボールに触れることを目的としており、勝敗はつけません。保護者の方も一緒に参加できます。</p>
        </div>
        <div class="side">
          <p class="mt"><b>対象</b>未就学児〜小学3年生</p>
          <p class="mt"><b>開催</b>年 4 回・県内 4 地区</p>
          <span class="go">くわしく見る →</span>
        </div>
      </a>

      <a href="#" class="actRow">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="cat">女子サッカー</p>
          <h3>なでしこひろば</h3>
          <p>女子を対象とした体験プログラム。小学生から大人まで、年齢の上限はありません。参加者の約 6 割がサッカー未経験の方です。用具の貸し出しがあります。</p>
        </div>
        <div class="side">
          <p class="mt"><b>対象</b>女子・年齢不問</p>
          <p class="mt"><b>開催</b>春・秋／県内各地</p>
          <span class="go">くわしく見る →</span>
        </div>
      </a>

      <a href="#" class="actRow">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="cat">シニア</p>
          <h3>シニアリーグ（O-40 / O-50）</h3>
          <p>40 歳以上・50 歳以上を対象としたリーグ戦。無理のない試合時間と運用で、長く続けられることを目的としています。チーム単位での参加になります。</p>
        </div>
        <div class="side">
          <p class="mt"><b>対象</b>40 歳以上</p>
          <p class="mt"><b>開催</b>年間リーグ戦</p>
          <span class="go">くわしく見る →</span>
        </div>
      </a>

      <a href="#" class="actRow">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="cat">障がい者サッカー</p>
          <h3>障がい者サッカーの取り組み</h3>
          <p>知的障がい・視覚障がい・聴覚障がい・電動車椅子など、さまざまなかたちのサッカーがあります。県内での活動状況と、参加できる交流会をご案内しています。</p>
        </div>
        <div class="side">
          <p class="mt"><b>対象</b>障がいのある方</p>
          <p class="mt"><b>開催</b>交流会・体験会</p>
          <span class="go">くわしく見る →</span>
        </div>
      </a>

      <a href="#" class="actRow">
        <div class="ph"><span>写真</span></div>
        <div class="bd">
          <p class="cat">フットサル</p>
          <h3>フットサルの活動</h3>
          <p>少人数・屋内でできるため、天候に左右されず年間を通じて活動できます。サッカー経験者だけでなく、これからはじめる方の入口としても適しています。</p>
        </div>
        <div class="side">
          <p class="mt"><b>対象</b>年代・性別問わず</p>
          <p class="mt"><b>開催</b>大会・リーグ</p>
          <span class="go">くわしく見る →</span>
        </div>
      </a>

    </div>
  </div>
</section>

<!-- チームを探す -->
<section class="sec pale">
  <div class="wrap">
    <div class="findIn">
      <div class="findBox">
        <h3>近くのチームを探す</h3>
        <p>お住まいの地域と、対象の年代を選んでください。活動しているチームの一覧が表示されます。</p>
        <div class="findRow">
          <select><option>地域を選ぶ</option><option>北信</option><option>東信</option><option>中信</option><option>南信</option></select>
          <select><option>年代を選ぶ</option><option>小学生（4種）</option><option>中学生（3種）</option><option>高校生（2種）</option><option>大人・社会人（1種）</option><option>女子</option><option>シニア</option></select>
        </div>
        <a href="teams.html" class="btn">チームを探す</a>
      </div>
      <div class="ph"><span>地図または写真｜県内 4 地区のイメージ</span></div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="sec">
  <div class="wrap">
    <div class="secHead">
      <span class="no en">04 / FAQ</span>
      <h2>はじめる前に、よくいただくご質問</h2>
    </div>
    <div class="faq">
      <details open>
        <summary>サッカーの経験がまったくありませんが、大丈夫でしょうか。</summary>
        <div class="a">問題ありません。体験プログラムの参加者の多くは未経験の方です。ボールに触れることからはじめられる内容になっています。</div>
      </details>
      <details>
        <summary>用具を持っていません。何を用意すればよいですか。</summary>
        <div class="a">体験の段階では、動きやすい服装と運動靴があれば参加できます。ボールやビブスは会場でお貸しします。チームに入る段階で、あらためてご相談ください。</div>
      </details>
      <details>
        <summary>費用はどのくらいかかりますか。</summary>
        <div class="a">体験プログラムの多くは無料または少額の参加費です。チームに所属する場合の会費はチームごとに異なりますので、見学の際にご確認ください。選手登録料は別途必要です。</div>
      </details>
      <details>
        <summary>保護者が付き添う必要はありますか。</summary>
        <div class="a">未就学児・小学校低学年の体験プログラムでは、保護者の方の同伴をお願いしています。一緒に参加していただける内容です。</div>
      </details>
      <details>
        <summary>途中でやめたくなったら、どうすればよいですか。</summary>
        <div class="a">体験プログラムはその都度のご参加ですので、続ける義務はありません。チームに所属したあとの退会についても、チームにお申し出いただければ手続きできます。</div>
      </details>
    </div>
  </div>
</section>

<!-- 関連情報 -->
<section class="sec pale">
  <div class="wrap">
    <div class="secHead" style="margin-bottom:40px">
      <span class="no en">05 / NEWS</span>
      <h2>この分野の最新情報</h2>
    </div>
    <div class="rel">
      <a href="detail.html"><span class="d en">2026.08.15</span><span class="c">お知らせ</span><span class="t">なでしこひろば 秋季開催のご案内 ─ 参加者を募集しています</span></a>
      <a href="detail.html"><span class="d en">2026.07.22</span><span class="c">お知らせ</span><span class="t">キッズサッカーフェスティバル（中信）開催のご案内</span></a>
      <a href="detail.html"><span class="d en">2026.07.18</span><span class="c">登録・手続き</span><span class="t">フットサル選手権大会 県予選 エントリー受付開始</span></a>
      <a href="detail.html"><span class="d en">2026.07.14</span><span class="c">お知らせ</span><span class="t">障がい者サッカー交流会 参加チーム募集について</span></a>
    </div>
    <div class="relMore"><a href="list.html">この分野の情報をすべて見る</a></div>
  </div>
</section>

<section class="cta">
  <div class="wrap">
    <h2>まず、見学からでも構いません。</h2>
    <p>どこに問い合わせればよいかわからない場合は、協会事務局までご連絡ください。お近くの活動をご案内します。</p>
    <div class="acts2">
      <a href="search.html" class="btnMain">体験・イベントを探す</a>
      <a href="utility.html#form" class="btnGhost">事務局に問い合わせる</a>
    </div>
  </div>
</section>

<script>document.getElementById('tg').onclick=()=>document.body.classList.toggle('noNotes');</script>

<?php endwhile;
get_footer();?>
