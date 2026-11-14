<?php
namespace App\Models;

class Assessment extends Model
{
    protected static $table = 'assessments';

    const STATUSES = ['new' => 'New', 'reviewed' => 'Reviewed', 'contacted' => 'Contacted', 'closed' => 'Closed'];

    public static function frameworks(int $id): array
    {
        return array_column(db()->fetchAll('SELECT framework FROM assessment_frameworks WHERE assessment_id = ?', [$id]), 'framework');
    }
}
