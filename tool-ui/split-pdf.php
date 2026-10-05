<div class="row g-4">
  <div class="col-lg-5">
    <div id="spDrop" class="drop-zone">
      <i class="bi bi-scissors"></i>
      <p class="mb-1 fw-bold">Click or drop a PDF here</p>
      <input id="spInput" type="file" accept="application/pdf" hidden>
    </div>
    <div id="spInfo" class="mt-3"></div>
  </div>
  <div class="col-lg-7">
    <div class="mb-3">
      <label class="form-label fw-bold">What do you want to do?</label>
      <div class="form-check"><input class="form-check-input" type="radio" name="spMode" id="spModeRange" value="range" checked><label class="form-check-label" for="spModeRange">Extract specific pages into one PDF</label></div>
      <div class="form-check"><input class="form-check-input" type="radio" name="spMode" id="spModeEach" value="each"><label class="form-check-label" for="spModeEach">Split every page into a separate PDF (ZIP)</label></div>
    </div>
    <div id="spRangeBox">
      <label class="form-label small" for="spRange">Pages to extract (e.g. 1-3,5,8-10) - this PDF has <span id="spTotal">?</span> page(s)</label>
      <input id="spRange" class="form-control mb-1" placeholder="1-3,5,8-10">
      <div id="spRangeMsg" class="small text-muted mb-3"></div>
    </div>
    <button id="spGo" class="btn btn-primary btn-lg w-100" disabled><i class="bi bi-scissors"></i> Split PDF</button>
  </div>
</div>
<script>
(function(){
  var $=TK.$, pdfFile=null, pageCount=0;
  document.querySelectorAll('[name="spMode"]').forEach(function(r){
    r.addEventListener('change', function(){ $('spRangeBox').classList.toggle('d-none', r.value==='each' && r.checked); });
  });

  TK.dropzone($('spDrop'), $('spInput'), function(list){
    pdfFile=list[0]; pageCount=0; $('spGo').disabled=true; $('spTotal').textContent='?';
    $('spInfo').innerHTML='<div class="file-item"><i class="bi bi-file-earmark-pdf text-danger fs-4"></i><span class="name">'+TK.escapeHtml(pdfFile.name)+'</span></div>';
    if(!TK.checkLibs(['PDFLib'])) return;
    TK.readAsArrayBuffer(pdfFile).then(function(buf){
      return window.PDFLib.PDFDocument.load(buf);
    }).then(function(doc){
      pageCount=doc.getPageCount(); $('spTotal').textContent=pageCount; $('spGo').disabled=false;
    }).catch(function(err){
      console.error(err);
      TK.toast('Could not open this PDF - '+(err && /encrypt/i.test(err.message||'') ? 'it is password protected.' : 'the file may be damaged.'));
    });
  }, /pdf/i);

  /* Accepts "1-3,5,8-10", ignores empty/garbage parts, auto-swaps reversed ranges, clamps to the page count. */
  function parseRange(str, max){
    var out=new Set(), bad=[];
    str.split(',').forEach(function(part){
      part=part.trim(); if(!part) return;
      var m=part.match(/^(\d+)\s*-\s*(\d+)$/);
      if(m){
        var a=+m[1], b=+m[2]; if(a>b){ var t=a; a=b; b=t; }
        a=Math.max(1,a); b=Math.min(max,b);
        if(a>max || b<1){ bad.push(part); return; }
        for(var i=a;i<=b;i++) out.add(i-1);
      } else if(/^\d+$/.test(part)){
        var n=+part; if(n>=1 && n<=max) out.add(n-1); else bad.push(part);
      } else { bad.push(part); }
    });
    return { idx: Array.from(out).sort(function(a,b){return a-b;}), bad: bad };
  }
  $('spRange').addEventListener('input', function(){
    if(!pageCount){ $('spRangeMsg').textContent=''; return; }
    var r=parseRange($('spRange').value, pageCount);
    $('spRangeMsg').textContent = $('spRange').value.trim()==='' ? '' :
      (r.idx.length ? 'Will extract '+r.idx.length+' page(s)'+(r.bad.length?' - ignoring: '+r.bad.join(', '):'') : 'No valid pages in this range');
  });

  $('spGo').addEventListener('click', function(){
    if(!pdfFile || !pageCount) return;
    var mode=document.querySelector('[name="spMode"]:checked').value;
    var stem=TK.baseName(pdfFile.name);
    var btn=this; TK.busy(btn,true,'Processing...');
    TK.readAsArrayBuffer(pdfFile).then(function(buf){ return window.PDFLib.PDFDocument.load(buf); }).then(function(src){
      if(mode==='range'){
        var r=parseRange($('spRange').value, pageCount);
        if(!r.idx.length){ TK.toast('Enter a valid page range (1-'+pageCount+')'); TK.busy(btn,false); return; }
        return window.PDFLib.PDFDocument.create().then(function(out){
          return out.copyPages(src, r.idx).then(function(pages){ pages.forEach(function(p){ out.addPage(p); }); return out.save(); });
        }).then(function(bytes){ TK.download(new Blob([bytes],{type:'application/pdf'}), stem+'-extracted.pdf'); TK.busy(btn,false); });
      } else {
        var pad=String(pageCount).length;
        var files=[], chain=Promise.resolve();
        for(var i=0;i<pageCount;i++){
          (function(pageIndex){
            chain=chain.then(function(){
              return window.PDFLib.PDFDocument.create().then(function(out){
                return out.copyPages(src,[pageIndex]).then(function(pages){ out.addPage(pages[0]); return out.save(); });
              });
            }).then(function(bytes){
              var num=('000'+(pageIndex+1)).slice(-Math.max(pad,1));
              files.push({name:stem+'-page-'+num+'.pdf', blob:new Blob([bytes],{type:'application/pdf'})});
            });
          })(i);
        }
        return chain.then(function(){ return TK.downloadZip(files, stem+'-split-pages.zip'); }).then(function(){ TK.busy(btn,false); });
      }
    }).catch(function(err){ console.error(err); TK.toast('Could not process this PDF - '+(err&&err.message?err.message:'please try again')); TK.busy(btn,false); });
  });
})();
</script>
