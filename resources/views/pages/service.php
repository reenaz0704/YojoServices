<?php
use App\Models\Service;
$s = $service;
$isSecurity = $s['page_type'] === 'security';
$isLegal = $s['page_type'] === 'legal';
$reqLabel = $s['page_type'] === 'compliance' ? 'Major requirements' : 'Key activities';
$sections = [];
$sections[] = ['overview', $isSecurity ? 'Service overview' : 'Introduction'];
if ($s['meaning']) $sections[] = ['meaning', $isSecurity ? 'What it involves' : 'What it means'];
if ($s['why_needed']) $sections[] = ['why', 'Why it matters'];
if ($s['requirements']) $sections[] = ['requirements', $reqLabel];
if ($s['scope']) $sections[] = ['scope', $isSecurity ? 'Assessment scope' : 'Scope of work'];
if ($s['methodology']) $sections[] = ['methodology', $isSecurity ? 'Testing methodology' : 'Assessment methodology'];
if ($s['security_areas']) $sections[] = ['areas', 'Security areas covered'];
if ($s['reporting']) $sections[] = ['reporting', 'Reporting process'];
if ($s['remediation']) $sections[] = ['remediation', 'Remediation guidance'];
if ($s['deliverables']) $sections[] = ['deliverables', 'Expected deliverables'];
if ($s['benefits']) $sections[] = ['benefits', 'Benefits'];
if ($faqs) $sections[] = ['faqs', 'FAQs'];
partial('page-hero', [
    'title' => $s['title'], 'eyebrow' => $s['category_name'], 'text' => $s['summary'], 'icon' => $s['icon'],
    'crumbs' => ['Services' => url('services'), $s['category_name'] => url('services/' . $s['category_slug']), $s['title'] => null],
    'actions' => [['Request Consultation', url('book-consultation?service=' . $s['slug'])], ['Request a Proposal', url('request-proposal?service=' . $s['slug'])]],
]);
?>
<section class="section">
    <div class="container detail">
        <aside class="detail__toc">
            <div class="toc" data-toc>
                <p class="toc__title">On this page</p>
                <?php foreach ($sections as $sec): ?><a href="#<?= $sec[0] ?>"><?= e($sec[1]) ?></a><?php endforeach; ?>
            </div>
        </aside>

        <article class="detail__main prose-sections">
            <?php if ($s['engagement_type']): ?><p class="engagement"><?= icon('briefcase', 'icon icon--sm') ?> <strong>Engagement type:</strong> <?= e($s['engagement_type']) ?></p><?php endif; ?>

            <section id="overview"><h2><?= e($sections[0][1]) ?></h2><p class="lead"><?= nl2br(e($s['intro'])) ?></p></section>
            <?php if ($s['meaning']): ?><section id="meaning"><h2><?= $isSecurity ? 'What it involves' : 'What it means' ?></h2><p><?= nl2br(e($s['meaning'])) ?></p></section><?php endif; ?>
            <?php if ($s['why_needed']): ?><section id="why"><h2>Why it matters</h2><div class="callout"><?= icon('alert') ?><p><?= nl2br(e($s['why_needed'])) ?></p></div></section><?php endif; ?>
            <?php if ($s['requirements']): ?><section id="requirements"><h2><?= e($reqLabel) ?></h2><?php partial('list', ['text' => $s['requirements'], 'style' => 'cards']); ?></section><?php endif; ?>
            <?php if ($s['scope']): ?><section id="scope"><h2><?= $isSecurity ? 'Assessment scope' : 'Scope of work' ?></h2><?php partial('list', ['text' => $s['scope']]); ?></section><?php endif; ?>
            <?php if ($s['methodology']): ?><section id="methodology"><h2><?= $isSecurity ? 'Testing methodology' : 'Assessment methodology' ?></h2><?php partial('list', ['text' => $s['methodology'], 'style' => 'steps']); ?></section><?php endif; ?>
            <?php if ($s['security_areas']): ?><section id="areas"><h2>Security areas covered</h2><?php partial('list', ['text' => $s['security_areas'], 'style' => 'cards']); ?></section><?php endif; ?>
            <?php if ($s['reporting']): ?><section id="reporting"><h2>Reporting process</h2><?php partial('list', ['text' => $s['reporting'], 'style' => 'steps']); ?></section><?php endif; ?>
            <?php if ($s['remediation']): ?><section id="remediation"><h2>Remediation guidance</h2><?php partial('list', ['text' => $s['remediation']]); ?></section><?php endif; ?>
            <?php if ($s['deliverables']): ?><section id="deliverables"><h2>Expected deliverables</h2><?php partial('list', ['text' => $s['deliverables'], 'style' => 'cards']); ?></section><?php endif; ?>
            <?php if ($s['benefits']): ?><section id="benefits"><h2>Benefits</h2><?php partial('list', ['text' => $s['benefits']]); ?></section><?php endif; ?>

            <?php if ($s['engagement_note']): ?>
            <div class="notice"><span><?= icon($isSecurity ? 'shield-alert' : ($isLegal ? 'scale' : 'info')) ?></span><div><strong><?= $isSecurity ? 'Authorised testing only' : ($isLegal ? 'Technical support — not legal advice' : 'Scope of our role') ?></strong><p><?= e($s['engagement_note']) ?></p></div></div>
            <?php endif; ?>

            <?php if ($faqs): ?><section id="faqs"><h2>Frequently asked questions</h2><?php partial('faq-list', ['faqs' => $faqs]); ?></section><?php endif; ?>
        </article>

        <aside class="detail__side">
            <div class="side-cta">
                <span class="side-cta__icon"><?= icon('calendar') ?></span>
                <h3>Discuss <?= e($s['title']) ?></h3>
                <p>A 30-minute call with a consultant to understand your scope and timelines.</p>
                <a class="btn btn--primary btn--block" href="<?= url('book-consultation?service=' . $s['slug']) ?>">Request Consultation</a>
                <a class="btn btn--ghost btn--block" href="<?= url('request-proposal?service=' . $s['slug']) ?>">Request a Proposal</a>
                <p class="side-cta__contact"><?= icon('phone-call', 'icon icon--xs') ?> <a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone_primary'))) ?>"><?= e(setting('phone_primary')) ?></a></p>
            </div>
            <?php if ($industries): ?>
            <div class="side-box"><h4>Relevant industries</h4><?php foreach ($industries as $i): ?><a class="side-link" href="<?= url('industries/' . $i['slug']) ?>"><?= icon($i['icon'], 'icon icon--sm') ?><?= e($i['name']) ?></a><?php endforeach; ?></div>
            <?php endif; ?>
            <?php if ($siblings): ?>
            <div class="side-box"><h4>Related services</h4><?php foreach ($siblings as $r): ?><a class="side-link" href="<?= url('services/' . $s['category_slug'] . '/' . $r['slug']) ?>"><?= icon($r['icon'], 'icon icon--sm') ?><?= e($r['title']) ?></a><?php endforeach; ?></div>
            <?php endif; ?>
        </aside>
    </div>
</section>
<?php partial('cta', ['title' => 'Ready to start with ' . $s['title'] . '?', 'text' => 'Tell us about your environment and objectives — we will respond with a clear scope and next steps.']); ?>
