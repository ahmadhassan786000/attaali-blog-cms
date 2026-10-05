<?php
require __DIR__ . '/includes/init.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$posts = array();
$matchedTools = array();
if ($q !== '') {
    $like = '%' . $q . '%';
    $posts = db_all("SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM posts p LEFT JOIN categories c ON c.id = p.category_id
                     WHERE p.status = 'published' AND (p.title LIKE ? OR p.excerpt LIKE ? OR p.content LIKE ?)
                     ORDER BY p.created_at DESC LIMIT 24", array($like, $like, $like));
    $ql = strtolower($q);
    foreach (all_tools() as $slug => $t) {
        if (strpos(strtolower($t['name'] . ' ' . $t['desc']), $ql) !== false) {
            $matchedTools[$slug] = $t;
        }
    }
}
seo('title', $q !== '' ? 'Search results for "' . $q . '"' : 'Search');
seo('noindex', '1');
include __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
  <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li><li class="breadcrumb-item active">Search</li></ol></nav>
  <h1 class="section-title mb-4">Search<?php echo $q !== '' ? ': "' . e($q) . '"' : ''; ?></h1>
  <?php if ($q === ''): ?>
    <div class="alert alert-info">Type something in the search box above to find tools and articles.</div>
  <?php else: ?>
    <?php if ($matchedTools): ?>
      <h2 class="h5 fw-bold mb-3">Tools</h2>
      <div class="row g-3 mb-5"><?php foreach ($matchedTools as $slug => $t) { echo tool_card($slug, $t); } ?></div>
    <?php endif; ?>
    <h2 class="h5 fw-bold mb-3">Articles</h2>
    <?php if (!$posts): ?>
      <div class="alert alert-info">No articles found for "<?php echo e($q); ?>". Try different keywords.</div>
    <?php else: ?>
      <div class="row g-4"><?php foreach ($posts as $p) { echo str_replace('col-md-6 col-lg-4', 'col-md-4', post_card($p)); } ?></div>
    <?php endif; ?>
  <?php endif; ?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
