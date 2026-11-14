/**
 * Yojo Solutions — frontend interactions (vanilla ES6+, no dependencies).
 */
(() => {
  'use strict';

  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
  const BASE = ($('meta[name="base-url"]') || {}).content || '';
  const CSRF = () => ($('meta[name="csrf-token"]') || {}).content || '';
  const api = (path) => `${BASE}/api/${path}`;
  const store = {
    get(k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set(k, v) { try { localStorage.setItem(k, v); } catch (e) { /* ignore */ } },
  };

  /* ---------------- Navigation ---------------- */
  const nav = $('#siteNav');
  const menu = $('#navMenu');
  const toggle = $('#navToggle');
  const isDesktop = () => window.matchMedia('(min-width: 1100px)').matches;

  if (toggle && menu) {
    toggle.addEventListener('click', () => {
      const open = toggle.getAttribute('aria-expanded') !== 'true';
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      menu.classList.toggle('is-open', open);
      if (nav) nav.classList.toggle('menu-open', open);
      document.body.style.overflow = open ? 'hidden' : '';
    });
  }

  const closeDropdowns = (except) => {
    $$('.has-mega, .has-drop').forEach((li) => {
      if (li === except) return;
      li.classList.remove('is-open');
      const b = $('.nav__trigger', li);
      if (b) b.setAttribute('aria-expanded', 'false');
    });
  };

  $$('.has-mega, .has-drop').forEach((li) => {
    const btn = $('.nav__trigger', li);
    let timer;
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const open = !li.classList.contains('is-open');
      closeDropdowns(li);
      li.classList.toggle('is-open', open);
      btn.setAttribute('aria-expanded', String(open));
    });
    li.addEventListener('mouseenter', () => { if (!isDesktop()) return; clearTimeout(timer); closeDropdowns(li); li.classList.add('is-open'); btn.setAttribute('aria-expanded', 'true'); });
    li.addEventListener('mouseleave', () => { if (!isDesktop()) return; timer = setTimeout(() => { li.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false'); }, 160); });
  });
  const megaTabs = $$('[data-mega-tab]');
  const showMega = (slug) => {
    megaTabs.forEach((t) => { const on = t.dataset.megaTab === slug; t.classList.toggle('is-active', on); t.setAttribute('aria-selected', String(on)); });
    $$('[data-mega-panel]').forEach((p) => p.classList.toggle('is-active', p.dataset.megaPanel === slug));
  };
  megaTabs.forEach((t) => {
    t.addEventListener('click', (e) => { e.stopPropagation(); showMega(t.dataset.megaTab); });
    t.addEventListener('mouseenter', () => { if (isDesktop()) showMega(t.dataset.megaTab); });
    t.addEventListener('focus', () => { if (isDesktop()) showMega(t.dataset.megaTab); });
  });
  document.addEventListener('click', (e) => { if (isDesktop() && !e.target.closest('.has-mega, .has-drop')) closeDropdowns(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeDropdowns(); } });

  const toTop = $('#toTop');
  const onScroll = () => {
    const y = window.scrollY;
    if (nav) nav.classList.toggle('is-scrolled', y > 8);
    if (toTop) {
      toTop.classList.toggle('is-visible', y > 700);
      const max = document.documentElement.scrollHeight - window.innerHeight;
      toTop.style.setProperty('--p', max > 0 ? Math.round((y / max) * 100) : 0);
    }
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
  if (toTop) toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  /* ---------------- Reveal on scroll + counters ---------------- */
  const animateCount = (el) => {
    const target = parseInt(el.dataset.count, 10) || 0;
    const dur = 1600;
    const start = performance.now();
    const tick = (now) => {
      const p = Math.min(1, (now - start) / dur);
      el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString();
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };

  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => {
        if (!en.isIntersecting) return;
        en.target.classList.add('is-in');
        $$('[data-count]', en.target).forEach(animateCount);
        io.unobserve(en.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    $$('.reveal, .fx-reveal, .mv, .risk-list li').forEach((el) => io.observe(el));
  } else {
    $$('.reveal, .fx-reveal, .mv, .risk-list li').forEach((el) => el.classList.add('is-in'));
    $$('[data-count]').forEach((el) => { el.textContent = el.dataset.count; });
  }

  /* ---------------- Testimonial slider ---------------- */
  $$('[data-slider]').forEach((slider) => {
    const track = $('.slider__track', slider);
    const slides = $$('.quote-card', track);
    const dotsWrap = $('[data-dots]', slider);
    if (!slides.length) return;
    let index = 0;
    let auto;
    const perView = () => (window.innerWidth >= 1100 ? 3 : window.innerWidth >= 768 ? 2 : 1);
    const pages = () => Math.max(1, slides.length - perView() + 1);
    const render = () => {
      index = Math.min(index, pages() - 1);
      const w = slides[0].getBoundingClientRect().width + parseFloat(getComputedStyle(slides[0]).marginRight || 0);
      track.style.transform = `translateX(${-index * w}px)`;
      $$('button', dotsWrap).forEach((d, i) => d.classList.toggle('is-active', i === index));
    };
    const buildDots = () => {
      dotsWrap.innerHTML = '';
      for (let i = 0; i < pages(); i++) {
        const b = document.createElement('button');
        b.setAttribute('aria-label', `Go to slide ${i + 1}`);
        b.addEventListener('click', () => { index = i; render(); restart(); });
        dotsWrap.appendChild(b);
      }
    };
    const go = (d) => { index = (index + d + pages()) % pages(); render(); };
    const restart = () => { clearInterval(auto); auto = setInterval(() => go(1), 6500); };
    $('[data-prev]', slider).addEventListener('click', () => { go(-1); restart(); });
    $('[data-next]', slider).addEventListener('click', () => { go(1); restart(); });
    slider.addEventListener('mouseenter', () => clearInterval(auto));
    slider.addEventListener('mouseleave', restart);
    let sx = null;
    track.addEventListener('touchstart', (e) => { sx = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', (e) => { if (sx === null) return; const dx = e.changedTouches[0].clientX - sx; if (Math.abs(dx) > 40) go(dx < 0 ? 1 : -1); sx = null; restart(); });
    window.addEventListener('resize', () => { buildDots(); render(); });
    buildDots(); render(); restart();
  });

  /* ---------------- FAQ accordion + category tabs + search ---------------- */
  $$('[data-faq]').forEach((faq) => {
    const items = $$('[data-faq-item]', faq);
    const setOpen = (item, open) => {
      const q = $('.faq__q', item);
      q.setAttribute('aria-expanded', String(open));
      item.classList.toggle('is-open', open);
    };
    items.forEach((item) => {
      const q = $('.faq__q', item);
      setOpen(item, q.getAttribute('aria-expanded') === 'true');
      q.addEventListener('click', () => setOpen(item, q.getAttribute('aria-expanded') !== 'true'));
    });
    const search = $('[data-faq-search]', faq);
    const empty = $('[data-faq-empty]', faq);
    const tabs = $$('[data-faq-tab]', faq);
    let cat = 'all';
    const apply = () => {
      const term = search ? search.value.trim().toLowerCase() : '';
      let shown = 0;
      items.forEach((item) => {
        const ok = (cat === 'all' || term || item.dataset.cat === cat) && (!term || item.textContent.toLowerCase().includes(term));
        item.hidden = !ok;
        if (ok) shown++;
      });
      if (empty) empty.hidden = shown > 0;
    };
    tabs.forEach((t) => t.addEventListener('click', () => {
      cat = t.dataset.faqTab;
      tabs.forEach((x) => { const on = x === t; x.classList.toggle('is-active', on); x.setAttribute('aria-selected', String(on)); });
      if (search) search.value = '';
      apply();
    }));
    if (search) search.addEventListener('input', apply);
  });

  /* ---------------- Services page: division tabs + search ---------------- */
  $$('[data-svc-tabs]').forEach((root) => {
    const tabs = $$('[data-svc-tab]', root);
    const panels = $$('[data-svc-panel]', root);
    const search = $('[data-svc-search]', root);
    const empty = $('[data-svc-empty]', root);
    const show = (slug, focus) => {
      tabs.forEach((t) => { const on = t.dataset.svcTab === slug; t.classList.toggle('is-active', on); t.setAttribute('aria-selected', String(on)); t.tabIndex = on ? 0 : -1; if (on && focus) t.focus(); });
      panels.forEach((p) => p.classList.toggle('is-active', p.dataset.svcPanel === slug));
    };
    tabs.forEach((t, i) => {
      t.addEventListener('click', () => { show(t.dataset.svcTab); if (search && search.value) { search.value = ''; filter(); } history.replaceState(null, '', '#' + t.dataset.svcTab); });
      t.addEventListener('keydown', (e) => {
        const d = e.key === 'ArrowRight' || e.key === 'ArrowDown' ? 1 : e.key === 'ArrowLeft' || e.key === 'ArrowUp' ? -1 : 0;
        if (d) { e.preventDefault(); show(tabs[(i + d + tabs.length) % tabs.length].dataset.svcTab, true); }
      });
    });
    const filter = () => {
      const term = search.value.trim().toLowerCase();
      root.classList.toggle('is-searching', !!term);
      let any = false;
      panels.forEach((p) => {
        let n = 0;
        $$('[data-svc-item]', p).forEach((it) => { const ok = !term || it.dataset.text.includes(term); it.hidden = !ok; if (ok) n++; });
        if (term && !n) p.setAttribute('data-empty', ''); else p.removeAttribute('data-empty');
        if (n) any = true;
      });
      if (empty) empty.hidden = any;
    };
    if (search) search.addEventListener('input', filter);
    const hash = location.hash.slice(1);
    if (hash && tabs.some((t) => t.dataset.svcTab === hash)) show(hash);
  });

  /* ---------------- Table of contents scrollspy ---------------- */
  const toc = $('[data-toc]');
  if (toc && 'IntersectionObserver' in window) {
    const links = $$('a', toc);
    const spy = new IntersectionObserver((entries) => {
      entries.forEach((en) => {
        if (en.isIntersecting) {
          links.forEach((l) => l.classList.toggle('is-active', l.getAttribute('href') === `#${en.target.id}`));
        }
      });
    }, { rootMargin: '-30% 0px -65% 0px' });
    links.forEach((l) => { const t = $(l.getAttribute('href')); if (t) spy.observe(t); });
  }

  /* ---------------- Cookie banner ---------------- */
  const cookie = $('#cookieBanner');
  if (cookie && !store.get('yj_cookie')) {
    cookie.hidden = false;
    $$('[data-cookie]', cookie).forEach((b) => b.addEventListener('click', () => { store.set('yj_cookie', b.dataset.cookie); cookie.hidden = true; }));
  }

  /* ---------------- Copy link ---------------- */
  $$('[data-copy]').forEach((b) => b.addEventListener('click', async () => {
    try { await navigator.clipboard.writeText(b.dataset.copy); b.style.color = 'var(--ok)'; setTimeout(() => { b.style.color = ''; }, 1500); } catch (e) { /* ignore */ }
  }));

  /* ---------------- Shared form helpers ---------------- */
  const setError = (form, name, msg) => {
    let field = form.querySelector(`[data-field="${name}"]`);
    if (!field) {
      const input = form.querySelector(`[name="${name}"], [name="${name}[]"]`);
      field = input ? input.closest('.field') : null;
    }
    const errEl = field ? $('.field__err', field) : form.querySelector(`[data-err="${name}"]`);
    if (field) field.classList.toggle('has-error', !!msg);
    if (errEl) errEl.textContent = msg || '';
    return !msg;
  };
  const clearErrors = (scope) => {
    $$('.has-error', scope).forEach((f) => f.classList.remove('has-error'));
    $$('.field__err', scope).forEach((e) => { e.textContent = ''; });
  };
  const validateInputs = (form, scope) => {
    let ok = true;
    let first = null;
    $$('input, select, textarea', scope).forEach((el) => {
      if (!el.name || el.type === 'hidden' || el.closest('.hp') || el.type === 'radio' || el.name.endsWith('[]')) return;
      let msg = '';
      const v = el.type === 'checkbox' ? el.checked : el.value.trim();
      const label = ((el.closest('.field') && $('label, .label', el.closest('.field'))) || {}).textContent || 'This field';
      const name = label.replace('*', '').replace(/\(.*\)/, '').trim();
      if (el.required && !v) msg = el.type === 'checkbox' ? 'Please tick this box to continue.' : `${name} is required.`;
      else if (v && el.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)) msg = 'Enter a valid email address.';
      else if (v && el.pattern && !new RegExp(el.pattern).test(v)) msg = `Enter a valid ${name.toLowerCase()}.`;
      else if (v && el.minLength > 0 && v.length < el.minLength) msg = `${name} must be at least ${el.minLength} characters.`;
      else if (v && el.type === 'date' && el.min && v < el.min) msg = 'Please choose a future date.';
      else if (v && el.type === 'date' && new Date(`${v}T00:00:00`).getDay() === 0) msg = 'We are closed on Sundays — please choose another date.';
      if (msg) { ok = false; if (!first) first = el; }
      setError(form, el.name, msg);
    });
    if (first) first.focus({ preventScroll: false });
    return ok;
  };
  const serialize = (form) => {
    const data = {};
    new FormData(form).forEach((v, k) => {
      const m = k.match(/^(\w+)\[(\w*)\]$/);
      if (m) {
        if (m[2]) { data[m[1]] = data[m[1]] || {}; data[m[1]][m[2]] = v; } else { (data[m[1]] = data[m[1]] || []).push(v); }
      } else data[k] = v;
    });
    return data;
  };
  const post = async (path, body) => {
    const res = await fetch(api(path), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF(), Accept: 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(body),
    });
    let json = {};
    try { json = await res.json(); } catch (e) { json = { success: false, message: 'Unexpected server response.' }; }
    return { status: res.status, json };
  };
  const showAlert = (form, msg) => { const a = $('.form__alert', form); if (a) { a.textContent = msg || ''; a.hidden = !msg; } };

  /* ---------------- Service multi-select picker ---------------- */
  $$('[data-svc-picker]').forEach((picker) => {
    const btn = $('.svc-picker__btn', picker);
    const panel = $('.svc-picker__panel', picker);
    const summary = $('[data-svc-summary]', picker);
    const chips = $('[data-svc-chips]', picker);
    const boxes = $$('input[type=checkbox]', picker);
    const search = $('.svc-picker__search', picker);
    const sync = () => {
      const sel = boxes.filter((b) => b.checked);
      summary.textContent = sel.length ? `${sel.length} service${sel.length > 1 ? 's' : ''} selected` : 'Select one or more services';
      chips.innerHTML = '';
      sel.forEach((b) => {
        const s = document.createElement('span');
        s.textContent = b.nextElementSibling.textContent;
        const x = document.createElement('button');
        x.type = 'button'; x.textContent = '×'; x.setAttribute('aria-label', `Remove ${s.textContent}`);
        x.addEventListener('click', () => { b.checked = false; sync(); });
        s.appendChild(x);
        chips.appendChild(s);
      });
    };
    btn.addEventListener('click', () => { const open = panel.hidden; panel.hidden = !open; btn.setAttribute('aria-expanded', String(open)); if (open && search) search.focus(); });
    document.addEventListener('click', (e) => { if (!picker.contains(e.target)) { panel.hidden = true; btn.setAttribute('aria-expanded', 'false'); } });
    boxes.forEach((b) => b.addEventListener('change', sync));
    if (search) search.addEventListener('input', () => {
      const t = search.value.toLowerCase();
      $$('.check', panel).forEach((c) => { c.hidden = !c.textContent.toLowerCase().includes(t); });
      $$('fieldset', panel).forEach((f) => { f.hidden = !$$('.check', f).some((c) => !c.hidden); });
    });
    sync();
  });

  /* ---------------- Enquiry / consultation / proposal forms ---------------- */
  $$('[data-enquiry-form]').forEach((form) => {
    const success = form.parentElement.querySelector('[data-form-success]');
    const submit = $('button[type=submit]', form);
    let busy = false;
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (busy) return;
      clearErrors(form); showAlert(form, '');
      let ok = validateInputs(form, form);
      const type = form.elements.type.value;
      if (type !== 'contact' && !$$('input[name="services[]"]:checked', form).length) { ok = setError(form, 'services', 'Select at least one service.') && ok; }
      if (form.hasAttribute('data-quick') && !$$('input[name="divisions[]"]:checked', form).length) { ok = setError(form, 'divisions', 'Select at least one service area.') && ok; }
      if (!ok) { showAlert(form, 'Please correct the highlighted fields.'); return; }
      busy = true; submit.classList.add('is-loading');
      try {
        const { json } = await post('enquiry', serialize(form));
        if (json.success) {
          store.set('yojo_enquired', String(Date.now()));
          form.hidden = true;
          $('[data-success-message]', success).textContent = json.message;
          $('[data-success-ref]', success).textContent = json.reference;
          success.hidden = false;
          if (!form.hasAttribute('data-quick')) success.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
          Object.entries(json.errors || {}).forEach(([k, v]) => setError(form, k, v));
          showAlert(form, json.message || 'Something went wrong. Please try again.');
        }
      } catch (err) {
        showAlert(form, 'Network error — please check your connection and try again.');
      } finally { busy = false; submit.classList.remove('is-loading'); }
    });
  });

  /* ---------------- Audit Readiness wizard ---------------- */
  const wiz = $('#wizard');
  if (wiz) {
    const panels = $$('[data-step]', wiz);
    const indicators = $$('[data-step-indicator]', wiz);
    const back = $('[data-back]', wiz);
    const next = $('[data-next]', wiz);
    const nextLabel = $('.btn__label', next);
    const bar = $('[data-progress]', wiz);
    const otherWrap = $('[data-other]', wiz);
    let step = 0;
    let busy = false;

    $$('input[name="frameworks[]"]', wiz).forEach((b) => b.addEventListener('change', () => {
      otherWrap.hidden = !$('input[value="OTHER"]', wiz).checked;
      setError(wiz, 'frameworks', '');
    }));
    $$('.q input', wiz).forEach((r) => r.addEventListener('change', () => r.closest('.q').classList.remove('has-error')));

    const show = (n) => {
      step = n;
      panels.forEach((p, i) => { p.hidden = i !== n; });
      indicators.forEach((li, i) => { li.classList.toggle('is-current', i === n); li.classList.toggle('is-done', i < n); });
      bar.style.width = `${(n / (panels.length - 1)) * 100}%`;
      back.hidden = n === 0 || n === panels.length - 1;
      next.hidden = n === panels.length - 1;
      nextLabel.textContent = n === 3 ? 'See my results' : 'Continue';
      wiz.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const validateStep = (n) => {
      const panel = panels[n];
      clearErrors(panel);
      showAlert(wiz, '');
      if (n === 1) {
        if (!$$('input[name="frameworks[]"]:checked', wiz).length) return setError(wiz, 'frameworks', 'Select at least one requirement.');
        return true;
      }
      if (n === 2) {
        let ok = true; let first = null;
        $$('.q', panel).forEach((q) => {
          const answered = $$('input', q).some((r) => r.checked);
          q.classList.toggle('has-error', !answered);
          if (!answered) { ok = false; if (!first) first = q; }
        });
        $('[data-err="answers"]', wiz).textContent = ok ? '' : 'Please answer every question.';
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return ok;
      }
      return validateInputs(wiz, panel);
    };

    const renderResult = (ref, r) => {
      $('[data-ref]', wiz).textContent = ref;
      $('[data-band]', wiz).textContent = r.band;
      $('[data-band-text]', wiz).textContent = r.band_text;
      const scoreEl = $('[data-score]', wiz);
      const ring = $('[data-ring]', wiz);
      setTimeout(() => { ring.setAttribute('stroke-dasharray', `${r.score} 100`); }, 150);
      const start = performance.now();
      const tick = (now) => { const p = Math.min(1, (now - start) / 1200); scoreEl.textContent = Math.round(r.score * p); if (p < 1) requestAnimationFrame(tick); };
      requestAnimationFrame(tick);
      const wrap = $('[data-domains]', wiz);
      wrap.innerHTML = '';
      Object.values(r.domains).forEach((d, i) => {
        const cls = d.score < 40 ? 'lo' : d.score < 70 ? 'mid' : 'hi';
        const row = document.createElement('div');
        row.className = 'bar';
        row.innerHTML = `<span></span><div class="bar__track"><div class="bar__fill ${cls}" style="--w:${d.score}%;--d:${i * 0.1}s"></div></div><em class="mono">${d.score}%</em>`;
        row.firstChild.textContent = d.label;
        wrap.appendChild(row);
      });
      const prio = $('[data-prio]', wiz);
      prio.innerHTML = '';
      (r.priorities || []).forEach((p) => { const li = document.createElement('li'); li.textContent = p; prio.appendChild(li); });
      $('[data-prio-wrap]', wiz).hidden = !(r.priorities || []).length;
    };

    back.addEventListener('click', () => show(Math.max(0, step - 1)));
    next.addEventListener('click', async () => {
      if (busy || !validateStep(step)) return;
      if (step < 3) { show(step + 1); return; }
      busy = true; next.classList.add('is-loading');
      try {
        const { json } = await post('assessment', serialize(wiz));
        if (json.success) {
          renderResult(json.reference, json.result);
          show(4);
        } else {
          const errs = json.errors || {};
          Object.entries(errs).forEach(([k, v]) => setError(wiz, k, v));
          const stepOf = { organization_type: 0, industry: 0, company_size: 0, country: 0, frameworks: 1, answers: 2 };
          const firstKey = Object.keys(errs)[0];
          if (firstKey && stepOf[firstKey] !== undefined) show(stepOf[firstKey]);
          showAlert(wiz, json.message || 'Please review your answers.');
        }
      } catch (e) {
        showAlert(wiz, 'Network error — please try again.');
      } finally { busy = false; next.classList.remove('is-loading'); }
    });
    show(0);
  }
  /* ---------------- Homepage hero slider ---------------- */
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  $$('[data-hero-slider]').forEach((hs) => {
    const slides = $$('.hs__slide', hs);
    const live = $('[data-hs-live]', hs);
    if (slides.length < 2) return;
    let idx = 0;
    let timer = null;
    const paused = reduceMotion; // no autoplay when the visitor prefers reduced motion
    let hover = false;
    const dur = (i) => Math.min(3000, Math.max(2000, (parseFloat(slides[i].dataset.duration) || 3) * 1000 - 500)); // 2-3 s per slide
    const setFocusable = (slide, on) => $$('a, button', slide).forEach((el) => { if (on) el.removeAttribute('tabindex'); else el.setAttribute('tabindex', '-1'); });
    const schedule = () => {
      clearTimeout(timer);
      if (paused || hover || document.hidden) return;
      timer = setTimeout(() => go(idx + 1), dur(idx));
    };
    const go = (n, announce) => {
      const next = (n + slides.length) % slides.length;
      if (next !== idx) {
        slides[idx].classList.remove('is-active');
        slides[idx].setAttribute('aria-hidden', 'true');
        setFocusable(slides[idx], false);
        idx = next;
        slides[idx].classList.add('is-active');
        slides[idx].removeAttribute('aria-hidden');
        setFocusable(slides[idx], true);
      }
      hs.style.setProperty('--hs-dur', dur(idx) / 1000 + 's');
      hs.dispatchEvent(new CustomEvent('hs:change', { detail: { index: idx, icon: slides[idx].dataset.icon } }));
      if (announce && live) live.textContent = slides[idx].getAttribute('aria-label');
      schedule();
    };
    const prev = $('[data-hs-prev]', hs); const next = $('[data-hs-next]', hs);
    if (prev) prev.addEventListener('click', () => go(idx - 1, true));
    if (next) next.addEventListener('click', () => go(idx + 1, true));
    hs.addEventListener('mouseenter', () => { hover = true; clearTimeout(timer); });
    hs.addEventListener('mouseleave', () => { hover = false; schedule(); });
    hs.addEventListener('focusin', () => { hover = true; clearTimeout(timer); });
    hs.addEventListener('focusout', (e) => { if (!hs.contains(e.relatedTarget)) { hover = false; schedule(); } });
    hs.addEventListener('keydown', (e) => { if (e.key === 'ArrowRight') go(idx + 1, true); if (e.key === 'ArrowLeft') go(idx - 1, true); });
    let sx = null, sy = null;
    hs.addEventListener('touchstart', (e) => { sx = e.touches[0].clientX; sy = e.touches[0].clientY; }, { passive: true });
    hs.addEventListener('touchend', (e) => {
      if (sx === null) return;
      const dx = e.changedTouches[0].clientX - sx; const dy = e.changedTouches[0].clientY - sy;
      if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) go(idx + (dx < 0 ? 1 : -1), true);
      sx = null;
    });
    document.addEventListener('visibilitychange', schedule);
    slides.forEach((sl, i) => setFocusable(sl, i === 0));
    go(0);
  });

  /* ---------------- Frameworks: dialog, card glow, connecting lines ---------------- */
  const fwDlg = $('#fwDialog');
  if (fwDlg && typeof fwDlg.showModal === 'function') {
    const content = $('[data-fw-content]', fwDlg);
    let opener = null;
    $$('[data-fw-open]').forEach((card) => card.addEventListener('click', (e) => {
      const tpl = document.getElementById('fw-' + card.dataset.fwOpen);
      if (!tpl) return;
      e.preventDefault();
      opener = card;
      content.innerHTML = '';
      content.appendChild(tpl.content.cloneNode(true));
      fwDlg.showModal();
      content.scrollTop = 0;
    }));
    const close = () => fwDlg.close();
    $('[data-fw-close]', fwDlg).addEventListener('click', close);
    fwDlg.addEventListener('click', (e) => { if (e.target === fwDlg) close(); });
    fwDlg.addEventListener('close', () => { if (opener) opener.focus(); });
  }
  $$('.fw-card').forEach((c) => c.addEventListener('pointermove', (e) => {
    const r = c.getBoundingClientRect();
    c.style.setProperty('--mx', (e.clientX - r.left) + 'px');
    c.style.setProperty('--my', (e.clientY - r.top) + 'px');
  }));
  const fwLinks = $('[data-fw-links]');
  if (fwLinks) {
    const draw = () => {
      const board = fwLinks.parentElement.getBoundingClientRect();
      const cards = $$('.fw-card', fwLinks.parentElement);
      const pts = cards.map((c) => { const r = c.getBoundingClientRect(); return [r.left - board.left + r.width / 2, r.top - board.top + r.height / 2]; });
      let d = '';
      for (let i = 1; i < pts.length; i++) {
        const [x1, y1] = pts[i - 1]; const [x2, y2] = pts[i];
        const mx = (x1 + x2) / 2;
        d += `M${x1.toFixed(0)} ${y1.toFixed(0)} C${mx.toFixed(0)} ${y1.toFixed(0)} ${mx.toFixed(0)} ${y2.toFixed(0)} ${x2.toFixed(0)} ${y2.toFixed(0)} `;
      }
      fwLinks.setAttribute('viewBox', `0 0 ${board.width.toFixed(0)} ${board.height.toFixed(0)}`);
      fwLinks.innerHTML = d ? `<path d="${d}" pathLength="400"/>` : '';
    };
    draw();
    let rt; window.addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(draw, 150); });
    if ('IntersectionObserver' in window) {
      const lo = new IntersectionObserver((en) => { if (en[0].isIntersecting) { draw(); fwLinks.classList.add('is-drawn'); lo.disconnect(); } }, { threshold: 0.05 });
      lo.observe(fwLinks.parentElement);
    } else { fwLinks.classList.add('is-drawn'); }
  }

  /* ---------------- Who We Are: core values ---------------- */
  $$('[data-values]').forEach((root) => {
    const tabs = $$('[data-value-tab]', root);
    const panels = $$('[data-value-panel]', root);
    let i = 0; let t = null; let interacted = false;
    const show = (n) => {
      i = (n + tabs.length) % tabs.length;
      tabs.forEach((b, k) => { b.classList.toggle('is-active', k === i); b.setAttribute('aria-selected', String(k === i)); });
      panels.forEach((p, k) => { p.hidden = k !== i; p.classList.toggle('is-active', k === i); });
    };
    tabs.forEach((b, k) => {
      b.addEventListener('click', () => { interacted = true; clearInterval(t); show(k); });
      b.addEventListener('mouseenter', () => { if (window.matchMedia('(hover: hover)').matches) { interacted = true; clearInterval(t); show(k); } });
    });
    if (!reduceMotion && 'IntersectionObserver' in window) {
      new IntersectionObserver((en) => {
        clearInterval(t);
        if (en[0].isIntersecting && !interacted) t = setInterval(() => show(i + 1), 3800);
      }, { threshold: 0.4 }).observe(root);
    }
  });

  /* ---------------- Insights filters: apply on change ---------------- */
  $$('form[data-autosubmit] select').forEach((sel) => sel.addEventListener('change', () => sel.form.submit()));

  /* ---------------- Enquiry reminder pop-up ----------------
     Shown after N minutes of *active* time (tab visible + recent interaction),
     then at most once per further N-minute interval, up to a per-visit maximum.
     Never shown after a successful enquiry, on admin pages, or while a form is being filled in. */
  const pop = $('#enqPop');
  if (pop && !location.pathname.includes('/admin')) {
    const KEY = 'yojo_pop';
    const minutes = Math.max(1, parseInt(pop.dataset.interval, 10) || 5);
    const maxShows = Math.max(1, parseInt(pop.dataset.max, 10) || 3);
    const INTERVAL = minutes * 60;
    const IDLE_AFTER = 60; // seconds without interaction before the timer pauses
    const ss = {
      get() { try { return JSON.parse(sessionStorage.getItem(KEY)) || {}; } catch (e) { return {}; } },
      set(v) { try { sessionStorage.setItem(KEY, JSON.stringify(v)); } catch (e) { /* ignore */ } },
    };
    const state = Object.assign({ active: 0, next: INTERVAL, shows: 0 }, ss.get());
    let lastInput = Date.now();
    let visible = false;
    const enquired = () => {
      const t = parseInt(store.get('yojo_enquired') || '0', 10);
      return t && Date.now() - t < 30 * 24 * 3600 * 1000; // 30 days
    };
    const busyWithForm = () => {
      const a = document.activeElement;
      if (a && a.closest && a.closest('form') && /^(INPUT|TEXTAREA|SELECT)$/.test(a.tagName)) return true;
      return $$('form[data-enquiry-form]:not([data-quick]), #wizard form, form#wizard').some((f) => $$('input[type=text], input[type=email], input[type=tel], textarea', f).some((el) => el.value.trim() !== ''));
    };
    ['pointermove', 'pointerdown', 'keydown', 'scroll', 'touchstart', 'wheel'].forEach((ev) => window.addEventListener(ev, () => { lastInput = Date.now(); }, { passive: true }));
    const show = () => {
      visible = true;
      pop.hidden = false;
      pop.classList.remove('is-leaving');
      void pop.offsetWidth;
      pop.classList.add('is-visible');
      document.body.style.overflow = 'hidden';
      setTimeout(() => { const f = $('input[name="full_name"]', pop); if (f) f.focus({ preventScroll: true }); }, 450);
      state.shows += 1;
      ss.set(state);
    };
    const hide = () => {
      if (!visible) return;
      visible = false;
      pop.classList.remove('is-visible');
      pop.classList.add('is-leaving');
      document.body.style.overflow = '';
      // the next reminder is a full interval of active time after dismissal
      state.next = state.active + INTERVAL;
      ss.set(state);
      setTimeout(() => { if (!visible) { pop.hidden = true; pop.classList.remove('is-leaving'); } }, 380);
    };
    $$('[data-pop-close]', pop).forEach((b) => b.addEventListener('click', hide));
    $$('[data-pop-cta]', pop).forEach((a) => a.addEventListener('click', () => { state.next = state.active + INTERVAL; state.shows = maxShows; ss.set(state); }));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hide(); });
    setInterval(() => {
      if (document.hidden || visible) return;                       // timer pauses while the tab is hidden
      if (Date.now() - lastInput > IDLE_AFTER * 1000) return;        // and while the visitor is idle
      state.active += 1;
      if (state.active % 5 === 0) ss.set(state);
      if (state.active < state.next || state.shows >= maxShows || enquired()) return;
      if (busyWithForm() || $('.nav__menu.is-open') || (fwDlg && fwDlg.open)) { state.next = state.active + 30; return; } // retry later
      show();
    }, 1000);
    window.addEventListener('pagehide', () => ss.set(state));
  }
})();
