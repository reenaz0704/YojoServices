<?php $titles = ['appearance' => 'Website design template', 'company' => 'Company', 'about' => 'Who We Are page', 'contact' => 'Contact details (also used in the footer)', 'footer' => 'Footer', 'social' => 'Social media (footer icons)', 'popup' => 'Enquiry pop-up', 'homepage' => 'Homepage sections', 'seo' => 'SEO defaults'];
$order = array_flip(array_keys($titles));
uksort($groups, function ($a, $b) use ($order) { return ($order[$a] ?? 99) <=> ($order[$b] ?? 99); }); ?>
<form method="post" action="<?= url('admin/settings') ?>" data-dirty>
    <?= csrf_field() ?>
    <div class="adm-grid adm-grid--form">
        <div class="adm-stack">
            <?php foreach ($groups as $g => $items): ?>
            <section class="panel">
                <header class="panel__head"><h2><?= e($titles[$g] ?? ucfirst($g)) ?></h2></header>
                <?php foreach ($items as $s): ?>
                <div class="fld">
                    <label for="s_<?= e($s['setting_key']) ?>"><?= e($s['label']) ?></label>
                    <?php if ($s['input_type'] === 'bool'): ?>
                        <select id="s_<?= e($s['setting_key']) ?>" name="settings[<?= e($s['setting_key']) ?>]"><option value="1" <?= $s['setting_value'] === '1' ? 'selected' : '' ?>>On</option><option value="0" <?= $s['setting_value'] !== '1' ? 'selected' : '' ?>>Off</option></select>
                    <?php elseif (in_array($s['input_type'], ['layout', 'scene'], true)): ?>
                        <select id="s_<?= e($s['setting_key']) ?>" name="settings[<?= e($s['setting_key']) ?>]">
                        <?php foreach ($s['input_type'] === 'layout' ? site_layouts() : site_scene_styles() as $ok => $ol): ?><option value="<?= e($ok) ?>" <?= (string) $s['setting_value'] === (string) $ok ? 'selected' : '' ?>><?= e($ol) ?></option><?php endforeach; ?>
                        </select>
                    <?php elseif ($s['input_type'] === 'template'): ?>
                        <div class="tpl-pick">
                        <?php foreach (site_templates() as $tk => $tv): ?>
                            <label class="tpl-pick__opt"><input type="radio" name="settings[<?= e($s['setting_key']) ?>]" value="<?= e($tk) ?>" <?= $s['setting_value'] === $tk ? 'checked' : '' ?>>
                                <span class="tpl-pick__sw"><?php foreach ($tv['swatch'] as $c): ?><i style="background:<?= e($c) ?>"></i><?php endforeach; ?></span>
                                <strong><?= e($tv['name']) ?></strong><small><?= e($tv['desc']) ?></small>
                                <a href="<?= url('/') ?>?template=<?= e($tk) ?>" target="_blank" rel="noopener">Preview</a></label>
                        <?php endforeach; ?>
                        </div>
                    <?php elseif ($s['input_type'] === 'number'): ?>
                        <input id="s_<?= e($s['setting_key']) ?>" type="number" min="1" max="120" name="settings[<?= e($s['setting_key']) ?>]" value="<?= e($s['setting_value']) ?>">
                    <?php elseif ($s['input_type'] === 'textarea'): ?>
                        <textarea id="s_<?= e($s['setting_key']) ?>" name="settings[<?= e($s['setting_key']) ?>]" rows="<?= substr_count((string) $s['setting_value'], "
") > 2 ? 8 : 3 ?>"><?= e($s['setting_value']) ?></textarea>
                    <?php else: ?>
                        <input id="s_<?= e($s['setting_key']) ?>" type="<?= in_array($s['input_type'], ['email', 'url'], true) ? $s['input_type'] : 'text' ?>" name="settings[<?= e($s['setting_key']) ?>]" value="<?= e($s['setting_value']) ?>">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </section>
            <?php endforeach; ?>
        </div>
        <aside class="adm-stack">
            <div class="panel panel--sticky">
                <button class="adm-btn adm-btn--primary adm-btn--block">Save settings</button>
                <h3 class="sub">Email delivery (SMTP)</h3>
                <p class="tiny">For security, SMTP credentials are stored in <code>config/mail.local.php</code> on the server, not in the database.</p>
                <dl class="dl dl--compact">
                    <div><dt>Status</dt><dd><?= $mail['enabled'] ? '<span class="status status--won">Sending via SMTP</span>' : '<span class="status status--new">Logging only (dev)</span>' ?></dd></div>
                    <div><dt>Host</dt><dd><?= e($mail['host']) ?>:<?= (int) $mail['port'] ?> (<?= e($mail['encryption']) ?>)</dd></div>
                    <div><dt>From</dt><dd><?= e($mail['from_email']) ?></dd></div>
                </dl>
            </div>
        </aside>
    </div>
</form>
