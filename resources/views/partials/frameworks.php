<?php /** @var array $frameworks */
if (!$frameworks) return;
$names = array_column($frameworks, 'short_name');
?>
<section class="fw" id="frameworks">
    <?php partial('framework-marquee', ['frameworks' => $frameworks]); ?>

    <div class="container">
        <div class="fw__head fx-reveal">
            <p class="eyebrow eyebrow--light">Standards &amp; regulations</p>
            <h2 class="fw__title"><?= e(setting('frameworks_title', 'Frameworks, Standards & Regulations We Help Organizations Meet')) ?></h2>
            <p class="fw__lead"><?= e(setting('frameworks_text')) ?></p>
        </div>

        <div class="fw__board">
            <svg class="fw__links" aria-hidden="true" preserveAspectRatio="none" data-fw-links></svg>
            <div class="fw__grid">
                <?php foreach ($frameworks as $i => $f): ?>
                <a class="fw-card fx-reveal" style="--i:<?= $i ?>" href="<?= url('frameworks/' . $f['slug']) ?>" data-fw-open="<?= e($f['slug']) ?>" aria-haspopup="dialog">
                    <span class="fw-card__glow" aria-hidden="true"></span>
                    <span class="fw-card__top"><?php $logo = 'images/frameworks/' . $f['slug'] . '.png'; if (is_file(BASE_PATH . '/public/assets/' . $logo)): ?><span class="fw-card__logo"><img src="<?= asset($logo) ?>" alt="<?= e($f['short_name']) ?> logo" loading="lazy" decoding="async"></span><?php else: ?><span class="fw-card__icon"><?= icon($f['icon']) ?></span><?php endif; ?><span class="fw-card__region"><?= e($f['region']) ?></span></span>
                    <strong class="fw-card__name"><?= e($f['short_name']) ?></strong>
                    <span class="fw-card__full"><?= e($f['name']) ?></span>
                    <span class="fw-card__text"><?= e(str_limit($f['overview'], 96)) ?></span>
                    <span class="fw-card__go">Explore <?= icon('arrow-right', 'icon icon--xs') ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php foreach ($frameworks as $f): ?>
    <template id="fw-<?= e($f['slug']) ?>">
        <div class="fw-dlg__head">
            <span class="fw-dlg__icon"><?= icon($f['icon']) ?></span>
            <div><p class="eyebrow"><?= e($f['region']) ?></p><h2 id="fwDlgTitle"><?= e($f['name']) ?></h2></div>
        </div>
        <div class="fw-dlg__body">
            <section><h3>Overview</h3><p><?= e($f['overview']) ?></p></section>
            <?php if ($f['purpose']): ?><section><h3>Purpose</h3><p><?= e($f['purpose']) ?></p></section><?php endif; ?>
            <?php if ($f['requirements']): ?><section><h3>Key requirements</h3><ul class="checks"><?php foreach (lines($f['requirements']) as $l): ?><li><?= icon('check', 'icon icon--xs') ?><?= e($l) ?></li><?php endforeach; ?></ul></section><?php endif; ?>
            <?php if ($f['why_needed']): ?><section><h3>Why organisations need it</h3><p><?= e($f['why_needed']) ?></p></section><?php endif; ?>
            <?php if ($f['how_we_help']): ?><section class="fw-dlg__help"><h3>How Yojo Solutions can support</h3><p><?= e($f['how_we_help']) ?></p></section><?php endif; ?>
        </div>
        <div class="fw-dlg__foot">
            <?php if ($f['service_slug']): ?><a class="btn btn--ghost" href="<?= url('services/' . $f['category_slug'] . '/' . $f['service_slug']) ?>"><?= e($f['service_title']) ?> <?= icon('arrow-right', 'icon icon--xs') ?></a><?php endif; ?>
            <a class="btn btn--primary" href="<?= url('book-consultation?topic=' . rawurlencode($f['short_name'])) ?>">Enquire about <?= e($f['short_name']) ?></a>
        </div>
    </template>
    <?php endforeach; ?>
    <dialog class="fw-dlg" id="fwDialog" aria-labelledby="fwDlgTitle">
        <button class="fw-dlg__close" type="button" data-fw-close aria-label="Close"><?= icon('close', 'icon icon--sm') ?></button>
        <div class="fw-dlg__content" data-fw-content></div>
    </dialog>
</section>
