# -*- coding: utf-8 -*-
"""既存サイトの固定ページを新デザインで起こす（出典：nagano-fa.or.jp 各ページ。本文は原文ベース、創作しない）
   python3 gen_fixed.py → pages/<name>.body.html を書き出す"""
import os, re, html
B = os.path.dirname(os.path.abspath(__file__))
O = 'https://www.nagano-fa.or.jp'
E = html.escape

def U(u):
    if u.startswith('//'): u = 'https:' + u
    if u.startswith('/'): u = O + u
    return u

def ext(u):
    m = re.search(r'\.(pdf|docx?|xlsx?|zip)(\?|$)', u, re.I)
    return m.group(1).lower() if m else ''

def ftype(u):
    e = ext(u)
    return {'pdf': ('PDF', ''), 'doc': ('DOC', 'w'), 'docx': ('DOC', 'w'), 'xls': ('XLS', 'x'), 'xlsx': ('XLS', 'x'), 'zip': ('ZIP', 'l')}.get(e, ('WEB', 'o'))

def head(name, title, desc, crumbs, en, h1, lead, bg, small='', active=''):
    bc = '<a href="index.html">TOP</a>' + ''.join(f'<span aria-hidden="true">›</span><a href="{h}">{E(t)}</a>' for t, h in crumbs) + f'<span aria-hidden="true">›</span><span aria-current="page">{E(title)}</span>'
    sm = f'<small>{small}</small>' if small else ''
    return (f'<!-- title:{title}|active:{active}|desc:{desc} -->\n<main>\n'
            f'<nav class="bc wrap" aria-label="パンくず">{bc}</nav>\n'
            f'<section class="pg-hero"><div class="bgword" aria-hidden="true">{bg}</div><div class="wrap"><div class="e">{en}</div><h1>{h1}{sm}</h1><p>{lead}</p></div></section>\n')

def lnav(items):
    return '<nav class="lnav" aria-label="ページ内メニュー"><ul>' + ''.join(f'<li><a href="#{i}"><small>{en}</small>{ja}</a></li>' for i, en, ja in items) + '</ul></nav>\n'

def sec(id_, en, ja, inner, pale=False, more=None):
    mo = f'<a class="more" href="{more[1]}">{more[0]}</a>' if more else ''
    return f'\n<section class="blk{" pale" if pale else ""}"{f" id={chr(34)}{id_}{chr(34)}" if id_ else ""}><div class="wrap">\n  <div class="bh"><div><div class="e">{en}</div><h2>{ja}</h2></div>{mo}</div>\n{inner}\n</div></section>\n'

def dl(items, one=False):
    """items: (label, url, note)"""
    li = []
    for it in items:
        label, url = it[0], it[1]; note = it[2] if len(it) > 2 else ''
        t, c = ftype(url)
        tgt = '' if url.startswith('#') else ' target="_blank" rel="noopener"'
        li.append(f'<li><a href="{E(U(url))}"{tgt}><span class="fbtn-i{(" " + c) if c else ""}">{t}</span>{E(label)}{f"<small>{E(note)}</small>" if note else ""}</a></li>')
    return f'<ul class="dl{" fx-dl1" if one else ""}">' + ''.join(li) + '</ul>'

def h3(t): return f'<h3 class="h3j">{t}</h3>'

def contact(title='お問い合わせ', who='（一社）長野県サッカー協会 事務局', tel='0263-87-8565', extra=''):
    return f'''
<section class="blk pale fx-ct"><div class="wrap">
  <div class="fx-ctb"><div><div class="e">CONTACT</div><h2>{title}</h2><p>{who}<br>TEL {tel}{extra}</p></div><a class="btn btn-n" href="contact.html">お問い合わせフォーム<span class="arr">→</span></a></div>
</div></section>
'''

def end(): return '</main>\n'

def write(name, s):
    open(os.path.join(B, 'pages', name.replace('_', '-') + '.body.html'), 'w', encoding='utf-8').write(s)
    print('wrote', name)

PAGES = {}
def page(fn):
    PAGES[fn.__name__] = fn; return fn

# ======================================================================
@page
def download():
    s = head('download', '書式ダウンロード', '長野県サッカー協会の各種申請書・届出書・報告書などの書式をダウンロードできます。',
             [], 'DOWNLOAD', '書式ダウンロード', '協会事務局・審判委員会への各種申請書、届出書、報告書などの書式を掲載しています。', 'FORMS')
    s += lnav([('office', 'OFFICE', '協会事務局'), ('entry', 'REGISTRATION', '登録・海外遠征'), ('referee', 'REFEREE', '審判委員会')])
    a = h3('事業関連')
    a += '<div class="fx-g2">'
    a += '<div><p class="fx-k">役員派遣</p>' + dl([('役員派遣申請書', '/wp-content/uploads/2016/09/yakuin_haken-1.docx')], 1) + '</div>'
    a += '<div><p class="fx-k">共催・後援申請</p>' + dl([('共催・後援申請を提出される方へ', '/wp-content/uploads/2016/09/dd0825591f09dd85d983daca63cd1a2d.pdf'), ('事業共催・後援申請書', '/wp-content/uploads/2024/06/ae8a89d68462aae9cd3ae0682ffdf480-2.docx')], 1) + '</div>'
    a += '</div>'
    a += '<p class="fx-k">全国大会出場激励金</p>' + dl([('全国大会出場激励金交付要綱', '/wp-content/uploads/2016/09/e64ccb7ae0443ca46a8441e11529fca1.pdf'), ('全国大会出場激励金交付申請書', '/wp-content/uploads/2024/06/24d736dc6d6700c30a27f0a58dbceb38-8.docx'), ('全国大会出場激励金振込口座番号等記入用紙', '/wp-content/uploads/2024/07/9b576ee8184725eb40fba93894540aa5-4-3.docx')])
    a += h3('会計書式') + dl([('2026年度 会計書式一式', '/wp-content/uploads/2026/09/c2ec277ca0303f920a3356fa45833936.zip')])
    a += h3('賛助会員 入会届・退会届') + dl([
        ('入会および退会の手続きに関する規則', '//nagano-fa.or.jp/wp-content/uploads/2016/02/member_kiyaku.pdf', '230KB'),
        ('入会金', '//nagano-fa.or.jp/wp-content/uploads/2016/02/member_nyukaikin.pdf', '51KB'),
        ('入会申請書', '/wp-content/uploads/2024/06/7df05e253f16fafadb51bd5b199b44fd-1.pdf', '62KB'),
        ('入会申請書', '/wp-content/uploads/2024/06/7df05e253f16fafadb51bd5b199b44fd-1.docx', '17KB'),
        ('退会届', '/wp-content/uploads/2024/06/98abc62f90c986143f7440424f435acb.pdf', '48KB'),
        ('退会届', '/wp-content/uploads/2024/06/98abc62f90c986143f7440424f435acb.docx', '17KB')])
    a += h3('ハンドブック等') + dl([('リスペクトフェアプレーツールダウンロード（JFA）', 'https://www.jfa.jp/respect/download.html'), ('キッズプログラム（JFA）', 'https://www.jfa.jp/youth_development/players_first/')])
    s += sec('office', 'OFFICE', '協会事務局', a)
    b = h3('サッカー／フットサル共通') + dl([('JFA ダウンロード（登録関係 資料）', 'http://www.jfa.jp/registration/download/'), ('各種登録に関する規則等（JFA）', 'http://www.jfa.jp/about_jfa/policy_rule/#07')])
    b += h3('海外遠征申請') + dl([('海外遠征申請について', '/wp-content/uploads/2025/10/1b155392fc4addf0ffe85b30048f749a.pdf'), ('海外遠征申請書（チーム用）', '/wp-content/uploads/2025/10/5958b8e419fe5124eb1317c40b81479c.doc')])
    b += '<p class="fx-note">登録の手続きは <a href="registration.html">登録のご案内</a> をご覧ください。</p>'
    s += sec('entry', 'REGISTRATION', '登録・海外遠征', b, pale=True)
    c = h3('各種申請書類') + dl([
        ('審判講習会 返金申請書（サッカー・フットサル共通）', '/wp-content/uploads/2026/03/20260330_rf_news_01-01.pdf', '85KB'),
        ('JFA KickOff 利用マニュアル（講習会など）', '//nagano-fa.or.jp/wp-content/uploads/2016/02/kickoff-referee_riyou.pdf', '10MB'),
        ('JFA KickOff 派遣割当システム利用マニュアル【審判員・INS】', '//nagano-fa.or.jp/wp-content/uploads/2016/02/kickoff-referee_haken.pdf', '3.4MB'),
        ('サッカー・フットサル審判員派遣申請書【原則2カ月前までに申請】', '/wp-content/uploads/2019/12/referee_dsp_app.doc', '39KB'),
        ('サッカー・フットサル審判員派遣申請書【原則2カ月前までに申請】', '/wp-content/uploads/2019/12/referee_dsp_app.pdf', '149KB'),
        ('サッカー・フットサル 休止申請（申請書・ご案内）', '/wp-content/uploads/2016/09/b462973a35ba1d7b4760231db7db82f5.pdf', '60KB')])
    c += h3('報告書類') + dl([
        ('サッカー審判報告書・重要事項報告書', '/wp-content/uploads/2019/08/1920refree_report.pdf', '36KB'),
        ('サッカー審判報告書・重要事項報告書', '/wp-content/uploads/2019/08/1920refree_report.xlsx', '21KB'),
        ('フットサル審判報告書・重要事項報告書', '//nagano-fa.or.jp/wp-content/uploads/2016/02/futsal-report_pdf.pdf', '36KB'),
        ('フットサル審判報告書・重要事項報告書', '//nagano-fa.or.jp/wp-content/uploads/2016/02/futsal-report_ex.xls', '20KB'),
        ('サッカー アセスメントリポート', '//nagano-fa.or.jp/wp-content/uploads/2016/02/soccer-Assessment-report.xls', '50KB'),
        ('サッカー アセスメントリポート メモ（形式1）', '//nagano-fa.or.jp/wp-content/uploads/2016/02/soccer-Assessment-report_memo01.pdf', '78KB'),
        ('サッカー アセスメントリポート メモ（形式2）', '//nagano-fa.or.jp/wp-content/uploads/2016/02/soccer-Assessment-report_memo02.pdf', '39.7KB'),
        ('サッカー アセスメントリポート メモ（形式1・2）', '//nagano-fa.or.jp/wp-content/uploads/2016/02/soccer-Assessment-report_ex.xls', '128KB'),
        ('サッカー 新アセスメントリポート メモ', '/wp-content/uploads/2016/09/1e6dcbb0ba56ba4dbb41387da4394556.xls', '264KB')])
    c += h3('試合運営の資料') + dl([
        ('試合前の打ち合わせ', '/wp-content/uploads/2016/09/game-meeting_doc.pdf', '333KB'),
        ('試合前の打ち合わせ（修正してご利用ください）', '/wp-content/uploads/2016/09/game-meeting_doc.docx', '139KB'),
        ('第4の審判員の役割', '//nagano-fa.or.jp/wp-content/uploads/2016/02/4th-referee_pdf.pdf', '117KB'),
        ('第4の審判員の役割（修正してご利用ください）', '//nagano-fa.or.jp/wp-content/uploads/2016/02/4th-referee_doc.doc', '47KB'),
        ('第4の審判メモ', '//nagano-fa.or.jp/wp-content/uploads/2016/02/soccer-4th_memo.pdf', '22KB')])
    s += sec('referee', 'REFEREE', '審判委員会', c)
    s += contact()
    return s + end()

# ======================================================================
@page
def links():
    s = head('links', 'リンク', 'JFA・北信越・長野県内のサッカー関係団体、リーグ、連盟、国際連盟、県内の郡市協会へのリンク集です。',
             [], 'LINKS', 'リンク', 'JFAや北信越・長野県内のサッカー関係団体、リーグ、連盟などへのリンクです。', 'LINKS')
    s += lnav([('jfa', 'JFA', 'JFA・北信越'), ('league', 'LEAGUE', 'リーグ・連盟'), ('nagano', 'NAGANO', '県内の団体'), ('world', 'WORLD', '国際連盟'), ('city', 'CITY', '郡市協会')])
    def lk(items):
        return '<ul class="fx-lk">' + ''.join((f'<li><a href="{E(u)}" target="_blank" rel="noopener"><span>{E(t)}</span>{f"<small>{E(sub)}</small>" if sub else ""}</a></li>' if u else f'<li><span class="fx-nolink"><span>{E(t)}</span></span></li>') for t, u, sub in items) + '</ul>'
    a = h3('日本サッカー協会') + lk([('公益財団法人日本サッカー協会', 'http://www.jfa.jp/', ''), ('JFAサッカー文化創造拠点 blue-ing!', 'https://blueing.jfa.jp/', ''), ('JFAこころのプロジェクト（夢先生）', 'https://www.jfa.jp/social_action_programme/yumesen/', ''), ('JFAアカデミー', 'https://www.jfa.jp/youth_development/jfa_academy/', ''), ('JFA Sports Managers College', 'https://www.jfa.jp/smc/', ''), ('チケットJFA', 'http://ticket.jfa.or.jp/', ''), ('JFA SQUARE', 'https://square.jfa.jp/squarePtnServlet/topmenu?ss=3', '')])
    a += h3('北信越地域協会') + lk([('北信越サッカー協会', 'http://www.hokushinetsu-fa.football/site/', ''), ('新潟県サッカー協会', 'http://www.niigata-fa.or.jp/', ''), ('富山県サッカー協会', 'https://toyfa.jp/', ''), ('石川県サッカー協会', 'http://www.ishikawa-fa.or.jp/', ''), ('福井県サッカー協会', 'http://fukui-fa.com/', '')])
    s += sec('jfa', 'JFA / HOKUSHINETSU', 'JFA・北信越地域協会', a)
    b = h3('リーグ') + lk([('Jリーグ', 'https://www.jleague.jp/', ''), ('JFL', 'http://www.jfl.or.jp/jfl-pc/view/s.php?a=1', ''), ('WEリーグ', 'https://weleague.jp/', ''), ('なでしこLEAGUE', 'http://www.nadeshikoleague.jp/', ''), ('高円宮杯 U-18 プレミアリーグ', 'https://www.jfa.jp/match/takamado_jfa_u18_premier2019/', ''), ('Fリーグ', 'https://www.fleague.jp/', '')])
    b += h3('長野県内の参加チーム') + lk([('松本山雅FC', 'http://www.yamaga-fc.com/', 'J3'), ('AC長野パルセイロ', 'http://parceiro.co.jp/', 'J3'), ('AC長野パルセイロ・レディース', 'https://parceiro.co.jp/ladies/', 'WE'), ('ボアルース長野FC', 'http://boaluz-nagano.com/', 'F1')])
    b += h3('連盟') + lk([('全日本大学サッカー連盟', 'http://www.jufa.jp/', ''), ('全日本大学女子サッカー連盟', 'http://www.juwfa.com/', ''), ('全国高等学校体育連盟', 'http://www.zen-koutairen.com/', ''), ('日本クラブユースサッカー連盟', 'http://www.jcy-football.com/', ''), ('日本中学校体育連盟', 'https://nippon-chutairen.or.jp/', ''), ('日本フットサル連盟', 'http://www.jff-futsal.or.jp/', ''), ('一般社団法人日本障がい者サッカー連盟', 'http://www.jiff.football/', '')])
    s += sec('league', 'LEAGUE / FEDERATION', 'リーグ・連盟', b, pale=True)
    c = lk([('北信越フットボールリーグ', 'http://www.hfl.jp/index.html', ''), ('北信越大学サッカーリーグ', 'http://hufl.info/', ''), ('高円宮杯 U-18 プリンスリーグ', 'https://www.jfa.jp/match/takamado_jfa_u18_prince2019/', ''), ('北信越ユース（U-15）リーグ', 'http://www.hokushinetsu-fa.football/site/u-15/youthu-15league/', ''), ('北信越クラブユース連盟', 'http://hokushinetsu-cy.com/', ''), ('長野県高等学校体育連盟', 'http://www.ngn-hssp.org/index.html', ''), ('長野県クラブユース連盟', 'https://jcy-nagano.net/', ''), ('長野県中学校体育連盟', 'http://n-ctr.sakura.ne.jp/', ''), ('長野県フットサル連盟', 'http://naganoff.jp/', ''), ('中信サッカーリーグ', 'http://csl.nagano.jp/', ''), ('東信フットボールリーグ', 'https://www.nfbl.info/', '')])
    s += sec('nagano', 'NAGANO / HOKUSHINETSU', '長野県・北信越のリーグ・連盟', c)
    d = lk([('FIFA 国際サッカー連盟', 'http://www.fifa.com/', ''), ('AFC アジアサッカー連盟', 'http://www.the-afc.com/', ''), ('EAFF 東アジアサッカー連盟', 'https://eaff.com/index_j.html', ''), ('UEFA 欧州サッカー連盟', 'http://jp.uefa.com/', ''), ('CAF アフリカサッカー連盟', 'http://www.cafonline.com/', ''), ('CONCACAF 北中米カリブサッカー連盟', 'http://www.concacaf.com/', ''), ('CSF（CONMEBOL）南米サッカー連盟', 'http://www.conmebol.com/', ''), ('OFC オセアニアサッカー連盟', 'https://www.oceaniafootball.com/', '')])
    s += sec('world', 'INTERNATIONAL', '国際連盟・大陸連盟', d, pale=True)
    cities = [('中野市サッカー協会', ''), ('須坂市サッカー協会', ''), ('長野市サッカー協会', 'http://ncfa.jp/'), ('千曲市サッカー協会', 'http://chikuma-fa.org/'), ('上田市サッカー協会', ''), ('佐久サッカー協会', ''), ('大町市サッカー協会', ''), ('安曇野市サッカー協会', ''), ('松本市サッカー協会', 'http://mf-a.net/'), ('塩尻市サッカー協会', 'http://soccer2shiojiri.web.fc2.com/'), ('木曽郡サッカー協会', ''), ('岡谷市サッカー協会', ''), ('諏訪市サッカー協会', ''), ('茅野市サッカー協会', ''), ('辰野町サッカー協会', ''), ('（一社）箕輪町サッカー協会', 'http://minowafa.net/'), ('伊那市サッカー協会', ''), ('飯田市サッカー協会', '')]
    e = lk([(t, u, '') for t, u in cities]) + '<p class="fx-note">郡市協会については、情報が不確定です。リンク掲載を希望される協会は、（一社）長野県サッカー協会 事務局（TEL 0263-87-8565）までご連絡ください。</p>'
    s += sec('city', 'CITY ASSOCIATIONS', '長野県内の郡市協会', e)
    return s + end()

# ======================================================================
FAC = {
 '北信': ('north', 'HOKUSHIN', [
  ('南長野運動公園総合球技場', '南長野', 'in', '長野市篠ノ井東福寺320番地', '026-293-4062', '//goo.gl/maps/Q7QXs', ''),
  ('千曲川リバーフロントスポーツガーデン運動場', 'リバーフロント', 'in', '長野市大字屋島四ツ屋前3300', '026-259-7358', '//goo.gl/maps/1lFnX', ''),
  ('長野運動公園総合運動場陸上競技場', '長野陸上', 'in', '長野市大字屋島四ツ屋前3300', '026-259-7358', '//goo.gl/maps/ihgwz', ''),
  ('長野市犀川第2運動場', '犀川河川敷', 'no', '長野市青木島町青木島乙954-2', '026-224-5083', '//goo.gl/maps/rAFMX', ''),
  ('木島平ジュニアサッカー場', '木島平', '', '下高井郡木島平村木島平スキー場内', '0269-82-4404', '//goo.gl/maps/V3PuM', ''),
  ('千曲市サッカー場', '千曲市', 'adj', '千曲市磯部1406-1', '026-276-1731', '//goo.gl/maps/CLiCn', '体育館'),
  ('須坂市福島スポーツ広場', '', 'no', '須坂市大字福島1077番地2', '026-248-2020', 'https://drive.google.com/open?id=13Bc1Y8IBzoOWv9zMJS5ZV14ARLA&usp=sharing', ''),
  ('須坂市北部運動広場', '', 'in', '須坂市大字小河原1055番地3', '026-248-5600', 'https://maps.app.goo.gl/wB5WuAer3QXcokq19', ''),
  ('飯綱高原南グラウンド', '', 'in', '長野市大字上ケ屋2472番地', '026-226-3272', 'https://www.google.com/maps/search/?api=1&query=%E9%A3%AF%E7%B6%B1%E9%AB%98%E5%8E%9F%E5%8D%97%E3%82%B0%E3%83%A9%E3%82%A6%E3%83%B3%E3%83%89', ''),
 ]),
 '東信': ('east', 'TOSHIN', [
  ('菅平高原スポーツランド サニアパーク菅平', 'サニアパーク', 'in', '上田市菅平高原1278-244', '0268-61-7090', '//goo.gl/maps/r4lRl', ''),
  ('菅平大原学園グラウンド', '大原グラウンド', '', '上田市菅平高原1223-2692', '0268-74-3377', '//goo.gl/maps/vxJtU', ''),
  ('上田古戦場公園多目的グラウンド', '上田古戦場', 'in', '上田市芳田3780-85', '0268-22-0695', '//goo.gl/maps/NVbB7', ''),
  ('上田市民の森多目的グラウンド', '市民の森', 'adj', '上田市芳田3780-85', '0268-23-0363（市民の森わしば山荘）', '//goo.gl/maps/4s6nP', '体育館'),
  ('千曲川市民緑地', '上田上堀', '', '上田市千曲川市民緑地（上田上堀）', '0268-23-5105', '//goo.gl/maps/wqUzU', ''),
  ('丸子総合グラウンド', '丸子総合', '', '上田市御嶽堂1-1', '0268-43-2250', '//goo.gl/maps/YTV8L', ''),
  ('東御中央公園グラウンド', '東御グラウンド', '', '東御市鞍掛188番地', '0268-62-2200', '//goo.gl/maps/FKWUr', ''),
  ('鈴与マタイスポーツパーク（佐久総合運動公園）', '佐久陸上', 'in', '佐久市平賀3011番地', '0267-63-7101', '//goo.gl/maps/knMcw', ''),
 ]),
 '中信': ('center', 'CHUSHIN', [
  ('松本平広域公園総合球技場 アルウィン', 'アルウィン', 'in', '松本市神林5300', '0263-57-2211', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.knjEUKr1uz2g&usp=sharing', ''),
  ('松本平広域公園芝生グラウンド', 'アル芝', 'adj', '松本市神林5300', '0263-57-2211', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kqWqEfMZ62F0&usp=sharing', 'アル芝備品倉庫内'),
  ('松本平広域公園球技場', '広域公園サッカー場', 'adj', '松本市神林5300', '0263-57-2211', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kCGNJSAZ2jg0&usp=sharing', '陸上競技場・体育館'),
  ('松本平広域公園陸上競技場', '松本広域陸上', 'in', '松本市神林5300', '0263-57-2211', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kipJSnjMFaik&usp=sharing', ''),
  ('松本平広域公園陸上補助競技場', '松本広域陸上補助', 'adj', '松本市神林5300', '0263-57-2211', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kbbCcBJ58Uu8&usp=sharing', '陸上競技場・体育館'),
  ('長野県フットボールセンター（松本市サッカー場）', 'フットボールセンター', 'in', '松本市今井7037-7', '0263-32-1818', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.ktVb-VFDxweE&usp=sharing', ''),
  ('松本市西南公園', '西南公園', 'adj', '松本市神林5838', '0263-32-1818', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.ktaAfosmOOo8&usp=sharing', '神林公民館（0263-58-2039）'),
  ('松本市あがた運動公園多目的広場', 'あがた運動公園', 'in', '松本市県1丁目3番29号', '0263-34-5650', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kid8eA9BTKhw&usp=sharing', ''),
  ('松本市島立運動広場', '島立運動広場', 'no', '松本市島立1213', '0263-32-1818', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kmuAh76wgjmI&usp=sharing', '松本市営サッカー場より'),
  ('松本市平瀬運動広場', '平瀬運動広場', 'no', '松本市島内7412', '0263-48-1110', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.k5hf47Zov9l8&usp=sharing', 'ラーラ松本より'),
  ('松本市和田運動広場', '和田運動広場', 'no', '松本市和田850-1', '0263-32-1818', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kNKRPCp_7l94&usp=sharing', '松本市営サッカー場より'),
  ('松本市あずさ運動公園多目的グランド', 'あずさグラウンド', 'adj', '松本市大字島内1666番地777', '0263-47-1427', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kNRdwxs1P7Tg&usp=sharing', '松本児童園（0263-47-0590）'),
  ('松本市梓川ふるさと公園多目的グラウンド', '梓川ふるさと公園', 'in', '松本市梓川梓7077-1', '0263-78-5000', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kqR-ZccUSFPQ&usp=sharing', ''),
  ('松商学園総合グラウンド', '松商総合', 'in', '松本市新村2095-1', '0263-48-7200（松本大学）', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kQveOy8_fhZc&usp=sharing', ''),
  ('松本市かりがねサッカー場', 'かりがね', 'in', '松本市大字惣社325', '', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kjvVkF9UKVKA&usp=sharing', ''),
  ('松本歯科大学陸上競技場', '歯科大', 'in', '塩尻市広丘郷原1780', '0263-52-3100（大学代表）', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.k_TuMj0wjGOo&usp=sharing', ''),
  ('穂高牧グラウンド', '穂高牧', 'in', '安曇野市穂高牧1928番地', '0263-83-8088', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kCzV3sbkvr1M&usp=sharing', ''),
  ('池田アルプス広場多目的グラウンド', '池田アルプスグラウンド', 'no', '北安曇郡池田町高瀬川第1緑地', '0261-62-7654', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kw-F0_yda8_I&usp=sharing', '池田町総合体育館より持出し'),
  ('あづみ野広場多目的グラウンド', 'あずみ野グラウンド', 'no', '北安曇郡池田町高瀬川第2緑地', '0261-62-7654', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.k1sd7M4M7fLg&usp=sharing', '池田町総合体育館より持出し'),
  ('大町市運動公園サッカー場', '大町市営', 'adj', '大町市常盤5638-44', '0261-22-8855', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.ke4REXlhCYc4&usp=sharing', '総合体育館'),
  ('大町市運動公園陸上競技場', '大町陸上', 'adj', '大町市常盤5638-44', '0261-22-8855', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.k0qYeA_aHg5M&usp=sharing', '総合体育館'),
  ('塩尻市中央スポーツ公園運動広場', '中スポ', 'adj', '塩尻市広丘高出1486-194', '0263-53-1001', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.k1j7s2BzrutY&usp=sharing', 'スポーツ管理棟'),
  ('塩尻市中央スポーツ公園サッカー場', '中スポ芝', 'adj', '塩尻市広丘高出1486-194', '0263-53-1001', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.ku8oXsj63jho&usp=sharing', 'スポーツ管理棟'),
  ('塩尻市小坂田公園多目的運動場', '小坂田公園', 'adj', '塩尻市塩尻町1080', '0263-53-1001', 'https://www.google.com/maps/d/edit?mid=zLdEiJ7pSt3Y.kLvFmfGRAjTQ&usp=sharing', 'レストラン棟・市民プール'),
 ]),
 '南信': ('south', 'NANSHIN', [
  ('茅野市運動公園陸上競技場', '茅野陸上', 'in', '茅野市玉川500', '0266-72-8399', '//goo.gl/maps/gM7oS', ''),
  ('富士見高原陸上競技場', '富士見高原', '', '諏訪郡富士見町広原12067-3', '0266-66-2931', '//goo.gl/maps/hBJg7', ''),
  ('女神湖人工芝サッカーグラウンド', '女神湖', '', '北佐久郡立科町女神湖湖畔', '', '//goo.gl/maps/anHav', ''),
  ('伊那陸上競技場', '伊那陸上', 'in', '伊那市伊那5810番地', '0265-73-6345', '//goo.gl/maps/3iNYZ', ''),
  ('伊那美すずスポーツ公園運動場', '伊那美すずスポーツ公園', 'in', '伊那市美篶7310番地123', '0265-72-3540', '//goo.gl/maps/syfJ9', ''),
  ('伊那富士塚スポーツ公園運動場', '伊那富士塚', 'in', '伊那市伊那4558番地1', '0265-78-5010', '//goo.gl/maps/Fl2ey', '陸上競技場にもあり'),
  ('美和湖芝生グラウンド', '美和湖', 'in', '伊那市長谷黒河内283先', '0265-78-5010', '//goo.gl/maps/70cz3', ''),
  ('大芝総合運動場', '大芝', 'adj', '上伊那郡南箕輪村2358-5', '0265-78-5835', '//goo.gl/maps/sGKFM', '管理事務所'),
  ('南箕輪富士塚運動場', '南箕輪富士塚', 'adj', '上伊那郡南箕輪村2380-1079', '0265-78-5835', '//goo.gl/maps/bJAdq', '老人ホーム'),
  ('飯田松尾総合運動場', '飯田総合', 'in', '飯田市松尾明7445', '0265-22-4511 内線3734', '//goo.gl/maps/DaZt4', ''),
  ('飯田三日市場運動公園多目的運動場', '飯田運動公園', 'adj', '飯田市三日市場1986', '0265-25-5588', '//goo.gl/maps/EKAy2', '県営飯田野球場・弓道場'),
  ('飯田市座光寺河川敷運動場', '飯田座光寺', 'no', '飯田市座光寺6565-ハ-6', '0265-22-4511 内線3734', '//goo.gl/maps/y4GM9', '高森消防署座光寺分署'),
  ('飯田市川路多目的広場', '飯田市川路', 'no', '飯田市川路2500', '0265-22-4511 内線3734', 'https://goo.gl/maps/RKUTu', '天竜川総合学習館・川路公民館'),
  ('高森町山吹ほたるパーク（長野県フットボールセンター高森）', 'フットボールセンター高森', 'in', '下伊那郡高森町山吹4540番地', '0265-35-9416', 'https://maps.app.goo.gl/okbNf3aVVuccU48HA', ''),
 ]),
}
AED = {'in': ('施設内', 'in'), 'adj': ('隣接施設内', 'adj'), 'no': ('設置なし', 'no'), '': ('調査中', 'na')}

def nagano_map(cls):
    src = open(os.path.join(B, 'pages', 'start.body.html'), encoding='utf-8').read()
    i = src.find('<div class="sg-map">'); j = src.find('</svg></div>', i) + len('</svg></div>')
    return src[i:j].replace('class="sg-map"', f'class="sg-map {cls}"')

@page
def facilities():
    total = sum(len(v[2]) for v in FAC.values())
    s = head('facilities', '主要競技場', '長野県内（北信・東信・中信・南信）の主要競技場の所在地、問い合わせ先、AEDの設置状況を掲載しています。',
             [], 'STADIUMS &amp; GROUNDS', '長野県内の主要競技場', f'北信・東信・中信・南信の主要競技場 {total}か所の所在地、問い合わせ先、AEDの設置状況を掲載しています。', 'GROUNDS')
    pills = '<button class="pill" type="button" data-f="all" aria-selected="true">すべて</button>' + ''.join(f'<button class="pill" type="button" data-f="{v[0]}" data-r="{k}" aria-selected="false">{k}<small>{len(v[2])}</small></button>' for k, v in FAC.items())
    rows = []
    for k, (slug, en, items) in FAC.items():
        for nm, ab, aed, addr, tel, mp, an in items:
            lab, cl = AED[aed]
            note = f'（{E(an)}）' if an else ''
            rows.append(f'<li data-c="{slug}"><div class="fc-h"><span class="fc-ar">{k}</span><h3>{E(nm)}</h3>{f"<span class={chr(34)}fc-ab{chr(34)}>略称：{E(ab)}</span>" if ab else ""}</div>'
                        f'<dl class="fc-d"><div><dt>所在地</dt><dd>{E(addr)}</dd></div>' + (f'<div><dt>電話</dt><dd>{E(tel)}</dd></div>' if tel else '') +
                        f'<div><dt>AED</dt><dd><span class="fc-aed {cl}">{lab}</span>{note}</dd></div></dl>'
                        f'<a class="fc-map" href="{E(U(mp))}" target="_blank" rel="noopener">地図を見る</a></li>')
    inner = f'''  <div class="fc-top">
    {nagano_map('fc-mp')}
    <div class="fc-side">
      <div class="filt fc-filt" data-filter="fcList"><div class="row"><span>地区</span>{pills}</div></div>
      <div class="fc-legend"><p class="fx-k">AEDの表示</p><ul><li><span class="fc-aed in">施設内</span>競技場の施設内に設置</li><li><span class="fc-aed adj">隣接施設内</span>隣接する施設内に設置</li><li><span class="fc-aed no">設置なし</span>設置されていません</li><li><span class="fc-aed na">調査中</span>情報を確認中です</li></ul></div>
      <p class="fx-note">掲載情報は行政等のWebサイトからの情報のため、すべての競技場を目視では確認していません。マッチコミッショナー、運営責任者等は試合開始前までにAEDの設置場所等をご確認ください。電話番号は問い合わせ先です。</p>
    </div>
  </div>
  <ul class="fc-list" id="fcList">{''.join(rows)}</ul>'''
    s += sec('list', 'GROUNDS', '地区別の主要競技場', inner)
    aed = dl([('AEDについて（JFA）', 'https://www.jfa.jp/football_family/medical/a07.html'), ('JFAメディカルインフォメーション', 'https://www.jfa.jp/football_family/medical/'), ('AEDの使い方（フクダ電子）', 'https://www.fukuda.co.jp/aed/'), ('AEDハンドブック（フクダ電子）', '//nagano-fa.or.jp/wp-content/uploads/2016/02/handb.pdf')])
    s += sec('aed', 'AED', 'AEDについて', '<p class="lead2">一次救命からAEDの使い方までを、以下のページで確認できます。</p>' + aed, pale=True)
    s += contact('掲載情報の誤り・新しい情報について', '掲載情報の誤りにお気づきの方、新たな情報をお持ちの方は、（一社）長野県サッカー協会 事務局までご連絡ください。<br>')
    return s + end()

# ======================================================================
@page
def request():
    s = head('request', '掲載依頼', '試合結果・星取表・お知らせなど、長野県サッカー協会Webサイトへの掲載依頼の方法とフォーマットを掲載しています。',
             [], 'REQUEST', '掲載依頼', '日程・試合結果・お知らせなど、当サイトへの掲載を依頼する方法です。試合結果・星取表は必ずPDFにして、掲載依頼フォームからアップロードしてください。', 'REQUEST')
    a = '''  <div class="cards c2 fx-rq">
    <a class="cd" href="https://forms.gle/NwtuuFpxL4AaL17t7" target="_blank" rel="noopener"><div class="bd"><div class="lab">GOOGLE FORM</div><h3>日程・試合結果・NEWS<br>掲載依頼フォーム</h3><p>大会の日程、試合結果、星取表、お知らせの掲載はこちらから依頼してください。</p><span class="go">フォームを開く</span></div></a>
    <a class="cd" href="#jfa"><div class="bd"><div class="lab">JFA FESTIVAL</div><h3>JFAフェスティバル<br>掲載依頼フォーム</h3><p>JFAキッズ／レディース・ガールズ／ファミリーフットサルフェスティバル専用の掲載依頼です。</p><span class="go">ご案内を見る</span></div></a>
  </div>'''
    s += sec('form', 'FORM', '掲載依頼フォーム', a)
    b = '''  <div class="tbl-w"><table class="tbl fx-tbl fx-rt"><thead><tr><th scope="col">依頼の締切</th><th scope="col">掲載日</th></tr></thead><tbody>
    <tr><td><b>月曜 9:00</b> までの依頼</td><td><b>火曜</b> 午前に掲載</td></tr>
    <tr><td><b>水曜 9:00</b> までの依頼</td><td><b>木曜</b> 午前に掲載</td></tr>
  </tbody></table></div>
  <p class="fx-note">運用はすべて平日です。締切日が祝祭日・休日の場合は、翌営業日が締切日になります。ただし、掲載日を変更する場合があります。</p>'''
    s += sec('rotation', 'SCHEDULE', '掲載までのローテーション', b, pale=True)
    c = dl([('試合結果用のフォーマット【リーグ戦用】', '/wp-content/uploads/2017/03/df65d8a55fb60012ebcca948a5f9bdd7.xlsx'), ('試合結果用のフォーマット【リーグ戦以外用】', '/wp-content/uploads/2017/03/game-results-format.xls'), ('星取表フォーマット', '/wp-content/uploads/2025/04/bb1236bdcf13ec57628dbb882b699f8a.xlsx'), ('試合結果以外のフォーマット', '/wp-content/uploads/2017/03/request-hp.docx')])
    s += sec('format', 'FORMAT', 'フォーマット', c)
    d = '''  <div class="prose">
    <p>JFAフェスティバル専用の掲載依頼フォームです。一度送信した掲載依頼を修正する場合も、こちらから再度送信してください。その際は、以前の掲載依頼の修正であることがわかるよう、掲載内容の冒頭にその旨を記載してください。</p>
    <ul>
      <li>office_kouhou@nagano-fa.or.jp からのメールを受信できるよう設定してください。</li>
      <li>半角カナは使用しないでください。</li>
      <li>添付できるファイルは Windows系ファイル（ワード、エクセル、パワーポイント、PDF、イラストレーター、フォトショップ、写真：jpeg・jpg・gif・png、テキスト）で、合計20MBまでです。</li>
    </ul>
  </div>
  <div class="tbl-w"><table class="tbl fx-tbl"><caption class="fx-cap">フォームの入力項目</caption><tbody>
    <tr><th scope="row">掲載依頼者名・電話番号・メールアドレス<span class="req">必須</span></th><td>電話番号は市外局番から（携帯可）</td></tr>
    <tr><th scope="row">所属<span class="req">必須</span></th><td>事務局／技術／審判／施設／医学／国体／規律／1種社会人／1種学生／2種／3種／4種／女子／フットサル／シニア／グラスルーツ</td></tr>
    <tr><th scope="row">タイトル<span class="req">必須</span></th><td>キッズサッカー／レディース・ガールズサッカー／ファミリーフットサル フェスティバル20××長野in（会場名）</td></tr>
    <tr><th scope="row">主催・共催・主管・後援<span class="req">必須</span></th><td>主催：一般社団法人長野県サッカー協会／共催：開催自治体等／主管：地区・市区郡町村サッカー協会、各種連盟の都道府県組織等／後援：公益財団法人日本サッカー協会</td></tr>
    <tr><th scope="row">協力</th><td>チーム名等</td></tr>
    <tr><th scope="row">開催日程・会場・参加者<span class="req">必須</span></th><td>参加者は対象となる参加者を記入</td></tr>
    <tr><th scope="row">掲載内容・資料</th><td>募集内容など。資料は5点まで添付できます</td></tr>
  </tbody></table></div>
  <p class="fx-note">うまく送信できない場合は、フォーマット（試合結果、試合結果以外）に必要な情報を記載のうえ、<a href="mailto:office_kouhou@nagano-fa.or.jp">office_kouhou@nagano-fa.or.jp</a> へ添付してお送りください。</p>
  <div class="fx-btns"><a class="btn btn-g" href="https://www.nagano-fa.or.jp/office-form-jfa" target="_blank" rel="noopener">JFAフェスティバル掲載依頼フォーム<span class="arr">→</span></a></div>'''
    s += sec('jfa', 'JFA FESTIVAL', 'JFAフェスティバルの掲載依頼', d, pale=True)
    s += contact()
    return s + end()

# ======================================================================
@page
def terms():
    s = head('terms', '利用規約', '長野県サッカー協会ホームページの利用規約です。', [], 'TERMS OF USE', '利用規約',
             '（一社）長野県サッカー協会のホームページ（以下「当サイト」）をご利用いただくには、以下の利用規約をご一読いただき、ご同意いただく必要があります。当サイトをご利用いただくことで、以下の利用規約にご同意いただいたものとみなします。', 'TERMS')
    s = s.replace('</p></div></section>\n', '</p><div class="meta"><span>制定 <b>2013年8月4日</b></span></div></div></section>\n', 1)
    sections = [
      ('t1', '利用規約に関して', '<p>当協会が定める利用規約（以下「当利用規約」といいます）は、了承を得ることなく内容を変更することがあります。</p>'),
      ('t2', '当サイトに関して', '<p>当協会は予告をせずに、当サイトの情報の更新・修正・削除等をすることがあります。また、サイトの公開を一時中止・閉鎖等することがあります。これらによりお客様に生じるいかなる障害に関しましても、当協会は一切の責任を負わないものとします。</p>'),
      ('t3', '当サイトから配信される情報に関して', '<p>当協会は当サイトに掲載する情報に関して、細心の注意を払っております。しかしながら、全ての情報の正確性、安全性等に関しまして、一切の保障をするものではありません。また、当サイトをご利用することにより発生するいかなる損害・損失・トラブル等に関して、いかなる理由に関わらず、当協会は一切の責任を負わないものとします。</p>'),
      ('t4', '著作権などに関して', '<p>当サイトに掲載されている全ての内容に関する権利は、当協会に既存するか、ライセンスに基づいて使用しております。したがいまして、当サイトに掲載されている全てを、無断使用・複製することを固く禁止いたします。</p><h3>当サイトへのリンクについて</h3><p>当サイトにリンクをはる場合には、必ず当協会までご連絡ください。その際、サイトの内容などによっては、リンクをはることをお断りする場合があります。</p><h3>当サイトからのリンクについて</h3><p>当サイトから当協会以外のサイトにリンクをはっている場合、サイト利用者の皆さまへの便宜のためにご提供しているにすぎません。また、リンク先のサイトは当協会の管理下にあるものではありません。</p>'),
      ('t5', '免責事項', '<p>当協会は、利用者および第三者に対し、当サイトを通じて提供する全ての情報に関する適法性、完全性、正確性、有用性、第三者の権利侵害がないこと等、当サイトの利用・接続環境等に関していかなる保障も行なわないものとします。</p><p>また、未成年者が当サイトを利用する場合、親権者は当サイトや当サイト内で公開されるコンテンツが未成年者にとって適切かどうか判断する責を負うものとします。利用者が当サイトの利用に際し第三者に対して損害を与えた場合、利用者の責任と費用をもって解決し、当協会に損害を与えることのないものとします。</p><p>当協会は当サイトの利用により発生した利用者の損害全てに対し、いかなる責任も負わないものとし、一切の損害賠償をする義務は負わないものとします。また、利用者が本規約に違反したことにより当協会に損害を与えた場合、当協会は当該利用者に対して損害の賠償を請求できるものとします。</p><p>下記の事項により利用者または第三者が被った損害について、当協会はその責任を負わないものとします。</p><ul><li>通信機器、通信回線、コンピュータ等のシステム機器等の障害または瑕疵等により当サイトの提供が中断もしくは遅延し、または誤送信もしくは欠陥が生じた場合</li><li>第三者による当サイトへの妨害、システムへの侵入、情報改変等により当サイトの提供が中断もしくは遅延し、または誤送信もしくは欠陥が生じた場合</li><li>当協会の推奨しない通信環境から接続したために当サイト内の情報を完全に取得できない場合、または機器等に障害・瑕疵が発生した場合</li><li>利用者から送信された情報が、当協会の故意もしくは重過失によらないシステム上の制限、エラー、内容の瑕疵により受信されない場合、または誤った情報が受信された場合</li><li>当協会に故意または重過失なくして、当サイトが提供する情報に誤送信、欠陥があった場合</li></ul><p>当協会は、当サイトの利用に関して利用者が使用しているコンピュータ、通信機器、通信ソフト等のサポートは一切行いません。利用者と各種プロバイダーとの接続に関する苦情等についても、一切これを受け付けないものとします。</p><p>利用者は、当サイトの利用ならびに接続に関連して第三者からクレーム、損害賠償請求等を受けた場合には、自らの責任と負担において解決するものとし、当協会は解決に協力する義務を負わないものとします。また、当協会が第三者からクレーム、損害賠償請求等を受けた場合、その解決に関して必要な範囲で利用者に協力を求めることがあり、利用者はこれを拒否しないものとします。</p>'),
      ('t6', '本規約違反について', '<p>サイト利用者の皆さまには、当サイトを（一社）長野県サッカー協会公式ウェブサイトと認識し、合法的な目的にのみ利用することに同意していただきます。また以下の事項に該当する場合には、当協会は利用者に対して除名・法的措置をとらせていただくことがあります。</p><ul><li>当協会が定める著作権規定に対して違反する行為、また他者の著作権、商標権等の知的財産権を侵害する行為、またはそのおそれのある行為があった場合</li><li>他者の財産、プライバシーもしくは肖像権を侵害する行為、またはそのおそれのある行為があった場合</li><li>他者を差別もしくは誹謗中傷し、または他者の名誉もしくは信用を毀損する行為があった場合</li><li>当サイトの運営を妨害する行為があった場合</li><li>別の他人や組織などになりすましたり、人物、企業、グループあるいはその他の実体との関係を故意に詐称した場合</li><li>意図的に法律違反をした場合</li><li>上記各項目のいずれかに該当する行為（当該行為を他者が行っている場合を含む）を助長する目的の行為（リンクを張る等を含む）があった場合</li></ul><p class="pv-sign">2013年8月4日　（一社）長野県サッカー協会</p>'),
    ]
    toc = ''.join(f'<li><a href="#{i}">{t}</a></li>' for i, t, _ in sections)
    body = ''.join(f'<h2 id="{i}"><span class="no">{n+1}.</span>{t}</h2>{c}' for n, (i, t, c) in enumerate(sections))
    s += f'\n<!-- 出典：/office_top/kiyaku（本文は原文のまま） -->\n<section class="blk"><div class="wrap">\n  <div class="pv-doc">\n    <nav class="pv-toc" aria-label="目次"><b>目次</b><ol>{toc}</ol></nav>\n    <div class="prose pv-body">{body}</div>\n  </div>\n</div></section>\n'
    return s + end()

if __name__ == '__main__':
    import sys
    names = sys.argv[1:] or list(PAGES)
    for n in names: write(n, PAGES[n]())
