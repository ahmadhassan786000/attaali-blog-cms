<div class="row g-4">
  <div class="col-lg-5">
    <label class="form-label small" for="acDob">Date of birth</label>
    <input id="acDob" type="date" class="form-control mb-3">
    <label class="form-label small" for="acAt">Age at date (default: today)</label>
    <input id="acAt" type="date" class="form-control mb-3">
    <button id="acGo" class="btn btn-primary btn-lg w-100"><i class="bi bi-calculator"></i> Calculate Age</button>
  </div>
  <div class="col-lg-7">
    <div id="acResult" class="d-none">
      <div class="row g-2 mb-3">
        <div class="col-4"><div class="stat-box"><div class="n" id="acYears">0</div><div class="l">Years</div></div></div>
        <div class="col-4"><div class="stat-box"><div class="n" id="acMonths">0</div><div class="l">Months</div></div></div>
        <div class="col-4"><div class="stat-box"><div class="n" id="acDays">0</div><div class="l">Days</div></div></div>
      </div>
      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between">Total days lived <strong id="acTotalDays">-</strong></li>
        <li class="list-group-item d-flex justify-content-between">Total weeks lived <strong id="acTotalWeeks">-</strong></li>
        <li class="list-group-item d-flex justify-content-between">Total hours lived <strong id="acTotalHours">-</strong></li>
        <li class="list-group-item d-flex justify-content-between">Next birthday in <strong id="acNextBday">-</strong></li>
      </ul>
    </div>
    <div id="acEmpty" class="text-muted text-center py-5"><i class="bi bi-calendar-heart" style="font-size:3rem"></i><p class="mt-2 mb-0">Enter your date of birth to see your exact age.</p></div>
  </div>
</div>
<script>
(function(){
  var $=TK.$;
  var today=new Date(); $('acAt').valueAsDate=today;

  /* Calendar-day difference that ignores local time-of-day and DST, so a 14-calendar-day span is
     always 14 even if a daylight-saving clock change happens in between. */
  function calendarDays(a,b){
    var ua=Date.UTC(a.getFullYear(),a.getMonth(),a.getDate());
    var ub=Date.UTC(b.getFullYear(),b.getMonth(),b.getDate());
    return Math.round((ub-ua)/86400000);
  }
  function daysInMonth(y,m){ return new Date(y,m+1,0).getDate(); }
  /* Add y/m to a date, clamping the day to the target month's length instead of overflowing into
     the next month (so "31 Mar" + 0y - 1m lands on 28/29 Feb, not 3 Mar). */
  function addMonthsClamped(date,yAdd,mAdd){
    var y=date.getFullYear()+yAdd, m=date.getMonth()+mAdd;
    y+=Math.floor(m/12); m=((m%12)+12)%12;
    var day=Math.min(date.getDate(), daysInMonth(y,m));
    return new Date(y,m,day);
  }

  function ageBetween(dob, at){
    var years=at.getFullYear()-dob.getFullYear();
    var months=at.getMonth()-dob.getMonth();
    if(months<0){ years--; months+=12; }
    var temp=addMonthsClamped(dob, years, months);
    if(temp>at){ months--; if(months<0){ months+=12; years--; } temp=addMonthsClamped(dob, years, months); }
    var days=calendarDays(temp, at);
    return {years:years, months:months, days:days};
  }

  $('acGo').addEventListener('click', function(){
    var dobStr=$('acDob').value; if(!dobStr){ TK.toast('Please select your date of birth'); return; }
    var dob=new Date(dobStr+'T00:00:00');
    var at=$('acAt').value ? new Date($('acAt').value+'T00:00:00') : new Date(new Date().toDateString());
    if(isNaN(dob)){ TK.toast('Please enter a valid date of birth'); return; }
    if(dob>at){ TK.toast('Date of birth must be before the selected date'); return; }

    var age=ageBetween(dob, at);
    $('acYears').textContent=age.years; $('acMonths').textContent=age.months; $('acDays').textContent=age.days;

    var totalDays=calendarDays(dob, at);
    $('acTotalDays').textContent=totalDays.toLocaleString();
    $('acTotalWeeks').textContent=Math.floor(totalDays/7).toLocaleString();
    $('acTotalHours').textContent=(totalDays*24).toLocaleString();

    var next=new Date(at.getFullYear(), dob.getMonth(), dob.getDate());
    if(calendarDays(at,next)<0) next=new Date(at.getFullYear()+1, dob.getMonth(), dob.getDate());
    var daysToNext=calendarDays(at, next);
    $('acNextBday').textContent = daysToNext===0 ? 'Today! \ud83c\udf89' : daysToNext+' day(s)';

    $('acResult').classList.remove('d-none'); $('acEmpty').classList.add('d-none');
  });
})();
</script>
