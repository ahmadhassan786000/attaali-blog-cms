<?php
require __DIR__ . '/_base.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    if ($name !== '') {
        $slug = unique_slug('categories', slugify($name));
        db_exec('INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)', array($name, $slug, $desc));
        flash('success', 'Category added.');
    }
    redirect(admin_url('categories.php'));
}
if (isset($_GET['delete']) && csrf_ok()) {
    $id = (int)$_GET['delete'];
    db_exec('UPDATE posts SET category_id = NULL WHERE category_id = ?', array($id));
    db_exec('DELETE FROM categories WHERE id = ?', array($id));
    flash('success', 'Category deleted.');
    redirect(admin_url('categories.php'));
}

$cats = get_categories_with_counts();
admin_header('Categories');
?>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="card"><div class="card-body">
      <h2 class="h6 fw-bold">Add Category</h2>
      <form method="post">
        <?php echo csrf_field(); ?>
        <label class="form-label small">Name</label><input class="form-control mb-2" name="name" required>
        <label class="form-label small">Description</label><textarea class="form-control mb-3" name="description" rows="2"></textarea>
        <button class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i> Add Category</button>
      </form>
    </div></div>
  </div>
  <div class="col-lg-7">
    <div class="card">
      <div class="table-responsive"><table class="table align-middle mb-0">
        <thead><tr><th>Name</th><th>Posts</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($cats as $c): ?>
          <tr><td><?php echo e($c['name']); ?></td><td><?php echo (int)$c['post_count']; ?></td>
          <td><a href="categories.php?delete=<?php echo (int)$c['id']; ?>&csrf=<?php echo e(csrf_token()); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this category?')"><i class="bi bi-trash"></i></a></td></tr>
        <?php endforeach; ?>
        <?php if (!$cats): ?><tr><td colspan="3" class="text-center text-muted py-3">No categories yet.</td></tr><?php endif; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div>
<?php admin_footer(); ?>
