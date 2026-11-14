<?php /** @var array $post  optional int $i */ $href = url('insights/' . $post['slug']); ?>
<article class="post-card fx-reveal" style="--i:<?= (int) ($i ?? 0) ?>">
    <a class="post-card__thumb" href="<?= $href ?>" tabindex="-1" aria-hidden="true">
        <?php if ($post['thumbnail']): ?>
            <img src="<?= e(media_url($post['thumbnail'])) ?>" alt="" loading="lazy">
        <?php else: ?>
            <div class="thumb-art thumb-art--<?= (int) $post['category_id'] % 4 ?>"><span class="thumb-art__grid"></span><?= icon(['shield', 'chart', 'code', 'globe', 'book', 'file-text'][(int) $post['category_id'] % 6]) ?></div>
        <?php endif; ?>
        <?php if ($post['thumbnail']): for ($k = 0; $k < 4; $k++): ?><i class="ins-spark" style="--k:<?= $k ?>"></i><?php endfor; endif; ?>
        <span class="post-card__cat"><?= e($post['category_name']) ?></span>
    </a>
    <div class="post-card__body">
        <div class="post-card__meta"><time datetime="<?= e($post['published_at']) ?>"><?= icon('calendar', 'icon icon--xs') ?> <?= format_date($post['published_at']) ?></time><span><?= (int) $post['reading_minutes'] ?> min read</span></div>
        <h3><a href="<?= $href ?>"><?= e($post['title']) ?></a></h3>
        <p><?= e(str_limit($post['excerpt'], 140)) ?></p>
        <a class="post-card__more" href="<?= $href ?>" aria-label="Read more: <?= e($post['title']) ?>">Read More <span class="post-card__arrow"><?= icon('arrow-right', 'icon icon--sm') ?></span></a>
    </div>
</article>
