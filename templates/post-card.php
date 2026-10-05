<?php /* Latest Articles card – expects $post */ ?>
<article class="card-x">
  <a class="card-img-wrap" href="<?= e(post_url($post)) ?>" tabindex="-1" aria-hidden="true">
    <img src="<?= e($post['image']) ?>" alt="" width="440" height="250" loading="lazy">
    <span class="chip-over"><?= e($post['category']) ?></span>
  </a>
  <div class="card-body-x">
    <p class="mono-meta"><?= e($post['author']) ?> &bull; <?= d($post['date'], 'M j, Y') ?> &bull; <?= (int)$post['read'] ?> min</p>
    <h3><a href="<?= e(post_url($post)) ?>"><?= e($post['title']) ?></a></h3>
    <p class="excerpt"><?= e($post['excerpt']) ?></p>
    <div class="card-foot"><a class="read-link" href="<?= e(post_url($post)) ?>">Read More <i class="bi bi-arrow-right"></i></a>
      <button class="bm-btn" type="button" aria-label="Bookmark article" aria-pressed="false"><i class="bi bi-bookmark"></i></button></div>
  </div>
</article>
