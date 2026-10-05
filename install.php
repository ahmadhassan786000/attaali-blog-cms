<?php
/* One-click installer. DELETE this file after installation. */
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
define('SITE_ROOT', __DIR__);
$cfgFile = __DIR__ . '/includes/config.php';
$done = false;
$errors = array();
$in = array(
    'db_host' => 'localhost', 'db_name' => '', 'db_user' => '', 'db_pass' => '',
    'site_name' => 'Attaali', 'admin_user' => 'admin', 'admin_email' => '',
);

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

if (is_file($cfgFile) && !isset($_GET['force'])) {
    $already = true;
} else {
    $already = false;
}

if (!$already && $_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($in as $k => $v) {
        $in[$k] = isset($_POST[$k]) ? trim($_POST[$k]) : '';
    }
    $adminPass = isset($_POST['admin_pass']) ? $_POST['admin_pass'] : '';
    $in['db_pass'] = isset($_POST['db_pass']) ? $_POST['db_pass'] : '';

    if ($in['db_name'] === '' || $in['db_user'] === '') { $errors[] = 'Database name and user are required.'; }
    if ($in['site_name'] === '') { $errors[] = 'Site name is required.'; }
    if ($in['admin_user'] === '' || !preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $in['admin_user'])) { $errors[] = 'Admin username must be 3-40 letters/numbers.'; }
    if (strlen($adminPass) < 8) { $errors[] = 'Admin password must be at least 8 characters.'; }
    if ($in['admin_email'] === '' || !filter_var($in['admin_email'], FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid admin e-mail.'; }

    if (!$errors) {
        mysqli_report(MYSQLI_REPORT_OFF);
        $link = @mysqli_connect($in['db_host'], $in['db_user'], $in['db_pass']);
        if (!$link) {
            $errors[] = 'Cannot connect to MySQL: ' . mysqli_connect_error();
        } else {
            @mysqli_query($link, 'CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $in['db_name']) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            if (!@mysqli_select_db($link, $in['db_name'])) {
                $errors[] = 'Database "' . h($in['db_name']) . '" not found and could not be created. Create it in cPanel first.';
            }
            mysqli_close($link);
        }
    }

    if (!$errors) {
        define('DB_HOST', $in['db_host']);
        define('DB_USER', $in['db_user']);
        define('DB_PASS', $in['db_pass']);
        define('DB_NAME', $in['db_name']);
        define('DEBUG', false);
        require __DIR__ . '/includes/db.php';
        require __DIR__ . '/includes/schema.php';

        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

        foreach (schema_statements() as $sql) {
            if (db_query($sql) === false) {
                $errors[] = 'Could not create tables: ' . mysqli_error(db());
                break;
            }
        }
    }

    if (!$errors) {
        $now = date('Y-m-d H:i:s');
        // admin user
        $hash = password_hash($adminPass, PASSWORD_DEFAULT);
        $exists = db_val('SELECT id FROM users WHERE username = ?', array($in['admin_user']));
        if ($exists) {
            db_exec('UPDATE users SET password = ?, email = ? WHERE id = ?', array($hash, $in['admin_email'], (int)$exists));
        } else {
            db_exec('INSERT INTO users (username, password, email, created_at) VALUES (?, ?, ?, ?)', array($in['admin_user'], $hash, $in['admin_email'], $now));
        }
        // settings
        foreach (default_settings($in['site_name'], $in['admin_email']) as $k => $v) {
            db_exec('INSERT IGNORE INTO settings (k, v) VALUES (?, ?)', array($k, $v));
        }
        db_exec('UPDATE settings SET v = ? WHERE k = ?', array($in['site_name'], 'site_name'));
        // categories
        $cats = array('Tutorials' => 'Step by step guides', 'Tips & Tricks' => 'Quick tips to work smarter', 'Technology' => 'Tech news and explainers', 'Guides' => 'In-depth guides');
        foreach ($cats as $name => $desc) {
            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
            db_exec('INSERT IGNORE INTO categories (name, slug, description) VALUES (?, ?, ?)', array($name, $slug, $desc));
        }
        // pages
        foreach (seed_pages() as $pg) {
            db_exec('INSERT IGNORE INTO pages (title, slug, content, meta_desc, show_in_footer, updated_at) VALUES (?, ?, ?, ?, 1, ?)', array($pg[0], $pg[1], $pg[2], $pg[3], $now));
        }
        // sample posts
        foreach (seed_posts() as $i => $po) {
            $cid = db_val('SELECT id FROM categories WHERE name = ?', array($po[3]), null);
            $content = str_replace('href="/', 'href="' . $base . '/', $po[4]);
            db_exec("INSERT IGNORE INTO posts (title, slug, excerpt, content, category_id, meta_desc, status, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, 'published', ?, ?)",
                array($po[0], $po[1], $po[2], $content, $cid, $po[2], date('Y-m-d H:i:s', time() - $i * 86400), $now));
        }

        $cfg = "<?php\n"
            . "define('DB_HOST', " . var_export($in['db_host'], true) . ");\n"
            . "define('DB_NAME', " . var_export($in['db_name'], true) . ");\n"
            . "define('DB_USER', " . var_export($in['db_user'], true) . ");\n"
            . "define('DB_PASS', " . var_export($in['db_pass'], true) . ");\n"
            . "define('BASE_PATH', " . var_export($base, true) . ");\n"
            . "define('SITE_TIMEZONE', 'Asia/Karachi');\n"
            . "define('DEBUG', false);\n";
        if (@file_put_contents($cfgFile, $cfg) === false) {
            $errors[] = 'Could not write includes/config.php. Make the includes folder writable and try again.';
        } else {
            $done = true;
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Install - Website Setup</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:linear-gradient(135deg,#4f46e5,#06b6d4);min-height:100vh}.card{border:0;border-radius:18px}</style>
</head>
<body>
<div class="container py-5" style="max-width:640px">
  <div class="card shadow-lg"><div class="card-body p-4 p-md-5">
    <h1 class="h3 fw-bold mb-1">Website Installer</h1>
    <p class="text-muted">Fill in the details once. Tables, admin account and starter content are created automatically.</p>
    <?php if ($already): ?>
      <div class="alert alert-warning">The site is already installed. For security please <strong>delete install.php</strong> from your server.</div>
      <a class="btn btn-primary" href="admin/">Go to Admin Panel</a>
    <?php elseif ($done): ?>
      <div class="alert alert-success"><strong>Installation complete!</strong></div>
      <ol>
        <li><strong>Delete install.php</strong> from your server now.</li>
        <li>Login to the admin panel with the username and password you just chose.</li>
        <li>Open Settings and add your AdSense details when approved.</li>
      </ol>
      <a class="btn btn-primary" href="admin/">Open Admin Panel</a>
      <a class="btn btn-outline-secondary" href="./">View Website</a>
    <?php else: ?>
      <?php foreach ($errors as $er): ?><div class="alert alert-danger py-2"><?php echo $er; ?></div><?php endforeach; ?>
      <form method="post" autocomplete="off">
        <h2 class="h6 text-uppercase text-muted mt-3">Database</h2>
        <div class="row g-2">
          <div class="col-6"><label class="form-label">Host</label><input class="form-control" name="db_host" value="<?php echo h($in['db_host']); ?>" required></div>
          <div class="col-6"><label class="form-label">Database name</label><input class="form-control" name="db_name" value="<?php echo h($in['db_name']); ?>" required></div>
          <div class="col-6"><label class="form-label">DB username</label><input class="form-control" name="db_user" value="<?php echo h($in['db_user']); ?>" required></div>
          <div class="col-6"><label class="form-label">DB password</label><input class="form-control" type="password" name="db_pass"></div>
        </div>
        <h2 class="h6 text-uppercase text-muted mt-4">Website &amp; Admin</h2>
        <div class="row g-2">
          <div class="col-12"><label class="form-label">Site name</label><input class="form-control" name="site_name" value="<?php echo h($in['site_name']); ?>" required></div>
          <div class="col-6"><label class="form-label">Admin username</label><input class="form-control" name="admin_user" value="<?php echo h($in['admin_user']); ?>" required></div>
          <div class="col-6"><label class="form-label">Admin password (min 8)</label><input class="form-control" type="password" name="admin_pass" required></div>
          <div class="col-12"><label class="form-label">Admin / contact e-mail</label><input class="form-control" type="email" name="admin_email" value="<?php echo h($in['admin_email']); ?>" required></div>
        </div>
        <button class="btn btn-primary btn-lg w-100 mt-4">Install Website</button>
      </form>
    <?php endif; ?>
  </div></div>
</div>
</body>
</html>
