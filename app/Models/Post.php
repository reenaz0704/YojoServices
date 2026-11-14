<?php
namespace App\Models;

class Post extends Model
{
    protected static $table = 'posts';

    const SELECT = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug, u.name AS author_name, u.designation AS author_designation, u.bio AS author_bio
        FROM posts p JOIN post_categories c ON c.id = p.category_id LEFT JOIN users u ON u.id = p.author_id';

    public static function latest(int $limit = 3): array
    {
        return db()->fetchAll(self::SELECT . " WHERE p.status = 'published' AND p.published_at <= NOW() ORDER BY p.published_at DESC LIMIT " . (int) $limit);
    }

    /** Filtered, paginated listing. Returns [rows, total]. */
    public static function search(array $f, int $page, int $perPage): array
    {
        $where = ["p.status = 'published'", 'p.published_at <= NOW()'];
        $params = [];
        if (!empty($f['q'])) {
            $where[] = '(p.title LIKE :q OR p.excerpt LIKE :q2 OR p.content LIKE :q3)';
            $params['q'] = $params['q2'] = $params['q3'] = '%' . $f['q'] . '%';
        }
        if (!empty($f['category'])) {
            $where[] = 'c.slug = :cat';
            $params['cat'] = $f['category'];
        }
        if (!empty($f['tag'])) {
            $where[] = 'EXISTS (SELECT 1 FROM post_tag pt JOIN tags t ON t.id = pt.tag_id WHERE pt.post_id = p.id AND t.slug = :tag)';
            $params['tag'] = $f['tag'];
        }
        if (!empty($f['period'])) {
            $where[] = (strlen($f['period']) === 4 ? "DATE_FORMAT(p.published_at, '%Y')" : "DATE_FORMAT(p.published_at, '%Y-%m')") . ' = :period';
            $params['period'] = $f['period'];
        }
        $orders = ['newest' => 'p.published_at DESC', 'oldest' => 'p.published_at ASC', 'popular' => 'p.views DESC, p.published_at DESC', 'title' => 'p.title ASC'];
        $order = $orders[$f['sort'] ?? 'newest'] ?? $orders['newest'];
        $w = implode(' AND ', $where);
        $total = (int) db()->value("SELECT COUNT(*) FROM posts p JOIN post_categories c ON c.id = p.category_id WHERE $w", $params);
        $offset = max(0, ($page - 1) * $perPage);
        $rows = db()->fetchAll(self::SELECT . " WHERE $w ORDER BY $order LIMIT $perPage OFFSET $offset", $params);
        return [$rows, $total];
    }

    /** Months that have published articles, for the date filter. */
    public static function periods(): array
    {
        return db()->fetchAll("SELECT DATE_FORMAT(published_at, '%Y-%m') AS ym, COUNT(*) AS n FROM posts WHERE status = 'published' AND published_at <= NOW() GROUP BY ym ORDER BY ym DESC");
    }

    public static function findPublished(string $slug): ?array
    {
        return db()->fetch(self::SELECT . " WHERE p.slug = ? AND p.status = 'published' AND p.published_at <= NOW()", [$slug]);
    }

    public static function tags(int $postId): array
    {
        return db()->fetchAll('SELECT t.* FROM tags t JOIN post_tag pt ON pt.tag_id = t.id WHERE pt.post_id = ? ORDER BY t.name', [$postId]);
    }

    public static function related(array $post, int $limit = 3): array
    {
        return db()->fetchAll(self::SELECT . " WHERE p.status = 'published' AND p.published_at <= NOW() AND p.id <> ? ORDER BY (p.category_id = ?) DESC, p.published_at DESC LIMIT " . (int) $limit, [(int) $post['id'], (int) $post['category_id']]);
    }

    public static function categoriesWithCounts(): array
    {
        return db()->fetchAll("SELECT c.*, (SELECT COUNT(*) FROM posts p WHERE p.category_id = c.id AND p.status = 'published') AS post_count FROM post_categories c ORDER BY c.sort_order");
    }

    public static function popularTags(int $limit = 20): array
    {
        return db()->fetchAll("SELECT t.*, COUNT(*) AS uses FROM tags t JOIN post_tag pt ON pt.tag_id = t.id JOIN posts p ON p.id = pt.post_id AND p.status = 'published' GROUP BY t.id ORDER BY uses DESC, t.name LIMIT " . (int) $limit);
    }

    /** Replace a post's tags from a comma-separated string. */
    public static function syncTags(int $postId, string $csv): void
    {
        db()->query('DELETE FROM post_tag WHERE post_id = ?', [$postId]);
        foreach (array_unique(array_filter(array_map('trim', explode(',', $csv)))) as $name) {
            $slug = slugify($name);
            $tag = db()->fetch('SELECT id FROM tags WHERE slug = ?', [$slug]);
            $tagId = $tag ? (int) $tag['id'] : db()->insert('tags', ['slug' => $slug, 'name' => mb_substr($name, 0, 80)]);
            db()->query('INSERT IGNORE INTO post_tag (post_id, tag_id) VALUES (?, ?)', [$postId, $tagId]);
        }
    }
}
