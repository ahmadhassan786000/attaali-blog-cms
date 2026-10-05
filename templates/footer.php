<footer class="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-5">
        <a class="brand" href="<?= BASE_URL ?>"><span class="brand-box">A</span><span>ATTAALI</span></a>
        <p class="foot-text">In-depth journalism, benchmarks, and tactical guides covering artificial intelligence, developer tooling, and modern software engineering.</p>
        <p class="mono-label">Connect &amp; Syndication</p>
        <div class="d-flex gap-2">
          <a class="social" href="#" aria-label="Share"><i class="bi bi-share"></i></a>
          <a class="social" href="#" aria-label="Code"><i class="bi bi-code"></i></a>
          <a class="social" href="#" aria-label="Community"><i class="bi bi-people-fill"></i></a>
          <a class="social" href="#" aria-label="Video"><i class="bi bi-play-btn"></i></a>
          <a class="social" href="#" aria-label="RSS"><i class="bi bi-rss"></i></a>
        </div>
      </div>
      <div class="col-md-7 col-lg-4"><h2 class="foot-h">Editorial Coverage</h2>
        <ul class="foot-list two-col">
          <?php foreach (['AI Tools'=>'ai-tools','Artificial Intelligence'=>'artificial-intelligence','Web Development'=>'web-development','Software & DevOps'=>'software-productivity','Productivity'=>'productivity','Automation'=>'automation','SEO & Growth'=>'blogging-seo'] as $l => $s): ?>
          <li><a href="<?= e(cat_url($s)) ?>"><?= e($l) ?></a></li><?php endforeach; ?></ul></div>
      <div class="col-md-5 col-lg-3"><h2 class="foot-h">Standards &amp; Operations</h2>
        <ul class="foot-list">
          <?php foreach (['About Us'=>'about-us','Editorial Policy'=>'editorial-policy','Contact Us'=>'contact','Privacy Policy'=>'privacy-policy','Terms & Conditions'=>'terms-and-conditions','Disclaimer'=>'disclaimer','Cookie Policy'=>'cookie-policy','Affiliate Disclosure'=>'affiliate-disclosure'] as $l => $s): ?>
          <li><a href="<?= e(cat_url($s)) ?>"><?= e($l) ?></a></li><?php endforeach; ?></ul></div>
    </div>
    <div class="foot-bottom"><span>&copy; <?= date('Y') ?> ATTAALI. All rights reserved. Independent technology journalism.</span>
      <span><a href="<?= BASE_URL ?>feed.xml"><i class="bi bi-rss"></i> RSS Syndication</a> &nbsp; <a href="#top"><i class="bi bi-arrow-up"></i> Back to Top</a></span></div>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>

