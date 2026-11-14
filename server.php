<?php
/**
 * Router for PHP's built-in development server only:
 *   php -S 127.0.0.1:8090 -t public server.php
 * Not used in production (Apache uses public/.htaccess).
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path !== '/' && is_file(__DIR__ . '/public' . $path)) {
    return false;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/public/index.php';
