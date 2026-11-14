<?php
namespace App\Middleware;

use App\Core\Csrf;

/** Rejects state-changing requests without a valid CSRF token. */
class CsrfMiddleware
{
    public static function handle(): void
    {
        if (!Csrf::verify(Csrf::fromRequest())) {
            if (is_api_request()) {
                json_response(['success' => false, 'message' => 'Your session has expired. Please refresh the page and try again.'], 419);
            }
            abort(419, 'Session expired — please go back and try again.');
        }
    }
}
