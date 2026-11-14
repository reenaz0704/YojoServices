<?php
/**
 * Reusable enquiry form. Vars: $formType (contact|consultation|proposal), $menu, $industries, $sizes, $preselect
 */
use App\Services\SpamGuard;
$isBooking = $formType === 'consultation';
?>
<form class="form" data-enquiry-form novalidate>
    <input type="hidden" name="type" value="<?= e($formType) ?>">
    <input type="hidden" name="_ft" value="<?= e(SpamGuard::formToken()) ?>">
    <input type="hidden" name="source_page" value="<?= e(current_path()) ?>">
    <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

    <div class="form__grid">
        <div class="field"><label for="f_name">Full name <b>*</b></label><input id="f_name" name="full_name" required minlength="2" maxlength="120" autocomplete="name"><small class="field__err"></small></div>
        <div class="field"><label for="f_company">Company name <b>*</b></label><input id="f_company" name="company" required minlength="2" maxlength="160" autocomplete="organization"><small class="field__err"></small></div>
        <div class="field"><label for="f_email">Business email <b>*</b></label><input id="f_email" type="email" name="email" required maxlength="190" autocomplete="email"><small class="field__err"></small></div>
        <div class="field"><label for="f_phone">Phone number <b>*</b></label><input id="f_phone" type="tel" name="phone" required pattern="^\+?[0-9\s\-()]{7,20}$" autocomplete="tel" placeholder="+91 …"><small class="field__err"></small></div>
        <div class="field"><label for="f_industry">Industry <b>*</b></label>
            <select id="f_industry" name="industry" required><option value="">Select industry</option><?php foreach ($industries as $i): ?><option><?= e($i) ?></option><?php endforeach; ?></select><small class="field__err"></small></div>
        <div class="field"><label for="f_size">Company size <b>*</b></label>
            <select id="f_size" name="company_size" required><option value="">Select size</option><?php foreach ($sizes as $s): ?><option value="<?= e($s) ?>"><?= e($s) ?> employees</option><?php endforeach; ?></select><small class="field__err"></small></div>

        <?php if ($isBooking): ?>
        <div class="field"><label for="f_date">Preferred date <b>*</b></label><input id="f_date" type="date" name="preferred_date" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" max="<?= date('Y-m-d', strtotime('+90 days')) ?>"><small class="field__err"></small></div>
        <div class="field"><label for="f_time">Preferred time (IST) <b>*</b></label>
            <select id="f_time" name="preferred_time" required><option value="">Select a slot</option><?php foreach (['10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'] as $t): ?><option value="<?= $t ?>"><?= date('g:i A', strtotime($t)) ?></option><?php endforeach; ?></select><small class="field__err"></small></div>
        <div class="field field--full"><span class="label">Meeting mode <b>*</b></span>
            <div class="seg"><?php foreach (['Video call', 'Phone call', 'In person'] as $k => $m): ?><label><input type="radio" name="meeting_mode" value="<?= $m ?>" <?= $k === 0 ? 'checked' : '' ?>><span><?= $m ?></span></label><?php endforeach; ?></div><small class="field__err"></small></div>
        <?php endif; ?>

        <div class="field field--full" data-field="services">
            <span class="label">Services required <?= $formType === 'contact' ? '<em>(optional)</em>' : '<b>*</b>' ?></span>
            <div class="svc-picker" data-svc-picker>
                <button type="button" class="svc-picker__btn" aria-expanded="false"><span data-svc-summary>Select one or more services</span><?= icon('chevron-down', 'icon icon--sm') ?></button>
                <div class="svc-picker__panel" hidden>
                    <input type="search" class="svc-picker__search" placeholder="Filter services…" aria-label="Filter services">
                    <?php foreach ($menu as $g): if (!$g['items']) continue; ?>
                    <fieldset><legend><?= e($g['label']) ?></legend>
                        <?php foreach ($g['items'] as $s): ?>
                        <label class="check"><input type="checkbox" name="services[]" value="<?= (int) $s['id'] ?>" <?= in_array((int) $s['id'], $preselect ?? [], true) ? 'checked' : '' ?>><span><?= e($s['title']) ?></span></label>
                        <?php endforeach; ?>
                    </fieldset>
                    <?php endforeach; ?>
                </div>
                <div class="chips" data-svc-chips></div>
            </div>
            <small class="field__err"></small>
        </div>

        <div class="field field--full"><label for="f_msg">Message <?= $formType === 'contact' ? '<b>*</b>' : '<em>(optional)</em>' ?></label>
            <textarea id="f_msg" name="message" rows="5" maxlength="5000" <?= $formType === 'contact' ? 'required minlength="10"' : '' ?> placeholder="Briefly describe your objectives, timelines and systems in scope."></textarea>
            <small class="field__hint"><?= icon('alert', 'icon icon--xs') ?> Please do not share passwords, confidential evidence or sensitive client data in this form.</small><small class="field__err"></small></div>

        <div class="field field--full"><label class="check check--consent"><input type="checkbox" name="consent" value="1" required><span>I agree that <?= e(setting('company_name')) ?> may process my details to respond to this request, as described in the <a href="<?= url('legal/privacy-policy') ?>" target="_blank">Privacy Policy</a>. <b>*</b></span></label><small class="field__err"></small></div>
    </div>

    <div class="form__foot">
        <button class="btn btn--primary btn--lg" type="submit"><span class="btn__label"><?= $isBooking ? 'Request this slot' : ($formType === 'proposal' ? 'Request proposal' : 'Send enquiry') ?></span><span class="spinner" aria-hidden="true"></span></button>
        <p class="form__secure"><?= icon('lock', 'icon icon--xs') ?> Encrypted in transit · Never shared with third parties</p>
    </div>
    <div class="form__alert" role="alert" hidden></div>
</form>

<div class="form-success" data-form-success hidden>
    <div class="form-success__icon"><?= icon('check-circle') ?></div>
    <h3>Request received</h3>
    <p data-success-message></p>
    <div class="ref-box"><span>Your reference</span><strong class="mono" data-success-ref></strong></div>
    <p class="muted">A confirmation has been sent to your email. Our team typically responds within one business day.</p>
    <a class="btn btn--ghost" href="<?= url('/') ?>">Back to home</a>
</div>
