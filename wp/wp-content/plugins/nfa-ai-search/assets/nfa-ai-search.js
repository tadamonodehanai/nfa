/* =========================================================
   AI検索（対話型）— [nfa_ai_search]
   デザイン NFA_site_design/assets/js/search.js を WordPress 用に移植したもの。
   - 会話の組み立て（compose）・候補タグ（steps）・描画は search.js と同じ
   - 掲載データの絞り込み・件数・入力文の読み取りは、サーバー（/wp-json/nfa/v1/search）が行う
     （search.js の toFacets / find / cnt の置き換え。読み取りは APIキーがあれば Claude）
   - 掲載データは WordPress のお知らせ（投稿）と大会・リーグ資料（nfa_doc の行）
   ========================================================= */
(function () {
  'use strict';
  var app = document.querySelector('[data-nfa-ais]');
  if (!app) return;
  var CFG;
  try { CFG = JSON.parse(app.getAttribute('data-nfa-ais')); } catch (e) { return; }
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var RM = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- 語彙（サーバーの辞書と同じもの） ---------- */
  var AX = CFG.axes;   // { t: {name, items:[{v,l,short}]}, p: {...}, r: {...} }
  var PAGE = CFG.pages || {};   // 対象 → 委員会ページの URL
  var SHORT = {};
  AX.t.items.forEach(function (it) { SHORT[it.v] = it.short || it.l; });
  function label(ax, v) { if (ax === 'kw') return v; var it = AX[ax].items.filter(function (x) { return x.v === v; })[0]; return it ? it.l : v; }
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
  var U = CFG.urls || {};

  /* ---------- サーバーへの問い合わせ ---------- */
  var nonce = CFG.nonce;
  function refreshNonce() {
    return fetch(CFG.api + 'nonce', { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (d) { nonce = d.nonce; });
  }
  function api(q, via, c, retried) {
    var p = new URLSearchParams();
    p.set('q', q || ''); p.set('via', via);
    ['t', 'p', 'r'].forEach(function (ax) { if (c[ax][0]) p.set('c[' + ax + ']', c[ax][0]); });
    if (c.join) p.set('c[join]', '1');
    if (c.kw) p.set('c[kw]', c.kw);
    return fetch(CFG.api + 'search?' + p.toString(), { credentials: 'same-origin', headers: { 'X-NFA-Nonce': nonce } }).then(function (r) {
      if (r.status === 403 && !retried) return refreshNonce().then(function () { return api(q, via, c, true); });
      return r.json().then(function (d) { if (!r.ok) { var e = new Error(d && d.message || 'error'); e.status = r.status; throw e; } return d; });
    });
  }

  /* ---------- 回答文を組み立てる（search.js の compose と同じ。段階3で Claude の回答文に置き換える） ---------- */
  function who(st) { return st.t.length ? st.t.map(function (v) { return SHORT[v] || label('t', v); }).join('・') : ''; }
  function groups(docs) {
    var g = {}, order = [];
    docs.forEach(function (d) { if (d.k !== 'pdf') return; if (!g[d.g]) { g[d.g] = { name: d.g, n: 0, d: d.d }; order.push(d.g); } g[d.g].n++; if (d.d > g[d.g].d) g[d.g].d = d.d; });
    return order.map(function (k) { return g[k]; }).sort(function (a, b) { return a.d < b.d ? 1 : -1; });
  }
  function compose(st, docs, res) {
    var w = who(st), W = w ? w + 'の' : '', P = st.p[0], R = st.r.length ? st.r.join('・') : '';
    var pdfs = docs.filter(function (d) { return d.k === 'pdf'; }), news = docs.filter(function (d) { return d.k === 'news'; });
    var h = [];
    if (st.join) {
      h.push('<p>' + (w ? w + 'の' : '') + 'チームは「<a href="' + esc(U.teams) + '">チームを探す</a>」で、活動地域・活動競技・募集年代から探せます。' +
        (st.t.indexOf('cat-w') > -1 ? '女子の選手を募集しているチームも絞り込めます。' : '') + '</p>');
      h.push('<p>入団後の選手登録は、JFAの登録システム「KICKOFF」で行います。手順は「<a href="' + esc(U.register) + '">登録について</a>」にまとめています。</p>');
      return h.join('');
    }
    if (!docs.length) {
      var what0 = st.kw ? '「' + esc(st.kw) + '」' : W + (P ? label('p', P).replace(/を.*$|に.*$/, '') : '条件');
      return '<p>' + what0 + 'に当てはまる資料・記事は、いま公式サイトに掲載されていませんでした。条件をひとつ外すか、ことばを変えてお試しください。</p>' +
        '<div class="sr-picks">' + starters() + '</div>';
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
      if (st.t.indexOf('technical') > -1 && PAGE.technical) h.push('<p>指導者ライセンス（C級・D級・キッズリーダーなど）の内容と受講資格は、<a href="' + esc(PAGE.technical) + '">指導者のページ</a>にまとめています。</p>');
    } else if (P === 'event') {
      var e0 = news[0] || docs[0];
      h.push('<p>' + W + '参加募集・イベントの案内は' + (res ? res.nnews : news.length) + '件あります。いちばん新しいものは、' + e0.d + '「' + esc(e0.t) + '」です。</p>');
    } else if (P === 'reg') {
      h.push('<p>選手・チームの登録は、JFAの登録システム「KICKOFF」で行います。手順と関連資料は「<a href="' + esc(U.register) + '">登録について</a>」にまとめています。</p>');
      if (docs[0]) h.push('<p>' + W + '登録・申込に関する最新の掲載は、' + docs[0].d + '「' + esc(docs[0].g ? docs[0].g + ' ' + docs[0].t : docs[0].t) + '」です。</p>');
    } else {
      var top = docs.slice(0, 3);
      h.push('<p>' + W + (R ? R + 'の' : '') + '最新の掲載は次のとおりです。</p>');
      h.push('<ul class="sr-a-list">' + top.map(function (d) { return '<li><b>' + esc(d.g ? d.g + ' ' + d.t : d.t) + '</b><span>' + d.d + '</span></li>'; }).join('') + '</ul>');
    }
    if (st.t[0] && PAGE[st.t[0]]) h.push('<p>' + (SHORT[st.t[0]] || '') + 'の情報は、<a href="' + esc(PAGE[st.t[0]]) + '">' + (SHORT[st.t[0]] || '') + 'のページ</a>にもまとめています。</p>');
    return h.join('');
  }

  /* ---------- 対話の状態 ---------- */
  var C = { t: [], p: [], r: [], join: false, kw: '' };   // いま重なっている条件
  var N = null;     // 直近の件数（サーバーの counts。軸ごと・選択肢ごと）
  var TOTAL = 0;    // いまの条件の件数
  var turns = [];
  var busy = false;
  var thread = $('#sr-thread', app);
  var VOICE = {
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
  function snap() { return { t: C.t.slice(), p: C.p.slice(), r: C.r.slice(), join: C.join, kw: C.kw }; }
  function cnt(ax, v) { return N && N[ax] && typeof N[ax][v] === 'number' ? N[ax][v] : 0; }

  /* 条件の変化を、ひとことで伝える */
  function diffNote(prev) {
    var add = [], rep = [], rm = [];
    ['t', 'p', 'r'].forEach(function (ax) {
      var a = prev[ax][0], b = C[ax][0];
      if (!a && b) add.push(label(ax, b));
      else if (a && !b) rm.push(label(ax, a));
      else if (a && b && a !== b) rep.push('「' + label(ax, a) + '」を「' + label(ax, b) + '」に');
    });
    if (prev.kw && !C.kw) rm.push(prev.kw);
    var n = [];
    if (rep.length) n.push(rep.join('、') + '替えました。');
    if (add.length && turns.length) n.push('「' + add.join('」「') + '」で絞り込みました。');
    if (rm.length) n.push('「' + rm.join('」「') + '」の条件をはずしました。');
    return n.join('');
  }

  /* 次の一歩（候補タグ）：0件になるものは出さない */
  function steps(docs) {
    var rows = [];
    function chips(ax, mode, items) {
      return items.map(function (it) {
        var n = cnt(ax, it.v); if (!n || (n === docs.length && mode !== 'swap' && C[ax].length)) return '';
        return '<button type="button" class="sr-pick" data-ax="' + ax + '" data-v="' + esc(it.v) + '" data-mode="' + mode + '"><span class="hash">#</span>' + esc(ax === 't' ? (SHORT[it.v] || it.l) : it.l) + '<small>（' + n + '件）</small></button>';
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

  function listHtml(docs, full, res) {
    var pdfs = docs.filter(function (d) { return d.k === 'pdf'; }), news = docs.filter(function (d) { return d.k === 'news'; });
    var np = full ? 6 : 3, nn = full ? 4 : 2, h = '';
    if (pdfs.length) h += '<div class="sr-rel-h"><h3>該当する資料</h3><span>' + pdfs.length + '件・新しい順</span></div><ul class="dl sr-dl">' + pdfs.slice(0, np).map(docLi).join('') + '</ul>';
    if (news.length) h += '<div class="sr-rel-h"><h3>該当する記事</h3><span>' + news.length + '件・新しい順</span></div><ul class="nl">' + news.slice(0, nn).map(newsLi).join('') + '</ul>';
    var rest = Math.max(0, pdfs.length - np) + Math.max(0, news.length - nn);
    if (full && rest) h += '<button type="button" class="sr-all" data-all>残り ' + rest + ' 件もすべて表示</button>';
    return h;
  }
  function docLi(d) {
    return '<li><a href="' + esc(d.u) + '" target="_blank" rel="noopener"><span class="fbtn-i">' + esc(d.f || 'PDF') + '</span><span class="sr-dn">' + esc(d.g) + '<br><b>' + esc(d.t) + '</b></span><small>' + esc(d.d) + '</small></a></li>';
  }
  function newsLi(d) {
    return '<li><a href="' + esc(d.u) + '"><span class="th"><img src="' + esc(d.img || CFG.placeholder) + '" alt="" loading="lazy"><span class="badge" style="background:' + esc(d.col || 'var(--navy)') + '">' + esc(d.l) + '</span></span><span><span class="mt"><span class="dt">' + esc(d.d) + '</span></span><h3>' + esc(d.t) + '</h3></span><span class="ar" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a></li>';
  }

  /* 1ターン分の返事をつくる（search.js の reply と同じ。件数・絞り込みはサーバーの結果を使う） */
  function reply(prev, res, raw) {
    if (res.unread) {
      return { msg: '<p>すみません、「' + esc(raw) + '」がうまく汲み取れませんでした。<b>どなたの</b>ことか、<b>何を</b>知りたいかを教えていただけると探せます。たとえば「小学生の大会はいつ？」「審判の資格を更新したい」のように。</p>', docs: [], rows: [['まずはここから', starters()]] };
    }
    var st = res.st, docs = res.docs, note = diffNote(prev), relaxed = res.relaxed ? label(res.relaxed, C[res.relaxed][0]) : '';
    if (!C.t.length && !C.p.length && !C.r.length && !C.kw && !C.join) {
      return { msg: '<p>条件をすべてはずしました。あらためて、ことばでもタグからでもお探しください。</p>', docs: [], rows: [['まずはここから', starters()]] };
    }
    var ask = !C.t.length && !C.join && /schedule|result/.test(C.p[0] || '') && !C.r.length;
    var msg = note ? '<p class="sr-note">' + note + '</p>' : '';
    if (ask) msg += '<p>' + label('p', C.p[0]).replace(/を.*$|に.*$/, '') + 'ですね。<b>どの年代・種目の情報ですか？</b>選ぶと絞り込みます。</p>';
    else {
      if (relaxed) msg += '<p>「' + esc(relaxed) + '」に当てはまる掲載は、いまはありませんでした。条件をゆるめて、近い情報をご案内します。</p>';
      msg += compose(st, docs, res);
    }
    return { msg: msg, docs: ask ? [] : docs, rows: steps(docs), lead: !ask && docs.length > 0 };   // 聞き返すときは一覧を出さず、選択肢を先に見せる
  }
  function starters() {
    return $$('.sr-chips [data-sq]', app).map(function (b) { var t = b.textContent.replace(/^#/, ''); return '<button type="button" class="sr-pick" data-say="' + esc(t) + '"><span class="hash">#</span>' + esc(t) + '</button>'; }).join('');
  }

  /* ---------- 描画 ---------- */
  function syncSide() {
    $$('.sr-ops .pill', app).forEach(function (b) {
      var ax = b.getAttribute('data-ax'), v = b.getAttribute('data-v'), on = C[ax].indexOf(v) > -1;
      b.setAttribute('aria-pressed', on);
      if (on) { b.disabled = false; return; }
      b.disabled = N ? cnt(ax, v) === 0 : false;   // 該当する情報がない条件は選べない
    });
    var n = C.t.length + C.p.length + C.r.length + (C.kw ? 1 : 0), sel = $('#sr-sel', app); if (sel) sel.textContent = n + '件 選択中';
    var tags = [];
    ['t', 'p', 'r'].forEach(function (ax) { C[ax].forEach(function (v) { tags.push('<button type="button" class="sr-tag" data-ax="' + ax + '" data-v="' + esc(v) + '" data-mode="drop" aria-label="' + AX[ax].name + '：' + esc(label(ax, v)) + 'をはずす"><span class="hash">#</span>' + esc(label(ax, v)) + '<i aria-hidden="true">×</i></button>'); }); });
    if (C.kw) tags.push('<button type="button" class="sr-tag" data-ax="kw" data-v="' + esc(C.kw) + '" data-mode="drop" aria-label="ことば：' + esc(C.kw) + 'をはずす"><span class="hash">#</span>「' + esc(C.kw) + '」<i aria-hidden="true">×</i></button>');
    $('#sr-now', app).innerHTML = '<div class="sr-now-top"><span class="sr-now-h">いまの条件</span>' + (n ? '<span class="sr-cnt">該当 <b>' + TOTAL + '</b> 件</span>' : '') + '</div>' +
      '<div class="sr-now-tags">' + (tags.join('') || '<span class="sr-tag is-none">まだありません</span>') + '</div>' +
      (n ? '<button type="button" class="sr-clr">条件をクリア</button>' : '');
  }
  /* 最初の一文のすぐ後に「いちばん近い資料・記事」のカードを置く */
  function splitLead(msg, card) {
    if (!card) return msg;
    var m = msg.match(/^((?:<p class="sr-note">.*?<\/p>)?<p>.*?<\/p>)/);
    return m ? m[1] + card + msg.slice(m[1].length) : card + msg;
  }
  function turnHtml(t, i) {
    var last = i === turns.length - 1;
    var rel = last && t.rows.length ? '<div class="sr-steps"><p class="sr-steps-h">' + (t.docs.length ? '関連のご案内' : '次の一歩') + '</p>' + t.rows.map(function (r) { return '<div class="sr-step"><span>' + r[0] + '</span><div class="sr-picks">' + r[1] + '</div></div>'; }).join('') + '</div>' : '';
    var top = t.docs[0], card = '';
    if (top && t.lead) {
      card = '<div class="sr-card"><h3>' + esc(top.g ? top.g : top.t) + (top.g ? '<small>' + esc(top.t) + '</small>' : '') + '</h3>' +
        '<p class="sr-card-src">出典：' + esc(top.k === 'pdf' ? '大会・リーグの資料（' + (top.f || 'PDF') + '）' : (top.l || 'お知らせ')) + '　・　' + esc(top.d) + ' 更新</p>' +
        (top.x ? '<p class="sr-card-x">' + esc(top.x) + '</p>' : '') +
        '<a class="btn btn-g" href="' + esc(top.u) + '"' + (top.k === 'pdf' ? ' target="_blank" rel="noopener"' : '') + '><span>' + (top.k === 'pdf' ? 'この資料を開く' : 'この記事を開く') + '</span><span class="arr" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a></div>';
    }
    return (t.you ? '<div class="sr-you"><p>' + esc(t.you) + '</p></div>' : '') +
      '<div class="sr-nfa' + (last ? ' is-last' : ' is-past') + '"><span class="sr-av" aria-hidden="true">N</span><div class="sr-bub">' +
      splitLead(t.msg, card) + (t.docs.length > 1 ? '<div class="sr-lists">' + listHtml(t.docs.slice(t.lead ? 1 : 0), last, t.res) + '</div>' : '') + rel + '</div></div>';
  }
  function scrollToY(y) { window.lenis ? window.lenis.scrollTo(y) : scrollTo({ top: y, behavior: RM ? 'auto' : 'smooth' }); }
  function draw(animate) {
    syncSide();
    var chat = turns.length > 0;
    app.classList.toggle('is-chat', chat); app.classList.toggle('is-entry', !chat);
    thread.innerHTML = turns.map(turnHtml).join('');
    var last = thread.lastElementChild;
    if (animate && last && !RM) { var b = $('.sr-bub', last); if (b) { b.classList.add('is-in'); } }
    if (animate && turns.length > 1) {
      var you = thread.querySelectorAll('.sr-you'); var target = you[you.length - 1] || last;
      scrollToY(target.getBoundingClientRect().top + scrollY - 150);
    }
  }
  /* 返事を待つあいだ：利用者の吹き出しと「回答を作成しています」を先に出す */
  function waiting(you) {
    var chat = true;
    app.classList.toggle('is-chat', chat); app.classList.toggle('is-entry', !chat);
    thread.innerHTML = turns.map(turnHtml).join('') +
      (you ? '<div class="sr-you"><p>' + esc(you) + '</p></div>' : '') +
      '<div class="sr-nfa is-last"><span class="sr-av" aria-hidden="true">N</span><p class="sr-think">掲載中の資料・記事から回答を作成しています<i></i><i></i><i></i></p></div>';
  }

  /* ---------- 話す（自然文） / 選ぶ（タグ） ---------- */
  function ask(q, via, next, you, raw) {
    if (busy) return;
    busy = true;
    var prev = snap();
    waiting(you);
    api(q, via, next).then(function (res) {
      C = { t: res.c.t, p: res.c.p, r: res.c.r, join: !!res.c.join, kw: res.c.kw || '' };
      N = res.counts; TOTAL = res.total;
      var r = reply(prev, res, raw);
      turns.push({ you: you, msg: r.msg, docs: r.docs, rows: r.rows, lead: r.lead, res: res });
    }).catch(function (e) {
      turns.push({ you: you, msg: '<p>' + esc(e.status === 429 ? e.message : 'うまくつながりませんでした。少し時間をおいて、もう一度お試しください。') + '</p>', docs: [], rows: [] });
    }).then(function () {
      busy = false;
      draw(true);
    });
  }
  function say(text, fresh) {
    text = (text || '').trim(); if (!text) return;
    if (fresh) { C = { t: [], p: [], r: [], join: false, kw: '' }; turns = []; }
    try { history.replaceState(null, '', location.pathname + '?q=' + encodeURIComponent(turns.length ? turns[0].you : text)); } catch (e) {}
    ask(text, 'text', snap(), text, text);
  }
  function pick(ax, v, mode) {
    var next = snap();
    if (ax === 'kw') next.kw = '';
    else if (mode === 'drop') next[ax] = next[ax].filter(function (x) { return x !== v; });
    else next[ax] = [v];
    if (ax === 'p') next.join = false;
    ask('', 'set', next, utterance(ax, v, mode), '');
  }
  function welcome() {   // 入口（チャット投稿フォーム）に戻す
    C = { t: [], p: [], r: [], join: false, kw: '' };
    turns = [];
    try { history.replaceState(null, '', location.pathname); } catch (e) {}
    draw(false);
  }
  /* 条件パネルの件数だけを先に読み込む（会話には何も足さない） */
  function loadCounts() {
    return api('', 'set', snap()).then(function (res) { N = res.counts; syncSide(); }).catch(function () {});
  }

  /* ---------- 操作 ---------- */
  var form = $('#sr-form', app), input = $('#sr-q', app), cmp = $('#sr-cmp', app), input2 = $('#sr-q2', app);
  function toThread() { var y = app.getBoundingClientRect().top + scrollY - 100; scrollToY(y); }
  if (form) form.addEventListener('submit', function (e) { e.preventDefault(); say(input.value, true); input.value = ''; toThread(); });
  if (cmp) cmp.addEventListener('submit', function (e) { e.preventDefault(); say(input2.value, !turns.length); input2.value = ''; });
  $$('.sr-chips [data-sq]', app).forEach(function (b) { b.addEventListener('click', function () { say(b.textContent.replace(/^#/, ''), true); toThread(); }); });
  $$('.sr-ops .pill', app).forEach(function (b) { b.addEventListener('click', function () {
    var ax = b.getAttribute('data-ax'), v = b.getAttribute('data-v');
    pick(ax, v, C[ax].indexOf(v) > -1 ? 'drop' : (C[ax].length ? 'swap' : 'add'));
  }); });
  var rst = $('.sr-reset', app); if (rst) rst.addEventListener('click', function () { welcome(); toThread(); });
  app.addEventListener('click', function (e) {
    if (!e.target.closest) return;
    if (e.target.closest('.sr-clr')) { welcome(); toThread(); return; }
    var s1 = e.target.closest('[data-say]'); if (s1) { say(s1.getAttribute('data-say'), !turns.length); return; }
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

  // 起動：?q= があればその質問から会話を始める。なければ入口を出し、条件パネルの件数だけ読み込む
  var q0 = new URLSearchParams(location.search).get('q');
  welcome();
  if (q0) { say(q0.slice(0, 200), false); setTimeout(toThread, 400); } else { loadCounts(); }
})();
