<?php
namespace App\Core;

/**
 * Session-based authentication with brute-force throttling and role levels.
 * Role levels: viewer(1) < editor(2) < admin(3) < super_admin(4).
 */
class Auth
{
    const MAX_ATTEMPTS = 5;
    const LOCK_MINUTES = 15;

    public static function attempt(string $email, string $password): array
    {
        $ip = client_ip();
        $recent = (int) db()->value(
            'SELECT COUNT(*) FROM login_attempts WHERE (ip = ? OR email = ?) AND success = 0 AND created_at > (NOW() - INTERVAL ' . self::LOCK_MINUTES . ' MINUTE)',
            [$ip, $email]
        );
        if ($recent >= self::MAX_ATTEMPTS) {
            return [false, 'Too many failed attempts. Please try again in ' . self::LOCK_MINUTES . ' minutes.'];
        }

        $user = db()->fetch(
            'SELECT u.*, r.slug AS role_slug, r.level AS role_level, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id WHERE u.email = ? LIMIT 1',
            [$email]
        );
        $ok = $user && (int) $user['is_active'] === 1 && password_verify($password, $user['password_hash']);
        db()->insert('login_attempts', ['ip' => $ip, 'email' => substr($email, 0, 190), 'success' => $ok ? 1 : 0]);

        if (!$ok) {
            return [false, 'Invalid email or password.'];
        }
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            db()->update('users', ['password_hash' => password_hash($password, PASSWORD_DEFAULT)], (int) $user['id']);
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['login_at'] = time();
        db()->update('users', ['last_login_at' => date('Y-m-d H:i:s')], (int) $user['id']);
        self::log('login', 'users', (int) $user['id']);
        return [true, ''];
    }

    public static function user(): ?array
    {
        static $user = false;
        if ($user !== false) {
            return $user;
        }
        if (empty($_SESSION['user_id'])) {
            return $user = null;
        }
        $user = db()->fetch(
            'SELECT u.id, u.name, u.email, u.is_active, r.slug AS role_slug, r.level AS role_level, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id WHERE u.id = ?',
            [(int) $_SESSION['user_id']]
        );
        if (!$user || (int) $user['is_active'] !== 1) {
            self::logout();
            return $user = null;
        }
        return $user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function level(): int
    {
        $u = self::user();
        return $u ? (int) $u['role_level'] : 0;
    }

    /** True when the current user has at least the given role. */
    public static function can(string $role): bool
    {
        $levels = ['viewer' => 1, 'editor' => 2, 'admin' => 3, 'super_admin' => 4];
        return self::level() >= ($levels[$role] ?? 99);
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /** Write an entry to the admin audit trail. */
    public static function log(string $action, string $entity = '', int $entityId = 0, string $details = ''): void
    {
        try {
            db()->insert('activity_log', [
                'user_id'   => $_SESSION['user_id'] ?? null,
                'action'    => $action,
                'entity'    => $entity,
                'entity_id' => $entityId ?: null,
                'details'   => substr($details, 0, 500),
                'ip'        => client_ip(),
            ]);
        } catch (\Throwable $e) {
            Logger::error('activity log failed: ' . $e->getMessage());
        }
    }
}
