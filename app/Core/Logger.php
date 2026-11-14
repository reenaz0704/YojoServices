<?php
namespace App\Core;

/** Simple file logger writing to storage/logs (outside the web root). */
class Logger
{
    public static function write(string $channel, string $level, string $message): void
    {
        $line = sprintf("[%s] %s.%s: %s\n", date('Y-m-d H:i:s'), $channel, strtoupper($level), $message);
        @file_put_contents(STORAGE_PATH . '/logs/' . $channel . '-' . date('Y-m') . '.log', $line, FILE_APPEND | LOCK_EX);
    }

    public static function error(string $message): void
    {
        self::write('app', 'error', $message);
    }

    public static function info(string $message, string $channel = 'app'): void
    {
        self::write($channel, 'info', $message);
    }
}
