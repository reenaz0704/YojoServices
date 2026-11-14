<?php
namespace App\Controllers\Admin;

class DashboardController
{
    public function index(): string
    {
        $db = db();
        $kpi = [
            'new_enquiries' => (int) $db->value("SELECT COUNT(*) FROM enquiries WHERE status = 'new'"),
            'enquiries_30' => (int) $db->value('SELECT COUNT(*) FROM enquiries WHERE created_at > NOW() - INTERVAL 30 DAY'),
            'consultations' => (int) $db->value("SELECT COUNT(*) FROM enquiries WHERE type = 'consultation' AND preferred_date >= CURDATE() AND status NOT IN ('closed','spam')"),
            'assessments' => (int) $db->value("SELECT COUNT(*) FROM assessments WHERE status = 'new'"),
            'avg_score' => (int) $db->value('SELECT ROUND(AVG(score)) FROM assessments'),
            'posts' => (int) $db->value("SELECT COUNT(*) FROM posts WHERE status = 'published'"),
            'services' => (int) $db->value('SELECT COUNT(*) FROM services WHERE is_published = 1'),
        ];
        // Enquiries per day, last 14 days
        $rows = $db->fetchAll('SELECT DATE(created_at) d, COUNT(*) c FROM enquiries WHERE created_at > CURDATE() - INTERVAL 13 DAY GROUP BY DATE(created_at)');
        $map = array_column($rows, 'c', 'd');
        $trend = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $trend[] = ['d' => date('d M', strtotime($d)), 'c' => (int) ($map[$d] ?? 0)];
        }
        return view('admin/dashboard', [
            'title' => 'Dashboard',
            'kpi' => $kpi,
            'trend' => $trend,
            'byType' => $db->fetchAll('SELECT type, COUNT(*) c FROM enquiries GROUP BY type'),
            'topServices' => $db->fetchAll('SELECT s.title, COUNT(*) c FROM enquiry_services es JOIN services s ON s.id = es.service_id GROUP BY s.id ORDER BY c DESC LIMIT 6'),
            'recent' => $db->fetchAll('SELECT id, reference, type, full_name, company, status, created_at FROM enquiries ORDER BY created_at DESC LIMIT 8'),
            'upcoming' => $db->fetchAll("SELECT id, reference, full_name, company, preferred_date, preferred_time, meeting_mode FROM enquiries WHERE type = 'consultation' AND preferred_date >= CURDATE() AND status NOT IN ('closed','spam') ORDER BY preferred_date, preferred_time LIMIT 5"),
            'recentAssess' => $db->fetchAll('SELECT id, reference, company, score, readiness_band, created_at FROM assessments ORDER BY created_at DESC LIMIT 5'),
        ], 'layouts/admin');
    }

    public function activity(): string
    {
        return view('admin/activity', [
            'title' => 'Activity log',
            'rows' => db()->fetchAll('SELECT a.*, u.name FROM activity_log a LEFT JOIN users u ON u.id = a.user_id ORDER BY a.created_at DESC LIMIT 200'),
        ], 'layouts/admin');
    }
}
