<?php
$_footer_pages = db_all('SELECT title, slug FROM pages WHERE show_in_footer = 1 ORDER BY id ASC');
$_popular = array_slice(all_tools(), 0, 6, true);
$_social = array(
    'facebook' => 'bi-facebook', 'twitter' => 'bi-twitter-x', 'youtube' => 'bi-youtube',
    'instagram' => 'bi-instagram', 'whatsapp' => 'bi-whatsapp',
);
?>
<?php ad('footer'); ?>
<footer class="site-footer mt-5">
  <div class="container py-5">
    <div class="row g-4">
      <div class="col-lg-4">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2 mb-3" href="<?php echo e(url('/')); ?>"><span class="brand-mark"><i class="bi bi-lightning-charge-fill"></i></span><?php echo e(site_name()); ?></a>
        <p class="text-muted small mb-3"><?php echo e(setting('tagline', '')); ?>. Fast, free and private tools that run right in your browser.</p>
        <div class="d-flex gap-2">
          <?php foreach ($_social as $k => $ic): $v = setting($k, ''); if ($v === '') { continue; } ?>
            <a class="social-btn" href="<?php echo e($v); ?>" target="_blank" rel="noopener" aria-label="<?php echo e(ucfirst($k)); ?>"><i class="bi <?php echo e($ic); ?>"></i></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <h6 class="fw-bold mb-3">Popular Tools</h6>
        <ul class="list-unstyled small footer-links">
          <?php foreach ($_popular as $slug => $tool): ?><li><a href="<?php echo e(tool_url($slug)); ?>"><?php echo e($tool['name']); ?></a></li><?php endforeach; ?>
          <li><a href="<?php echo e(url('tools')); ?>">All tools &rarr;</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="fw-bold mb-3">Explore</h6>
        <ul class="list-unstyled small footer-links">
          <li><a href="<?php echo e(url('/')); ?>">Home</a></li>
          <li><a href="<?php echo e(url('blog')); ?>">Blog</a></li>
          <li><a href="<?php echo e(url('contact')); ?>">Contact</a></li>
          <li><a href="<?php echo e(url('sitemap')); ?>">Sitemap</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-3">
        <h6 class="fw-bold mb-3">Legal</h6>
        <ul class="list-unstyled small footer-links">
          <?php foreach ($_footer_pages as $fp): ?><li><a href="<?php echo e(url($fp['slug'])); ?>"><?php echo e($fp['title']); ?></a></li><?php endforeach; ?>
        </ul>
      </div>
    </div>
    <hr class="my-4">
    <div class="d-flex flex-column flex-md-row justify-content-between small text-muted">
      <div>&copy; <?php echo date('Y'); ?> <?php echo e(site_name()); ?>. All rights reserved.</div>
      <div>Your files never leave your device <i class="bi bi-shield-check text-success"></i></div>
    </div>
  </div>
</footer>

<div id="cookieBar" class="cookie-bar d-none">
  <div class="container d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
    <span class="small">We use cookies to improve your experience and to show relevant ads. See our <a href="<?php echo e(url('privacy-policy')); ?>">Privacy Policy</a>.</span>
    <button class="btn btn-primary btn-sm" id="cookieOk" type="button">Got it</button>
  </div>
</div>

<button id="toTop" class="to-top d-none" type="button" aria-label="Back to top"><i class="bi bi-arrow-up"></i></button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(asset('js/main.js')); ?>"></script>
<?php echo setting('footer_code', ''); ?>
</body>
</html>
