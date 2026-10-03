<?php
/* Reusable HTML fragments */

function tool_card($slug, $tool)
{
    $cats = tool_categories();
    $color = isset($cats[$tool['cat']]) ? $cats[$tool['cat']]['color'] : 'primary';
    $search = strtolower($tool['name'] . ' ' . $tool['desc'] . ' ' . $tool['cat'] . ' ' . $slug);
    $h = '<div class="col-sm-6 col-lg-4 col-xl-3" data-tool-card data-cat="' . e($tool['cat']) . '" data-search="' . e($search) . '">';
    $h .= '<a class="tool-card" href="' . e(tool_url($slug)) . '">';
    $h .= '<div class="tool-icon c-' . e($color) . '"><i class="bi ' . e($tool['icon']) . '"></i></div>';
    $h .= '<h3>' . e($tool['name']) . '</h3><p>' . e(text_excerpt($tool['desc'], 95)) . '</p>';
    $h .= '</a></div>';
    return $h;
}

function category_badge_color($catName)
{
    $palette = array(
        'Tutorials' => '#4f46e5', 'Tips & Tricks' => '#d97706', 'Technology' => '#0891b2',
        'Guides' => '#dc2626',
    );
    if (isset($palette[$catName])) {
        return $palette[$catName];
    }
    $hash = crc32($catName);
    $hues = array('#4f46e5', '#0891b2', '#d97706', '#dc2626', '#059669', '#7c3aed');
    return $hues[$hash % count($hues)];
}

function post_card($p)
{
    $catColor = !empty($p['cat_name']) ? category_badge_color($p['cat_name']) : '#6b7280';
  $postUrl = post_url($p);
  $h = '<div class="col-md-6 col-lg-4"><article class="card post-card">';
  $h .= '<div class="thumb-wrap"><a href="' . e($postUrl) . '" class="post-thumb" aria-label="Read ' . e($p['title']) . '"><img src="' . e(post_image($p)) . '" alt="' . e($p['title']) . '" loading="lazy"><span class="thumb-shade"></span><span class="thumb-icon"><i class="bi bi-journal-richtext"></i></span></a>';
    if (!empty($p['cat_name'])) {
    $h .= '<a href="' . e(url('category/' . rawurlencode($p['cat_slug']))) . '" class="post-category text-decoration-none" style="--category-color:' . e($catColor) . '"><i class="bi bi-bookmark-fill"></i>' . e($p['cat_name']) . '</a>';
    }
    $h .= '</div>';
    $h .= '<div class="card-body d-flex flex-column">';
  $h .= '<div class="post-kicker"><i class="bi bi-stars"></i> Fresh read</div>';
  $h .= '<h3 class="card-title h5 mb-2"><a href="' . e($postUrl) . '" class="text-decoration-none">' . e($p['title']) . '</a></h3>';
    $ex = $p['excerpt'] !== '' ? $p['excerpt'] : text_excerpt($p['content'], 130);
    $h .= '<p class="excerpt flex-grow-1">' . e(text_excerpt($ex, 110)) . '</p>';
  $h .= '<div class="post-footer"><div class="post-author"><div class="avatar"><i class="bi bi-lightning-charge-fill"></i></div>';
  $h .= '<div class="meta"><div class="name">' . e(site_name()) . '</div><div class="time"><i class="bi bi-clock"></i> ' . e(time_ago($p['created_at'])) . ' <span class="meta-dot">&bull;</span> ' . reading_time($p['content']) . ' min read</div></div></div>';
  $h .= '<span class="post-arrow" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span></div>';
    $h .= '</div></article></div>';
    return $h;
}

/* Sidebar used on blog pages */
function render_sidebar()
{
    $cats = get_categories_with_counts();
    $popular = db_all("SELECT p.title, p.slug, p.views FROM posts p WHERE p.status = 'published' ORDER BY p.views DESC, p.created_at DESC LIMIT 5");
    ob_start();
    ?>
    <aside class="sidebar">
      <div class="card mb-3">
        <div class="card-body">
          <form action="<?php echo e(url('search')); ?>" method="get">
            <label class="form-label fw-bold" for="sbq">Search</label>
            <div class="input-group"><input id="sbq" class="form-control" name="q" placeholder="Search..."><button class="btn btn-primary" aria-label="Search"><i class="bi bi-search"></i></button></div>
          </form>
        </div>
      </div>
      <?php ad('sidebar'); ?>
      <div class="card mb-3">
        <div class="card-header"><i class="bi bi-folder2-open"></i> Categories</div>
        <ul class="list-group list-group-flush">
          <?php foreach ($cats as $c): ?>
            <li class="list-group-item d-flex justify-content-between bg-transparent">
              <a href="<?php echo e(url('category/' . rawurlencode($c['slug']))); ?>"><?php echo e($c['name']); ?></a>
              <span class="badge text-bg-secondary rounded-pill"><?php echo (int)$c['post_count']; ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php if ($popular): ?>
      <div class="card mb-3">
        <div class="card-header"><i class="bi bi-fire"></i> Popular posts</div>
        <ul class="list-group list-group-flush">
          <?php foreach ($popular as $pp): ?>
            <li class="list-group-item bg-transparent"><a href="<?php echo e(post_url($pp)); ?>"><?php echo e($pp['title']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
      <div class="card mb-3">
        <div class="card-header"><i class="bi bi-tools"></i> Free tools</div>
        <ul class="list-group list-group-flush">
          <?php foreach (array_slice(all_tools(), 0, 6, true) as $slug => $t): ?>
            <li class="list-group-item bg-transparent"><a href="<?php echo e(tool_url($slug)); ?>"><i class="bi <?php echo e($t['icon']); ?>"></i> <?php echo e($t['name']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>
    <?php
    return ob_get_clean();
}
