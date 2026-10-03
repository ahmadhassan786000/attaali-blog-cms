<?php
require __DIR__ . '/_base.php';

if (isset($_GET['toggle']) && csrf_ok()) {
    $slug = preg_replace('/[^a-z0-9-]/', '', $_GET['toggle']);
    db_exec('INSERT INTO tools_status (slug, enabled, views) VALUES (?, 0, 0) ON DUPLICATE KEY UPDATE enabled = 1 - enabled', array($slug));
    redirect(admin_url('tools.php'));
}
if (isset($_GET['delete']) && csrf_ok()) {
    $slug = preg_replace('/[^a-z0-9-]/', '', $_GET['delete']);
    $row = db_row('SELECT slug FROM custom_tools WHERE slug = ?', array($slug));
    if ($row) {
        @unlink(SITE_ROOT . '/tool-ui/' . $slug . '.php');
        db_exec('DELETE FROM custom_tools WHERE slug = ?', array($slug));
        db_exec('DELETE FROM tools_status WHERE slug = ?', array($slug));
        flash('success', 'Tool deleted.');
    }
    redirect(admin_url('tools.php'));
}
$tools = all_tools(false);
admin_header('Manage Tools');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <p class="text-muted mb-0">Turn tools on or off. Disabled tools disappear from the menu and homepage but their files stay on the server.</p>
  <a href="tool-add.php" class="btn btn-primary text-nowrap ms-3"><i class="bi bi-plus-lg"></i> Add New Tool</a>
</div>
<div class="card">
  <div class="table-responsive"><table class="table align-middle mb-0">
    <thead><tr><th>Tool</th><th>Category</th><th>Views</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($tools as $slug => $t): ?>
      <tr>
        <td><i class="bi <?php echo e($t['icon']); ?>"></i> <?php echo e($t['name']); ?> <?php if (!empty($t['custom'])): ?><span class="badge text-bg-info">Custom</span><?php endif; ?></td>
        <td class="small text-muted"><?php echo e($t['cat']); ?></td>
        <td><?php echo (int)$t['views']; ?></td>
        <td><span class="badge text-bg-<?php echo $t['enabled'] ? 'success' : 'secondary'; ?>"><?php echo $t['enabled'] ? 'Enabled' : 'Disabled'; ?></span></td>
        <td class="text-nowrap">
          <?php if (!empty($t['custom'])): ?>
            <a href="tool-add.php?slug=<?php echo e($slug); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <?php endif; ?>
          <a href="tools.php?toggle=<?php echo e($slug); ?>&csrf=<?php echo e(csrf_token()); ?>" class="btn btn-sm btn-outline-<?php echo $t['enabled'] ? 'danger' : 'success'; ?>"><?php echo $t['enabled'] ? 'Disable' : 'Enable'; ?></a>
          <?php if (!empty($t['custom'])): ?>
            <a href="tools.php?delete=<?php echo e($slug); ?>&csrf=<?php echo e(csrf_token()); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this tool permanently? This removes its file too.')"><i class="bi bi-trash"></i></a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
<div class="alert alert-info mt-4"><i class="bi bi-info-circle"></i> Built-in tools are edited by changing their file in <code>tool-ui/</code> directly. Tools marked <span class="badge text-bg-info">Custom</span> were added from the "Add New Tool" page and can be edited or deleted right here.</div>
<?php admin_footer(); ?>
