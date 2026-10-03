<?php
require __DIR__ . '/includes/init.php';

seo('title', '');
seo('description', setting('meta_description'));
seo('schema', array(json_encode(array(
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => site_name(),
    'url' => abs_url(''),
    'potentialAction' => array('@type' => 'SearchAction', 'target' => abs_url('search') . '?q={search_term_string}', 'query-input' => 'required name=search_term_string'),
), JSON_UNESCAPED_SLASHES)));

$tools = all_tools();
$cats = tool_categories();
$latest = db_all("SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM posts p LEFT JOIN categories c ON c.id = p.category_id
                  WHERE p.status = 'published' ORDER BY p.created_at DESC LIMIT 6");
include __DIR__ . '/includes/header.php';
?>
<section class="hero text-center">
  <div class="container position-relative">
    <h1 class="display-5 mb-3">Free Online Tools for <span style="color:#a5f3fc">Everyday Work</span></h1>
    <p class="lead mx-auto" style="max-width:680px">Convert text and images to PDF, compress photos, extract text, make QR codes and more. No signup, no watermark and your files never leave your device.</p>
    <div class="hero-search">
      <div class="input-group input-group-lg shadow-lg" style="border-radius:14px">
        <input type="search" id="toolFilter" class="form-control" placeholder="Search a tool... e.g. image to pdf" aria-label="Search tools">
        <button class="btn btn-dark" type="button" onclick="document.getElementById('toolsGrid').scrollIntoView({behavior:'smooth'})"><i class="bi bi-search"></i></button>
      </div>
    </div>
    <div class="hero-badges mt-4">
      <span><i class="bi bi-check-circle"></i> 100% Free</span>
      <span><i class="bi bi-shield-lock"></i> Private &amp; Secure</span>
      <span><i class="bi bi-lightning-charge"></i> Instant Results</span>
      <span><i class="bi bi-phone"></i> Works on Mobile</span>
    </div>
  </div>
</section>

<section class="container py-5" id="toolsGrid">
  <div class="text-center mb-4">
    <h2 class="section-title">Popular Tools</h2>
    <p class="section-sub">Pick a tool and get the job done in seconds</p>
  </div>
  <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
    <a href="#" class="cat-pill active" data-cat-pill="all"><i class="bi bi-grid"></i> All</a>
    <?php foreach ($cats as $cn => $cm): ?>
      <a href="#" class="cat-pill" data-cat-pill="<?php echo e($cn); ?>"><i class="bi <?php echo e($cm['icon']); ?>"></i> <?php echo e($cn); ?></a>
    <?php endforeach; ?>
  </div>
  <div class="row g-3">
    <?php foreach ($tools as $slug => $tool) { echo tool_card($slug, $tool); } ?>
  </div>
  <p id="toolEmpty" class="text-center text-muted d-none mt-4">No tools found. Try a different keyword.</p>
</section>

<section class="container pb-4">
  <div class="row g-3">
    <div class="col-md-4"><div class="card feature-box h-100"><i class="bi bi-shield-check"></i><h3 class="h5 mt-2">Private by design</h3><p class="text-muted mb-0 small">Files are processed inside your browser. Nothing is uploaded to our servers.</p></div></div>
    <div class="col-md-4"><div class="card feature-box h-100"><i class="bi bi-rocket-takeoff"></i><h3 class="h5 mt-2">Fast &amp; simple</h3><p class="text-muted mb-0 small">No installation and no registration. Open a tool and start working immediately.</p></div></div>
    <div class="col-md-4"><div class="card feature-box h-100"><i class="bi bi-gift"></i><h3 class="h5 mt-2">Free forever</h3><p class="text-muted mb-0 small">Unlimited use with no watermarks, no hidden fees and no daily limits.</p></div></div>
  </div>
</section>

<?php if ($latest): ?>
<section class="container py-5">
  <div class="d-flex justify-content-between align-items-end mb-4">
    <div><h2 class="section-title mb-1">Latest from the Blog</h2><p class="section-sub mb-0">Guides, tips and tutorials</p></div>
    <a class="btn btn-outline-primary" href="<?php echo e(url('blog')); ?>">View all <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="row g-4">
    <?php foreach ($latest as $p) { echo post_card($p); } ?>
  </div>
</section>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
