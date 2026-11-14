<?php
/**
 * CLI installer: creates the database (if permitted), imports schema.sql and seed.sql,
 * and optionally sets the administrator credentials.
 *
 * Usage:
 *   php database/install.php
 *   php database/install.php --admin-email=you@company.com --admin-password='StrongPass!23'
 */
if (PHP_SAPI !== 'cli') { exit("CLI only\n"); }
require dirname(__DIR__) . '/app/Core/bootstrap.php';

$opts = getopt('', ['admin-email::', 'admin-password::', 'no-create']);
$cfg = config('database');

if (!isset($opts['no-create'])) {
    $pdo = new PDO(sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $cfg['host'], $cfg['port']), $cfg['username'], $cfg['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $cfg['database']) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
}

$pdo = db()->pdo();
foreach (['schema.sql', 'seed.sql'] as $file) {
    $sql = file_get_contents(__DIR__ . '/' . $file);
    // Split on semicolons at line end (no stored procedures in these files)
    foreach (preg_split('/;\s*\n/', $sql) as $stmt) {
        $stmt = trim(preg_replace('/^--.*$/m', '', $stmt));
        if ($stmt !== '') { $pdo->exec($stmt); }
    }
    echo "Imported $file\n";
}

require __DIR__ . '/migrate.php';

if (!empty($opts['admin-email']) && !empty($opts['admin-password'])) {
    db()->query('UPDATE users SET email = ?, password_hash = ? WHERE id = 1', [$opts['admin-email'], password_hash($opts['admin-password'], PASSWORD_DEFAULT)]);
    echo "Administrator updated: {$opts['admin-email']}\n";
} else {
    echo "Default admin: admin@yojosolutions.com / ChangeMe@2026  (change it immediately in Admin → My Profile)\n";
}
echo "Done.\n";
