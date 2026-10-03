<?php

[$post['content'], $toc] = add_toc($post['content']);

$popular = array_slice(dummy_popular(), 0, 4);
$related = dummy_related_sidebar();
$more = array_slice(dummy_ai(), 0, 4);
$comments = dummy_comments();

$count = 0;
foreach ($comments as $c) {
    $count += 1 + count($c['replies']);
}

$path = '/' . $post['category_slug'] . '/' . $post['slug'] . '/';
$url = SITE_URL . $path;

$page_title = $post['title'] . ' | ATTAALI';
$meta_desc = $post['excerpt'];
$canonical_path = $path;
$active = $post['category_slug'];

$extra_css = 'css/post.css';
$extra_js = 'js/post.js';

$head_extra =
    '<meta property="og:image" content="' . e(SITE_URL . $post['image']) . '">' .
    '<meta name="twitter:card" content="summary_large_image">' .
    '<script type="application/ld+json">' .
    json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post['title'],
        'description' => $post['excerpt'],
        'image' => SITE_URL . $post['image'],
        'datePublished' => $post['published_at'],
        'dateModified' => $post['updated_at'],
        'author' => [
            '@type' => 'Person',
            'name' => $post['author']['name']
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => SITE_NAME
        ],
        'mainEntityOfPage' => $url,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) .
    '</script>';

$content_parts = explode('<!--AD_IN_CONTENT-->', $post['content'], 2);

require __DIR__ . '/header.php';
?>

<div class="read-progress" id="readProgress" aria-hidden="true"></div>
<main id="main" class="container post-page">

  <nav class="crumbs" aria-label="Breadcrumb"><ol>
    <li><a href="<?= BASE_URL ?>">Home</a></li>
    <li><a href="<?= BASE_URL ?>blog/">ATTAALI Dispatch</a></li>
    <li><a href="<?= e(cat_url($post['category_slug'])) ?>"><?= e($post['category']) ?></a></li>
    <li aria-current="page"><?= e($post['title']) ?></li>
  </ol></nav>

  <header class="post-head">
    <a class="cat-tag" href="<?= e(cat_url($post['category_slug'])) ?>">
      <i class="bi bi-cpu"></i> <?= e($post['category']) ?>
    </a>

    <h1><?= e($post['title']) ?></h1>

    <p class="post-lede"><?= e($post['excerpt']) ?></p>

    <div class="byline">
      <div class="by-left">
        <span class="by-avatar"><?= e($post['author']['initials']) ?></span>

        <div>
          <a class="by-name" href="<?= BASE_URL ?>author/<?= e($post['author']['slug']) ?>/">
            <?= e($post['author']['name']) ?>
          </a>

          <span class="by-role">/ <?= e($post['author']['role']) ?></span>

          <p class="mono-meta mb-0">
            Published
            <time datetime="<?= e($post['published_at']) ?>">
              <?= d($post['published_at']) ?>
            </time>
            &bull;
            Updated
            <time datetime="<?= e($post['updated_at']) ?>">
              <?= d($post['updated_at']) ?>
            </time>
            &bull;
            <i class="bi bi-clock"></i>
            <?= (int)$post['read'] ?> min read
          </p>
        </div>
      </div>

      <div class="by-actions">
        <button class="btn-line" type="button" id="bookmarkBtn" aria-pressed="false">
          <i class="bi bi-bookmark"></i> Bookmark
        </button>

        <button class="btn-navy" type="button" id="shareBtn">
          <i class="bi bi-share"></i> Share
        </button>
      </div>
    </div>
  </header>

  <figure class="post-figure">
    <img
      src="<?= e($post['image']) ?>"
      alt="<?= e($post['image_alt']) ?>"
      width="1020"
      height="524"
    >

    <figcaption>
      <span><?= e($post['caption']) ?></span>
      <span>ATTAALI Editorial Image</span>
    </figcaption>
  </figure>

  <div class="row g-4 g-xl-5">

    <article class="col-lg-8" aria-label="Article">

      <section class="toc" aria-label="Table of contents">
        <button
          class="toc-head"
          type="button"
          id="tocToggle"
          aria-expanded="true"
          aria-controls="tocList"
        >
          <span>
            <i class="bi bi-list-ul"></i>
            Table of Contents
          </span>
          <i class="bi bi-chevron-up"></i>
        </button>

        <ol id="tocList">
          <?php foreach ($toc as $t): ?>
            <li>
              <a href="#<?= e($t['id']) ?>">
                <?= e($t['text']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ol>
      </section>

      <div class="article-body" id="articleBody">

        <?= $content_parts[0] ?>

        <?php if (isset($content_parts[1])): ?>

          <aside class="ad-box" aria-label="Advertisement">
            <span class="mono-label">
              Advertisement &mdash; Sponsored Network
            </span>

            <div>
              <strong>Ad slot (in-content)</strong>
              <p>AdSense / banner code from admin settings renders here.</p>
            </div>
          </aside>

          <?= $content_parts[1] ?>

        <?php endif; ?>

      </div>

      <div class="share-row">

        <span class="mono-label">Share Article</span>

        <div class="d-flex gap-2 align-items-center flex-wrap">

          <a
            class="sh"
            target="_blank"
            rel="noopener"
            aria-label="Share on X"
            href="https://twitter.com/intent/tweet?url=<?= rawurlencode($url) ?>&text=<?= rawurlencode($post['title']) ?>"
          >
            <i class="bi bi-twitter-x"></i>
          </a>

          <a
            class="sh"
            target="_blank"
            rel="noopener"
            aria-label="Share on LinkedIn"
            href="https://www.linkedin.com/sharing/share-offsite/?url=<?= rawurlencode($url) ?>"
          >
            <i class="bi bi-linkedin"></i>
          </a>

          <a
            class="sh"
            target="_blank"
            rel="noopener"
            aria-label="Share on Facebook"
            href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($url) ?>"
          >
            <i class="bi bi-facebook"></i>
          </a>

          <button
            class="sh sh-text"
            type="button"
            id="copyLink"
            data-url="<?= e($url) ?>"
          >
            <i class="bi bi-link-45deg"></i>
            Copy Link
          </button>

        </div>

        <span class="mono-meta">
          <i class="bi bi-eye"></i>
          <?= number_format((int)$post['views']) ?> views
        </span>

      </div>

      <div class="tags">
        <span class="mono-label">Tags:</span>

        <?php foreach ($post['tags'] as $tg): ?>
          <a href="<?= BASE_URL ?>tag/<?= e(slugify($tg)) ?>/">
            #<?= e($tg) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <?php include __DIR__ . '/author-box.php'; ?>

      <?php include __DIR__ . '/comments.php'; ?>

    </article>

    <?php include __DIR__ . '/post-sidebar.php'; ?>

  </div>

  <section class="more" aria-labelledby="h-more">

    <div class="sec-head">

      <div>
        <p class="eyebrow">
          <i class="bi bi-circle-fill dot"></i>
          Continue Reading
        </p>

        <h2 id="h-more">
          More from <?= e($post['category']) ?>
        </h2>
      </div>

      <a class="sec-link" href="<?= e(cat_url($post['category_slug'])) ?>">
        Explore All <?= e($post['category']) ?>
        <i class="bi bi-arrow-right"></i>
      </a>

    </div>

    <div class="row g-4">

      <?php foreach ($more as $post_card): ?>

        <?php
        $post = $post_card;
        ?>

        <div class="col-sm-6 col-xl-3">
          <?php include __DIR__ . '/post-card-compact.php'; ?>
        </div>

      <?php endforeach; ?>

    </div>

  </section>

</main>

<?php require __DIR__ . '/footer.php'; ?>
