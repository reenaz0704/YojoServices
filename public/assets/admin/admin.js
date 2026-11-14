/**
 * Admin Control Center interactions: sidebar, confirmations, slug generation,
 * rich-text editor (contenteditable, sanitized server-side), image previews, unsaved-change guard.
 */
(() => {
  'use strict';
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
  const BASE = ($('meta[name="base-url"]') || {}).content || '';
  const CSRF = ($('meta[name="csrf-token"]') || {}).content || '';

  // Sidebar toggle (mobile)
  const side = $('#admSide');
  const burger = $('#admBurger');
  if (burger && side) {
    burger.addEventListener('click', (e) => { e.stopPropagation(); side.classList.toggle('is-open'); });
    document.addEventListener('click', (e) => { if (side.classList.contains('is-open') && !side.contains(e.target)) side.classList.remove('is-open'); });
  }

  // Confirm destructive actions
  $$('form[data-confirm]').forEach((f) => f.addEventListener('submit', (e) => {
    if (!window.confirm(f.dataset.confirm)) e.preventDefault(); else f.dataset.submitted = '1';
  }));

  // Slug auto-generation
  const slugify = (s) => s.toLowerCase().normalize('NFKD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
  $$('[data-slug-from]').forEach((slug) => {
    const src = $(`[name="${slug.dataset.slugFrom}"]`);
    if (!src) return;
    let touched = slug.value !== '';
    slug.addEventListener('input', () => { touched = slug.value !== ''; });
    src.addEventListener('input', () => { if (!touched) slug.value = slugify(src.value); });
  });

  // Pivot filter
  $$('[data-pivot-filter]').forEach((inp) => inp.addEventListener('input', () => {
    const t = inp.value.toLowerCase();
    $$('.pivot__opt', inp.parentElement).forEach((o) => { o.hidden = !o.textContent.toLowerCase().includes(t); });
  }));

  // Image preview
  $$('[data-img-field]').forEach((wrap) => {
    const input = $('input[type=file]', wrap);
    const img = $('[data-img-preview]', wrap);
    input.addEventListener('change', () => {
      const f = input.files[0];
      if (!f) return;
      img.src = URL.createObjectURL(f);
      img.hidden = false;
    });
  });

  // Rich-text editor
  $$('[data-rte]').forEach((rte) => {
    const area = $('.rte__area', rte);
    const src = $('.rte__src', rte);
    let htmlMode = false;
    const sync = () => { if (!htmlMode) src.value = area.innerHTML; };
    area.addEventListener('input', sync);
    area.addEventListener('paste', (e) => {
      // Paste as plain text paragraphs to avoid foreign styling
      e.preventDefault();
      const text = (e.clipboardData || window.clipboardData).getData('text/plain');
      const html = text.split(/\n{2,}/).map((p) => `<p>${p.replace(/[&<>]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c])).replace(/\n/g, '<br>')}</p>`).join('');
      document.execCommand('insertHTML', false, html);
      sync();
    });
    $$('[data-cmd]', rte).forEach((btn) => btn.addEventListener('click', async () => {
      const cmd = btn.dataset.cmd;
      if (cmd === 'html') {
        htmlMode = !htmlMode;
        if (htmlMode) { src.value = area.innerHTML; src.hidden = false; area.hidden = true; } else { area.innerHTML = src.value; src.hidden = true; area.hidden = false; }
        btn.classList.toggle('is-on', htmlMode);
        return;
      }
      if (htmlMode) return;
      area.focus();
      if (cmd === 'createLink') {
        const u = window.prompt('Link URL (https://…)');
        if (u && /^(https?:\/\/|\/|mailto:)/i.test(u)) document.execCommand('createLink', false, u);
      } else if (cmd === 'image') {
        const picker = document.createElement('input');
        picker.type = 'file';
        picker.accept = 'image/png,image/jpeg,image/webp,image/gif';
        picker.addEventListener('change', async () => {
          const fd = new FormData();
          fd.append('file', picker.files[0]);
          fd.append('_csrf', CSRF);
          try {
            const res = await fetch(`${BASE}/admin/upload`, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-CSRF-Token': CSRF } });
            const j = await res.json();
            if (j.success) { area.focus(); document.execCommand('insertHTML', false, `<img src="${j.url}" alt="">`); sync(); } else alert(j.message);
          } catch (e) { alert('Upload failed.'); }
        });
        picker.click();
      } else {
        document.execCommand(cmd, false, btn.dataset.val ? `<${btn.dataset.val}>` : null);
      }
      sync();
    }));
    rte.closest('form').addEventListener('submit', () => { if (htmlMode) { area.innerHTML = src.value; } else sync(); });
  });

  // Unsaved changes guard
  $$('form[data-dirty]').forEach((form) => {
    let dirty = false;
    form.addEventListener('input', () => { dirty = true; });
    form.addEventListener('submit', () => { dirty = false; });
    window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
  });
})();
