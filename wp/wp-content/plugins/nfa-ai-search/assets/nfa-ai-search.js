/* ============================================================
   NFA AI Search — 探索画面（[nfa_ai_search]）
   ワイヤー NFA_wireframes_site/search.html の画面と擬似対話を移植。
   左＝条件（構造）、右＝会話（経過）の二分割。条件は軸ごとにひとつ、AND で累積する。
   探索ロジックはサーバー側（/wp-json/nfa/v1/search）にあり、ここでは描画と操作だけを行う。
   ============================================================ */
(function () {
  'use strict';

  var root = document.querySelector('[data-nfa-ais]');
  if (!root) return;
  var CFG;
  try { CFG = JSON.parse(root.getAttribute('data-nfa-ais')); } catch (e) { return; }
  if (!CFG || !CFG.api) return;

  var state = {
    cond: {},        // { target, purpose, area, keyword }
    total: 0,
    facets: [],
    busy: false,
    sideOff: false,
    sideOpen: false  // スマホで条件パネルを開いているか
  };

  var esc = function (s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  };
  var AXIS_ORDER = ['target', 'purpose', 'area'];

  /* ---------- API ---------- */
  var nonce = CFG.nonce;

  function refreshNonce() {
    return fetch(CFG.api + 'nonce', { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (d) { nonce = d.nonce; });
  }

  function callSearch(q, via, cond, retried) {
    var p = new URLSearchParams();
    p.set('q', q || '');
    p.set('via', via);
    Object.keys(cond).forEach(function (k) { if (cond[k]) p.set('cond[' + k + ']', cond[k]); });
    return fetch(CFG.api + 'search?' + p.toString(), {
      credentials: 'same-origin',
      headers: { 'X-NFA-Nonce': nonce }
    }).then(function (r) {
      if (r.status === 403 && !retried) {
        // ページキャッシュで nonce が古くなっていたら、取り直して1回だけやり直す
        return refreshNonce().then(function () { return callSearch(q, via, cond, true); });
      }
      return r.json().then(function (d) {
        if (!r.ok) {
          var err = new Error(d && d.message ? d.message : 'error');
          err.status = r.status;
          throw err;
        }
        return d;
      });
    });
  }

  /* ---------- 画面の骨組み（search.html と同じマークアップ・クラス名） ---------- */
  function shell() {
    root.innerHTML =
      (CFG.header ?
        '<section class="pHead"><div class="in">' +
          '<p class="crumb"><a href="' + esc(CFG.home) + '">ホーム</a> ／ ' + esc(CFG.title) + '</p>' +
          '<h1>' + esc(CFG.title) + '</h1>' +
          (CFG.description ? '<p>' + esc(CFG.description) + '</p>' : '') +
        '</div></section>' : '') +
      '<div class="stage"><div class="cols">' +
        '<aside class="side" data-side>' +
          '<div class="sideHd"><h2 data-sidehd>条件から絞り込む</h2><button type="button" data-clear>すべて解除</button></div>' +
          '<div id="facets" data-facets></div>' +
        '</aside>' +
        '<main>' +
          '<div class="condBar hide" data-condbar>' +
            '<span class="lb">いまの条件</span><span data-chips></span>' +
            '<span class="hits">該当 <b class="en" data-hits>0</b> 件</span>' +
            '<button type="button" class="sideTg" data-sidetg>条件パネルを隠す</button>' +
          '</div>' +
          '<div class="thread" data-thread>' +
            '<div class="entry" data-entry>' +
              '<h2>' + esc(CFG.lead) + '</h2>' +
              (CFG.sub ? '<p>' + esc(CFG.sub) + '</p>' : '') +
              '<div class="exs">' + (CFG.examples || []).map(function (e) {
                return '<button type="button" class="ex" data-ex="' + esc(e) + '">' + esc(e) + '</button>';
              }).join('') + '</div>' +
            '</div>' +
          '</div>' +
          '<div class="dock">' +
            '<div class="dockIn">' +
              '<input type="text" data-q maxlength="200" autocomplete="off" placeholder="' + esc(CFG.placeholder) + '" aria-label="知りたいことを入力">' +
              '<button type="button" class="send" data-send>探す</button>' +
            '</div>' +
            '<p class="dockNote">入力内容から条件を読み取ります。条件は左のパネルからも足せます。</p>' +
          '</div>' +
        '</main>' +
      '</div></div>';
  }

  /* ---------- 左パネル ---------- */
  function drawFacets() {
    var box = root.querySelector('[data-facets]');
    box.innerHTML = state.facets.map(function (ax) {
      return '<div class="facet"><h3>' + esc(ax.name) + '</h3>' +
        (ax.axis ? '<span class="axis">軸：' + esc(ax.axis) + '</span>' : '') +
        '<div class="opts">' + ax.options.map(function (o) {
          return '<button type="button" class="opt' + (o.on ? ' on' : '') + (o.dead ? ' dead' : '') + '"' +
            (o.dead ? ' disabled' : '') + ' aria-pressed="' + (o.on ? 'true' : 'false') + '"' +
            ' data-facet="' + esc(ax.key) + '" data-value="' + esc(o.value) + '">' +
            esc(o.value) + '<span class="n en">' + o.n + '</span></button>';
        }).join('') + '</div></div>';
    }).join('');
  }

  /* ---------- 条件バー ---------- */
  function hasCond() { return Object.keys(state.cond).length > 0; }

  function drawCond() {
    var has = hasCond();
    root.querySelector('[data-condbar]').classList.toggle('hide', !has);
    root.querySelector('[data-entry]').classList.toggle('hide', has);
    var keys = AXIS_ORDER.concat(['keyword']).filter(function (k) { return state.cond[k]; });
    root.querySelector('[data-chips]').innerHTML = keys.map(function (k) {
      return '<span class="chip">' + esc(k === 'keyword' ? '「' + state.cond[k] + '」' : state.cond[k]) +
        '<button type="button" data-remove="' + esc(k) + '" aria-label="解除">×</button></span>';
    }).join('');
    root.querySelector('[data-hits]').textContent = state.total;
  }

  /* ---------- 会話 ---------- */
  function optBtn(key, value, n) {
    return '<button type="button" class="opt" data-add="' + esc(key) + '" data-value="' + esc(value) + '">' + esc(value) +
      (typeof n === 'number' ? '<span class="n en">' + n + '</span>' : '') + '</button>';
  }

  function turnBody(res) {
    var h = '';
    if (res.rescue && res.rescue.length) {
      h += '<div class="rescue"><p>ひとつ条件を外すと、見つかるかもしれません。</p><div class="opts">' +
        res.rescue.map(function (r) {
          return '<button type="button" class="opt" data-remove="' + esc(r.key) + '">「' + esc(r.value) + '」を外す</button>';
        }).join('') + '</div></div>';
    }
    if (res.results && res.results.length) {
      h += '<div class="results">' + res.results.map(function (i) {
        return '<a class="res" href="' + esc(i.url) + '">' +
          '<span class="date en">' + esc(i.date) + '</span>' +
          '<span><span class="ttl">' + esc(i.title) + '</span><span class="meta">' +
          (i.meta || []).map(function (m) { return '<span>' + esc(m) + '</span>'; }).join('') +
          '</span></span><span class="go">詳細 →</span></a>';
      }).join('') + '</div>';
    }
    if (res.more) {
      h += '<div class="resMore"><a href="' + esc(res.more.url) + '">この条件の' + res.more.total + '件をすべて見る</a></div>';
    }
    if (res.next) {
      h += '<div class="nextUp"><p class="lb">さらに絞り込むなら（' + esc(res.next.name) + '）</p><div class="opts">' +
        res.next.options.map(function (o) { return optBtn(res.next.key, o.value, o.n); }).join('') + '</div></div>';
    }
    if (res.popular && res.popular.length) {
      h += '<div class="nextUp"><p class="lb">よく選ばれている条件</p><div class="opts">' +
        res.popular.map(function (v) { return optBtn('target', v); }).join('') + '</div></div>';
    }
    return h;
  }

  /* 1ターン：利用者の吹き出し → AIの返事 → 結果・次の一手 */
  function run(q, via, cond, said) {
    if (state.busy) return;
    state.busy = true;
    setInputDisabled(true);

    var t = document.createElement('div');
    t.className = 'turn';
    t.innerHTML = (said ? '<div class="bubble">' + esc(said) + '</div>' : '') +
      '<div class="ai"><div class="av en">AI</div>' +
      '<div class="say" aria-live="polite"><span class="nfa-ais-dots" aria-label="さがしています"><i></i><i></i><i></i></span></div></div>';
    root.querySelector('[data-thread]').appendChild(t);
    t.scrollIntoView({ behavior: 'smooth', block: 'start' });

    callSearch(q, via, cond).then(function (res) {
      state.cond = res.cond && !Array.isArray(res.cond) ? res.cond : {};
      state.total = res.total;
      state.facets = res.facets;
      /* res.say はサーバー側でエスケープ済み（<b> のみ含む） */
      t.querySelector('.say').innerHTML = res.say;
      t.insertAdjacentHTML('beforeend', turnBody(res));
      drawFacets(); drawCond();
    }).catch(function (e) {
      t.querySelector('.say').textContent = e.status === 429 ? e.message : 'うまくつながりませんでした。少し時間をおいて、もう一度お試しください。';
    }).then(function () {
      state.busy = false;
      setInputDisabled(false);
      t.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  }

  function sayOf(key, value) {
    var ax = (CFG.axes || []).filter(function (a) { return a.key === key; })[0];
    var o = ax && ax.options.filter(function (x) { return x.value === value; })[0];
    return (o && o.say) || value;
  }
  function addCond(key, value) {
    var c = Object.assign({}, state.cond); c[key] = value;
    run('', 'set', c, sayOf(key, value));
  }
  function removeCond(key) {
    var was = state.cond[key];
    if (!was) return;
    var c = Object.assign({}, state.cond); delete c[key];
    run('', 'set', c, '「' + was + '」は外してください');
  }
  function ask(text) {
    text = (text || '').trim();
    if (!text) return;
    run(text, 'text', state.cond, text);
  }
  function clearAll() {
    state.cond = {}; state.total = 0;
    shell();
    load();
  }
  function setInputDisabled(on) {
    root.querySelectorAll('[data-q], [data-send]').forEach(function (n) { n.disabled = !!on; });
  }

  /* ---------- 操作 ---------- */
  root.addEventListener('click', function (e) {
    var t = e.target, n;
    if (t.closest('[data-send]')) {
      var input = root.querySelector('[data-q]');
      var v = input.value;
      input.value = '';
      ask(v);
      return;
    }
    if ((n = t.closest('[data-ex]'))) { ask(n.getAttribute('data-ex')); return; }
    if ((n = t.closest('[data-facet]'))) {
      var key = n.getAttribute('data-facet'), val = n.getAttribute('data-value');
      if (state.cond[key] === val) removeCond(key); else addCond(key, val);
      return;
    }
    if ((n = t.closest('[data-add]'))) { addCond(n.getAttribute('data-add'), n.getAttribute('data-value')); return; }
    if ((n = t.closest('[data-remove]'))) { removeCond(n.getAttribute('data-remove')); return; }
    if (t.closest('[data-clear]')) { clearAll(); return; }
    if ((n = t.closest('[data-sidetg]'))) {
      state.sideOff = !state.sideOff;
      document.body.classList.toggle('sideOff', state.sideOff);   // search.html と同じく body に付ける
      n.textContent = state.sideOff ? '条件パネルを表示' : '条件パネルを隠す';
      return;
    }
    if (t.closest('[data-sidehd]')) {   // スマホでは条件パネルを開閉式にする
      state.sideOpen = !state.sideOpen;
      root.querySelector('[data-side]').classList.toggle('open', state.sideOpen);
    }
  });

  root.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter' || !e.target.matches('[data-q]')) return;
    if (e.isComposing || e.keyCode === 229) return;   // 日本語変換の確定Enterは無視
    e.preventDefault();
    var v = e.target.value;
    e.target.value = '';
    ask(v);
  });

  /* パネルの件数を読み込む（会話には何も足さない） */
  function load() {
    return callSearch('', 'set', {}).then(function (res) {
      state.facets = res.facets;
      drawFacets(); drawCond();
    }).catch(function () {});
  }

  /* ---------- 起動 ---------- */
  shell();
  // TOP などから ?q= 付きで来たら、その言葉ですぐ探す
  var params = new URLSearchParams(window.location.search);
  var initial = (params.get('q') || '').trim();
  if (initial) {
    params.delete('q');
    var qs = params.toString();
    window.history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : '') + window.location.hash);
  }
  load().then(function () { if (initial) ask(initial.slice(0, 200)); });
})();
