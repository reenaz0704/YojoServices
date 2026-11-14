<?php use App\Services\SpamGuard;
$countries = ['India', 'United States', 'United Kingdom', 'United Arab Emirates', 'Saudi Arabia', 'Singapore', 'Australia', 'Canada', 'Germany', 'Other'];
partial('page-hero', [
    'title' => 'Audit Readiness Assessment', 'eyebrow' => 'Free self-assessment · ~5 minutes',
    'text' => 'Answer a few questions about your organisation and current security practices to receive an indicative readiness summary.',
    'crumbs' => ['Readiness Assessment' => null],
]); ?>
<section class="section">
    <div class="container wizard-wrap">
        <div class="notice notice--warn reveal"><span><?= icon('info') ?></span><div><strong>Preliminary self-assessment only</strong><p>The result is calculated from your own answers using the transparent scoring rules shown below. It is <strong>not</strong> an official audit, certification or assurance opinion.</p></div></div>

        <form class="wizard card-pane" id="wizard" novalidate>
            <input type="hidden" name="_ft" value="<?= e(SpamGuard::formToken()) ?>">
            <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

            <ol class="wizard__steps" aria-label="Progress">
                <?php foreach (['Organisation', 'Requirements', 'Security status', 'Contact', 'Summary'] as $i => $label): ?>
                <li class="<?= $i === 0 ? 'is-current' : '' ?>" data-step-indicator="<?= $i ?>"><span class="mono"><?= $i + 1 ?></span><em><?= $label ?></em></li>
                <?php endforeach; ?>
            </ol>
            <div class="wizard__progress"><div class="wizard__progress-bar" data-progress></div></div>

            <!-- Step 1 -->
            <fieldset class="wizard__panel" data-step="0">
                <legend class="h3">Tell us about your organisation</legend>
                <div class="form__grid">
                    <div class="field"><label for="a_type">Organisation type <b>*</b></label><select id="a_type" name="organization_type" required><option value="">Select</option><?php foreach (['Private company', 'Public listed company', 'Startup', 'Government / PSU', 'Non-profit', 'Other'] as $o): ?><option><?= $o ?></option><?php endforeach; ?></select><small class="field__err"></small></div>
                    <div class="field"><label for="a_ind">Industry <b>*</b></label><select id="a_ind" name="industry" required><option value="">Select</option><?php foreach ($industries as $o): ?><option><?= e($o) ?></option><?php endforeach; ?></select><small class="field__err"></small></div>
                    <div class="field"><label for="a_size">Company size <b>*</b></label><select id="a_size" name="company_size" required><option value="">Select</option><?php foreach ($sizes as $o): ?><option value="<?= e($o) ?>"><?= e($o) ?> employees</option><?php endforeach; ?></select><small class="field__err"></small></div>
                    <div class="field"><label for="a_country">Country <b>*</b></label><select id="a_country" name="country" required><option value="">Select</option><?php foreach ($countries as $o): ?><option><?= $o ?></option><?php endforeach; ?></select><small class="field__err"></small></div>
                </div>
            </fieldset>

            <!-- Step 2 -->
            <fieldset class="wizard__panel" data-step="1" hidden>
                <legend class="h3">Which requirements are you working towards?</legend>
                <p class="muted">Select all that apply.</p>
                <div class="tiles" data-field="frameworks">
                    <?php foreach ($frameworks as $k => $label): ?>
                    <label class="tile"><input type="checkbox" name="frameworks[]" value="<?= e($k) ?>"><span><?= icon($k === 'VAPT' ? 'radar' : ($k === 'ITAUDIT' ? 'clipboard' : ($k === 'OTHER' ? 'plus' : 'shield-check'))) ?><strong><?= e($label) ?></strong></span></label>
                    <?php endforeach; ?>
                </div>
                <small class="field__err" data-err="frameworks"></small>
                <div class="field" data-other hidden><label for="a_other">Please specify</label><input id="a_other" name="other_requirement" maxlength="255"></div>
            </fieldset>

            <!-- Step 3 -->
            <fieldset class="wizard__panel" data-step="2" hidden>
                <legend class="h3">Current security status</legend>
                <p class="muted">Answer honestly — this is for your benefit. “Partially” means the practice exists but is informal or inconsistent.</p>
                <?php foreach ($domains as $dk => $d): ?>
                <div class="qgroup">
                    <h4><?= e($d[0]) ?></h4>
                    <?php foreach ($d[1] as $qk => $q): ?>
                    <div class="q" data-q="<?= e($qk) ?>">
                        <p><?= e($q) ?></p>
                        <div class="seg seg--sm">
                            <?php foreach (['yes' => 'Yes', 'partial' => 'Partially', 'no' => 'No', 'unsure' => 'Not sure'] as $v => $l): ?>
                            <label><input type="radio" name="answers[<?= e($qk) ?>]" value="<?= $v ?>"><span><?= $l ?></span></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
                <small class="field__err" data-err="answers"></small>
            </fieldset>

            <!-- Step 4 -->
            <fieldset class="wizard__panel" data-step="3" hidden>
                <legend class="h3">Where should we send your summary?</legend>
                <div class="form__grid">
                    <div class="field"><label for="a_name">Full name <b>*</b></label><input id="a_name" name="full_name" required minlength="2" maxlength="120" autocomplete="name"><small class="field__err"></small></div>
                    <div class="field"><label for="a_company">Company <b>*</b></label><input id="a_company" name="company" required minlength="2" maxlength="160" autocomplete="organization"><small class="field__err"></small></div>
                    <div class="field"><label for="a_email">Business email <b>*</b></label><input id="a_email" type="email" name="email" required maxlength="190" autocomplete="email"><small class="field__err"></small></div>
                    <div class="field"><label for="a_phone">Phone <em>(optional)</em></label><input id="a_phone" type="tel" name="phone" pattern="^\+?[0-9\s\-()]{7,20}$" autocomplete="tel"><small class="field__err"></small></div>
                    <div class="field field--full"><label for="a_role">Designation <em>(optional)</em></label><input id="a_role" name="designation" maxlength="120" autocomplete="organization-title"></div>
                    <div class="field field--full"><label class="check check--consent"><input type="checkbox" name="consent" value="1" required><span>I consent to <?= e(setting('company_name')) ?> storing my answers and contacting me about this assessment, as described in the <a href="<?= url('legal/privacy-policy') ?>" target="_blank">Privacy Policy</a>. <b>*</b></span></label><small class="field__err"></small></div>
                </div>
            </fieldset>

            <!-- Step 5 -->
            <div class="wizard__panel" data-step="4" hidden>
                <div class="result" data-result>
                    <div class="result__head">
                        <div class="result__ring"><svg viewBox="0 0 120 120" class="ring"><circle cx="60" cy="60" r="50" class="ring__bg"/><circle cx="60" cy="60" r="50" class="ring__fg" pathLength="100" stroke-dasharray="0 100" data-ring/></svg><div><strong class="mono" data-score>0</strong><span>/ 100</span></div></div>
                        <div>
                            <p class="eyebrow">Indicative readiness</p>
                            <h3 data-band>—</h3>
                            <p data-band-text></p>
                            <p class="ref-inline">Reference: <strong class="mono" data-ref></strong></p>
                        </div>
                    </div>
                    <div class="result__domains" data-domains></div>
                    <div class="result__prio" data-prio-wrap hidden><h4>Suggested priorities</h4><ul data-prio></ul></div>
                    <div class="notice notice--warn"><span><?= icon('alert') ?></span><div><strong>This is a preliminary self-assessment</strong><p>It is based solely on your answers, has not been verified, and is not an audit, certification or assurance opinion. A consultant can validate these results with evidence.</p></div></div>
                    <div class="btn-row"><a class="btn btn--primary" href="<?= url('book-consultation') ?>"><?= icon('calendar', 'icon icon--sm') ?> Discuss results with a consultant</a><button type="button" class="btn btn--ghost" onclick="window.print()"><?= icon('download', 'icon icon--sm') ?> Save as PDF</button></div>
                </div>
            </div>

            <div class="form__alert" role="alert" hidden></div>
            <div class="wizard__nav" data-nav>
                <button type="button" class="btn btn--ghost" data-back hidden><?= icon('chevron-left', 'icon icon--sm') ?> Back</button>
                <button type="button" class="btn btn--primary" data-next><span class="btn__label">Continue</span><span class="spinner" aria-hidden="true"></span> <?= icon('arrow-right', 'icon icon--sm') ?></button>
            </div>
        </form>

        <details class="rules reveal">
            <summary><?= icon('info', 'icon icon--sm') ?> How is the score calculated?</summary>
            <div class="rules__body">
                <p>Each answer earns points: <strong>Yes = 2</strong>, <strong>Partially = 1</strong>, <strong>No / Not sure = 0</strong>. Each of the <?= count($domains) ?> domains is scored as a percentage of its maximum points; the overall score is the average of domain scores.</p>
                <table class="table"><thead><tr><th>Score</th><th>Band</th><th>Interpretation</th></tr></thead><tbody>
                <?php foreach ($bands as $i => $b): $max = $i === 0 ? 100 : $bands[$i - 1][0] - 1; ?><tr><td class="mono"><?= $b[0] ?>–<?= $max ?></td><td><strong><?= e($b[1]) ?></strong></td><td><?= e($b[2]) ?></td></tr><?php endforeach; ?>
                </tbody></table>
            </div>
        </details>
    </div>
</section>
