<?php
namespace App\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index(): string
    {
        $categories = Service::categories();
        foreach ($categories as &$c) {
            $c['services'] = Service::inCategory((int) $c['id']);
        }
        return $this->render('pages/services', [
            'meta_title' => 'Our Audit, Cybersecurity & Consulting Services | ' . setting('company_name'),
            'meta_description' => 'Comprehensive security, audit, compliance and technology consulting solutions designed to help organizations manage risks and strengthen digital trust.',
            'categories' => $categories,
        ]);
    }

    public function category(string $slug): string
    {
        $category = Service::category($slug);
        if (!$category) {
            // Former category URLs -> new divisions
            $legacy = ['governance-risk-compliance' => 'grc', 'cybersecurity-audit-vapt' => 'offensive-security',
                'it-audit-assurance' => 'grc', 'data-privacy-risk-advisory' => 'grc',
                'iso-audit-compliance' => 'grc', 'it-consulting' => 'grc', 'cybersecurity-audit' => 'offensive-security',
                'network-security-assessment' => 'offensive-security', 'red-teaming' => 'offensive-security',
                'cybersecurity-services' => 'defensive-security', 'cyber-legal-services' => 'defensive-security', 'security-operation-center' => 'defensive-security'];
            if (isset($legacy[$slug])) {
                redirect('services/' . $legacy[$slug], 301);
            }
            abort(404);
        }
        return $this->render('pages/service-category', [
            'meta_title' => $category['name'] . ' | ' . setting('company_name'),
            'meta_description' => $category['summary'],
            'category' => $category,
            'services' => Service::inCategory((int) $category['id']),
            'categories' => Service::categories(),
        ]);
    }

    public function show(string $categorySlug, string $slug): string
    {
        $service = Service::findPublished($categorySlug, $slug);
        if (!$service) {
            $merged = ['red-team-external-penetration-testing' => 'offensive-security/external-penetration-testing',
                'soc-incident-response' => 'defensive-security/incident-response-advisory'];
            if (isset($merged[$slug])) {
                redirect('services/' . $merged[$slug], 301);
            }
            $moved = Service::findBySlug($slug); // service moved to another division
            if ($moved) {
                redirect('services/' . $moved['category_slug'] . '/' . $moved['slug'], 301);
            }
            redirect('services/' . $categorySlug, 302); // retired service -> its division (or 404)
        }
        $siblings = array_values(array_filter(Service::inCategory((int) $service['category_id']), function ($s) use ($service) {
            return (int) $s['id'] !== (int) $service['id'];
        }));
        return $this->render('pages/service', [
            'meta_title' => $service['meta_title'] ?: $service['title'],
            'meta_description' => $service['meta_description'] ?: $service['summary'],
            'service' => $service,
            'faqs' => Service::faqs((int) $service['id']),
            'siblings' => array_slice($siblings, 0, 6),
            'industries' => db()->fetchAll('SELECT i.slug, i.name, i.icon FROM industries i JOIN industry_service x ON x.industry_id = i.id WHERE x.service_id = ? AND i.is_published = 1', [(int) $service['id']]),
        ]);
    }
}
