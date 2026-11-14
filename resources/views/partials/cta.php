<section class="section">
    <div class="container">
        <div class="cta-band reveal">
            <div class="cta-band__art" aria-hidden="true">
                <svg viewBox="0 0 400 400"><defs><radialGradient id="ctaG" cx="50%" cy="50%" r="50%"><stop offset="0" stop-color="#6d5dfc" stop-opacity=".55"/><stop offset="1" stop-color="#6d5dfc" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="200" fill="url(#ctaG)"/><g fill="none" stroke="rgba(255,255,255,.18)"><circle cx="200" cy="200" r="70"/><circle cx="200" cy="200" r="120"/><circle cx="200" cy="200" r="170" stroke-dasharray="4 8"/></g></svg>
            </div>
            <div class="cta-band__content">
                <p class="eyebrow eyebrow--light">Let's talk</p>
                <h2><?= e($title ?? setting('cta_title')) ?></h2>
                <p><?= e($text ?? setting('cta_text')) ?></p>
            </div>
            <div class="cta-band__actions">
                <a class="btn btn--accent btn--lg" href="<?= url('book-consultation') ?>"><?= icon('calendar', 'icon icon--sm') ?> Book a Consultation</a>
                <a class="btn btn--outline-light btn--lg" href="<?= url('request-proposal') ?>">Request a Proposal</a>
            </div>
        </div>
    </div>
</section>
