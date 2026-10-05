<div class="row g-4">
  <div class="col-lg-5">
    <label class="form-label small" for="liType">Generate by</label>
    <select id="liType" class="form-select mb-3"><option value="paragraphs">Paragraphs</option><option value="sentences">Sentences</option><option value="words">Words</option></select>
    <label class="form-label small" for="liCount">How many?</label>
    <input id="liCount" type="number" class="form-control mb-1" value="3" min="1" max="200">
    <div id="liCountMsg" class="small text-muted mb-2"></div>
    <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="liClassic" checked><label class="form-check-label" for="liClassic">Start with "Lorem ipsum dolor sit amet..."</label></div>
    <button id="liGo" class="btn btn-primary btn-lg w-100"><i class="bi bi-magic"></i> Generate</button>
  </div>
  <div class="col-lg-7">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <label class="form-label fw-bold mb-0" for="liOut">Result</label>
      <button id="liCopy" class="btn btn-sm btn-outline-secondary"><i class="bi bi-clipboard"></i> Copy</button>
    </div>
    <textarea id="liOut" class="form-control tool-text" style="min-height:320px" readonly></textarea>
  </div>
</div>
<script>
(function(){
  var $=TK.$;
  var MAX={ paragraphs:100, sentences:500, words:5000 };
  var words=("lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ut enim ad minim veniam quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat duis aute irure dolor in reprehenderit voluptate velit esse cillum dolore eu fugiat nulla pariatur excepteur sint occaecat cupidatat non proident sunt in culpa qui officia deserunt mollit anim id est laborum").split(' ');
  function rndWord(){ return words[Math.floor(Math.random()*words.length)]; }
  function sentence(minW,maxW){
    var n=minW+Math.floor(Math.random()*(maxW-minW));
    var arr=[]; for(var i=0;i<n;i++) arr.push(rndWord());
    var s=arr.join(' '); return s.charAt(0).toUpperCase()+s.slice(1)+'.';
  }
  function paragraph(){
    var n=3+Math.floor(Math.random()*4), arr=[]; for(var i=0;i<n;i++) arr.push(sentence(6,16));
    return arr.join(' ');
  }

  function clampCount(){
    var type=$('liType').value, max=MAX[type];
    $('liCount').max=max;
    var v=Math.round(parseFloat($('liCount').value));
    if(!v || v<1) v=1;
    if(v>max){ v=max; $('liCountMsg').textContent='Limited to '+max+' '+type+' at a time.'; }
    else { $('liCountMsg').textContent=''; }
    $('liCount').value=v;
    return v;
  }
  $('liType').addEventListener('change', clampCount);
  $('liCount').addEventListener('change', clampCount);

  $('liGo').addEventListener('click', function(){
    var type=$('liType').value, count=clampCount(), out=[];
    var classic=$('liClassic').checked;
    if(type==='words'){
      var arr=[]; for(var i=0;i<count;i++) arr.push(rndWord());
      if(classic){ var base='lorem ipsum dolor sit amet consectetur adipiscing elit'.split(' '); arr=base.slice(0,count).concat(arr).slice(0,count); }
      out.push(arr.join(' '));
    } else if(type==='sentences'){
      for(var i=0;i<count;i++) out.push(sentence(6,16));
      if(classic) out[0]='Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
    } else {
      for(var i=0;i<count;i++) out.push(paragraph());
      if(classic) out[0]='Lorem ipsum dolor sit amet, consectetur adipiscing elit. '+out[0];
    }
    $('liOut').value = type==='paragraphs' ? out.join('\n\n') : out.join(' ');
  });
  $('liCopy').addEventListener('click', function(){ TK.copy($('liOut').value); });
  clampCount();
  $('liGo').click();
})();
</script>
