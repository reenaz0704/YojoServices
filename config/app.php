<?php
/**
 * Application configuration. Override per environment in config/app.local.php
 * (that file is ignored by git / not deployed with defaults).
 */
return [
    'name'         => 'Yojo Solutions',
    // Public canonical URL, e.g. https://audit.yojosolutions.com (no trailing slash). Empty = auto-detect.
    'url'          => getenv('APP_URL') ?: '',
    // Force a URL base path when hosted in a sub-folder; null = auto-detect from SCRIPT_NAME.
    'base_path'    => null,
    'debug'        => getenv('APP_DEBUG') === '1',
    'timezone'     => 'Asia/Kolkata',
    'session_name' => 'yojo_audit_sess',
    'uploads'      => [
        'max_bytes' => 4 * 1024 * 1024,
        'allowed'   => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif', 'image/svg+xml' => 'svg'],
    ],
    // Anti-spam: minimum seconds between form render and submit; max submissions per IP per 10 minutes.
    'spam' => [
        'min_seconds' => 3,
        'max_per_window' => 5,
    ],
];
