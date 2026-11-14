<?php
/**
 * Global helper functions used across controllers and views.
 */

use App\Core\Database;
use App\Core\View;
use App\Core\Csrf;

/** Read a config value using dot notation: config('mail.host'). */
function config(string $key, $default = null)
{
    static $cache = [];
    $parts = explode('.', $key);
    $file = array_shift($parts);
    if (!array_key_exists($file, $cache)) {
        $path = BASE_PATH . '/config/' . $file . '.php';
        $cache[$file] = is_file($path) ? require $path : [];
        // Optional untracked overrides: config/<file>.local.php
        $local = BASE_PATH . '/config/' . $file . '.local.php';
        if (is_file($local)) {
            $cache[$file] = array_replace_recursive($cache[$file], require $local);
        }
    }
    $value = $cache[$file];
    foreach ($parts as $p) {
        if (!is_array($value) || !array_key_exists($p, $value)) {
            return $default;
        }
        $value = $value[$p];
    }
    return $value;
}

function db(): Database
{
    return Database::instance();
}

/** HTML-escape output. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Base path of the app (supports installation in a sub-folder). */
function base_path_url(): string
{
    static $base = null;
    if ($base === null) {
        $configured = config('app.base_path');
        if ($configured !== null && $configured !== '') {
            $base = rtrim((string) $configured, '/');
        } else {
            $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
            $base = rtrim($dir === '.' ? '' : $dir, '/');
        }
    }
    return $base;
}

function url(string $path = '/'): string
{
    return base_path_url() . '/' . ltrim($path, '/');
}

function full_url(string $path = '/'): string
{
    $configured = rtrim((string) config('app.url', ''), '/');
    if ($configured !== '') {
        return $configured . '/' . ltrim($path, '/');
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url($path);
}

function asset(string $path): string
{
    $file = BASE_PATH . '/public/assets/' . ltrim($path, '/');
    $v = is_file($file) ? filemtime($file) : 1;
    return url('assets/' . ltrim($path, '/')) . '?v=' . $v;
}

/** URL for an uploaded media file (served through MediaController). */
function media_url(?string $file): string
{
    if (!$file) {
        return '';
    }
    if (preg_match('#^https?://#', $file)) {
        return $file;
    }
    return url('media/' . rawurlencode(basename($file)));
}

/** Site setting stored in the `settings` table, cached per request. */
function setting(string $key, $default = '')
{
    static $settings = null;
    if ($settings === null) {
        try {
            $settings = [];
            foreach (db()->fetchAll('SELECT setting_key, setting_value FROM settings') as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (\Throwable $e) {
            $settings = [];
        }
    }
    return (isset($settings[$key]) && $settings[$key] !== '') ? $settings[$key] : $default;
}

function view(string $template, array $data = [], ?string $layout = 'layouts/main'): string
{
    return View::render($template, $data, $layout);
}

function partial(string $name, array $data = []): void
{
    echo View::render('partials/' . $name, $data, null);
}

function redirect(string $path, int $code = 302): void
{
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)), true, $code);
    exit;
}

function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function abort(int $code = 404, string $message = 'Page not found'): void
{
    http_response_code($code);
    if (is_api_request()) {
        json_response(['success' => false, 'message' => $message], $code);
    }
    echo view('pages/error', ['code' => $code, 'title' => $message, 'meta_title' => $message]);
    exit;
}

function is_api_request(): bool
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    return strpos($uri, '/api/') !== false;
}

function csrf_token(): string
{
    return Csrf::token();
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Csrf::token()) . '">';
}

function input(string $key, $default = null)
{
    static $json = null;
    if ($json === null) {
        $json = [];
        if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            $decoded = json_decode((string) file_get_contents('php://input'), true);
            $json = is_array($decoded) ? $decoded : [];
        }
    }
    if (array_key_exists($key, $json)) {
        return $json[$key];
    }
    if (array_key_exists($key, $_POST)) {
        return $_POST[$key];
    }
    return $_GET[$key] ?? $default;
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

/** Split newline-separated admin text into a clean list. */
function lines(?string $text): array
{
    if (!$text) {
        return [];
    }
    return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text)), 'strlen'));
}

/** Split "Title :: description" list items. */
function split_item(string $line): array
{
    $parts = explode('::', $line, 2);
    return [trim($parts[0]), isset($parts[1]) ? trim($parts[1]) : ''];
}

function slugify(string $text): string
{
    $text = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $text), '-'));
    return $text !== '' ? $text : 'item-' . substr(bin2hex(random_bytes(3)), 0, 6);
}

function str_limit(?string $text, int $limit = 140): string
{
    $text = trim(strip_tags((string) $text));
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $limit)) . '…';
}

function format_date(?string $date, string $format = 'd M Y'): string
{
    return $date ? date($format, strtotime($date)) : '';
}

function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }
    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
}

/** Inline SVG icon from the icon library partial. */
function icon(string $name, string $class = 'icon'): string
{
    return \App\Services\Icons::get($name, $class);
}

function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = base_path_url();
    if ($base !== '' && strpos($path, $base) === 0) {
        $path = substr($path, strlen($base));
    }
    return '/' . trim($path, '/');
}

function is_active(string $prefix): bool
{
    $p = current_path();
    return $prefix === '/' ? $p === '/' : strpos($p, $prefix) === 0;
}

/** Admin-entered link: full URLs, anchors, mailto: and tel: pass through; site paths get the base URL. */
function site_link(?string $link): string
{
    $link = trim((string) $link);
    if ($link === '') {
        return url('/');
    }
    if (preg_match('#^(https?:)?//|^(mailto:|tel:|\#)#i', $link)) {
        return $link;
    }
    return url($link);
}

/** Sanitised rich text whose root-relative links ("/services/...") respect the install base path. */
function rich(?string $html): string
{
    $base = base_path_url();
    $html = (string) $html;
    return $base === '' ? $html : preg_replace('#href="/(?!/)#', 'href="' . $base . '/', $html);
}

/** Front-end design templates (palette + typography + 3D scene). */
function site_templates(): array
{
    return [
        'emerald' => ['name' => 'Emerald Quantum', 'desc' => 'Deep forest black, emerald and lime — a pulsing quantum core.', 'font' => 'Space Grotesk', 'body' => 'Manrope', 'theme' => '#03100b', 'swatch' => ['#34f5a4', '#c6ff4d', '#0fb8a0']],
        'ember'   => ['name' => 'Solar Ember', 'desc' => 'Volcanic charcoal with amber, tangerine and crimson — a molten forge knot.', 'font' => 'Sora', 'body' => 'Manrope', 'theme' => '#0e0705', 'swatch' => ['#ff8a3d', '#ffcc4d', '#ff3d6e']],
        'sentinel' => ['name' => 'Sentinel Audit Grid', 'desc' => 'Cyber-audit command centre: 3D shield, padlock and scanner in neon green, yellow and scarlet, with a HUD-style layout.', 'font' => 'Chakra Petch', 'body' => 'IBM Plex Sans', 'theme' => '#07090a', 'swatch' => ['#2bff88', '#ffd23f', '#ff2e4d']],
        'prism' => ['name' => 'Prism Pulse', 'desc' => 'Bright magenta, orange, yellow and lime gradients; a different 3D scene for each section (vortex, voxel cube, crystal rift, flow wave, galaxy) and new layouts: cascade carousel, stacked cards, showcase, roadmap, spotlight slider.', 'font' => 'Syne', 'body' => 'Inter', 'theme' => '#0a0507', 'swatch' => ['#ff2fb9', '#ff7a1a', '#ffe53b']],
        'prism-light' => ['name' => 'Prism Pulse Light', 'desc' => 'Prism Pulse colours and layout on warm white: bright magenta, orange, yellow and lime gradients with light 3D scenes.', 'font' => 'Syne', 'body' => 'Inter', 'theme' => '#fbf6ef', 'swatch' => ['#fbf6ef', '#ff2fb9', '#ff7a1a'], 'base' => 'prism', 'tone' => 'light', 'layout' => 'prism'],
        'sentinel-light' => ['name' => 'Sentinel Audit Grid Light', 'desc' => 'Sentinel green, yellow and scarlet on clean white, with a new layout (spotlight frameworks, expanding service columns, stacked reasons, cascade industries) and a 3D cyber shield.', 'font' => 'Chakra Petch', 'body' => 'IBM Plex Sans', 'theme' => '#ffffff', 'swatch' => ['#ffffff', '#12c96a', '#ff2e4d'], 'base' => 'sentinel', 'tone' => 'light', 'layout' => 'alt'],
        'prism-clay' => ['name' => 'Prism Studio Light', 'desc' => 'Warm white with Prism colours, the Gallery layout (mirror carousel, slipstream slider, expanding columns) and soft clay 3D shapes with real shadows.', 'font' => 'Syne', 'body' => 'Inter', 'theme' => '#fbf6ef', 'swatch' => ['#fbf6ef', '#ffb3de', '#ffc58a'], 'base' => 'prism', 'tone' => 'light', 'layout' => 'gallery', 'scene' => 'clay'],
        'sentinel-clay' => ['name' => 'Sentinel Studio Light', 'desc' => 'Clean white with Sentinel green, yellow and scarlet, the Gallery layout and clay 3D: inflated shield, padlock, rounded data cubes and iso stairs.', 'font' => 'Chakra Petch', 'body' => 'IBM Plex Sans', 'theme' => '#ffffff', 'swatch' => ['#ffffff', '#8fe8b6', '#ff9aa8'], 'base' => 'sentinel', 'tone' => 'light', 'layout' => 'gallery', 'scene' => 'clay'],
        'gilt' => ['name' => 'Saffron Maroon (Royal Light)', 'desc' => 'Warm ivory background with maroon, crimson, gold, saffron yellow and blush pink; serif headings, ornamental gold header, flowing colour background, new section layout and clay 3D.', 'font' => 'Playfair Display', 'body' => 'DM Sans', 'theme' => '#fffaf2', 'swatch' => ['#7a1f3d', '#e63946', '#d4a017', '#ffc2d1'], 'base' => 'gilt', 'tone' => 'light', 'layout' => 'gilt', 'scene' => 'clay'],
        'nebula'  => ['name' => 'Nebula Violet', 'desc' => 'Cosmic plum with violet, magenta and rose — a neural particle orbit.', 'font' => 'Outfit', 'body' => 'Manrope', 'theme' => '#0b0613', 'swatch' => ['#b46bff', '#ff5fd2', '#ff9a8b']],
    ];
}

function site_template(): string
{
    static $t = null;
    if ($t !== null) { return $t; }
    $all = site_templates();
    $t = (string) setting('site_template', 'emerald');
    if (\App\Core\Auth::check()) {
        $q = (string) ($_GET['template'] ?? '');
        if (isset($all[$q])) {
            setcookie('yojo_tpl_preview', $q, ['expires' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
            $_COOKIE['yojo_tpl_preview'] = $q;
        }
        $c = (string) ($_COOKIE['yojo_tpl_preview'] ?? '');
        if (isset($all[$c])) { $t = $c; }
    }
    if (!isset($all[$t])) { $t = 'emerald'; }
    return $t;
}

/** Layout styles selectable in admin (independent of colours). '' = template default. */
function site_layouts(): array
{
    return ['' => 'Template default', 'classic' => 'Classic grid', 'prism' => 'Prism (cascade, stacked cards, showcase)', 'alt' => 'Sentinel (spotlight, columns, stacked reasons)', 'gallery' => 'Gallery (mirror carousel, slipstream, columns)'];
}

/** 3D scene styles selectable in admin. '' = template default. */
function site_scene_styles(): array
{
    return ['' => 'Template default', 'neon' => 'Neon particles (vortex, galaxy, crystal)', 'clay' => 'Clay studio (soft 3D shapes, best on light templates)', 'off' => 'No 3D (gradient background only)'];
}
