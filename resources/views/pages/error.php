<section class="error-page">
    <div class="page-hero__bg" aria-hidden="true"><div class="grid-lines"></div></div>
    <div class="container">
        <p class="error-page__code mono"><?= (int) ($code ?? 404) ?></p>
        <h1><?= e($title ?? 'Page not found') ?></h1>
        <p>The page you requested could not be shown. It may have moved, or the link may be incorrect.</p>
        <div class="btn-row btn-row--center"><a class="btn btn--accent" href="<?= url('/') ?>">Go to homepage</a><a class="btn btn--outline-light" href="<?= url('contact') ?>">Contact us</a></div>
    </div>
</section>
