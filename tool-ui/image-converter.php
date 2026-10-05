<div class="row g-4">
  <div class="col-lg-5">
    <div id="cvDrop" class="drop-zone">
      <i class="bi bi-cloud-arrow-up"></i>
      <p class="mb-1 fw-bold">Click or drop images here</p>
      <p class="small text-muted mb-0">Multiple files supported</p>
      <input id="cvInput" type="file" accept="image/*" multiple hidden>
    </div>
    <div id="cvList" class="mt-3"></div>
    <div class="row g-2 mt-3">
      <div class="col-6"><label class="form-label small" for="cvFormat">Convert to</label><select id="cvFormat" class="form-select form-select-sm"><option value="image/jpeg">JPG</option><option value="image/png">PNG</option><option value="image/webp">WEBP</option></select></div>
      <div class="col-6"><label class="form-label small" for="cvQuality">Quality</label><input id="cvQuality" type="range" class="form-range" min="10" max="100" value="90"></div>
    </div>
    <button id="cvGo" class="btn btn-primary btn-lg w-100 mt-3" disabled><i class="bi bi-arrow-left-right"></i> Convert Images</button>
  </div>
  <div class="col-lg-7">
    <div id="cvResults" class="row g-3"></div>
    <button id="cvZip" class="btn btn-success mt-3 d-none"><i class="bi bi-file-zip"></i> Download all as ZIP</button>
  </div>
</div>
<script>
(function(){
  var $=TK.$, files=[], results=[];
  function renderList(){
    var list=$('cvList'); list.innerHTML='';
    files.forEach(function(f,i){
      var div=document.createElement('div'); div.className='file-item';
      div.innerHTML='<span class="name">'+TK.escapeHtml(f.name)+'</span><span class="small text-muted">'+TK.formatBytes(f.size)+'</span>'+
        '<button class="btn btn-sm btn-outline-danger" data-del="'+i+'" aria-label="Remove"><i class="bi bi-trash"></i></button>';
      list.appendChild(div);
    });
    $('cvGo').disabled = files.length===0;
  }
  TK.dropzone($('cvDrop'), $('cvInput'), function(list){ files=files.concat(list); renderList(); }, /^image\//);
  $('cvList').addEventListener('click', function(e){
    var b=e.target.closest('[data-del]'); if(!b) return;
    files.splice(+b.dataset.del,1); renderList();
  });

  $('cvGo').addEventListener('click', function(){
    if(!files.length) return;
    var btn=this; TK.busy(btn,true,'Converting...');
    $('cvResults').innerHTML=''; results=[]; $('cvZip').classList.add('d-none');
    var mime=$('cvFormat').value, q=parseInt($('cvQuality').value,10)/100;
    var chain=Promise.resolve();
    files.forEach(function(file){
      chain=chain.then(function(){ return TK.loadImage(file); }).then(function(img){
        var s=TK.clampSize(img.naturalWidth, img.naturalHeight);
        var canvas=document.createElement('canvas'); canvas.width=s.w; canvas.height=s.h;
        var ctx=canvas.getContext('2d');
        if(mime==='image/jpeg'){ ctx.fillStyle='#fff'; ctx.fillRect(0,0,canvas.width,canvas.height); }
        ctx.drawImage(img,0,0,s.w,s.h);
        URL.revokeObjectURL(img._url);
        return TK.canvasToBlob(canvas, mime, mime==='image/png'?undefined:q).then(function(blob){
          var ext=TK.blobExt(blob, mime);
          var name=TK.baseName(file.name)+'.'+ext;
          results.push({name:name, blob:blob});
          var url=URL.createObjectURL(blob);
          var div=document.createElement('div'); div.className='col-6 col-md-4';
          div.innerHTML='<div class="result-thumb"><img src="'+url+'" alt=""><div class="small mt-1 text-truncate" title="'+TK.escapeHtml(name)+'">'+TK.escapeHtml(name)+'</div>'+
            '<a class="btn btn-sm btn-outline-primary mt-1 w-100" href="'+url+'" download="'+TK.escapeHtml(name)+'"><i class="bi bi-download"></i></a></div>';
          $('cvResults').appendChild(div);
        });
      }).catch(function(err){ console.error(err); TK.toast(err.message||'Could not convert "'+file.name+'"'); });
    });
    chain.then(function(){ TK.busy(btn,false); if(results.length>1) $('cvZip').classList.remove('d-none'); });
  });
  $('cvZip').addEventListener('click', function(){ TK.downloadZip(results,'converted-images.zip'); });
})();
</script>
