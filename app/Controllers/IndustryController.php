<?php
namespace App\Controllers;

use App\Models\Industry;

class IndustryController extends Controller
{
    public function index(): string
    {
        $industries = Industry::published();
        foreach ($industries as &$i) {
            $i['services'] = Industry::services((int) $i['id']);
        }
        return $this->render('pages/industries', [
            'meta_title' => 'Industries | ' . setting('company_name'),
            'meta_description' => 'Sector-specific security, audit and compliance programmes.',
            'industries' => $industries,
        ]);
    }

    public function show(string $slug): string
    {
        $industry = Industry::findPublished($slug);
        if (!$industry) {
            abort(404);
        }
        return $this->render('pages/industry', [
            'meta_title' => $industry['name'] . ' | Industries | ' . setting('company_name'),
            'meta_description' => $industry['summary'],
            'industry' => $industry,
            'services' => Industry::services((int) $industry['id']),
            'caseStudies' => db()->fetchAll('SELECT * FROM case_studies WHERE industry_id = ? AND is_published = 1', [(int) $industry['id']]),
            'others' => db()->fetchAll('SELECT slug, name, icon FROM industries WHERE is_published = 1 AND id <> ? ORDER BY sort_order', [(int) $industry['id']]),
        ]);
    }
}
