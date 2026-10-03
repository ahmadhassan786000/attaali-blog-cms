<?php
require __DIR__ . '/_base.php';

if (isset($_GET['delete']) && csrf_ok()) {
    $post = db_row('SELECT image FROM posts WHERE id = ?', array((int)$_GET['delete']));
    if ($post) {
        db_exec('DELETE FROM posts WHERE id = ?', array((int)$_GET['delete']));
        delete_upload($post['image']);
        flash('success', 'Post deleted.');
    }
    redirect(admin_url('posts.php'));
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = "1=1"; $params = array();
if ($q !== '') { $where .= ' AND title LIKE ?'; $params[] = '%' . $q . '%'; }
$page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$per = 15;
$total = (int)db_val("SELECT COUNT(*) FROM posts WHERE $where", $params, 0);
$posts = db_all("SELECT p.*, c.name AS cat_name FROM posts p LEFT JOIN categories c ON c.id = p.category_id
                 WHERE $where ORDER BY p.created_at DESC LIMIT $per OFFSET " . (($page - 1) * $per), $params);

admin_header('Blog Posts');
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <form class="d-flex" method="get"><input class="form-control me-2" name="q" placeholder="Search posts..." value="<?php echo e($q); ?>"><button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button></form>
  <a href="post-edit.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Post</a>
</div>
<div class="card">
  <div class="table-responsive"><table class="table align-middle mb-0">
    <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Views</th><th>Date</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($posts as $p): ?>
      <tr>
        <td class="text-truncate" style="max-width:260px"><a href="post-edit.php?id=<?php echo (int)$p['id']; ?>"><?php echo e($p['title']); ?></a></td>
        <td><?php echo e($p['cat_name'] ?: '-'); ?></td>
        <td><span class="badge text-bg-<?php echo $p['status']==='published'?'success':'secondary'; ?>"><?php echo e(ucfirst($p['status'])); ?></span></td>
        <td><?php echo (int)$p['views']; ?></td>
        <td class="small text-muted"><?php echo e(fdate($p['created_at'])); ?></td>
        <td class="text-nowrap">
          <a href="post-edit.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <a href="posts.php?delete=<?php echo (int)$p['id']; ?>&csrf=<?php echo e(csrf_token()); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this post permanently?')"><i class="bi bi-trash"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$posts): ?><tr><td colspan="6" class="text-center text-muted py-4">No posts found.</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</div>
<div class="mt-3"><?php echo paginate($total, $per, $page, admin_url('posts.php') . ($q !== '' ? '?q=' . urlencode($q) : '')); ?></div>
<?php admin_footer(); ?>
