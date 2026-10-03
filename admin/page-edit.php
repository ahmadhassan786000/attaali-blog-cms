<?php
require __DIR__ . '/_base.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$page = $id ? db_row('SELECT * FROM pages WHERE id = ?', array($id)) : null;
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        $errors[] = 'Security check failed, please try again.';
    } else {
        $title = trim($_POST['title']);
        $content = $_POST['content'];
        $metaDesc = trim($_POST['meta_desc']);
        $slugInput = trim($_POST['slug']);
        $footer = isset($_POST['show_in_footer']) ? 1 : 0;
        if ($title === '') { $errors[] = 'Title is required.'; }
        if (!$errors) {
            $slug = unique_slug('pages', slugify($slugInput !== '' ? $slugInput : $title), $id);
            $now = date('Y-m-d H:i:s');
            if ($page) {
                db_exec('UPDATE pages SET title=?, slug=?, content=?, meta_desc=?, show_in_footer=?, updated_at=? WHERE id=?', array($title, $slug, $content, $metaDesc, $footer, $now, $id));
                flash('success', 'Page updated.');
            } else {
                db_exec('INSERT INTO pages (title, slug, content, meta_desc, show_in_footer, updated_at) VALUES (?,?,?,?,?,?)', array($title, $slug, $content, $metaDesc, $footer, $now));
                flash('success', 'Page created.');
            }
            redirect(admin_url('pages.php'));
        }
    }
}
$v = function ($key, $default = '') use ($page) {
    if (isset($_POST[$key])) { return $_POST[$key]; }
    return $page && isset($page[$key]) ? $page[$key] : $default;
};
admin_header($page ? 'Edit Page' : 'New Page');
?>
<?php foreach ($errors as $er): ?><div class="alert alert-danger py-2"><?php echo e($er); ?></div><?php endforeach; ?>
<form method="post">
  <?php echo csrf_field(); ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card"><div class="card-body">
        <label class="form-label fw-bold">Title *</label>
        <input class="form-control form-control-lg mb-3" name="title" value="<?php echo e($v('title')); ?>" required>
        <label class="form-label small">URL Slug</label>
        <input class="form-control mb-3" name="slug" value="<?php echo e($v('slug')); ?>">
        <label class="form-label fw-bold">Content</label>
        <textarea class="form-control" name="content" id="pageContent" rows="16"><?php echo e($v('content')); ?></textarea>
        <p class="small text-muted mt-2 mb-0">You can use {{site_name}}, {{site_url}}, {{email}} and {{year}} anywhere in the content.</p>
      </div></div>
    </div>
    <div class="col-lg-4">
      <div class="card mb-3"><div class="card-body">
        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="show_in_footer" id="sif" <?php echo (int)$v('show_in_footer', 1) ? 'checked' : ''; ?>><label class="form-check-label" for="sif">Show link in footer</label></div>
        <button class="btn btn-primary w-100"><i class="bi bi-check2"></i> Save Page</button>
      </div></div>
      <div class="card"><div class="card-body">
        <label class="form-label small">Meta Description</label>
        <textarea class="form-control" name="meta_desc" rows="3"><?php echo e($v('meta_desc')); ?></textarea>
      </div></div>
    </div>
  </div>
</form>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>#quillEditor2{background:#fff;min-height:320px}</style>
<script>
(function(){
  var source=document.getElementById('pageContent');
  var wrap=document.createElement('div'); wrap.id='quillEditor2';
  source.style.display='none'; source.parentNode.insertBefore(wrap, source);
  var quill=new Quill(wrap, { theme:'snow', modules:{ toolbar:[[{header:[2,3,false]}],['bold','italic','underline','link'],[{list:'ordered'},{list:'bullet'}],['clean']] } });
  quill.root.innerHTML = source.value;
  quill.on('text-change', function(){ source.value = quill.root.innerHTML; });
  document.querySelector('form').addEventListener('submit', function(){ source.value = quill.root.innerHTML; });
})();
</script>
<?php admin_footer(); ?>
