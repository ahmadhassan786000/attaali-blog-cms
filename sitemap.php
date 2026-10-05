<?php
require __DIR__ . '/includes/init.php';
header('Content-Type: application/xml; charset=UTF-8');

$urls = array();
$urls[] = array(abs_url(''), '1.0', 'daily');
$urls[] = array(abs_url('tools'), '0.9', 'weekly');
$urls[] = array(abs_url('blog'), '0.8', 'daily');
$urls[] = array(abs_url('contact'), '0.4', 'monthly');

foreach (all_tools() as $slug => $t) {
    $urls[] = array(abs_url('tools/' . $slug), '0.8', 'monthly');
}
foreach (db_all('SELECT slug FROM categories') as $c) {
    $urls[] = array(abs_url('category/' . rawurlencode($c['slug'])), '0.6', 'weekly');
}
foreach (db_all("SELECT slug, updated_at FROM posts WHERE status = 'published'") as $p) {
    $urls[] = array(abs_url('blog/' . rawurlencode($p['slug'])), '0.7', 'monthly', $p['updated_at']);
}
foreach (db_all('SELECT slug, updated_at FROM pages') as $p) {
    $urls[] = array(abs_url($p['slug']), '0.5', 'yearly', $p['updated_at']);
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo '<url><loc>' . htmlspecialchars($u[0], ENT_XML1) . '</loc>';
    if (isset($u[3])) {
        echo '<lastmod>' . date('c', strtotime($u[3])) . '</lastmod>';
    }
    echo '<changefreq>' . $u[2] . '</changefreq><priority>' . $u[1] . '</priority></url>' . "\n";
}
echo '</urlset>';
