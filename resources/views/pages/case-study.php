<?php partial('page-hero', [
    'title' => $cs['title'], 'eyebrow' => $cs['industry_name'] ?: 'Case study',
    'crumbs' => ['Case studies' => url('case-studies'), $cs['title'] => null],
]); ?>
<section class="section"><div class="container container--narrow">
    <?php if (!$cs['is_authorized']): ?><div class="notice"><span><?= icon('info') ?></span><div><strong><?= e($cs['client_label'] ?: 'Illustrative scenario') ?></strong><p>This example describes how a typical engagement is structured. It does not describe a specific named client.</p></div></div><?php endif; ?>
    <div class="cs-flow">
        <?php foreach ([['Business challenge', 'challenge', 'alert'], ['Assessment performed', 'assessment', 'search'], ['Identified improvement areas', 'improvements', 'target'], ['Remediation approach', 'remediation', 'settings'], ['Outcome', 'outcome', 'check-circle']] as $i => $b): ?>
        <section class="cs-step reveal <?= $b[1] === 'outcome' ? 'cs-step--ok' : '' ?>">
            <span class="cs-step__icon"><?= icon($b[2]) ?></span>
            <div><p class="eyebrow mono">0<?= $i + 1 ?></p><h2><?= $b[0] ?></h2>
            <?php if (count(lines($cs[$b[1]])) > 1): partial('list', ['text' => $cs[$b[1]]]); else: ?><p><?= nl2br(e($cs[$b[1]])) ?></p><?php endif; ?></div>
        </section>
        <?php endforeach; ?>
    </div>
</div></section>
<?php partial('cta'); ?>
