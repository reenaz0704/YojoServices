<?php
namespace App\Models;

/**
 * Base model with common table operations. Subclasses set static $table.
 */
abstract class Model
{
    protected static $table = '';

    public static function find(int $id): ?array
    {
        return db()->fetch('SELECT * FROM `' . static::$table . '` WHERE id = ?', [$id]);
    }

    public static function findBy(string $column, $value): ?array
    {
        $column = preg_replace('/[^a-z_]/', '', $column);
        return db()->fetch('SELECT * FROM `' . static::$table . "` WHERE `$column` = ? LIMIT 1", [$value]);
    }

    public static function create(array $data): int
    {
        return db()->insert(static::$table, $data);
    }

    public static function update(int $id, array $data): void
    {
        db()->update(static::$table, $data, $id);
    }

    public static function delete(int $id): void
    {
        db()->delete(static::$table, $id);
    }

    public static function count(string $where = '1', array $params = []): int
    {
        return (int) db()->value('SELECT COUNT(*) FROM `' . static::$table . "` WHERE $where", $params);
    }

    /** Published rows ordered by sort_order. */
    public static function published(int $limit = 0): array
    {
        $sql = 'SELECT * FROM `' . static::$table . '` WHERE is_published = 1 ORDER BY sort_order, id';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }
        return db()->fetchAll($sql);
    }

    /** Generates a unique human-friendly reference, e.g. YJ-ENQ-251110-4F7K. */
    public static function reference(string $prefix): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $rand = '';
            for ($i = 0; $i < 4; $i++) {
                $rand .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $ref = 'YJ-' . $prefix . '-' . date('ymd') . '-' . $rand;
        } while (static::findBy('reference', $ref));
        return $ref;
    }
}
