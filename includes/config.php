<?php

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'attaali');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

define('BASE_PATH', getenv('BASE_PATH') ?: '/attaali');
define('SITE_TIMEZONE', getenv('SITE_TIMEZONE') ?: 'Asia/Karachi');
define('DEBUG', filter_var(getenv('DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN));
