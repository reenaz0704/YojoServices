<?php
$values = [];
foreach (lines(setting('core_values')) as $line) { $values[] = split_item($line); }
$valueIcons = ['scale', 'sparkle', 'shield-check', 'star', 'users', 'eye', 'refresh', 'target', 'compass'];
$offer = lines(setting('who_offerings'));
?>
<section class="who-hero">
    <div class="who-hero__bg" aria-hidden="true"><div class="grid-lines"></div><span class="hs__orb hs__orb--1"></span></div>
    <div class="container who-hero__inner">
        <nav class="crumbs" aria-label="Breadcrumb"><a href="<?= url('/') ?>">Home</a><span>/</span><span aria-current="page">Who We Are</span></nav>
        <p class="eyebrow eyebrow--light">Who we are</p>
        <h1 class="who-hero__title"><span class="fx-line">Cybersecurity.</span> <span class="fx-line who-rot" style="--i:1" data-rotate='["Consulting.","Audit.","Services."]'><span class="sr-only">Consulting. Audit. Services.</span><span class="who-rot__txt" aria-hidden="true">Consulting.</span><i class="who-rot__caret" aria-hidden="true"></i></span> <span class="fx-line grad-text" style="--i:2">Trust.</span></h1>
        <p class="who-hero__text"><?= e(setting('who_title')) ?></p>
    </div>
</section>

<!-- Split storytelling -->
<section class="section story" data-scene="wave">
    <div class="container story__grid">
        <div class="story__visual fx-reveal">
            <div class="story__frame" data-tilt>
                <div class="story__fx" aria-hidden="true">
                    <span class="sfx-glow"></span>
                    <span class="sfx-ring sfx-ring--1"></span><span class="sfx-ring sfx-ring--2"></span><span class="sfx-ring sfx-ring--3"></span>
                    <span class="sfx-scan"></span>
                    <?php for ($k = 0; $k < 14; $k++): ?><i class="sfx-spark" style="--k:<?= $k ?>"></i><?php endfor; ?>
                </div>
                <img class="story__img" src="<?= asset('images/about/audit-visual.webp') ?>" width="1010" height="890" loading="lazy" decoding="async"
                     alt="Audit report on a clipboard, laptop with analytics, magnifying glass and server stack in front of a globe">
                <span class="story__chip story__chip--1"><?= icon('radar', 'icon icon--sm') ?> Assessments &amp; audits</span>
                <span class="story__chip story__chip--2"><?= icon('lock', 'icon icon--sm') ?> Enterprise security</span>
                <span class="story__chip story__chip--3"><?= icon('settings', 'icon icon--sm') ?> IT consulting</span>
            </div>
        </div>
        <div class="story__copy">
            <p class="eyebrow fx-reveal">Our story</p>
            <p class="story__lead fx-reveal" style="--i:1"><?= e(setting('who_text')) ?></p>
            <p class="fx-reveal" style="--i:2"><?= e(setting('who_text_2')) ?></p>
            <?php if ($offer): ?>
            <ul class="offer-list">
                <?php foreach ($offer as $k => $o): ?><li class="fx-reveal" style="--i:<?= $k + 3 ?>"><span class="offer-list__n mono"><?= sprintf('%02d', $k + 1) ?></span><?= e($o) ?></li><?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Mission & vision -->
<section class="mv">
    <div class="mv__bg" aria-hidden="true"><div class="grid-lines"></div></div>
    <div class="container mv__grid">
        <article class="mv__item fx-reveal">
            <span class="mv__label"><span class="mv__icon"><?= icon('target') ?></span>Our Mission</span>
            <p class="mv__text"><?= e(setting('mission')) ?></p>
        </article>
        <div class="mv__divider" aria-hidden="true"><svg viewBox="0 0 2 200" preserveAspectRatio="none"><line x1="1" y1="0" x2="1" y2="200"/></svg></div>
        <article class="mv__item fx-reveal" style="--i:1">
            <span class="mv__label"><span class="mv__icon"><?= icon('eye') ?></span>Our Vision</span>
            <p class="mv__text"><?= e(setting('vision')) ?></p>
        </article>
    </div>
</section>

<!-- Core values: interactive reveal -->
<?php if ($values): ?>
<section class="section values-sec">
    <div class="container">
        <div class="section-head fx-reveal"><p class="eyebrow">Our core values</p><h2 class="h2 h2--xl">What guides every engagement</h2></div>
        <div class="vals" data-values>
            <ol class="vals__list" role="tablist" aria-label="Core values">
                <?php foreach ($values as $k => $v): ?>
                <li><button class="vals__btn <?= $k === 0 ? 'is-active' : '' ?>" role="tab" aria-selected="<?= $k === 0 ? 'true' : 'false' ?>" aria-controls="val-<?= $k ?>" data-value-tab="<?= $k ?>">
                    <span class="mono"><?= sprintf('%02d', $k + 1) ?></span><?= e($v[0]) ?>
                </button></li>
                <?php endforeach; ?>
            </ol>
            <div class="vals__stage">
                <?php foreach ($values as $k => $v): ?>
                <div class="vals__panel <?= $k === 0 ? 'is-active' : '' ?>" id="val-<?= $k ?>" role="tabpanel" data-value-panel="<?= $k ?>" <?= $k === 0 ? '' : 'hidden' ?>>
                    <span class="vals__icon"><?= icon($valueIcons[$k % count($valueIcons)]) ?></span>
                    <h3><?= e($v[0]) ?></h3>
                    <p><?= e($v[1]) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- How we work -->
<section class="section section--tint">
    <div class="container">
        <div class="section-head fx-reveal"><p class="eyebrow">How we work</p><h2 class="h2">Understand. Assess. Improve. Verify.</h2></div>
        <ol class="flow">
            <?php foreach ([['Understand', 'Your business model, obligations, technology and risk appetite come first.', 'users'], ['Assess', 'Evidence-based review and authorised testing against agreed criteria.', 'search'], ['Improve', 'Practical, prioritised remediation with hands-on support when you need it.', 'settings'], ['Verify', 'Follow-up and retesting so improvements are real and lasting.', 'check-circle']] as $i => $a): ?>
            <li class="flow__step fx-reveal" style="--i:<?= $i ?>"><span class="flow__icon"><?= icon($a[2]) ?></span><span class="mono flow__n">0<?= $i + 1 ?></span><h3><?= $a[0] ?></h3><p><?= $a[1] ?></p></li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head section-head--row fx-reveal"><div><p class="eyebrow">What we do</p><h2 class="h2">Our service divisions</h2></div><a class="btn btn--ghost" href="<?= url('services') ?>">All services <?= icon('arrow-right', 'icon icon--sm') ?></a></div>
        <div class="div-grid">
            <?php foreach ($categories as $i => $c): ?>
            <a class="div-card fx-reveal" href="<?= url('services/' . $c['slug']) ?>" style="--i:<?= $i ?>"><span class="div-card__num mono"><?= sprintf('%02d', $i + 1) ?></span><span class="div-card__icon"><?= icon($c['icon']) ?></span><h3><?= e($c['name']) ?></h3><p><?= e($c['summary']) ?></p><span class="div-card__foot"><span><?= (int) $c['service_count'] ?> services</span><span class="link-arrow">Explore <?= icon('arrow-right', 'icon icon--sm') ?></span></span></a>
            <?php endforeach; ?>
        </div>
        <div class="notice fx-reveal" style="margin-top:40px"><span><?= icon('info') ?></span><div><strong>Our commitment to independence</strong><p><?= e(setting('legal_disclaimer')) ?> Certifications, memberships and partnerships are displayed on this website only once verified.</p></div></div>
    </div>
</section>
<?php partial('cta'); ?>
