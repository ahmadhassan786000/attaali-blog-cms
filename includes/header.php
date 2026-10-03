<?php
/* Public site header. Set page info with seo('title', ...) etc. before including. */
$_title = seo('title');
$_full_title = $_title ? $_title . ' | ' . site_name() : site_name() . ' - ' . setting('tagline', 'Free online tools');
$_desc = seo('description') ? seo('description') : setting('meta_description', '');
$_canon = seo('canonical') ? seo('canonical') : abs_url(ltrim(current_path(), '/') === '' ? '' : substr(current_path(), strlen(base_path()) + 1));
$_img = seo('image') ? seo('image') : abs_url('assets/img/post-placeholder.svg');
$_type = seo('type') ? seo('type') : 'website';
$_adsense = setting('adsense_client', '');
$_menu_tools = tools_by_category();
?><!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo e($_full_title); ?></title>
<meta name="description" content="<?php echo e($_desc); ?>">
<?php if (seo('noindex')): ?><meta name="robots" content="noindex,follow">
<?php else: ?><meta name="robots" content="index,follow,max-image-preview:large">
<?php endif; ?>
<link rel="canonical" href="<?php echo e($_canon); ?>">
<meta property="og:site_name" content="<?php echo e(site_name()); ?>">
<meta property="og:type" content="<?php echo e($_type); ?>">
<meta property="og:title" content="<?php echo e($_title ? $_title : site_name()); ?>">
<meta property="og:description" content="<?php echo e($_desc); ?>">
<meta property="og:url" content="<?php echo e($_canon); ?>">
<meta property="og:image" content="<?php echo e($_img); ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#4f46e5">
<link rel="icon" type="image/svg+xml" href="<?php echo e(asset('img/favicon.svg')); ?>">
<script>try{var t=localStorage.getItem('theme');if(!t){t=window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-bs-theme',t);}catch(e){}</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
<?php if ($_adsense !== ''): ?><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo e($_adsense); ?>" crossorigin="anonymous"></script>
<?php endif; ?>
<?php foreach ((array)seo('schema') as $_s): ?><script type="application/ld+json"><?php echo $_s; ?></script>
<?php endforeach; ?>
<?php echo setting('head_code', ''); ?>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top site-nav">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?php echo e(url('/')); ?>">
      <span class="brand-mark"><i class="bi bi-lightning-charge-fill"></i></span><?php echo e(site_name()); ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
        <li class="nav-item"><a class="nav-link" href="<?php echo e(url('/')); ?>">Home</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="<?php echo e(url('tools')); ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">Tools</a>
          <div class="dropdown-menu mega-menu p-3">
            <div class="row g-3">
              <?php foreach ($_menu_tools as $cat => $list): if (!$list) { continue; } $cm = tool_categories(); ?>
              <div class="col-lg-3 col-md-6">
                <div class="mega-title text-<?php echo e($cm[$cat]['color']); ?>"><i class="bi <?php echo e($cm[$cat]['icon']); ?>"></i> <?php echo e($cat); ?></div>
                <?php foreach ($list as $_mSlug => $_mTool): ?>
                  <a class="dropdown-item rounded" href="<?php echo e(tool_url($_mSlug)); ?>"><?php echo e($_mTool['name']); ?></a>
                <?php endforeach; ?>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </li>
        <li class="nav-item"><a class="nav-link" href="<?php echo e(url('blog')); ?>">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo e(url('about-us')); ?>">About</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo e(url('contact')); ?>">Contact</a></li>
      </ul>
      <form class="d-flex me-lg-2 mb-2 mb-lg-0" action="<?php echo e(url('search')); ?>" method="get" role="search">
        <div class="input-group">
          <input class="form-control" type="search" name="q" placeholder="Search tools &amp; articles" aria-label="Search" value="<?php echo e(isset($_GET['q']) && basename($_SERVER['SCRIPT_NAME']) === 'search.php' ? $_GET['q'] : ''); ?>">
          <button class="btn btn-primary" type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
        </div>
      </form>
      <button class="btn btn-outline-secondary theme-toggle" id="themeToggle" type="button" aria-label="Toggle dark mode"><i class="bi bi-moon-stars"></i></button>
    </div>
  </div>
</nav>
<?php ad('header'); ?>
