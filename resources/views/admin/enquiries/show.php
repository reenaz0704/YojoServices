<?php use App\Core\Auth; use App\Models\Enquiry; ?>
<a class="adm-back" href="<?= url('admin/enquiries') ?>">← Enquiries</a>
<div class="adm-grid adm-grid--form">
    <div class="adm-stack">
        <section class="panel">
            <header class="panel__head"><h2><?= e($e['full_name']) ?> <small class="muted">· <?= e($e['company']) ?></small></h2><span class="status status--<?= e($e['status']) ?>"><?= e(Enquiry::STATUSES[$e['status']]) ?></span></header>
            <dl class="dl">
                <div><dt>Reference</dt><dd class="mono"><?= e($e['reference']) ?></dd></div>
                <div><dt>Type</dt><dd><?= e(Enquiry::TYPES[$e['type']]) ?></dd></div>
                <div><dt>Email</dt><dd><a href="mailto:<?= e($e['email']) ?>?subject=<?= rawurlencode('Re: ' . $e['reference']) ?>"><?= e($e['email']) ?></a></dd></div>
                <div><dt>Phone</dt><dd><a href="tel:<?= e(preg_replace('/[^\d+]/', '', $e['phone'])) ?>"><?= e($e['phone']) ?></a></dd></div>
                <div><dt>Industry</dt><dd><?= e($e['industry']) ?></dd></div>
                <div><dt>Company size</dt><dd><?= e($e['company_size']) ?></dd></div>
                <?php if ($e['preferred_date']): ?><div><dt>Requested slot</dt><dd><strong><?= e(date('D, d M Y', strtotime($e['preferred_date']))) ?> · <?= e($e['preferred_time']) ?> IST</strong><br><?= e($e['meeting_mode']) ?></dd></div><?php endif; ?>
                <div><dt>Received</dt><dd><?= e(format_date($e['created_at'], 'd M Y, H:i')) ?></dd></div>
                <div><dt>Source page</dt><dd><?= e($e['source_page'] ?: '—') ?></dd></div>
                <div><dt>Consent</dt><dd><?= $e['consent'] ? 'Given' : 'No' ?></dd></div>
            </dl>
            <h3 class="sub">Services required</h3>
            <div class="chips"><?php foreach ($services as $s): ?><span class="pill"><?= e($s['title']) ?></span><?php endforeach; ?><?= $services ? '' : '<span class="muted">None selected</span>' ?></div>
            <h3 class="sub">Message</h3>
            <div class="msg"><?= $e['message'] ? nl2br(e($e['message'])) : '<span class="muted">No message</span>' ?></div>
            <p class="muted tiny">IP <?= e($e['ip_address']) ?> · <?= e(str_limit($e['user_agent'], 90)) ?></p>
        </section>
        <section class="panel">
            <header class="panel__head"><h2>Notes & history</h2></header>
            <?php if (!$notes): ?><p class="muted">No notes yet.</p><?php endif; ?>
            <ul class="timeline-adm"><?php foreach ($notes as $n): ?><li><strong><?= e($n['author'] ?: 'System') ?></strong> <small><?= e(format_date($n['created_at'], 'd M Y, H:i')) ?></small><p><?= nl2br(e($n['note'])) ?></p></li><?php endforeach; ?></ul>
        </section>
    </div>
    <aside class="adm-stack">
        <?php if (Auth::can('editor')): ?>
        <form class="panel panel--sticky" method="post" action="<?= url('admin/enquiries/' . $e['id']) ?>">
            <?= csrf_field() ?>
            <div class="fld"><label>Status</label><select name="status"><?php foreach (Enquiry::STATUSES as $k => $l): ?><option value="<?= $k ?>" <?= $k === $e['status'] ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></div>
            <div class="fld"><label>Assigned to</label><select name="assigned_to"><option value="">Unassigned</option><?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>" <?= (int) $u['id'] === (int) $e['assigned_to'] ? 'selected' : '' ?>><?= e($u['name']) ?></option><?php endforeach; ?></select></div>
            <div class="fld"><label>Add internal note</label><textarea name="note" rows="4" placeholder="Call summary, next steps…"></textarea></div>
            <button class="adm-btn adm-btn--primary adm-btn--block">Update enquiry</button>
            <a class="adm-btn adm-btn--ghost adm-btn--block" href="mailto:<?= e($e['email']) ?>?subject=<?= rawurlencode('Re: your enquiry ' . $e['reference']) ?>"><?= icon('mail', 'icon icon--sm') ?> Reply by email</a>
        </form>
        <?php endif; ?>
        <?php if ($history): ?><section class="panel"><header class="panel__head"><h2>Other requests from this email</h2></header><?php foreach ($history as $h): ?><a class="agenda" href="<?= url('admin/enquiries/' . $h['id']) ?>"><span class="mono"><?= e($h['reference']) ?></span><small><?= e(Enquiry::STATUSES[$h['status']]) ?></small></a><?php endforeach; ?></section><?php endif; ?>
        <?php if (Auth::can('admin')): ?>
        <form method="post" action="<?= url('admin/enquiries/' . $e['id'] . '/delete') ?>" data-confirm="Permanently delete this enquiry and its notes?"><?= csrf_field() ?><button class="adm-btn adm-btn--danger adm-btn--block"><?= icon('trash', 'icon icon--sm') ?> Delete enquiry</button></form>
        <?php endif; ?>
    </aside>
</div>
