<?php
/* Bootstrap file: included at the top of every public / admin script */
define('SITE_ROOT', dirname(__DIR__));
define('ASSET_VER', '1.0.0');

if (!is_file(__DIR__ . '/config.php')) {
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    if (defined('ADMIN_AREA')) {
        $dir = rtrim(dirname($dir), '/');
    }
    header('Location: ' . $dir . '/install.php');
    exit;
}

require_once __DIR__ . '/config.php';
date_default_timezone_set(defined('SITE_TIMEZONE') ? SITE_TIMEZONE : 'Asia/Karachi');

if (defined('DEBUG') && DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', '0');
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/tools-registry.php';
require_once __DIR__ . '/partials.php';

// Start the session up-front so cookies can always be sent before any HTML output
start_session();
