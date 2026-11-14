<?php
partial('page-hero', [
    'title' => setting('insights_title', 'Latest Insights'), 'eyebrow' => 'Insights',
    'text' => setting('insights_text'),
    'crumbs' => ['Latest Insights' => null],
]);
$qs = function (array $over) use ($filters) {
    $q = array_filter(array_merge($filters, $over), 'strlen');
    if (($q['sort'] ?? '') === 'newest') unset($q['sort']);
    return url('insights') . ($q ? '?' . http_build_query($q) : '');
};
$active = array_filter([$filters['q'], $filters['category'], $filters['tag'], $filters['period']], 'strlen');
?>
<section class="section">
    <div class="container">
        <form class="ins-filters fx-reveal" method="get" action="<?= url('insights') ?>" data-autosubmit>
            <label class="search-input ins-filters__q"><?= icon('search') ?><input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Search articles…" aria-label="Search articles"></label>
            <label class="ins-filters__sel"><span class="sr-only">Category</span>
                <select name="category" aria-label="Category"><option value="">All categories</option><?php foreach ($categories as $c): ?><option value="<?= e($c['slug']) ?>" <?= $filters['category'] === $c['slug'] ? 'selected' : '' ?>><?= e($c['name']) ?> (<?= (int) $c['post_count'] ?>)</option><?php endforeach; ?></select>
            </label>
            <label class="ins-filters__sel"><span class="sr-only">Date</span>
                <select name="period" aria-label="Publication date"><option value="">Any date</option><?php foreach ($periods as $p): ?><option value="<?= e($p['ym']) ?>" <?= $filters['period'] === $p['ym'] ? 'selected' : '' ?>><?= e(date('F Y', strtotime($p['ym'] . '-01'))) ?> (<?= (int) $p['n'] ?>)</option><?php endforeach; ?></select>
            </label>
            <label class="ins-filters__sel"><span class="sr-only">Sort</span>
                <select name="sort" aria-label="Sort articles"><?php foreach ($sorts as $k => $l): ?><option value="<?= $k ?>" <?= $filters['sort'] === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select>
            </label>
            <?php if ($filters['tag']): ?><input type="hidden" name="tag" value="<?= e($filters['tag']) ?>"><?php endif; ?>
            <button class="btn btn--primary" type="submit">Apply</button>
        </form>

        <div class="filter-tabs filter-tabs--links ins-cats">
            <a class="<?= !$filters['category'] ? 'is-active' : '' ?>" href="<?= $qs(['category' => '', 'page' => '']) ?>">All</a>
            <?php foreach ($categories as $c): ?><a class="<?= $filters['category'] === $c['slug'] ? 'is-active' : '' ?>" href="<?= $qs(['category' => $c['slug'], 'page' => '']) ?>"><?= e($c['name']) ?> <span><?= (int) $c['post_count'] ?></span></a><?php endforeach; ?>
        </div>
        <?php if ($tags): ?><div class="chips-row ins-tags"><span class="muted">Tags:</span><?php foreach ($tags as $t): ?><a class="chip <?= $filters['tag'] === $t['slug'] ? 'is-active' : '' ?>" href="<?= $qs(['tag' => $filters['tag'] === $t['slug'] ? '' : $t['slug'], 'page' => '']) ?>">#<?= e($t['name']) ?></a><?php endforeach; ?></div><?php endif; ?>

        <p class="ins-count muted"><?= (int) $total ?> article<?= $total == 1 ? '' : 's' ?><?= $filters['q'] ? ' for “' . e($filters['q']) . '”' : '' ?><?= $filters['tag'] ? ' tagged #' . e($filters['tag']) : '' ?><?php if ($active): ?> · <a href="<?= url('insights') ?>">Clear filters</a><?php endif; ?></p>

        <?php if (!$posts): ?>
            <div class="empty"><?= icon('file-search') ?><p>No articles match these filters. Try another search or category.</p></div>
        <?php else: ?>
            <div class="post-grid post-grid--lg"><?php foreach ($posts as $k => $post) { partial('post-card', ['post' => $post, 'i' => $k]); } ?></div>
        <?php endif; ?>

        <?php if ($pages > 1): ?>
        <nav class="pager" aria-label="Pagination">
            <?php if ($page > 1): ?><a href="<?= $qs(['page' => $page - 1]) ?>" aria-label="Previous page"><?= icon('chevron-left', 'icon icon--sm') ?></a><?php endif; ?>
            <?php for ($p = 1; $p <= $pages; $p++): ?><a class="<?= $p === $page ? 'is-active' : '' ?>" href="<?= $qs(['page' => $p]) ?>" <?= $p === $page ? 'aria-current="page"' : '' ?>><?= $p ?></a><?php endfor; ?>
            <?php if ($page < $pages): ?><a href="<?= $qs(['page' => $page + 1]) ?>" aria-label="Next page"><?= icon('chevron-right', 'icon icon--sm') ?></a><?php endif; ?>
        </nav>
        <?php endif; ?>
    </div>
</section>
<?php partial('cta'); ?>
