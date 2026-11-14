<?php
namespace App\Middleware;

use App\Core\Auth;

/** Role-based access control: blocks users below the required role. */
class RoleMiddleware
{
    public static function handle(string $role): void
    {
        if (!Auth::can($role)) {
            http_response_code(403);
            echo view('admin/forbidden', ['title' => 'Access denied'], 'layouts/admin');
            exit;
        }
    }
}
