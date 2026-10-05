<?php
require __DIR__ . '/includes/init.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$tool = get_tool($slug);
if (!$tool || !$tool['enabled'] || !preg_match('/^[a-z0-9-]+$/', $slug)) {
    not_found();
}

// count views (one per visit)
db_exec('INSERT INTO tools_status (slug, enabled, views) VALUES (?, 1, 1) ON DUPLICATE KEY UPDATE views = views + 1', array($slug));

$cats = tool_categories();
$catMeta = $cats[$tool['cat']];
$title = $tool['name'] . ' - Free Online';
seo('title', $title);
seo('description', $tool['desc']);

$faqEntities = array();
foreach ($tool['faq'] as $f) {
    $faqEntities[] = array('@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f[1]));
}
$schemas = array(
    json_encode(array(
        '@context' => 'https://schema.org', '@type' => 'WebApplication', 'name' => $tool['name'],
        'url' => abs_url('tools/' . $slug), 'description' => $tool['desc'],
        'applicationCategory' => 'UtilitiesApplication', 'operatingSystem' => 'Any',
        'offers' => array('@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'),
    ), JSON_UNESCAPED_SLASHES),
    json_encode(array(
        '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => array(
            array('@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => abs_url('')),
            array('@type' => 'ListItem', 'position' => 2, 'name' => 'Tools', 'item' => abs_url('tools')),
            array('@type' => 'ListItem', 'position' => 3, 'name' => $tool['name'], 'item' => abs_url('tools/' . $slug)),
        ),
    ), JSON_UNESCAPED_SLASHES),
);
if ($faqEntities) {
    $schemas[] = json_encode(array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faqEntities), JSON_UNESCAPED_SLASHES);
}
seo('schema', $schemas);

$related = array();
foreach (all_tools() as $s => $t) {
    if ($s !== $slug && $t['cat'] === $tool['cat']) {
        $related[$s] = $t;
    }
}
if (count($related) < 4) {
    foreach (all_tools() as $s => $t) {
        if ($s !== $slug && !isset($related[$s])) {
            $related[$s] = $t;
        }
        if (count($related) >= 4) {
            break;
        }
    }
}
$related = array_slice($related, 0, 4, true);

include __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
  <nav aria-label="breadcrumb"><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(url('tools')); ?>">Tools</a></li>
    <li class="breadcrumb-item active"><?php echo e($tool['name']); ?></li>
  </ol></nav>

  <div class="d-flex align-items-center gap-3 mb-3">
    <div class="tool-icon tool-header-icon c-<?php echo e($catMeta['color']); ?>"><i class="bi <?php echo e($tool['icon']); ?>"></i></div>
    <div>
      <h1 class="h2 fw-bold mb-1"><?php echo e($tool['name']); ?></h1>
      <p class="text-muted mb-0"><?php echo e($tool['desc']); ?></p>
    </div>
  </div>

  <?php ad('tool_top'); ?>

  <section class="tool-shell mb-4" id="toolArea">
    <script src="<?php echo e(asset('js/tools-common.js')); ?>"></script>
    <?php foreach ($tool['libs'] as $lib): ?><script src="<?php echo e($lib); ?>"></script>
    <?php endforeach; ?>
    <?php include SITE_ROOT . '/tool-ui/' . $slug . '.php'; ?>
  </section>

  <?php ad('tool_bottom'); ?>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="article mb-4">
        <h2 class="h4 fw-bold">About <?php echo e($tool['name']); ?></h2>
        <p class="mb-4"><?php echo e($tool['about']); ?></p>
        <h2 class="h4 fw-bold">How to use</h2>
        <ol class="steps-list mb-0">
          <?php foreach ($tool['how'] as $step): ?><li><?php echo e($step); ?></li><?php endforeach; ?>
        </ol>
      </div>

      <?php if ($tool['faq']): ?>
      <div class="article">
        <h2 class="h4 fw-bold mb-3">Frequently Asked Questions</h2>
        <div class="accordion" id="faqAcc">
          <?php foreach ($tool['faq'] as $i => $f): ?>
          <div class="accordion-item">
            <h3 class="accordion-header"><button class="accordion-button <?php echo $i ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo $i; ?>"><?php echo e($f[0]); ?></button></h3>
            <div id="faq<?php echo $i; ?>" class="accordion-collapse collapse <?php echo $i ? '' : 'show'; ?>" data-bs-parent="#faqAcc"><div class="accordion-body"><?php echo e($f[1]); ?></div></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-body">
          <h2 class="h6 fw-bold"><i class="bi bi-shield-check text-success"></i> Your privacy</h2>
          <p class="small text-muted mb-0"><?php echo e(isset($tool['privacy']) ? $tool['privacy'] : 'This tool runs entirely in your browser. Your files and text are never uploaded to our servers.'); ?></p>
        </div>
      </div>
      <?php ad('sidebar'); ?>
      <div class="card">
        <div class="card-header bg-transparent fw-bold">Related tools</div>
        <ul class="list-group list-group-flush">
          <?php foreach ($related as $s => $t): ?>
            <li class="list-group-item bg-transparent"><a href="<?php echo e(tool_url($s)); ?>"><i class="bi <?php echo e($t['icon']); ?>"></i> <?php echo e($t['name']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
