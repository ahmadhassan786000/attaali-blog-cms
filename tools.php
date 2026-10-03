<?php
require __DIR__ . '/includes/init.php';

seo('title', 'All Free Online Tools');
seo('description', 'Browse all free online tools: PDF tools, image tools, text tools and utilities. No signup required and your files stay on your device.');

$grouped = tools_by_category();
$cats = tool_categories();
include __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
  <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li><li class="breadcrumb-item active">Tools</li></ol></nav>
  <div class="text-center mb-4">
    <h1 class="section-title">All Free Online Tools</h1>
    <p class="section-sub">Everything you need, organized by category</p>
    <div class="mx-auto" style="max-width:520px"><input type="search" id="toolFilter" class="form-control form-control-lg" placeholder="Search tools..."></div>
  </div>
  <?php ad('tool_top'); ?>
  <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
    <a href="#" class="cat-pill active" data-cat-pill="all"><i class="bi bi-grid"></i> All</a>
    <?php foreach ($cats as $cn => $cm): if (!$grouped[$cn]) { continue; } ?>
      <a href="#" class="cat-pill" data-cat-pill="<?php echo e($cn); ?>"><i class="bi <?php echo e($cm['icon']); ?>"></i> <?php echo e($cn); ?></a>
    <?php endforeach; ?>
  </div>
  <div class="row g-3">
    <?php foreach ($grouped as $cn => $list) { foreach ($list as $slug => $tool) { echo tool_card($slug, $tool); } } ?>
  </div>
  <p id="toolEmpty" class="text-center text-muted d-none mt-4">No tools found. Try a different keyword.</p>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
