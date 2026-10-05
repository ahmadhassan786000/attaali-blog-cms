<?php
require __DIR__ . '/../includes/init.php';
start_session();
if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php'); exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        $error = 'Security check failed, please try again.';
    } else {
        $u = trim(isset($_POST['username']) ? $_POST['username'] : '');
        $p = isset($_POST['password']) ? $_POST['password'] : '';
        $row = db_row('SELECT * FROM users WHERE username = ?', array($u));
        if ($row && password_verify($p, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $row['id'];
            header('Location: index.php'); exit;
        }
        $error = 'Invalid username or password.';
        usleep(400000);
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Admin Login - <?php echo e(site_name()); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>body{background:linear-gradient(135deg,#4338ca,#0891b2);min-height:100vh}.card{border:0;border-radius:18px}</style>
</head>
<body class="d-flex align-items-center">
<div class="container" style="max-width:420px">
  <div class="card shadow-lg"><div class="card-body p-4 p-md-5">
    <div class="text-center mb-4"><i class="bi bi-lightning-charge-fill" style="font-size:2rem;color:#4f46e5"></i>
      <h1 class="h4 fw-bold mt-2"><?php echo e(site_name()); ?> Admin</h1></div>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?php echo e($error); ?></div><?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required autofocus></div>
      <div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
      <button class="btn btn-primary w-100 btn-lg">Login</button>
    </form>
  </div></div>
</div>
</body>
</html>
