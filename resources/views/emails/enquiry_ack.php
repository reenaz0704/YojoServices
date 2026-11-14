<h2 style="margin:0 0 12px;font-size:20px;color:#010535;">Thank you, <?= e($enquiry['full_name']) ?></h2>
<p>We have received your <?= e(strtolower($typeLabel)) ?> and a consultant will respond within one business day.</p>
<p style="background:#f2f2f5;border-left:4px solid #3a2e92;padding:12px 16px;border-radius:6px;">Your reference number: <strong style="font-family:Consolas,monospace;color:#3a2e92;"><?= e($enquiry['reference']) ?></strong></p>
<?php if ($enquiry['preferred_date']): ?><p><strong>Requested slot:</strong> <?= e(date('l, d F Y', strtotime($enquiry['preferred_date']))) ?> at <?= e($enquiry['preferred_time']) ?> IST (<?= e($enquiry['meeting_mode']) ?>). This slot is provisional until confirmed by our team.</p><?php endif; ?>
<?php if ($services): ?><p><strong>Services of interest:</strong> <?= e(implode(', ', $services)) ?></p><?php endif; ?>
<p>Please quote your reference in any correspondence. For security, please do not send confidential evidence by email until we have agreed a secure channel.</p>
<p>Kind regards,<br><?= e(setting('company_name')) ?></p>
