<?php
use App\Models\Service;
$menu = Service::menu();
$links = [
    ['/about', 'Who We Are'],
    'services',
    ['/industries', 'Industry Expertise'],
    ['/insights', 'Latest Insights'],
    ['/faq', 'FAQ'],
    ['/contact', 'Contact'],
];
?>
<header class="nav" id="siteNav">
    <div class="nav__bar">
        <div class="container nav__inner">
            <a class="nav__brand" href="<?= url('/') ?>" aria-label="<?= e(setting('company_name')) ?> home">
                <img src="<?= asset('images/logo.png') ?>" alt="<?= e(setting('company_name')) ?> logo" width="176" height="40">
                <span class="sr-only"><?= e(setting('company_name')) ?></span>
            </a>

            <nav class="nav__menu" id="navMenu" aria-label="Primary">
                <ul class="nav__list">
                    <?php foreach ($links as $l): ?>
                    <?php if ($l === 'services'): ?>
                    <li class="has-mega">
                        <button class="nav__link nav__trigger <?= is_active('/services') ? 'is-active' : '' ?>" aria-expanded="false" aria-controls="megaServices">
                            Services <?= icon('chevron-down', 'icon icon--xs') ?>
                        </button>
                        <div class="mega" id="megaServices">
                            <div class="container mega__inner">
                                <div class="mega__tabs" role="tablist" aria-label="Service divisions">
                                    <?php $i = 0; foreach ($menu as $slug => $g): ?>
                                    <button class="mega__tab <?= $i === 0 ? 'is-active' : '' ?>" role="tab" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="mega-<?= e($slug) ?>" data-mega-tab="<?= e($slug) ?>">
                                        <span class="mega__icon"><?= icon($g['icon']) ?></span>
                                        <span><strong><?= e($g['label']) ?></strong><small><?= count($g['items']) ?> services</small></span>
                                        <?= icon('chevron-right', 'icon icon--xs mega__chev') ?>
                                    </button>
                                    <?php $i++; endforeach; ?>
                                    <a class="mega__all" href="<?= url('services') ?>#all-services">View all services <?= icon('arrow-right', 'icon icon--xs') ?></a>
                                </div>
                                <?php $i = 0; foreach ($menu as $slug => $g): ?>
                                <div class="mega__panel <?= $i === 0 ? 'is-active' : '' ?>" id="mega-<?= e($slug) ?>" role="tabpanel" data-mega-panel="<?= e($slug) ?>">
                                    <div class="mega__panel-head">
                                        <a class="mega__title" href="<?= url('services/' . $slug) ?>"><?= e($g['label']) ?> <?= icon('arrow-up-right', 'icon icon--xs') ?></a>
                                        <p><?= e($g['tagline']) ?></p>
                                    </div>
                                    <ul class="mega__list">
                                        <?php foreach ($g['items'] as $s): ?>
                                        <li><a href="<?= Service::url($s) ?>"><?= icon($s['icon'], 'icon icon--sm') ?><span><?= e($s['title']) ?></span></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php $i++; endforeach; ?>
                                <aside class="mega__aside">
                                    <p class="eyebrow eyebrow--light">Free self-assessment</p>
                                    <h3>How audit-ready are you?</h3>
                                    <p>Answer 14 questions and get an indicative readiness summary in under five minutes.</p>
                                    <a class="btn btn--accent btn--sm" href="<?= url('security-assessment') ?>">Start assessment <?= icon('arrow-right', 'icon icon--sm') ?></a>
                                </aside>
                            </div>
                        </div>
                    </li>
                    <?php else: ?>
                    <li><a class="nav__link <?= ($l[0] === '/' ? current_path() === '/' : is_active($l[0])) ? 'is-active' : '' ?>" href="<?= url($l[0]) ?>"><?= e($l[1]) ?></a></li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <div class="nav__cta nav__cta--mobile">
                    <a class="btn btn--ghost" href="<?= url('security-assessment') ?>">Free Security Assessment</a>
                    <a class="btn btn--primary" href="<?= url('book-consultation') ?>">Book a Consultation</a>
                </div>
            </nav>

            <div class="nav__cta">
                <a class="btn btn--ghost btn--sm nav__assess" href="<?= url('security-assessment') ?>"><?= icon('shield-check', 'icon icon--sm') ?> Free Security Assessment</a>
                <a class="btn btn--primary btn--sm" href="<?= url('book-consultation') ?>"><?= icon('calendar', 'icon icon--sm') ?> Book a Consultation</a>
            </div>
            <button class="nav__toggle" id="navToggle" aria-label="Open menu" aria-expanded="false" aria-controls="navMenu"><span></span><span></span><span></span></button>
        </div>
    </div>
</header>
