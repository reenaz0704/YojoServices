<?php partial('page-hero', ['title' => $page['title'], 'eyebrow' => 'Legal', 'crumbs' => [$page['title'] => null]]); ?>
<section class="section"><div class="container container--narrow">
    <p class="muted">Last updated: <?= format_date($page['updated_at'] ?: date('Y-m-d'), 'd F Y') ?></p>
    <div class="prose"><?= $page['content'] ?></div>
</div></section>
