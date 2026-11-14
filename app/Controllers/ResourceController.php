<?php
namespace App\Controllers;

use App\Models\Post;

/** Latest Insights (articles). The former Resources hub redirects here. */
class ResourceController extends Controller
{
    const PER_PAGE = 9;

    public function index(): string
    {
        $sorts = ['newest' => 'Newest first', 'oldest' => 'Oldest first', 'popular' => 'Most read', 'title' => 'Title A–Z'];
        $filters = [
            'q'        => mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 100),
            'category' => preg_replace('/[^a-z0-9\-]/', '', (string) ($_GET['category'] ?? '')),
            'tag'      => preg_replace('/[^a-z0-9\-]/', '', (string) ($_GET['tag'] ?? '')),
            'period'   => preg_match('/^\d{4}(-\d{2})?$/', (string) ($_GET['period'] ?? '')) ? (string) $_GET['period'] : '',
            'sort'     => isset($sorts[$_GET['sort'] ?? '']) ? (string) $_GET['sort'] : 'newest',
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        list($posts, $total) = Post::search($filters, $page, self::PER_PAGE);
        return $this->render('pages/resources', [
            'meta_title' => 'Latest Insights | ' . setting('company_name'),
            'meta_description' => setting('insights_text', 'Cybersecurity, audit, risk and regulatory insights.'),
            'posts' => $posts,
            'total' => $total,
            'page' => $page,
            'pages' => (int) ceil($total / self::PER_PAGE),
            'filters' => $filters,
            'sorts' => $sorts,
            'categories' => Post::categoriesWithCounts(),
            'tags' => Post::popularTags(),
            'periods' => Post::periods(),
        ]);
    }

    public function show(string $slug): string
    {
        $post = Post::findPublished($slug);
        if (!$post) {
            abort(404);
        }
        db()->query('UPDATE posts SET views = views + 1 WHERE id = ?', [(int) $post['id']]);
        return $this->render('pages/post', [
            'meta_title' => $post['meta_title'] ?: $post['title'],
            'meta_description' => $post['meta_description'] ?: $post['excerpt'],
            'og_image' => $post['thumbnail'] ? media_url($post['thumbnail']) : null,
            'og_type' => 'article',
            'post' => $post,
            'tags' => Post::tags((int) $post['id']),
            'related' => Post::related($post),
        ]);
    }

    public function legacyIndex(): string
    {
        $qs = $_SERVER['QUERY_STRING'] ?? '';
        redirect('insights' . ($qs !== '' ? '?' . $qs : ''), 301);
        return '';
    }

    public function legacyShow(string $slug): string
    {
        redirect('insights/' . rawurlencode($slug), 301);
        return '';
    }
}
