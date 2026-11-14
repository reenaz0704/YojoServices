<?php
namespace App\Services;

/**
 * Anti-spam checks for public forms:
 *  - honeypot field ("website") must be empty
 *  - form must not be submitted faster than a human could (signed timestamp)
 *  - per-IP rate limit within a 10-minute window
 *  - link-stuffing heuristic
 */
class SpamGuard
{
    /** Signed form-start token embedded in every public form. */
    public static function formToken(): string
    {
        $t = (string) time();
        return $t . '.' . hash_hmac('sha256', $t, self::key());
    }

    public static function check(string $table): ?string
    {
        if (trim((string) input('website', '')) !== '') {
            return 'Submission rejected.';
        }
        $token = (string) input('_ft', '');
        $parts = explode('.', $token, 2);
        if (count($parts) !== 2 || !hash_equals(hash_hmac('sha256', $parts[0], self::key()), $parts[1])) {
            return 'Invalid form token. Please refresh the page and try again.';
        }
        if (time() - (int) $parts[0] < (int) config('app.spam.min_seconds', 3)) {
            return 'Please take a moment to review your details before submitting.';
        }
        $count = (int) db()->value(
            "SELECT COUNT(*) FROM `$table` WHERE ip_address = ? AND created_at > (NOW() - INTERVAL 10 MINUTE)",
            [client_ip()]
        );
        if ($count >= (int) config('app.spam.max_per_window', 5)) {
            return 'Too many submissions from your network. Please try again in a few minutes.';
        }
        $message = (string) input('message', '');
        if (preg_match_all('#https?://#i', $message) > 3) {
            return 'Please remove links from your message.';
        }
        return null;
    }

    private static function key(): string
    {
        $file = STORAGE_PATH . '/cache/app.key';
        if (!is_file($file)) {
            @file_put_contents($file, bin2hex(random_bytes(32)));
        }
        return (string) @file_get_contents($file);
    }
}
