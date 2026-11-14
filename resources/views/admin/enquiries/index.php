<?php use App\Core\Auth; use App\Models\Enquiry; ?>
<div class="tabs">
    <a class="<?= $f['status'] === '' ? 'is-active' : '' ?>" href="?<?= http_build_query(array_merge($f, ['status' => ''])) ?>">All <span><?= array_sum($counts) ?></span></a>
    <?php foreach (Enquiry::STATUSES as $k => $l): ?><a class="<?= $f['status'] === $k ? 'is-active' : '' ?>" href="?<?= http_build_query(array_merge($f, ['status' => $k])) ?>"><?= e($l) ?> <span><?= (int) ($counts[$k] ?? 0) ?></span></a><?php endforeach; ?>
</div>
<section class="panel">
    <header class="panel__head panel__head--tools">
        <form class="adm-search" method="get"><?= icon('search', 'icon icon--sm') ?><input type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Reference, name, company, email…"><input type="hidden" name="status" value="<?= e($f['status']) ?>">
            <select name="type" onchange="this.form.submit()"><option value="">All types</option><?php foreach (Enquiry::TYPES as $k => $l): ?><option value="<?= $k ?>" <?= $f['type'] === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select>
        </form>
        <span class="muted"><?= (int) $total ?> result(s)</span>
        <?php if (Auth::can('admin')): ?><a class="adm-btn adm-btn--ghost" href="<?= url('admin/enquiries/export') ?>?<?= http_build_query($f) ?>"><?= icon('download', 'icon icon--sm') ?> Export CSV</a><?php endif; ?>
    </header>
    <div class="table-wrap"><table class="tbl">
        <thead><tr><th>Reference</th><th>Contact</th><th>Type</th><th>Services</th><th>Status</th><th>Received</th></tr></thead>
        <tbody>
        <?php if (!$rows): ?><tr><td colspan="6" class="muted">No enquiries match.</td></tr><?php endif; ?>
        <?php foreach ($rows as $r): ?>
        <tr class="<?= $r['status'] === 'new' ? 'is-unread' : '' ?>">
            <td><a class="mono" href="<?= url('admin/enquiries/' . $r['id']) ?>"><?= e($r['reference']) ?></a></td>
            <td><strong><?= e($r['full_name']) ?></strong><small><?= e($r['company']) ?> · <?= e($r['email']) ?></small></td>
            <td><?= e(Enquiry::TYPES[$r['type']]) ?><?php if ($r['preferred_date']): ?><small><?= e(format_date($r['preferred_date'])) ?> <?= e($r['preferred_time']) ?></small><?php endif; ?></td>
            <td><small><?= e(str_limit((string) $r['services'], 70)) ?: '—' ?></small></td>
            <td><span class="status status--<?= e($r['status']) ?>"><?= e(Enquiry::STATUSES[$r['status']]) ?></span></td>
            <td><?= e(format_date($r['created_at'], 'd M Y, H:i')) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php if ($pages > 1): ?><nav class="adm-pager"><?php for ($p = 1; $p <= $pages; $p++): ?><a class="<?= $p === $page ? 'is-active' : '' ?>" href="?<?= http_build_query(array_merge($f, ['page' => $p])) ?>"><?= $p ?></a><?php endfor; ?></nav><?php endif; ?>
</section>
