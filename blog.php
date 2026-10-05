<?php
require __DIR__ . '/includes/init.php';

$per = max(3, (int)setting('posts_per_page', 9));
$page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$total = (int)db_val("SELECT COUNT(*) FROM posts WHERE status = 'published'", array(), 0);
$offset = ($page - 1) * $per;
$posts = db_all("SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM posts p LEFT JOIN categories c ON c.id = p.category_id
                 WHERE p.status = 'published' ORDER BY p.created_at DESC LIMIT " . (int)$per . " OFFSET " . (int)$offset);

seo('title', 'Blog' . ($page > 1 ? ' - Page ' . $page : ''));
seo('description', 'Read the latest guides, tutorials and tips about PDF, images, productivity and free online tools.');
if ($page > 1) {
    seo('canonical', abs_url('blog?page=' . $page));
}
include __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
  <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li><li class="breadcrumb-item active">Blog</li></ol></nav>
  <h1 class="section-title mb-4">Blog</h1>
  <div class="row g-4">
    <div class="col-lg-8">
      <?php if (!$posts): ?>
        <div class="alert alert-info">No articles published yet. Please check back soon.</div>
      <?php else: ?>
        <div class="row g-4">
          <?php foreach ($posts as $i => $p) { echo str_replace('col-md-6 col-lg-4', 'col-md-6', post_card($p)); if ($i == 1) { echo '<div class="col-12">'; ad('in_article'); echo '</div>'; } } ?>
        </div>
        <div class="mt-4"><?php echo paginate($total, $per, $page, url('blog')); ?></div>
      <?php endif; ?>
    </div>
    <div class="col-lg-4"><?php echo render_sidebar(); ?></div>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
