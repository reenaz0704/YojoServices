<?php partial('page-hero', [
    'title' => 'Our Audit, Cybersecurity & Consulting Services',
    'eyebrow' => 'Services',
    'text' => 'Comprehensive security, audit, compliance and technology consulting solutions designed to help organizations manage risks and strengthen digital trust.',
    'crumbs' => ['Services' => null],
    'actions' => [['Request a Consultation', url('book-consultation')], ['Take the readiness assessment', url('security-assessment')]],
]); ?>
<section class="section svc-deck-sec" id="all-services">
    <div class="container">
        <div class="section-head fx-reveal"><p class="eyebrow">All services</p><h2 class="h2">Choose a division to see its services</h2></div>
        <div class="svc-deck" data-svc-deck>
            <?php foreach ($categories as $i => $c): ?>
            <article class="svc-dcard" style="--i:<?= $i ?>">
                <button class="svc-dcard__head" type="button" aria-expanded="false" aria-controls="dl-<?= e($c['slug']) ?>">
                    <span class="svc-dcard__n mono"><?= sprintf('%02d', $i + 1) ?></span>
                    <span class="svc-dcard__icon"><?= icon($c['icon']) ?></span>
                    <span class="svc-dcard__txt"><strong><?= e($c['name']) ?></strong><small><?= count($c['services']) ?> services</small></span>
                    <span class="svc-dcard__plus" aria-hidden="true"></span>
                </button>
                <div class="svc-dcard__drop" id="dl-<?= e($c['slug']) ?>">
                    <div class="svc-dcard__inner">
                        <ul>
                            <?php foreach ($c['services'] as $k => $sv): ?>
                            <li style="--k:<?= $k ?>"><a href="<?= url('services/' . $c['slug'] . '/' . $sv['slug']) ?>"><?= icon($sv['icon'], 'icon icon--sm') ?><span><?= e($sv['title']) ?></span><?= icon('arrow-up-right', 'icon icon--xs') ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <a class="svc-dcard__all" href="<?= url('services/' . $c['slug']) ?>">Open <?= e($c['name']) ?> <?= icon('arrow-right', 'icon icon--xs') ?></a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="section svc-page" data-svc-tabs>
    <div class="container">
        <div class="svc-tabs fx-reveal" role="tablist" aria-label="Service divisions">
            <?php foreach ($categories as $i => $c): ?>
            <button class="svc-tab <?= $i === 0 ? 'is-active' : '' ?>" role="tab" id="tab-<?= e($c['slug']) ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="div-<?= e($c['slug']) ?>" data-svc-tab="<?= e($c['slug']) ?>">
                <span class="svc-tab__n mono"><?= sprintf('%02d', $i + 1) ?></span>
                <span class="svc-tab__icon"><?= icon($c['icon']) ?></span>
                <span class="svc-tab__label"><?= e($c['name']) ?></span>
            </button>
            <?php endforeach; ?>
        </div>

        <label class="search-input svc-search"><?= icon('search') ?><input type="search" placeholder="Search all <?= array_sum(array_column($categories, 'service_count')) ?> services…" data-svc-search aria-label="Search services"></label>

        <?php foreach ($categories as $i => $c): ?>
        <div class="svc-div <?= $i === 0 ? 'is-active' : '' ?>" id="div-<?= e($c['slug']) ?>" role="tabpanel" aria-labelledby="tab-<?= e($c['slug']) ?>" data-svc-panel="<?= e($c['slug']) ?>">
            <header class="svc-div__head">
                <div>
                    <p class="eyebrow">Division <?= sprintf('%02d', $i + 1) ?></p>
                    <h2 class="h2"><?= e($c['name']) ?></h2>
                    <p class="lead"><?= e($c['description']) ?></p>
                </div>
                <a class="btn btn--ghost" href="<?= url('services/' . $c['slug']) ?>">Division overview <?= icon('arrow-right', 'icon icon--sm') ?></a>
            </header>
            <?php if ($c['page_type'] === 'legal' || $c['page_type'] === 'security'): ?>
            <div class="notice notice--compact"><span><?= icon($c['page_type'] === 'legal' ? 'scale' : 'shield-alert') ?></span><div><p><?= e($c['engagement_note']) ?></p></div></div>
            <?php endif; ?>
            <div class="svc-acc">
                <?php foreach ($c['services'] as $k => $s): ?>
                <details class="svc-acc__item" data-svc-item data-text="<?= e(strtolower($s['title'] . ' ' . $s['summary'] . ' ' . $c['name'])) ?>">
                    <summary>
                        <span class="svc-acc__icon"><?= icon($s['icon']) ?></span>
                        <span class="svc-acc__title"><strong><?= e($s['title']) ?></strong><small><?= e($s['engagement_type']) ?></small></span>
                        <span class="svc-acc__plus" aria-hidden="true"></span>
                    </summary>
                    <div class="svc-acc__body">
                        <p><?= e($s['summary']) ?></p>
                        <?php $acts = array_slice(lines($s['requirements'] ?: $s['security_areas']), 0, 4); if ($acts): ?>
                        <ul class="checks"><?php foreach ($acts as $a): $it = split_item($a); ?><li><?= icon('check', 'icon icon--xs') ?><?= e($it[0]) ?></li><?php endforeach; ?></ul>
                        <?php endif; ?>
                        <div class="svc-acc__actions">
                            <a class="btn btn--primary btn--sm" href="<?= url('services/' . $c['slug'] . '/' . $s['slug']) ?>">View service details <?= icon('arrow-right', 'icon icon--xs') ?></a>
                            <a class="btn btn--ghost btn--sm" href="<?= url('book-consultation?service=' . $s['slug']) ?>">Request Consultation</a>
                        </div>
                    </div>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <p class="empty" data-svc-empty hidden>No services match your search. <a href="<?= url('contact') ?>">Tell us what you need →</a></p>
    </div>
</section>
<?php partial('cta'); ?>
