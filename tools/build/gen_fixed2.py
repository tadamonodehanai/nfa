# -*- coding: utf-8 -*-
"""固定ページ（後半）：登録／指導者講習会／レフェリーアカデミー／審判講習会 年間計画／長野スタイル／女子トレセン／JFAフェスティバル／サイトマップ
   出典：nagano-fa.or.jp の各固定ページ・画像（図版は文字起こし）。創作しない。"""
from gen_fixed import head, lnav, sec, dl, h3, contact, end, write, E, U
P = {}
def page(fn): P[fn.__name__] = fn; return fn

def jfa_faq(extra=()):
    items = [('JFA FAQ TOP', '//www.jfa.or.jp/info/inquiry/kickoff/'), ('JFA ID', '//www.jfa.or.jp/info/inquiry/kickoff/jfaid/'), ('KICKOFF全般FAQ', '//www.jfa.or.jp/info/inquiry/kickoff/common/')] + list(extra) + [('各種マニュアル', '//www.jfa.or.jp/info/inquiry/kickoff/user-guide/')]
    return dl(items)

KICKOFF = 'https://jfaid.jfa.jp/uniid-server/login'

def fees(title, rows):
    return f'<div class="fx-fee"><p class="fx-k">{title}</p><dl>' + ''.join(f'<div><dt>{a}</dt><dd>{b}</dd></div>' for a, b in rows) + '</dl></div>'

# ======================================================================
@page
def registration():
    s = head('registration', '登録のご案内', 'サッカー・フットサルのチーム・選手登録、指導者登録、審判登録と、JFA ID・KICKOFFについてのご案内です。',
             [], 'REGISTRATION', '登録のご案内', 'JFA主催大会や北信越地域・長野県の各種大会へ出場するためには、JFAへのチーム登録・選手登録が必要です。指導者・審判の登録もKICKOFFで行います。', 'ENTRY')
    s += lnav([('team', 'TEAM / PLAYER', 'チーム・選手'), ('coach', 'COACH', '指導者'), ('referee', 'REFEREE', '審判'), ('jfaid', 'JFA ID', 'JFA ID・KICKOFF')])
    # --- チーム・選手
    a = '''  <div class="fx-split">
    <div class="prose">
      <p>JFA主催大会や北信越地域／長野県の各種大会へ出場するためには、JFAへのチーム登録および選手登録が必要になります。</p>
      <p>JFA IDをお持ちでない方は、新規で取得してから、任意のログインIDとログインパスワードを入力し、KICKOFFシステムにログインしてください。すでにJFA IDをお持ちの方は、新たに取得しないようご注意ください。お持ちのJFA IDからログインし、登録作業を行うことができます。</p>
    </div>
    <div class="fx-box">
      <p class="fx-box-t">登録担当者は複数名の登録を</p>
      <p>チーム登録責任者が異動等で不在となり、チーム関連の申請ができなくなる、JFAから登録責任者へのメールが届かない等のケースが数多く報告されています。代理登録者は任意項目ですが、不測の事態に備え、登録担当者を常に複数名登録してください。</p>
      <p>1チームにつき最大3名がKICKOFFシステムで各種手続きを行えます。KICKOFFを主に操作する方を「登録責任者」とし、代理を2名まで登録してください。JFA IDをお持ちでない方は、JFA IDを取得してから代理登録をしてください。</p>
    </div>
  </div>
  <div class="fx-info">
    <div><p class="fx-k">選手証・監督証</p><p>2018年度から選手証・監督証は完全電子化されています。サッカー／フットサルのいずれも、選手証・監督証は発送されません。</p></div>
    <div><p class="fx-k">クラブ申請（書式第12号）</p><p>2020年度から、クラブ申請は書式（紙）ではなく、Googleフォームによる Web からの申請に変更になりました。</p><a class="fx-a" href="https://forms.gle/pKDCAZqyVxXK5fj17" target="_blank" rel="noopener">クラブ申請フォーム</a></div>
    <div><p class="fx-k">書類添付</p><p>選手を追加・情報変更する際の必要書類は、電子ファイルで添付できます（xls・xlsx・doc・docx・pdf・txt・csv・jpg・jpeg・png・gif・tif／tiff、2MBまで）。</p><a class="fx-a" href="https://www.jfa.jp/registration/player_team/registration.html" target="_blank" rel="noopener">書類添付が必要な選手について（JFA）</a></div>
  </div>
  <div class="fx-btns"><a class="btn btn-g" href="''' + KICKOFF + '''" target="_blank" rel="noopener">KICKOFFでWeb登録<span class="arr">→</span></a><a class="btn btn-o" href="https://www.jfa.jp/documents/#l2" target="_blank" rel="noopener">各種申請書類（JFA）</a></div>'''
    a += h3('よくある質問（JFA）') + jfa_faq()
    s += sec('team', 'TEAM / PLAYER', 'サッカー・フットサル（チーム・選手）', a)
    # --- 指導者
    b = '''  <div class="prose">
    <p>C・D級の所定のカリキュラムを受講し、認定試験に合格した指導者は、JFAに指導者登録することによって JFA認定ライセンス（資格）指導者となります。登録することにより、指導や最新サッカーに関わる情報がテクニカルニュースによって伝達され、情報の共有が図られます。また、リフレッシュ研修を受講できるようになり、指導のレベルアップが図られます。</p>
    <p>（公財）日本サッカー協会は、平成16年4月より全ての公認指導者資格（ライセンス）保有者を対象にした登録制度を開始しました（登録義務化）。本制度を通じて、日本サッカー協会と公認指導者双方のコミュニケーションを円滑にし、公認指導者に正確かつ迅速に情報を届けることを目指しています。</p>
  </div>
  <div class="fx-g2 fx-g2t">
    <div><p class="fx-k">登録のメリット</p><ul class="fx-ul">
      <li>JFAから付与される指導者資格（指導者ライセンス認定証）</li><li>リフレッシュ研修会の受講</li><li>機関誌『テクニカルニュース』（年6回）</li><li>各種テクニカル刊行物の購入</li><li>JFAコモンズ（Webサイト）による情報提供</li><li>マッチングシステム「コーチ・スクエア」への参画</li><li>日本サッカーミュージアム入場料割引</li></ul></div>
    <div>''' + fees('登録費', [('公認S級コーチ', '10,000円／年'), ('公認A・B・C級コーチ', '5,000円／年'), ('公認D級コーチ・キッズリーダー', '3,000円／年')]) + '''<p class="fx-note">キッズリーダーの登録は任意です。</p></div>
  </div>'''
    b += h3('ご利用ガイド・動画マニュアル') + dl([('利用者共通マニュアル', '//nagano-fa.or.jp/wp-content/uploads/2016/02/kickoff-kyoutu_m2015.pdf', '2.8MB'), ('JFA ID 本人写真登録 操作イメージ【長野県版マニュアル】', '//nagano-fa.or.jp/wp-content/uploads/2016/02/nfa_jfaid_image_manual.pdf'), ('JFA ID 取得手順マニュアル動画', '//www.jfa.jp/registration/kickoff/movie.html'), ('JFA ID 保有資格紐付け手順マニュアル動画', '//www.jfa.jp/registration/kickoff/movie_2.html')])
    b += '<div class="fx-btns"><a class="btn btn-g" href="' + KICKOFF + '" target="_blank" rel="noopener">KICKOFFでWeb登録<span class="arr">→</span></a><a class="btn btn-o" href="coach-course.html">指導者講習会のご案内</a></div>'
    s += sec('coach', 'COACH', '指導者登録', b, pale=True)
    # --- 審判
    c = '''  <div class="prose">
    <p>円滑な試合運営および競技規則の理解を深めるため、審判員および審判インストラクターに講習会を受けていただくとともに、それぞれのライセンス（資格）への登録を進めています。サッカーとフットサルではライセンスを別に設定し、レベルによって担当できる試合を区別しています。</p>
    <p>新規に審判資格を取得するには、「新規講習会」を受講していただきます。サッカーおよびフットサル4級取得講習会、3級昇級講習会は、審判のお知らせで随時ご案内しています。</p>
  </div>
  <div class="fx-fees">''' + fees('サッカー 登録費（長野県登録）', [('1級', '22,000円／年'), ('女子1級', '14,000円／年'), ('2級', '8,000円／年'), ('3級', '一般 6,000円／ユース 1,500円'), ('4級', '一般 4,500円／ユース 1,000円')]) + fees('フットサル 登録費（長野県登録）', [('1級', '13,000円／年'), ('2級', '8,000円／年'), ('3級', '一般 6,000円／ユース 1,500円'), ('4級', '一般 4,500円／ユース 1,000円')]) + fees('インストラクター 登録費（長野県登録）', [('S1級', '12,000円／年'), ('F1級', '11,000円／年'), ('2級', '7,000円／年'), ('3級（一般）', '6,000円／年')]) + '</div>'
    c += h3('ご利用ガイド') + dl([('審判マニュアル', '//nagano-fa.or.jp/wp-content/uploads/2016/02/referee_m2015.pdf', '2.18MB'), ('審判／指導者FAQ（JFA）', '//www.jfa.or.jp/info/inquiry/kickoff/ref-leader/')])
    c += '<div class="fx-btns"><a class="btn btn-g" href="' + KICKOFF + '" target="_blank" rel="noopener">KICKOFFでWeb登録<span class="arr">→</span></a><a class="btn btn-o" href="referee-schedule.html">審判講習会 年間計画</a><a class="btn btn-o" href="academy.html">長野県レフェリーアカデミー</a></div>'
    s += sec('referee', 'REFEREE', '審判登録', c)
    # --- JFA ID
    d = '''  <div class="acc faq">
    <div class="acc-i"><button class="acc-b" type="button" data-acc aria-expanded="false" aria-controls="k1"><span class="t">JFA IDとは</span></button><div class="acc-p" id="k1" hidden><p>名前、Eメールアドレス等を登録することで、JFAが発行するIDを取得できます。それをJFA IDと呼びます。KICKOFFを利用するためにはJFA IDが必要です。</p></div></div>
    <div class="acc-i"><button class="acc-b" type="button" data-acc aria-expanded="false" aria-controls="k2"><span class="t">JFA IDを取ると、何が変わりますか</span></button><div class="acc-p" id="k2" hidden><p>JFA IDで個人情報と全ての登録番号を集約し、好きなログイン名とパスワードでKICKOFFにログインできます（文字制限あり）。各カテゴリーごとに登録番号とパスワードでログインする必要がなくなります。</p></div></div>
    <div class="acc-i"><button class="acc-b" type="button" data-acc aria-expanded="false" aria-controls="k3"><span class="t">JFA IDを取るには、何が必要ですか</span></button><div class="acc-p" id="k3" hidden><p>インターネットに接続できる環境と、メールアドレスがあれば取得できます。メールアドレスは必須です（フリーメール可、携帯電話のメールは非推奨）。</p></div></div>
    <div class="acc-i"><button class="acc-b" type="button" data-acc aria-expanded="false" aria-controls="k4"><span class="t">誰がJFA IDを取るのですか</span></button><div class="acc-p" id="k4" hidden><p>JFA IDは誰でも取得できます。チーム登録責任者、審判、指導者は必ず取得してください。指導者には、ライセンスを持っていないがチームの監督として登録されている方も含まれます。</p></div></div>
    <div class="acc-i"><button class="acc-b" type="button" data-acc aria-expanded="false" aria-controls="k5"><span class="t">フットサルの登録制度は</span></button><div class="acc-p" id="k5" hidden><p>2014年4月より、フットサルの登録制度は「個人登録＋大会毎のチーム登録」から、サッカーと同様の「加盟チーム登録」に変わりました。「フットサル登録規程」は2014年3月31日をもって廃止され、「フットサル選手の登録と移籍等に関する規則」として整備されています。</p></div></div>
  </div>
  <div class="fx-box fx-help"><p class="fx-box-t">KICKOFFのお問い合わせ</p><p>JFA登録ヘルプデスクの営業時間など、詳しくは JFA のWebサイトでご確認ください。</p><a class="fx-a" href="https://www.jfa.jp/registration/" target="_blank" rel="noopener">JFA 登録のページ</a></div>'''
    s += sec('jfaid', 'JFA ID / KICKOFF', 'JFA ID・KICKOFFについて', d, pale=True)
    s += contact()
    return s + end()

# ======================================================================
@page
def coach_course():
    s = head('coach-course', '指導者講習会', 'JFA公認コーチ養成講習会（B／C／Dライセンス）、キッズリーダー養成講習会、GK指導者講習会、リフレッシュ研修会のご案内です。',
             [('指導者', 'technical.html')], 'COACHING COURSES', '指導者講習会', 'サッカーの指導に関心がある方を対象に、レベルに応じた指導者講習会を開催しています。講習会に合格した方は、JFAに登録することでリフレッシュ研修会の受講などが可能になります。', 'COURSES')
    s += lnav([('license', 'LICENSE', 'B／C／D'), ('kids', 'KIDS LEADER', 'キッズリーダー'), ('gk', 'GOALKEEPER', 'GK指導者'), ('refresh', 'REFRESH', 'リフレッシュ研修')])
    def lic(lv, en, purpose, days, outline, qual):
        q = ''.join(f'<li>{x}</li>' for x in qual)
        return f'''<article class="fx-lic"><div class="fx-lic-h"><span class="fx-lv">{lv}</span><div><p class="e">{en}</p><h3>{lv}ライセンス</h3></div></div>
      <dl class="fx-dl"><div><dt>目的</dt><dd>{purpose}</dd></div><div><dt>日数</dt><dd>{days}</dd></div><div><dt>概要</dt><dd>{outline}</dd></div><div><dt>受講資格</dt><dd><ul class="fx-ul">{q}</ul></dd></div></dl></article>'''
    a = '<div class="fx-lics">'
    a += lic('B', 'B LICENSE', 'アマチュアチーム及びアマチュアレベルの選手（子どもから大人）に質の高い指導ができる人材を養成すると同時に、Cライセンスコーチからレベルアップし、指導の全般的な基礎固めを目指す指導者を養成します。', '集合講習 前期6日間・後期5日間・試験3日間<br><small>間の学習として日常での指導の積み上げ、e-ラーニングによる共通科目の受講があります。</small>', 'ゲーム分析、トレーニングプランの作成、トレーニングセッションを参加者同士が連携して実践し、アマチュアチーム・アマチュア選手の育成、チームマネジメントができるよう、前期・後期・間の学習を含めて取り組みます。', ['有効なCライセンスコーチ資格を保有', '受講年の4月1日現在で満20歳以上', 'Bライセンストライアル（C級スキルアップ研修）もしくはそれに準ずる研修等を受講している'])
    a += lic('C', 'C LICENSE', 'アマチュアチーム及びアマチュアレベルの選手（子どもから大人）を指導できる人材を養成すると同時に、これから指導の基礎を築こうとする指導者を養成します。', '8日間<br><small>原則土日祝日開催。コースによっては増える場合があります。</small>', '基礎的なゲーム分析、トレーニングプランの作成、トレーニングセッションを参加者同士がグループで連携して実践し、指導の基礎を学びます。', ['受講年度の4月1日現在で満15歳以上', '県内および地域で活動しているクラブやスポーツ教室で実際に指導にあたっている指導者、およびこれから指導者になろうとする方'])
    a += lic('D', 'D LICENSE', '特に12歳以下の子どもたちへのサッカー指導の考え方に触れ、子どもたちが安心して安全にサッカーができる環境づくりと、サッカーの楽しさを伝えられる指導者を養成します。', '1日<br><small>平日夜間等を利用し2〜3日で行うコースもあります。</small>', '実技としてゲームを経験し、ワークショップとディスカッションで、子どもたちに接する大人としての基本的姿勢を学びます。', ['受講年度の4月1日現在で満15歳以上', '県内および地域で活動しているクラブやスポーツ教室で実際に指導にあたっている指導者、およびこれから指導者になろうとする方'])
    a += '</div><p class="fx-note">C・Dライセンスは、受講資格を満たしていればどなたでも申し込みができます。開催要項は 指導者のお知らせ で案内します。</p>'
    a += '<div class="fx-btns"><a class="btn btn-n" href="technical.html#news">指導者のお知らせ<span class="arr">→</span></a></div>'
    s += sec('license', 'JFA COACHING LICENSE', 'JFA公認コーチ養成講習会（B／C／Dライセンス）', a)
    def course(desc, rows):
        r = ''.join(f'<div><dt>{k}</dt><dd>{v}</dd></div>' for k, v in rows)
        return f'<div class="fx-course"><p class="lead2">{desc}</p><dl class="fx-dl">{r}</dl></div>'
    b = course('キッズ（U-6・U-8・U-10）を対象とした、サッカーを含む身体を動かすことの指導にあたる指導者を養成します。', [('受講資格', '<ul class="fx-ul"><li>当年3月31日現在で満16歳以上</li><li>各自で傷害保険に加入済みであること（当協会では保険会社の紹介はしていません）</li></ul>以上の資格を満たしていれば、どなたでも受講できます。'), ('カリキュラム', '実技 1.5時間／講義 1.5時間')])
    s += sec('kids', 'KIDS LEADER', 'JFA公認キッズリーダー養成講習会', b, pale=True)
    c = course('レベルアップのための自己研鑽が重要であると考えています。単に「資格更新のために必要だから」ではなく、新しい知識や情報を得ていただくために開催しています。', [('対象', '長野県内のGK指導に関わる指導者'), ('カリキュラム', '講義／実技'), ('備考', 'JFA公認ゴールキーパーC級養成講習会は、JFAが開催しています。')])
    s += sec('gk', 'GOALKEEPER', 'GK指導者講習会', c)
    d = course('レベルアップのための自己研鑽が重要であると考えています。単に「資格更新のために必要だから」ではなく、リフレッシュ研修会を通じて、新しい知識や情報を得ていただくために開催しています。S級・A級・B級・C級を保有されている方は、リフレッシュポイントを獲得できます。', [('カリキュラム', '講義／実技')])
    d += '''  <div class="tbl-w"><table class="tbl fx-tbl fx-pt"><caption class="fx-cap">保有資格の更新に必要なポイント</caption><thead><tr><th scope="col">資格</th><th scope="col">獲得期間</th><th scope="col">必要ポイント</th></tr></thead><tbody>
    <tr><th scope="row">S級コーチ</th><td>2年間</td><td>40</td></tr><tr><th scope="row">A級ジェネラル</th><td>4年間</td><td>40</td></tr><tr><th scope="row">A級U-12</th><td>4年間</td><td>40</td></tr><tr><th scope="row">B級コーチ</th><td>4年間</td><td>40</td></tr><tr><th scope="row">C級コーチ</th><td>4年間</td><td>40</td></tr>
  </tbody></table></div>
  <p class="fx-note">リフレッシュポイントの獲得状況は、KICKOFF →「指導者」→ 指導者登録番号とパスワードを入力 →「基本情報」→「リフレッシュポイント確認・申請」で確認できます。</p>'''
    s += sec('refresh', 'REFRESH', 'リフレッシュ研修会', d, pale=True)
    s += '''
<section class="blk"><div class="wrap">
  <div class="fx-rel"><a href="nagano-style.html"><span class="e">NAGANO STYLE</span><b>長野県の育成コンセプト「長野スタイル」</b></a><a href="registration.html#coach"><span class="e">REGISTRATION</span><b>指導者登録のご案内</b></a></div>
</div></section>
'''
    return s + end()

# ======================================================================
@page
def academy():
    s = head('academy', '長野県レフェリーアカデミー', '審判技術の向上と審判員・指導者等の交流を目的に、毎月開催している審判セミナー（長野県レフェリーアカデミー）とU25育成プログラムのご案内です。',
             [('審判', 'referee.html')], 'REFEREE ACADEMY', '長野県レフェリーアカデミー', '審判技術の向上と、審判員・指導者等の交流を目的に、毎月1回、長野県フットボールセンターと北信地区で審判セミナーを開催しています。', 'ACADEMY', small='（審判セミナー）')
    s += lnav([('about', 'ABOUT', '概要'), ('needs', 'NEEDS', '受講ニーズ'), ('guide', 'GUIDELINES', '募集要項'), ('u25', 'U25 PROGRAM', 'U25育成'), ('curriculum', 'CURRICULUM', 'カリキュラム'), ('graduate', 'GRADUATES', '卒業生')])
    a = '''  <div class="fx-split">
    <div class="prose">
      <p>審判セミナーは、（一社）長野県サッカー協会審判委員会 審友会の事業として、1級審判員として活躍された山際氏、大峡氏、松本氏、小出氏を主要講師に迎え、1年間を通して行われます。</p>
      <p>2015年からは U25育成プログラムがスタートしました。長野県内はもちろん、北信越、日本および世界で活躍できる審判員を発掘・育成するシステムの確立や審判員の環境の向上を図るため、25歳以下の若手レフェリーを短期間に集中的に指導し、技術や競技規則の習得、人間性の育成をめざし、将来のトップレフェリー候補者を養成します。</p>
      <p>また、長野県内各地域で計画的に開催する更新講習会により、審判活動の啓発、情報提供、地域からの審判員の発掘を行います。</p>
    </div>
    <div class="fx-box">
      <p class="fx-box-t">対象</p><p>サッカーが好きで審判に興味がある方なら、若い方（原則18歳以上）から熟年の方まで幅広く募集しています。女性の方も歓迎です。</p>
      <p class="fx-box-t">講師</p><p>山際将史、大峡諭、松本瑛右、小出貴彦（1級審判員として活躍）を中心に、1級〜3級インストラクターが担当します。</p>
    </div>
  </div>
  <div class="kpi fx-kpi"><div><b>毎月<small>第3水曜</small></b><span>19:00〜21:00</span></div><div><b>1<small>年間</small></b><span>3月〜翌年2月</span></div><div><b>無料</b><span>受講料（交通費等は自己負担）</span></div></div>'''
    s += sec('about', 'ABOUT', '審判セミナーについて', a)
    # needs
    M = {'o': ('fx-o', '○'), 'x': ('fx-x', '×'), 't': ('fx-t', '△')}
    H = ['研修をしたい', '更新をしたい', '取得・昇級したい']
    def cell(m, t, i=0):
        c, sym = M[m]
        return f'<td data-h="{H[i]}"><span class="fx-mk {c}" aria-label="{sym}">{sym}</span><span>{t}</span></td>'
    rows = [('資格なし', [('o', '事前申し込みが必要'), ('x', '更新する級がありません'), ('o', 'U25育成プログラムで4級が取得可能。所定の審査をパスすれば3級取得も可能')]),
            ('4級審判員', [('o', '事前申し込みが必要'), ('o', 'Kickoffによる事前申込が必要。1回の参加で更新可能'), ('o', '所定の審査をパスすれば3級取得が可能')]),
            ('3級審判員', [('o', '事前申し込みが必要'), ('o', 'Kickoffによる事前申込が必要。1回の参加で更新可能'), ('t', '技能が認められる者は県協会に推薦し、北信越所定の審査にパスすれば2級昇級可能')]),
            ('2級審判員', [('o', '事前申し込みが必要'), ('x', '更新はできません'), ('x', '昇級はできません')])]
    tb = ''.join(f'<tr><th scope="row">{r}</th>' + ''.join(cell(m, t, i) for i, (m, t) in enumerate(cs)) + '</tr>' for r, cs in rows)
    b = '<p class="lead2">審判技術の向上を目的とした研修から、審判資格の更新、4級取得、3級昇級、2級昇級を目指す方まで、ニーズに合わせて受講できます。</p>'
    b += f'<div class="tbl-w"><table class="tbl fx-needs"><thead><tr><th scope="col">保有資格 ＼ ニーズ</th><th scope="col">研修をしたい</th><th scope="col">更新をしたい</th><th scope="col">取得・昇級したい</th></tr></thead><tbody>{tb}</tbody></table></div>'
    b += '<p class="fx-note">○ 受講できる　△ 条件により可能　× 対象外</p>'
    s += sec('needs', 'NEEDS', '受講ニーズ早見表', b, pale=True)
    # guidelines
    G = [('目的', '1）受講生の審判活動に対する関心度を高める。<br>2）U25審判員の発掘・育成を図る。'),
         ('日時', '毎月第3水曜日 19:00〜21:00'),
         ('場所', '長野県フットボールセンター（松本市今井7037-7）及び北信地区'),
         ('研修内容', '①（講義）競技規則、審判法、ディスカッション<br>②（実技）フィジカル／プラクティカルトレーニング<br>③ U25育成プログラム：25歳未満の若手審判員の発掘・育成を図るコース。3級へ昇級できます。上記とは別に実技指導、競技規則確認テスト、体力試験、実技試験が行われます。'),
         ('受講の特典', '1）更新講習会免除（4級・3級有資格者）※1<br>2）4級取得・認定講習会免除（資格を持っていない方）※1<br>3）筆記試験免除（U25育成プログラム受講生）※2<br>4）2級受験への推薦制度あり（U25育成プログラム受講生で優秀成績者）<br><small>※1 Kickoffによる申し込みが必要です。　※2 審判セミナー（3月〜9月）受講率80%以上。</small>'),
         ('募集対象', '原則として4級審判員、審判資格がない方'),
         ('受講レベル', '【4級審判員向け】競技規則の確認、基本的な審判技能の習得'),
         ('応募資格', '1）年間を通じてセミナーに参加できる方<br>2）原則18歳以上の男女<br>3）スポーツ保険に加入できる方（本セミナーでのケガ等については一切の責任を負いかねます）<br>4）U25育成プログラムを希望する方は、25歳以下で将来1級を目指す審判員。派遣審判員として環境を整えられる方が対象です（帯同審判員として上級を目指す方は対象外）。<br><small>※18歳未満の方は、保護者等が会場まで送迎できること。</small>'),
         ('研修年限', '3月より翌年2月までの1年間'),
         ('募集人員', '1）10名程度　2）U25育成プログラムは5名以内　3）S4・S3更新 11月〜2月 各20名程度'),
         ('受講料', '無料（研修会参加に要する現地までの交通費・宿泊費等は自己負担）'),
         ('応募手続き', '自筆の参加申込書（写真貼付）を協会に郵送。更新はすべてKickoffによる事前申し込みが必要です。'),
         ('選考方法', '研修および更新：先着順<br>U25育成プログラム：先着順。申し込み多数の場合は書類選考'),
         ('応募先', '〒390-1131 長野県松本市今井7037-7<br>（一社）長野県サッカー協会 審判委員会<br>TEL 0263-50-8330　FAX 0263-50-8383　<a href="mailto:referee@nagano-fa.or.jp">referee@nagano-fa.or.jp</a>')]
    c = '<div class="tbl-w"><table class="tbl fx-tbl fx-gl"><tbody>' + ''.join(f'<tr><th scope="row">{k}</th><td>{v}</td></tr>' for k, v in G) + '</tbody></table></div>'
    c += h3('関連資料') + dl([('公式案内（募集要項）', '/wp-content/uploads/2017/06/9e09e4ecac1474818d6311c9f683be8a.pdf'), ('参加 早見表', '/wp-content/uploads/2017/06/2015_needs.pdf', '55KB'), ('参加申込用紙', '/wp-content/uploads/2017/06/80244a81944de193c4c17577a90f9ddf.pdf'), ('カリキュラム', '/wp-content/uploads/2017/06/d5b9383338043a6dcd9ed9e2f836f875.pdf')])
    c += '<div class="fx-box fx-help"><p class="fx-box-t">更新講習会（S3級・S4級）</p><p>11月〜翌年2月の毎回、各級定員20名程度（会場：アルウィン第3〜5会議室）。Kickoffから申し込んでください。</p><a class="fx-a" href="' + KICKOFF + '" target="_blank" rel="noopener">Kickoffで申し込む</a></div>'
    s += sec('guide', 'GUIDELINES', '募集要項', c)
    # U25 flow
    d = '''  <p class="lead2">25歳以下の若手審判員を対象に、最短2年で2級取得を目指すプログラムです。資格のない方でも、最短1年で3級審判の資格が取得できるよう指導します。</p>
  <ol class="fx-flow">
    <li class="fx-fl-in"><span class="no">01</span><div><h3>申込</h3><p>本人が参加申込書を提出します。</p></div></li>
    <li class="fx-fl-in"><span class="no">02</span><div><h3>書類審査</h3><p>NFA審判委員会が書類審査を行います。</p></div></li>
    <li class="fx-fl-in"><span class="no">03</span><div><h3>承認</h3><p>審判委員会から本人へ承認の連絡をします。</p></div></li>
  </ol>
  <div class="fx-yr">
    <div class="fx-yr-c"><p class="fx-yr-t"><b>1年次</b></p>
      <div class="fx-yr-s"><span class="no">04</span><div><h3>カリキュラム</h3><ul class="fx-ul"><li>審判セミナー受講</li><li>競技規則 確認小テスト・模試</li><li>実技指導</li><li>4級認定試験（無資格の方）※9月頃実施および4級合格発表</li><li>3級認定試験（10月〜11月実施）</li></ul></div></div>
      <div class="fx-yr-s fx-goal"><span class="no">05</span><div><h3>11月下旬　3級合格発表</h3></div></div>
      <div class="fx-yr-s"><span class="no">06</span><div><h3>推薦</h3><p>審友会（審判セミナー）が2級昇級を審判委員会に推薦します。</p></div></div>
    </div>
    <div class="fx-yr-c"><p class="fx-yr-t"><b>2年次</b></p>
      <div class="fx-yr-s"><span class="no">07</span><div><h3>カリキュラム</h3><ul class="fx-ul"><li>審判セミナー受講</li><li>《北信越カリキュラム》競技規則 確認テスト</li><li>実技指導</li><li>2級認定試験（9月頃実施）：筆記試験・体力試験・実技試験</li></ul></div></div>
      <div class="fx-yr-s fx-goal"><span class="no">08</span><div><h3>11月下旬　2級合格発表</h3></div></div>
    </div>
  </div>'''
    s += sec('u25', 'U25 PROGRAM', 'U25育成プログラム（申込から合格までの流れ）', d, pale=True)
    # curriculum
    cur = [('3月', '試合前の打ち合わせ', 'フラッグテクニック'), ('4月', 'オフサイド「プレーを妨害する」', 'オフサイドテスト①'), ('5月', '副審の任務', 'FKセレモニー'), ('6月', 'リクエスト講義①', 'オフサイド'), ('7月', 'オフサイド「相手競技者を妨害する」', 'オフサイドテスト②'), ('8月', 'ペナルティーキック PA周辺で配慮すること', 'ペナルティーキック'), ('9月', '主審の任務', '主審のポジショニング'), ('10月', '反スポーツ的行為', 'PA周辺のファウル'), ('11月', 'ハンドリング', '—'), ('12月', 'リクエスト講義②', '—'), ('1月', '得点または決定的な得点の機会の阻止', '—'), ('2月', '審判報告書の書き方', '—')]
    e = '<div class="tbl-w"><table class="tbl fx-cur"><caption class="fx-cap">審判セミナー 研修カリキュラム（例：2017年 7期生）</caption><thead><tr><th scope="col">開催</th><th scope="col">講義　19:00〜19:50</th><th scope="col">実技　20:00〜21:00</th></tr></thead><tbody>' + ''.join(f'<tr><th scope="row">{m}</th><td>{a}</td><td>{b}</td></tr>' for m, a, b in cur) + '</tbody></table></div>'
    e += '<p class="fx-note">11月〜2月は実技を行いません（講義 19:00〜20:30）。上記以外の日程で、実際のレフェリングやゲーム観戦を行う予定です。</p>'
    u = [('3月', '1〜4条', ''), ('4月', '', '4級認定試験（無資格者）'), ('5月', '7〜10条', ''), ('6月', '11〜12条', ''), ('7月', '13〜17条', ''), ('8月', '模試', ''), ('9月', '確認テスト', '3級実技試験：10月〜11月下旬に開催。追試を3回まで設定し、その都度指導しながら合格に導きます。11月下旬 合格発表、3級昇級者研修会に参加（11月下旬〜12月初旬）'), ('10月', '筆記試験（免除）※1', ''), ('11月', '体力試験', '')]
    e += '<div class="tbl-w"><table class="tbl fx-cur"><caption class="fx-cap">U25育成プログラム カリキュラム（予定）</caption><thead><tr><th scope="col">開催</th><th scope="col">競技規則確認・小テスト</th><th scope="col">実技指導</th></tr></thead><tbody>' + ''.join(f'<tr><th scope="row">{m}</th><td>{a or "—"}</td><td>{b or "—"}</td></tr>' for m, a, b in u) + '</tbody></table></div>'
    e += '''<div class="fx-box"><p class="fx-box-t">U25 実技指導の内容</p><p>毎週火曜日19時〜21時、松本市サッカー場でU18育成会を活用して行います（5月以降は、4月中に月2回ほど日程を組みご案内します）。最低2回の参加が必要です。本人の技術習得レベルに応じて指導回数は増減します。</p>
  <div class="fx-g2"><div><p class="fx-k">副審</p><p>フラッグテクニック・オフサイドライン・ファウルサポート</p></div><div><p class="fx-k">主審</p><p>シャドーポジション（15分INT）／ポジション・シグナル（笛）（15分INT）／判定・シグナル（笛）（30分INT）／総合（1試合）</p></div></div></div>
  <p class="fx-note">※1 筆記試験免除の条件：3月〜9月までの受講率80%（セミナー5回）以上、かつ9月の確認テストで基準を満たしていること。プログラムは途中で変更になる場合があります。</p>'''
    s += sec('curriculum', 'CURRICULUM', 'カリキュラム', e)
    # graduates
    g1 = [('小出 貴彦', '2015年（1期生）'), ('松本 瑛右', '2016年（1期生）'), ('井口 朋恵', '2017年（5期生）　長野県初の女子1級審判員')]
    g2 = [('木内 岳彦', '2期生'), ('山辺 雅司', '2期生'), ('高木 良輔', '2期生'), ('上原 雄次', '3期生'), ('高坂 凌', '4期生'), ('菅 賢太郎', '5期生')]
    def gl(lv, xs): return f'<div class="fx-gr"><p class="fx-gr-lv"><b>{lv}</b>審判員</p><ul>' + ''.join(f'<li><b>{n}</b><span>{t}</span></li>' for n, t in xs) + '</ul></div>'
    f = '<p class="lead2">審判セミナーの受講生から、1級審判員、2級審判員へ昇級した卒業生を紹介します。</p><div class="fx-grs">' + gl('1級', g1) + gl('2級', g2) + '</div>'
    s += sec('graduate', 'GRADUATES', '活躍する卒業生', f, pale=True)
    s += contact('審判セミナーのお問い合わせ', '（一社）長野県サッカー協会 審判委員会', '0263-50-8330（平日 10:00〜16:00）')
    return s + end()

# ======================================================================
@page
def referee_schedule():
    s = head('referee-schedule', '審判 各種講習会 年間計画', 'サッカー審判の4級取得・4級更新・3級昇級・3級更新講習会とJFAラーニングの年間の開催時期の目安です。',
             [('審判', 'referee.html')], 'REFEREE COURSES', '審判 各種講習会 年間計画', '各種講習会の開催時期の目安です。正式なご案内は、原則として開催日の1か月前までに当サイトで掲載し、JFA Kickoffからもメールで配信します。', 'SCHEDULE', small='（サッカー）')
    MON = ['4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月', '1月', '2月', '3月']
    X = [150, 175, 201, 227, 253, 279, 316, 357, 399, 438, 474, 509]
    def pos(x):
        for i in range(11):
            if X[i] <= x <= X[i + 1]: return (i + (x - X[i]) / (X[i + 1] - X[i]) + .5) / 12 * 100
        return (0 if x < X[0] else 11.5) / 12 * 100 + (100 / 24 if x < X[0] else 0)
    # 出典画像 referee_soccer_year.gif の位置を月軸に写したもの
    R = [('4級取得', 'g', [(188, ''), (401, ''), (454, 'ユース限定'), (510, '')], None),
         ('4級更新', 'g', [(192, 'ユース限定試合観戦'), (282, '審判交流会'), (355, '試合観戦'), (454, ''), (485, '')], None),
         ('JFAラーニング', 'g', [], (316, 480, '10月〜翌年2月下旬')),
         ('3級昇級', 'b', [(355, '体力試験＆講習'), (401, '筆記試験')], (185, 250, '翌年5月〜7月 実技試験')),
         ('3級更新', 'b', [(282, '審判交流会'), (454, ''), (485, '')], None)]
    head_row = '<div class="ys-row ys-hd"><div class="ys-k"></div><div class="ys-t">' + ''.join(f'<span>{m}</span>' for m in MON) + '</div></div>'
    rows = ''
    for k, c, dots, span in R:
        t = ''
        if span:
            a, b, lab = span; t += f'<span class="ys-span" style="left:{pos(a):.1f}%;width:{pos(b) - pos(a):.1f}%"><i>{lab}</i></span>'
        for n, (x, lab) in enumerate(dots):
            up = ' up' if (k == '3級昇級' and lab == '筆記試験') else ''
            t += f'<span class="ys-dot{up}" style="left:{pos(x):.1f}%">{f"<i>{lab}</i>" if lab else ""}</span>'
        rows += f'<div class="ys-row ys-{c}"><div class="ys-k">{k}</div><div class="ys-t">{t}</div></div>'
    def mon(x):
        for i in range(11):
            if X[i] <= x <= X[i + 1]:
                f = (x - X[i]) / (X[i + 1] - X[i])
                return MON[i] if f < .35 else (MON[i + 1] if f > .65 else MON[i][:-1] + '〜' + MON[i + 1])
        return MON[0] if x < X[0] else MON[-1]
    li = ''.join(f'<li><b>{k}</b>：' + '、'.join(mon(x) + (f'（{lab}）' if lab else '') for x, lab in d) + (f'{"、" if d else ""}{sp[2]}' if sp else '') + '</li>' for k, c, d, sp in R)
    a = f'''  <div class="ys-legend"><span class="ys-g">4級</span><span class="ys-b">3級</span><span class="ys-n">● 開催時期の目安</span></div>
  <div class="ys-w"><div class="ys" role="img" aria-label="サッカー審判 各種講習会の年間計画">{head_row}{rows}</div></div>
  <p class="ys-hint">表は横にスクロールできます</p>
  <ul class="ys-sr">{li}</ul>'''
    s += sec('plan', 'ANNUAL PLAN', '年間計画（サッカー）', a)
    b = '''  <ul class="fx-notes">
    <li>開催スケジュールは目安です。会場確保の関係で、予定が大幅に変更される場合があります。</li>
    <li>4級審判員の更新講習会は、JFAラーニングをご利用ください。</li>
    <li>3級取得講習会を受講される方で、1次試験（体力・筆記）に合格された方は、必ず4級更新講習会（講義講習会・JFAラーニング）を受講してください。受講されない場合は、1次試験合格の取り消しおよび4級審判員失効となります。</li>
    <li>4級取得講習会を受講されるユースの方へ：ユース限定の4級取得講習会を1月から2月の期間に開催しています。なるべくユース限定の講習会を受講してください。</li>
  </ul>
  <div class="fx-btns"><a class="btn btn-n" href="referee.html#news">審判のお知らせ<span class="arr">→</span></a><a class="btn btn-o" href="registration.html#referee">審判登録のご案内</a><a class="btn btn-o" href="academy.html">長野県レフェリーアカデミー</a></div>'''
    s += sec('notes', 'NOTES', 'ご注意', b, pale=True)
    s += contact('審判講習会のお問い合わせ', '（一社）長野県サッカー協会 審判委員会', '0263-50-8330（平日 10:00〜16:00）')
    return s + end()

# ======================================================================
@page
def nagano_style():
    s = head('nagano-style', '長野スタイル', '長野県サッカー協会の育成コンセプト「長野スタイル」。U-10からU-16まで各年代で獲得すべきものとプレーモデルをまとめています。',
             [('育成・強化', 'grow.html')], 'NAGANO STYLE', '長野スタイル', '長野県の育成コンセプト『長野スタイル』です。各年代で獲得すべきもの、プレーモデルとしてまとめました。2028年国民スポーツ大会優勝に向け指導者のベクトルを合わせ、長野県サッカーを向上させていきましょう。', 'STYLE', small='（育成コンセプト）', active='grow')
    s = s.replace('</p></div></section>\n', '</p><div class="meta"><span>技術委員会 <b>2026.1.11版</b></span></div></div></section>\n', 1)
    s += lnav([('concept', 'CONCEPT', 'コンセプト'), ('stage', 'AGE GROUP', '年代別'), ('base', 'BASE', '5つの課題'), ('slides', 'DOCUMENT', '資料')])
    a = '''  <div class="ns-key">
    <p class="ns-base"><span class="ib">個人戦術と HARD WORK を</span><span class="ib">ベースに</span></p>
    <div class="ns-two"><p><span>01</span><b><span class="ib">攻守において</span><span class="ib">主導権をにぎる</span></b></p><p><span>02</span><b><span class="ib">個々の選手が</span><span class="ib">ストロングポイントを発揮する</span></b></p></div>
    <p class="ns-sub">TEAM NAGANOとしての Japan's Way 実現に向けた育成年代 U-10 → U-16 へのアプローチ</p>
  </div>
  <div class="cards c2 ns-cards">
    <article class="cd"><div class="bd"><div class="lab">PLAYER'S FIRST</div><h3>主役は「選手」</h3><p>選抜活動、トレセン活動、チーム活動等、様々なシーンでそれぞれの選手の「個の育成」を第一に考えます。長野県から、プロでさらに海外で活躍する選手、日本代表選手を輩出する。そして、生涯を通じてサッカーを楽しめる選手を育てます。</p></div></article>
    <article class="cd"><div class="bd"><div class="lab">ROAD TO 2028</div><h3>2028 頂点を目指す</h3><p>2028年、長野県で国民スポーツ大会を開催し、長野県代表チームは優勝することを目指します。長野県の育成に関わる仲間が目標やコンセプトを共有し、ベクトルを合わせ、『TEAM NAGANO』として取り組みます。</p></div></article>
  </div>'''
    s += sec('concept', 'CONCEPT', '育成コンセプト', a)
    st = [('U-10', '〜U10', 'Enjoy Football', 'ボールフィーリング／コーディネーション', 'サッカーそのものを味わう・全体像をとらえる', '3人制→4人制 スモールサイドゲーム／5・6人制（U10）'),
          ('U-11・12', 'U11・12', '個人戦術に基づいた判断と的確なプレー', '動きながらのテクニック／パーフェクトスキルの獲得', '個人戦術とテクニックから課題を捉える・解決する', '6〜7人制（U11）／8人制（U12）'),
          ('U-13・14', 'U13・14', '個人戦術に基づいた意図的なグループ戦術', '意図的な攻防・コンビネーション／テクニックの追求（Goodフットボーラーとして）', '個人戦術に基づいたグループ戦術で課題を捉える・解決する', '9人制（U13）／11人制'),
          ('U-15・16', 'U15・16', '個の特長を生かしチームとして戦う', '判断を磨く／テクニックの追求（ポジションの専門性）', '個人戦術・グループ戦術をBaseに、ストロングポイントを発揮しチームで戦う', '11人制')]
    b = '<p class="lead2">一貫指導で、選手を発育発達段階に応じたアプローチで育成します。</p><ol class="ns-stage">' + ''.join(f'<li><p class="ns-age">{a}</p><h3>{c}</h3><p class="ns-k">{d}</p><p class="ns-g">{e}</p><p class="ns-n"><small>ゲーム</small>{f}</p></li>' for a, _, c, d, e, f in st) + '</ol>'
    s += sec('stage', 'AGE GROUP', '年代別の育成コンセプト', b, pale=True)
    c = '''  <div class="ns-bs">
    <div><p class="fx-k">基本の徹底</p><p>良い習慣（good habit）にするべき「基本」＝サッカー選手に必要な要素。攻守における個人戦術の理解、サッカーの目的の理解／プレーの原則の理解 → プレーで表現できる。</p><p class="fx-k">指導者の働きかけ視点</p><p>テクニック：パーフェクトスキル（的確な判断に基づく対人の中で発揮できる技術）。High Intensity の中でも High Quality で発揮する。</p></div>
    <ol class="ns-five"><li>個人で失わないテクニックの獲得（コントロール、ターン）</li><li>浮き球の扱い…コントロールしてプレーする＝1タッチで収める</li><li>正確なパス（多彩なキックで）</li><li>動きながらのテクニックの発揮（意図のあるファーストタッチ）</li><li>状況把握…観る・観ておく（ボール、ゴール、味方、相手、スペース）</li></ol>
  </div>'''
    s += sec('base', 'BASE', '「個人戦術」と「HARD WORK」を確かなBaseとするために', c)
    sl = ''.join(f'<li><a href="assets/img/ns-{i:02d}.jpg" target="_blank" rel="noopener"><img src="assets/img/ns-{i:02d}.jpg" alt="長野スタイル 資料 {i}ページ" loading="lazy"><span>{i:02d}</span></a></li>' for i in range(1, 19))
    d = f'<ul class="ns-sl">{sl}</ul>' + dl([('NAGANO FA 育成コンセプト 長野スタイル（全18ページ）', '/wp-content/uploads/2026/01/nagano-style_v2.pdf')])
    s += sec('slides', 'DOCUMENT', '資料（全18ページ）', d, pale=True)
    s += '''
<section class="blk"><div class="wrap">
  <div class="fx-rel"><a href="coach-course.html"><span class="e">COACHING COURSES</span><b>指導者講習会のご案内</b></a><a href="tracen.html"><span class="e">TRAINING CENTER</span><b>女子トレセンについて</b></a></div>
</div></section>
'''
    return s + end()

# ======================================================================
@page
def tracen():
    s = head('tracen', '女子トレセンについて', '長野県女子トレセン（ナショナルトレーニングセンター制度）のしくみ、選考の流れ、よくある質問です。',
             [('女子', 'cat-w.html')], 'TRAINING CENTER', '女子トレセンについて', '日本サッカーの強化・発展のため、将来日本代表選手となる優秀な素材を発掘し、良い環境、良い指導を与えることを目的とした制度です。選手にとっては、夢（目標）に近づくチャレンジ・チャンスの場です。', 'TRACEN', small='（ナショナルトレーニングセンター制度）')
    s += lnav([('about', 'ABOUT', 'トレセンとは'), ('step', 'STEP', '選考の流れ'), ('faq', 'FAQ', 'よくある質問')])
    a = '''  <div class="fx-split tc-split">
    <div class="prose">
      <p>長野県女子トレセンでは、まず選考会を行い、サッカー選手としてのスキル・身体能力の高い選手を選考します。選ばれた選手で月に1回ほどのトレーニングを行います。</p>
      <p>トレーニングの中で特に能力・質の高い選手は「北信越トレセン」の選考会へ推薦します。北信越トレセンの選考会で選ばれた選手は、年3回の北信越トレセンに参加します。そこでさらに選考され、「ナショナルトレセン」へ推薦します。ナショナルトレセンでは、日本のトップレベルのトレーニングやコーチ、時には上の年代の選手と共に、世界で戦うレベルのトレーニングや試合を経験できます。</p>
      <p>男子はサッカー人口が多いため、地区トレセンに選ばれてから長野県トレセンがありますが、女子は現在サッカー人口の関係上、地区トレセンはなく、長野県女子トレセンのみです。</p>
    </div>
    <div class="tc-py" aria-label="長野県女子トレセン制度の位置づけ">
      <p class="fx-k">長野県女子トレセン制度の位置づけ</p>
      <p class="tc-gl">日本代表</p>
      <ol class="tc-lv">
        <li class="jp w1"><b>なでしこジャパン</b><small>FIFAワールドカップ／オリンピック</small></li>
        <li class="jp w2"><b>U-20 代表</b><small>U-19・U-18 代表／FIFA U-20 ワールドカップ</small></li>
        <li class="jp w3"><b>U-17 代表</b><small>U-16・U-15 代表／FIFA U-17 ワールドカップ</small></li>
      </ol>
      <p class="tc-gl">トレセン</p>
      <ol class="tc-lv">
        <li class="tc w4"><b>ナショナルトレセン</b></li>
        <li class="tc w5"><b>9地域トレセン</b><small>北信越トレセン</small></li>
        <li class="tc nf w6"><b>47都道府県トレセン</b><small>長野県トレセン</small></li>
      </ol>
      <p class="tc-ages"><span>U-18</span><span>U-15</span><span>U-12</span></p>
    </div>
  </div>'''
    s += sec('about', 'ABOUT', 'トレセンとは', a)
    b = '''  <ol class="steps tc-steps">
    <li><h3>長野県女子トレセンの選考会に参加</h3><p>毎年3月に選考会を行います。U12（小学生）とU15（中学生）が対象です。選考会に参加し、選考を受けて選ばれます。参加方法、場所、日時は当サイトで案内します。</p></li>
    <li><h3>長野県女子トレセンのトレーニングに参加</h3><p>長野県女子トレセン主催のトレーニングに参加します。その中の選手から、北信越トレセンへの参加推薦が与えられます。</p></li>
    <li><h3>北信越トレセンの選考会に参加</h3><p>北信越トレセンのトレーニング（計3回）に参加します。その中の選手から、ナショナルトレセンへの参加推薦が与えられます。</p></li>
    <li><h3>ナショナルトレセンの選考会に参加</h3><p>ナショナルトレセンのトレーニングに参加します。ナショナルトレセンコーチの指導のもとトレーニングを行い、日本代表候補が選ばれます。</p></li>
  </ol>
  <p class="tc-goal"><b>日本代表</b>日本代表として、世界レベルの女子サッカーを経験します。</p>'''
    s += sec('step', 'STEP', 'トレセンに入るまでの流れ', b, pale=True)
    Q = [('トレセンの選考会に参加したいです。条件はありますか？', 'まず、長野県サッカー協会に登録された選手でなければなりません。詳しくは所属チームの指導者に確認してください。女子サッカーチームに所属していなければならないわけではありません。'),
         ('所属チームがありません。トレセンの選考会に参加できますか？', '所属チームがない場合は、長野県サッカー協会に選手登録されていないと思われますので、参加できません。まずは近くのサッカーチームに所属することをお勧めします。近くの女子サッカーチームをお探しでしたら、お問い合わせください。'),
         ('トレセンに選ばれたら、今所属しているチームを辞めないといけませんか？', 'トレセンはチームではありませんので、現在所属しているチームを辞める必要はありません。ただし、トレセンの活動と所属チームの活動の日にちが重なる場合もあります。その場合は、所属チームとトレセンのスタッフと相談してください。'),
         ('その年度のトレセン選考会に選ばれませんでした。', '基本的に、その年の選考会に選ばれなかった場合、その年にトレセンの選手として活動することはできません。しかし、女子リーグでの活躍や各種女子サッカー大会での功績次第では、トレセン選手に抜擢される可能性があります。'),
         ('サッカーを始めたばかりですが、トレセンに入ることはできますか？', 'トレセンの選考会はサッカー選手としての能力を見て選考するため、初心者が選ばれることは難しいかもしれません。所属チームの指導者へのトレセン選考会の案内に「監督として推薦できる選手」と付け加えているのはそのためです。ただし、まだどう成長するかわからない若い年代の選手だけに、可能性がないわけではありません。選考会で選ばれなかったとしても、女子リーグでの活躍や各種女子サッカー大会への参加を通じて、トレセンに抜擢されることはあります。'),
         ('キーパーでもトレセンに選ばれますか？', '選ばれます。トレセンでは、キーパーコーチがキーパー専用のトレーニングを行います。キーパーだけのトレーニングばかりではなく、フィールドの選手と同じトレーニングを行うこともあります。'),
         ('サッカーが上手じゃないとトレセンに選ばれませんか？', 'サッカー選手としての能力は、単に「サッカーが上手」だけではありません。「あいさつ」「人を尊敬できる（リスペクト）」「思いやりがある」「自分のことは自分でできる・決められる」「素直に謝れる」など、一人の人間として当たり前のことができるかも大事な要素です。トレセンは「長野県代表」として県外の選手と交流することが多くなるため、そうした意識や心構えもとても重要です。'),
         ('トレセンに選ばれないと、なでしこジャパンになれないのですか？', 'そんなことはありません。小さい時にトレセンに選ばれなくても、大人になって社会人のチームや大学のチーム、Lリーグのチームに所属して、なでしこジャパンになった選手はたくさんいます。小学生・中学生の年代でトレセンに選ばれて上のレベルのサッカーを経験することは貴重な経験で、なでしこジャパンに近づく一つの方法ですが、トレセンだけがすべてではありません。')]
    c = '<div class="acc faq">' + ''.join(f'<div class="acc-i"><button class="acc-b" type="button" data-acc aria-expanded="false" aria-controls="q{n}"><span class="t">{q}</span></button><div class="acc-p" id="q{n}" hidden><p>{a}</p></div></div>' for n, (q, a) in enumerate(Q, 1)) + '</div>'
    s += sec('faq', 'FAQ', 'よくある質問', c)
    s += '''
<section class="blk pale"><div class="wrap">
  <div class="fx-rel"><a href="cat-w.html"><span class="e">WOMEN</span><b>女子のお知らせ・大会情報</b></a><a href="teams.html"><span class="e">FIND A TEAM</span><b>チームを探す</b></a></div>
</div></section>
'''
    return s + end()

# ======================================================================
@page
def festival():
    s = head('festival', 'JFAフェスティバル', 'サッカーボールを蹴る楽しさを通じて、家族のコミュニケーションを深めるJFAフェスティバル（キッズ、レディース／ガールズ、ファミリーフットサル、フットボールデー、女子サッカーデー）のご案内です。',
             [('キッズ', 'grassroots.html')], 'JFA FESTIVAL', 'JFAフェスティバル', 'サッカーの普及や強化にとどまらず、ボールを蹴る楽しさを通じて、サッカーを身近に味わえる機会を増やし、家族のコミュニケーションを深めるイベントです。休日は、お近くのイベント会場へ足を運んでみてください。', 'FESTIVAL')
    F = [('kids', 'JFA キッズサッカーフェスティバル', 'サッカーが好きな子どもであれば誰でも参加できるイベントです。外遊びが少なくなっているキッズ年代の子どもたちが、サッカーを通じて身体を動かす楽しさに触れ、スポーツの素晴らしさを生涯にわたって感じられるきっかけを作ります。', 'キッズ年代'),
         ('ladies', 'JFA レディース／ガールズサッカーフェスティバル', '女性であれば誰でも気軽にサッカーを体験できるイベントです。サッカー経験の有無やレベルに関係なく、仲間と一緒にサッカーをする楽しさに触れ、スポーツの素晴らしさを生涯にわたって感じられるきっかけを作ります。', '女性'),
         ('family', 'JFA ファミリーフットサルフェスティバル', '親子で気軽に参加できるフェスティバルです。少人数でも気軽に楽しめるフットサルを通して、家族で仲良く身体を動かし、スポーツの素晴らしさを感じられるきっかけを作ります。', '親子'),
         ('fday', 'JFA フットボールデー', 'サッカーに普段親しみのない方々も含めて、全てのサッカーファミリーが集まるフェスティバルです。身体を動かす様々な体験を通してサッカーの魅力を知り、スポーツの楽しさを感じられるきっかけを作ります。', 'すべての方')]
    cards = ''.join(f'<article class="fs-c"><div class="fs-lg"><img src="assets/img/spc-fes-{k}.png" alt="{t}" loading="lazy"></div><div class="fs-bd"><p class="fs-who">対象：{w}</p><h3>{t}</h3><p>{d}</p></div></article>' for k, t, d, w in F)
    cards += '<article class="fs-c"><div class="fs-lg"><img src="assets/img/spc-wday.png" alt="JFA 女子サッカーデー" loading="lazy"></div><div class="fs-bd"><p class="fs-who">3月8日</p><h3>JFA 女子サッカーデー</h3><p>JFAは、国連が定める「国際女性デー」の3月8日を「JFA女子サッカーデー」と定めました。日本全国の女子サッカーが普及・発展するきっかけを作ります。</p></div></article>'
    s += sec('list', 'FESTIVAL', 'フェスティバルの種類', f'<div class="fs-g">{cards}</div>')
    s += sec('news', 'NEWS', 'フェスティバルの開催案内', '<p class="lead2">各フェスティバルの開催日・会場・参加募集は、お知らせで案内します。</p><div class="fx-btns"><a class="btn btn-g" href="list.html">フェスティバルのお知らせ<span class="arr">→</span></a><a class="btn btn-o" href="request.html#jfa">フェスティバルの掲載依頼（主催者向け）</a></div>', pale=True)
    return s + end()

# ======================================================================
@page
def sitemap():
    s = head('sitemap', 'サイトマップ', '長野県サッカー協会Webサイトのページ一覧です。', [], 'SITEMAP', 'サイトマップ', '当サイトのページ一覧です。', 'SITEMAP')
    G = [('ABOUT', '協会について', [('協会について', 'about.html'), ('利用規約', 'terms.html'), ('プライバシーポリシー', 'privacy.html'), ('お問い合わせ', 'contact.html')]),
         ('CATEGORY', 'カテゴリ', [('1種（社会人・大学）', 'cat-1.html'), ('2種（高校）', 'cat-2.html'), ('3種（中学）', 'cat-3.html'), ('4種（小学）', 'cat-4.html'), ('女子', 'cat-w.html'), ('フットサル', 'cat-f.html'), ('シニア', 'cat-s.html'), ('キッズ', 'grassroots.html')]),
         ('COMMITTEE', '専門委員会', [('審判', 'referee.html'), ('長野県レフェリーアカデミー', 'academy.html'), ('審判 各種講習会 年間計画', 'referee-schedule.html'), ('指導者', 'technical.html'), ('指導者講習会', 'coach-course.html'), ('医学', 'medical.html')]),
         ('START / GROW', 'はじめる・育成強化', [('はじめる', 'start.html'), ('チームを探す', 'teams.html'), ('JFAフェスティバル', 'festival.html'), ('育成・強化', 'grow.html'), ('長野スタイル', 'nagano-style.html'), ('女子トレセンについて', 'tracen.html')]),
         ('SUPPORT', '関わる・手続き', [('協賛のご案内', 'partner.html'), ('登録のご案内', 'registration.html'), ('書式ダウンロード', 'download.html'), ('掲載依頼', 'request.html'), ('主要競技場', 'facilities.html')]),
         ('INFO', 'お知らせ・情報', [('お知らせ一覧', 'list.html'), ('情報を探す（AI）', 'search.html'), ('リンク', 'links.html')])]
    body = '<div class="sm-g">' + ''.join(f'<div class="sm-c"><p class="e">{en}</p><h2>{ja}</h2><ul>' + ''.join(f'<li><a href="{h}">{t}</a></li>' for t, h in xs) + '</ul></div>' for en, ja, xs in G) + '</div>'
    s += f'\n<section class="blk"><div class="wrap">\n{body}\n</div></section>\n'
    return s + end()

if __name__ == '__main__':
    import sys
    names = sys.argv[1:] or list(P)
    for n in names: write(n, P[n]())
