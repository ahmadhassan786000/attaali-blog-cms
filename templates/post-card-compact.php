<?php /* Latest in AI card – expects $post */ ?>
<article class="card-x">
  <a class="card-img-wrap" href="<?= e(post_url($post)) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e($post['image']) ?>" alt="" width="330" height="200" loading="lazy"></a>
  <div class="card-body-x">
    <span class="kicker"><?= e($post['kicker']) ?></span>
    <h3><a href="<?= e(post_url($post)) ?>"><?= e($post['title']) ?></a></h3>
    <p class="excerpt"><?= e($post['excerpt']) ?></p>
    <div class="card-foot"><span class="mono-meta"><?= (int)$post['read'] ?> min read</span><a class="read-link" href="<?= e(post_url($post)) ?>">Read <i class="bi bi-arrow-right"></i></a></div>
  </div>
</article>
