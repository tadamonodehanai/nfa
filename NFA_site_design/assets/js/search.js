/* =========================================================
   AI検索（デモ版・対話型）
   - 話しかけるように入力 → 協会が吹き出しで答える。続けて話すと条件が重なっていく（AND）
   - 入力文から「対象・目的・地域」を読み取り（toFacets）、条件を AND で掛け合わせて
     掲載データ（search-data.js ＝ 既存サイトから収集した実データ）を絞り込む
   - 回答文はデータからその場で組み立てる（compose）
   ★本番ではここを Claude API（＋掲載ページを根拠にした検索＝RAG）に差し替える。
     差し替え位置には「SWAP」コメントを付けている
   ========================================================= */
(function () {
  'use strict';
  var DOCS = window.NFA_DOCS || [];
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var RM = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- 語彙（条件の辞書） ---------- */
  var AX = {
    t: { name: '対象', items: [
      { v: 'referee', l: '審判', rx: /審判|レフェリー/ },
      { v: 'technical', l: '指導者', rx: /指導者|コーチ|監督|ライセンス/ },
      { v: 'grassroots', l: 'キッズ', rx: /キッズ|幼児|未就学|園児|U-?[68](?!\d)/i },
      { v: 'cat-4', l: '小学生（4種）', rx: /小学|4種|４種|U-?1[0-2](?!\d)|少年/i },
      { v: 'cat-3', l: '中学生（3種）', rx: /中学|3種|３種|U-?1[3-5](?!\d)/i },
      { v: 'cat-2', l: '高校生（2種）', rx: /高校|2種|２種|U-?1[6-8](?!\d)/i },
      { v: 'cat-1', l: '社会人・大学（1種）', rx: /社会人|大学|1種|１種|大人|一般/ },
      { v: 'cat-w', l: '女子', rx: /女子|なでしこ|女の子|ガールズ|レディース/ },
      { v: 'cat-s', l: 'シニア', rx: /シニア|O-?[3-7]\d|４０歳|40歳|50歳|60歳/i },
      { v: 'cat-f', l: 'フットサル', rx: /フットサル/ },
      { v: 'para', l: '障がい者サッカー', rx: /障がい|障害|ブラインド|知的障|電動車椅子/ }
    ]},
    p: { name: '目的', items: [
      { v: 'course', l: '講習会に参加', rx: /講習|資格|更新|ライセンス|研修|養成|受講|昇級/ },
      { v: 'reg', l: '登録・手続き', rx: /登録|手続|申請|申込|申し込|移籍|入りたい|入会|加入|チーム.{0,3}(探|入|さが)/ },
      { v: 'result', l: '試合結果を見る', rx: /結果|星取|順位|勝敗|スコア|優勝|勝った/ },
      { v: 'event', l: 'イベント・セミナーに参加', rx: /イベント|セミナー|フェスティバル|体験|教室|参加したい|募集|キャンプ/ },
      { v: 'schedule', l: '大会日程を知る', rx: /いつ|日程|予定|スケジュール|組み合わせ|組合せ|開催日|大会|試合/ },
      { v: 'news', l: 'お知らせを読む', rx: /お知らせ|ニュース|最新|新着/ }
    ]},
    r: { name: '地域', items: [
      { v: '北信', l: '北信', rx: /北信|長野市|千曲|須坂|中野|飯山/ },
      { v: '東信', l: '東信', rx: /東信|佐久|上田|上小|小諸|東御/ },
      { v: '中信', l: '中信', rx: /中信|松本|塩尻|安曇|大町|木曽/ },
      { v: '南信', l: '南信', rx: /南信|諏訪|伊那|飯田/ },
      { v: '県全域', l: '県全域', rx: /県全域|県全体|全県|県大会|県リーグ/ }
    ]}
  };
  var PAGE = { 'cat-4': 'cat-4.html', 'cat-3': 'cat-3.html', 'cat-2': 'cat-2.html', 'cat-1': 'cat-1.html', 'cat-w': 'cat-w.html',
    'cat-s': 'cat-s.html', 'cat-f': 'cat-f.html', grassroots: 'grassroots.html', referee: 'referee.html', technical: 'technical.html' };
  var SHORT = { 'cat-4': '小学生', 'cat-3': '中学生', 'cat-2': '高校生', 'cat-1': '社会人・大学', 'cat-w': '女子', 'cat-s': 'シニア',
    'cat-f': 'フットサル', grassroots: 'キッズ', para: '障がい者サッカー', referee: '審判', technical: '指導者' };
  function label(ax, v) { var it = AX[ax].items.filter(function (x) { return x.v === v; })[0]; return it ? it.l : v; }

  /* ---------- 入力文 → 条件（SWAP：本番はAIが意図を読み取る） ---------- */
  function toFacets(q) {
    var f = { t: [], p: [], r: [] }, rest = q;
    ['t', 'p', 'r'].forEach(function (ax) {
      // 1つの軸につき1つだけ読み取る（矛盾した条件を作らない）
      var best = null;
      AX[ax].items.forEach(function (it) {
        var m = q.match(it.rx);
        // 目的は辞書の並び順（具体的な意図が先）、対象・地域は文中で先に出た語を採る
        if (m && (best === null || (ax !== 'p' && m.index < best.i))) best = { v: it.v, i: m.index };
      });
      if (best) f[ax].push(best.v);
      AX[ax].items.forEach(function (it) { rest = rest.replace(new RegExp(it.rx.source, 'g' + (it.rx.flags || '').replace('g', '')), ' '); });
    });
    // 「チームに入りたい」は登録よりチーム探しとして扱う
    if (/チーム.{0,4}(入|探|さが)/.test(q)) { f.join = true; f.p = []; }
    f.kw = rest.replace(/[？?！!、。・「」（）()]/g, ' ')
      .replace(/について|ですか|ますか|ありますか|知りたい|したい|たい|ている|いる|どう|すれば|どこ|なに|何|ください|教えて|情報|の|は|を|が|に|で|と|も|へ|や/g, ' ')
      .split(/\s+/).filter(function (w) { return w.length >= 2; });
    return f;
  }

  /* ---------- 絞り込み（軸の中は OR、軸どうしは AND） ---------- */
  function hit(d, st) {
    if (st.t.length && !st.t.some(function (v) { return d.c.indexOf(v) > -1; })) return false;
    if (st.p.length && !st.p.some(function (v) { return d.p.indexOf(v) > -1; })) return false;
    if (st.r.length && !st.r.some(function (v) { return d.r.indexOf(v) > -1; })) return false;
    return true;
  }
  function score(d, kw) {
    var s = 0, text = (d.g || '') + ' ' + d.t + ' ' + (d.x || '');
    kw.forEach(function (w) { if (text.indexOf(w) > -1) s += 3; });
    return s;
  }
  function find(st) {
    var kw = st.kw || [], none = !st.t.length && !st.p.length && !st.r.length;
    var list = DOCS.filter(function (d) { return hit(d, st); }).map(function (d) { return { d: d, s: score(d, kw) }; });
    if (none && kw.length) list = list.filter(function (x) { return x.s > 0; });
    // 条件があるときは新しい順（掲載の鮮度を優先）、ことばだけのときは一致度順
    if (none && kw.length) list.sort(function (a, b) { return b.s - a.s || (a.d.d < b.d.d ? 1 : -1); });
    return list.map(function (x) { return x.d; });
  }

  /* ---------- 回答文を組み立てる（SWAP：本番はAIが根拠ページから生成） ---------- */
  function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
  function who(st) { return st.t.length ? st.t.map(function (v) { return SHORT[v] || label('t', v); }).join('・') : ''; }
  function groups(docs) {
    var g = {}, order = [];
    docs.forEach(function (d) { if (d.k !== 'pdf') return; if (!g[d.g]) { g[d.g] = { name: d.g, n: 0, d: d.d }; order.push(d.g); } g[d.g].n++; if (d.d > g[d.g].d) g[d.g].d = d.d; });
    return order.map(function (k) { return g[k]; }).sort(function (a, b) { return a.d < b.d ? 1 : -1; });
  }
  function compose(st, docs) {
    var w = who(st), W = w ? w + 'の' : '', P = st.p[0], R = st.r.length ? st.r.join('・') : '';
    var pdfs = docs.filter(function (d) { return d.k === 'pdf'; }), news = docs.filter(function (d) { return d.k === 'news'; });
    var h = [];
    if (st.join) {
      h.push('<p>' + (w ? w + 'の' : '') + 'チームは「<a href="teams.html">チームを探す</a>」で、活動地域・活動競技・募集年代から探せます。' +
        (st.t.indexOf('cat-w') > -1 ? '女子の選手を募集しているチームも絞り込めます。' : '') + '</p>');
      h.push('<p>入団後の選手登録は、JFAの登録システム「KICKOFF」で行います。手順は「<a href="grow.html#register">登録について</a>」にまとめています。</p>');
      return h.join('');
    }
    if (!docs.length) {
      var what0 = st.q ? '「' + esc(st.q) + '」' : W + (P ? label('p', P).replace(/を.*$|に.*$/, '') : '条件');
      return '<p>' + what0 + 'に当てはまる資料・記事は、いま公式サイトに掲載されていませんでした。条件をひとつ外すか、ことばを変えてお試しください。</p>' +
        '<div class="sr-picks">' + $$('.sr-chips [data-sq]').map(function (b) { return '<button type="button" class="sr-pick" data-sq2>' + esc(b.textContent) + '</button>'; }).join('') + '</div>';
    }
    if (P === 'schedule' || P === 'result') {
      var gs = groups(docs).slice(0, 4), what = P === 'schedule' ? '日程' : '試合結果・星取表';
      if (gs.length) {
        h.push('<p>' + W + '大会・リーグの' + what + 'は、大会ごとの資料（PDF）で公開しています' + (R ? '（' + R + 'に絞っています）' : '') + '。いま掲載されているのは次の大会です。</p>');
        h.push('<ul class="sr-a-list">' + gs.map(function (g) { return '<li><b>' + esc(g.name) + '</b><span>最新の更新 ' + g.d + '／資料 ' + g.n + '件</span></li>'; }).join('') + '</ul>');
        h.push('<p>いちばん新しい資料は、' + pdfs[0].d + ' 更新の「' + esc(pdfs[0].g + ' ' + pdfs[0].t) + '」です。' + (P === 'schedule' ? '日程は変更になる場合があります。' : '') + '</p>');
      } else {
        h.push('<p>' + W + what + 'に関するお知らせは次のとおりです。いちばん新しいものは、' + news[0].d + '「' + esc(news[0].t) + '」です。</p>');
      }
    } else if (P === 'course') {
      var c = news[0] || docs[0];
      h.push('<p>' + (w ? w + 'に関する' : '') + '講習会・資格の案内は、お知らせで告知しています。直近の案内は、' + c.d + '「' + esc(c.t) + '」です。</p>');
      if (st.t.indexOf('referee') > -1) h.push('<p>審判講習会の正式なご案内は、原則開催日の1か月前までにホームページに掲載します。また、JFA KICKOFFよりメール配信を行います。</p>');
      if (st.t.indexOf('technical') > -1) h.push('<p>指導者ライセンス（C級・D級・キッズリーダーなど）の内容と受講資格は、<a href="technical.html">指導者のページ</a>にまとめています。</p>');
    } else if (P === 'event') {
      h.push('<p>' + W + '参加募集・イベントの案内は' + news.length + '件あります。いちばん新しいものは、' + (news[0] || docs[0]).d + '「' + esc((news[0] || docs[0]).t) + '」です。</p>');
    } else if (P === 'reg') {
      h.push('<p>選手・チームの登録は、JFAの登録システム「KICKOFF」で行います。手順と関連資料は「<a href="grow.html#register">登録について</a>」にまとめています。</p>');
      if (docs[0]) h.push('<p>' + W + '登録・申込に関する最新の掲載は、' + docs[0].d + '「' + esc(docs[0].g ? docs[0].g + ' ' + docs[0].t : docs[0].t) + '」です。</p>');
    } else {
      var top = docs.slice(0, 3);
      h.push('<p>' + W + (R ? R + 'の' : '') + '最新の掲載は次のとおりです。</p>');
      h.push('<ul class="sr-a-list">' + top.map(function (d) { return '<li><b>' + esc(d.g ? d.g + ' ' + d.t : d.t) + '</b><span>' + d.d + '</span></li>'; }).join('') + '</ul>');
    }
    if (st.t[0] && PAGE[st.t[0]]) h.push('<p>' + (SHORT[st.t[0]] || '') + 'の情報は、<a href="' + PAGE[st.t[0]] + '">' + (SHORT[st.t[0]] || '') + 'のページ</a>にもまとめています。</p>');
    return h.join('');
  }

  /* ---------- 対話の状態 ---------- */
  var C = { t: [], p: [], r: [], join: false };   // いま重なっている条件
  var turns = [];
  var thread = $('#sr-thread');
  var VOICE = {   // タグを押したときも“会話”になるよう、人の言い方に言い換える（utterance）
    p: { schedule: '大会の日程を教えてください', result: '試合結果が知りたいです', reg: '登録の手続きを知りたいです',
      course: '講習会について知りたいです', event: '参加できるイベントはありますか？', news: '最新のお知らせを見たいです' }
  };
  function utterance(ax, v, mode) {
    if (mode === 'drop') return '「' + label(ax, v) + '」の条件ははずしたいです';
    if (ax === 't') return mode === 'swap' ? (SHORT[v] || label(ax, v)) + 'の場合はどうですか？' : (SHORT[v] || label(ax, v)) + 'の情報が見たいです';
    if (ax === 'p') return VOICE.p[v];
    if (ax === 'r') return v === '県全域' ? '県全体のものだけにしてください' : v + 'だけに絞ってください';
    return v;
  }
  function snap() { return { t: C.t.slice(), p: C.p.slice(), r: C.r.slice(), kw: [], join: C.join }; }
  function cnt(o) { return find(o).length; }

  /* 条件の変化を、ひとことで伝える */
  function diffNote(prev) {
    var add = [], rep = [], rm = [];
    ['t', 'p', 'r'].forEach(function (ax) {
      var a = prev[ax][0], b = C[ax][0];
      if (!a && b) add.push(label(ax, b));
      else if (a && !b) rm.push(label(ax, a));
      else if (a && b && a !== b) rep.push('「' + label(ax, a) + '」を「' + label(ax, b) + '」に');
    });
    var n = [];
    if (rep.length) n.push(rep.join('、') + '替えました。');
    if (add.length && turns.length) n.push('「' + add.join('」「') + '」で絞り込みました。');
    if (rm.length) n.push('「' + rm.join('」「') + '」の条件をはずしました。');
    return n.join('');
  }

  /* 次の一歩（候補タグ）：0件になるものは出さない */
  function steps(docs) {
    var rows = [], st = snap();
    function chips(ax, mode, items) {
      return items.map(function (it) {
        var o = snap(); o[ax] = [it.v];
        var n = cnt(o); if (!n || (n === docs.length && mode !== 'swap' && C[ax].length)) return '';
        return '<button type="button" class="sr-pick" data-ax="' + ax + '" data-v="' + it.v + '" data-mode="' + mode + '">' + (ax === 't' ? (SHORT[it.v] || it.l) : it.l) + '<small>（' + n + '件）</small></button>';
      }).join('');
    }
    var T = AX.t.items.filter(function (x) { return x.v !== 'para'; });
    if (!C.t.length) { var h = chips('t', 'add', T); if (h) rows.push(['どなたの情報ですか', h]); }
    if (C.t.length && !C.p.length) { var h2 = chips('p', 'add', AX.p.items); if (h2) rows.push(['何を知りたいですか', h2]); }
    if (C.t.length && C.p.length && !C.r.length && /schedule|result/.test(C.p[0])) { var h3 = chips('r', 'add', AX.r.items); if (h3) rows.push(['地区で絞る', h3]); }
    if (C.t.length && C.p.length) { var h4 = chips('p', 'swap', AX.p.items.filter(function (x) { return x.v !== C.p[0]; })); if (h4) rows.push(['ほかに知りたいこと', h4]); }
    if (C.t.length) { var h5 = chips('t', 'swap', T.filter(function (x) { return x.v !== C.t[0]; })); if (h5) rows.push(['同じ条件で、ほかの対象を見る', h5]); }
    return rows;
  }

  function listHtml(docs, full) {
    var pdfs = docs.filter(function (d) { return d.k === 'pdf'; }), news = docs.filter(function (d) { return d.k === 'news'; });
    var np = full ? 6 : 3, nn = full ? 4 : 2, h = '';
    if (pdfs.length) h += '<div class="sr-rel-h"><h3>該当する資料</h3><span>' + pdfs.length + '件・新しい順</span></div><ul class="dl sr-dl">' + pdfs.slice(0, np).map(docLi).join('') + '</ul>';
    if (news.length) h += '<div class="sr-rel-h"><h3>該当する記事</h3><span>' + news.length + '件・新しい順</span></div><ul class="nl">' + news.slice(0, nn).map(newsLi).join('') + '</ul>';
    var rest = Math.max(0, pdfs.length - np) + Math.max(0, news.length - nn);
    if (full && rest) h += '<button type="button" class="sr-all" data-all>残り ' + rest + ' 件もすべて表示</button>';
    return h;
  }
  function docLi(d) {
    return '<li><a href="' + esc(d.u) + '" target="_blank" rel="noopener"><span class="fbtn-i">' + (d.f || 'PDF') + '</span><span class="sr-dn">' + esc(d.g) + '<br><b>' + esc(d.t) + '</b></span><small>' + d.d + '</small></a></li>';
  }
  var BADGE = { '審判': 'var(--tag-match)', '技術': 'var(--tag-info)', '協会': 'var(--tag-info)', 'グラスルーツ': 'var(--tag-event)', '医学': 'var(--alert)' };
  function newsLi(d) {
    return '<li><a href="' + esc(d.u) + '" target="_blank" rel="noopener"><span class="th"><img src="assets/img/ph-photo.jpg" alt="" loading="lazy"><span class="badge" style="background:' + (BADGE[d.l] || 'var(--navy)') + '">' + esc(d.l) + '</span></span><span><span class="mt"><span class="dt">' + d.d + '</span></span><h3>' + esc(d.t) + '</h3></span><span class="ar" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a></li>';
  }

  /* 1ターン分の返事をつくる */
  function reply(prev, unread, raw) {
    if (unread) {
      return { msg: '<p>すみません、「' + esc(raw) + '」がうまく汲み取れませんでした。<b>どなたの</b>ことか、<b>何を</b>知りたいかを教えていただけると探せます。たとえば「小学生の大会はいつ？」「審判の資格を更新したい」のように。</p>', docs: [], rows: [['まずはここから', starters()]] };
    }
    var st = snap(), docs = find(st), note = diffNote(prev), relaxed = '';
    if (!C.t.length && !C.p.length && !C.r.length) {
      return { msg: '<p>条件をすべてはずしました。あらためて、ことばでもタグからでもお探しください。</p>', docs: [], rows: [['まずはここから', starters()]] };
    }
    if (!docs.length && (C.p.length || C.r.length)) {
      var drop = C.p.length ? 'p' : 'r', s2 = snap(); s2[drop] = [];
      var d2 = find(s2);
      if (d2.length) { relaxed = label(drop, C[drop][0]); docs = d2; st = s2; }
    }
    var ask = !C.t.length && !C.join && /schedule|result/.test(C.p[0] || '') && !C.r.length;
    var msg = note ? '<p class="sr-note">' + note + '</p>' : '';
    if (ask) msg += '<p>' + label('p', C.p[0]).replace(/を.*$|に.*$/, '') + 'ですね。<b>どの年代・種目の情報ですか？</b>選ぶと絞り込みます。</p>';
    else {
      if (relaxed) msg += '<p>「' + esc(relaxed) + '」に当てはまる掲載は、いまはありませんでした。条件をゆるめて、近い情報をご案内します。</p>';
      msg += compose(st, docs);
      if (docs.length) msg += '<p class="sr-src">この回答は、下に並べた公式サイトの資料・記事（' + docs.length + '件）をもとに組み立てています。</p>';
    }
    return { msg: msg, docs: ask ? [] : docs, rows: steps(docs) };   // 聞き返すときは一覧を出さず、選択肢を先に見せる
  }
  function starters() {
    return $$('.sr-chips [data-sq]').map(function (b) { return '<button type="button" class="sr-pick" data-say="' + esc(b.textContent) + '">' + esc(b.textContent) + '</button>'; }).join('');
  }

  /* ---------- 描画 ---------- */
  function syncSide() {
    $$('.sr-ops .pill').forEach(function (b) {
      var ax = b.getAttribute('data-ax'), v = b.getAttribute('data-v'), on = C[ax].indexOf(v) > -1;
      b.setAttribute('aria-pressed', on);
      if (on) { b.disabled = false; return; }
      var o = snap(); o[ax] = [v]; b.disabled = cnt(o) === 0;   // 該当する情報がない条件は選べない
    });
    var n = C.t.length + C.p.length + C.r.length, sel = $('#sr-sel'); if (sel) sel.textContent = n + '件 選択中';
    var tags = [];
    ['t', 'p', 'r'].forEach(function (ax) { C[ax].forEach(function (v) { tags.push('<button type="button" class="sr-tag" data-ax="' + ax + '" data-v="' + v + '" data-mode="drop" aria-label="' + AX[ax].name + '：' + label(ax, v) + 'をはずす">' + AX[ax].name + '：' + label(ax, v) + '<i aria-hidden="true">×</i></button>'); }); });
    var total = n ? cnt(snap()) : 0;
    $('#sr-now').innerHTML = '<span class="sr-now-h">いま重なっている条件</span>' + (tags.join('') || '<span class="sr-tag is-none">まだありません</span>') + (n ? '<span class="sr-cnt">該当 <b>' + total + '</b> 件</span>' : '');
  }
  function turnHtml(t, i) {
    var last = i === turns.length - 1;
    var rows = last && t.rows.length ? '<div class="sr-steps"><p class="sr-steps-h">次の一歩</p>' + t.rows.map(function (r) { return '<div class="sr-step"><span>' + r[0] + '</span><div class="sr-picks">' + r[1] + '</div></div>'; }).join('') + '</div>' : '';
    return (t.you ? '<div class="sr-you"><p>' + esc(t.you) + '</p></div>' : '') +
      '<div class="sr-nfa' + (last ? ' is-last' : ' is-past') + '"><span class="sr-av" aria-hidden="true">NFA</span><div class="sr-bub"><div class="sr-a-h"><span class="en">AI ANSWER</span><span class="sr-demo">デモ版</span></div>' +
      t.msg + (t.docs.length ? '<div class="sr-lists">' + listHtml(t.docs, last) + '</div>' : '') + rows + '</div></div>';
  }
  function draw(animate) {
    syncSide();
    var mn = $('#sr-main'); if (mn) mn.classList.toggle('is-chat', turns.some(function (t) { return t.you; }) && turns.length > 3);
    thread.innerHTML = turns.map(turnHtml).join('');
    var last = thread.lastElementChild;
    if (animate && last && !RM) {
      var b = $('.sr-bub', last); b.classList.add('is-wait');
      var th = document.createElement('p'); th.className = 'sr-think'; th.innerHTML = '掲載中の資料・記事から回答を作成しています<i></i><i></i><i></i>';
      b.parentNode.insertBefore(th, b);
      setTimeout(function () { th.remove(); b.classList.remove('is-wait'); b.classList.add('is-in'); }, 650);
    }
    if (animate && turns.length > 1) {
      var you = thread.querySelectorAll('.sr-you'); var target = you[you.length - 1] || last;
      var y = target.getBoundingClientRect().top + scrollY - 150;
      window.lenis ? lenis.scrollTo(y) : scrollTo({ top: y, behavior: RM ? 'auto' : 'smooth' });
    }
  }

  /* ---------- 話す（自然文） / 選ぶ（タグ） ---------- */
  function say(text, fresh) {
    text = (text || '').trim(); if (!text) return;
    if (fresh) { C = { t: [], p: [], r: [], join: false }; turns = []; }
    var prev = snap(), f = toFacets(text);
    var got = f.t.length || f.p.length || f.r.length || f.join;
    ['t', 'p', 'r'].forEach(function (ax) { if (f[ax].length) C[ax] = f[ax].slice(0, 1); });   // 軸ごとに置き換え（矛盾させない）
    if (f.join) { C.join = true; C.p = []; } else if (f.p.length) C.join = false;
    var r = reply(prev, !got, text);
    turns.push({ you: text, msg: r.msg, docs: r.docs, rows: r.rows });
    try { history.replaceState(null, '', location.pathname + '?q=' + encodeURIComponent(turns[0].you)); } catch (e) {}
    draw(true);
  }
  function pick(ax, v, mode) {
    var prev = snap();
    if (mode === 'drop') C[ax] = C[ax].filter(function (x) { return x !== v; });
    else C[ax] = [v];
    if (ax === 'p') C.join = false;
    var r = reply(prev, false, '');
    turns.push({ you: utterance(ax, v, mode), msg: r.msg, docs: r.docs, rows: r.rows });
    draw(true);
  }
  function welcome() {
    C = { t: [], p: [], r: [], join: false };
    turns = [{ you: '', msg: '<p>こんにちは。長野県サッカー協会です。<br>大会の日程、試合結果、登録や講習会のことなど、知りたいことを<b>話しかけるように</b>入力してください。担当の委員会が分からなくても大丈夫です。</p>', docs: [], rows: [['たとえば', starters()]] }];
    draw(false);
  }

  /* ---------- 操作 ---------- */
  var form = $('#sr-form'), input = $('#sr-q'), cmp = $('#sr-cmp'), input2 = $('#sr-q2');
  function toThread() { var r = $('#result'); if (r) { var y = r.getBoundingClientRect().top + scrollY - 100; window.lenis ? lenis.scrollTo(y) : scrollTo({ top: y, behavior: RM ? 'auto' : 'smooth' }); } }
  if (form) form.addEventListener('submit', function (e) { e.preventDefault(); say(input.value, true); input.value = ''; toThread(); });
  if (cmp) cmp.addEventListener('submit', function (e) { e.preventDefault(); say(input2.value, !turns.some(function (t) { return t.you; })); input2.value = ''; });
  $$('.sr-chips [data-sq]').forEach(function (b) { b.addEventListener('click', function () { say(b.textContent, true); toThread(); }); });
  $$('.sr-ops .pill').forEach(function (b) { b.addEventListener('click', function () {
    var ax = b.getAttribute('data-ax'), v = b.getAttribute('data-v');
    pick(ax, v, C[ax].indexOf(v) > -1 ? 'drop' : (C[ax].length ? 'swap' : 'add'));
  }); });
  var clr = $('.sr-clr'); if (clr) clr.addEventListener('click', welcome);
  var rst = $('.sr-reset'); if (rst) rst.addEventListener('click', function () { welcome(); toThread(); });
  document.addEventListener('click', function (e) {
    if (!e.target.closest) return;
    var s1 = e.target.closest('[data-say]'); if (s1) { say(s1.getAttribute('data-say'), !turns.some(function (t) { return t.you; })); return; }
    var t = e.target.closest('.sr-pick[data-ax], .sr-tag[data-ax]'); if (t) { pick(t.getAttribute('data-ax'), t.getAttribute('data-v'), t.getAttribute('data-mode') || 'add'); return; }
    var a = e.target.closest('[data-all]'); if (a) expandAll();
  });
  function expandAll() {
    var t = turns[turns.length - 1], pdfs = t.docs.filter(function (d) { return d.k === 'pdf'; }), news = t.docs.filter(function (d) { return d.k === 'news'; });
    var h = '';
    if (pdfs.length) h += '<div class="sr-rel-h"><h3>該当する資料</h3><span>' + pdfs.length + '件・新しい順</span></div><ul class="dl sr-dl">' + pdfs.map(docLi).join('') + '</ul>';
    if (news.length) h += '<div class="sr-rel-h"><h3>該当する記事</h3><span>' + news.length + '件・新しい順</span></div><ul class="nl">' + news.map(newsLi).join('') + '</ul>';
    thread.lastElementChild.querySelector('.sr-lists').innerHTML = h;
  }

  // 起動：?q= があればその質問から会話を始める。なければ協会からのあいさつ
  var q0 = new URLSearchParams(location.search).get('q');
  welcome();
  if (q0) { say(q0, false); setTimeout(toThread, 400); }
})();
