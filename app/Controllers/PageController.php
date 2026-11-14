<?php
namespace App\Controllers;

use App\Models\Service;

class PageController extends Controller
{
    public function about(): string
    {
        return $this->render('pages/about', [
            'meta_title' => 'Who We Are | ' . setting('company_name'),
            'meta_description' => setting('who_text'),
            'categories' => Service::categories(),
            'trust' => db()->fetchAll("SELECT * FROM trust_items WHERE is_published = 1 AND type <> 'framework' ORDER BY sort_order"),
        ]);
    }

    public function whoWeAre(): string
    {
        redirect('about', 301);
        return '';
    }

    public function framework(string $slug): string
    {
        $f = db()->fetch('SELECT f.*, s.slug AS service_slug, s.title AS service_title, c.slug AS category_slug FROM frameworks f LEFT JOIN services s ON s.id = f.service_id AND s.is_published = 1 LEFT JOIN service_categories c ON c.id = s.category_id WHERE f.slug = ? AND f.is_published = 1', [$slug]);
        if (!$f) {
            abort(404);
        }
        return $this->render('pages/framework', [
            'meta_title' => $f['name'] . ' | Frameworks | ' . setting('company_name'),
            'meta_description' => $f['overview'],
            'f' => $f,
            'others' => db()->fetchAll('SELECT slug, short_name, icon FROM frameworks WHERE is_published = 1 AND id <> ? ORDER BY sort_order', [(int) $f['id']]),
        ]);
    }

    public function faq(): string
    {
        $cats = db()->fetchAll('SELECT c.*, (SELECT COUNT(*) FROM faqs f WHERE f.category_id = c.id AND f.is_published = 1) AS faq_count FROM faq_categories c ORDER BY c.sort_order');
        return $this->render('pages/faq', [
            'meta_title' => 'Frequently Asked Questions | ' . setting('company_name'),
            'meta_description' => 'Answers about our audit, compliance, cybersecurity, cyber legal and IT consulting services and how engagements work.',
            'categories' => array_values(array_filter($cats, function ($c) { return (int) $c['faq_count'] > 0; })),
            'faqs' => db()->fetchAll('SELECT f.*, c.slug AS category_slug, c.name AS category_name FROM faqs f LEFT JOIN faq_categories c ON c.id = f.category_id WHERE f.is_published = 1 ORDER BY c.sort_order, f.sort_order, f.id'),
        ]);
    }

    public function legal(string $slug): string
    {
        $page = db()->fetch('SELECT * FROM pages WHERE slug = ? AND is_published = 1', [$slug]);
        if (!$page) {
            abort(404);
        }
        return $this->render('pages/legal', ['page' => $page, 'meta_title' => $page['title'] . ' | ' . setting('company_name')]);
    }

    public function sitemap(): string
    {
        header('Content-Type: application/xml; charset=utf-8');
        $urls = ['/', '/about', '/services', '/industries', '/insights', '/case-studies', '/faq', '/contact', '/book-consultation', '/security-assessment'];
        foreach (db()->fetchAll('SELECT slug FROM service_categories WHERE is_published = 1') as $c) { $urls[] = '/services/' . $c['slug']; }
        foreach (Service::allPublished() as $s) { $urls[] = '/services/' . $s['category_slug'] . '/' . $s['slug']; }
        foreach (db()->fetchAll('SELECT slug FROM industries WHERE is_published = 1') as $i) { $urls[] = '/industries/' . $i['slug']; }
        foreach (db()->fetchAll("SELECT slug FROM posts WHERE status = 'published'") as $p) { $urls[] = '/insights/' . $p['slug']; }
        foreach (db()->fetchAll('SELECT slug FROM frameworks WHERE is_published = 1') as $f) { $urls[] = '/frameworks/' . $f['slug']; }
        foreach (db()->fetchAll('SELECT slug FROM pages WHERE is_published = 1') as $p) { $urls[] = '/legal/' . $p['slug']; }
        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $u) {
            $xml .= '<url><loc>' . e(full_url($u)) . '</loc></url>';
        }
        return $xml . '</urlset>';
    }
}
