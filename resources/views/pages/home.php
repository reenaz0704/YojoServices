<?php
$methodology = [
    ['Initial Consultation', 'Understand your objectives, regulatory drivers, timelines and constraints.', 'users'],
    ['Scope Definition', 'Agree systems, locations, frameworks, rules of engagement and deliverables in writing.', 'target'],
    ['Assessment & Evidence Collection', 'Interviews, walkthroughs, technical testing and sample-based evidence review.', 'search'],
    ['Risk and Gap Analysis', 'Rate findings by likelihood and impact against the agreed criteria.', 'chart'],
    ['Audit Reporting', 'Clear executive and technical reports with prioritised, practical recommendations.', 'file-text'],
    ['Remediation & Follow-up', 'Support fixes, retest and validate closure so improvements stick.', 'refresh'],
];
$why = [
    ['Structured audit methodology', 'Repeatable, documented procedures aligned to recognised standards.', 'layers'],
    ['Risk-based assessments', 'Effort focused where the business impact is highest.', 'target'],
    ['Experienced consulting professionals', 'Practitioners who have implemented the controls they assess.', 'users'],
    ['Practical remediation', 'Recommendations sized to your team, budget and technology.', 'settings'],
    ['Compliance-focused approach', 'Every finding mapped to the frameworks your customers ask for.', 'shield-check'],
    ['Transparent reporting', 'Clear severity ratings, evidence and no hidden assumptions.', 'eye'],
    ['Continuous improvement', 'Follow-up cycles that turn audits into lasting maturity.', 'refresh'],
];
?>
<!-- ============ HERO SLIDER ============ -->
<?php partial('hero-slider', ['slides' => $slides]); ?>

<!-- ============ FRAMEWORKS ============ -->
<?php partial('frameworks', ['frameworks' => $frameworks]); ?>

<?php if ($trust): ?>
<section class="trust">
    <div class="container">
        <p class="trust__label">Recognitions &amp; partners</p>
        <div class="trust__logos">
            <?php foreach ($trust as $t): ?>
            <a class="trust__logo" <?= $t['url'] ? 'href="' . e($t['url']) . '" target="_blank" rel="noopener"' : '' ?> title="<?= e(ucfirst($t['type'])) ?>">
                <?php if ($t['logo']): ?><img src="<?= e(media_url($t['logo'])) ?>" alt="<?= e($t['name']) ?>" loading="lazy"><?php else: ?><span><?= e($t['name']) ?></span><?php endif; ?>
                <small><?= e(ucfirst($t['type'])) ?><?= $t['is_verified'] ? ' · verified' : '' ?></small>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ WHO WE ARE (teaser) ============ -->
<section class="section who-teaser">
    <div class="container who-teaser__grid">
        <div class="who-teaser__big fx-reveal" aria-hidden="true"><span>Secure.</span><span>Resilient.</span><span class="grad-text">Trusted.</span></div>
        <div class="fx-reveal" style="--i:1">
            <p class="eyebrow">Who we are</p>
            <h2 class="h2 h2--xl"><?= e(setting('who_title', setting('about_title'))) ?></h2>
            <p class="lead"><?= e(setting('who_text', setting('about_text'))) ?></p>
            <div class="who-teaser__mv">
                <div><h3><?= icon('target', 'icon icon--sm') ?> Our mission</h3><p><?= e(setting('mission')) ?></p></div>
                <div><h3><?= icon('eye', 'icon icon--sm') ?> Our vision</h3><p><?= e(setting('vision')) ?></p></div>
            </div>
            <a class="btn btn--primary" href="<?= url('about') ?>">More about Yojo Solutions <?= icon('arrow-right', 'icon icon--sm') ?></a>
        </div>
    </div>
</section>

<!-- ============ CORE SERVICES ============ -->
<section class="section section--tint" id="services">
    <div class="container">
        <div class="section-head section-head--row fx-reveal">
            <div><p class="eyebrow">Our services</p><h2 class="h2 h2--xl">Five divisions. One integrated view of risk.</h2>
            <p>From ISO compliance to authorised security testing, cyber-legal support and IT consulting — each practice strengthens the next.</p></div>
            <a class="btn btn--ghost" href="<?= url('services') ?>">All services <?= icon('arrow-right', 'icon icon--sm') ?></a>
        </div>
        <div class="div-grid">
            <?php foreach ($categories as $i => $c): ?>
            <a class="div-card fx-reveal" href="<?= url('services/' . $c['slug']) ?>" style="--i:<?= $i ?>">
                <span class="div-card__num mono"><?= sprintf('%02d', $i + 1) ?></span>
                <span class="div-card__icon"><?= icon($c['icon']) ?></span>
                <h3><?= e($c['name']) ?></h3>
                <p><?= e($c['summary']) ?></p>
                <span class="div-card__foot"><span><?= (int) $c['service_count'] ?> services</span><span class="link-arrow">Explore <?= icon('arrow-right', 'icon icon--sm') ?></span></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ WHY CHOOSE US ============ -->
<section class="section section--dark">
    <div class="section__bg" aria-hidden="true"><div class="grid-lines"></div></div>
    <div class="container why">
        <div class="why__intro reveal">
            <p class="eyebrow eyebrow--light">Why choose us</p>
            <h2 class="h2">Assurance that stands up to scrutiny — and makes you safer.</h2>
            <p>Compliance alone doesn't stop incidents. We design every engagement to produce evidence auditors accept <em>and</em> improvements attackers notice.</p>
            <a class="btn btn--accent" href="<?= url('book-consultation') ?>">Talk to a consultant</a>
        </div>
        <?php require dirname(__DIR__) . '/partials/why-art.php'; ?>
        <div class="why__grid">
            <?php foreach ($why as $i => $w): ?>
            <div class="why__item reveal" style="--i:<?= $i ?>"><figure class="why__pic" aria-hidden="true"><?php $wimg = 'images/why/' . $w[2] . '.webp'; if (is_file(BASE_PATH . '/public/assets/' . $wimg)): ?><img class="why__img" src="<?= asset($wimg) ?>" alt="" loading="lazy" decoding="async"><span class="why__shine"></span><span class="why__scan"></span><?php for ($k = 0; $k < 6; $k++): ?><i class="why__spark" style="--k:<?= $k ?>"></i><?php endfor; ?><?php else: ?><?= $why_art[$w[2]] ?? '' ?><?php endif; ?></figure><span class="why__icon"><?= icon($w[2]) ?></span><div><h3><?= e($w[0]) ?></h3><p><?= e($w[1]) ?></p></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ METHODOLOGY ============ -->
<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <p class="eyebrow">Our audit methodology</p>
            <h2 class="h2">Six disciplined stages, from first call to verified closure</h2>
        </div>
        <ol class="timeline" data-timeline>
            <?php foreach ($methodology as $i => $m): ?>
            <li class="timeline__step reveal" style="--i:<?= $i ?>">
                <div class="timeline__marker"><span class="mono"><?= $i + 1 ?></span></div>
                <div class="timeline__card"><span class="timeline__icon"><?= icon($m[2]) ?></span><h3><?= e($m[0]) ?></h3><p><?= e($m[1]) ?></p></div>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- ============ INDUSTRIES ============ -->
<section class="section section--tint ind-sec">
    <div class="container">
        <div class="section-head section-head--row fx-reveal">
            <div><p class="eyebrow">Industry expertise</p><h2 class="h2 h2--xl">Security programmes shaped by your sector</h2>
            <p>Every industry faces different threats, regulators and constraints. Explore how we approach yours.</p></div>
            <a class="btn btn--ghost" href="<?= url('industries') ?>">All industries <?= icon('arrow-right', 'icon icon--sm') ?></a>
        </div>
        <div class="icard-grid">
            <?php foreach ($industries as $i => $ind) { partial('industry-card', ['ind' => $ind, 'i' => $i]); } ?>
        </div>
    </div>
</section>

<!-- ============ STATS ============ -->
<?php if ($stats): ?>
<section class="stats">
    <div class="container">
        <div class="stats__grid">
            <?php foreach ($stats as $s): ?>
            <div class="stat reveal">
                <strong class="stat__value"><span data-count="<?= (int) $s['value'] ?>">0</span><?= e($s['suffix']) ?></strong>
                <span class="stat__label"><?= e($s['label']) ?></span>
                <?php if ($s['description']): ?><small><?= e($s['description']) ?></small><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="stats__note"><?= e(setting('stats_note')) ?></p>
    </div>
</section>
<?php endif; ?>

<!-- ============ CASE STUDIES ============ -->
<?php if ($caseStudies): ?>
<section class="section case-sec" data-scene="none">
    <div class="container">
        <div class="section-head section-head--row reveal">
            <div><p class="eyebrow">Case studies</p><h2 class="h2">How engagements create measurable improvement</h2></div>
            <a class="btn btn--ghost" href="<?= url('case-studies') ?>">View all <?= icon('arrow-right', 'icon icon--sm') ?></a>
        </div>
        <div class="case-grid">
            <?php foreach ($caseStudies as $cs): ?>
            <article class="case-card reveal">
                <div class="case-card__top">
                    <span class="tag"><?= e($cs['industry_name'] ?: 'Cross-industry') ?></span>
                    <?php if (!$cs['is_authorized']): ?><span class="badge badge--muted" title="Not a client record — shows how a typical engagement is structured"><?= icon('info', 'icon icon--xs') ?> <?= e($cs['client_label'] ?: 'Illustrative') ?></span><?php else: ?><span class="badge badge--ok"><?= icon('check', 'icon icon--xs') ?> Client-approved</span><?php endif; ?>
                </div>
                <h3><?= e($cs['title']) ?></h3>
                <dl class="case-card__dl">
                    <div><dt>Challenge</dt><dd><?= e(str_limit($cs['challenge'], 120)) ?></dd></div>
                    <div><dt>Assessment</dt><dd><?= e(str_limit($cs['assessment'], 100)) ?></dd></div>
                    <div class="case-card__outcome"><dt>Outcome</dt><dd><?= e(str_limit($cs['outcome'], 120)) ?></dd></div>
                </dl>
                <a class="link-arrow" href="<?= url('case-studies/' . $cs['slug']) ?>">Read case study <?= icon('arrow-right', 'icon icon--sm') ?></a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ TESTIMONIALS ============ -->
<?php if ($testimonials): ?>
<section class="section section--tint">
    <div class="container">
        <div class="section-head reveal"><p class="eyebrow">Client voices</p><h2 class="h2">What organisations say about working with us</h2></div>
        <div class="slider" data-slider>
            <div class="slider__track">
                <?php foreach ($testimonials as $t): ?>
                <figure class="quote-card">
                    <span class="quote-card__mark"><?= icon('quote') ?></span>
                    <?php if (!$t['is_verified']): ?><span class="badge badge--muted quote-card__badge">Sample — replace in admin</span><?php endif; ?>
                    <blockquote><?= e($t['content']) ?></blockquote>
                    <figcaption>
                        <?php if ($t['image']): ?><img src="<?= e(media_url($t['image'])) ?>" alt="" width="48" height="48" loading="lazy"><?php else: ?><span class="avatar"><?= e(mb_substr(preg_replace('/\[.*?\]\s*/', '', $t['client_name']), 0, 1)) ?></span><?php endif; ?>
                        <span><strong><?= e($t['client_name']) ?></strong><small><?= e(trim($t['designation'] . ', ' . $t['company'], ', ')) ?></small></span>
                    </figcaption>
                </figure>
                <?php endforeach; ?>
            </div>
            <div class="slider__nav">
                <button class="slider__btn" data-prev aria-label="Previous testimonial"><?= icon('chevron-left') ?></button>
                <div class="slider__dots" data-dots></div>
                <button class="slider__btn" data-next aria-label="Next testimonial"><?= icon('chevron-right') ?></button>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ INSIGHTS ============ -->
<?php if ($posts): ?>
<section class="section insights-sec">
    <div class="container">
        <div class="section-head section-head--row fx-reveal">
            <div><p class="eyebrow">Knowledge</p><h2 class="h2 h2--xl"><?= e(setting('insights_title', 'Latest Insights')) ?></h2><p><?= e(setting('insights_text')) ?></p></div>
            <a class="btn btn--ghost" href="<?= url('insights') ?>">All insights <?= icon('arrow-right', 'icon icon--sm') ?></a>
        </div>
        <div class="post-grid post-grid--lg"><?php foreach ($posts as $k => $post) { partial('post-card', ['post' => $post, 'i' => $k]); } ?></div>
    </div>
</section>
<?php endif; ?>

<!-- ============ FAQ ============ -->
<section class="section section--tint">
    <div class="container faq-split">
        <div class="reveal">
            <p class="eyebrow">FAQs</p>
            <h2 class="h2">Questions we're often asked</h2>
            <p class="lead">Can't find what you're looking for? Our consultants are happy to help.</p>
            <a class="btn btn--ghost" href="<?= url('faq') ?>">Browse all FAQs by category <?= icon('arrow-right', 'icon icon--sm') ?></a>
        </div>
        <div class="reveal"><?php partial('faq-list', ['faqs' => $faqs, 'openFirst' => true]); ?></div>
    </div>
</section>

<?php partial('cta'); ?>

<!-- ============ FRAMEWORK LOGOS (above footer) ============ -->
<section class="fw-foot" aria-label="Frameworks we support"><?php partial('framework-marquee', ['frameworks' => $frameworks, 'reverse' => true]); ?></section>
