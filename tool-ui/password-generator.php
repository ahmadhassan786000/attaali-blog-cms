<div class="row g-4">
  <div class="col-lg-7">
    <div class="input-group input-group-lg mb-3">
      <input id="pgOut" class="form-control mono" readonly aria-label="Generated password">
      <button id="pgCopy" class="btn btn-outline-secondary" aria-label="Copy password"><i class="bi bi-clipboard"></i></button>
      <button id="pgRefresh" class="btn btn-primary" aria-label="Generate new password"><i class="bi bi-arrow-clockwise"></i></button>
    </div>
    <div class="strength-bar mb-1"><div id="pgStrengthBar"></div></div>
    <div id="pgStrengthLabel" class="small text-muted mb-4"></div>

    <label class="form-label small" for="pgLen">Password length: <span id="pgLenVal">16</span></label>
    <input id="pgLen" type="range" class="form-range mb-3" min="6" max="64" value="16">
    <div class="form-check"><input class="form-check-input" type="checkbox" id="pgUpper" checked><label class="form-check-label" for="pgUpper">Uppercase letters (A-Z)</label></div>
    <div class="form-check"><input class="form-check-input" type="checkbox" id="pgLower" checked><label class="form-check-label" for="pgLower">Lowercase letters (a-z)</label></div>
    <div class="form-check"><input class="form-check-input" type="checkbox" id="pgNums" checked><label class="form-check-label" for="pgNums">Numbers (0-9)</label></div>
    <div class="form-check"><input class="form-check-input" type="checkbox" id="pgSymbols" checked><label class="form-check-label" for="pgSymbols">Symbols (!@#$%^&*)</label></div>
    <div class="form-check"><input class="form-check-input" type="checkbox" id="pgAmbig"><label class="form-check-label" for="pgAmbig">Exclude ambiguous characters (l 1 I O 0)</label></div>
  </div>
  <div class="col-lg-5">
    <div class="card"><div class="card-body">
      <h6 class="fw-bold"><i class="bi bi-shield-check text-success"></i> Generated locally</h6>
      <p class="small text-muted mb-0">This password is created using your browser's secure random generator (crypto.getRandomValues). It is never sent anywhere or stored.</p>
    </div></div>
  </div>
</div>
<script>
(function(){
  var $=TK.$;
  var AMBIG=/[l1IO0]/g;

  function randInt(max){ var a=new Uint32Array(1); crypto.getRandomValues(a); return a[0]%max; }

  function generate(){
    var len=parseInt($('pgLen').value,10);
    var excludeAmbig=$('pgAmbig').checked;
    var setDefs=[
      {on:$('pgUpper').checked, chars:'ABCDEFGHIJKLMNOPQRSTUVWXYZ'},
      {on:$('pgLower').checked, chars:'abcdefghijklmnopqrstuvwxyz'},
      {on:$('pgNums').checked, chars:'0123456789'},
      {on:$('pgSymbols').checked, chars:'!@#$%^&*()_+-=[]{}|;:,.<>?'},
    ];
    var anyOn=setDefs.some(function(s){return s.on;});
    if(!anyOn){ $('pgUpper').checked=true; setDefs[0].on=true; }
    var sets=setDefs.filter(function(s){return s.on;}).map(function(s){
      var c=excludeAmbig ? s.chars.replace(AMBIG,'') : s.chars;
      return c || s.chars; /* never leave a chosen set totally empty */
    });
    var all=sets.join('');

    /* guarantee at least one character from every ticked set, then fill and shuffle */
    var pass=sets.map(function(s){ return s[randInt(s.length)]; });
    while(pass.length<len) pass.push(all[randInt(all.length)]);
    pass=pass.slice(0,len);
    for(var i=pass.length-1;i>0;i--){ var j=randInt(i+1); var t=pass[i]; pass[i]=pass[j]; pass[j]=t; }

    var result=pass.join('');
    $('pgOut').value=result;
    scorePassword(result, all.length);
  }

  /* Strength = real search-space entropy (bits) = length * log2(alphabet size), not a fixed checklist. */
  function scorePassword(p, alphabetSize){
    var bits = alphabetSize>1 ? p.length*Math.log2(alphabetSize) : 0;
    var bar=$('pgStrengthBar');
    var pct=Math.min(100, bits/80*100);
    bar.style.width=pct+'%';
    var label, color;
    if(bits<40){ label='Weak'; color='#dc3545'; }
    else if(bits<60){ label='Fair'; color='#f59e0b'; }
    else if(bits<80){ label='Good'; color='#0d9488'; }
    else { label='Strong'; color='#198754'; }
    bar.style.background=color;
    $('pgStrengthLabel').textContent='Strength: '+label+' ('+Math.round(bits)+'-bit)';
  }

  ['pgUpper','pgLower','pgNums','pgSymbols','pgAmbig'].forEach(function(id){ $(id).addEventListener('change', generate); });
  $('pgLen').addEventListener('input', function(){ $('pgLenVal').textContent=$('pgLen').value; generate(); });
  $('pgRefresh').addEventListener('click', generate);
  $('pgCopy').addEventListener('click', function(){ TK.copy($('pgOut').value, 'Password copied'); });
  generate();
})();
</script>
