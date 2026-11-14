<?php partial('page-hero', ['title' => $heading, 'eyebrow' => $formType === 'consultation' ? 'Consultation' : 'Proposal', 'text' => $intro, 'crumbs' => [$heading => null]]); ?>
<section class="section">
    <div class="container contact">
        <div class="contact__form card-pane reveal">
            <?php partial('enquiry-form', get_defined_vars()); ?>
        </div>
        <aside class="contact__side">
            <div class="contact-card reveal">
                <h3>What happens next</h3>
                <ol class="mini-steps">
                    <li><span class="mono">1</span><div><strong>Confirmation</strong><p>You receive an email with your reference number immediately.</p></div></li>
                    <li><span class="mono">2</span><div><strong>Consultant review</strong><p>A consultant reviews your request and confirms <?= $formType === 'consultation' ? 'the slot' : 'any scoping questions' ?> within one business day.</p></div></li>
                    <li><span class="mono">3</span><div><strong><?= $formType === 'consultation' ? 'Discovery call' : 'Tailored proposal' ?></strong><p><?= $formType === 'consultation' ? 'A focused conversation about your goals, scope and timelines.' : 'A clear proposal with scope, deliverables, timeline and fees.' ?></p></div></li>
                </ol>
            </div>
            <div class="contact-card contact-card--dark reveal">
                <h3><?= icon('lock', 'icon icon--sm') ?> Confidentiality</h3>
                <p>We're happy to sign an NDA before detailed discussions. Please don't upload confidential evidence through this form — we'll arrange a secure channel when needed.</p>
            </div>
        </aside>
    </div>
</section>
