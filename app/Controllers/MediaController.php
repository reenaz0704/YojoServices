<?php
namespace App\Controllers;

/** Streams uploaded images from storage/uploads with a strict MIME whitelist. */
class MediaController extends Controller
{
    public function show(string $file): string
    {
        $file = basename($file);
        if (!preg_match('/^[A-Za-z0-9\-_.]+\.(jpg|png|webp|gif|svg)$/', $file)) {
            abort(404);
        }
        $path = STORAGE_PATH . '/uploads/' . $file;
        if (!is_file($path)) {
            abort(404);
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        if (!in_array($mime, array_keys(config('app.uploads.allowed')), true)) {
            abort(404);
        }
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=2592000');
        header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'");
        readfile($path);
        return '';
    }
}
