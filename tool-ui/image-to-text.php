<div class="row g-4">
  <div class="col-lg-5">
    <div id="ocrDrop" class="drop-zone">
      <i class="bi bi-cloud-arrow-up"></i>
      <p class="mb-1 fw-bold">Click or drop an image here</p>
      <input id="ocrInput" type="file" accept="image/*" hidden>
    </div>
    <div id="ocrPreview" class="mt-3"></div>
    <label class="form-label small mt-3">Language of the text</label>
    <select id="ocrLang" class="form-select mb-3">
      <option value="eng">English</option>
      <option value="urd">Urdu</option>
      <option value="ara">Arabic</option>
      <option value="hin">Hindi</option>
    </select>
    <button id="ocrGo" class="btn btn-primary btn-lg w-100" disabled><i class="bi bi-body-text"></i> Extract Text</button>
    <div id="ocrProgress" class="mt-3 d-none">
      <div class="progress"><div id="ocrBar" class="progress-bar" style="width:0%"></div></div>
      <div id="ocrStatus" class="small text-muted mt-1"></div>
    </div>
  </div>
  <div class="col-lg-7">
    <label class="form-label fw-bold d-flex justify-content-between">Extracted text
      <span><button id="ocrCopy" class="btn btn-sm btn-outline-secondary"><i class="bi bi-clipboard"></i> Copy</button>
      <button id="ocrDl" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i> TXT</button></span>
    </label>
    <textarea id="ocrOut" class="form-control tool-text" style="min-height:340px" placeholder="Extracted text will appear here..." readonly></textarea>
  </div>
</div>
<script>
(function(){
  var $=TK.$, file=null;
  TK.dropzone($('ocrDrop'), $('ocrInput'), function(list){
    file=list[0];
    $('ocrPreview').innerHTML='<img src="'+URL.createObjectURL(file)+'" class="img-fluid rounded border">';
    $('ocrGo').disabled=false;
  }, /^image\//);

  $('ocrGo').addEventListener('click', function(){
    if(!file || typeof Tesseract==='undefined'){ TK.toast('OCR engine failed to load'); return; }
    var btn=this; TK.busy(btn,true,'Reading...');
    $('ocrProgress').classList.remove('d-none'); $('ocrOut').value='';
    Tesseract.recognize(file, $('ocrLang').value, {
      logger: function(m){
        if(m.status){ $('ocrStatus').textContent=m.status.replace(/_/g,' '); }
        if(m.progress!=null){ $('ocrBar').style.width=Math.round(m.progress*100)+'%'; }
      }
    }).then(function(res){
      $('ocrOut').value=res.data.text.trim();
      TK.busy(btn,false); $('ocrProgress').classList.add('d-none');
      if(!res.data.text.trim()) TK.toast('No text found in this image');
    }).catch(function(err){ console.error(err); TK.toast('Could not read text from this image'); TK.busy(btn,false); $('ocrProgress').classList.add('d-none'); });
  });

  $('ocrCopy').addEventListener('click', function(){
    var t=$('ocrOut').value; if(!t){ TK.toast('Nothing to copy'); return; }
    navigator.clipboard.writeText(t).then(function(){ TK.toast('Copied to clipboard'); });
  });
  $('ocrDl').addEventListener('click', function(){
    var t=$('ocrOut').value; if(!t){ TK.toast('Nothing to download'); return; }
    TK.downloadText(t,'extracted-text.txt');
  });
})();
</script>
