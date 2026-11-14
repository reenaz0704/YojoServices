<?php use App\Models\Enquiry; $max = max(1, max(array_column($trend, 'c'))); ?>
<div class="adm-kpis">
    <a class="kpi" href="<?= url('admin/enquiries?status=new') ?>"><span class="kpi__icon"><?= icon('inbox') ?></span><div><small>New enquiries</small><strong><?= $kpi['new_enquiries'] ?></strong><em><?= $kpi['enquiries_30'] ?> in last 30 days</em></div></a>
    <a class="kpi" href="<?= url('admin/enquiries?type=consultation') ?>"><span class="kpi__icon"><?= icon('calendar') ?></span><div><small>Upcoming consultations</small><strong><?= $kpi['consultations'] ?></strong><em>requested slots</em></div></a>
    <a class="kpi" href="<?= url('admin/assessments?status=new') ?>"><span class="kpi__icon"><?= icon('checklist') ?></span><div><small>New assessments</small><strong><?= $kpi['assessments'] ?></strong><em>avg. score <?= $kpi['avg_score'] ?: '—' ?></em></div></a>
    <a class="kpi" href="<?= url('admin/manage/posts') ?>"><span class="kpi__icon"><?= icon('file-text') ?></span><div><small>Published content</small><strong><?= $kpi['posts'] ?></strong><em><?= $kpi['services'] ?> live services</em></div></a>
</div>

<div class="adm-grid adm-grid--2-1">
    <section class="panel">
        <header class="panel__head"><h2>Enquiries — last 14 days</h2></header>
        <div class="chart" role="img" aria-label="Enquiries per day">
            <?php foreach ($trend as $t): ?>
            <div class="chart__col" title="<?= e($t['d']) ?>: <?= $t['c'] ?>"><span class="chart__bar" style="height:<?= max(3, round($t['c'] / $max * 100)) ?>%"><b><?= $t['c'] ?: '' ?></b></span><small><?= e(substr($t['d'], 0, 2)) ?></small></div>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="panel">
        <header class="panel__head"><h2>Most requested services</h2></header>
        <?php if (!$topServices): ?><p class="muted">No data yet.</p><?php endif; ?>
        <?php $tmax = $topServices ? max(array_column($topServices, 'c')) : 1; foreach ($topServices as $s): ?>
        <div class="hbar"><span><?= e($s['title']) ?></span><div><i style="width:<?= round($s['c'] / $tmax * 100) ?>%"></i></div><b><?= $s['c'] ?></b></div>
        <?php endforeach; ?>
        <?php if ($byType): ?><div class="chips"><?php foreach ($byType as $b): ?><span class="pill"><?= e(Enquiry::TYPES[$b['type']] ?? $b['type']) ?>: <?= $b['c'] ?></span><?php endforeach; ?></div><?php endif; ?>
    </section>
</div>

<div class="adm-grid adm-grid--2-1">
    <section class="panel">
        <header class="panel__head"><h2>Recent enquiries</h2><a href="<?= url('admin/enquiries') ?>">View all →</a></header>
        <div class="table-wrap"><table class="tbl">
            <thead><tr><th>Reference</th><th>Contact</th><th>Type</th><th>Status</th><th>Received</th></tr></thead>
            <tbody>
            <?php if (!$recent): ?><tr><td colspan="5" class="muted">No enquiries yet.</td></tr><?php endif; ?>
            <?php foreach ($recent as $r): ?>
            <tr><td><a class="mono" href="<?= url('admin/enquiries/' . $r['id']) ?>"><?= e($r['reference']) ?></a></td><td><strong><?= e($r['full_name']) ?></strong><small><?= e($r['company']) ?></small></td><td><?= e(Enquiry::TYPES[$r['type']]) ?></td><td><span class="status status--<?= e($r['status']) ?>"><?= e(Enquiry::STATUSES[$r['status']]) ?></span></td><td><?= e(format_date($r['created_at'], 'd M, H:i')) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    </section>
    <div class="adm-stack">
        <section class="panel">
            <header class="panel__head"><h2>Upcoming consultations</h2></header>
            <?php if (!$upcoming): ?><p class="muted">No upcoming slots.</p><?php endif; ?>
            <?php foreach ($upcoming as $u): ?>
            <a class="agenda" href="<?= url('admin/enquiries/' . $u['id']) ?>"><span class="agenda__date"><b><?= date('d', strtotime($u['preferred_date'])) ?></b><?= date('M', strtotime($u['preferred_date'])) ?></span><span><strong><?= e($u['company']) ?></strong><small><?= e($u['preferred_time']) ?> IST · <?= e($u['meeting_mode']) ?> · <?= e($u['full_name']) ?></small></span></a>
            <?php endforeach; ?>
        </section>
        <section class="panel">
            <header class="panel__head"><h2>Latest assessments</h2></header>
            <?php if (!$recentAssess): ?><p class="muted">None yet.</p><?php endif; ?>
            <?php foreach ($recentAssess as $a): ?>
            <a class="agenda" href="<?= url('admin/assessments/' . $a['id']) ?>"><span class="score-pill" style="--s:<?= (int) $a['score'] ?>"><?= (int) $a['score'] ?></span><span><strong><?= e($a['company']) ?></strong><small><?= e($a['readiness_band']) ?> · <?= e(format_date($a['created_at'])) ?></small></span></a>
            <?php endforeach; ?>
        </section>
    </div>
</div>
