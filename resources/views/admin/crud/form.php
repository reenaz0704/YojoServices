<?php
$isNew = empty($item['id']);
$action = $isNew ? url('admin/manage/' . $r['key'] . '/create') : url('admin/manage/' . $r['key'] . '/' . $item['id'] . '/edit');
$side = ['bool', 'select', 'image', 'icon', 'number', 'datetime', 'slug', 'tags', 'pivot'];
?>
<a class="adm-back" href="<?= url('admin/manage/' . $r['key']) ?>">← <?= e($r['label']) ?></a>
<?php if (!empty($r['notice'])): ?><div class="adm-flash adm-flash--info"><?= icon('info', 'icon icon--sm') ?><?= e($r['notice']) ?></div><?php endif; ?>
<form class="adm-form" method="post" action="<?= $action ?>" enctype="multipart/form-data" data-dirty>
    <?= csrf_field() ?>
    <div class="adm-grid adm-grid--form">
        <div class="panel">
            <?php foreach ($r['fields'] as $name => $f): if (in_array($f['type'], $side, true)) continue; $val = $item[$name] ?? ''; $err = $errors[$name] ?? null; ?>
            <div class="fld <?= $err ? 'has-error' : '' ?>">
                <label for="f_<?= $name ?>"><?= e($f['label']) ?><?= !empty($f['required']) ? ' <b>*</b>' : '' ?></label>
                <?php if ($f['type'] === 'rich'): ?>
                    <div class="rte" data-rte>
                        <div class="rte__bar">
                            <button type="button" data-cmd="formatBlock" data-val="H2" title="Heading">H2</button>
                            <button type="button" data-cmd="formatBlock" data-val="H3" title="Subheading">H3</button>
                            <button type="button" data-cmd="formatBlock" data-val="P" title="Paragraph">¶</button>
                            <span></span>
                            <button type="button" data-cmd="bold" title="Bold"><b>B</b></button>
                            <button type="button" data-cmd="italic" title="Italic"><i>I</i></button>
                            <button type="button" data-cmd="underline" title="Underline"><u>U</u></button>
                            <span></span>
                            <button type="button" data-cmd="insertUnorderedList" title="Bulleted list">• List</button>
                            <button type="button" data-cmd="insertOrderedList" title="Numbered list">1. List</button>
                            <button type="button" data-cmd="formatBlock" data-val="BLOCKQUOTE" title="Quote">❝</button>
                            <button type="button" data-cmd="createLink" title="Link">Link</button>
                            <button type="button" data-cmd="unlink" title="Remove link">Unlink</button>
                            <button type="button" data-cmd="image" title="Insert image">Image</button>
                            <span></span>
                            <button type="button" data-cmd="removeFormat" title="Clear formatting">Clear</button>
                            <button type="button" data-cmd="html" title="Edit HTML">&lt;/&gt;</button>
                        </div>
                        <div class="rte__area prose" contenteditable="true"><?= $val ?></div>
                        <textarea name="<?= $name ?>" id="f_<?= $name ?>" class="rte__src" hidden><?= e($val) ?></textarea>
                    </div>
                <?php elseif (in_array($f['type'], ['textarea', 'lines'], true)): ?>
                    <textarea id="f_<?= $name ?>" name="<?= $name ?>" rows="<?= (int) ($f['rows'] ?? ($f['type'] === 'lines' ? 6 : 4)) ?>" <?= !empty($f['max']) ? 'maxlength="' . (int) $f['max'] . '"' : '' ?> <?= !empty($f['required']) ? 'required' : '' ?>><?= e($val) ?></textarea>
                <?php elseif ($f['type'] === 'password'): ?>
                    <input id="f_<?= $name ?>" type="password" name="<?= $name ?>" autocomplete="new-password" <?= $isNew ? 'required' : '' ?>>
                <?php else: ?>
                    <input id="f_<?= $name ?>" type="<?= in_array($f['type'], ['email', 'url'], true) ? $f['type'] : 'text' ?>" name="<?= $name ?>" value="<?= e($val) ?>" <?= !empty($f['max']) ? 'maxlength="' . (int) $f['max'] . '"' : '' ?> <?= !empty($f['required']) ? 'required' : '' ?>>
                <?php endif; ?>
                <?php if (!empty($f['help'])): ?><small><?= e($f['help']) ?></small><?php endif; ?>
                <?php if ($err): ?><em><?= e($err) ?></em><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <aside class="adm-stack">
            <div class="panel panel--sticky">
                <div class="adm-actions">
                    <button class="adm-btn adm-btn--primary" type="submit"><?= icon('check', 'icon icon--sm') ?> Save</button>
                    <button class="adm-btn adm-btn--ghost" type="submit" name="_stay" value="1">Save & continue editing</button>
                </div>
                <?php foreach ($r['fields'] as $name => $f): if (!in_array($f['type'], $side, true)) continue; $val = $item[$name] ?? ''; $err = $errors[$name] ?? null; ?>
                <div class="fld <?= $err ? 'has-error' : '' ?>">
                    <?php if ($f['type'] === 'bool'): ?>
                        <label class="switch"><input type="checkbox" name="<?= $name ?>" value="1" <?= $val ? 'checked' : '' ?>><span></span><?= e($f['label']) ?></label>
                    <?php else: ?>
                        <label for="f_<?= $name ?>"><?= e($f['label']) ?><?= !empty($f['required']) ? ' <b>*</b>' : '' ?></label>
                        <?php if ($f['type'] === 'select'): ?>
                            <select id="f_<?= $name ?>" name="<?= $name ?>" <?= !empty($f['required']) ? 'required' : '' ?>><option value="">—</option><?php foreach ($options[$name] as $k => $l): ?><option value="<?= e($k) ?>" <?= (string) $k === (string) $val ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select>
                        <?php elseif ($f['type'] === 'pivot'): $sel = is_array($val) ? array_map('intval', $val) : []; ?>
                            <div class="pivot"><input type="search" placeholder="Filter…" data-pivot-filter><?php foreach ($options[$name] as $k => $l): ?><label class="pivot__opt"><input type="checkbox" name="<?= $name ?>[]" value="<?= e($k) ?>" <?= in_array((int) $k, $sel, true) ? 'checked' : '' ?>><?= e($l) ?></label><?php endforeach; ?></div>
                        <?php elseif ($f['type'] === 'icon'): ?>
                            <div class="icon-pick"><?php foreach ($icons as $ic): ?><label title="<?= e($ic) ?>"><input type="radio" name="<?= $name ?>" value="<?= e($ic) ?>" <?= $ic === ($val ?: 'shield') ? 'checked' : '' ?>><span><?= icon($ic, 'icon icon--sm') ?></span></label><?php endforeach; ?></div>
                        <?php elseif ($f['type'] === 'image'): ?>
                            <div class="img-field" data-img-field>
                                <img src="<?= $val ? e(media_url($val)) : '' ?>" alt="" <?= $val ? '' : 'hidden' ?> data-img-preview>
                                <input id="f_<?= $name ?>" type="file" name="<?= $name ?>" accept="image/png,image/jpeg,image/webp,image/gif,image/svg+xml">
                                <?php if ($val): ?><label class="check-sm"><input type="checkbox" name="<?= $name ?>_remove" value="1"> Remove image</label><?php endif; ?>
                            </div>
                        <?php elseif ($f['type'] === 'datetime'): ?>
                            <input id="f_<?= $name ?>" type="datetime-local" name="<?= $name ?>" value="<?= $val ? e(date('Y-m-d\TH:i', strtotime($val))) : '' ?>">
                        <?php elseif ($f['type'] === 'number'): ?>
                            <input id="f_<?= $name ?>" type="number" name="<?= $name ?>" value="<?= e($val) ?>" min="0" <?= !empty($f['required']) ? 'required' : '' ?>>
                        <?php else: ?>
                            <input id="f_<?= $name ?>" type="text" name="<?= $name ?>" value="<?= e($val) ?>" <?= $f['type'] === 'slug' ? 'data-slug-from="' . e($f['from']) . '" pattern="[a-z0-9\-]*"' : '' ?> <?= !empty($f['max']) ? 'maxlength="' . (int) $f['max'] . '"' : '' ?>>
                        <?php endif; ?>
                        <?php if (!empty($f['help'])): ?><small><?= e($f['help']) ?></small><?php endif; ?>
                    <?php endif; ?>
                    <?php if ($err): ?><em><?= e($err) ?></em><?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php if (!$isNew && !empty($r['preview'])): $p = preg_replace_callback('/\{(\w+)\}/', function ($m) use ($item) {
                    if ($m[1] === 'category_slug' && !empty($item['category_id'])) { return db()->value('SELECT slug FROM service_categories WHERE id = ?', [(int) $item['category_id']]); }
                    return $item[$m[1]] ?? ''; }, $r['preview']); ?>
                <a class="adm-btn adm-btn--ghost adm-btn--block" href="<?= url($p) ?>" target="_blank" rel="noopener"><?= icon('eye', 'icon icon--sm') ?> View on website</a>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</form>
