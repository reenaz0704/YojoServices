<?php partial('page-hero', [
    'title' => $category['name'], 'eyebrow' => $category['tagline'], 'text' => $category['summary'], 'icon' => $category['icon'],
    'crumbs' => ['Services' => url('services'), $category['name'] => null],
    'actions' => [['Book a Consultation', url('book-consultation')], ['Request a Proposal', url('request-proposal')]],
]); ?>
<section class="section">
    <div class="container">
        <div class="cat-intro fx-reveal">
            <p class="lead"><?= e($category['description']) ?></p>
            <?php if ($category['engagement_note']): ?><div class="notice"><span><?= icon('info') ?></span><div><strong><?= $category['page_type'] === 'legal' ? 'Technical support — not legal advice' : 'How we engage' ?></strong><p><?= e($category['engagement_note']) ?></p></div></div><?php endif; ?>
        </div>
        <div class="svc-card-grid">
            <?php foreach ($services as $i => $s): ?>
            <a class="svc-card svc-card--sm fx-reveal" href="<?= url('services/' . $category['slug'] . '/' . $s['slug']) ?>" style="--i:<?= $i % 6 ?>">
                <span class="svc-card__icon"><?= icon($s['icon']) ?></span>
                <h3><?= e($s['title']) ?></h3>
                <p><?= e(str_limit($s['summary'], 120)) ?></p>
                <?php if ($s['engagement_type']): ?><span class="svc-card__type"><?= e($s['engagement_type']) ?></span><?php endif; ?>
                <span class="link-arrow">Learn more <?= icon('arrow-right', 'icon icon--sm') ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="section section--tint">
    <div class="container">
        <div class="section-head fx-reveal"><p class="eyebrow">Other divisions</p><h2 class="h2">Explore related services</h2></div>
        <div class="div-grid">
            <?php foreach ($categories as $i => $c): if ($c['id'] == $category['id']) continue; ?>
            <a class="div-card fx-reveal" href="<?= url('services/' . $c['slug']) ?>"><span class="div-card__icon"><?= icon($c['icon']) ?></span><h3><?= e($c['name']) ?></h3><p><?= e($c['summary']) ?></p><span class="link-arrow">Explore <?= icon('arrow-right', 'icon icon--sm') ?></span></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php partial('cta'); ?>
