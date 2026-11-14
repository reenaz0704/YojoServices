<?php /** @var array $faqs  optional: bool $searchable, bool $openFirst, array $categories (tabs) */
$cats = $categories ?? [];
?>
<div class="faq" data-faq>
    <?php if (!empty($searchable)): ?>
    <label class="faq__search">
        <?= icon('search') ?>
        <input type="search" placeholder="Search questions and answers…" data-faq-search aria-label="Search FAQs">
    </label>
    <?php endif; ?>
    <?php if ($cats): ?>
    <div class="faq__tabs" role="tablist" aria-label="FAQ categories">
        <button class="faq__tab is-active" role="tab" aria-selected="true" data-faq-tab="all">All <span><?= count($faqs) ?></span></button>
        <?php foreach ($cats as $c): ?><button class="faq__tab" role="tab" aria-selected="false" data-faq-tab="<?= e($c['slug']) ?>"><?= icon($c['icon'], 'icon icon--xs') ?> <?= e($c['name']) ?> <span><?= (int) $c['faq_count'] ?></span></button><?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="faq__list">
        <?php foreach ($faqs as $i => $f): $open = $i === 0 && !empty($openFirst); $id = 'faq-' . (int) ($f['id'] ?? $i) . '-' . $i; ?>
        <div class="faq__item <?= $open ? 'is-open' : '' ?>" data-faq-item data-cat="<?= e($f['category_slug'] ?? '') ?>">
            <button class="faq__q" aria-expanded="<?= $open ? 'true' : 'false' ?>" aria-controls="<?= $id ?>">
                <span><?= e($f['question']) ?></span><span class="faq__toggle" aria-hidden="true"></span>
            </button>
            <div class="faq__a" id="<?= $id ?>" role="region"><div class="faq__a-inner"><div class="prose prose--faq"><?php
                $ans = (string) $f['answer'];
                echo strpos(ltrim($ans), '<') === 0 ? rich($ans) /* sanitised on save */ : '<p>' . nl2br(e($ans)) . '</p>';
            ?></div></div></div>
        </div>
        <?php endforeach; ?>
    </div>
    <p class="faq__empty" data-faq-empty hidden>No matching questions. <a href="<?= url('contact') ?>">Ask us directly →</a></p>
</div>
