<?php partial('page-hero', [
    'title' => 'Case studies', 'eyebrow' => 'Resources',
    'text' => 'How our engagements are structured — from the business challenge to verified outcomes. Client-approved studies are labelled; others are illustrative scenarios.',
    'crumbs' => ['Case studies' => null],
]); ?>
<section class="section"><div class="container">
    <?php if (!$caseStudies): ?><p class="empty">Case studies will be published here once approved by clients.</p><?php endif; ?>
    <div class="case-grid">
    <?php foreach ($caseStudies as $cs): ?>
        <article class="case-card reveal">
            <div class="case-card__top"><span class="tag"><?= e($cs['industry_name'] ?: 'Cross-industry') ?></span><?= $cs['is_authorized'] ? '<span class="badge badge--ok">Client-approved</span>' : '<span class="badge badge--muted">' . e($cs['client_label'] ?: 'Illustrative') . '</span>' ?></div>
            <h3><?= e($cs['title']) ?></h3>
            <p><?= e(str_limit($cs['challenge'], 160)) ?></p>
            <a class="link-arrow" href="<?= url('case-studies/' . $cs['slug']) ?>">Read case study <?= icon('arrow-right', 'icon icon--sm') ?></a>
        </article>
    <?php endforeach; ?>
    </div>
</div></section>
<?php partial('cta'); ?>
