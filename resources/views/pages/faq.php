<?php partial('page-hero', [
    'title' => 'Frequently asked questions', 'eyebrow' => 'FAQ',
    'text' => 'Clear answers about our audit, compliance, cybersecurity, cyber legal and IT consulting services — and how an engagement works.',
    'crumbs' => ['FAQ' => null],
]); ?>
<section class="section">
    <div class="container faq-page">
        <div class="faq-page__main"><?php partial('faq-list', ['faqs' => $faqs, 'searchable' => true, 'categories' => $categories, 'openFirst' => true]); ?></div>
        <aside class="faq-page__side">
            <div class="side-cta">
                <span class="side-cta__icon"><?= icon('calendar') ?></span>
                <h3>Still have a question?</h3>
                <p>Talk to a consultant about your requirements — no obligation.</p>
                <a class="btn btn--primary btn--block" href="<?= url('book-consultation') ?>">Request a Consultation</a>
                <a class="btn btn--ghost btn--block" href="<?= url('contact') ?>">Contact us</a>
            </div>
        </aside>
    </div>
</section>
<?php partial('cta'); ?>
