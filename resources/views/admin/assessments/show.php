<?php use App\Core\Auth; use App\Models\Assessment;
$labels = ['yes' => 'Yes', 'partial' => 'Partially', 'no' => 'No', 'unsure' => 'Not sure']; ?>
<a class="adm-back" href="<?= url('admin/assessments') ?>">← Assessments</a>
<div class="adm-grid adm-grid--form">
    <div class="adm-stack">
        <section class="panel">
            <header class="panel__head"><h2><?= e($a['company']) ?></h2><span class="score-pill score-pill--lg" style="--s:<?= (int) $a['score'] ?>"><?= (int) $a['score'] ?></span></header>
            <p><strong><?= e($a['readiness_band']) ?></strong> · preliminary self-assessment</p>
            <dl class="dl">
                <div><dt>Contact</dt><dd><?= e($a['full_name']) ?><?= $a['designation'] ? ', ' . e($a['designation']) : '' ?></dd></div>
                <div><dt>Email</dt><dd><a href="mailto:<?= e($a['email']) ?>"><?= e($a['email']) ?></a></dd></div>
                <div><dt>Phone</dt><dd><?= e($a['phone'] ?: '—') ?></dd></div>
                <div><dt>Organisation</dt><dd><?= e($a['organization_type']) ?> · <?= e($a['company_size']) ?> employees</dd></div>
                <div><dt>Industry / country</dt><dd><?= e($a['industry']) ?> · <?= e($a['country']) ?></dd></div>
                <div><dt>Requirements</dt><dd><?= e(implode(', ', $frameworks)) ?><?= $a['other_requirement'] ? ' — ' . e($a['other_requirement']) : '' ?></dd></div>
                <div><dt>Submitted</dt><dd><?= e(format_date($a['created_at'], 'd M Y, H:i')) ?></dd></div>
            </dl>
        </section>
        <section class="panel">
            <header class="panel__head"><h2>Domain scores & answers</h2></header>
            <?php foreach ($domains as $dk => $d): $ds = $domainScores[$dk]['score'] ?? 0; ?>
            <div class="domain">
                <div class="hbar"><span><strong><?= e($d[0]) ?></strong></span><div><i style="width:<?= (int) $ds ?>%"></i></div><b><?= (int) $ds ?>%</b></div>
                <ul><?php foreach ($d[1] as $qk => $q): $ans = $answers[$qk] ?? ''; ?><li><span class="ans ans--<?= e($ans) ?>"><?= e($labels[$ans] ?? '—') ?></span><?= e($q) ?></li><?php endforeach; ?></ul>
            </div>
            <?php endforeach; ?>
        </section>
    </div>
    <aside class="adm-stack">
        <?php if (Auth::can('editor')): ?>
        <form class="panel panel--sticky" method="post" action="<?= url('admin/assessments/' . $a['id']) ?>">
            <?= csrf_field() ?>
            <div class="fld"><label>Status</label><select name="status"><?php foreach (Assessment::STATUSES as $k => $l): ?><option value="<?= $k ?>" <?= $k === $a['status'] ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></div>
            <div class="fld"><label>Reviewer notes</label><textarea name="reviewer_notes" rows="6"><?= e($a['reviewer_notes']) ?></textarea></div>
            <button class="adm-btn adm-btn--primary adm-btn--block">Save review</button>
            <a class="adm-btn adm-btn--ghost adm-btn--block" href="mailto:<?= e($a['email']) ?>?subject=<?= rawurlencode('Your readiness assessment ' . $a['reference']) ?>"><?= icon('mail', 'icon icon--sm') ?> Email respondent</a>
        </form>
        <?php endif; ?>
    </aside>
</div>
