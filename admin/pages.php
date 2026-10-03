<?php
require __DIR__ . '/_base.php';

if (isset($_GET['delete']) && csrf_ok()) {
    db_exec('DELETE FROM pages WHERE id = ?', array((int)$_GET['delete']));
    flash('success', 'Page deleted.');
    redirect(admin_url('pages.php'));
}
$pages = db_all('SELECT * FROM pages ORDER BY title');
admin_header('Pages');
?>
<div class="d-flex justify-content-between mb-3">
  <p class="text-muted mb-0">Static pages like About, Privacy Policy and Terms. These are required for AdSense approval.</p>
  <a href="page-edit.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Page</a>
</div>
<div class="card">
  <div class="table-responsive"><table class="table align-middle mb-0">
    <thead><tr><th>Title</th><th>URL</th><th>Footer</th><th>Updated</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($pages as $p): ?>
      <tr>
        <td><a href="page-edit.php?id=<?php echo (int)$p['id']; ?>"><?php echo e($p['title']); ?></a></td>
        <td class="small text-muted">/<?php echo e($p['slug']); ?></td>
        <td><?php echo $p['show_in_footer'] ? '<i class="bi bi-check-circle text-success"></i>' : '-'; ?></td>
        <td class="small text-muted"><?php echo e(fdate($p['updated_at'])); ?></td>
        <td class="text-nowrap">
          <a href="page-edit.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <a href="pages.php?delete=<?php echo (int)$p['id']; ?>&csrf=<?php echo e(csrf_token()); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this page?')"><i class="bi bi-trash"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
<?php admin_footer(); ?>
