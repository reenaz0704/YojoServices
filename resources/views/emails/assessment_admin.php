<h2 style="margin:0 0 6px;font-size:20px;color:#010535;">New readiness self-assessment</h2>
<p style="color:#707070;">Reference <strong style="color:#3a2e92;"><?= e($reference) ?></strong></p>
<p><strong><?= e($data['full_name']) ?></strong>, <?= e($data['designation']) ?> — <?= e($data['company']) ?><br><?= e($data['email']) ?> · <?= e($data['phone']) ?></p>
<p><?= e($data['organization_type']) ?> · <?= e($data['industry']) ?> · <?= e($data['company_size']) ?> employees · <?= e($data['country']) ?></p>
<p><strong>Requirements:</strong> <?= e(implode(', ', $frameworks)) ?><?= $data['other_requirement'] ? ' (' . e($data['other_requirement']) . ')' : '' ?></p>
<p style="font-size:18px;"><strong>Score: <?= (int) $result['score'] ?>/100 — <?= e($result['band']) ?></strong></p>
<table role="presentation" width="100%" cellpadding="6" style="font-size:14px;border-collapse:collapse;">
<?php foreach ($result['domains'] as $d): ?><tr><td style="border-bottom:1px solid #eee;"><?= e($d['label']) ?></td><td style="border-bottom:1px solid #eee;text-align:right;"><?= (int) $d['score'] ?>%</td></tr><?php endforeach; ?>
</table>
