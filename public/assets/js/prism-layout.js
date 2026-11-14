/* Yojo Solutions — "Prism Pulse" layout engine.
   Re-presents the existing page content (nothing is added or removed) as:
   cascade carousel, stacked cards, showcase pills, scroll roadmap,
   spotlight slider and fanned cards. Also tags sections for the 3D scenes. */
(() => {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const main = $('main');
  if (!main) return;
  const LAYOUT = document.documentElement.dataset.layout;
  const ALT = LAYOUT === 'alt'; // Sentinel arrangement
  const GAL = LAYOUT === 'gallery'; // Gallery arrangement
  const GILT = LAYOUT === 'gilt'; // Saffron Maroon arrangement
  const PRISM = !ALT && !GAL && !GILT; // Prism Pulse (+ Light)

  /* ---- scene tags ---- */
  const tag = (el, s) => el && !el.dataset.scene && (el.dataset.scene = s);
  if (ALT || GAL || GILT) {
    tag($('.hs'), 'shield');
    tag($('.fw__grid')?.closest('section'), 'galaxy');
    tag($('.who-teaser'), 'wave');
    tag($('#services'), 'voxel');
    tag($('.why')?.closest('section'), 'rift');
    tag($('[data-timeline]')?.closest('section'), 'shield');
    tag($('.ind-sec'), 'vortex');
    tag($('.insights-sec'), 'galaxy');
  }
  tag($('.hs'), 'vortex');
  tag($('.fw__grid')?.closest('section'), 'rift');
  tag($('.who-teaser'), 'galaxy');
  tag($('#services'), 'voxel');
  tag($('.why')?.closest('section'), 'rift');
  tag($('[data-timeline]')?.closest('section'), 'wave');
  tag($('.ind-sec'), 'galaxy');
  tag($('.stats'), 'vortex');
  tag($('.insights-sec'), 'wave');
  const cycle = ['vortex', 'voxel', 'galaxy', 'rift', 'wave'];
  $$(':scope > section, :scope > div > section', main).forEach((s, i) => tag(s, cycle[i % cycle.length]));

  /* ---- generic: horizontal snap carousel with 3D depth ---- */
  function carousel(track, { counter = false, depth = 'cascade' } = {}) {
    if (!track || track.dataset.pz) return;
    track.dataset.pz = depth;
    const items = [...track.children];
    if (items.length < 2) return;
    const wrap = document.createElement('div');
    wrap.className = 'pz-car pz-car--' + depth;
    track.parentNode.insertBefore(wrap, track);
    wrap.appendChild(track);
    track.classList.add('pz-car__track');
    track.setAttribute('tabindex', '0');
    const ui = document.createElement('div');
    ui.className = 'pz-car__ui';
    ui.innerHTML = (counter ? '<span class="pz-car__count"><b>01</b><i>/ ' + String(items.length).padStart(2, '0') + '</i></span>' : '') +
      '<span class="pz-car__bar"><span></span></span><button type="button" class="pz-car__btn" data-dir="-1" aria-label="Previous">&#8592;</button><button type="button" class="pz-car__btn" data-dir="1" aria-label="Next">&#8594;</button>';
    wrap.appendChild(ui);
    const bar = $('.pz-car__bar span', ui), num = $('.pz-car__count b', ui);
    ui.addEventListener('click', (e) => {
      const b = e.target.closest('[data-dir]'); if (!b) return;
      const w = items[0].getBoundingClientRect().width + 24;
      track.scrollBy({ left: w * +b.dataset.dir, behavior: reduce ? 'auto' : 'smooth' });
    });
    const update = () => {
      const r = track.getBoundingClientRect(), mid = r.left + r.width / 2;
      let best = 0, bd = Infinity;
      items.forEach((el, i) => {
        const c = el.getBoundingClientRect(); const d = (c.left + c.width / 2 - mid) / r.width;
        if (Math.abs(d) < bd) { bd = Math.abs(d); best = i; }
        if (!reduce) el.style.setProperty('--d', d.toFixed(3));
        el.classList.toggle('is-center', false);
      });
      items[best].classList.add('is-center');
      const max = track.scrollWidth - track.clientWidth;
      bar.style.transform = 'scaleX(' + (max > 0 ? Math.max(0.06, track.scrollLeft / max) : 1) + ')';
      if (num) num.textContent = String(best + 1).padStart(2, '0');
    };
    track.addEventListener('scroll', () => requestAnimationFrame(update), { passive: true });
    addEventListener('resize', update);
    // drag to scroll with the mouse
    let down = false, sx = 0, sl = 0, moved = false;
    track.addEventListener('pointerdown', (e) => { if (e.pointerType !== 'mouse') return; down = true; moved = false; sx = e.clientX; sl = track.scrollLeft; track.classList.add('is-drag'); });
    addEventListener('pointermove', (e) => { if (!down) return; const dx = e.clientX - sx; if (Math.abs(dx) > 4) moved = true; track.scrollLeft = sl - dx; });
    addEventListener('pointerup', () => { down = false; track.classList.remove('is-drag'); });
    track.addEventListener('click', (e) => { if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; } }, true);
    update();
  }

  // Frameworks → 3D cascade with big counter
  carousel($('.fw__grid'), { counter: true, depth: (GAL || PRISM) ? 'mirror' : (ALT || GILT) ? 'spot' : 'cascade' });
  // Industries → spotlight slider
  carousel($('.icard-grid'), { counter: true, depth: PRISM ? 'arc' : GILT ? 'mirror' : GAL ? 'slip' : ALT ? 'cascade' : 'spot' });
  // Service cards on the services / division pages → curved arc (Prism)
  if (PRISM) $$('.svc-card-grid').forEach((g) => carousel(g, { counter: true, depth: 'arc' }));
  // Testimonials keep their own slider; case studies → freefall deck (Prism) or spotlight
  if (PRISM) $$('.case-grid').forEach((g) => carousel(g, { counter: true, depth: 'mirror' })); else carousel($('.case-grid'), { depth: 'spot' });
  // Client voices → cards-cascade deck: the front card folds away and the next rises from the pile
  if (PRISM) $$('[data-slider]').forEach((sl) => {
    const track = $('.slider__track', sl); const cards = track ? [...track.children] : [];
    if (cards.length < 2) return;
    sl.classList.add('pz-deck');
    const n = cards.length; let cur = 0, timer;
    const counter = document.createElement('span'); counter.className = 'pz-deck__count';
    $('.slider__nav', sl)?.insertBefore(counter, $('[data-next]', sl));
    const fit = () => { track.style.height = ''; const h = Math.max(...cards.map((c) => c.scrollHeight)); track.style.height = (h + 40) + 'px'; };
    const set = (i) => {
      cur = (i + n) % n;
      cards.forEach((c, k) => { const o = (k - cur + n) % n; c.style.setProperty('--o', o === n - 1 ? -1 : Math.min(o, 3)); c.classList.toggle('is-front', o === 0); c.setAttribute('aria-hidden', o !== 0); });
      counter.innerHTML = '<b>' + String(cur + 1).padStart(2, '0') + '</b> / ' + String(n).padStart(2, '0');
    };
    const go = (d) => { set(cur + d); clearInterval(timer); auto(); };
    const auto = () => { if (!reduce) timer = setInterval(() => set(cur + 1), 5000); };
    // capture phase so the original slider handlers don't also run
    sl.addEventListener('click', (e) => { const b = e.target.closest('[data-prev],[data-next]'); if (!b) return; e.stopImmediatePropagation(); e.preventDefault(); go(b.hasAttribute('data-next') ? 1 : -1); }, true);
    sl.addEventListener('pointerenter', () => clearInterval(timer)); sl.addEventListener('pointerleave', auto);
    set(0); fit(); addEventListener('resize', fit); auto();
  });

  /* ---- Services: stacked sticky cards ---- */
  if (ALT || GAL) {
    // Services -> expanding colonnade; the hovered/focused column opens up.
    $$('.div-grid').forEach((g) => {
      g.classList.add('pz-cols');
      const cols = [...g.children];
      const open = (c) => cols.forEach((x) => x.classList.toggle('is-open', x === c));
      cols.forEach((c) => { c.addEventListener('mouseenter', () => open(c)); c.addEventListener('focus', () => open(c)); });
      open(cols[0]);
    });
    // Why choose us -> stacked sticky cards (Sentinel arrangement only)
    if (ALT) $$('.why__grid').forEach((g) => { g.dataset.pz = 1; g.classList.add('pz-stack', 'pz-stack--why'); [...g.children].forEach((c, i) => c.style.setProperty('--k', i)); });
  }
  if (!ALT && !GAL) $$('.div-grid').forEach((g) => { g.classList.add('pz-stack'); [...g.children].forEach((c, i) => c.style.setProperty('--k', i)); });

  /* ---- Why choose us → showcase: pills left, detail right ---- */
  const why = $('.why__grid');
  if (why && !why.dataset.pz) {
    why.dataset.pz = 1;
    const items = $$('.why__item', why);
    const pills = document.createElement('div');
    pills.className = 'pz-pills'; pills.setAttribute('role', 'tablist');
    items.forEach((it, i) => {
      const h = $('h3', it);
      const b = document.createElement('button');
      b.type = 'button'; b.className = 'pz-pill'; b.setAttribute('role', 'tab');
      b.innerHTML = '<span>' + String(i + 1).padStart(2, '0') + '</span>' + (h ? h.textContent : 'Item ' + (i + 1));
      b.addEventListener('mouseenter', () => show(i)); b.addEventListener('click', () => { show(i); stop(); });
      pills.appendChild(b);
      it.setAttribute('role', 'tabpanel');
    });
    why.classList.add('pz-show');
    why.prepend(pills);
    let cur = -1, timer;
    function show(i) {
      if (i === cur) return; cur = i;
      items.forEach((it, k) => it.classList.toggle('is-on', k === i));
      $$('.pz-pill', pills).forEach((p, k) => { p.classList.toggle('is-on', k === i); p.setAttribute('aria-selected', k === i); });
    }
    const stop = () => clearInterval(timer);
    show(0);
    if (!reduce) { timer = setInterval(() => show((cur + 1) % items.length), 4200); why.addEventListener('pointerenter', stop); }
  }

  /* ---- Methodology timeline → roadmap ascent (scroll-driven) ---- */
  const tl = $('[data-timeline]');
  if (tl && !tl.dataset.pz) {
    tl.dataset.pz = 1;
    tl.classList.add('pz-road');
    const steps = $$('.timeline__step', tl);
    const meter = document.createElement('div');
    meter.className = 'pz-road__meter';
    meter.innerHTML = '<span class="pz-road__num">01</span><span class="pz-road__lbl">of ' + String(steps.length).padStart(2, '0') + ' steps</span>';
    tl.parentNode.insertBefore(meter, tl);
    const rail = document.createElement('span'); rail.className = 'pz-road__rail'; rail.innerHTML = '<span></span><i></i>';
    tl.prepend(rail);
    const fill = rail.firstChild, dot = rail.lastChild, num = $('.pz-road__num', meter);
    const upd = () => {
      const r = tl.getBoundingClientRect();
      const p = Math.min(1, Math.max(0, (innerHeight * 0.55 - r.top) / r.height));
      fill.style.transform = 'scaleY(' + p + ')'; dot.style.top = (p * 100) + '%';
      let active = 0;
      steps.forEach((s, i) => { const on = s.getBoundingClientRect().top < innerHeight * 0.6; s.classList.toggle('is-passed', on); if (on) active = i; });
      num.textContent = String(active + 1).padStart(2, '0');
    };
    addEventListener('scroll', () => requestAnimationFrame(upd), { passive: true }); upd();
  }

  /* ---- Insights → almanac stack (Prism) or fanned cards ---- */
  if (PRISM) $$('.post-grid').forEach((g) => { g.dataset.pz = 1; g.classList.add('pz-alm'); [...g.children].forEach((c, i) => c.style.setProperty('--k', i)); });
  $$('.post-grid:not(.pz-alm)').forEach((g) => { g.classList.add('pz-fan'); [...g.children].forEach((c, i, a) => c.style.setProperty('--f', (i - (a.length - 1) / 2).toFixed(2))); });

  /* ---- Freefall deck: cards drop one by one onto a pile as the section scrolls ---- */
  function freefall(grid) {
    if (!grid || grid.dataset.pz) return;
    const cards = [...grid.children];
    if (cards.length < 2 || reduce) return;
    grid.dataset.pz = 'fall';
    const sec = document.createElement('div'); sec.className = 'pz-fall'; sec.style.setProperty('--n', cards.length);
    grid.parentNode.insertBefore(sec, grid);
    const stage = document.createElement('div'); stage.className = 'pz-fall__stage'; sec.appendChild(stage);
    const meter = document.createElement('div'); meter.className = 'pz-fall__meter';
    meter.innerHTML = '<b>01</b><i>/ ' + String(cards.length).padStart(2, '0') + '</i><span><em></em></span>';
    stage.appendChild(meter); stage.appendChild(grid); grid.classList.add('pz-fall__deck');
    const num = meter.querySelector('b'), dot = meter.querySelector('em');
    const upd = () => {
      const r = sec.getBoundingClientRect();
      const p = Math.min(1, Math.max(0, -r.top / Math.max(1, r.height - innerHeight)));
      const f = p * (cards.length - 0.999);
      cards.forEach((c, i) => {
        const s = Math.min(1, Math.max(0, f - i + 1)); // 0 = waiting above, 1 = landed
        const depth = Math.max(0, f - i);            // how far down the pile it is
        c.style.setProperty('--s', s.toFixed(3)); c.style.setProperty('--p', Math.min(4, depth).toFixed(3));
        c.style.zIndex = 10 + i;
      });
      num.textContent = String(Math.min(cards.length, Math.floor(f) + 1)).padStart(2, '0');
      dot.style.top = (p * 100) + '%';
    };
    addEventListener('scroll', () => requestAnimationFrame(upd), { passive: true }); addEventListener('resize', upd); upd();
  }

  /* ---- Section entrance transitions (every page): fold, rise, tilt, zoom ---- */
  if (PRISM && !reduce && 'IntersectionObserver' in window) {
    const kinds = ['fold', 'tilt', 'rise', 'zoom'];
    const secs = $$(':scope > section, :scope > div > section, .fw, .stats, .cta-band, .page-hero ~ section', main).filter((s) => !s.matches('.hs'));
    const io = new IntersectionObserver((es) => es.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } }), { rootMargin: '0px 0px -12% 0px', threshold: 0.04 });
    [...new Set(secs)].forEach((s, i) => {
      const r = s.getBoundingClientRect(); if (r.top < innerHeight * 0.9) return; // already on screen: leave as is
      s.classList.add('pz-enter', 'pz-enter--' + kinds[i % kinds.length]); io.observe(s);
    });
    // fallback (throttled observers, fast jumps): reveal anything at or above the fold
    const sweep = () => $$('.pz-enter:not(.is-in)').forEach((s) => { if (s.getBoundingClientRect().top < innerHeight * 0.95) s.classList.add('is-in'); });
    addEventListener('scroll', () => requestAnimationFrame(sweep), { passive: true }); setInterval(sweep, 600);
  }

  /* ---- Big gradient heading words (who-teaser) ---- */
  $$('.section-head h2, .fw__head h2').forEach((h) => h.classList.add('pz-title'));
})();
