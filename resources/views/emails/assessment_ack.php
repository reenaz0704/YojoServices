<h2 style="margin:0 0 12px;font-size:20px;color:#010535;">Your preliminary readiness summary</h2>
<p>Hi <?= e($data['full_name']) ?>, thank you for completing the Audit Readiness Assessment.</p>
<p style="background:#f2f2f5;border-radius:8px;padding:16px;font-size:16px;">Indicative score: <strong style="color:#3a2e92;font-size:22px;"><?= (int) $result['score'] ?>/100</strong><br><strong><?= e($result['band']) ?></strong> — <?= e($result['band_text']) ?></p>
<table role="presentation" width="100%" cellpadding="6" style="font-size:14px;border-collapse:collapse;">
<?php foreach ($result['domains'] as $d): ?><tr><td style="border-bottom:1px solid #eee;"><?= e($d['label']) ?></td><td style="border-bottom:1px solid #eee;text-align:right;"><?= (int) $d['score'] ?>%</td></tr><?php endforeach; ?>
</table>
<?php if ($result['priorities']): ?><p><strong>Suggested priorities:</strong> <?= e(implode(', ', $result['priorities'])) ?></p><?php endif; ?>
<p style="font-size:13px;color:#707070;border-top:1px solid #eee;padding-top:12px;"><strong>Important:</strong> this is a preliminary self-assessment based solely on your answers. It is not an official audit, certification or assurance opinion. Reference: <?= e($reference) ?>.</p>
<p><a href="<?= e(full_url('book-consultation')) ?>" style="background:#3a2e92;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;">Discuss results with a consultant</a></p>
