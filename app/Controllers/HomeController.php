<?php
namespace App\Controllers;

use App\Models\Post;
use App\Models\Service;

class HomeController extends Controller
{
    public function index(): string
    {
        $db = db();
        return $this->render('pages/home', [
            'meta_title'   => setting('seo_title', 'Yojo Solutions'),
            'slides'       => $db->fetchAll('SELECT * FROM hero_slides WHERE is_published = 1 ORDER BY sort_order, id'),
            'frameworks'   => $db->fetchAll('SELECT f.*, s.slug AS service_slug, s.title AS service_title, c.slug AS category_slug FROM frameworks f LEFT JOIN services s ON s.id = f.service_id AND s.is_published = 1 LEFT JOIN service_categories c ON c.id = s.category_id WHERE f.is_published = 1 ORDER BY f.sort_order, f.id'),
            'categories'   => Service::categories(),
            'trust'        => $db->fetchAll("SELECT * FROM trust_items WHERE is_published = 1 AND type <> 'framework' ORDER BY FIELD(type, 'certification','association','partner','client'), sort_order"),
            'stats'        => $db->fetchAll('SELECT * FROM stats WHERE is_published = 1 ORDER BY sort_order LIMIT 4'),
            'industries'   => $db->fetchAll('SELECT * FROM industries WHERE is_published = 1 ORDER BY sort_order'),
            'caseStudies'  => $db->fetchAll('SELECT cs.*, i.name AS industry_name FROM case_studies cs LEFT JOIN industries i ON i.id = cs.industry_id WHERE cs.is_published = 1 ORDER BY cs.sort_order LIMIT 3'),
            'testimonials' => $db->fetchAll('SELECT * FROM testimonials WHERE is_published = 1 ORDER BY RAND() LIMIT 8'),
            'posts'        => Post::latest(5),
            'faqs'         => $db->fetchAll('SELECT f.*, c.slug AS category_slug FROM faqs f LEFT JOIN faq_categories c ON c.id = f.category_id WHERE f.is_published = 1 ORDER BY c.sort_order, f.sort_order LIMIT 6'),
        ]);
    }
}
