/* Yojo Solutions — interaction layer: scroll progress, cursor aura,
   3D card tilt with spotlight, magnetic buttons, counting stats. */
(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fine = matchMedia('(hover: hover) and (pointer: fine)').matches;
  const root = document.documentElement;

  // Scroll progress
  const bar = document.querySelector('.fx-progress');
  const onScroll = () => {
    const max = Math.max(1, root.scrollHeight - innerHeight);
    if (bar) bar.style.setProperty('--p', (scrollY / max).toFixed(4));
  };
  addEventListener('scroll', onScroll, { passive: true }); onScroll();

  if (reduce) return;

  // Cursor aura (eased follow)
  const aura = document.querySelector('.fx-aura');
  if (aura && fine) {
    let x = -999, y = -999, tx = x, ty = y;
    addEventListener('pointermove', (e) => { tx = e.clientX; ty = e.clientY; }, { passive: true });
    const tick = () => { x += (tx - x) * 0.12; y += (ty - y) * 0.12; aura.style.setProperty('--ax', x + 'px'); aura.style.setProperty('--ay', y + 'px'); requestAnimationFrame(tick); };
    tick();
  }

  // 3D tilt + spotlight
  const sel = '.svc-card, .div-card, .ind-card, .icard, .fw-card, .post-card, .case-card, .quote-card, .mini-card, .about-card, .why__item, .stat, .timeline__card, .contact-card';
  if (fine) {
    document.querySelectorAll(sel).forEach((el) => {
      el.setAttribute('data-tilt', '');
      const max = el.offsetWidth > 520 ? 4 : 8;
      el.addEventListener('pointermove', (e) => {
        const r = el.getBoundingClientRect();
        const px = (e.clientX - r.left) / r.width, py = (e.clientY - r.top) / r.height;
        el.classList.add('is-tilting');
        el.style.setProperty('--ry', ((px - 0.5) * max).toFixed(2) + 'deg');
        el.style.setProperty('--rx', ((0.5 - py) * max).toFixed(2) + 'deg');
        el.style.setProperty('--mx', (px * 100).toFixed(1) + '%');
        el.style.setProperty('--my', (py * 100).toFixed(1) + '%');
      });
      el.addEventListener('pointerleave', () => { el.classList.remove('is-tilting'); el.style.setProperty('--rx', '0deg'); el.style.setProperty('--ry', '0deg'); });
    });

    // Magnetic primary buttons
    document.querySelectorAll('.btn--primary, .btn--accent, .hs__edge').forEach((b) => {
      b.addEventListener('pointermove', (e) => {
        const r = b.getBoundingClientRect();
        b.style.translate = `${((e.clientX - r.left) / r.width - 0.5) * 10}px ${((e.clientY - r.top) / r.height - 0.5) * 8}px`;
      });
      b.addEventListener('pointerleave', () => { b.style.translate = ''; });
    });
  }
})();

/* Sentinel template: number the section headings like HUD modules */
(() => {
  if (document.documentElement.dataset.template !== 'sentinel') return;
  document.querySelectorAll('main .section-head, main .fw__head').forEach((h, i) => {
    const eb = h.querySelector('.eyebrow');
    h.setAttribute('data-idx', String(i + 1).padStart(2, '0') + (eb ? ' / ' + eb.textContent.trim().toUpperCase().slice(0, 32) : ''));
    if (eb) eb.hidden = true;
  });
})();

/* Rotating typed word (Who We Are hero): types, holds ~2.5 s, deletes, next word */
(() => {
  document.querySelectorAll('[data-rotate]').forEach((el) => {
    let words; try { words = JSON.parse(el.dataset.rotate); } catch (e) { return; }
    const out = el.querySelector('.who-rot__txt'); if (!out || words.length < 2) return;
    if (matchMedia('(prefers-reduced-motion: reduce)').matches) { let i = 0; setInterval(() => { i = (i + 1) % words.length; out.textContent = words[i]; }, 2500); return; }
    let i = 0, txt = words[0], del = true;
    const tick = () => {
      const w = words[i];
      if (del) { txt = txt.slice(0, -1); out.textContent = txt; if (!txt) { del = false; i = (i + 1) % words.length; } return setTimeout(tick, 45); }
      txt = words[i].slice(0, txt.length + 1); out.textContent = txt;
      if (txt === words[i]) { del = true; return setTimeout(tick, 2300); }
      setTimeout(tick, 85);
    };
    setTimeout(tick, 2300);
  });
})();

/* Services page: division cards open into an animated list of their services */
(() => {
  const deck = document.querySelector('[data-svc-deck]'); if (!deck) return;
  deck.addEventListener('click', (e) => {
    const b = e.target.closest('.svc-dcard__head'); if (!b) return;
    const card = b.closest('.svc-dcard'), open = b.getAttribute('aria-expanded') !== 'true';
    deck.querySelectorAll('.svc-dcard.is-open').forEach((c) => { c.classList.remove('is-open'); c.querySelector('.svc-dcard__head').setAttribute('aria-expanded', 'false'); });
    if (open) { card.classList.add('is-open'); b.setAttribute('aria-expanded', 'true'); setTimeout(() => card.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 350); }
  });
  if (location.hash === '#all-services') deck.querySelector('.svc-dcard__head')?.click();
})();

/* 3D tilt that follows the pointer ([data-tilt]) */
(() => {
  if (matchMedia('(prefers-reduced-motion: reduce)').matches || matchMedia('(hover: none)').matches) return;
  document.querySelectorAll('[data-tilt]').forEach((el) => {
    el.addEventListener('pointermove', (e) => { const r = el.getBoundingClientRect(); const x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      el.style.setProperty('--rx', (x * 14).toFixed(2) + 'deg'); el.style.setProperty('--ry', (-y * 10).toFixed(2) + 'deg'); });
    el.addEventListener('pointerleave', () => { el.style.setProperty('--rx', '0deg'); el.style.setProperty('--ry', '0deg'); });
  });
})();
