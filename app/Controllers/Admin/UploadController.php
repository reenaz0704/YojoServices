<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Services\Uploader;

/** Image upload endpoint for the rich-text editor (returns JSON). */
class UploadController
{
    public function store(): string
    {
        if (empty($_FILES['file'])) {
            json_response(['success' => false, 'message' => 'No file uploaded.'], 422);
        }
        list($ok, $res) = Uploader::store($_FILES['file']);
        if (!$ok) {
            json_response(['success' => false, 'message' => $res], 422);
        }
        Auth::log('upload', 'media', 0, $res);
        json_response(['success' => true, 'file' => $res, 'url' => media_url($res)]);
        return '';
    }
}
