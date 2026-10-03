<?php
require __DIR__ . '/includes/init.php';
header('Content-Type: text/plain; charset=UTF-8');
echo "User-agent: *\n";
echo "Allow: /\n";
echo "Disallow: /admin/\n";
echo "Disallow: /includes/\n";
echo "Disallow: /install.php\n";
echo "Disallow: /search?\n\n";
echo "Sitemap: " . abs_url('sitemap.xml') . "\n";
