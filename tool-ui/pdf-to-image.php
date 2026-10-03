<div class="row g-4">
  <div class="col-lg-5">
    <div id="p2iDrop" class="drop-zone">
      <i class="bi bi-file-earmark-pdf"></i>
      <p class="mb-1 fw-bold">Click or drop a PDF here</p>
      <p class="small text-muted mb-0">Only one PDF at a time</p>
      <input id="p2iInput" type="file" accept="application/pdf" hidden>
    </div>
    <div id="p2iFileInfo" class="mt-3"></div>
    <div class="row g-2 mt-2">
      <div class="col-6"><label class="form-label small" for="p2iFormat">Format</label><select id="p2iFormat" class="form-select form-select-sm"><option value="image/png">PNG</option><option value="image/jpeg" selected>JPG</option></select></div>
      <div class="col-6"><label class="form-label small" for="p2iScale">Quality / scale</label><select id="p2iScale" class="form-select form-select-sm"><option value="1">1x (fast)</option><option value="1.5" selected>1.5x (screen)</option><option value="2">2x (print)</option><option value="3">3x (large print)</option></select></div>
    </div>
    <button id="p2iConvert" class="btn btn-primary btn-lg w-100 mt-3" disabled><i class="bi bi-images"></i> Convert to Images</button>
  </div>
  <div class="col-lg-7">
    <div id="p2iProgress" class="mb-3 d-none"><div class="progress"><div id="p2iBar" class="progress-bar" style="width:0%"></div></div><div id="p2iStatus" class="small text-muted mt-1"></div></div>
    <div id="p2iResults" class="row g-3"></div>
    <button id="p2iZip" class="btn btn-success mt-3 d-none"><i class="bi bi-file-zip"></i> Download all as ZIP</button>
  </div>
</div>
<script>
(function(){
  var $=TK.$, pdfFile=null, results=[], urls=[], WORKER='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
  if (window.pdfjsLib) { pdfjsLib.GlobalWorkerOptions.workerSrc = WORKER; }

  TK.dropzone($('p2iDrop'), $('p2iInput'), function(list){
    pdfFile=list[0];
    $('p2iFileInfo').innerHTML='<div class="file-item"><i class="bi bi-file-earmark-pdf text-danger fs-4"></i><span class="name">'+TK.escapeHtml(pdfFile.name)+'</span><span class="small text-muted">'+TK.formatBytes(pdfFile.size)+'</span></div>';
    $('p2iConvert').disabled=false;
  }, /pdf/i);

  function clearResults(){
    urls.forEach(function(u){ URL.revokeObjectURL(u); }); urls=[]; results=[];
    $('p2iResults').innerHTML=''; $('p2iZip').classList.add('d-none');
  }

  /* pages are rendered on white paper, and the scale is lowered if the canvas would get too big for the browser */
  function safeScale(page, wanted){
    var vp=page.getViewport({scale:1}), s=wanted;
    s=Math.min(s, TK.MAX_SIDE/Math.max(vp.width,vp.height), Math.sqrt(TK.MAX_AREA/(vp.width*vp.height)));
    return Math.max(0.1, s);
  }

  $('p2iConvert').addEventListener('click', function(){
    if(!pdfFile) return;
    if(!window.pdfjsLib){ TK.checkLibs(['pdfjsLib']); return; }
    var btn=this; TK.busy(btn,true,'Converting...');
    clearResults();
    $('p2iProgress').classList.remove('d-none'); $('p2iBar').style.width='0%'; $('p2iStatus').textContent='Opening PDF...';
    var wanted=parseFloat($('p2iScale').value)||1.5, mime=$('p2iFormat').value, ext=mime==='image/png'?'png':'jpg';
    var stem=TK.baseName(pdfFile.name);
    var loading=null, pdfDoc=null;

    function finish(){ TK.busy(btn,false); $('p2iProgress').classList.add('d-none'); if(pdfDoc){ pdfDoc.destroy(); pdfDoc=null; } }

    TK.readAsArrayBuffer(pdfFile).then(function(buf){
      loading=pdfjsLib.getDocument({data:buf});
      loading.onPassword=function(update, reason){
        var pw=window.prompt(reason===pdfjsLib.PasswordResponses.INCORRECT_PASSWORD ? 'Wrong password. Enter the PDF password again:' : 'This PDF is password protected. Enter the password:');
        if(pw===null){ loading.destroy(); } else { update(pw); }
      };
      return loading.promise;
    }).then(function(pdf){
      pdfDoc=pdf;
      var n=pdf.numPages, chain=Promise.resolve();
      for(var p=1;p<=n;p++){
        (function(pageNum){
          chain=chain.then(function(){ $('p2iStatus').textContent='Rendering page '+pageNum+' of '+n; return pdf.getPage(pageNum); }).then(function(page){
            var viewport=page.getViewport({scale:safeScale(page,wanted)});
            var canvas=document.createElement('canvas'); canvas.width=Math.floor(viewport.width); canvas.height=Math.floor(viewport.height);
            var ctx=canvas.getContext('2d');
            ctx.fillStyle='#ffffff'; ctx.fillRect(0,0,canvas.width,canvas.height);
            return page.render({canvasContext:ctx, viewport:viewport}).promise.then(function(){
              page.cleanup();
              return TK.canvasToBlob(canvas, mime, 0.92);
            }).then(function(blob){
              canvas.width=canvas.height=0;
              var pad=String(n).length, num=('000'+pageNum).slice(-Math.max(pad,1));
              var name=stem+'-page-'+num+'.'+ext;
              results.push({name:name, blob:blob});
              var url=URL.createObjectURL(blob); urls.push(url);
              var div=document.createElement('div'); div.className='col-6 col-md-4';
              div.innerHTML='<div class="result-thumb"><img src="'+url+'" alt="Page '+pageNum+'"><div class="small mt-1">Page '+pageNum+' <span class="text-muted">('+TK.formatBytes(blob.size)+')</span></div><a class="btn btn-sm btn-outline-primary mt-1 w-100" href="'+url+'" download="'+TK.escapeHtml(name)+'"><i class="bi bi-download"></i></a></div>';
              $('p2iResults').appendChild(div);
              $('p2iBar').style.width=Math.round((pageNum/n)*100)+'%';
            });
          });
        })(p);
      }
      return chain;
    }).then(function(){
      finish();
      if(results.length>1) $('p2iZip').classList.remove('d-none');
    }).catch(function(err){
      console.error(err);
      var name=err&&err.name;
      var msg = name==='PasswordException' ? 'Password missing or incorrect - the PDF could not be opened.'
        : name==='InvalidPDFException' ? 'This file is not a valid PDF or it is damaged.'
        : (err&&err.message&&/canvas|memory/i.test(err.message)) ? 'This page is too large to render - choose a lower scale.'
        : 'Could not read this PDF. Please check the file and try again.';
      TK.toast(msg); finish();
    });
  });

  $('p2iZip').addEventListener('click', function(){ TK.downloadZip(results, TK.baseName(pdfFile.name)+'-pages.zip'); });
})();
</script>
