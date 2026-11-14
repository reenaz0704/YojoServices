<?php
namespace App\Models;

class Service extends Model
{
    protected static $table = 'services';

    public static function categories(): array
    {
        return db()->fetchAll('SELECT c.*, (SELECT COUNT(*) FROM services s WHERE s.category_id = c.id AND s.is_published = 1) AS service_count FROM service_categories c WHERE c.is_published = 1 ORDER BY c.sort_order');
    }

    public static function category(string $slug): ?array
    {
        return db()->fetch('SELECT * FROM service_categories WHERE slug = ? AND is_published = 1', [$slug]);
    }

    public static function inCategory(int $categoryId): array
    {
        return db()->fetchAll('SELECT * FROM services WHERE category_id = ? AND is_published = 1 ORDER BY sort_order, title', [$categoryId]);
    }

    public static function allPublished(): array
    {
        return db()->fetchAll('SELECT s.id, s.slug, s.title, s.summary, s.icon, s.menu_group, s.engagement_type, c.slug AS category_slug, c.name AS category_name FROM services s JOIN service_categories c ON c.id = s.category_id WHERE s.is_published = 1 AND c.is_published = 1 ORDER BY c.sort_order, s.sort_order');
    }

    /** Divisions with their published services — shared by the mega-menu, footer and Services page. */
    public static function menu(): array
    {
        static $menu = null;
        if ($menu === null) {
            $menu = [];
            try {
                foreach (self::categories() as $c) {
                    $menu[$c['slug']] = ['label' => $c['name'], 'icon' => $c['icon'], 'slug' => $c['slug'], 'tagline' => $c['tagline'], 'items' => []];
                }
                foreach (self::allPublished() as $s) {
                    if (isset($menu[$s['category_slug']])) {
                        $menu[$s['category_slug']]['items'][] = $s;
                    }
                }
            } catch (\Throwable $e) {
            }
        }
        return $menu;
    }

    /** Find a published service by slug alone (used to redirect old URLs). */
    public static function findBySlug(string $slug): ?array
    {
        return db()->fetch('SELECT s.slug, c.slug AS category_slug FROM services s JOIN service_categories c ON c.id = s.category_id WHERE s.slug = ? AND s.is_published = 1 AND c.is_published = 1', [$slug]);
    }

    public static function findPublished(string $categorySlug, string $slug): ?array
    {
        return db()->fetch(
            'SELECT s.*, c.slug AS category_slug, c.name AS category_name, c.page_type, c.engagement_note FROM services s JOIN service_categories c ON c.id = s.category_id WHERE s.slug = ? AND c.slug = ? AND s.is_published = 1 AND c.is_published = 1',
            [$slug, $categorySlug]
        );
    }

    public static function faqs(int $serviceId): array
    {
        return db()->fetchAll('SELECT * FROM service_faqs WHERE service_id = ? ORDER BY sort_order, id', [$serviceId]);
    }

    public static function url(array $s): string
    {
        return url('services/' . $s['category_slug'] . '/' . $s['slug']);
    }
}
