<?php
require __DIR__ . '/includes/init.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$post = db_row("SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM posts p LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.slug = ? AND p.status = 'published'", array($slug));
if (!$post) {
    not_found();
}
db_exec('UPDATE posts SET views = views + 1 WHERE id = ?', array($post['id']));

seo('title', $post['title']);
seo('description', $post['meta_desc'] !== '' ? $post['meta_desc'] : text_excerpt($post['content'], 160));
seo('type', 'article');
seo('image', post_image($post));
seo('schema', array(json_encode(array(
    '@context' => 'https://schema.org', '@type' => 'Article',
    'headline' => $post['title'], 'image' => post_image($post),
    'datePublished' => date('c', strtotime($post['created_at'])), 'dateModified' => date('c', strtotime($post['updated_at'])),
    'author' => array('@type' => 'Organization', 'name' => site_name()),
    'publisher' => array('@type' => 'Organization', 'name' => site_name()),
    'mainEntityOfPage' => abs_url('blog/' . $post['slug']),
), JSON_UNESCAPED_SLASHES)));

$related = db_all("SELECT id, title, slug, image, created_at, excerpt, content, cat_slug, cat_name FROM (
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM posts p LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.status = 'published' AND p.id <> ? AND (p.category_id = ? OR ? IS NULL) ORDER BY p.created_at DESC LIMIT 3
) t", array($post['id'], $post['category_id'], $post['category_id']));

$shareUrl = urlencode(abs_url('blog/' . $post['slug']));
$shareTitle = urlencode($post['title']);
$hasArticleImage = preg_match('/<img\b/i', $post['content']) === 1;
include __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
  <nav aria-label="breadcrumb"><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(url('blog')); ?>">Blog</a></li>
    <?php if ($post['cat_name']): ?><li class="breadcrumb-item"><a href="<?php echo e(url('category/' . rawurlencode($post['cat_slug']))); ?>"><?php echo e($post['cat_name']); ?></a></li><?php endif; ?>
    <li class="breadcrumb-item active text-truncate" style="max-width:220px"><?php echo e($post['title']); ?></li>
  </ol></nav>

  <div class="row g-4">
    <div class="col-lg-8">
      <article class="article">
        <?php if ($post['cat_name']): ?><a class="badge badge-cat text-decoration-none mb-2" href="<?php echo e(url('category/' . rawurlencode($post['cat_slug']))); ?>"><?php echo e($post['cat_name']); ?></a><?php endif; ?>
        <h1 class="fw-bold mb-2"><?php echo e($post['title']); ?></h1>
        <div class="post-meta mb-3"><i class="bi bi-calendar3"></i> <?php echo e(fdate($post['created_at'])); ?> &middot; <i class="bi bi-clock"></i> <?php echo reading_time($post['content']); ?> min read &middot; <i class="bi bi-eye"></i> <?php echo number_short($post['views']); ?> views</div>
        <?php if (!empty($post['image']) && !$hasArticleImage): ?><img src="<?php echo e(post_image($post)); ?>" class="w-100 rounded mb-4" alt="<?php echo e($post['title']); ?>" loading="lazy"><?php endif; ?>
        <div class="article-content"><?php echo $post['content']; ?></div>
        <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
          <span class="fw-bold me-2">Share:</span>
          <a class="share-btn share-fb" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $shareUrl; ?>"><i class="bi bi-facebook"></i> Facebook</a>
          <a class="share-btn share-tw" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?php echo $shareUrl; ?>&text=<?php echo $shareTitle; ?>"><i class="bi bi-twitter-x"></i> X</a>
          <a class="share-btn share-wa" target="_blank" rel="noopener" href="https://wa.me/?text=<?php echo $shareTitle; ?>%20<?php echo $shareUrl; ?>"><i class="bi bi-whatsapp"></i> WhatsApp</a>
          <a class="share-btn share-in" target="_blank" rel="noopener" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $shareUrl; ?>"><i class="bi bi-linkedin"></i> LinkedIn</a>
        </div>
      </article>
      <?php ad('in_article'); ?>
      <?php if ($related): ?>
      <div class="mt-4">
        <h2 class="h4 fw-bold mb-3">You may also like</h2>
        <div class="row g-4"><?php foreach ($related as $r) { echo str_replace('col-md-6 col-lg-4', 'col-md-4', post_card($r)); } ?></div>
      </div>
      <?php endif; ?>
    </div>
    <div class="col-lg-4"><?php echo render_sidebar(); ?></div>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
