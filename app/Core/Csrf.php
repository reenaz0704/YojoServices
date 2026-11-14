<?php
namespace App\Core;

/** Session-bound CSRF token. */
class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function verify(?string $token): bool
    {
        return is_string($token) && !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
    }

    public static function fromRequest(): ?string
    {
        $t = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? input('_csrf');
        return is_string($t) ? $t : null;
    }
}
