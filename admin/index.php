<?php
require __DIR__ . '/_base.php';

$stats = array(
    'posts' => db_val("SELECT COUNT(*) FROM posts WHERE status='published'", array(), 0),
    'drafts' => db_val("SELECT COUNT(*) FROM posts WHERE status='draft'", array(), 0),
    'views' => db_val('SELECT COALESCE(SUM(views),0) FROM posts', array(), 0),
    'messages' => db_val('SELECT COUNT(*) FROM messages WHERE is_read = 0', array(), 0),
);
$recentPosts = db_all('SELECT id, title, slug, status, views, created_at FROM posts ORDER BY created_at DESC LIMIT 6');
$recentMsgs = db_all('SELECT id, name, subject, created_at FROM messages ORDER BY created_at DESC LIMIT 5');
$topTools = db_all('SELECT slug, views FROM tools_status ORDER BY views DESC LIMIT 6');

admin_header('Dashboard');
?>
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3"><div class="stat-card g1"><div class="num"><?php echo (int)$stats['posts']; ?></div><div>Published Posts</div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card g2"><div class="num"><?php echo (int)$stats['drafts']; ?></div><div>Drafts</div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card g3"><div class="num"><?php echo number_short($stats['views']); ?></div><div>Total Post Views</div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card g4"><div class="num"><?php echo (int)$stats['messages']; ?></div><div>Unread Messages</div></div></div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header bg-transparent d-flex justify-content-between"><strong>Recent Posts</strong><a href="posts.php" class="small">View all</a></div>
      <div class="table-responsive"><table class="table mb-0 align-middle">
        <thead><tr><th>Title</th><th>Status</th><th>Views</th><th>Date</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($recentPosts as $p): ?>
          <tr>
            <td class="text-truncate" style="max-width:220px"><?php echo e($p['title']); ?></td>
            <td><span class="badge text-bg-<?php echo $p['status']==='published'?'success':'secondary'; ?>"><?php echo e(ucfirst($p['status'])); ?></span></td>
            <td><?php echo (int)$p['views']; ?></td>
            <td class="small text-muted"><?php echo e(fdate($p['created_at'])); ?></td>
            <td><a href="post-edit.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$recentPosts): ?><tr><td colspan="5" class="text-muted text-center py-3">No posts yet</td></tr><?php endif; ?>
        </tbody>
      </table></div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card mb-3">
      <div class="card-header bg-transparent d-flex justify-content-between"><strong>Recent Messages</strong><a href="messages.php" class="small">View all</a></div>
      <ul class="list-group list-group-flush">
        <?php foreach ($recentMsgs as $m): ?>
          <li class="list-group-item"><strong><?php echo e($m['name']); ?></strong> - <?php echo e($m['subject'] ?: '(no subject)'); ?><div class="small text-muted"><?php echo e(fdate($m['created_at'])); ?></div></li>
        <?php endforeach; ?>
        <?php if (!$recentMsgs): ?><li class="list-group-item text-muted text-center">No messages yet</li><?php endif; ?>
      </ul>
    </div>
    <div class="card">
      <div class="card-header bg-transparent"><strong>Most Viewed Tools</strong></div>
      <ul class="list-group list-group-flush">
        <?php foreach ($topTools as $t): $tool = get_tool($t['slug']); if (!$tool) { continue; } ?>
          <li class="list-group-item d-flex justify-content-between"><span><i class="bi <?php echo e($tool['icon']); ?>"></i> <?php echo e($tool['name']); ?></span><span class="badge text-bg-primary rounded-pill"><?php echo (int)$t['views']; ?></span></li>
        <?php endforeach; ?>
        <?php if (!$topTools): ?><li class="list-group-item text-muted text-center">No tool views yet</li><?php endif; ?>
      </ul>
    </div>
  </div>
</div>
<?php admin_footer(); ?>
