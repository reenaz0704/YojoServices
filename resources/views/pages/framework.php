<?php partial('page-hero', [
    'title' => $f['name'], 'eyebrow' => 'Framework · ' . $f['region'], 'text' => $f['overview'], 'icon' => $f['icon'],
    'crumbs' => ['Frameworks' => url('/#frameworks'), $f['short_name'] => null],
    'actions' => [['Enquire about ' . $f['short_name'], url('book-consultation?topic=' . rawurlencode($f['short_name']))]],
]); ?>
<section class="section">
    <div class="container detail detail--2">
        <article class="detail__main prose-sections">
            <?php if ($f['purpose']): ?><section><h2>Purpose</h2><p class="lead"><?= e($f['purpose']) ?></p></section><?php endif; ?>
            <?php if ($f['requirements']): ?><section><h2>Key requirements</h2><?php partial('list', ['text' => $f['requirements'], 'style' => 'cards']); ?></section><?php endif; ?>
            <?php if ($f['why_needed']): ?><section><h2>Why organisations need it</h2><div class="callout"><?= icon('alert') ?><p><?= e($f['why_needed']) ?></p></div></section><?php endif; ?>
            <?php if ($f['how_we_help']): ?><section><h2>How Yojo Solutions can support</h2><p><?= e($f['how_we_help']) ?></p></section><?php endif; ?>
            <div class="notice"><span><?= icon('info') ?></span><div><strong>Consulting, not certification</strong><p><?= e(setting('legal_disclaimer')) ?></p></div></div>
        </article>
        <aside class="detail__side">
            <div class="side-cta">
                <span class="side-cta__icon"><?= icon('calendar') ?></span>
                <h3>Discuss <?= e($f['short_name']) ?></h3>
                <p>Talk to a consultant about scope, readiness and timelines.</p>
                <a class="btn btn--primary btn--block" href="<?= url('book-consultation?topic=' . rawurlencode($f['short_name'])) ?>">Request Consultation</a>
                <?php if ($f['service_slug']): ?><a class="btn btn--ghost btn--block" href="<?= url('services/' . $f['category_slug'] . '/' . $f['service_slug']) ?>"><?= e($f['service_title']) ?></a><?php endif; ?>
            </div>
            <?php if ($others): ?><div class="side-box"><h4>Other frameworks</h4><?php foreach ($others as $o): ?><a class="side-link" href="<?= url('frameworks/' . $o['slug']) ?>"><?= icon($o['icon'], 'icon icon--sm') ?><?= e($o['short_name']) ?></a><?php endforeach; ?></div><?php endif; ?>
        </aside>
    </div>
</section>
<?php partial('cta'); ?>
