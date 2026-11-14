<?php
namespace App\Controllers\Api;

use App\Models\Service;

/** Read-only JSON content endpoints. */
class ContentController
{
    public function faqs(): string
    {
        $q = mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 100);
        $params = [];
        $sql = 'SELECT id, question, answer, topic FROM faqs WHERE is_published = 1';
        if ($q !== '') {
            $sql .= ' AND (question LIKE ? OR answer LIKE ?)';
            $params = ['%' . $q . '%', '%' . $q . '%'];
        }
        json_response(['success' => true, 'data' => db()->fetchAll($sql . ' ORDER BY sort_order', $params)]);
        return '';
    }

    public function services(): string
    {
        $rows = array_map(function ($s) {
            return ['id' => (int) $s['id'], 'title' => $s['title'], 'group' => $s['category_name'], 'url' => Service::url($s)];
        }, Service::allPublished());
        json_response(['success' => true, 'data' => $rows]);
        return '';
    }

    /** Site-wide quick search across services, industries and articles. */
    public function search(): string
    {
        $q = mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 80);
        if (mb_strlen($q) < 2) {
            json_response(['success' => true, 'data' => []]);
        }
        $like = '%' . $q . '%';
        $out = [];
        foreach (db()->fetchAll('SELECT s.title, s.slug, c.slug AS category_slug FROM services s JOIN service_categories c ON c.id = s.category_id WHERE s.is_published = 1 AND (s.title LIKE ? OR s.summary LIKE ?) LIMIT 6', [$like, $like]) as $s) {
            $out[] = ['type' => 'Service', 'title' => $s['title'], 'url' => Service::url($s)];
        }
        foreach (db()->fetchAll('SELECT name, slug FROM industries WHERE is_published = 1 AND name LIKE ? LIMIT 3', [$like]) as $i) {
            $out[] = ['type' => 'Industry', 'title' => $i['name'], 'url' => url('industries/' . $i['slug'])];
        }
        foreach (db()->fetchAll("SELECT title, slug FROM posts WHERE status = 'published' AND (title LIKE ? OR excerpt LIKE ?) LIMIT 4", [$like, $like]) as $p) {
            $out[] = ['type' => 'Article', 'title' => $p['title'], 'url' => url('insights/' . $p['slug'])];
        }
        json_response(['success' => true, 'data' => $out]);
        return '';
    }
}
