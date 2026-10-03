<?php
/* General helper functions (PHP 7.2 compatible, procedural) */

function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function contains($haystack, $needle)
{
    return $needle === '' || strpos((string)$haystack, (string)$needle) !== false;
}

function starts_with($haystack, $needle)
{
    return substr((string)$haystack, 0, strlen($needle)) === $needle;
}

/* ---------- URLs ---------- */
function base_path()
{
    return defined('BASE_PATH') ? BASE_PATH : '';
}

function url($path = '')
{
    return base_path() . '/' . ltrim($path, '/');
}

function site_origin()
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    return ($https ? 'https://' : 'http://') . $host;
}

function abs_url($path = '')
{
    $custom = setting('site_url', '');
    if ($custom !== '') {
        return rtrim($custom, '/') . '/' . ltrim($path, '/');
    }
    return site_origin() . url($path);
}

function asset($path)
{
    return url('assets/' . $path) . '?v=' . ASSET_VER;
}

function redirect($path)
{
    header('Location: ' . $path);
    exit;
}

function current_path()
{
    $p = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
    return $p ? $p : '/';
}

/* ---------- Text helpers ---------- */
function slugify($text)
{
    $text = trim((string)$text);
    if (function_exists('mb_strtolower')) {
        $text = mb_strtolower($text, 'UTF-8');
    } else {
        $text = strtolower($text);
    }
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    if ($text === '' || $text === null) {
        $text = 'item-' . time();
    }
    return $text;
}

function unique_slug($table, $slug, $exclude_id = 0)
{
    $allowed = array('posts', 'categories', 'pages');
    if (!in_array($table, $allowed, true)) {
        return $slug;
    }
    $base = $slug;
    $i = 2;
    while (db_val("SELECT COUNT(*) FROM `$table` WHERE slug = ? AND id <> ?", array($slug, (int)$exclude_id), 0) > 0) {
        $slug = $base . '-' . $i;
        $i++;
    }
    return $slug;
}

function text_excerpt($html, $len = 160)
{
    $t = trim(preg_replace('/\s+/', ' ', strip_tags((string)$html)));
    if (function_exists('mb_strlen')) {
        if (mb_strlen($t, 'UTF-8') <= $len) {
            return $t;
        }
        $cut = mb_substr($t, 0, $len, 'UTF-8');
    } else {
        if (strlen($t) <= $len) {
            return $t;
        }
        $cut = substr($t, 0, $len);
    }
    $sp = strrpos($cut, ' ');
    if ($sp !== false && $sp > $len * 0.6) {
        $cut = substr($cut, 0, $sp);
    }
    return rtrim($cut, " ,.;:-") . '...';
}

function reading_time($html)
{
    $words = str_word_count(strip_tags((string)$html));
    return max(1, (int)ceil($words / 200));
}

function fdate($ts)
{
    return date('M j, Y', strtotime($ts));
}

function time_ago($ts)
{
    $diff = time() - strtotime($ts);
    if ($diff < 60) { return 'Just now'; }
    if ($diff < 3600) { return floor($diff / 60) . 'm ago'; }
    if ($diff < 86400) { return floor($diff / 3600) . 'h ago'; }
    if ($diff < 172800) { return 'Yesterday'; }
    if ($diff < 604800) { return floor($diff / 86400) . 'd ago'; }
    if ($diff < 2419200) { return floor($diff / 604800) . 'w ago'; }
    return fdate($ts);
}

function number_short($n)
{
    $n = (int)$n;
    if ($n >= 1000000) {
        return round($n / 1000000, 1) . 'M';
    }
    if ($n >= 1000) {
        return round($n / 1000, 1) . 'K';
    }
    return (string)$n;
}

/* ---------- Settings ---------- */
function settings_all()
{
    static $s = null;
    if ($s === null) {
        $s = array();
        foreach (db_all('SELECT k, v FROM settings') as $r) {
            $s[$r['k']] = $r['v'];
        }
    }
    return $s;
}

function setting($k, $default = '')
{
    $s = settings_all();
    return (isset($s[$k]) && $s[$k] !== '') ? $s[$k] : $default;
}

function set_setting($k, $v)
{
    db_exec('INSERT INTO settings (k, v) VALUES (?, ?) ON DUPLICATE KEY UPDATE v = VALUES(v)', array($k, $v));
}

function site_name()
{
    return setting('site_name', 'Attaali');
}

/* Output a configured ad unit (HTML pasted from AdSense) */
function ad($slot)
{
    $code = setting('ad_' . $slot, '');
    if ($code === '') {
        return;
    }
    echo '<div class="ad-wrap my-3 text-center" data-ad="' . e($slot) . '">' . $code . '</div>';
}

/* ---------- Session / CSRF / Flash ---------- */
function start_session()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name('attaali_sid');
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_set_cookie_params(0, '/', '', $https, true);
        session_start();
    }
}

function csrf_token()
{
    start_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_ok()
{
    start_session();
    $t = isset($_POST['csrf']) ? $_POST['csrf'] : (isset($_GET['csrf']) ? $_GET['csrf'] : (isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : ''));
    return !empty($_SESSION['csrf']) && is_string($t) && hash_equals($_SESSION['csrf'], $t);
}

function flash($type, $msg)
{
    start_session();
    $_SESSION['flash'][] = array($type, $msg);
}

function flash_render()
{
    start_session();
    if (empty($_SESSION['flash'])) {
        return '';
    }
    $out = '';
    foreach ($_SESSION['flash'] as $f) {
        $out .= '<div class="alert alert-' . e($f[0]) . ' alert-dismissible fade show" role="alert">' . e($f[1])
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
    unset($_SESSION['flash']);
    return $out;
}

/* ---------- Uploads ---------- */
function upload_image($file, $maxBytes = 5242880)
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return array(false, 'Upload failed (error code ' . (isset($file['error']) ? $file['error'] : '?') . ').');
    }
    if ($file['size'] > $maxBytes) {
        return array(false, 'Image is too large (max ' . round($maxBytes / 1048576) . ' MB).');
    }
    $info = @getimagesize($file['tmp_name']);
    if (!$info) {
        return array(false, 'File is not a valid image.');
    }
    $map = array(
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_GIF  => 'gif',
    );
    if (defined('IMAGETYPE_WEBP')) {
        $map[IMAGETYPE_WEBP] = 'webp';
    }
    if (!isset($map[$info[2]])) {
        return array(false, 'Only JPG, PNG, GIF and WEBP images are allowed.');
    }
    $sub = 'uploads/' . date('Y') . '/' . date('m');
    $dir = SITE_ROOT . '/' . $sub;
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
        return array(false, 'Could not create upload folder.');
    }
    $name = bin2hex(random_bytes(8)) . '.' . $map[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        return array(false, 'Could not save uploaded file.');
    }
    return array(true, $sub . '/' . $name);
}

function delete_upload($rel)
{
    if ($rel && starts_with($rel, 'uploads/') && !contains($rel, '..')) {
        $p = SITE_ROOT . '/' . $rel;
        if (is_file($p)) {
            @unlink($p);
        }
    }
}

/* ---------- Pagination ---------- */
function paginate($total, $perPage, $page, $baseUrl)
{
    $pages = (int)ceil($total / $perPage);
    if ($pages <= 1) {
        return '';
    }
    $sep = contains($baseUrl, '?') ? '&' : '?';
    $html = '<nav aria-label="Pagination"><ul class="pagination justify-content-center flex-wrap">';
    $link = function ($p, $label, $active = false, $disabled = false) use ($baseUrl, $sep) {
        $cls = 'page-item' . ($active ? ' active' : '') . ($disabled ? ' disabled' : '');
        $href = $disabled ? '#' : e($baseUrl . ($p > 1 ? $sep . 'page=' . $p : ''));
        return '<li class="' . $cls . '"><a class="page-link" href="' . $href . '">' . $label . '</a></li>';
    };
    $html .= $link($page - 1, '&laquo;', false, $page <= 1);
    $start = max(1, $page - 2);
    $end = min($pages, $page + 2);
    if ($start > 1) {
        $html .= $link(1, '1');
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        }
    }
    for ($i = $start; $i <= $end; $i++) {
        $html .= $link($i, $i, $i == $page);
    }
    if ($end < $pages) {
        if ($end < $pages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        }
        $html .= $link($pages, $pages);
    }
    $html .= $link($page + 1, '&raquo;', false, $page >= $pages);
    return $html . '</ul></nav>';
}

/* ---------- Page rendering variables (SEO) ---------- */
function seo($key = null, $value = null)
{
    static $data = array();
    if ($key === null) {
        return $data;
    }
    if ($value !== null) {
        $data[$key] = $value;
    }
    return isset($data[$key]) ? $data[$key] : '';
}

function not_found()
{
    http_response_code(404);
    seo('title', 'Page not found');
    seo('noindex', '1');
    include SITE_ROOT . '/includes/header.php';
    echo '<main class="container py-5 text-center"><h1 class="display-1 fw-bold text-gradient">404</h1>'
        . '<p class="lead">Sorry, the page you are looking for does not exist.</p>'
        . '<a class="btn btn-primary btn-lg" href="' . e(url('/')) . '"><i class="bi bi-house"></i> Back to home</a>'
        . ' <a class="btn btn-outline-primary btn-lg" href="' . e(url('tools')) . '">Browse tools</a></main>';
    include SITE_ROOT . '/includes/footer.php';
    exit;
}

/* Replace {{tokens}} inside page content */
function replace_tokens($html)
{
    $map = array(
        '{{site_name}}' => site_name(),
        '{{site_url}}'  => abs_url(''),
        '{{email}}'     => setting('contact_email', 'contact@' . preg_replace('/^www\./', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'example.com')),
        '{{year}}'      => date('Y'),
    );
    return strtr($html, $map);
}

/* ---------- Content queries ---------- */
function get_categories_with_counts()
{
    return db_all("SELECT c.*, (SELECT COUNT(*) FROM posts p WHERE p.category_id = c.id AND p.status = 'published') AS post_count
                   FROM categories c ORDER BY c.name ASC");
}

function post_url($p)
{
    return url('blog/' . rawurlencode($p['slug']));
}

function post_image($p)
{
    if (!empty($p['image'])) {
        return url($p['image']);
    }
    return asset('img/post-placeholder.svg');
}

