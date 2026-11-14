<section class="panel">
    <div class="table-wrap"><table class="tbl">
        <thead><tr><th>When</th><th>User</th><th>Action</th><th>Entity</th><th>Details</th><th>IP</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
        <tr><td><?= e(format_date($r['created_at'], 'd M Y, H:i')) ?></td><td><?= e($r['name'] ?: '—') ?></td><td><span class="pill"><?= e($r['action']) ?></span></td><td><?= e($r['entity']) ?><?= $r['entity_id'] ? ' #' . (int) $r['entity_id'] : '' ?></td><td><small><?= e($r['details']) ?></small></td><td class="mono tiny"><?= e($r['ip']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
</section>
