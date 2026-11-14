<?php
use App\Models\Service;
$menu = Service::menu();
$socials = array_filter([
    'linkedin' => setting('social_linkedin'), 'facebook' => setting('social_facebook'), 'instagram' => setting('social_instagram'),
    'x' => setting('social_x'), 'youtube' => setting('social_youtube'),
]);
$wa = preg_replace('/\D/', '', setting('whatsapp'));
$quick = [['/', 'Home'], ['/about', 'Who We Are'], ['/services', 'Services'], ['/industries', 'Industry Expertise'], ['/insights', 'Latest Insights'], ['/faq', 'FAQ'], ['/contact', 'Contact']];
$tel = function ($n) { return preg_replace('/[^\d+]/', '', $n); };
?>
<footer class="footer" id="siteFooter">
    <div class="footer__fx" aria-hidden="true">
        <div class="footer__aurora"></div>
        <svg class="footer__net" viewBox="0 0 1200 400" preserveAspectRatio="xMidYMid slice">
            <g class="footer__net-lines">
                <path d="M0 320 L180 250 L340 290 L520 180 L700 230 L880 120 L1050 170 L1200 90"/>
                <path d="M120 60 L300 140 L520 180 L640 70 L880 120 L1000 40"/>
                <path d="M180 250 L300 140 M700 230 L640 70 M1050 170 L1000 40"/>
            </g>
            <g class="footer__net-nodes">
                <circle cx="180" cy="250" r="3"/><circle cx="340" cy="290" r="2.5"/><circle cx="520" cy="180" r="3.5"/><circle cx="700" cy="230" r="2.5"/>
                <circle cx="880" cy="120" r="3.5"/><circle cx="1050" cy="170" r="2.5"/><circle cx="300" cy="140" r="3"/><circle cx="640" cy="70" r="2.5"/><circle cx="1000" cy="40" r="3"/>
            </g>
            <circle class="footer__packet" r="3"><animateMotion dur="9s" repeatCount="indefinite" path="M0 320 L180 250 L340 290 L520 180 L700 230 L880 120 L1050 170 L1200 90"/></circle>
            <circle class="footer__packet footer__packet--2" r="2.5"><animateMotion dur="12s" begin="2s" repeatCount="indefinite" path="M120 60 L300 140 L520 180 L640 70 L880 120 L1000 40"/></circle>
        </svg>
    </div>
    <div class="footer__line" aria-hidden="true"></div>

    <div class="container">
        <div class="footer__cta fx-reveal">
            <div>
                <p class="eyebrow eyebrow--light">Let's talk</p>
                <h2 class="footer__headline"><?= e(setting('footer_cta_title', 'Let\'s secure what matters to your business.')) ?></h2>
            </div>
            <a class="btn btn--accent btn--lg footer__cta-btn" href="<?= url('book-consultation') ?>"><span><?= e(setting('footer_cta_label', 'Request a Consultation')) ?></span> <?= icon('arrow-right', 'icon icon--sm') ?></a>
        </div>

        <div class="footer__grid">
            <div class="footer__col footer__about fx-reveal" style="--i:0">
                <a class="footer__logo" href="<?= url('/') ?>" aria-label="<?= e(setting('company_name')) ?> home">
                    <span class="footer__orbit" aria-hidden="true"><i></i><i></i></span>
                    <img src="<?= asset('images/logo-circle.png') ?>" alt="" width="52" height="52" loading="lazy">
                    <span class="footer__brand"><strong><?= e(setting('company_name')) ?></strong><small><?= e(setting('company_tagline')) ?></small></span>
                </a>
                <p><?= e(setting('footer_description', setting('company_intro'))) ?></p>
                <?php if ($socials || $wa): ?>
                <div class="footer__social">
                    <?php foreach ($socials as $net => $link): ?><a href="<?= e($link) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e(ucfirst($net)) ?>"><?= icon($net) ?></a><?php endforeach; ?>
                    <?php if ($wa): ?><a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><?= icon('whatsapp') ?></a><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="footer__col fx-reveal" style="--i:1">
                <h3 class="footer__h">Quick Links</h3>
                <ul class="footer__links">
                    <?php foreach ($quick as $q): ?><li><a href="<?= url($q[0]) ?>"><?= e($q[1]) ?></a></li><?php endforeach; ?>
                </ul>
            </div>
            <div class="footer__col fx-reveal" style="--i:2">
                <h3 class="footer__h">Our Services</h3>
                <ul class="footer__links">
                    <?php foreach ($menu as $slug => $g): ?><li><a href="<?= url('services/' . $slug) ?>"><?= e(preg_replace('/ & Related Services$|(?<=Compliance|Audit) Services$/', '', $g['label'])) ?></a></li><?php endforeach; ?>
                    <li><a href="<?= url('security-assessment') ?>">Readiness Assessment</a></li>
                </ul>
            </div>
            <div class="footer__col fx-reveal" style="--i:3">
                <h3 class="footer__h">Contact Information</h3>
                <ul class="footer__contact">
                    <li><span class="footer__ic"><?= icon('phone-call', 'icon icon--sm') ?></span><span><a href="tel:<?= e($tel(setting('phone_primary'))) ?>"><?= e(setting('phone_primary')) ?></a><?php if (setting('phone_secondary')): ?><br><a href="tel:<?= e($tel(setting('phone_secondary'))) ?>"><?= e(setting('phone_secondary')) ?></a><?php endif; ?></span></li>
                    <li><span class="footer__ic"><?= icon('mail', 'icon icon--sm') ?></span><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li>
                    <li><span class="footer__ic"><?= icon('map-pin', 'icon icon--sm') ?></span><span><?= e(setting('address')) ?><?php if (setting('map_url')): ?><br><a class="footer__map" href="<?= e(setting('map_url')) ?>" target="_blank" rel="noopener noreferrer">View on Google Maps <?= icon('arrow-up-right', 'icon icon--xs') ?></a><?php endif; ?></span></li>
                    <?php if (setting('business_hours')): ?><li><span class="footer__ic"><?= icon('clock', 'icon icon--sm') ?></span><span><?= e(setting('business_hours')) ?></span></li><?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="footer__bottom">
            <p>© <?= date('Y') ?> <?= e(setting('company_name')) ?>. All rights reserved.<br><small><?= e(setting('legal_disclaimer')) ?></small></p>
            <ul class="footer__legal">
                <li><a href="<?= url('legal/privacy-policy') ?>">Privacy Policy</a></li>
                <li><a href="<?= url('legal/cookie-policy') ?>">Cookie Policy</a></li>
                <li><a href="<?= url('legal/terms-and-conditions') ?>">Terms &amp; Conditions</a></li>
                <li><a href="<?= url('sitemap.xml') ?>">Sitemap</a></li>
            </ul>
        </div>
    </div>
</footer>
<?php if ($wa): ?><a class="wa-float" href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp"><?= icon('whatsapp') ?></a><?php endif; ?>
<button class="to-top" id="toTop" aria-label="Back to top"><svg class="to-top__ring" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22" pathLength="100"/></svg><?= icon('chevron-down') ?></button>
