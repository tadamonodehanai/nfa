/* =========================================================
   NFA site — main.js
   全ページ共通。要素があるときだけ各機能が動く（WordPressの各テンプレートで共用）
   依存：GSAP 3.12.5 / ScrollTrigger（任意。無ければ静止状態で表示）
   ========================================================= */
(function(){
  'use strict';
  var d = document, root = d.documentElement;
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hasG = typeof window.gsap !== 'undefined';
  var hasST = hasG && typeof window.ScrollTrigger !== 'undefined';
  if (hasST) gsap.registerPlugin(ScrollTrigger);
  var $ = function(s, c){ return (c||d).querySelector(s); };
  var $$ = function(s, c){ return [].slice.call((c||d).querySelectorAll(s)); };
  var VIDEO_BASE = root.getAttribute('data-video-base') || 'assets/video/';

  /* ---------- ヘッダー高さ（追従要素の基準） ---------- */
  var hd = $('.hd');
  function hdh(){ return hd ? hd.offsetHeight : 0; }
  function setH(){ root.style.setProperty('--hdh', hdh()+'px'); }
  setH(); window.addEventListener('resize', setH);

  /* ---------- SPドロワー（JFA型：上から開くアコーディオン） ---------- */
  var mb = $('.menu-btn'), dr = $('#drawer');
  if (mb && dr){
    function setOpen(o){
      dr.hidden = false;
      requestAnimationFrame(function(){ dr.classList.toggle('is-open', o); });
      mb.setAttribute('aria-expanded', o); mb.classList.toggle('is-open', o);
      root.classList.toggle('no-scroll', o);
      if (!o) setTimeout(function(){ if(!dr.classList.contains('is-open')) dr.hidden = true; }, 300);
    }
    mb.addEventListener('click', function(){ setOpen(mb.getAttribute('aria-expanded') !== 'true'); });
    $$('a', dr).forEach(function(a){ a.addEventListener('click', function(){ setOpen(false); }); });
    d.addEventListener('keydown', function(e){ if(e.key === 'Escape' && mb.getAttribute('aria-expanded') === 'true') setOpen(false); });
  }
  /* アコーディオン（ドロワー・フッター・FAQ・地区リーグ） */
  $$('[data-acc]').forEach(function(b){
    var p = d.getElementById(b.getAttribute('aria-controls'));
    if (!p) return;
    b.addEventListener('click', function(){
      var o = b.getAttribute('aria-expanded') !== 'true';
      b.setAttribute('aria-expanded', o); p.hidden = !o;
    });
  });

  /* ---------- PCメガメニュー（カテゴリ） ---------- */
  $$('.nav [data-mega]').forEach(function(li){
    var btn = $('button', li), panel = $('.mega', li), t;
    if (!btn || !panel) return;
    function open(o){ btn.setAttribute('aria-expanded', o); panel.hidden = !o; }
    btn.addEventListener('click', function(){ open(btn.getAttribute('aria-expanded') !== 'true'); });
    li.addEventListener('mouseenter', function(){ clearTimeout(t); open(true); });
    li.addEventListener('mouseleave', function(){ t = setTimeout(function(){ open(false); }, 120); });
    d.addEventListener('keydown', function(e){ if(e.key === 'Escape') open(false); });
  });

  /* ---------- 横スクロール（日程スライダー）矢印 ---------- */
  $$('[data-scroller]').forEach(function(w){
    var list = $('.sched-list', w), prev = $('.sc-prev', w), next = $('.sc-next', w);
    if (!list) return;
    function step(){ var c = $('.sch', list); return c ? c.offsetWidth : 240; }
    list.scrollLeft = 0;
    var fit = function(){ var can = list.scrollWidth > list.clientWidth + 4; w.classList.toggle('no-scroll-x', !can); };
    fit(); window.addEventListener('resize', fit);
    if ($$('.sch', list).length <= 3) w.classList.add('is-few');
    if (prev) prev.addEventListener('click', function(){ list.scrollBy({left:-step(), behavior:'smooth'}); });
    if (next) next.addEventListener('click', function(){ list.scrollBy({left: step(), behavior:'smooth'}); });
  });

  /* ---------- タブ絞り込み（NEWS・一覧） ---------- */
  $$('[data-filter]').forEach(function(group){
    var target = d.getElementById(group.getAttribute('data-filter'));
    if (!target) return;
    var tabs = $$('[data-f]', group), items = $$('[data-c]', target);
    tabs.forEach(function(t){ t.addEventListener('click', function(){
      tabs.forEach(function(x){ x.setAttribute('aria-selected', x === t); });
      items.forEach(function(c){ c.hidden = !(t.dataset.f === 'all' || (' '+c.dataset.c+' ').indexOf(' '+t.dataset.f+' ') > -1); });
      /* カテゴリタブ：選んだカテゴリのページへのリンクを出す */
      var sec = group.closest('section'), go = sec && $('.sched-go', sec);
      if (go){ if (t.dataset.go){ go.href = t.dataset.go; $('b span', go).textContent = t.dataset.goLabel; go.hidden = false; } else { go.hidden = true; } }
      target.scrollLeft = 0;
    }); });
  });

  /* ---------- フォーム（デザイン確認用：送信しない） ---------- */
  $$('form[data-demo]').forEach(function(f){ f.addEventListener('submit', function(e){ e.preventDefault(); }); });
  /* TOPのAI検索：ボタンを押すとその対象の質問として検索ページへ */
  $$('.chips button[data-q]').forEach(function(b){ b.addEventListener('click', function(){ location.href = 'search.html?q=' + encodeURIComponent(b.textContent.replace(/（.*?）/,'') + 'の最新情報'); }); });

  /* ---------- 入場の幕（TOPのみ） ---------- */
  var cur = $('.curtain');
  if (cur){
    if (hasG && !reduce){
      gsap.timeline()
        .from('.curtain b', {opacity:0, y:12, duration:.4})
        .to('.curtain b', {opacity:0, duration:.25}, '+=.25')
        .to('.curtain i', {xPercent:120, duration:1, ease:'power3.inOut'}, '-=.1')
        .from('.hero h1', {y:24, opacity:0, duration:.7, ease:'power2.out'}, '-=.55')
        .set(cur, {display:'none'});
    } else { cur.style.display = 'none'; }
  }

  /* ---------- ヒーローカルーセル（TOPのみ） ---------- */
  var slides = $$('.hero .slide');
  if (slides.length > 1){
    var dots = $$('.dots button'), cap = d.getElementById('heroCap'), cnt = d.getElementById('cnt');
    var idx = 0, timer = null, DUR = 6;
    var bar = function(i){
      dots.forEach(function(b,k){ var x = $('i', b); if(hasG){ gsap.killTweensOf(x); gsap.set(x, {scaleX:k<i?1:0}); } else { x.style.transform = 'scaleX('+(k<=i?1:0)+')'; } b.setAttribute('aria-selected', k===i); });
      if (hasG && !reduce && dots[i]) gsap.to($('i', dots[i]), {scaleX:1, duration:DUR, ease:'none'});
    };
    var restart = function(){ clearTimeout(timer); if(!reduce) timer = setTimeout(function(){ go(idx+1); }, DUR*1000); };
    var go = function(n){
      var prev = slides[idx]; idx = (n+slides.length)%slides.length; var nx = slides[idx];
      if (prev === nx) return;
      nx.style.zIndex = 3; prev.style.zIndex = 2;
      if (hasG && !reduce){
        gsap.fromTo(nx, {clipPath:'inset(0 0 0 100%)'}, {clipPath:'inset(0 0 0 0%)', duration:1.1, ease:'power3.inOut', onComplete:function(){ prev.classList.remove('is-on'); prev.style.clipPath='inset(0 0 0 100%)'; prev.style.zIndex=''; nx.style.zIndex=2; }});
        gsap.fromTo($('img', nx), {scale:1.18}, {scale:1.04, duration:DUR+1.2, ease:'none'});
      } else { prev.classList.remove('is-on'); prev.style.clipPath='inset(0 0 0 100%)'; nx.style.clipPath='inset(0 0 0 0)'; }
      nx.classList.add('is-on');
      if (cap){ cap.innerHTML = '<b>'+nx.dataset.cap+'</b><span>'+nx.dataset.txt+'</span>'; if (nx.dataset.href) cap.href = nx.dataset.href; }
      if (cnt) cnt.textContent = ('0'+(idx+1)).slice(-2);
      bar(idx); restart();
    };
    dots.forEach(function(b,k){ b.addEventListener('click', function(){ go(k); }); });
    if (hasG && !reduce) gsap.fromTo($('img', slides[0]), {scale:1.18}, {scale:1.04, duration:DUR+1.2, ease:'none'});
    bar(0); restart();
  }

  /* ---------- 映像：イメージとして扱う（ゆっくり再生） ---------- */
  $$('video').forEach(function(v){ var r = function(){ v.playbackRate = 0.75; }; v.addEventListener('loadedmetadata', r); v.addEventListener('play', r); });

  /* ---------- 映像：イメージとして無音で流す ---------- */
  var vv = d.getElementById('vmVideo');
  if (vv){
    var list = ['mv1','mv2','mv3','mv4'], vi = 0;
    vv.addEventListener('ended', function(){ vi = (vi+1)%list.length; vv.src = VIDEO_BASE+list[vi]+'.mp4'; vv.play().catch(function(){}); });
    if ('IntersectionObserver' in window) new IntersectionObserver(function(es){ es.forEach(function(e){ try{ e.isIntersecting ? vv.play() : vv.pause(); }catch(x){} }); }, {threshold:.1}).observe(vv);
  }
  if ('IntersectionObserver' in window){
    var io = new IntersectionObserver(function(es){ es.forEach(function(e){
      var v = e.target;
      if (e.isIntersecting){ if(!v.getAttribute('src')) v.src = v.dataset.src; v.play().catch(function(){}); } else { v.pause(); }
    }); }, {threshold:.3});
    $$('video[data-src]').forEach(function(v){ io.observe(v); });
  }

  if (!hasST || reduce) return;

  /* ---------- ビジョン：全面の映像がスクロールで縮む ---------- */
  if ($('#vmPin')){
    var mm = gsap.matchMedia();
    var vmTl = function(endClip, dx, dy){
      return gsap.timeline({scrollTrigger:{trigger:'#vmPin', start:'top top+=' + hdh(), end:'bottom bottom', scrub:.6}})
        .fromTo('#vm', {clipPath:'inset(0% 0% 0% 0%)'}, {clipPath:endClip, ease:'power2.inOut', duration:1})
        .fromTo('#vmMedia', {xPercent:0, yPercent:0, x:0, y:0}, {xPercent:dx, yPercent:dy, ease:'power2.inOut', duration:1}, 0)
        .fromTo('#vmVideo', {scale:1.12}, {scale:1, ease:'none', duration:1}, 0)
        .fromTo('#vmCopy > *', {y:30, opacity:0}, {y:0, opacity:1, stagger:.08, duration:.4, ease:'power2.out'}, .55)
        .to({}, {duration:.45});
    };
    mm.add('(min-width:961px)', function(){ vmTl('inset(12% 5% 12% 46%)', 20.5, 0); });
    mm.add('(max-width:960px)', function(){ vmTl('inset(52% 4% 5% 4%)', 0, 23.5); });
  }

  /* ---------- 下層ヒーロー：画像がスクロールで縮む（参考サイトの縮小ギミック） ---------- */
  $$('.ph-hero .ph-img').forEach(function(el){
    gsap.fromTo(el, {clipPath:'inset(0% 0% 0% 0%)'}, {clipPath:'inset(0% 4% 0% 4%)', ease:'none', scrollTrigger:{trigger:el, start:'top top+='+hdh(), end:'bottom top', scrub:true}});
    var im = $('img', el); if (im) gsap.fromTo(im, {scale:1.12}, {scale:1, ease:'none', scrollTrigger:{trigger:el, start:'top bottom', end:'bottom top', scrub:true}});
  });

  /* ---------- 画像：少し内側から外へ開く ---------- */
  $$('.pk, .gt, .nc .th, .cc .th, .pl .mv, .part .ph, .rv-img').forEach(function(el){
    gsap.fromTo(el, {clipPath:'inset(8% 6% 8% 6%)'}, {clipPath:'inset(0% 0% 0% 0%)', duration:1.1, ease:'power3.out', clearProps:'clipPath', scrollTrigger:{trigger:el, start:'top 88%', once:true}});
  });
  /* ---------- パララックス ---------- */
  $$('.gt .ph img').forEach(function(el){ gsap.fromTo(el, {yPercent:-8}, {yPercent:0, ease:'none', scrollTrigger:{trigger:el.parentNode, start:'top bottom', end:'bottom top', scrub:true}}); });
  if ($('.part .px')) gsap.fromTo('.part .px', {yPercent:-8}, {yPercent:0, ease:'none', scrollTrigger:{trigger:'.part', start:'top bottom', end:'bottom top', scrub:true}});
  $$('.bgword').forEach(function(el){ gsap.fromTo(el, {xPercent:10}, {xPercent:-10, ease:'none', scrollTrigger:{trigger:el.parentNode, start:'top bottom', end:'bottom top', scrub:true}}); });
})();

/* =========================================================
   MOTION v2 — スムーススクロール／ページ遷移／見出しの文字送り／要素の段階表示
   ========================================================= */
(function(){
  'use strict';
  var d = document, root = d.documentElement;
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hasG = typeof window.gsap !== 'undefined', hasST = hasG && typeof window.ScrollTrigger !== 'undefined';
  var $$ = function(s, c){ return [].slice.call((c||d).querySelectorAll(s)); };
  var hd = d.querySelector('.hd');
  var off = function(){ return (hd ? hd.offsetHeight : 0) + 56; };

  /* 1. スムーススクロール（Lenis） */
  var lenis = null;
  if (!reduce && window.Lenis){
    lenis = new Lenis({lerp:.11, smoothWheel:true, wheelMultiplier:.95});
    if (hasST){ lenis.on('scroll', ScrollTrigger.update); gsap.ticker.add(function(t){ lenis.raf(t*1000); }); gsap.ticker.lagSmoothing(0); }
    else { var raf = function(t){ lenis.raf(t); requestAnimationFrame(raf); }; requestAnimationFrame(raf); }
    $$('a[href^="#"]').forEach(function(a){ a.addEventListener('click', function(e){
      var id = a.getAttribute('href'); if (id.length < 2) return;
      var t = d.querySelector(id); if (!t) return;
      e.preventDefault(); lenis.scrollTo(t, {offset:-off(), duration:1.1});
    }); });
    new MutationObserver(function(){ root.classList.contains('no-scroll') ? lenis.stop() : lenis.start(); }).observe(root, {attributes:true, attributeFilter:['class']});
  }

  /* 2. ページ遷移：濃紺のパネルで覆ってから移動 */
  var pt = d.querySelector('.pt');
  if (pt && !reduce){
    d.addEventListener('click', function(e){
      var a = e.target.closest && e.target.closest('a'); if (!a) return;
      var h = a.getAttribute('href') || '';
      if (!h || h.charAt(0) === '#' || a.target === '_blank' || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      if (/^(https?:|mailto:|tel:)/.test(h) || !/\.html/.test(h)) return;
      var cur = location.pathname.split('/').pop() || 'index.html';
      if (h.split('#')[0] === cur) return;
      e.preventDefault();
      pt.classList.add('is-leave');
      setTimeout(function(){ location.href = h; }, 560);
    });
    window.addEventListener('pageshow', function(e){ if (e.persisted) pt.classList.remove('is-leave'); });
  }

  /* ページ内メニュー：現在のセクションを表示 */
  var lnLinks = $$('.lnav a[href^="#"]');
  if (lnLinks.length && 'IntersectionObserver' in window){
    var map = {}; lnLinks.forEach(function(a){ var t = d.querySelector(a.getAttribute('href')); if (t) map[t.id] = a; });
    var io2 = new IntersectionObserver(function(es){ es.forEach(function(e){ if (e.isIntersecting){ lnLinks.forEach(function(x){ x.removeAttribute('aria-current'); }); var a = map[e.target.id]; if (a){ a.setAttribute('aria-current','true'); var ul = a.closest('ul'); if (ul) ul.scrollTo({left:a.parentNode.offsetLeft - 16, behavior:'smooth'}); } } }); }, {rootMargin:'-40% 0px -55% 0px'});
    Object.keys(map).forEach(function(id){ io2.observe(d.getElementById(id)); });
  }

  /* スクロール進捗 */
  var sp = d.querySelector('.sprog');
  if (sp){ var upd = function(){ var m = root.scrollHeight - innerHeight; sp.style.transform = 'scaleX(' + (m > 0 ? scrollY / m : 0) + ')'; }; addEventListener('scroll', upd, {passive:true}); upd(); }

  if (!hasST || reduce) return;

  /* 3. 英字見出し：1文字ずつ下から立ち上がる */
  $$('.bh .e, .sh .e').forEach(function(el){
    var chars = [];
    (function walk(node){
      [].slice.call(node.childNodes).forEach(function(n){
        if (n.nodeType === 3){
          var frag = d.createDocumentFragment();
          n.textContent.split('').forEach(function(ch){
            var o = d.createElement('span'); o.className = 'chw';
            var c = d.createElement('span'); c.className = 'ch'; c.textContent = ch === ' ' ? ' ' : ch;
            o.appendChild(c); frag.appendChild(o); chars.push(c);
          });
          node.replaceChild(frag, n);
        } else if (n.nodeType === 1) walk(n);
      });
    })(el);
    el.setAttribute('aria-label', el.textContent.replace(/ /g,' '));
    gsap.from(chars, {yPercent:110, duration:.9, ease:'expo.out', stagger:.028, scrollTrigger:{trigger:el, start:'top 92%', once:true}});
  });

  /* 4. 要素：まとまりごとに少しずつ表示 */
  var sel = '.cards > *, .trn-grid > *, .nl > li, .catgrid > .ct, .tm > li, .steps > li, .pillars3 > li, .kpi > div, .dl > li, .ngrid > .nc, .cgrid > .cc, .pillars > .pl, .acc > .acc-i, .lnk > a, .gates > .gt, .org > div';
  var items = $$(sel).filter(function(el){ return !el.closest('#sr-thread'); });   /* AI検索の会話は search.js 側で表示 */
  if (items.length){
    gsap.set(items, {y:36, opacity:0});
    ScrollTrigger.batch(items, {start:'top 92%', once:true, onEnter:function(b){ gsap.to(b, {y:0, opacity:1, duration:.9, ease:'power3.out', stagger:.07, overwrite:'auto'}); }});
    /* 絞り込みで後から表示された要素は即表示 */
    $$('[data-filter] [data-f], .pill').forEach(function(t){ t.addEventListener('click', function(){ setTimeout(function(){ gsap.set(items.filter(function(x){ return !x.hidden; }), {y:0, opacity:1}); ScrollTrigger.refresh(); }, 30); }); });
  }

  /* 5. 本文見出し（h2j / h3j）・説明文：ふわっと */
  $$('.h2j, .h3j, .lead2, .plan-v, .contact, .cta-band .wrap > *').forEach(function(el){
    gsap.from(el, {y:24, opacity:0, duration:1, ease:'power3.out', scrollTrigger:{trigger:el, start:'top 90%', once:true}});
  });
})();
