<?php
require __DIR__ . '/includes/init.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$cat = db_row('SELECT * FROM categories WHERE slug = ?', array($slug));
if (!$cat) {
    not_found();
}
$per = max(3, (int)setting('posts_per_page', 9));
$page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$total = (int)db_val("SELECT COUNT(*) FROM posts WHERE status = 'published' AND category_id = ?", array($cat['id']), 0);
$offset = ($page - 1) * $per;
$posts = db_all("SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM posts p LEFT JOIN categories c ON c.id = p.category_id
                 WHERE p.status = 'published' AND p.category_id = ? ORDER BY p.created_at DESC LIMIT " . (int)$per . " OFFSET " . (int)$offset, array($cat['id']));

seo('title', $cat['name'] . ' Articles');
seo('description', $cat['description'] !== '' ? $cat['description'] : 'Browse all articles in ' . $cat['name']);
include __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
  <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li><li class="breadcrumb-item"><a href="<?php echo e(url('blog')); ?>">Blog</a></li><li class="breadcrumb-item active"><?php echo e($cat['name']); ?></li></ol></nav>
  <h1 class="section-title mb-1"><?php echo e($cat['name']); ?></h1>
  <?php if ($cat['description']): ?><p class="section-sub mb-4"><?php echo e($cat['description']); ?></p><?php endif; ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <?php if (!$posts): ?><div class="alert alert-info">No articles in this category yet.</div><?php else: ?>
        <div class="row g-4"><?php foreach ($posts as $p) { echo str_replace('col-md-6 col-lg-4', 'col-md-6', post_card($p)); } ?></div>
        <div class="mt-4"><?php echo paginate($total, $per, $page, url('category/' . rawurlencode($cat['slug']))); ?></div>
      <?php endif; ?>
    </div>
    <div class="col-lg-4"><?php echo render_sidebar(); ?></div>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
