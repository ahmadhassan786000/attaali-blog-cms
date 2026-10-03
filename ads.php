<?php
require __DIR__ . '/includes/init.php';
header('Content-Type: text/plain; charset=UTF-8');
echo setting('ads_txt', "google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0\n");
