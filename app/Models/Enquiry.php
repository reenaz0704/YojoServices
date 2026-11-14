<?php
namespace App\Models;

class Enquiry extends Model
{
    protected static $table = 'enquiries';

    const STATUSES = ['new' => 'New', 'in_review' => 'In review', 'contacted' => 'Contacted', 'proposal_sent' => 'Proposal sent', 'won' => 'Won', 'closed' => 'Closed', 'spam' => 'Spam'];
    const TYPES = ['contact' => 'General enquiry', 'consultation' => 'Consultation booking', 'proposal' => 'Proposal request', 'assessment' => 'Security assessment request'];

    public static function services(int $id): array
    {
        return db()->fetchAll('SELECT s.id, s.title FROM services s JOIN enquiry_services es ON es.service_id = s.id WHERE es.enquiry_id = ? ORDER BY s.title', [$id]);
    }

    public static function notes(int $id): array
    {
        return db()->fetchAll('SELECT n.*, u.name AS author FROM enquiry_notes n LEFT JOIN users u ON u.id = n.user_id WHERE n.enquiry_id = ? ORDER BY n.created_at DESC', [$id]);
    }

    /** Recent duplicate (same fingerprint within 24h) — returns existing reference. */
    public static function recentDuplicate(string $fingerprint): ?array
    {
        return db()->fetch('SELECT reference FROM enquiries WHERE fingerprint = ? AND created_at > (NOW() - INTERVAL 24 HOUR) LIMIT 1', [$fingerprint]);
    }
}
