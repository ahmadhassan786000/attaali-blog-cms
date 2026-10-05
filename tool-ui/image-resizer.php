<div class="row g-4">
  <div class="col-lg-5">
    <div id="irDrop" class="drop-zone">
      <i class="bi bi-cloud-arrow-up"></i>
      <p class="mb-1 fw-bold">Click or drop an image here</p>
      <input id="irInput" type="file" accept="image/*" hidden>
    </div>
    <div id="irOrig" class="small text-muted mt-2"></div>
    <div class="row g-2 mt-2">
      <div class="col-6"><label class="form-label small" for="irW">Width (px)</label><input id="irW" type="number" class="form-control form-control-sm" min="1" max="20000" disabled></div>
      <div class="col-6"><label class="form-label small" for="irH">Height (px)</label><input id="irH" type="number" class="form-control form-control-sm" min="1" max="20000" disabled></div>
      <div class="col-12 form-check">
        <input class="form-check-input" type="checkbox" id="irLock" checked>
        <label class="form-check-label small" for="irLock">Lock aspect ratio</label>
      </div>
      <div class="col-12"><label class="form-label small" for="irPercent">Or resize by percentage</label>
        <input id="irPercent" type="range" class="form-range" min="10" max="200" value="100" disabled>
        <div class="small text-muted text-center"><span id="irPercentVal">100</span>%</div>
      </div>
      <div class="col-6"><label class="form-label small" for="irFormat">Format</label><select id="irFormat" class="form-select form-select-sm"><option value="image/jpeg">JPG</option><option value="image/png">PNG</option><option value="image/webp">WEBP</option></select></div>
      <div class="col-6"><label class="form-label small" for="irQuality">Quality</label><input id="irQuality" type="range" class="form-range" min="10" max="100" value="90"></div>
    </div>
    <button id="irGo" class="btn btn-primary btn-lg w-100 mt-3" disabled><i class="bi bi-download"></i> Resize &amp; Download</button>
  </div>
  <div class="col-lg-7">
    <label class="form-label fw-bold">Preview</label>
    <div class="preview-box"><canvas id="irCanvas"></canvas></div>
    <div id="irNewSize" class="text-center small text-muted mt-2"></div>
  </div>
</div>
<script>
(function(){
  var $=TK.$, img=null, origW=0, origH=0, origName='resized', canvas=$('irCanvas'), ctx=canvas.getContext('2d');
  var MAXPX=20000;

  TK.dropzone($('irDrop'), $('irInput'), function(list){
    var file=list[0];
    TK.loadImage(file).then(function(im){
      if(img) URL.revokeObjectURL(img._url);
      img=im; origW=im.naturalWidth; origH=im.naturalHeight; origName=TK.baseName(file.name);
      $('irW').value=origW; $('irH').value=origH; $('irPercent').value=100; $('irPercentVal').textContent=100;
      $('irOrig').textContent='Original: '+origW+' x '+origH+'px, '+TK.formatBytes(file.size);
      ['irW','irH','irPercent'].forEach(function(id){ $(id).disabled=false; });
      $('irGo').disabled=false;
      draw();
    }).catch(function(err){ TK.toast(err.message||'Could not open this image'); });
  }, /^image\//);

  function clampInt(v, fallback){ v=Math.round(parseFloat(v)); if(!v || v<1) return 1; if(v>MAXPX) return MAXPX; return v; }

  function draw(){
    if(!img) return;
    var w=clampInt($('irW').value, origW), h=clampInt($('irH').value, origH);
    var s=TK.clampSize(w,h); w=s.w; h=s.h;
    canvas.width=w; canvas.height=h;
    ctx.clearRect(0,0,w,h); ctx.drawImage(img,0,0,w,h);
    $('irNewSize').textContent='New size: '+w+' x '+h+'px'+(s.clamped?' (limited to a safe maximum)':'');
  }

  $('irW').addEventListener('input', function(){
    var w=clampInt(this.value); this.value=w;
    if($('irLock').checked && origW){ $('irH').value=Math.max(1,Math.round(origH*(w/origW))); }
    $('irPercent').value=Math.min(200,Math.max(10,Math.round(w/origW*100)));
    $('irPercentVal').textContent=$('irPercent').value;
    draw();
  });
  $('irH').addEventListener('input', function(){
    var h=clampInt(this.value); this.value=h;
    if($('irLock').checked && origH){ $('irW').value=Math.max(1,Math.round(origW*(h/origH))); }
    draw();
  });
  $('irPercent').addEventListener('input', function(){
    $('irPercentVal').textContent=this.value;
    if(!origW) return;
    $('irW').value=Math.max(1,Math.round(origW*this.value/100));
    $('irH').value=Math.max(1,Math.round(origH*this.value/100));
    draw();
  });

  $('irGo').addEventListener('click', function(){
    if(!img) return;
    var mime=$('irFormat').value, q=parseInt($('irQuality').value,10)/100;
    var out=canvas;
    if(mime==='image/jpeg'){
      var tmp=document.createElement('canvas'); tmp.width=canvas.width; tmp.height=canvas.height;
      var tctx=tmp.getContext('2d'); tctx.fillStyle='#fff'; tctx.fillRect(0,0,tmp.width,tmp.height); tctx.drawImage(canvas,0,0);
      out=tmp;
    }
    var btn=this; TK.busy(btn,true,'Preparing...');
    TK.canvasToBlob(out, mime, mime==='image/png'?undefined:q).then(function(b){
      TK.download(b, origName+'-'+canvas.width+'x'+canvas.height+'.'+TK.blobExt(b,mime));
      TK.busy(btn,false);
    }).catch(function(err){ TK.toast(err.message||'Could not create this image'); TK.busy(btn,false); });
  });
})();
</script>
