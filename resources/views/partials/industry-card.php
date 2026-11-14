<?php /** @var array $ind  int $i */
$tags = array_filter(array_map('trim', preg_split('/[·,|]/u', (string) ($ind['risk_tags'] ?? ''))));
?>
<a class="icard fx-reveal" href="<?= url('industries/' . $ind['slug']) ?>" style="--i:<?= (int) ($i ?? 0) ?>">
    <div class="icard__media" aria-hidden="true">
        <?php if (!empty($ind['image'])): ?>
            <img src="<?= e(media_url($ind['image'])) ?>" alt="" loading="lazy">
        <?php else: ?>
            <div class="icard__illus icard__illus--<?= ((int) ($i ?? 0)) % 4 ?>">
                <svg viewBox="0 0 200 120" preserveAspectRatio="xMidYMid slice"><g class="icard__lines"><path d="M0 90 L50 70 L90 82 L140 40 L200 55"/><path d="M0 30 L60 46 L110 20 L160 34 L200 10"/></g><g class="icard__nodes"><circle cx="50" cy="70" r="2.5"/><circle cx="90" cy="82" r="2"/><circle cx="140" cy="40" r="3"/><circle cx="60" cy="46" r="2"/><circle cx="110" cy="20" r="2.5"/><circle cx="160" cy="34" r="2"/></g></svg>
                <span class="icard__glyph"><?= icon($ind['icon']) ?></span>
            </div>
        <?php endif; ?>
    </div>
    <div class="icard__body">
        <span class="icard__icon"><?= icon($ind['icon'], 'icon icon--sm') ?></span>
        <h3><?= e($ind['name']) ?></h3>
        <p><?= e(str_limit($ind['summary'], 110)) ?></p>
        <?php if ($tags): ?><ul class="icard__risks" aria-label="Key risks"><?php foreach (array_slice($tags, 0, 4) as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul><?php endif; ?>
        <span class="icard__more">Explore industry <span class="icard__arrow"><svg viewBox="0 0 40 12" aria-hidden="true"><path d="M0 6 H36 M30 1 L36 6 L30 11"/></svg></span></span>
    </div>
</a>
