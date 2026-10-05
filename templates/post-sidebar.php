<?php /* expects $popular, $related */ ?>
<aside class="col-lg-4 side" aria-label="Sidebar"><div class="side-sticky">
  <form class="side-search" action="<?= BASE_URL ?>search/" method="get" role="search"><label for="sq" class="visually-hidden">Search</label><i class="bi bi-search"></i><input id="sq" name="q" type="search" placeholder="Search technical archives..." required minlength="2"></form>

  <section class="side-card" aria-labelledby="h-popd">
    <div class="side-head"><h2 id="h-popd" class="mono-label blue"><i class="bi bi-graph-up-arrow"></i> Popular Dispatches</h2><a class="mono-meta" href="<?= BASE_URL ?>blog/?sort=popular">View All</a></div>
    <ol class="pop-list"><?php foreach ($popular as $i => $p): ?>
      <li><span class="mono-meta"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span><div><a href="<?= e(post_url($p)) ?>"><?= e($p['title']) ?></a><span class="mono-meta d-block"><?= d($p['date'], 'M d, Y') ?> &bull; <?= (int)$p['read'] ?> min read</span></div></li>
    <?php endforeach; ?></ol>
  </section>

  <section class="side-news" aria-labelledby="h-sn">
    <p class="mono-label light"><i class="bi bi-envelope"></i> Weekly Intelligence</p>
    <h2 id="h-sn">Engineering Dispatches Delivered Every Thursday</h2>
    <p>Curated system breakdowns, frontier AI research syntheses, and actionable developer tooling benchmarks. No marketing spam.</p>
    <form id="sideNews" novalidate><label for="sn-email" class="visually-hidden">Email</label><input id="sn-email" type="email" placeholder="developer@domain.com" required><button type="submit">Subscribe Free</button></form>
    <p class="sn-note" id="snMsg" role="status">Free weekly briefing. Unsubscribe anytime.</p>
  </section>

  <section class="side-card" aria-labelledby="h-rel">
    <div class="side-head"><h2 id="h-rel" class="mono-label">Related Coverage</h2><i class="bi bi-diagram-3"></i></div>
    <ul class="rel-list"><?php foreach ($related as $p): ?>
      <li><a href="<?= e(post_url($p)) ?>"><img src="<?= e($p['image']) ?>" alt="" width="64" height="48" loading="lazy"></a>
        <div><span class="kicker"><?= e($p['kicker']) ?></span><a href="<?= e(post_url($p)) ?>"><?= e($p['title']) ?></a><span class="mono-meta d-block"><?= d($p['date'], 'M d, Y') ?></span></div></li>
    <?php endforeach; ?></ul>
  </section>

  <aside class="ad-box side-ad" aria-label="Advertisement"><span class="mono-label">Partner Network &mdash; Sidebar</span><div><i class="bi bi-box"></i><strong>Ad slot (sidebar)</strong><p>Admin-controlled ad code renders here.</p></div></aside>
</div></aside>
