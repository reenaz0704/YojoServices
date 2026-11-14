<?php partial('page-hero', [
    'title' => 'Let\'s talk about your security and compliance goals', 'eyebrow' => 'Contact us',
    'text' => 'Share a few details and a consultant will get back to you within one business day.',
    'crumbs' => ['Contact' => null],
]); ?>
<section class="section">
    <div class="container contact">
        <div class="contact__form card-pane reveal">
            <h2 class="h3">Send us an enquiry</h2>
            <p class="muted">Fields marked * are required.</p>
            <?php partial('enquiry-form', get_defined_vars()); ?>
        </div>
        <aside class="contact__side">
            <div class="contact-card reveal">
                <h3>Contact details</h3>
                <ul class="contact-list">
                    <li><span><?= icon('map-pin') ?></span><div><small>Office</small><p><?= e(setting('address')) ?></p></div></li>
                    <li><span><?= icon('phone-call') ?></span><div><small>Phone</small><p><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone_primary'))) ?>"><?= e(setting('phone_primary')) ?></a><?php if (setting('phone_secondary')): ?><br><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone_secondary'))) ?>"><?= e(setting('phone_secondary')) ?></a><?php endif; ?></p></div></li>
                    <li><span><?= icon('mail') ?></span><div><small>Email</small><p><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p></div></li>
                    <li><span><?= icon('clock') ?></span><div><small>Hours</small><p><?= e(setting('business_hours')) ?></p></div></li>
                </ul>
            </div>
            <div class="contact-card contact-card--dark reveal">
                <h3>Prefer a scheduled call?</h3>
                <p>Pick a time that suits you for a 30-minute consultation.</p>
                <a class="btn btn--accent btn--block" href="<?= url('book-consultation') ?>"><?= icon('calendar', 'icon icon--sm') ?> Book a Consultation</a>
            </div>
            <div class="map reveal">
                <iframe title="Office location" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q=<?= rawurlencode(setting('map_embed_query', 'Visakhapatnam')) ?>&z=13&output=embed"></iframe>
            </div>
        </aside>
    </div>
</section>
