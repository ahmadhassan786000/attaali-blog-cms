<?php
require __DIR__ . '/_base.php';

if (isset($_GET['delete']) && csrf_ok()) {
    db_exec('DELETE FROM messages WHERE id = ?', array((int)$_GET['delete']));
    redirect(admin_url('messages.php'));
}
if (isset($_GET['read']) && csrf_ok()) {
    db_exec('UPDATE messages SET is_read = 1 WHERE id = ?', array((int)$_GET['read']));
    redirect(admin_url('messages.php'));
}
$msgs = db_all('SELECT * FROM messages ORDER BY created_at DESC');
admin_header('Messages');
?>
<div class="accordion" id="msgAcc">
<?php foreach ($msgs as $i => $m): ?>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#m<?php echo (int)$m['id']; ?>">
        <?php if (!$m['is_read']): ?><span class="badge text-bg-primary me-2">New</span><?php endif; ?>
        <strong class="me-2"><?php echo e($m['name']); ?></strong> <span class="text-muted small"><?php echo e($m['subject'] ?: '(no subject)'); ?> &middot; <?php echo e(fdate($m['created_at'])); ?></span>
      </button>
    </h2>
    <div id="m<?php echo (int)$m['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#msgAcc">
      <div class="accordion-body">
        <p class="mb-1"><strong>From:</strong> <?php echo e($m['name']); ?> &lt;<a href="mailto:<?php echo e($m['email']); ?>"><?php echo e($m['email']); ?></a>&gt;</p>
        <p style="white-space:pre-wrap"><?php echo e($m['message']); ?></p>
        <a href="mailto:<?php echo e($m['email']); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-reply"></i> Reply by Email</a>
        <?php if (!$m['is_read']): ?><a href="messages.php?read=<?php echo (int)$m['id']; ?>&csrf=<?php echo e(csrf_token()); ?>" class="btn btn-sm btn-outline-secondary">Mark as read</a><?php endif; ?>
        <a href="messages.php?delete=<?php echo (int)$m['id']; ?>&csrf=<?php echo e(csrf_token()); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this message?')"><i class="bi bi-trash"></i></a>
      </div>
    </div>
  </div>
<?php endforeach; ?>
<?php if (!$msgs): ?><div class="text-center text-muted py-5">No messages yet.</div><?php endif; ?>
</div>
<?php admin_footer(); ?>
