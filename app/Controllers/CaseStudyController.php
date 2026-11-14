<?php
namespace App\Controllers;

class CaseStudyController extends Controller
{
    const SQL = 'SELECT cs.*, i.name AS industry_name, i.slug AS industry_slug FROM case_studies cs LEFT JOIN industries i ON i.id = cs.industry_id';

    public function index(): string
    {
        return $this->render('pages/case-studies', [
            'meta_title' => 'Case Studies | ' . setting('company_name'),
            'caseStudies' => db()->fetchAll(self::SQL . ' WHERE cs.is_published = 1 ORDER BY cs.sort_order'),
        ]);
    }

    public function show(string $slug): string
    {
        $cs = db()->fetch(self::SQL . ' WHERE cs.slug = ? AND cs.is_published = 1', [$slug]);
        if (!$cs) {
            abort(404);
        }
        return $this->render('pages/case-study', ['meta_title' => $cs['title'] . ' | Case Study', 'cs' => $cs]);
    }
}
