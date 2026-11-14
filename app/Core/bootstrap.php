<?php
/**
 * Application bootstrap: autoloading, configuration, error handling, sessions.
 * Included by public/index.php and CLI scripts. Compatible with PHP 7.4+.
 */
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__, 2));
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', BASE_PATH . '/resources/views');
define('STORAGE_PATH', BASE_PATH . '/storage');

// PSR-4 style autoloader for the App\ namespace.
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') !== 0) {
        return;
    }
    $file = APP_PATH . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require APP_PATH . '/Core/helpers.php';

date_default_timezone_set((string) config('app.timezone', 'Asia/Kolkata'));

// Errors: log always, display only in debug mode.
error_reporting(E_ALL);
ini_set('display_errors', config('app.debug') ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', STORAGE_PATH . '/logs/php-error.log');

set_exception_handler(function ($e) {
    \App\Core\Logger::error($e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, (string) $e . PHP_EOL);
        exit(1);
    }
    http_response_code(500);
    if (is_api_request()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again later.']);
        return;
    }
    if (config('app.debug')) {
        echo '<pre>' . e((string) $e) . '</pre>';
        return;
    }
    echo \App\Core\View::render('pages/error', ['code' => 500, 'title' => 'Something went wrong']);
});

if (PHP_SAPI !== 'cli') {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_name((string) config('app.session_name', 'yojo_session'));
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    if ($secure) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
