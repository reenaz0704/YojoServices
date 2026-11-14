<?php
namespace App\Controllers;

use App\Models\Service;
use App\Services\AssessmentScorer;
use App\Services\EnquiryService;

class ContactController extends Controller
{
    private function formData(string $type, array $extra = []): array
    {
        $preselect = [];
        if (!empty($_GET['service'])) {
            $s = db()->fetch('SELECT id FROM services WHERE slug = ? AND is_published = 1', [(string) $_GET['service']]);
            if ($s) {
                $preselect[] = (int) $s['id'];
            }
        }
        return array_merge([
            'formType'   => $type,
            'menu'       => Service::menu(),
            'industries' => EnquiryService::industries(),
            'sizes'      => EnquiryService::SIZES,
            'preselect'  => $preselect,
        ], $extra);
    }

    public function contact(): string
    {
        return $this->render('pages/contact', $this->formData('contact', [
            'meta_title' => 'Contact Us | ' . setting('company_name'),
            'meta_description' => 'Speak with our audit, compliance and cybersecurity consultants.',
        ]));
    }

    public function consultation(): string
    {
        return $this->render('pages/consultation', $this->formData('consultation', [
            'meta_title' => 'Book a Consultation | ' . setting('company_name'),
            'heading' => 'Book a Consultation',
            'intro' => 'Choose a convenient date and time. A consultant will confirm the slot by email within one business day.',
        ]));
    }

    public function proposal(): string
    {
        return $this->render('pages/consultation', $this->formData('proposal', [
            'meta_title' => 'Request a Proposal | ' . setting('company_name'),
            'heading' => 'Request a Proposal',
            'intro' => 'Tell us what you need. We\'ll review your requirements and respond with scoping questions or a tailored proposal.',
        ]));
    }

    public function assessment(): string
    {
        return $this->render('pages/assessment', [
            'meta_title' => 'Audit Readiness Assessment | ' . setting('company_name'),
            'meta_description' => 'A free, preliminary self-assessment of your audit and security readiness.',
            'domains' => AssessmentScorer::domains(),
            'frameworks' => AssessmentScorer::FRAMEWORKS,
            'bands' => AssessmentScorer::BANDS,
            'industries' => EnquiryService::industries(),
            'sizes' => EnquiryService::SIZES,
        ]);
    }
}
