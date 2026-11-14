<?php use App\Models\Assessment; ?>
<div class="adm-flash adm-flash--info"><?= icon('info', 'icon icon--sm') ?>Self-assessment results are preliminary and based only on the respondent's answers — validate with evidence before advising.</div>
<section class="panel">
    <header class="panel__head panel__head--tools">
        <form class="adm-search" method="get"><?= icon('search', 'icon icon--sm') ?><input type="search" name="q" value="<?= e($q) ?>" placeholder="Reference, company, email…">
            <select name="status" onchange="this.form.submit()"><option value="">All statuses</option><?php foreach (Assessment::STATUSES as $k => $l): ?><option value="<?= $k ?>" <?= $status === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></form>
        <span class="muted"><?= count($rows) ?> shown</span>
    </header>
    <div class="table-wrap"><table class="tbl">
        <thead><tr><th>Score</th><th>Reference</th><th>Organisation</th><th>Requirements</th><th>Status</th><th>Submitted</th></tr></thead>
        <tbody>
        <?php if (!$rows): ?><tr><td colspan="6" class="muted">No assessments yet.</td></tr><?php endif; ?>
        <?php foreach ($rows as $a): ?>
        <tr class="<?= $a['status'] === 'new' ? 'is-unread' : '' ?>">
            <td><span class="score-pill" style="--s:<?= (int) $a['score'] ?>"><?= (int) $a['score'] ?></span></td>
            <td><a class="mono" href="<?= url('admin/assessments/' . $a['id']) ?>"><?= e($a['reference']) ?></a><small><?= e($a['readiness_band']) ?></small></td>
            <td><strong><?= e($a['company']) ?></strong><small><?= e($a['full_name']) ?> · <?= e($a['industry']) ?> · <?= e($a['country']) ?></small></td>
            <td><small><?= e(str_replace(',', ', ', (string) $a['fw'])) ?></small></td>
            <td><span class="status status--<?= e($a['status']) ?>"><?= e(Assessment::STATUSES[$a['status']]) ?></span></td>
            <td><?= e(format_date($a['created_at'], 'd M Y')) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
</section>
