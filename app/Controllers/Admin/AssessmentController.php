<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Models\Assessment;
use App\Services\AssessmentScorer;

class AssessmentController
{
    public function index(): string
    {
        $q = mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 100);
        $status = (string) ($_GET['status'] ?? '');
        $where = ['1'];
        $params = [];
        if ($q !== '') {
            $where[] = '(reference LIKE :q OR company LIKE :q2 OR email LIKE :q3)';
            $params['q'] = $params['q2'] = $params['q3'] = '%' . $q . '%';
        }
        if (isset(Assessment::STATUSES[$status])) {
            $where[] = 'status = :s';
            $params['s'] = $status;
        }
        $rows = db()->fetchAll('SELECT a.*, (SELECT GROUP_CONCAT(framework) FROM assessment_frameworks f WHERE f.assessment_id = a.id) AS fw FROM assessments a WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC LIMIT 200', $params);
        return view('admin/assessments/index', ['title' => 'Readiness assessments', 'rows' => $rows, 'q' => $q, 'status' => $status], 'layouts/admin');
    }

    public function show(string $id): string
    {
        $a = Assessment::find((int) $id);
        if (!$a) {
            abort(404);
        }
        return view('admin/assessments/show', [
            'title' => 'Assessment ' . $a['reference'], 'a' => $a,
            'frameworks' => array_map(function ($f) { return AssessmentScorer::FRAMEWORKS[$f] ?? $f; }, Assessment::frameworks((int) $id)),
            'answers' => json_decode($a['answers'], true) ?: [],
            'domainScores' => json_decode($a['domain_scores'], true) ?: [],
            'domains' => AssessmentScorer::domains(),
        ], 'layouts/admin');
    }

    public function update(string $id): string
    {
        $status = (string) input('status');
        $data = ['reviewer_notes' => mb_substr(trim((string) input('reviewer_notes', '')), 0, 5000)];
        if (isset(Assessment::STATUSES[$status])) {
            $data['status'] = $status;
        }
        Assessment::update((int) $id, $data);
        Auth::log('assessment_update', 'assessments', (int) $id, $status);
        flash('success', 'Assessment updated.');
        redirect('admin/assessments/' . (int) $id);
        return '';
    }
}
