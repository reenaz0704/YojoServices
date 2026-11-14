<?php
$siteName = setting('company_name', 'Yojo Solutions');
$title = $meta_title ?? setting('seo_title', $siteName);
$desc = $meta_description ?? setting('seo_description');
$canonical = full_url(current_path());
$tplKey = site_template(); $tpl = site_templates()[$tplKey]; $tplBase = $tpl['base'] ?? $tplKey; $tplTone = $tpl['tone'] ?? 'dark'; $tplLayout = $tpl['layout'] ?? ($tplBase === 'prism' ? 'prism' : '');
$lay = (string) setting('site_layout', ''); if (isset(site_layouts()[$lay]) && $lay !== '') { $tplLayout = $lay === 'classic' ? '' : $lay; }
$tplScene = $tpl['scene'] ?? ''; $sc = (string) setting('site_scene', ''); if (isset(site_scene_styles()[$sc]) && $sc !== '') { $tplScene = $sc; }
$fontQ = implode('&', array_map(fn($f) => 'family=' . str_replace(' ', '+', $f) . ':wght@400;500;600;700', array_unique([$tpl['font'], $tpl['body']])));
?><!DOCTYPE html>
<html lang="en" data-template="<?= e($tplBase) ?>" data-tone="<?= e($tplTone) ?>" data-layout="<?= e($tplLayout) ?>" data-scene-style="<?= e($tplScene) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e(str_limit($desc, 300)) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta property="og:type" content="<?= e($og_type ?? 'website') ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e(str_limit($desc, 300)) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e(!empty($og_image) ? $og_image : full_url('assets/images/logo.png')) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="base-url" content="<?= e(base_path_url()) ?>">
    <meta name="theme-color" content="<?= e($tpl['theme']) ?>">
    <link rel="icon" href="<?= asset('images/favicon.ico') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?<?= e($fontQ) ?>&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/future.css') ?>">
    <?php if ($tplLayout): ?><link rel="stylesheet" href="<?= asset('css/pz-modules.css') ?>"><?php endif; ?>
    <?php if ($tplBase === 'sentinel'): ?><link rel="stylesheet" href="<?= asset('css/sentinel.css') ?>"><?php endif; ?>
    <?php if ($tplBase === 'prism'): ?><link rel="stylesheet" href="<?= asset('css/prism.css') ?>"><?php endif; ?>
    <?php if ($tplTone === 'light'): ?><link rel="stylesheet" href="<?= asset('css/light.css') ?>"><?php endif; ?>
    <?php if ($tplBase === 'gilt'): ?><link rel="stylesheet" href="<?= asset('css/gilt.css') ?>"><?php endif; ?>
    <?php if ($tplBase === 'prism'): ?><link rel="stylesheet" href="<?= asset('css/prism-plus.css') ?>"><?php endif; ?>
    <script type="application/ld+json"><?= json_encode([
        '@context' => 'https://schema.org', '@type' => 'ProfessionalService', 'name' => $siteName,
        'url' => full_url('/'), 'logo' => full_url('assets/images/logo.png'), 'email' => setting('email'),
        'telephone' => setting('phone_primary'), 'address' => setting('address'),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body class="<?= current_path() === '/' ? 'is-home' : '' ?>">
<canvas class="fx3d" aria-hidden="true"></canvas>
<div class="fx-noise" aria-hidden="true"></div>
<?php if ($tplBase === 'gilt'): ?><div class="gl-fluid" aria-hidden="true"><i></i><i></i><i></i><i></i></div><?php endif; ?>
<div class="fx-progress" aria-hidden="true"></div>
<div class="fx-aura" aria-hidden="true"></div>
<a class="skip-link" href="#main">Skip to content</a>
<?php partial('header'); ?>
<main id="main"><?= $content ?></main>
<?php partial('footer'); ?>
<div class="cookie" id="cookieBanner" hidden>
    <p>We use only essential cookies to keep forms secure. No tracking or advertising cookies. <a href="<?= url('legal/cookie-policy') ?>">Cookie Policy</a></p>
    <div class="cookie__actions"><button class="btn btn--sm btn--ghost-dark" data-cookie="essential">Essential only</button><button class="btn btn--sm btn--primary" data-cookie="ok">OK</button></div>
</div>
<?php if (setting('popup_enabled', '1') === '1'):
    $popMenu = \App\Models\Service::menu();
    $popInd = []; try { $popInd = \App\Services\EnquiryService::industries(); } catch (\Throwable $e) {}
?>
<div class="enq-pop" id="enqPop" role="dialog" aria-modal="true" aria-labelledby="enqPopTitle" hidden
     data-interval="<?= max(1, (int) setting('popup_interval_minutes', '5')) ?>" data-max="<?= max(1, (int) setting('popup_max_per_session', '3')) ?>">
    <div class="enq-pop__backdrop" data-pop-close></div>
    <div class="enq-pop__card">
        <button class="enq-pop__close" type="button" data-pop-close aria-label="Close"><?= icon('close', 'icon icon--sm') ?></button>
        <div class="enq-pop__side" aria-hidden="true">
            <svg viewBox="0 0 120 120"><circle class="enq-pop__ring" cx="60" cy="60" r="52"/><circle class="enq-pop__ring enq-pop__ring--2" cx="60" cy="60" r="38"/><path class="enq-pop__shield" d="M60 26 L86 36 V58 C86 76 74 89 60 94 C46 89 34 76 34 58 V36 Z"/><path class="enq-pop__tick" d="M48 60 L57 69 L74 51"/></svg>
            <p class="enq-pop__eyebrow"><span class="pulse-dot"></span> We're here to help</p>
            <h2 id="enqPopTitle"><?= e(setting('popup_title')) ?></h2>
            <p><?= e(setting('popup_text')) ?></p>
            <a class="enq-pop__link" href="<?= e(site_link(setting('popup_cta_secondary_url', 'services'))) ?>" data-pop-cta><?= e(setting('popup_cta_secondary', 'Explore Our Services')) ?> <?= icon('arrow-right', 'icon icon--xs') ?></a>
        </div>
        <div class="enq-pop__main">
            <form class="form form--pop" data-enquiry-form data-quick novalidate>
                <input type="hidden" name="type" value="contact">
                <input type="hidden" name="quick" value="1">
                <input type="hidden" name="_ft" value="<?= e(\App\Services\SpamGuard::formToken()) ?>">
                <input type="hidden" name="source_page" value="<?= e(current_path()) ?> (enquiry pop-up)">
                <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                <div class="form__grid">
                    <div class="field"><label for="p_name">Full name <b>*</b></label><input id="p_name" name="full_name" required minlength="2" maxlength="120" autocomplete="name"><small class="field__err"></small></div>
                    <div class="field"><label for="p_company">Organisation <b>*</b></label><input id="p_company" name="company" required minlength="2" maxlength="160" autocomplete="organization"><small class="field__err"></small></div>
                    <div class="field"><label for="p_email">Business email <b>*</b></label><input id="p_email" type="email" name="email" required maxlength="190" autocomplete="email"><small class="field__err"></small></div>
                    <div class="field"><label for="p_phone">Phone <b>*</b></label><input id="p_phone" type="tel" name="phone" required pattern="^\+?[0-9\s\-()]{7,20}$" autocomplete="tel" placeholder="+91 …"><small class="field__err"></small></div>
                    <div class="field"><label for="p_ind">Industry <b>*</b></label><select id="p_ind" name="industry" required><option value="">Select industry</option><?php foreach ($popInd as $i): ?><option><?= e($i) ?></option><?php endforeach; ?></select><small class="field__err"></small></div>
                    <div class="field"><label for="p_size">Company size <b>*</b></label><select id="p_size" name="company_size" required><option value="">Select size</option><?php foreach (\App\Services\EnquiryService::SIZES as $sz): ?><option value="<?= e($sz) ?>"><?= e($sz) ?> employees</option><?php endforeach; ?></select><small class="field__err"></small></div>
                    <div class="field field--full" data-field="divisions"><span class="label">Services you're interested in <b>*</b></span>
                        <div class="pop-checks"><?php foreach ($popMenu as $slug => $g): ?><label class="pop-check"><input type="checkbox" name="divisions[]" value="<?= e($slug) ?>"><span><?= icon($g['icon'], 'icon icon--sm') ?><?= e($g['label']) ?></span></label><?php endforeach; ?></div>
                        <small class="field__err"></small></div>
                    <div class="field field--full"><label for="p_msg">Message <em>(optional)</em></label><textarea id="p_msg" name="message" rows="2" maxlength="5000" placeholder="Briefly describe your requirement and timelines."></textarea><small class="field__err"></small></div>
                    <div class="field field--full"><label class="check check--consent"><input type="checkbox" name="consent" value="1" required><span>I agree to be contacted about this request as described in the <a href="<?= url('legal/privacy-policy') ?>" target="_blank">Privacy Policy</a>. <b>*</b></span></label><small class="field__err"></small></div>
                </div>
                <div class="enq-pop__actions">
                    <button class="btn btn--primary" type="submit"><span class="btn__label"><?= e(setting('popup_cta_primary', 'Request a Consultation')) ?></span><span class="spinner" aria-hidden="true"></span></button>
                    <button class="enq-pop__later" type="button" data-pop-close><?= e(setting('popup_dismiss', 'Maybe Later')) ?></button>
                </div>
                <div class="form__alert" role="alert" hidden></div>
            </form>
            <div class="form-success" data-form-success hidden>
                <div class="form-success__icon"><?= icon('check-circle') ?></div>
                <h3>Thank you — request received</h3>
                <p data-success-message></p>
                <div class="ref-box"><span>Your reference</span><strong class="mono" data-success-ref></strong></div>
                <button class="btn btn--ghost" type="button" data-pop-close>Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<script src="<?= asset('js/main.js') ?>" defer></script>
<?php if (\App\Core\Auth::check()): ?>
<nav class="tpl-switch" aria-label="Preview design template (visible to admins only)">
    <?php foreach (site_templates() as $k => $t): ?><a href="?template=<?= e($k) ?>" class="<?= $k === $tplKey ? 'is-on' : '' ?>" title="<?= e($t['desc']) ?>"><i style="background:linear-gradient(135deg,<?= e(implode(',', $t['swatch'])) ?>)"></i><span><?= e($t['name']) ?></span></a><?php endforeach; ?>
</nav>
<?php endif; ?>
<script type="importmap">{"imports":{"three":"https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js","three/addons/":"https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"}}</script>
<script type="module" src="<?= asset('js/fx3d.js') ?>"></script>
<?php if ($tplBase === 'prism'): ?><script type="module" src="<?= asset('js/hero3d.js') ?>"></script><?php endif; ?>
<script src="<?= asset('js/fx-ui.js') ?>" defer></script>
<?php if ($tplLayout): ?><script src="<?= asset('js/prism-layout.js') ?>" defer></script><?php endif; ?>
</body>
</html>
