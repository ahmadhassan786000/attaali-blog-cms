<div class="row g-4">
  <div class="col-lg-5">
    <div id="mpDrop" class="drop-zone">
      <i class="bi bi-file-earmark-pdf"></i>
      <p class="mb-1 fw-bold">Click or drop PDF files here</p>
      <p class="small text-muted mb-0">Add two or more PDFs</p>
      <input id="mpInput" type="file" accept="application/pdf" multiple hidden>
    </div>
    <div id="mpList" class="mt-3"></div>
  </div>
  <div class="col-lg-7 d-flex flex-column align-items-center justify-content-center text-center">
    <i class="bi bi-union" style="font-size:3rem;color:var(--brand)"></i>
    <p class="text-muted mt-2">Files are merged in the order shown on the left. Use the arrows to reorder.</p>
    <button id="mpMake" class="btn btn-primary btn-lg w-100" disabled><i class="bi bi-download"></i> Merge PDFs (<span id="mpCount">0</span>)</button>
  </div>
</div>
<script>
(function(){
  var $=TK.$, files=[];
  function render(){
    var list=$('mpList'); list.innerHTML='';
    files.forEach(function(f,i){
      var div=document.createElement('div'); div.className='file-item';
      div.innerHTML='<i class="bi bi-file-earmark-pdf text-danger fs-4"></i><span class="name">'+TK.escapeHtml(f.name)+'</span><span class="small text-muted">'+TK.formatBytes(f.size)+'</span>'+
        '<button class="btn btn-sm btn-outline-secondary" data-up="'+i+'" aria-label="Move up"><i class="bi bi-arrow-up"></i></button>'+
        '<button class="btn btn-sm btn-outline-secondary" data-down="'+i+'" aria-label="Move down"><i class="bi bi-arrow-down"></i></button>'+
        '<button class="btn btn-sm btn-outline-danger" data-del="'+i+'" aria-label="Remove"><i class="bi bi-trash"></i></button>';
      list.appendChild(div);
    });
    $('mpCount').textContent=files.length;
    $('mpMake').disabled = files.length<2;
  }
  TK.dropzone($('mpDrop'), $('mpInput'), function(list){ list.forEach(function(f){ files.push(f); }); render(); }, /pdf/i);
  $('mpList').addEventListener('click', function(e){
    var b=e.target.closest('button'); if(!b) return;
    if(b.dataset.del!==undefined){ files.splice(+b.dataset.del,1); }
    else if(b.dataset.up!==undefined){ var i=+b.dataset.up; if(i>0){ var t=files[i-1]; files[i-1]=files[i]; files[i]=t; } }
    else if(b.dataset.down!==undefined){ var j=+b.dataset.down; if(j<files.length-1){ var t2=files[j+1]; files[j+1]=files[j]; files[j]=t2; } }
    render();
  });

  function loadOne(f){
    return TK.readAsArrayBuffer(f).then(function(buf){
      return window.PDFLib.PDFDocument.load(buf).catch(function(err){
        var enc = err && (err.name==='EncryptedPDFError' || /encrypt/i.test(err.message||''));
        throw new Error('"'+f.name+'" '+(enc ? 'is password protected - remove the password first.' : 'is not a valid PDF or is damaged.'));
      });
    });
  }

  $('mpMake').addEventListener('click', function(){
    if(files.length<2) return;
    if(!TK.checkLibs(['PDFLib'])) return;
    var btn=this; TK.busy(btn,true,'Merging...');
    var PDFLib=window.PDFLib, total=0;
    PDFLib.PDFDocument.create().then(function(merged){
      var chain=Promise.resolve();
      files.forEach(function(f,idx){
        chain=chain.then(function(){ return loadOne(f); })
          .then(function(src){ return merged.copyPages(src, src.getPageIndices()); })
          .then(function(pages){ pages.forEach(function(p){ merged.addPage(p); total++; }); });
      });
      return chain.then(function(){ return merged.save(); });
    }).then(function(bytes){
      TK.download(new Blob([bytes], {type:'application/pdf'}), 'merged.pdf');
      TK.toast('Merged '+files.length+' files ('+total+' pages)');
      TK.busy(btn,false);
    }).catch(function(err){ console.error(err); TK.toast(err.message||'Could not merge these files'); TK.busy(btn,false); });
  });
})();
</script>
