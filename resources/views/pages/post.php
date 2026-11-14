<?php $shareUrl = rawurlencode(full_url('insights/' . $post['slug'])); $shareTitle = rawurlencode($post['title']); ?>
<article>
    <header class="post-hero">
        <div class="page-hero__bg" aria-hidden="true"><div class="grid-lines"></div></div>
        <div class="container container--narrow">
            <nav class="crumbs"><a href="<?= url('/') ?>">Home</a><span>/</span><a href="<?= url('insights') ?>">Latest Insights</a><span>/</span><a href="<?= url('insights?category=' . $post['category_slug']) ?>"><?= e($post['category_name']) ?></a></nav>
            <a class="tag tag--light" href="<?= url('insights?category=' . $post['category_slug']) ?>"><?= e($post['category_name']) ?></a>
            <h1><?= e($post['title']) ?></h1>
            <p class="page-hero__text"><?= e($post['excerpt']) ?></p>
            <div class="post-meta">
                <span class="avatar"><?= e(mb_substr($post['author_name'] ?: 'Y', 0, 1)) ?></span>
                <span><strong><?= e($post['author_name'] ?: setting('company_name')) ?></strong><small><time datetime="<?= e($post['published_at']) ?>"><?= format_date($post['published_at'], 'd F Y') ?></time> · <?= (int) $post['reading_minutes'] ?> min read</small></span>
            </div>
        </div>
    </header>
    <div class="container post-layout">
        <aside class="share" aria-label="Share">
            <span>Share</span>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareUrl ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn"><?= icon('linkedin') ?></a>
            <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" rel="noopener" aria-label="Share on X"><?= icon('x') ?></a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener" aria-label="Share on Facebook"><?= icon('facebook') ?></a>
            <a href="https://wa.me/?text=<?= $shareTitle ?>%20<?= $shareUrl ?>" target="_blank" rel="noopener" aria-label="Share on WhatsApp"><?= icon('whatsapp') ?></a>
            <button data-copy="<?= e(full_url('insights/' . $post['slug'])) ?>" aria-label="Copy link"><?= icon('link') ?></button>
        </aside>
        <div class="post-body">
            <?php if ($post['thumbnail']): ?><img class="post-body__img" src="<?= e(media_url($post['thumbnail'])) ?>" alt=""><?php endif; ?>
            <div class="prose"><?= $post['content'] /* sanitized on save by HtmlSanitizer */ ?></div>
            <?php if ($tags): ?><div class="chips-row post-tags"><?php foreach ($tags as $t): ?><a class="chip" href="<?= url('insights?tag=' . $t['slug']) ?>">#<?= e($t['name']) ?></a><?php endforeach; ?></div><?php endif; ?>
            <div class="author-box">
                <span class="avatar avatar--lg"><?= e(mb_substr($post['author_name'] ?: 'Y', 0, 1)) ?></span>
                <div><strong><?= e($post['author_name'] ?: setting('company_name')) ?></strong><small><?= e($post['author_designation'] ?: 'Consultant, ' . setting('company_name')) ?></small><p><?= e($post['author_bio'] ?: 'Insights from the ' . setting('company_name') . ' audit and cybersecurity team.') ?></p></div>
            </div>
        </div>
    </div>
</article>
<?php if ($related): ?>
<section class="section section--tint"><div class="container">
    <div class="section-head reveal"><p class="eyebrow">Related articles</p><h2 class="h2">Keep reading</h2></div>
    <div class="post-grid"><?php foreach ($related as $r) { partial('post-card', ['post' => $r]); } ?></div>
</div></section>
<?php endif; ?>
<?php partial('cta'); ?>
