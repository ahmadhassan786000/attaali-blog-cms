<ul class="nav nav-pills mb-3" id="b64Tabs">
  <li class="nav-item"><button class="nav-link active" data-tab="text">Text</button></li>
  <li class="nav-item"><button class="nav-link" data-tab="file">File</button></li>
</ul>
<div id="b64TextPane">
  <div class="row g-4">
    <div class="col-lg-6">
      <label class="form-label fw-bold" for="b64In">Input</label>
      <textarea id="b64In" class="form-control tool-text mono" style="min-height:260px" placeholder="Type text here to encode, or a Base64 string to decode..."></textarea>
      <div class="d-flex gap-2 mt-2">
        <button id="b64Encode" class="btn btn-primary flex-fill"><i class="bi bi-lock"></i> Encode</button>
        <button id="b64Decode" class="btn btn-outline-primary flex-fill"><i class="bi bi-unlock"></i> Decode</button>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label fw-bold mb-0" for="b64Out">Result</label>
        <button id="b64Copy" class="btn btn-sm btn-outline-secondary"><i class="bi bi-clipboard"></i> Copy</button>
      </div>
      <textarea id="b64Out" class="form-control tool-text mono" style="min-height:260px" readonly></textarea>
    </div>
  </div>
</div>
<div id="b64FilePane" class="d-none">
  <div id="b64Drop" class="drop-zone mb-3"><i class="bi bi-cloud-arrow-up"></i><p class="mb-0 fw-bold">Click or drop any file here to encode as Base64</p><input id="b64FileInput" type="file" hidden></div>
  <textarea id="b64FileOut" class="form-control tool-text mono" style="min-height:200px" readonly placeholder="Base64 data URI will appear here..."></textarea>
  <button id="b64FileCopy" class="btn btn-sm btn-outline-secondary mt-2"><i class="bi bi-clipboard"></i> Copy</button>
</div>
<script>
(function(){
  var $=TK.$;
  document.querySelectorAll('#b64Tabs [data-tab]').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.querySelectorAll('#b64Tabs [data-tab]').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      $('b64TextPane').classList.toggle('d-none', btn.dataset.tab!=='text');
      $('b64FilePane').classList.toggle('d-none', btn.dataset.tab!=='file');
    });
  });
  function utf8Encode(str){ return btoa(unescape(encodeURIComponent(str))); }
  function utf8Decode(b64){ return decodeURIComponent(escape(atob(b64))); }

  /* Accepts real-world Base64 variants: data: URI prefix, URL-safe alphabet (- _), missing "="
     padding, and whitespace/newlines from copy-pasting. */
  function normalizeB64(s){
    s=s.trim();
    var m=s.match(/^data:[^;]*;base64,(.*)$/s);
    if(m) s=m[1];
    s=s.replace(/\s+/g,'');
    s=s.replace(/-/g,'+').replace(/_/g,'/');
    var pad=s.length%4;
    if(pad===2) s+='=='; else if(pad===3) s+='=';
    if(!s || !/^[A-Za-z0-9+/]*={0,2}$/.test(s)) throw new Error('bad-chars');
    return s;
  }
  function looksBinary(s){
    var bad=0;
    for(var i=0;i<s.length;i++){ var c=s.charCodeAt(i); if(c===0xFFFD || (c<32 && c!==9 && c!==10 && c!==13)) bad++; }
    return bad>0 && bad/s.length>0.01;
  }

  $('b64Encode').addEventListener('click', function(){
    if(!$('b64In').value){ TK.toast('Please enter some text'); return; }
    try{ $('b64Out').value=utf8Encode($('b64In').value); } catch(e){ TK.toast('Could not encode this text'); }
  });
  $('b64Decode').addEventListener('click', function(){
    var raw=$('b64In').value;
    if(!raw.trim()){ TK.toast('Please enter a Base64 string'); return; }
    var norm;
    try{ norm=normalizeB64(raw); } catch(e){ TK.toast('Invalid Base64 string'); return; }
    try{
      var decoded=utf8Decode(norm);
      if(looksBinary(decoded)){ $('b64Out').value=''; TK.toast('This looks like binary (non-text) data - open the File tab to decode files, or the text would come out as garbage.'); return; }
      $('b64Out').value=decoded;
    } catch(e){ TK.toast('Invalid Base64 string'); }
  });
  $('b64Copy').addEventListener('click', function(){ TK.copy($('b64Out').value); });
  TK.dropzone($('b64Drop'), $('b64FileInput'), function(list){
    TK.readAsDataURL(list[0]).then(function(dataUrl){ $('b64FileOut').value=dataUrl; }).catch(function(err){ TK.toast(err.message||'Could not read this file'); });
  });
  $('b64FileCopy').addEventListener('click', function(){ TK.copy($('b64FileOut').value); });
})();
</script>
