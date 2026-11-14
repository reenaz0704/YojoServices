<h2 style="margin:0 0 6px;font-size:20px;color:#010535;">New <?= e($typeLabel) ?></h2>
<p style="margin:0 0 18px;color:#707070;">Reference <strong style="color:#3a2e92;"><?= e($enquiry['reference']) ?></strong> · <?= e($enquiry['created_at']) ?></p>
<table role="presentation" width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
<?php foreach (['Name' => $enquiry['full_name'], 'Company' => $enquiry['company'], 'Email' => $enquiry['email'], 'Phone' => $enquiry['phone'], 'Industry' => $enquiry['industry'], 'Company size' => $enquiry['company_size'],
    'Preferred slot' => $enquiry['preferred_date'] ? $enquiry['preferred_date'] . ' ' . $enquiry['preferred_time'] . ' IST (' . $enquiry['meeting_mode'] . ')' : '', 'Services' => implode(', ', $services), 'Source page' => $enquiry['source_page']] as $k => $v): if ($v === '' || $v === null) continue; ?>
<tr><td style="border-bottom:1px solid #eee;color:#707070;width:140px;"><?= e($k) ?></td><td style="border-bottom:1px solid #eee;"><?= e($v) ?></td></tr>
<?php endforeach; ?>
</table>
<?php if ($enquiry['message']): ?><p style="margin:18px 0 4px;color:#707070;">Message</p><div style="background:#f7f7fb;border-radius:8px;padding:14px;"><?= nl2br(e($enquiry['message'])) ?></div><?php endif; ?>
<p style="margin-top:22px;"><a href="<?= e(full_url('admin/enquiries/' . $enquiry['id'])) ?>" style="background:#3a2e92;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;">Open in dashboard</a></p>
