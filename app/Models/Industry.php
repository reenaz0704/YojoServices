<?php
namespace App\Models;

class Industry extends Model
{
    protected static $table = 'industries';

    public static function findPublished(string $slug): ?array
    {
        return db()->fetch('SELECT * FROM industries WHERE slug = ? AND is_published = 1', [$slug]);
    }

    public static function services(int $industryId): array
    {
        return db()->fetchAll('SELECT s.*, c.slug AS category_slug FROM services s JOIN industry_service i ON i.service_id = s.id JOIN service_categories c ON c.id = s.category_id WHERE i.industry_id = ? AND s.is_published = 1 ORDER BY s.title', [$industryId]);
    }
}
