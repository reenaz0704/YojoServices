<?php
$i = $industry;
$tags = array_filter(array_map('trim', preg_split('/[·,|]/u', (string) $i['risk_tags'])));
$risks = lines($i['risks']);
$approach = lines($i['approach']);
partial('page-hero', [
    'title' => $i['name'], 'eyebrow' => 'Industry expertise', 'text' => $i['summary'], 'icon' => $i['icon'],
    'crumbs' => ['Industry Expertise' => url('industries'), $i['name'] => null],
    'actions' => [['Request a Consultation', url('book-consultation')], ['Take the readiness assessment', url('security-assessment')]],
]);
?>
<!-- Overview with visual -->
<section class="section">
    <div class="container ind-ov">
        <div class="ind-ov__copy fx-reveal">
            <p class="eyebrow">Industry overview</p>
            <p class="lead"><?= nl2br(e($i['overview'])) ?></p>
            <?php if ($tags): ?><ul class="icard__risks icard__risks--lg"><?php foreach ($tags as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul><?php endif; ?>
        </div>
        <div class="ind-ov__visual fx-reveal" style="--i:1" aria-hidden="true">
            <?php if ($i['image']): ?><img src="<?= e(media_url($i['image'])) ?>" alt="" loading="lazy">
            <?php else: ?>
            <div class="ind-ov__art">
                <svg viewBox="0 0 320 320"><circle cx="160" cy="160" r="130" class="ring-a"/><circle cx="160" cy="160" r="90" class="ring-b"/><circle cx="160" cy="160" r="50" class="ring-c"/>
                    <?php foreach (array_slice($risks, 0, 5) as $k => $r): $ang = deg2rad(-90 + $k * 72); ?><line x1="160" y1="160" x2="<?= round(160 + 130 * cos($ang)) ?>" y2="<?= round(160 + 130 * sin($ang)) ?>" class="spoke"/><circle cx="<?= round(160 + 130 * cos($ang)) ?>" cy="<?= round(160 + 130 * sin($ang)) ?>" r="7" class="blip" style="--d:<?= $k * .4 ?>s"/><?php endforeach; ?>
                </svg>
                <span class="ind-ov__glyph"><?= icon($i['icon']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Risk radar list -->
<?php if ($risks): ?>
<section class="section section--dark ind-risk">
    <div class="section__bg" aria-hidden="true"><div class="grid-lines"></div></div>
    <div class="container">
        <div class="section-head fx-reveal"><p class="eyebrow eyebrow--light">Threat landscape</p><h2 class="h2 h2--xl">Common cybersecurity risks</h2></div>
        <ol class="risk-list">
            <?php foreach ($risks as $k => $r): ?><li class="fx-reveal" style="--i:<?= $k ?>"><span class="risk-list__n mono"><?= sprintf('%02d', $k + 1) ?></span><span><?= e($r) ?></span><span class="risk-list__bar" style="--v:<?= 92 - $k * 9 ?>%"></span></li><?php endforeach; ?>
        </ol>
    </div>
</section>
<?php endif; ?>

<!-- Governance & compliance -->
<section class="section">
    <div class="container ind-two">
        <?php if ($i['governance']): ?>
        <div class="ind-panel fx-reveal"><span class="ind-panel__icon"><?= icon('compass') ?></span><h2>Governance challenges</h2><?php partial('list', ['text' => $i['governance']]); ?></div>
        <?php endif; ?>
        <div class="ind-panel ind-panel--alt fx-reveal" style="--i:1"><span class="ind-panel__icon"><?= icon('scale') ?></span><h2>Relevant compliance requirements</h2>
            <div class="chips-row"><?php foreach (lines($i['compliance']) as $c): ?><span class="chip chip--solid"><?= e($c) ?></span><?php endforeach; ?></div>
            <?php if ($i['challenges']): ?><h3 class="ind-panel__sub">Common audit challenges</h3><?php partial('list', ['text' => $i['challenges']]); ?><?php endif; ?>
        </div>
    </div>
</section>

<!-- Recommended services + solutions -->
<?php if ($services || $i['solutions']): ?>
<section class="section section--tint">
    <div class="container">
        <div class="section-head fx-reveal"><p class="eyebrow">How we help</p><h2 class="h2 h2--xl">Recommended audit services &amp; cybersecurity solutions</h2></div>
        <?php if ($services): ?>
        <div class="svc-card-grid">
            <?php foreach ($services as $k => $s): ?>
            <a class="svc-card svc-card--sm fx-reveal" style="--i:<?= $k ?>" href="<?= url('services/' . $s['category_slug'] . '/' . $s['slug']) ?>"><span class="svc-card__icon"><?= icon($s['icon']) ?></span><h3><?= e($s['title']) ?></h3><p><?= e(str_limit($s['summary'], 110)) ?></p><span class="link-arrow">Learn more <?= icon('arrow-right', 'icon icon--sm') ?></span></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if ($i['solutions']): ?>
        <div class="sol-strip fx-reveal"><h3><?= icon('shield-check', 'icon icon--sm') ?> Cybersecurity solutions</h3><ul><?php foreach (lines($i['solutions']) as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Consulting approach diagram -->
<?php if ($approach): ?>
<section class="section">
    <div class="container">
        <div class="section-head fx-reveal"><p class="eyebrow">Consulting approach</p><h2 class="h2">How we approach <?= e($i['name']) ?> engagements</h2></div>
        <ol class="flow flow--line">
            <?php foreach ($approach as $k => $a): ?><li class="flow__step fx-reveal" style="--i:<?= $k ?>"><span class="mono flow__n"><?= sprintf('%02d', $k + 1) ?></span><p><?= e($a) ?></p></li><?php endforeach; ?>
        </ol>
        <?php if ($i['outcomes']): ?><div class="outcomes fx-reveal"><h3><?= icon('check-circle', 'icon icon--sm icon--ok') ?> Expected business outcomes</h3><?php partial('list', ['text' => $i['outcomes']]); ?></div><?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($caseStudies): ?>
<section class="section section--tint"><div class="container"><div class="section-head fx-reveal"><p class="eyebrow">Related case studies</p></div><div class="case-grid">
<?php foreach ($caseStudies as $cs): ?><a class="case-card fx-reveal" href="<?= url('case-studies/' . $cs['slug']) ?>"><h3><?= e($cs['title']) ?></h3><p><?= e(str_limit($cs['outcome'], 140)) ?></p><span class="link-arrow">Read <?= icon('arrow-right', 'icon icon--sm') ?></span></a><?php endforeach; ?>
</div></div></section>
<?php endif; ?>
<section class="section section--tight"><div class="container"><div class="chips-row"><span class="muted">Other industries:</span><?php foreach ($others as $o): ?><a class="chip" href="<?= url('industries/' . $o['slug']) ?>"><?= e($o['name']) ?></a><?php endforeach; ?></div></div></section>
<?php partial('cta', ['title' => 'Discuss security and compliance for ' . $i['name'], 'text' => 'Tell us about your environment, regulators and timelines — a consultant will respond with practical next steps.']); ?>
