<?php use App\Core\Auth;
$canEdit = Auth::can('editor');
$fmt = function ($col, $v) {
    if (in_array($col, ['is_published', 'is_verified', 'is_authorized', 'is_active', 'is_featured'], true)) {
        return $v ? '<span class="status status--won">Yes</span>' : '<span class="status status--closed">No</span>';
    }
    if ($col === 'status') {
        return '<span class="status status--' . ($v === 'published' ? 'won' : 'new') . '">' . e(ucfirst((string) $v)) . '</span>';
    }
    if (preg_match('/_at$/', $col)) {
        return $v ? e(format_date($v, 'd M Y')) : '—';
    }
    return e(str_limit((string) $v, 70));
};
?>
<?php if (!empty($r['notice'])): ?><div class="adm-flash adm-flash--info"><?= icon('info', 'icon icon--sm') ?><?= e($r['notice']) ?></div><?php endif; ?>
<section class="panel">
    <header class="panel__head panel__head--tools">
        <form class="adm-search" method="get"><?= icon('search', 'icon icon--sm') ?><input type="search" name="q" value="<?= e($q) ?>" placeholder="Search <?= e(strtolower($r['label'])) ?>…"></form>
        <span class="muted"><?= (int) $total ?> record(s)</span>
        <?php if ($canEdit): ?><a class="adm-btn adm-btn--primary" href="<?= url('admin/manage/' . $r['key'] . '/create') ?>"><?= icon('plus', 'icon icon--sm') ?> New <?= e(strtolower($r['singular'])) ?></a><?php endif; ?>
    </header>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><?php foreach ($r['columns'] as $c => $label): ?><th><?= e($label) ?></th><?php endforeach; ?><th class="tbl__actions">Actions</th></tr></thead>
            <tbody>
            <?php if (!$rows): ?><tr><td colspan="<?= count($r['columns']) + 1 ?>" class="muted">Nothing found.</td></tr><?php endif; ?>
            <?php foreach ($rows as $row): ?>
            <tr>
                <?php $first = true; foreach ($r['columns'] as $c => $label): ?>
                <td><?php if ($first && $canEdit): ?><a href="<?= url('admin/manage/' . $r['key'] . '/' . $row['id'] . '/edit') ?>"><strong><?= $fmt($c, $row[$c] ?? '') ?></strong></a><?php else: ?><?= $fmt($c, $row[$c] ?? '') ?><?php endif; $first = false; ?></td>
                <?php endforeach; ?>
                <td class="tbl__actions">
                    <?php if (!empty($r['preview'])): $p = preg_replace_callback('/\{(\w+)\}/', function ($m) use ($row) { return $row[$m[1]] ?? ''; }, $r['preview']); ?>
                    <a class="icon-btn" href="<?= url($p) ?>" target="_blank" rel="noopener" title="View on site"><?= icon('eye', 'icon icon--sm') ?></a>
                    <?php endif; ?>
                    <?php if ($canEdit): ?>
                    <?php if (isset($r['fields']['sort_order']) && $q === ''): foreach (['up' => 'Move up', 'down' => 'Move down'] as $dir => $lbl): ?>
                    <form method="post" action="<?= url('admin/manage/' . $r['key'] . '/' . $row['id'] . '/move') ?>"><?= csrf_field() ?><input type="hidden" name="dir" value="<?= $dir ?>"><button class="icon-btn" title="<?= $lbl ?>" aria-label="<?= $lbl ?>"><?= $dir === 'up' ? '&uarr;' : '&darr;' ?></button></form>
                    <?php endforeach; endif; ?>
                    <?php if (isset($r['toggle']) || isset($r['fields']['is_published']) || isset($r['fields']['is_active'])): ?>
                    <form method="post" action="<?= url('admin/manage/' . $r['key'] . '/' . $row['id'] . '/toggle') ?>"><?= csrf_field() ?><input type="hidden" name="_q" value="<?= e($q) ?>"><button class="icon-btn" title="Publish / unpublish"><?= icon('refresh', 'icon icon--sm') ?></button></form>
                    <?php endif; ?>
                    <a class="icon-btn" href="<?= url('admin/manage/' . $r['key'] . '/' . $row['id'] . '/edit') ?>" title="Edit"><?= icon('edit', 'icon icon--sm') ?></a>
                    <form method="post" action="<?= url('admin/manage/' . $r['key'] . '/' . $row['id'] . '/delete') ?>" data-confirm="Delete this <?= e(strtolower($r['singular'])) ?>? This cannot be undone."><?= csrf_field() ?><button class="icon-btn icon-btn--danger" title="Delete"><?= icon('trash', 'icon icon--sm') ?></button></form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pages > 1): ?><nav class="adm-pager"><?php for ($p = 1; $p <= $pages; $p++): ?><a class="<?= $p === $page ? 'is-active' : '' ?>" href="?<?= http_build_query(['q' => $q, 'page' => $p]) ?>"><?= $p ?></a><?php endfor; ?></nav><?php endif; ?>
</section>
