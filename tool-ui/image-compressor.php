<div class="row g-4">
  <div class="col-lg-5">
    <div id="icDrop" class="drop-zone">
      <i class="bi bi-cloud-arrow-up"></i>
      <p class="mb-1 fw-bold">Click or drop images here</p>
      <p class="small text-muted mb-0">JPG, PNG or WEBP - multiple files supported</p>
      <input id="icInput" type="file" accept="image/*" multiple hidden>
    </div>
    <div id="icList" class="mt-3"></div>
    <div class="row g-2 mt-3">
      <div class="col-6"><label class="form-label small" for="icQuality">Quality: <span id="icQVal">80</span>%</label><input id="icQuality" type="range" class="form-range" min="10" max="100" value="80"></div>
      <div class="col-6"><label class="form-label small" for="icFormat">Output format</label><select id="icFormat" class="form-select form-select-sm"><option value="image/jpeg">JPG</option><option value="image/webp">WEBP</option><option value="image/png">PNG</option></select></div>
      <div class="col-12"><label class="form-label small" for="icMaxW">Max width (px, optional)</label><input id="icMaxW" type="number" class="form-control form-control-sm" placeholder="e.g. 1600" min="1" max="10000"></div>
    </div>
    <button id="icGo" class="btn btn-primary btn-lg w-100 mt-3" disabled><i class="bi bi-arrows-collapse"></i> Compress Images</button>
  </div>
  <div class="col-lg-7">
    <div id="icResults" class="row g-3"></div>
    <button id="icZip" class="btn btn-success mt-3 d-none"><i class="bi bi-file-zip"></i> Download all as ZIP</button>
  </div>
</div>
<script>
(function(){
  var $=TK.$, files=[], results=[];
  $('icQuality').addEventListener('input', function(){ $('icQVal').textContent=this.value; });

  function renderList(){
    var list=$('icList'); list.innerHTML='';
    files.forEach(function(f,i){
      var div=document.createElement('div'); div.className='file-item';
      div.innerHTML='<span class="name">'+TK.escapeHtml(f.name)+'</span><span class="small text-muted">'+TK.formatBytes(f.size)+'</span>'+
        '<button class="btn btn-sm btn-outline-danger" data-del="'+i+'" aria-label="Remove"><i class="bi bi-trash"></i></button>';
      list.appendChild(div);
    });
    $('icGo').disabled = files.length===0;
  }
  TK.dropzone($('icDrop'), $('icInput'), function(list){ files=files.concat(list); renderList(); }, /^image\//);
  $('icList').addEventListener('click', function(e){
    var b=e.target.closest('[data-del]'); if(!b) return;
    files.splice(+b.dataset.del,1); renderList();
  });

  function familyOf(mime){ return mime==='image/jpeg' ? 'jpeg' : mime==='image/webp' ? 'webp' : mime==='image/png' ? 'png' : 'other'; }

  /* Re-encoding an already-compressed photo (e.g. one that came from WhatsApp/Instagram) can end up
     BIGGER than the original, because the browser's built-in encoder is not as efficient as the one
     that made the original file. When that happens - and the user isn't deliberately converting to a
     different format - step the quality down and, if nothing beats the original, just keep the
     original file instead of handing back something bigger than what they started with. */
  function bestEncode(canvas, mime, startQ, originalSize, isRecompress){
    var steps = isRecompress ? [startQ, 0.7, 0.55, 0.4] : [startQ];
    var seen={}, chain=Promise.resolve(null);
    steps.forEach(function(q){
      q=Math.max(0.1, Math.min(1, q));
      var key=Math.round(q*100); if(seen[key]) return; seen[key]=1;
      chain=chain.then(function(best){
        if(best && best.size<originalSize) return best; /* already found something smaller - stop trying */
        return TK.canvasToBlob(canvas, mime, mime==='image/png'?undefined:q).then(function(blob){
          if(!best || blob.size<best.size) return blob;
          return best;
        });
      });
    });
    return chain;
  }

  $('icGo').addEventListener('click', function(){
    if(!files.length) return;
    var btn=this; TK.busy(btn,true,'Compressing...');
    $('icResults').innerHTML=''; results=[]; $('icZip').classList.add('d-none');
    var q=parseInt($('icQuality').value,10)/100, mime=$('icFormat').value;
    var maxW=Math.min(10000, parseInt($('icMaxW').value,10)||0);
    var chain=Promise.resolve(), errs=0;
    files.forEach(function(file){
      chain=chain.then(function(){ return TK.loadImage(file); }).then(function(img){
        var w=img.naturalWidth, h=img.naturalHeight;
        if(maxW && w>maxW){ h=Math.round(h*(maxW/w)); w=maxW; }
        var s=TK.clampSize(w,h); w=s.w; h=s.h;
        var canvas=document.createElement('canvas'); canvas.width=w; canvas.height=h;
        var ctx=canvas.getContext('2d');
        if(mime==='image/jpeg'){ ctx.fillStyle='#fff'; ctx.fillRect(0,0,w,h); }
        ctx.drawImage(img,0,0,w,h);
        URL.revokeObjectURL(img._url);
        var isRecompress = mime!=='image/png' && familyOf(mime)===familyOf(file.type) && !maxW;
        return bestEncode(canvas, mime, q, file.size, isRecompress).then(function(blob){
          var usedOriginal=false;
          if(isRecompress && blob.size>=file.size){ blob=file; usedOriginal=true; }
          var name = usedOriginal ? file.name : TK.baseName(file.name)+'.'+TK.blobExt(blob, mime);
          results.push({name:name, blob:blob});
          var diff = blob.size - file.size;
          var pct = file.size ? Math.round(Math.abs(diff)/file.size*100) : 0;
          var tag = usedOriginal ? '<span class="text-muted">(already optimal - original kept)</span>'
            : diff<0 ? '<span class="text-success fw-bold">(-'+pct+'%)</span>'
            : diff>0 ? '<span class="text-danger fw-bold">(+'+pct+'% larger)</span>'
            : '<span class="text-muted">(same size)</span>';
          var url=URL.createObjectURL(blob);
          var div=document.createElement('div'); div.className='col-6 col-md-4';
          div.innerHTML='<div class="result-thumb"><img src="'+url+'" alt="">'+
            '<div class="small mt-1 text-truncate" title="'+TK.escapeHtml(file.name)+'">'+TK.escapeHtml(file.name)+'</div>'+
            '<div class="small text-muted">'+TK.formatBytes(file.size)+' &rarr; '+TK.formatBytes(blob.size)+' '+tag+'</div>'+
            '<a class="btn btn-sm btn-outline-primary mt-1 w-100" href="'+url+'" download="'+TK.escapeHtml(name)+'"><i class="bi bi-download"></i></a></div>';
          $('icResults').appendChild(div);
        });
      }).catch(function(err){ errs++; console.error(err); TK.toast((err&&err.message)||'Could not process "'+file.name+'"'); });
    });
    chain.then(function(){
      TK.busy(btn,false);
      if(results.length>1) $('icZip').classList.remove('d-none');
      if(!results.length && errs) TK.toast('None of the images could be compressed');
    });
  });
  $('icZip').addEventListener('click', function(){ TK.downloadZip(results,'compressed-images.zip'); });
})();
</script>
