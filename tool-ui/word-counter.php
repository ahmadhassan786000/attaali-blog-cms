<div class="row g-4">
  <div class="col-lg-8">
    <textarea id="wcText" dir="auto" class="form-control tool-text" style="min-height:340px" placeholder="Start typing or paste your text here..."></textarea>
  </div>
  <div class="col-lg-4">
    <div class="row g-2">
      <div class="col-6"><div class="stat-box"><div class="n" id="wcWords">0</div><div class="l">Words</div></div></div>
      <div class="col-6"><div class="stat-box"><div class="n" id="wcChars">0</div><div class="l">Characters</div></div></div>
      <div class="col-6"><div class="stat-box"><div class="n" id="wcCharsNo">0</div><div class="l">No spaces</div></div></div>
      <div class="col-6"><div class="stat-box"><div class="n" id="wcSentences">0</div><div class="l">Sentences</div></div></div>
      <div class="col-6"><div class="stat-box"><div class="n" id="wcParas">0</div><div class="l">Paragraphs</div></div></div>
      <div class="col-6"><div class="stat-box"><div class="n" id="wcRead">0</div><div class="l">Read time (min)</div></div></div>
    </div>
    <div class="card mt-3">
      <div class="card-body">
        <h6 class="fw-bold small text-uppercase text-muted">Top keywords</h6>
        <div id="wcKeywords" class="small text-muted">Start typing to see keyword frequency.</div>
      </div>
    </div>
  </div>
</div>
<script>
(function(){
  var $=TK.$, t=$('wcText');
  var stop={the:1,is:1,at:1,of:1,and:1,a:1,to:1,in:1,on:1,for:1,with:1,it:1,this:1,that:1,as:1,are:1,was:1,be:1,by:1,an:1};
  /* \p{L} = any letter in any language, \p{N} = any digit. Keeps words together across hyphens/apostrophes; falls back to ASCII on very old browsers. */
  /* \p{Mn}/\p{Mc} are combining marks (Urdu/Arabic diacritics, Devanagari matras) that attach to the
     previous letter and must not split a word in two. */
  var WORD_RE = TK.uniRe("[\\p{L}\\p{N}\\p{Mn}\\p{Mc}]+(?:[''-][\\p{L}\\p{N}\\p{Mn}\\p{Mc}]+)*", 'g', "[A-Za-z0-9]+(?:['-][A-Za-z0-9]+)*");
  var SENT_RE = TK.uniRe('[.!?\\u061F\\u06D4\\u3002]+', 'g', '[.!?]+');

  function update(){
    var text=t.value;
    var words=(text.match(WORD_RE)||[]);
    $('wcWords').textContent=words.length;
    var codePoints = Array.from(text).length; /* counts emoji as 1 char, not 2 */
    $('wcChars').textContent=codePoints;
    $('wcCharsNo').textContent=Array.from(text.replace(/\s/g,'')).length;
    /* a period inside a number (3.5) or an abbreviation like "U.S." is not a sentence end */
    var forSentences = text.replace(/(\d)\.(?=\d)/g, '$1\u0000');
    var sentMatches = forSentences.match(SENT_RE) || [];
    var sentCount = sentMatches.length;
    var lastEnd = 0, m2, re2 = new RegExp(SENT_RE.source, SENT_RE.flags);
    while ((m2 = re2.exec(forSentences))) { lastEnd = m2.index + m2[0].length; }
    if (forSentences.slice(lastEnd).trim()) sentCount++; /* trailing text with no closing punctuation still counts */
    $('wcSentences').textContent = sentCount || (text.trim() ? 1 : 0);
    $('wcParas').textContent=text.split(/\n+/).filter(function(p){return p.trim();}).length;
    $('wcRead').textContent= words.length ? Math.max(1,Math.ceil(words.length/200)) : 0;
    var freq={};
    words.forEach(function(w){ w=w.toLowerCase(); if(w.length<3||stop[w]) return; freq[w]=(freq[w]||0)+1; });
    var top=Object.keys(freq).sort(function(a,b){return freq[b]-freq[a];}).slice(0,8);
    $('wcKeywords').innerHTML = top.length ? top.map(function(w){ return '<span class="badge text-bg-light border me-1 mb-1">'+TK.escapeHtml(w)+' ('+freq[w]+')</span>'; }).join('') : 'Start typing to see keyword frequency.';
  }
  t.addEventListener('input', update); update();
})();
</script>
