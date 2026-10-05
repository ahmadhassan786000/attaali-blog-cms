<?php
require_once __DIR__ . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');

$posts = array_merge(
    [dummy_featured()],
    dummy_latest(),
    dummy_ai(),
    dummy_tech(),
    dummy_popular()
);

$post = null;

foreach ($posts as $item) {
    if (($item['slug'] ?? '') === $slug) {
        $post = $item;
        break;
    }
}

if (!$post) {
    $post = dummy_featured();
}

$page_title = $post['title'] . ' | ' . SITE_NAME;
$meta_desc = $post['excerpt'];
$canonical_path = post_url($post);

require __DIR__ . '/templates/header.php';
?>

<main id="main" class="article-page">

  <div class="container">

    <nav class="article-breadcrumb" aria-label="Breadcrumb">
      <a href="<?= BASE_URL ?>">Home</a>
      <i class="bi bi-chevron-right"></i>
      <a href="<?= e(cat_url($post['category_slug'])) ?>"><?= e($post['category']) ?></a>
      <i class="bi bi-chevron-right"></i>
      <span>Article</span>
    </nav>

    <article>

      <header class="article-header">

        <div class="article-category">
          <?= e($post['category']) ?>
        </div>

        <h1><?= e($post['title']) ?></h1>

        <p class="article-lede">
          <?= e($post['excerpt']) ?>
        </p>

        <div class="article-meta">

          <div class="article-author">
            <span class="avatar-sq">
              <?= e($post['author'] ? strtoupper(substr($post['author'], 0, 2)) : 'AT') ?>
            </span>

            <div>
              <strong><?= e($post['author'] ?: 'ATTAALI Editorial Team') ?></strong>
              <span>Author</span>
            </div>
          </div>

          <div class="article-meta-details">
            <span>
              <i class="bi bi-calendar3"></i>
              <?= d($post['date'], 'F j, Y') ?>
            </span>

            <span>
              <i class="bi bi-clock"></i>
              <?= (int)$post['read'] ?> min read
            </span>
          </div>

        </div>

      </header>

      <figure class="article-hero">
        <img
          src="<?= e($post['image']) ?>"
          alt="<?= e($post['title']) ?>"
          width="1200"
          height="630"
        >
      </figure>

      <div class="article-layout">

        <aside class="article-share" aria-label="Share article">

          <span class="share-label">Share</span>

          <a href="#" aria-label="Share on Facebook">
            <i class="bi bi-facebook"></i>
          </a>

          <a href="#" aria-label="Share on X">
            <i class="bi bi-twitter-x"></i>
          </a>

          <a href="#" aria-label="Share on LinkedIn">
            <i class="bi bi-linkedin"></i>
          </a>

          <button type="button" aria-label="Copy article link" id="copy-article-link">
            <i class="bi bi-link-45deg"></i>
          </button>

        </aside>

        <div class="article-content">

          <p class="article-intro">
            <?= e($post['excerpt']) ?>
          </p>

          <h2>Introduction</h2>

          <p>
            Technology changes quickly, but understanding why a change matters
            is often more important than simply following the latest release.
            This article examines the subject from a practical engineering
            perspective and focuses on the ideas, trade-offs, and real-world
            considerations behind it.
          </p>

          <p>
            The goal is to provide a clear explanation that developers,
            engineers, researchers, and technology professionals can use when
            evaluating their own tools and workflows.
          </p>

          <div class="article-callout">
            <div class="article-callout-icon">
              <i class="bi bi-lightbulb"></i>
            </div>
            <div>
              <strong>Key takeaway</strong>
              <p>
                The important question is not simply which technology is newer,
                but where it provides a meaningful improvement for the actual
                problem being solved.
              </p>
            </div>
          </div>

          <h2>What You Need to Know</h2>

          <p>
            Modern software systems are increasingly built around measurable
            performance, maintainability, reliability, and developer experience.
            These factors should be considered together instead of evaluating a
            technology from a single benchmark or feature.
          </p>

          <h3>Performance and efficiency</h3>

          <p>
            Performance depends heavily on workload, infrastructure, traffic
            patterns, and implementation details. A result that looks
            impressive in one environment may not translate directly to
            another production environment.
          </p>

          <h3>Practical implementation</h3>

          <p>
            Before adopting a new approach, teams should consider the existing
            architecture, learning curve, deployment requirements, monitoring,
            maintenance, and long-term compatibility.
          </p>

          <div class="article-code">
            <div class="code-head">
              <span>Example</span>
              <button type="button" class="copy-code">Copy</button>
            </div>
            <pre><code>// Example implementation
const result = await processData(input);

if (result.success) {
    console.log(result.data);
}</code></pre>
          </div>

          <h2>Final Thoughts</h2>

          <p>
            The best technical decisions usually come from understanding the
            problem first and then selecting the appropriate tool. Benchmarks,
            documentation, testing, and real-world constraints should all be
            considered before making an implementation decision.
          </p>

          <div class="article-tags">
            <span>Tags</span>
            <a href="#">Technology</a>
            <a href="#">Engineering</a>
            <a href="#">AI</a>
          </div>

        </div>

      </div>

    </article>

    <section class="related-articles">

      <div class="sec-head">
        <div>
          <p class="eyebrow">Continue Reading</p>
          <h2>Related Articles</h2>
        </div>
      </div>

      <div class="row g-4">

        <?php foreach (array_slice(dummy_latest(), 0, 3) as $related): ?>

          <div class="col-md-6 col-lg-4">
            <?php
            $post = $related;
            include __DIR__ . '/templates/post-card.php';
            ?>
          </div>

        <?php endforeach; ?>

      </div>

    </section>

  </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const copyButton = document.getElementById('copy-article-link');

    if (copyButton) {
        copyButton.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(window.location.href);
                this.innerHTML = '<i class="bi bi-check2"></i>';
            } catch (error) {
                console.error(error);
            }
        });
    }

    document.querySelectorAll('.copy-code').forEach(function (button) {
        button.addEventListener('click', async function () {

            const code = this.closest('.article-code')
                .querySelector('code')
                .innerText;

            try {
                await navigator.clipboard.writeText(code);
                this.textContent = 'Copied';

                setTimeout(() => {
                    this.textContent = 'Copy';
                }, 1500);

            } catch (error) {
                console.error(error);
            }
        });
    });

});
</script>

<?php require __DIR__ . '/templates/footer.php'; ?>
