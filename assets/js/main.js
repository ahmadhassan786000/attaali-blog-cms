/* Site-wide JavaScript */
(function () {
  'use strict';

  // Theme toggle
  var btn = document.getElementById('themeToggle');
  function syncIcon() {
    if (!btn) return;
    var dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    btn.innerHTML = dark ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon-stars"></i>';
  }
  syncIcon();
  if (btn) {
    btn.addEventListener('click', function () {
      var cur = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-bs-theme', cur);
      try { localStorage.setItem('theme', cur); } catch (e) {}
      syncIcon();
    });
  }

  // Cookie notice
  var bar = document.getElementById('cookieBar');
  try {
    if (bar && !localStorage.getItem('cookieOk')) bar.classList.remove('d-none');
  } catch (e) {}
  var ok = document.getElementById('cookieOk');
  if (ok) ok.addEventListener('click', function () {
    try { localStorage.setItem('cookieOk', '1'); } catch (e) {}
    bar.classList.add('d-none');
  });

  // Back to top
  var top = document.getElementById('toTop');
  if (top) {
    window.addEventListener('scroll', function () {
      top.classList.toggle('d-none', window.scrollY < 500);
    }, { passive: true });
    top.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
  }

  // Live tool filter (home + tools page)
  var filterInput = document.getElementById('toolFilter');
  if (filterInput) {
    var cards = document.querySelectorAll('[data-tool-card]');
    var pills = document.querySelectorAll('[data-cat-pill]');
    var activeCat = 'all';
    var apply = function () {
      var q = filterInput.value.trim().toLowerCase();
      var shown = 0;
      cards.forEach(function (c) {
        var okText = !q || c.getAttribute('data-search').indexOf(q) !== -1;
        var okCat = activeCat === 'all' || c.getAttribute('data-cat') === activeCat;
        var show = okText && okCat;
        c.classList.toggle('d-none', !show);
        if (show) shown++;
      });
      var empty = document.getElementById('toolEmpty');
      if (empty) empty.classList.toggle('d-none', shown > 0);
    };
    filterInput.addEventListener('input', apply);
    pills.forEach(function (p) {
      p.addEventListener('click', function (ev) {
        ev.preventDefault();
        activeCat = p.getAttribute('data-cat-pill');
        pills.forEach(function (x) { x.classList.remove('active'); });
        p.classList.add('active');
        apply();
      });
    });
  }

  // Copy buttons: <button data-copy="#selector">
  document.addEventListener('click', function (ev) {
    var b = ev.target.closest('[data-copy]');
    if (!b) return;
    var el = document.querySelector(b.getAttribute('data-copy'));
    if (!el) return;
    var text = el.value !== undefined ? el.value : el.textContent;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(text).then(function () { window.TK && TK.toast('Copied to clipboard'); });
    } else {
      el.select && el.select(); document.execCommand('copy'); window.TK && TK.toast('Copied to clipboard');
    }
  });
})();
