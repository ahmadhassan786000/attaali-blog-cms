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
  var $=TK.$;
  var lastCanvas=null;

  function wifiEscape(s){
    return String(s).replace(/([\\;,:"])/g, '\\$1');
  }

  function buildValue(){
    var type=$('qrType').value;

    if(type==='phone'){
      var digits=$('qrPhoneVal').value.trim().replace(/[^\d+]/g,'');
      return digits ? 'tel:'+digits : '';
    }

    if(type==='email'){
      var addr=$('qrEmailVal').value.trim();

      if(!addr){
        return '';
      }

      var subject=$('qrEmailSubject').value.trim();

      return 'mailto:'+
        encodeURIComponent(addr).replace(/%40/g,'@')+
        (subject ? '?subject='+encodeURIComponent(subject) : '');
    }

    if(type==='wifi'){
      var ssid=$('qrWifiSsid').value;
      var sec=$('qrWifiSec').value;

      if(!ssid){
        return '';
      }

      return 'WIFI:T:'+sec+
        ';S:'+wifiEscape(ssid)+
        ';P:'+(sec==='nopass' ? '' : wifiEscape($('qrWifiPass').value))+
        ';;';
    }

    return $('qrTextVal').value.trim();
  }

  function boxes(){
    ['qrTextBox','qrPhoneBox','qrEmailBox','qrWifiBox'].forEach(function(id){
      $(id).classList.add('d-none');
    });

    var type=$('qrType').value;
    var boxId='qrTextBox';

    if(type==='phone'){
      boxId='qrPhoneBox';
    }else if(type==='email'){
      boxId='qrEmailBox';
    }else if(type==='wifi'){
      boxId='qrWifiBox';
    }

    $(boxId).classList.remove('d-none');
  }

  function showError(message){
    var msg=$('qrMsg');

    if(msg.textContent===message){
      return;
    }

    msg.textContent=message;
  }

  function draw(){
    var out=$('qrOut');

    out.innerHTML='';
    $('qrMsg').textContent='';
    $('qrDownload').disabled=true;
    lastCanvas=null;

    var value=buildValue();

    if(!value){
      return;
    }

    if(typeof QRCode==='undefined'){
      showError('QR Code component could not be loaded. Please reload the page.');
      return;
    }

    var size=Math.min(
      1000,
      Math.max(100,parseInt($('qrSize').value,10)||300)
    );

    var fg=$('qrColor').value;
    var bg=$('qrBg').value;

    try{
      var holder=document.createElement('div');

      holder.style.position='absolute';
      holder.style.left='-99999px';
      holder.style.top='-99999px';

      document.body.appendChild(holder);

      new QRCode(holder,{
        text:value,
        width:size,
        height:size,
        colorDark:fg,
        colorLight:bg,
        correctLevel:QRCode.CorrectLevel.M
      });

      setTimeout(function(){
        var sourceCanvas=holder.querySelector('canvas');
        var sourceImage=holder.querySelector('img');

        if(!sourceCanvas && !sourceImage){
          holder.remove();
          showError('Could not generate a QR code for this content.');
          return;
        }

        var canvas=document.createElement('canvas');
        canvas.width=size;
        canvas.height=size;

        var ctx=canvas.getContext('2d');
        ctx.fillStyle=bg;
        ctx.fillRect(0,0,size,size);

        if(sourceCanvas){
          ctx.drawImage(sourceCanvas,0,0,size,size);
          finish(canvas,holder);
          return;
        }

        sourceImage.onload=function(){
          ctx.drawImage(sourceImage,0,0,size,size);
          finish(canvas,holder);
        };
      },50);

    }catch(err){
      console.error(err);
      showError('Could not generate a QR code for this content.');
    }
  }

  function finish(canvas,holder){
    holder.remove();

    canvas.style.maxWidth='100%';
    canvas.setAttribute('aria-label','QR code');

    $('qrOut').appendChild(canvas);

    lastCanvas=canvas;
    $('qrDownload').disabled=false;
  }

  $('qrType').addEventListener('change',function(){
    boxes();
    draw();
  });

  document.querySelectorAll(
    '#qrTextBox input,#qrPhoneBox input,#qrEmailBox input,'+
    '#qrWifiBox input,#qrWifiBox select,#qrSize,#qrColor,#qrBg'
  ).forEach(function(el){
    el.addEventListener('input',draw);
    el.addEventListener('change',draw);
  });

  boxes();
  draw();

  $('qrDownload').addEventListener('click',function(){
    if(!lastCanvas){
      return;
    }

    TK.canvasToBlob(lastCanvas,'image/png')
      .then(function(blob){
        TK.download(blob,'qr-code.png');
      })
      .catch(function(err){
        TK.toast(err.message||'Could not download the QR code');
      });
  });
})();
</script>
