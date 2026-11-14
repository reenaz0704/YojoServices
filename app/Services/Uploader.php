<?php
namespace App\Services;

/**
 * Validates and stores uploaded images in storage/uploads (outside the web root).
 * Files are served via /media/{file} by MediaController with a whitelisted MIME type.
 */
class Uploader
{
    public static function store(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return [false, 'Upload failed (code ' . ($file['error'] ?? '?') . ').'];
        }
        $cfg = config('app.uploads');
        if ($file['size'] > $cfg['max_bytes']) {
            return [false, 'File is too large (max ' . round($cfg['max_bytes'] / 1048576) . ' MB).'];
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset($cfg['allowed'][$mime])) {
            return [false, 'Only JPG, PNG, WEBP, GIF or SVG images are allowed.'];
        }
        if ($mime === 'image/svg+xml') {
            $svg = (string) file_get_contents($file['tmp_name']);
            if (preg_match('/<script|on\w+\s*=|javascript:|<foreignObject/i', $svg)) {
                return [false, 'SVG contains disallowed content.'];
            }
        } elseif (@getimagesize($file['tmp_name']) === false) {
            return [false, 'The file is not a valid image.'];
        }
        $name = date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $cfg['allowed'][$mime];
        if (!move_uploaded_file($file['tmp_name'], STORAGE_PATH . '/uploads/' . $name)) {
            return [false, 'Could not save the file.'];
        }
        return [true, $name];
    }
}
