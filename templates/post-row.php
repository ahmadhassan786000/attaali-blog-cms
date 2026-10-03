<?php /* Technology horizontal card – expects $post */ ?>
<article class="row-card">
  <a href="<?= e(post_url($post)) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e($post['image']) ?>" alt="" width="220" height="220" loading="lazy"></a>
  <div>
    <span class="kicker"><?= e($post['kicker']) ?></span>
    <h3><a href="<?= e(post_url($post)) ?>"><?= e($post['title']) ?></a></h3>
    <p class="excerpt"><?= e($post['excerpt']) ?></p>
    <p class="mono-meta mb-0"><?= (int)$post['read'] ?> min read &bull; <a class="read-link" href="<?= e(post_url($post)) ?>">Read Article <i class="bi bi-arrow-right"></i></a></p>
  </div>
</article>
