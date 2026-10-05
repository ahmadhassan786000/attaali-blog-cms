<?php
require __DIR__ . '/_base.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $id ? db_row('SELECT * FROM posts WHERE id = ?', array($id)) : null;
if ($id && !$post) {
    flash('danger', 'Post not found.');
    redirect(admin_url('posts.php'));
}
$categories = db_all('SELECT * FROM categories ORDER BY name');
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        $errors[] = 'Security check failed, please try again.';
    } else {
        $title = trim($_POST['title']);
        $content = $_POST['content'];
        $excerpt = trim($_POST['excerpt']);
        $catId = (int)$_POST['category_id'] ?: null;
        $status = $_POST['status'] === 'draft' ? 'draft' : 'published';
        $metaTitle = trim($_POST['meta_title']);
        $metaDesc = trim($_POST['meta_desc']);
        $slugInput = trim($_POST['slug']);

        if ($title === '') { $errors[] = 'Title is required.'; }
        if (trim(strip_tags($content)) === '') { $errors[] = 'Content cannot be empty.'; }

        $imagePath = $post ? $post['image'] : '';
        if (!empty($_FILES['image']['name'])) {
            list($ok, $res) = upload_image($_FILES['image']);
            if ($ok) {
                if ($post && $post['image']) { delete_upload($post['image']); }
                $imagePath = $res;
            } else {
                $errors[] = $res;
            }
        }
        if (isset($_POST['remove_image']) && $imagePath) {
            delete_upload($imagePath);
            $imagePath = '';
        }

        if (!$errors) {
            $slug = unique_slug('posts', slugify($slugInput !== '' ? $slugInput : $title), $id);
            $now = date('Y-m-d H:i:s');
            if ($post) {
                db_exec('UPDATE posts SET title=?, slug=?, excerpt=?, content=?, image=?, category_id=?, meta_title=?, meta_desc=?, status=?, updated_at=? WHERE id=?',
                    array($title, $slug, $excerpt, $content, $imagePath, $catId, $metaTitle, $metaDesc, $status, $now, $id));
                flash('success', 'Post updated successfully.');
            } else {
                db_exec('INSERT INTO posts (title, slug, excerpt, content, image, category_id, meta_title, meta_desc, status, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)',
                    array($title, $slug, $excerpt, $content, $imagePath, $catId, $metaTitle, $metaDesc, $status, $now, $now));
                flash('success', 'Post created successfully.');
            }
            redirect(admin_url('posts.php'));
        }
    }
}

$v = function ($key, $default = '') use ($post) {
    if (isset($_POST[$key])) { return $_POST[$key]; }
    return $post && isset($post[$key]) ? $post[$key] : $default;
};

admin_header($post ? 'Edit Post' : 'New Post');
?>
<?php foreach ($errors as $er): ?><div class="alert alert-danger py-2"><?php echo e($er); ?></div><?php endforeach; ?>
<form method="post" enctype="multipart/form-data">
  <?php echo csrf_field(); ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card mb-3"><div class="card-body">
        <label class="form-label fw-bold">Title *</label>
        <input class="form-control form-control-lg mb-3" name="title" value="<?php echo e($v('title')); ?>" required>
        <label class="form-label small">URL Slug (leave blank to auto-generate)</label>
        <input class="form-control mb-3" name="slug" value="<?php echo e($v('slug')); ?>" placeholder="my-post-title">
        <label class="form-label fw-bold">Content *</label>
        <textarea class="form-control" name="content" id="postContent" rows="16"><?php echo e($v('content')); ?></textarea>
        <p class="small text-muted mt-2 mb-0">Tip: use the toolbar for headings, bold, links and images. You can also paste HTML.</p>
      </div></div>
      <div class="card"><div class="card-body">
        <label class="form-label fw-bold">Excerpt (short summary shown on listing pages)</label>
        <textarea class="form-control" name="excerpt" rows="2"><?php echo e($v('excerpt')); ?></textarea>
      </div></div>
    </div>
    <div class="col-lg-4">
      <div class="card mb-3"><div class="card-body">
        <label class="form-label fw-bold">Publish</label>
        <select class="form-select mb-3" name="status">
          <option value="published" <?php echo $v('status', 'published') === 'published' ? 'selected' : ''; ?>>Published</option>
          <option value="draft" <?php echo $v('status') === 'draft' ? 'selected' : ''; ?>>Draft</option>
        </select>
        <button class="btn btn-primary w-100"><i class="bi bi-check2"></i> <?php echo $post ? 'Update Post' : 'Publish Post'; ?></button>
        <?php if ($post): ?><a class="btn btn-outline-secondary w-100 mt-2" href="<?php echo e(post_url($post)); ?>" target="_blank"><i class="bi bi-eye"></i> View Live</a><?php endif; ?>
      </div></div>
      <div class="card mb-3"><div class="card-body">
        <label class="form-label fw-bold">Category</label>
        <select class="form-select" name="category_id">
          <option value="">- None -</option>
          <?php foreach ($categories as $c): ?><option value="<?php echo (int)$c['id']; ?>" <?php echo (int)$v('category_id') === (int)$c['id'] ? 'selected' : ''; ?>><?php echo e($c['name']); ?></option><?php endforeach; ?>
        </select>
      </div></div>
      <div class="card mb-3"><div class="card-body">
        <label class="form-label fw-bold">Featured Image</label>
        <?php if ($post && $post['image']): ?>
          <img src="<?php echo e(url($post['image'])); ?>" class="img-fluid rounded mb-2">
          <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="remove_image" id="rmImg"><label class="form-check-label small" for="rmImg">Remove current image</label></div>
        <?php endif; ?>
        <input type="file" class="form-control" name="image" accept="image/*">
      </div></div>
      <div class="card"><div class="card-body">
        <label class="form-label fw-bold">SEO</label>
        <label class="form-label small">Meta Title</label>
        <input class="form-control mb-2" name="meta_title" value="<?php echo e($v('meta_title')); ?>">
        <label class="form-label small">Meta Description</label>
        <textarea class="form-control" name="meta_desc" rows="3"><?php echo e($v('meta_desc')); ?></textarea>
      </div></div>
    </div>
  </div>
</form>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>#quillEditor{background:#fff;min-height:320px}</style>
<div id="quillToolbarHolder"></div>
<script>
(function(){
  var source=document.getElementById('postContent');
  var wrap=document.createElement('div'); wrap.id='quillEditor';
  source.style.display='none'; source.parentNode.insertBefore(wrap, source);
  var quill=new Quill(wrap, { theme:'snow', modules:{ toolbar:[[{header:[2,3,false]}],['bold','italic','underline','link'],[{list:'ordered'},{list:'bullet'}],['blockquote','code-block'],['image'],['clean']] } });
  quill.root.innerHTML = source.value;
  quill.on('text-change', function(){ source.value = quill.root.innerHTML; });
  document.querySelector('form').addEventListener('submit', function(){ source.value = quill.root.innerHTML; });
})();
</script>
<?php admin_footer(); ?>
