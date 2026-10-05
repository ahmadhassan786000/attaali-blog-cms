<?php /* expects $post['author'] */ $a = $post['author']; ?>
<section class="author-box" aria-label="About the author">
  <span class="ab-avatar"><?= e($a['initials']) ?></span>
  <div>
    <div class="ab-top"><h2><?= e($a['name']) ?></h2><a class="mono-meta blue" href="<?= BASE_URL ?>author/<?= e($a['slug']) ?>/">Lead Systems Editor</a></div>
    <p><?= e($a['bio']) ?></p>
    <div class="ab-foot"><span class="d-flex gap-2"><a class="sh" href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a><a class="sh" href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a></span>
      <a class="sec-link" href="<?= BASE_URL ?>author/<?= e($a['slug']) ?>/">View All <?= (int)$a['articles'] ?> Articles by <?= e(explode(' ', $a['name'])[0]) ?> <i class="bi bi-arrow-right"></i></a></div>
  </div>
</section>
