<div class="row g-4">
  <div class="col-lg-5">
    <div class="ai-prompt-panel">
      <div class="d-flex align-items-center gap-2 mb-2"><span class="tool-mini-icon"><i class="bi bi-stars"></i></span><label for="tiPrompt" class="form-label fw-bold mb-0">Describe your image</label></div>
      <textarea id="tiPrompt" class="form-control tool-text" rows="7" placeholder="A cinematic mountain lake at sunrise, mist over the water, ultra detailed...">A cinematic mountain lake at sunrise, mist over the water, ultra detailed, warm golden light</textarea>
      <p class="small text-muted mt-2 mb-3"><i class="bi bi-lightbulb"></i> Include the subject, style, lighting and mood for better results.</p>
      <label for="tiSizePreset" class="form-label fw-bold">Image resolution</label>
      <select id="tiSizePreset" class="form-select mb-3">
        <option value="1024x1024">Square &middot; 1024 x 1024</option>
        <option value="1280x720">Landscape &middot; 1280 x 720</option>
        <option value="720x1280">Portrait &middot; 720 x 1280</option>
        <option value="1920x1080">Full HD &middot; 1920 x 1080</option>
        <option value="custom">Custom resolution</option>
      </select>
      <div id="tiCustomSize" class="row g-2 d-none mb-3">
        <div class="col-6"><label for="tiWidth" class="form-label small">Width</label><input id="tiWidth" type="number" class="form-control" min="256" max="2048" value="1024"></div>
        <div class="col-6"><label for="tiHeight" class="form-label small">Height</label><input id="tiHeight" type="number" class="form-control" min="256" max="2048" value="1024"></div>
      </div>
      <button id="tiGenerate" class="btn btn-primary btn-lg w-100"><i class="bi bi-magic"></i> Generate image</button>
      <p class="small text-muted text-center mt-3 mb-0"><i class="bi bi-cloud-arrow-up"></i> Your prompt is sent to the image generation provider.</p>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="d-flex align-items-center justify-content-between mb-2"><label class="form-label fw-bold mb-0" for="tiImage">Generated image</label><span id="tiResolution" class="badge text-bg-light">1024 x 1024</span></div>
    <div id="tiPreview" class="ai-image-preview">
      <div id="tiEmpty" class="ai-empty-state"><i class="bi bi-image"></i><strong>Your image will appear here</strong><span>Write a prompt and choose a resolution to begin.</span></div>
      <div id="tiLoading" class="ai-loading d-none"><span class="spinner-border"></span><strong>Creating your image...</strong><span>This can take a few seconds.</span></div>
      <img id="tiImage" class="d-none" alt="Generated AI image">
    </div>
    <div id="tiActions" class="d-none ai-actions gap-2 mt-3">
      <button id="tiDownload" class="btn btn-primary flex-fill"><i class="bi bi-download"></i> Download image</button>
      <button id="tiOpen" class="btn btn-outline-primary" title="Open image in a new tab" aria-label="Open image in a new tab"><i class="bi bi-box-arrow-up-right"></i></button>
    </div>
    <p id="tiError" class="alert alert-danger d-none mt-3 mb-0" role="alert"></p>
  </div>
</div>
<script>
(function(){
  var $=TK.$, promptEl=$('tiPrompt'), presetEl=$('tiSizePreset'), customEl=$('tiCustomSize'), widthEl=$('tiWidth'), heightEl=$('tiHeight'),
      generateBtn=$('tiGenerate'), emptyEl=$('tiEmpty'), loadingEl=$('tiLoading'), imageEl=$('tiImage'), actionsEl=$('tiActions'),
      downloadBtn=$('tiDownload'), openBtn=$('tiOpen'), errorEl=$('tiError'), resolutionEl=$('tiResolution'), imageUrl='', reqToken=0;

  function getSize(){
    var parts=presetEl.value==='custom' ? [widthEl.value,heightEl.value] : presetEl.value.split('x');
    return {width:Math.min(2048,Math.max(256,parseInt(parts[0],10)||1024)),height:Math.min(2048,Math.max(256,parseInt(parts[1],10)||1024))};
  }
  function updateSize(){
    customEl.classList.toggle('d-none',presetEl.value!=='custom');
    var size=getSize(); resolutionEl.textContent=size.width+' x '+size.height;
  }
  function showError(message){ errorEl.textContent=message; errorEl.classList.remove('d-none'); }
  function clearError(){ errorEl.textContent=''; errorEl.classList.add('d-none'); }
  presetEl.addEventListener('change',updateSize); widthEl.addEventListener('input',updateSize); heightEl.addEventListener('input',updateSize); updateSize();

  generateBtn.addEventListener('click',function(){
    var prompt=promptEl.value.trim(), size=getSize();
    if(!prompt){ showError('Please describe the image you want to create.'); promptEl.focus(); return; }
    clearError();
    var myToken=++reqToken; /* ignore a stale response if the user clicks Generate again before this one finishes */
    imageUrl='https://image.pollinations.ai/prompt/'+encodeURIComponent(prompt)+'?width='+size.width+'&height='+size.height+'&nologo=true&enhance=true&seed='+Date.now();
    TK.busy(generateBtn,true,'Generating...'); emptyEl.classList.add('d-none'); actionsEl.classList.add('d-none'); imageEl.classList.add('d-none'); loadingEl.classList.remove('d-none');
    var settled=false;
    var timer=setTimeout(function(){
      if(settled || myToken!==reqToken) return; settled=true;
      loadingEl.classList.add('d-none'); emptyEl.classList.remove('d-none'); TK.busy(generateBtn,false);
      showError('This is taking too long. The image service may be busy - please try again.');
    }, 45000);
    imageEl.onload=function(){
      if(myToken!==reqToken) return; settled=true; clearTimeout(timer);
      loadingEl.classList.add('d-none'); imageEl.classList.remove('d-none'); actionsEl.classList.remove('d-none'); TK.busy(generateBtn,false);
    };
    imageEl.onerror=function(){
      if(myToken!==reqToken) return; settled=true; clearTimeout(timer);
      loadingEl.classList.add('d-none'); emptyEl.classList.remove('d-none'); TK.busy(generateBtn,false);
      showError('The image service could not create this image. Please try again with another prompt.');
    };
    imageEl.src=imageUrl;
  });
  openBtn.addEventListener('click',function(){ if(imageUrl) window.open(imageUrl,'_blank','noopener'); });
  downloadBtn.addEventListener('click',function(){
    if(!imageUrl) return;
    TK.busy(downloadBtn,true,'Preparing...');
    fetch(imageUrl).then(function(r){ if(!r.ok) throw new Error('download'); return r.blob(); }).then(function(blob){ TK.download(blob,'attaali-ai-image.png'); TK.busy(downloadBtn,false); }).catch(function(){ TK.busy(downloadBtn,false); window.open(imageUrl,'_blank','noopener'); TK.toast('Could not download directly - image opened in a new tab instead.'); });
  });
})();
</script>
