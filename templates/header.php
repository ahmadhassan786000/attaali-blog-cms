<?php require_once __DIR__ . '/../includes/functions.php';
$nav = [['AI Tools','ai-tools'],['Technology','technology'],['Web Development','web-development'],['Software','software-productivity'],['Productivity','productivity'],['Automation','automation'],['Blogging & SEO','blogging-seo']]; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title ?? SITE_NAME) ?></title>
<meta name="description" content="<?= e($meta_desc ?? '') ?>">
<link rel="canonical" href="<?= e(SITE_URL . ($canonical_path ?? '/')) ?>">
<meta property="og:title" content="<?= e($page_title ?? SITE_NAME) ?>">
<meta property="og:description" content="<?= e($meta_desc ?? '') ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= e(SITE_URL . ($canonical_path ?? '/')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Newsreader:wght@500;600;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
  <nav class="navbar navbar-expand-lg" aria-label="Main navigation">
    <div class="container">
      <a class="brand" href="<?= BASE_URL ?>"><span class="brand-box">A</span><span>ATTAALI</span></a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle menu"><i class="bi bi-list fs-2"></i></button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mx-auto">
          <?php foreach ($nav as [$label, $slug]): ?>
          <li class="nav-item"><a class="nav-link<?= ($active ?? '') === $slug ? ' active' : '' ?>" href="<?= e(cat_url($slug)) ?>"><?= e($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
        <div class="nav-actions">
          <a href="<?= BASE_URL ?>search/" aria-label="Search"><i class="bi bi-search"></i></a>
          <a class="user-btn" href="<?= BASE_URL ?>admin/login.php" aria-label="Admin login"><i class="bi bi-person-fill"></i></a>
        </div>
      </div>
    </div>
  </nav>
</header>
