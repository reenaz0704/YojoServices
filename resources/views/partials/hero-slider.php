<?php /** @var array $slides */
if (!$slides) {
    $slides = [['label' => setting('company_name'), 'title' => setting('company_name'), 'subtitle' => setting('company_tagline'), 'description' => setting('company_intro'),
        'cta_label' => 'Explore Our Services', 'cta_url' => 'services', 'cta2_label' => null, 'cta2_url' => null, 'image' => null, 'theme' => 'navy', 'icon' => 'shield-check', 'duration_seconds' => 5]];
}
$count = count($slides);
// Per-slide presentation (wording comes from the slide record; these only add styling and the icon row)
$hsExtras = [
    0 => ['sub_pill' => true, 'bold' => ['digital security', 'compliance'], 'cta2_icon' => 'calendar',
        'feats' => [['shield-check', 'Security', 'Audits', 'services/offensive-security'], ['lock', 'Cybersecurity', 'Services', 'services/defensive-security'],
                    ['scale', 'Cyber Legal', 'Services', 'services/defensive-security'], ['shield', 'Compliance', 'Support', 'services/grc']]],
    1 => ['pill_last' => true, 'sub_tags' => true, 'bold' => ['audit and consulting services'], 'cta2_icon' => 'users',
        'feats' => [['shield-check', 'Audit', 'Services', 'services/grc'], ['file-text', 'Compliance', 'Advisory', 'services/grc'],
                    ['chart', 'Risk', 'Management', 'services/grc'], ['users', 'Trusted', 'Partnership', 'about']]],
    2 => ['title_bar' => true, 'sub_tags' => true, 'tag_icons' => ['globe', 'network', 'cloud', 'settings', 'server'], 'bold' => ['security assessments'], 'cta2_icon' => 'file-text'],
    3 => ['sub_line' => true, 'no_bar' => true, 'feat_cards' => true, 'cta2_icon' => 'calendar',
        'feats' => [['shield-check', 'Proactive', 'Threat Protection', 'services/defensive-security'], ['activity', 'Real-Time', 'Monitoring', 'services/defensive-security'],
                    ['rocket', 'Rapid Incident', 'Response', 'services/defensive-security/incident-response-advisory']]],
    4 => ['label_icon' => 'scale', 'sub_sm' => true, 'no_bar' => true],
];
?>
<section class="hs" data-hero-slider aria-roledescription="carousel" aria-label="Yojo Solutions services">
    <div class="hs__stage">
        <?php foreach ($slides as $i => $s): $dur = max(2, min(3, (int) $s['duration_seconds'])); ?>
        <article class="hs__slide hs__slide--<?= e($s['theme']) ?> <?= $i === 0 ? 'is-active' : '' ?>" role="group" aria-roledescription="slide"
                 aria-label="<?= $i + 1 ?> of <?= $count ?>: <?= e($s['label']) ?>" data-duration="<?= $dur ?>" data-icon="<?= e($s['icon'] ?: 'shield-check') ?>" <?= $i === 0 ? '' : 'aria-hidden="true"' ?>>
            <div class="hs__bg" aria-hidden="true">
                <?php if ($s['image']): ?><img class="hs__img" src="<?= e(media_url($s['image'])) ?>" alt="" <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>><?php endif; ?>
                <div class="hs__grad"></div>
                <div class="grid-lines"></div>
                <span class="hs__orb hs__orb--1"></span><span class="hs__orb hs__orb--2"></span>
            </div>
            <div class="container hs__inner">
                <div class="hs__copy">
                    <?php $x = $hsExtras[$i] ?? []; ?><p class="hs__label"><span class="hs__num mono<?= !empty($x['label_icon']) ? ' hs__num--icon' : '' ?>"><?= !empty($x['label_icon']) ? icon($x['label_icon'], 'icon icon--sm') : sprintf('%02d', $i + 1) ?></span><?= e($s['label']) ?></p>
                    <?php $x = $hsExtras[$i] ?? []; $words = preg_split('/\s+/', trim($s['title'])); $pill = $x['pill_last'] ?? false; ?>
                    <h<?= $i === 0 ? '1' : '2' ?> class="hs__title<?= $pill ? ' hs__title--pill' : '' ?><?= !empty($x['title_bar']) ? ' hs__title--bar' : '' ?>"><?php foreach ($words as $w => $word): ?><span class="hs__word<?= $pill && $w === count($words) - 1 ? ' hs__word--pill' : '' ?>" style="--w:<?= $w ?>"><span><?= e($word) ?></span></span> <?php endforeach; ?></h<?= $i === 0 ? '1' : '2' ?>>
                    <?php if ($s['subtitle']): ?>
                        <?php if (!empty($x['sub_tags'])): ?>
                        <p class="hs__sub hs__sub--tags<?= !empty($x['tag_icons']) ? ' hs__sub--icons' : '' ?>"><?php foreach (array_values(array_filter(array_map('trim', preg_split('/[·|]/u', $s['subtitle'])))) as $ti => $tag): ?><span><?= !empty($x['tag_icons'][$ti]) ? icon($x['tag_icons'][$ti], 'icon') : '' ?><?= e($tag) ?></span><?php endforeach; ?></p>
                        <?php else: ?>
                        <p class="hs__sub<?= !empty($x['sub_pill']) ? ' hs__sub--pill' : '' ?><?= !empty($x['sub_line']) ? ' hs__sub--line' : '' ?><?= !empty($x['sub_sm']) ? ' hs__sub--sm' : '' ?>"><?= e($s['subtitle']) ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($s['description']): $desc = e($s['description']); foreach ($x['bold'] ?? [] as $bw) { $desc = str_replace(e($bw), '<strong>' . e($bw) . '</strong>', $desc); } ?>
                    <p class="hs__text<?= $x && empty($x['no_bar']) ? ' hs__text--bar' : '' ?>"><?= $desc ?></p>
                    <?php endif; ?>
                    <?php if (!empty($x['feats'])): ?>
                    <ul class="hs__feats<?= !empty($x['feat_cards']) ? ' hs__feats--cards' : '' ?>">
                        <?php foreach ($x['feats'] as $f): ?>
                        <li><a href="<?= e(site_link($f[3])) ?>" tabindex="-1"><span class="hs__feat-ic"><?= icon($f[0]) ?></span><span><?= e($f[1]) ?><br><?= e($f[2]) ?></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <div class="hs__actions">
                        <a class="btn btn--accent btn--lg hs__cta" href="<?= e(site_link($s['cta_url'])) ?>" <?= $i ? 'tabindex="-1"' : '' ?>><span><?= e($s['cta_label']) ?></span> <?= icon('arrow-right', 'icon icon--sm') ?></a>
                        <?php if (!empty($s['cta2_label'])): ?><a class="btn btn--outline-light btn--lg" href="<?= e(site_link($s['cta2_url'])) ?>" <?= $i ? 'tabindex="-1"' : '' ?>><?= !empty($x['cta2_icon']) ? icon($x['cta2_icon'], 'icon icon--sm') . ' ' : '' ?><?= e($s['cta2_label']) ?></a><?php endif; ?>
                    </div>
                </div>
                <div class="hs__art" aria-hidden="true">
                    <div class="hs__radar">
                        <span class="hs__ring hs__ring--1"></span><span class="hs__ring hs__ring--2"></span><span class="hs__ring hs__ring--3"></span>
                        <span class="hs__sweep"></span>
                        <span class="hs__core"><?= icon($s['icon'] ?: 'shield-check') ?></span>
                        <span class="hs__sat hs__sat--1"><?= icon('lock', 'icon icon--sm') ?></span>
                        <span class="hs__sat hs__sat--2"><?= icon('check', 'icon icon--sm') ?></span>
                        <span class="hs__sat hs__sat--3"><?= icon('activity', 'icon icon--sm') ?></span>
                    </div>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <canvas class="hs3d" aria-hidden="true"></canvas>
    <?php if ($count > 1): ?>
    <button class="hs__edge hs__edge--prev" type="button" data-hs-prev aria-label="Previous slide"><?= icon('chevron-left') ?></button>
    <button class="hs__edge hs__edge--next" type="button" data-hs-next aria-label="Next slide"><?= icon('chevron-right') ?></button>
    <?php endif; ?>
    <p class="sr-only" aria-live="polite" data-hs-live></p>
</section>
