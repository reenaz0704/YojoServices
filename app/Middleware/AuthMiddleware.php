<?php
namespace App\Middleware;

use App\Core\Auth;

/** Requires an authenticated admin session; enforces idle timeout. */
class AuthMiddleware
{
    const IDLE_SECONDS = 3600;

    public static function handle(): void
    {
        if (!Auth::check()) {
            redirect('admin/login');
        }
        if (isset($_SESSION['last_seen']) && time() - $_SESSION['last_seen'] > self::IDLE_SECONDS) {
            Auth::logout();
            flash('error', 'Your session expired. Please sign in again.');
            redirect('admin/login');
        }
        $_SESSION['last_seen'] = time();
        header('Cache-Control: no-store, private');
    }
}
