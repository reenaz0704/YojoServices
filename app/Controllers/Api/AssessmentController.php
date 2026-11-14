<?php
namespace App\Controllers\Api;

use App\Core\Logger;
use App\Core\Validator;
use App\Models\Assessment;
use App\Services\AssessmentScorer;
use App\Services\EnquiryService;
use App\Services\Mailer;
use App\Services\SpamGuard;

/** Stores an Audit Readiness self-assessment and returns the indicative score. */
class AssessmentController
{
    public function store(): string
    {
        if ($spam = SpamGuard::check('assessments')) {
            json_response(['success' => false, 'message' => $spam], 429);
        }
        $data = [
            'organization_type' => trim((string) input('organization_type', '')),
            'industry'          => trim((string) input('industry', '')),
            'company_size'      => trim((string) input('company_size', '')),
            'country'           => trim((string) input('country', '')),
            'frameworks'        => is_array(input('frameworks')) ? input('frameworks') : [],
            'other_requirement' => trim((string) input('other_requirement', '')),
            'full_name'         => trim((string) input('full_name', '')),
            'company'           => trim((string) input('company', '')),
            'email'             => strtolower(trim((string) input('email', ''))),
            'phone'             => trim((string) input('phone', '')),
            'designation'       => trim((string) input('designation', '')),
            'consent'           => input('consent'),
        ];
        $answers = is_array(input('answers')) ? input('answers') : [];

        $v = Validator::make($data, [
            'organization_type' => 'required|in:Private company,Public listed company,Startup,Government / PSU,Non-profit,Other',
            'industry'          => 'required|max:120',
            'company_size'      => 'required|in:' . implode(',', EnquiryService::SIZES),
            'country'           => 'required|max:80',
            'frameworks'        => 'required|array',
            'other_requirement' => 'max:255',
            'full_name'         => 'required|min:2|max:120',
            'company'           => 'required|min:2|max:160',
            'email'             => 'required|email|max:190',
            'phone'             => 'phone',
            'designation'       => 'max:120',
            'consent'           => 'accepted',
        ], ['frameworks' => 'Assessment requirements', 'consent' => 'privacy consent', 'full_name' => 'Full name', 'email' => 'Business email']);
        $errors = $v->errors();
        $frameworks = array_values(array_intersect(array_keys(AssessmentScorer::FRAMEWORKS), array_map('strval', $data['frameworks'])));
        if (!$frameworks && empty($errors['frameworks'])) {
            $errors['frameworks'] = 'Select at least one requirement.';
        }
        $result = AssessmentScorer::score($answers);
        if ($result['missing']) {
            $errors['answers'] = 'Please answer every question in Step 3.';
        }
        if ($errors) {
            json_response(['success' => false, 'message' => 'Please correct the highlighted fields.', 'errors' => $errors], 422);
        }

        // Keep only known answer keys.
        $clean = [];
        foreach (AssessmentScorer::domains() as $d) {
            foreach ($d[1] as $k => $q) {
                $clean[$k] = $answers[$k];
            }
        }
        $reference = Assessment::reference('ARA');
        db()->transaction(function ($db) use ($data, $clean, $result, $reference, $frameworks) {
            $id = $db->insert('assessments', [
                'reference' => $reference,
                'organization_type' => $data['organization_type'],
                'industry' => $data['industry'],
                'company_size' => $data['company_size'],
                'country' => $data['country'],
                'other_requirement' => $data['other_requirement'] ?: null,
                'answers' => json_encode($clean),
                'domain_scores' => json_encode($result['domains']),
                'score' => $result['score'],
                'readiness_band' => $result['band'],
                'full_name' => $data['full_name'],
                'company' => $data['company'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?: null,
                'designation' => $data['designation'] ?: null,
                'consent' => 1,
                'ip_address' => client_ip(),
            ]);
            foreach ($frameworks as $f) {
                $db->query('INSERT INTO assessment_frameworks (assessment_id, framework) VALUES (?, ?)', [$id, $f]);
            }
        });

        try {
            $mailer = new Mailer();
            $labels = array_map(function ($f) { return AssessmentScorer::FRAMEWORKS[$f]; }, $frameworks);
            $vars = ['data' => $data, 'result' => $result, 'reference' => $reference, 'frameworks' => $labels];
            $mailer->send(setting('notify_email', config('mail.notify_to')), "[Readiness Assessment] $reference — {$data['company']} ({$result['score']}%)", Mailer::template('assessment_admin', $vars), $data['email']);
            $mailer->send($data['email'], "Your preliminary readiness summary — $reference", Mailer::template('assessment_ack', $vars));
        } catch (\Throwable $e) {
            Logger::error('Assessment mail failed: ' . $e->getMessage());
        }

        unset($result['missing']);
        json_response(['success' => true, 'reference' => $reference, 'result' => $result]);
        return '';
    }
}
