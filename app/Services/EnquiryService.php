<?php
namespace App\Services;

use App\Core\Logger;
use App\Core\Validator;
use App\Models\Enquiry;

/**
 * Business logic for enquiries, consultation bookings and proposal requests:
 * validation → duplicate check → persistence → notification + acknowledgement emails.
 */
class EnquiryService
{
    const SIZES = ['1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'];

    public static function industries(): array
    {
        try {
            $rows = array_column(db()->fetchAll('SELECT name FROM industries WHERE is_published = 1 ORDER BY sort_order'), 'name');
        } catch (\Throwable $e) {
            $rows = [];
        }
        $rows[] = 'Government & Public Sector';
        $rows[] = 'Other';
        return $rows;
    }

    /** @return array [success(bool), payload(array)] */
    public static function submit(array $in): array
    {
        $type = in_array($in['type'] ?? '', array_keys(Enquiry::TYPES), true) ? $in['type'] : 'contact';
        $data = [
            'full_name'    => trim((string) ($in['full_name'] ?? '')),
            'company'      => trim((string) ($in['company'] ?? '')),
            'email'        => strtolower(trim((string) ($in['email'] ?? ''))),
            'phone'        => trim((string) ($in['phone'] ?? '')),
            'industry'     => trim((string) ($in['industry'] ?? '')),
            'company_size' => trim((string) ($in['company_size'] ?? '')),
            'message'      => trim((string) ($in['message'] ?? '')),
            'consent'      => $in['consent'] ?? null,
            'services'     => is_array($in['services'] ?? null) ? array_map('intval', $in['services']) : [],
            'preferred_date' => trim((string) ($in['preferred_date'] ?? '')),
            'preferred_time' => trim((string) ($in['preferred_time'] ?? '')),
            'meeting_mode'   => trim((string) ($in['meeting_mode'] ?? '')),
        ];

        // Quick-enquiry pop-up: visitors tick service divisions instead of individual services.
        $isQuick = !empty($in['quick']);
        $divisions = [];
        if (is_array($in['divisions'] ?? null)) {
            $wanted = array_slice(array_map('strval', $in['divisions']), 0, 10);
            foreach (db()->fetchAll('SELECT slug, name FROM service_categories WHERE is_published = 1') as $c) {
                if (in_array($c['slug'], $wanted, true)) { $divisions[] = $c['name']; }
            }
        }

        $rules = [
            'full_name'    => 'required|min:2|max:120',
            'company'      => 'required|min:2|max:160',
            'email'        => 'required|email|max:190',
            'phone'        => 'required|phone',
            'industry'     => 'required|max:120',
            'company_size' => 'required|in:' . implode(',', self::SIZES),
            'message'      => ($type === 'contact' && !$isQuick ? 'required|min:10' : 'max:5000') . '|max:5000',
            'consent'      => 'accepted',
            'services'     => $type === 'contact' ? 'array' : 'required|array',
        ];
        if ($type === 'consultation') {
            $rules['preferred_date'] = 'required|date|future';
            $rules['preferred_time'] = 'required|time';
            $rules['meeting_mode'] = 'required|in:Video call,Phone call,In person';
        }
        if ($divisions) {
            $data['message'] = trim('Services of interest: ' . implode(', ', $divisions) . "\n\n" . $data['message']);
        }
        $v = Validator::make($data, $rules, [
            'full_name' => 'Full name', 'company' => 'Company name', 'email' => 'Business email', 'phone' => 'Phone number',
            'company_size' => 'Company size', 'consent' => 'privacy consent', 'services' => 'Services required',
            'preferred_date' => 'Preferred date', 'preferred_time' => 'Preferred time', 'meeting_mode' => 'Meeting mode',
        ]);
        $errors = $v->errors();
        if ($isQuick && !$divisions) {
            $errors['divisions'] = 'Select at least one service area.';
        }
        if ($type === 'consultation' && empty($errors['preferred_date']) && (int) date('N', strtotime($data['preferred_date'])) === 7) {
            $errors['preferred_date'] = 'We are closed on Sundays — please choose another date.';
        }
        // Only accept services that exist and are published.
        $serviceIds = [];
        if ($data['services']) {
            $ph = implode(',', array_fill(0, count($data['services']), '?'));
            $serviceIds = array_map('intval', array_column(db()->fetchAll("SELECT id FROM services WHERE is_published = 1 AND id IN ($ph)", array_values($data['services'])), 'id'));
            if (count($serviceIds) !== count(array_unique($data['services']))) {
                $errors['services'] = 'One or more selected services are invalid.';
            }
        }
        if ($errors) {
            return [false, ['message' => 'Please correct the highlighted fields.', 'errors' => $errors]];
        }

        // Duplicate protection: same person + type + content within 24h returns the original reference.
        $fingerprint = hash('sha256', implode('|', [$type, $data['email'], mb_strtolower($data['message']), implode(',', $serviceIds), $data['preferred_date']]));
        if ($dup = Enquiry::recentDuplicate($fingerprint)) {
            return [true, ['reference' => $dup['reference'], 'duplicate' => true, 'message' => 'We already received this request. Your reference number is ' . $dup['reference'] . '.']];
        }

        $reference = Enquiry::reference(['contact' => 'ENQ', 'consultation' => 'CON', 'proposal' => 'PRP', 'assessment' => 'SEC'][$type]);
        $id = db()->transaction(function ($db) use ($data, $type, $reference, $fingerprint, $serviceIds, $in) {
            $id = $db->insert('enquiries', [
                'reference'      => $reference,
                'type'           => $type,
                'full_name'      => $data['full_name'],
                'company'        => $data['company'],
                'email'          => $data['email'],
                'phone'          => $data['phone'],
                'industry'       => $data['industry'],
                'company_size'   => $data['company_size'],
                'message'        => $data['message'] !== '' ? $data['message'] : null,
                'preferred_date' => $type === 'consultation' ? $data['preferred_date'] : null,
                'preferred_time' => $type === 'consultation' ? $data['preferred_time'] : null,
                'meeting_mode'   => $type === 'consultation' ? $data['meeting_mode'] : null,
                'source_page'    => mb_substr((string) ($in['source_page'] ?? ''), 0, 255),
                'consent'        => 1,
                'fingerprint'    => $fingerprint,
                'ip_address'     => client_ip(),
                'user_agent'     => mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            ]);
            foreach ($serviceIds as $sid) {
                $db->query('INSERT INTO enquiry_services (enquiry_id, service_id) VALUES (?, ?)', [$id, $sid]);
            }
            return $id;
        });

        self::notify((int) $id);
        return [true, ['reference' => $reference, 'message' => 'Thank you, ' . $data['full_name'] . '. Your request has been received.']];
    }

    /** Sends internal notification and customer acknowledgement. Failures are logged, never shown to the user. */
    public static function notify(int $id): void
    {
        $enquiry = Enquiry::find($id);
        if (!$enquiry) {
            return;
        }
        $services = array_column(Enquiry::services($id), 'title');
        $mailer = new Mailer();
        $typeLabel = Enquiry::TYPES[$enquiry['type']];
        $to = setting('notify_email', config('mail.notify_to'));
        try {
            $mailer->send($to, "[$typeLabel] {$enquiry['reference']} — {$enquiry['company']}",
                Mailer::template('enquiry_admin', compact('enquiry', 'services', 'typeLabel')), $enquiry['email']);
            $mailer->send($enquiry['email'], "We've received your request — {$enquiry['reference']}",
                Mailer::template('enquiry_ack', compact('enquiry', 'services', 'typeLabel')));
        } catch (\Throwable $e) {
            Logger::error('Enquiry notification failed: ' . $e->getMessage());
        }
    }
}
