<?php partial('page-hero', [
    'title' => 'Industry Expertise', 'eyebrow' => 'Industries',
    'text' => 'Every sector carries its own threats, regulators and audit pressures. We tailor scope, frameworks and reporting to the realities of your industry.',
    'crumbs' => ['Industry Expertise' => null],
    'actions' => [['Book a Consultation', url('book-consultation')]],
]); ?>
<section class="section ind-sec">
    <div class="container">
        <div class="icard-grid">
            <?php foreach ($industries as $i => $ind) { partial('industry-card', ['ind' => $ind, 'i' => $i]); } ?>
        </div>
    </div>
</section>
<?php partial('cta'); ?>
