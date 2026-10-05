<?php
require __DIR__ . '/includes/init.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$page = db_row('SELECT * FROM pages WHERE slug = ?', array($slug));
if (!$page) {
    not_found();
}
seo('title', $page['title']);
seo('description', $page['meta_desc'] !== '' ? $page['meta_desc'] : text_excerpt($page['content'], 160));
include __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
  <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li><li class="breadcrumb-item active"><?php echo e($page['title']); ?></li></ol></nav>
  <div class="row justify-content-center">
    <div class="col-lg-9">
      <div class="article">
        <h1 class="fw-bold mb-4"><?php echo e($page['title']); ?></h1>
        <div class="article-content"><?php echo replace_tokens($page['content']); ?></div>
      </div>
    </div>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
