<label class="form-label fw-bold" for="ccText">Your text</label>
<textarea id="ccText" dir="auto" class="form-control tool-text mb-3" style="min-height:220px" placeholder="Type or paste text here...">The Quick Brown Fox Jumps Over The Lazy Dog</textarea>
<div class="d-flex flex-wrap gap-2 mb-3">
  <button class="btn btn-outline-primary btn-sm" data-case="upper">UPPERCASE</button>
  <button class="btn btn-outline-primary btn-sm" data-case="lower">lowercase</button>
  <button class="btn btn-outline-primary btn-sm" data-case="title">Title Case</button>
  <button class="btn btn-outline-primary btn-sm" data-case="sentence">Sentence case</button>
  <button class="btn btn-outline-primary btn-sm" data-case="camel">camelCase</button>
  <button class="btn btn-outline-primary btn-sm" data-case="pascal">PascalCase</button>
  <button class="btn btn-outline-primary btn-sm" data-case="snake">snake_case</button>
  <button class="btn btn-outline-primary btn-sm" data-case="kebab">kebab-case</button>
  <button class="btn btn-outline-primary btn-sm" data-case="invert">iNVERT cASE</button>
</div>
<div class="d-flex justify-content-between align-items-center mb-2">
  <label class="form-label fw-bold mb-0" for="ccOut">Result</label>
  <div><button id="ccCopy" class="btn btn-sm btn-outline-secondary"><i class="bi bi-clipboard"></i> Copy</button>
  <button id="ccDl" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i> TXT</button></div>
</div>
<textarea id="ccOut" dir="auto" class="form-control tool-text" style="min-height:160px" readonly></textarea>
<script>
(function(){
  var $=TK.$;
  var LETTER = TK.uniRe('\\p{L}', '', '[A-Za-z\\u00C0-\\u024F]');
  /* "word" for the programming-case converters: a run of letters/digits. Splits camelCase and treats any
     run of non-letters (spaces, punctuation, underscores, hyphens...) as a separator. */
  var TOKEN_RE = TK.uniRe('[\\p{L}\\p{N}]+', 'g', "[A-Za-z0-9]+");
  function tokens(s){
    var out=[];
    (s.match(TOKEN_RE)||[]).forEach(function(run){
      /* split further on camelCase boundaries inside a run, e.g. "myVariableName" */
      var parts=run.split(TK.uniRe('(?<=[\\p{Ll}\\p{N}])(?=\\p{Lu})|(?<=\\p{Lu})(?=\\p{Lu}\\p{Ll})', '', ''));
      if(!parts.length || parts.length===1 && !parts[0]) { out.push(run); } else { parts.forEach(function(p){ if(p) out.push(p); }); }
    });
    return out;
  }
  function capitalize(w){ return w.charAt(0).toUpperCase()+w.slice(1).toLowerCase(); }

  var conv={
    upper: function(s){ return s.toUpperCase(); },
    lower: function(s){ return s.toLowerCase(); },
    title: function(s){
      var re=TK.uniRe('[\\p{L}\\p{N}][\\p{L}\\p{N}\\u2019\']*', 'g', "[A-Za-z0-9][A-Za-z0-9'\\u2019]*");
      return s.replace(re, function(w){ return w.charAt(0).toUpperCase()+w.slice(1).toLowerCase(); });
    },
    sentence: function(s){
      s=s.toLowerCase();
      var re=TK.uniRe('(^\\s*[\\p{L}\\p{N}])|([.!?\\n]\\s*[\\p{L}\\p{N}])', 'g', "(^\\s*[A-Za-z0-9])|([.!?\\n]\\s*[A-Za-z0-9])");
      return s.replace(re, function(m){ return m.toUpperCase(); });
    },
    camel: function(s){ var w=tokens(s); return w.map(function(x,i){ x=x.toLowerCase(); return i? capitalize(x) : x; }).join(''); },
    pascal: function(s){ return tokens(s).map(function(x){ return capitalize(x.toLowerCase()); }).join(''); },
    snake: function(s){ return tokens(s).map(function(x){ return x.toLowerCase(); }).join('_'); },
    kebab: function(s){ return tokens(s).map(function(x){ return x.toLowerCase(); }).join('-'); },
    invert: function(s){ return Array.from(s).map(function(c){ return c===c.toUpperCase() && c!==c.toLowerCase() ? c.toLowerCase() : c.toUpperCase(); }).join(''); },
  };
  document.querySelectorAll('[data-case]').forEach(function(btn){
    btn.addEventListener('click', function(){
      var text=$('ccText').value;
      if(!text.trim()){ TK.toast('Please enter some text'); return; }
      var fn=conv[btn.dataset.case];
      $('ccOut').value = fn(text);
    });
  });
  $('ccCopy').addEventListener('click', function(){ TK.copy($('ccOut').value); });
  $('ccDl').addEventListener('click', function(){
    if(!$('ccOut').value){ TK.toast('Nothing to download'); return; }
    TK.downloadText($('ccOut').value, 'converted-text.txt');
  });
})();
</script>
