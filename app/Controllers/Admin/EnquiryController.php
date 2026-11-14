<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Models\Enquiry;

class EnquiryController
{
    private function filters(): array
    {
        $where = ['1'];
        $params = [];
        $f = [
            'q' => mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 100),
            'status' => (string) ($_GET['status'] ?? ''),
            'type' => (string) ($_GET['type'] ?? ''),
        ];
        if ($f['q'] !== '') {
            $where[] = '(e.reference LIKE :q OR e.full_name LIKE :q2 OR e.company LIKE :q3 OR e.email LIKE :q4)';
            $params['q'] = $params['q2'] = $params['q3'] = $params['q4'] = '%' . $f['q'] . '%';
        }
        if (isset(Enquiry::STATUSES[$f['status']])) {
            $where[] = 'e.status = :status';
            $params['status'] = $f['status'];
        }
        if (isset(Enquiry::TYPES[$f['type']])) {
            $where[] = 'e.type = :type';
            $params['type'] = $f['type'];
        }
        return [$f, implode(' AND ', $where), $params];
    }

    public function index(): string
    {
        list($f, $where, $params) = $this->filters();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $total = (int) db()->value("SELECT COUNT(*) FROM enquiries e WHERE $where", $params);
        $rows = db()->fetchAll("SELECT e.*, (SELECT GROUP_CONCAT(s.title SEPARATOR ', ') FROM enquiry_services es JOIN services s ON s.id = es.service_id WHERE es.enquiry_id = e.id) AS services
            FROM enquiries e WHERE $where ORDER BY e.created_at DESC LIMIT 25 OFFSET " . (($page - 1) * 25), $params);
        $counts = array_column(db()->fetchAll('SELECT status, COUNT(*) c FROM enquiries GROUP BY status'), 'c', 'status');
        return view('admin/enquiries/index', ['title' => 'Enquiries', 'rows' => $rows, 'f' => $f, 'page' => $page, 'pages' => (int) ceil($total / 25), 'total' => $total, 'counts' => $counts], 'layouts/admin');
    }

    public function show(string $id): string
    {
        $e = Enquiry::find((int) $id);
        if (!$e) {
            abort(404);
        }
        return view('admin/enquiries/show', [
            'title' => 'Enquiry ' . $e['reference'], 'e' => $e,
            'services' => Enquiry::services((int) $id), 'notes' => Enquiry::notes((int) $id),
            'users' => db()->fetchAll('SELECT id, name FROM users WHERE is_active = 1 ORDER BY name'),
            'history' => db()->fetchAll('SELECT id, reference, type, created_at, status FROM enquiries WHERE email = ? AND id <> ? ORDER BY created_at DESC LIMIT 5', [$e['email'], (int) $id]),
        ], 'layouts/admin');
    }

    public function update(string $id): string
    {
        $id = (int) $id;
        $e = Enquiry::find($id);
        if (!$e) {
            abort(404);
        }
        $status = (string) input('status', $e['status']);
        $assigned = (int) input('assigned_to', 0);
        $data = [];
        if (isset(Enquiry::STATUSES[$status])) {
            $data['status'] = $status;
        }
        $data['assigned_to'] = $assigned && db()->value('SELECT id FROM users WHERE id = ?', [$assigned]) ? $assigned : null;
        Enquiry::update($id, $data);
        $note = trim((string) input('note', ''));
        if ($note !== '') {
            db()->insert('enquiry_notes', ['enquiry_id' => $id, 'user_id' => Auth::user()['id'], 'note' => mb_substr($note, 0, 5000)]);
        }
        if ($status !== $e['status']) {
            db()->insert('enquiry_notes', ['enquiry_id' => $id, 'user_id' => Auth::user()['id'], 'note' => 'Status changed: ' . Enquiry::STATUSES[$e['status']] . ' → ' . (Enquiry::STATUSES[$status] ?? $status)]);
        }
        Auth::log('enquiry_update', 'enquiries', $id, $status);
        flash('success', 'Enquiry updated.');
        redirect('admin/enquiries/' . $id);
        return '';
    }

    public function destroy(string $id): string
    {
        Enquiry::delete((int) $id);
        Auth::log('delete', 'enquiries', (int) $id);
        flash('success', 'Enquiry deleted.');
        redirect('admin/enquiries');
        return '';
    }

    /** CSV export of the current filter (formula-injection safe). */
    public function export(): string
    {
        list($f, $where, $params) = $this->filters();
        $rows = db()->fetchAll("SELECT e.reference, e.type, e.status, e.full_name, e.company, e.email, e.phone, e.industry, e.company_size, e.preferred_date, e.preferred_time, e.meeting_mode, e.message, e.created_at,
            (SELECT GROUP_CONCAT(s.title SEPARATOR '; ') FROM enquiry_services es JOIN services s ON s.id = es.service_id WHERE es.enquiry_id = e.id) AS services
            FROM enquiries e WHERE $where ORDER BY e.created_at DESC", $params);
        Auth::log('export', 'enquiries', 0, count($rows) . ' rows');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="enquiries-' . date('Ymd-His') . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $rows ? array_keys($rows[0]) : ['reference']);
        foreach ($rows as $row) {
            fputcsv($out, array_map(function ($v) {
                $v = (string) $v;
                return preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v;
            }, $row));
        }
        fclose($out);
        exit;
    }
}
