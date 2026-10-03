/* Shared helpers for all tools: window.TK  (plain ES5 so it also runs on older phones) */
(function () {
  'use strict';
  var TK = {};

  TK.$ = function (id) { return document.getElementById(id); };

  TK.formatBytes = function (n) {
    if (!n) return '0 B';
    var u = ['B', 'KB', 'MB', 'GB'], i = Math.min(3, Math.floor(Math.log(n) / Math.log(1024)));
    return (n / Math.pow(1024, i)).toFixed(i ? 1 : 0) + ' ' + u[i];
  };

  TK.escapeHtml = function (s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  };

  TK.toast = function (msg) {
    var t = document.createElement('div');
    t.className = 'toast-lite';
    t.setAttribute('role', 'status');
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function () { if (t.parentNode) t.parentNode.removeChild(t); }, 3200);
  };

  TK.baseName = function (name) { return String(name).replace(/\.[^.]+$/, ''); };

  /* file-name safe version of any string (keeps letters of every language) */
  TK.slug = function (s, max) {
    s = String(s).replace(/[\\/:*?"<>|\u0000-\u001f]+/g, ' ').replace(/\s+/g, '-').replace(/^-+|-+$/g, '');
    return s.slice(0, max || 40) || 'file';
  };

  TK.extForMime = function (mime) {
    return { 'image/jpeg': 'jpg', 'image/png': 'png', 'image/webp': 'webp', 'image/gif': 'gif', 'image/svg+xml': 'svg' }[mime] || 'png';
  };

  /* Regex with Unicode property escapes when the browser supports them, plain fallback otherwise */
  TK.uniRe = function (src, flags, fallbackSrc) {
    try { return new RegExp(src, (flags || '') + 'u'); } catch (e) { return new RegExp(fallbackSrc || '[A-Za-z0-9]', flags || ''); }
  };

  TK.download = function (blobOrUrl, filename) {
    var url = typeof blobOrUrl === 'string' ? blobOrUrl : URL.createObjectURL(blobOrUrl);
    var a = document.createElement('a');
    a.href = url; a.download = filename;
    document.body.appendChild(a); a.click(); a.remove();
    if (typeof blobOrUrl !== 'string') setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
  };

  TK.downloadText = function (text, filename, mime) {
    TK.download(new Blob([text], { type: mime || 'text/plain;charset=utf-8' }), filename);
  };

  /* Copy text: modern API first, then the old execCommand trick (works on http:// and old browsers) */
  function legacyCopy(text) {
    var ta = document.createElement('textarea');
    ta.value = text; ta.setAttribute('readonly', '');
    ta.style.cssText = 'position:fixed;top:0;left:0;opacity:0;pointer-events:none';
    document.body.appendChild(ta);
    ta.select();
    try { ta.setSelectionRange(0, text.length); } catch (e) {}
    var ok = false;
    try { ok = document.execCommand('copy'); } catch (e2) {}
    ta.parentNode.removeChild(ta);
    return ok;
  }
  TK.copy = function (text, okMsg) {
    if (!text) { TK.toast('Nothing to copy'); return Promise.resolve(false); }
    var done = function (ok) { TK.toast(ok ? (okMsg || 'Copied to clipboard') : 'Could not copy - select the text and press Ctrl+C'); return ok; };
    if (navigator.clipboard && navigator.clipboard.writeText && window.isSecureContext) {
      return navigator.clipboard.writeText(text).then(function () { return done(true); }, function () { return done(legacyCopy(text)); });
    }
    return Promise.resolve(done(legacyCopy(text)));
  };

  /* Canvas helpers. toBlob() returns null when the canvas is too big - turn that into a readable error. */
  TK.MAX_SIDE = 16384;
  TK.MAX_AREA = 120000000;
  TK.clampSize = function (w, h) {
    w = Math.max(1, Math.round(w)); h = Math.max(1, Math.round(h));
    var s = Math.min(1, TK.MAX_SIDE / Math.max(w, h), Math.sqrt(TK.MAX_AREA / (w * h)));
    if (s < 1) { w = Math.max(1, Math.floor(w * s)); h = Math.max(1, Math.floor(h * s)); }
    return { w: w, h: h, clamped: s < 1 };
  };

  TK.canvasToBlob = function (canvas, type, quality) {
    return new Promise(function (res, rej) {
      try {
        canvas.toBlob(function (b) {
          if (b) res(b); else rej(new Error('Your browser could not create this image (it may be too large). Try a smaller size.'));
        }, type, quality);
      } catch (e) { rej(e); }
    });
  };

  /* Blob type may differ from what we asked for (e.g. old Safari cannot encode WEBP and returns PNG) */
  TK.blobExt = function (blob, wanted) {
    var t = blob && blob.type ? blob.type : wanted;
    return TK.extForMime(t);
  };

  TK.loadImage = function (src) {
    return new Promise(function (res, rej) {
      var img = new Image();
      var isFile = typeof src !== 'string';
      var url = isFile ? URL.createObjectURL(src) : src;
      var label = isFile && src.name ? src.name : 'image';
      img.onload = function () {
        if (!img.naturalWidth || !img.naturalHeight) { if (isFile) URL.revokeObjectURL(url); rej(new Error('"' + label + '" has no readable size')); return; }
        img._url = url; res(img);
      };
      img.onerror = function () { if (isFile) URL.revokeObjectURL(url); rej(new Error('Could not open "' + label + '" - unsupported or damaged image')); };
      img.src = url;
    });
  };

  TK.readAsArrayBuffer = function (file) {
    return new Promise(function (res, rej) {
      var r = new FileReader();
      r.onload = function () { res(r.result); };
      r.onerror = function () { rej(r.error || new Error('Could not read file')); };
      r.readAsArrayBuffer(file);
    });
  };

  TK.readAsDataURL = function (file) {
    return new Promise(function (res, rej) {
      var r = new FileReader();
      r.onload = function () { res(r.result); };
      r.onerror = function () { rej(r.error || new Error('Could not read file')); };
      r.readAsDataURL(file);
    });
  };

  /* Drag & drop zone. accept = regex tested against file.type or name */
  TK.dropzone = function (zone, input, onFiles, accept) {
    var filter = function (list) {
      var all = Array.prototype.slice.call(list), arr = all;
      if (accept) arr = all.filter(function (f) { return accept.test(f.type) || accept.test(f.name); });
      if (!arr.length) { TK.toast('Unsupported file type'); return; }
      if (arr.length < all.length) TK.toast((all.length - arr.length) + ' unsupported file(s) skipped');
      onFiles(arr);
    };
    zone.setAttribute('tabindex', '0');
    zone.setAttribute('role', 'button');
    zone.addEventListener('click', function () { input.click(); });
    zone.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); } });
    input.addEventListener('change', function () { var l = Array.prototype.slice.call(input.files); input.value = ''; if (l.length) filter(l); });
    ['dragenter', 'dragover'].forEach(function (ev) {
      zone.addEventListener(ev, function (e) { e.preventDefault(); zone.classList.add('dragover'); });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
      zone.addEventListener(ev, function (e) { e.preventDefault(); zone.classList.remove('dragover'); });
    });
    zone.addEventListener('drop', function (e) { if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) filter(e.dataTransfer.files); });
  };

  /* files: [{name, blob}] */
  TK.downloadZip = function (files, zipName) {
    if (typeof JSZip === 'undefined') { TK.toast('ZIP library failed to load - please refresh the page'); return Promise.resolve(); }
    var zip = new JSZip();
    var used = {};
    files.forEach(function (f) {
      var n = f.name, m = n.match(/^(.*?)(\.[^.]*)?$/), base = m[1], ext = m[2] || '', k = 1;
      while (used[n.toLowerCase()]) { k++; n = base + '-' + k + ext; }
      used[n.toLowerCase()] = 1;
      zip.file(n, f.blob);
    });
    return zip.generateAsync({ type: 'blob' }).then(function (b) { TK.download(b, zipName || 'files.zip'); });
  };

  TK.busy = function (btn, on, label) {
    if (!btn) return;
    if (on) {
      if (!btn.disabled || btn.dataset.old === undefined) { btn.dataset.old = btn.innerHTML; }
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + TK.escapeHtml(label || 'Working...');
    } else {
      btn.disabled = false;
      if (btn.dataset.old !== undefined) { btn.innerHTML = btn.dataset.old; delete btn.dataset.old; }
    }
  };

  /* Shown inside the tool box when a CDN script did not load (blocked network, ad-blocker, offline...) */
  TK.checkLibs = function (names) {
    var missing = names.filter(function (n) { return typeof window[n] === 'undefined'; });
    if (!missing.length) return true;
    var area = document.getElementById('toolArea');
    if (area) {
      var box = document.createElement('div');
      box.className = 'alert alert-danger';
      box.innerHTML = '<strong>This tool could not load a required component (' + TK.escapeHtml(missing.join(', ')) + ').</strong> ' +
        'Please check your internet connection, disable any ad-blocker for this site and <a href="" onclick="location.reload();return false;">reload the page</a>.';
      area.insertBefore(box, area.firstChild);
    }
    return false;
  };

  window.TK = TK;
})();
