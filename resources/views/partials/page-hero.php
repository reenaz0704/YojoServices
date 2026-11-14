<?php /** @var string $title  @var array $crumbs  optional: $eyebrow, $text, $icon, $actions(bool) */ ?>
<section class="page-hero">
    <div class="page-hero__bg" aria-hidden="true"><div class="grid-lines"></div></div>
    <div class="container page-hero__inner">
        <nav class="crumbs" aria-label="Breadcrumb">
            <a href="<?= url('/') ?>">Home</a>
            <?php foreach (($crumbs ?? []) as $label => $href): ?>
                <span>/</span><?php if ($href): ?><a href="<?= e($href) ?>"><?= e($label) ?></a><?php else: ?><span aria-current="page"><?= e($label) ?></span><?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <?php if (!empty($icon)): ?><span class="page-hero__icon"><?= icon($icon) ?></span><?php endif; ?>
        <?php if (!empty($eyebrow)): ?><p class="eyebrow eyebrow--light"><?= e($eyebrow) ?></p><?php endif; ?>
        <h1><?= e($title) ?></h1>
        <?php if (!empty($text)): ?><p class="page-hero__text"><?= e($text) ?></p><?php endif; ?>
        <?php if (!empty($actions)): ?>
        <div class="btn-row">
            <a class="btn btn--accent" href="<?= e($actions[0][1]) ?>"><?= e($actions[0][0]) ?> <?= icon('arrow-right', 'icon icon--sm') ?></a>
            <?php if (isset($actions[1])): ?><a class="btn btn--outline-light" href="<?= e($actions[1][1]) ?>"><?= e($actions[1][0]) ?></a><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
