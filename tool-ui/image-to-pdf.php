<div class="row g-4">
  <div class="col-lg-5">
    <div id="ipDrop" class="drop-zone">
      <i class="bi bi-cloud-arrow-up"></i>
      <p class="mb-1 fw-bold">Click or drop images here</p>
      <p class="small text-muted mb-0">JPG, PNG, WEBP, GIF - multiple files supported</p>
      <input id="ipInput" type="file" accept="image/*" multiple hidden>
    </div>
    <div id="ipList" class="mt-3"></div>
  </div>
  <div class="col-lg-7">
    <div class="row g-2 mb-3">
      <div class="col-6 col-md-3"><label class="form-label small" for="ipPage">Page size</label><select id="ipPage" class="form-select form-select-sm"><option value="a4">A4</option><option value="letter">Letter</option><option value="fit">Fit to image</option></select></div>
      <div class="col-6 col-md-3"><label class="form-label small" for="ipOrient">Orientation</label><select id="ipOrient" class="form-select form-select-sm"><option value="auto">Auto (best fit)</option><option value="p">Portrait</option><option value="l">Landscape</option></select></div>
      <div class="col-6 col-md-3"><label class="form-label small" for="ipMargin">Margin (mm)</label><input id="ipMargin" type="number" class="form-control form-control-sm" value="8" min="0" max="30"></div>
      <div class="col-6 col-md-3"><label class="form-label small" for="ipQuality">Quality</label><select id="ipQuality" class="form-select form-select-sm"><option value="high">High (original size)</option><option value="balanced">Balanced (smaller file)</option><option value="small">Small (for e-mail)</option></select></div>
    </div>
    <button id="ipMake" class="btn btn-primary btn-lg w-100" disabled><i class="bi bi-download"></i> Create PDF (<span id="ipCount">0</span> images)</button>
    <p class="small text-muted mt-2 mb-0">Tip: use the arrows to reorder images before creating the PDF. Transparent areas become white.</p>
  </div>
</div>
<script>
(function(){
  var $=TK.$, files=[];
  var QUALITY={ high:{q:0.95,max:0}, balanced:{q:0.85,max:2600}, small:{q:0.7,max:1600} };

  function render(){
    var list=$('ipList'); list.innerHTML='';
    files.forEach(function(f,i){
      var div=document.createElement('div'); div.className='file-item';
      div.innerHTML='<img src="'+f.url+'" alt=""><span class="name">'+TK.escapeHtml(f.file.name)+'</span>'+
        '<span class="small text-muted">'+TK.formatBytes(f.file.size)+'</span>'+
        '<button class="btn btn-sm btn-outline-secondary" data-up="'+i+'" aria-label="Move up"><i class="bi bi-arrow-up"></i></button>'+
        '<button class="btn btn-sm btn-outline-secondary" data-down="'+i+'" aria-label="Move down"><i class="bi bi-arrow-down"></i></button>'+
        '<button class="btn btn-sm btn-outline-danger" data-del="'+i+'" aria-label="Remove"><i class="bi bi-trash"></i></button>';
      list.appendChild(div);
    });
    $('ipCount').textContent=files.length;
    $('ipMake').disabled = files.length===0;
  }
  TK.dropzone($('ipDrop'), $('ipInput'), function(list){
    list.forEach(function(f){ files.push({file:f, url:URL.createObjectURL(f)}); });
    render();
  }, /^image\//);
  $('ipList').addEventListener('click', function(e){
    var b=e.target.closest('button'); if(!b) return;
    if(b.dataset.del!==undefined){ var gone=files.splice(+b.dataset.del,1)[0]; if(gone) URL.revokeObjectURL(gone.url); }
    else if(b.dataset.up!==undefined){ var i=+b.dataset.up; if(i>0){ var t=files[i-1]; files[i-1]=files[i]; files[i]=t; } }
    else if(b.dataset.down!==undefined){ var j=+b.dataset.down; if(j<files.length-1){ var t2=files[j+1]; files[j+1]=files[j]; files[j]=t2; } }
    render();
  });

  /* Draw the image on a WHITE canvas (JPEG has no transparency - it would turn black otherwise). */
  function toJpeg(img, qual){
    var w=img.naturalWidth, h=img.naturalHeight;
    if(qual.max && Math.max(w,h)>qual.max){ var r=qual.max/Math.max(w,h); w=Math.round(w*r); h=Math.round(h*r); }
    var s=TK.clampSize(w,h); w=s.w; h=s.h;
    var canvas=document.createElement('canvas'); canvas.width=w; canvas.height=h;
    var ctx=canvas.getContext('2d');
    ctx.fillStyle='#ffffff'; ctx.fillRect(0,0,w,h);
    ctx.imageSmoothingQuality='high';
    ctx.drawImage(img,0,0,w,h);
    return { data: canvas.toDataURL('image/jpeg', qual.q), w:w, h:h };
  }

  $('ipMake').addEventListener('click', function(){
    if(!files.length) return;
    if(!TK.checkLibs(['jspdf'])) return;
    var btn=this; TK.busy(btn,true,'Creating PDF...');
    var pageOpt=$('ipPage').value, orientOpt=$('ipOrient').value, margin=Math.max(0,parseFloat($('ipMargin').value)||0);
    var qual=QUALITY[$('ipQuality').value]||QUALITY.high;
    var jsPDF=window.jspdf.jsPDF, doc=null, chain=Promise.resolve();

    files.forEach(function(f,idx){
      chain=chain.then(function(){ return TK.loadImage(f.file); }).then(function(img){
        var pic=toJpeg(img, qual);
        URL.revokeObjectURL(img._url);
        var landscape = orientOpt==='auto' ? pic.w>pic.h : orientOpt==='l';
        var pw, ph, format, unit;
        if(pageOpt==='fit'){
          /* page = image size (1px = 0.75pt); very large photos are scaled so the page stays a legal PDF size */
          var k=Math.min(1, 14000/Math.max(pic.w,pic.h));
          unit='px'; format=[Math.round(pic.w*k), Math.round(pic.h*k)]; landscape = pic.w>pic.h;
        } else { unit='mm'; format=pageOpt; }
        var orientation = landscape ? 'l' : 'p';
        if(!doc){ doc=new jsPDF({unit:unit, format:format, orientation:orientation}); }
        else { doc.addPage(format, orientation); }
        pw=doc.internal.pageSize.getWidth(); ph=doc.internal.pageSize.getHeight();
        var m = pageOpt==='fit' ? 0 : margin;
        var availW=Math.max(1,pw-m*2), availH=Math.max(1,ph-m*2);
        var ratio=Math.min(availW/pic.w, availH/pic.h);
        var w=pic.w*ratio, h=pic.h*ratio;
        doc.addImage(pic.data,'JPEG',(pw-w)/2,(ph-h)/2,w,h);
        btn.lastChild && (btn.lastChild.textContent=' Page '+(idx+1)+'/'+files.length);
      }).catch(function(err){ err.message = err.message || 'Could not process "'+f.file.name+'"'; throw err; });
    });
    chain.then(function(){ doc.save('images.pdf'); TK.busy(btn,false); })
      .catch(function(err){ console.error(err); TK.toast(err.message || 'Something went wrong, please try again'); TK.busy(btn,false); });
  });
})();
</script>
