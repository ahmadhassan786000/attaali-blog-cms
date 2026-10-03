<div class="row g-4">
  <div class="col-lg-6">
    <label class="form-label fw-bold" for="qrType">QR type</label>
    <select id="qrType" class="form-select mb-3">
      <option value="text">Text / URL</option>
      <option value="phone">Phone number</option>
      <option value="email">Email</option>
      <option value="wifi">Wi-Fi network</option>
    </select>

    <div id="qrTextBox"><label class="form-label small" for="qrTextVal">Text or URL</label><input id="qrTextVal" class="form-control mb-3" placeholder="https://example.com" value="https://attaali.com"></div>
    <div id="qrPhoneBox" class="d-none"><label class="form-label small" for="qrPhoneVal">Phone number (with country code)</label><input id="qrPhoneVal" class="form-control mb-3" placeholder="+923001234567"></div>
    <div id="qrEmailBox" class="d-none">
      <label class="form-label small" for="qrEmailVal">Email address</label><input id="qrEmailVal" class="form-control mb-2" placeholder="name@example.com">
      <label class="form-label small" for="qrEmailSubject">Subject (optional)</label><input id="qrEmailSubject" class="form-control mb-3">
    </div>
    <div id="qrWifiBox" class="d-none">
      <label class="form-label small" for="qrWifiSsid">Network name (SSID)</label><input id="qrWifiSsid" class="form-control mb-2">
      <label class="form-label small" for="qrWifiPass">Password</label><input id="qrWifiPass" class="form-control mb-2">
      <label class="form-label small" for="qrWifiSec">Security</label><select id="qrWifiSec" class="form-select mb-3"><option value="WPA">WPA/WPA2</option><option value="WEP">WEP</option><option value="nopass">None</option></select>
    </div>

    <div class="row g-2">
      <div class="col-6"><label class="form-label small" for="qrSize">Size (px)</label><input id="qrSize" type="number" class="form-control form-control-sm" value="300" min="100" max="1000" step="10"></div>
      <div class="col-3"><label class="form-label small" for="qrColor">Color</label><input id="qrColor" type="color" class="form-control form-control-sm form-control-color w-100" value="#000000"></div>
      <div class="col-3"><label class="form-label small" for="qrBg">Background</label><input id="qrBg" type="color" class="form-control form-control-sm form-control-color w-100" value="#ffffff"></div>
    </div>
    <div id="qrMsg" class="small text-danger mt-2"></div>
  </div>
  <div class="col-lg-6 d-flex flex-column align-items-center justify-content-center text-center">
    <div id="qrOut" class="mb-3"></div>
    <button id="qrDownload" class="btn btn-primary btn-lg" disabled><i class="bi bi-download"></i> Download PNG</button>
  </div>
</div>
<script>
(function(){
  var $=TK.$, lastCanvas=null;

  /* Escape ; , : \ as required by the Wi-Fi QR payload spec (MECARD-style). */
  function wifiEscape(s){ return String(s).replace(/([\\;,:"])/g, '\\$1'); }

  function buildValue(){
    var type=$('qrType').value;
    if(type==='phone'){
      var digits=$('qrPhoneVal').value.trim().replace(/[^\d+]/g,'');
      return digits ? 'tel:'+digits : '';
    }
    if(type==='email'){
      var addr=$('qrEmailVal').value.trim();
      if(!addr) return '';
      var s=$('qrEmailSubject').value.trim();
      return 'mailto:'+encodeURIComponent(addr).replace(/%40/g,'@')+(s?('?subject='+encodeURIComponent(s)):'');
    }
    if(type==='wifi'){
      var ssid=$('qrWifiSsid').value, sec=$('qrWifiSec').value;
      if(!ssid) return '';
      return 'WIFI:T:'+sec+';S:'+wifiEscape(ssid)+';P:'+(sec==='nopass'?'':wifiEscape($('qrWifiPass').value))+';;';
    }
    return $('qrTextVal').value.trim();
  }

  function boxes(){
    ['qrTextBox','qrPhoneBox','qrEmailBox','qrWifiBox'].forEach(function(id){ $(id).classList.add('d-none'); });
    var t=$('qrType').value;
    $(t==='text'?'qrTextBox':t==='phone'?'qrPhoneBox':t==='email'?'qrEmailBox':'qrWifiBox').classList.remove('d-none');
  }

  function draw(){
    var out=$('qrOut'); out.innerHTML=''; $('qrMsg').textContent=''; lastCanvas=null;
    var value=buildValue();
    if(!value){ $('qrDownload').disabled=true; return; }
    if(typeof qrcode==='undefined'){ TK.checkLibs(['qrcode']); return; }
    var size=Math.min(1000,Math.max(100,parseInt($('qrSize').value,10)||300));
    var fg=$('qrColor').value, bg=$('qrBg').value;
    try{
      if(qrcode.stringToBytesFuncs && qrcode.stringToBytesFuncs['UTF-8']){ qrcode.stringToBytes = qrcode.stringToBytesFuncs['UTF-8']; }
      var qr=qrcode(0, 'M'); /* typeNumber 0 = auto-pick the smallest size that fits the data */
      qr.addData(value); qr.make();
      var count=qr.getModuleCount();
      var margin=4; /* modules of quiet zone, per the QR spec */
      var cell=Math.max(1, Math.floor(size/(count+margin*2)));
      var total=cell*(count+margin*2);
      var canvas=document.createElement('canvas'); canvas.width=total; canvas.height=total;
      var ctx=canvas.getContext('2d');
      ctx.fillStyle=bg; ctx.fillRect(0,0,total,total);
      ctx.fillStyle=fg;
      for(var r=0;r<count;r++){
        for(var c=0;c<count;c++){
          if(qr.isDark(r,c)) ctx.fillRect((c+margin)*cell,(r+margin)*cell,cell,cell);
        }
      }
      canvas.style.maxWidth='100%'; canvas.setAttribute('aria-label','QR code');
      out.appendChild(canvas);
      lastCanvas=canvas;
      $('qrDownload').disabled=false;
    }catch(err){
      console.error(err);
      $('qrMsg').textContent = /code length overflow|too (long|much)/i.test(err.message||'') ? 'This content is too long for a QR code - please shorten it.' : 'Could not generate a QR code for this content.';
      $('qrDownload').disabled=true;
    }
  }

  $('qrType').addEventListener('change', function(){ boxes(); draw(); });
  document.querySelectorAll('#qrTextBox input,#qrPhoneBox input,#qrEmailBox input,#qrWifiBox input,#qrWifiBox select,#qrSize,#qrColor,#qrBg').forEach(function(el){
    el.addEventListener('input', draw);
  });
  boxes(); draw();

  $('qrDownload').addEventListener('click', function(){
    if(!lastCanvas) return;
    TK.canvasToBlob(lastCanvas,'image/png').then(function(b){ TK.download(b,'qr-code.png'); }).catch(function(err){ TK.toast(err.message||'Could not download the QR code'); });
  });
})();
</script>
