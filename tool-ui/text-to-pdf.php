<div class="row g-4">
  <div class="col-lg-7">
    <label class="form-label fw-bold" for="tpText">Your text</label>
    <textarea id="tpText" dir="auto" class="form-control tool-text" style="min-height:320px" placeholder="Paste or type your text here...">Type your document here.

You can create multiple paragraphs. Each page automatically breaks when it is full.</textarea>
    <div class="row g-2 mt-2">
      <div class="col-4"><label class="form-label small" for="tpPage">Page size</label><select id="tpPage" class="form-select form-select-sm"><option value="a4">A4</option><option value="letter">Letter</option></select></div>
      <div class="col-4"><label class="form-label small" for="tpSize">Font size (pt)</label><input id="tpSize" type="number" class="form-control form-control-sm" value="14" min="8" max="36"></div>
      <div class="col-4"><label class="form-label small" for="tpMargin">Margin (mm)</label><input id="tpMargin" type="number" class="form-control form-control-sm" value="18" min="5" max="40"></div>
      <div class="col-12"><label class="form-label small" for="tpFont">Font</label>
        <select id="tpFont" class="form-select form-select-sm">
          <option value="helvetica">Helvetica (standard)</option>
          <option value="times">Times</option>
          <option value="courier">Courier</option>
        </select>
      </div>
      <div class="col-12 form-check mt-2 ms-2">
        <input class="form-check-input" type="checkbox" id="tpNums" checked>
        <label class="form-check-label" for="tpNums">Add page numbers ("Page 1 of 3")</label>
      </div>
      <div class="col-12 form-check ms-2">
        <input class="form-check-input" type="checkbox" id="tpUnicode">
        <label class="form-check-label" for="tpUnicode">Unicode / Urdu / Arabic mode (renders pages as images so non-Latin text displays correctly). Switched on automatically when your text needs it.</label>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <label class="form-label fw-bold">&nbsp;</label>
    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center border rounded p-4" style="min-height:300px">
      <i class="bi bi-file-earmark-pdf" style="font-size:3rem;color:#dc3545"></i>
      <p class="text-muted mt-2 mb-3">Your text will be converted into a clean, paginated PDF with your chosen font, size and margins.</p>
      <button id="tpMake" class="btn btn-primary btn-lg w-100"><i class="bi bi-download"></i> Create PDF</button>
      <div id="tpProgress" class="w-100 mt-3 d-none"><div class="progress"><div id="tpBar" class="progress-bar" style="width:0%"></div></div></div>
    </div>
  </div>
</div>
<script>
(function(){
  var $=TK.$;
  var MM_PT = 25.4/72;   /* 1 pt in millimetres */

  /* characters the built-in PDF fonts (WinAnsi / cp1252) can draw */
  var CP1252_EXTRA = '\u20AC\u201A\u0192\u201E\u2026\u2020\u2021\u02C6\u2030\u0160\u2039\u0152\u017D\u2018\u2019\u201C\u201D\u2022\u2013\u2014\u02DC\u2122\u0161\u203A\u0153\u017E\u0178';
  function needsUnicode(text){
    for(var i=0;i<text.length;i++){
      var c=text.charCodeAt(i);
      if(c<=0xFF) continue;
      if(CP1252_EXTRA.indexOf(text.charAt(i))!==-1) continue;
      return true;
    }
    return false;
  }
  var RTL_RE = TK.uniRe('[\\p{Script=Arabic}\\p{Script=Hebrew}\\p{Script=Syriac}\\p{Script=Thaana}]', '', '[\\u0590-\\u08FF\\uFB1D-\\uFDFF\\uFE70-\\uFEFF]');
  function isRtl(s){ return RTL_RE.test(s); }

  function clean(text){ return text.replace(/\r\n?/g,'\n').replace(/\t/g,'    ').replace(/\u00A0/g,' '); }

  /* Word-wrap using a measuring function; words wider than a full line are split by characters. */
  function wrapParagraph(par, maxW, measure){
    if(par==='') return [''];
    var words=par.split(' '), lines=[], line='';
    function pushLong(w){
      var chunk='';
      Array.prototype.forEach.call(w, function(ch){
        if(chunk && measure(chunk+ch)>maxW){ lines.push(chunk); chunk=ch; } else { chunk+=ch; }
      });
      return chunk;
    }
    words.forEach(function(w){
      var test = line ? line+' '+w : w;
      if(measure(test)<=maxW){ line=test; return; }
      if(line){ lines.push(line); line=''; }
      if(measure(w)<=maxW){ line=w; } else { line=pushLong(w); }
    });
    lines.push(line);
    return lines;
  }

  function makeStandard(text, opt){
    var jsPDF=window.jspdf.jsPDF;
    var doc=new jsPDF({unit:'mm', format:opt.page});
    doc.setFont(opt.font); doc.setFontSize(opt.sizePt);
    var pw=doc.internal.pageSize.getWidth(), ph=doc.internal.pageSize.getHeight();
    var usableW=pw-opt.margin*2;
    var lineH=opt.sizePt*MM_PT*1.35, ascent=opt.sizePt*MM_PT*0.8;
    var lines=[];
    text.split('\n').forEach(function(par){ wrapParagraph(par, usableW, function(s){ return doc.getTextWidth(s); }).forEach(function(l){ lines.push(l); }); });
    var y=opt.margin+ascent;
    lines.forEach(function(line){
      if(y+lineH-ascent>ph-opt.margin+0.01){ doc.addPage(); y=opt.margin+ascent; }
      if(line!=='') doc.text(line, opt.margin, y);
      y+=lineH;
    });
    if(opt.numbers){
      var n=doc.getNumberOfPages();
      doc.setFont('helvetica'); doc.setFontSize(9); doc.setTextColor(120);
      for(var i=1;i<=n;i++){ doc.setPage(i); doc.text('Page '+i+' of '+n, pw/2, ph-Math.max(3,opt.margin/2.5), {align:'center'}); }
    }
    doc.save('document.pdf');
  }

  var STACKS={
    helvetica:"'Noto Naskh Arabic','Segoe UI','Geeza Pro','Noto Sans','Nirmala UI',Tahoma,Arial,sans-serif",
    times:"'Noto Naskh Arabic','Times New Roman','Noto Serif','Geeza Pro','Nirmala UI',serif",
    courier:"'Courier New','Noto Sans Mono','Noto Naskh Arabic',monospace"
  };

  function makeUnicode(text, opt, onProgress){
    var dims = opt.page==='a4' ? {w:210,h:297} : {w:215.9,h:279.4};
    var scale=2.5, mmToPx=(96/25.4)*scale;
    var pxW=Math.round(dims.w*mmToPx), pxH=Math.round(dims.h*mmToPx), pxM=opt.margin*mmToPx;
    var fontPx=opt.sizePt*(96/72)*scale, lineH=fontPx*1.55;
    var fontCss=fontPx+'px '+(STACKS[opt.font]||STACKS.helvetica);
    var mctx=document.createElement('canvas').getContext('2d');
    mctx.font=fontCss;
    var measure=function(s){ return mctx.measureText(s).width; };
    var maxW=pxW-pxM*2;
    var lines=[]; /* {t:'text', rtl:bool} */
    text.split('\n').forEach(function(par){
      var rtl=isRtl(par);
      wrapParagraph(par, maxW, measure).forEach(function(l){ lines.push({t:l, rtl:rtl}); });
    });
    var footerH = opt.numbers ? fontPx*1.6 : 0;
    var perPage=Math.max(1, Math.floor((pxH-pxM*2-footerH)/lineH));
    var pages=[]; for(var i=0;i<lines.length;i+=perPage){ pages.push(lines.slice(i,i+perPage)); }
    if(!pages.length) pages=[[{t:'',rtl:false}]];

    var jsPDF=window.jspdf.jsPDF;
    var doc=new jsPDF({unit:'mm', format:opt.page});
    pages.forEach(function(pageLines, idx){
      var c=document.createElement('canvas'); c.width=pxW; c.height=pxH;
      var ctx=c.getContext('2d');
      ctx.fillStyle='#ffffff'; ctx.fillRect(0,0,pxW,pxH);
      ctx.fillStyle='#111111'; ctx.font=fontCss; ctx.textBaseline='top';
      pageLines.forEach(function(l,i){
        if(!l.t) return;
        ctx.direction = l.rtl ? 'rtl' : 'ltr';
        ctx.textAlign = l.rtl ? 'right' : 'left';
        ctx.fillText(l.t, l.rtl ? pxW-pxM : pxM, pxM+i*lineH);
      });
      if(opt.numbers){
        ctx.direction='ltr'; ctx.textAlign='center'; ctx.fillStyle='#777777';
        ctx.font=Math.round(fontPx*0.65)+'px '+STACKS.helvetica;
        ctx.fillText('Page '+(idx+1)+' of '+pages.length, pxW/2, pxH-pxM*0.75);
      }
      if(idx>0) doc.addPage();
      doc.addImage(c.toDataURL('image/jpeg',0.92),'JPEG',0,0,dims.w,dims.h);
      onProgress(Math.round(((idx+1)/pages.length)*100));
    });
    doc.save('document.pdf');
  }

  $('tpMake').addEventListener('click', function(){
    if(!TK.checkLibs(['jspdf'])) return;
    var text=clean($('tpText').value);
    if(!text.trim()){ TK.toast('Please enter some text'); return; }
    var btn=this;
    var unicode=$('tpUnicode').checked;
    if(!unicode && needsUnicode(text)){
      unicode=true; $('tpUnicode').checked=true;
      TK.toast('Your text has non-Latin characters - Unicode mode switched on');
    }
    var opt={
      page: $('tpPage').value==='a4'?'a4':'letter',
      margin: Math.min(40,Math.max(5,parseFloat($('tpMargin').value)||18)),
      sizePt: Math.min(36,Math.max(8,parseFloat($('tpSize').value)||14)),
      font: $('tpFont').value,
      numbers: $('tpNums').checked
    };
    TK.busy(btn,true,'Creating...');
    $('tpProgress').classList.toggle('d-none', !unicode);
    /* let the button repaint before the heavy work starts */
    setTimeout(function(){
      try{
        if(unicode) makeUnicode(text,opt,function(p){ $('tpBar').style.width=p+'%'; }); else makeStandard(text,opt);
      }catch(err){ console.error(err); TK.toast('Could not create the PDF: '+(err.message||'unknown error')); }
      TK.busy(btn,false); $('tpProgress').classList.add('d-none'); $('tpBar').style.width='0%';
    }, 30);
  });
})();
</script>
