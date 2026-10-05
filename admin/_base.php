<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');
/* Included at the top of EVERY admin/*.php file */

define('ADMIN_AREA', true);

require __DIR__ . '/../includes/init.php';

start_session();

$isLogin = basename($_SERVER['SCRIPT_NAME']) === 'login.php';

/*
 * Protect every admin page.
 *
 * A session ID is valid only when the corresponding
 * user still exists in the database.
 */
if (!$isLogin) {

    $adminId = isset($_SESSION['admin_id'])
        ? (int)$_SESSION['admin_id']
        : 0;

    $adminUser = null;

    if ($adminId > 0) {
        $adminUser = db_row(
            'SELECT id, username, email FROM users WHERE id = ?',
            array($adminId)
        );
    }

    /*
     * No valid session OR user was deleted.
     */
    if (!$adminUser) {

        unset($_SESSION['admin_id']);

        /*
         * Destroy the current authenticated session.
         */
        $_SESSION = array();

        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        redirect('login.php');
        exit;
    }
}

function admin_user()
{
    start_session();

    $adminId = isset($_SESSION['admin_id'])
        ? (int)$_SESSION['admin_id']
        : 0;

    if ($adminId <= 0) {
        return null;
    }

    return db_row(
        'SELECT id, username, email FROM users WHERE id = ?',
        array($adminId)
    );
}

function admin_url($path = '')
{
    return url('admin/' . ltrim($path, '/'));
}

$_admin_nav = array(
    'index.php' => array('Dashboard', 'bi-speedometer2'),
    'posts.php' => array('Blog Posts', 'bi-file-earmark-text'),
    'categories.php' => array('Categories', 'bi-folder2'),
    'pages.php' => array('Pages', 'bi-file-earmark-richtext'),
    'tools.php' => array('Manage Tools', 'bi-tools'),
    'messages.php' => array('Messages', 'bi-envelope'),
    'settings.php' => array('Settings', 'bi-gear'),
);

function admin_header($title)
{
    global $_admin_nav;
    ?><!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">

<title><?php echo e($title); ?> - Admin - <?php echo e(site_name()); ?></title>

<link
    rel="icon"
    type="image/svg+xml"
    href="<?php echo e(asset('img/favicon.svg')); ?>"
>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet"
>

<link
    href="<?php echo e(asset('css/style.css')); ?>"
    rel="stylesheet"
>

</head>

<body class="admin-body">

<div class="d-flex">

  <aside class="admin-side d-none d-lg-block">

    <div class="brand">
      <i class="bi bi-lightning-charge-fill"></i>
      <?php echo e(site_name()); ?>
    </div>

    <?php foreach ($_admin_nav as $file => $meta): ?>

      <a
          href="<?php echo e(admin_url($file)); ?>"
          class="<?php echo basename($_SERVER['SCRIPT_NAME']) === $file ? 'active' : ''; ?>"
      >
        <i class="bi <?php echo e($meta[1]); ?>"></i>
        <?php echo e($meta[0]); ?>
      </a>

    <?php endforeach; ?>

    <a href="<?php echo e(admin_url('logout.php')); ?>">
      <i class="bi bi-box-arrow-right"></i>
      Logout
    </a>

    <a
        href="<?php echo e(url('/')); ?>"
        target="_blank"
    >
      <i class="bi bi-box-arrow-up-right"></i>
      View Website
    </a>

  </aside>

  <div class="admin-main">

    <nav class="navbar bg-body border-bottom d-lg-none">

      <div class="container-fluid">

        <button
            class="navbar-toggler"
            data-bs-toggle="collapse"
            data-bs-target="#adminMobileNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <span class="fw-bold">
          <?php echo e(site_name()); ?> Admin
        </span>

      </div>

      <div class="collapse w-100" id="adminMobileNav">

        <div class="list-group list-group-flush">

          <?php foreach ($_admin_nav as $file => $meta): ?>

            <a
                class="list-group-item"
                href="<?php echo e(admin_url($file)); ?>"
            >
              <i class="bi <?php echo e($meta[1]); ?>"></i>
              <?php echo e($meta[0]); ?>
            </a>

          <?php endforeach; ?>

          <a
              class="list-group-item"
              href="<?php echo e(admin_url('logout.php')); ?>"
          >
            <i class="bi bi-box-arrow-right"></i>
            Logout
          </a>

        </div>

      </div>

    </nav>

    <div class="container-fluid p-4">

      <h1 class="h4 fw-bold mb-4">
        <?php echo e($title); ?>
      </h1>

      <?php echo flash_render(); ?>
<?php
}

function admin_footer()
{
    ?>
    </div>
  </div>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>
</html>
<?php
}

