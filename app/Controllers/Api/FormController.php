<?php
namespace App\Controllers\Api;

use App\Services\EnquiryService;
use App\Services\SpamGuard;

/** JSON endpoints for enquiry, consultation and proposal forms. */
class FormController
{
    public function token(): string
    {
        json_response(['csrf' => csrf_token(), 'ft' => SpamGuard::formToken()]);
        return '';
    }

    public function enquiry(): string
    {
        if ($spam = SpamGuard::check('enquiries')) {
            json_response(['success' => false, 'message' => $spam], 429);
        }
        $fields = ['type', 'full_name', 'company', 'email', 'phone', 'industry', 'company_size', 'message', 'consent', 'services', 'preferred_date', 'preferred_time', 'meeting_mode', 'source_page', 'quick', 'divisions'];
        $in = [];
        foreach ($fields as $f) {
            $in[$f] = input($f);
        }
        list($ok, $payload) = EnquiryService::submit($in);
        json_response(array_merge(['success' => $ok], $payload), $ok ? 200 : 422);
        return '';
    }
}
