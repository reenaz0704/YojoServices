<?php /** @var array $frameworks  optional bool $reverse */
if (empty($frameworks)) return;
$reverse = !empty($reverse);
// Badge per framework: logo from assets/images/frameworks/{slug}.png when present, otherwise an icon seal
$emblem = function (array $f, int $k): string {
    $logo = 'images/frameworks/' . $f['slug'] . '.png';
    $mark = is_file(BASE_PATH . '/public/assets/' . $logo)
        ? '<span class="fw-logo__img"><img src="' . asset($logo) . '" alt="" loading="lazy" decoding="async"></span>'
        : '<span class="fw-logo__mark">' . icon($f['icon']) . '</span>';
    return '<span class="fw-logo fw-logo--' . ($k % 4) . '">' . $mark . '<span class="fw-logo__txt"><b>' . e($f['short_name']) . '</b><small>' . e($f['region']) . '</small></span></span>';
};
$list = $reverse ? array_reverse($frameworks) : $frameworks;
?>
<div class="fw__marquee<?= $reverse ? ' fw__marquee--rev' : '' ?>" aria-hidden="true">
    <div class="fw__track fw__track--logos"><?php for ($r = 0; $r < 2; $r++): foreach ($list as $k => $f): ?><?= $emblem($f, $k + ($reverse ? 2 : 0)) ?><?php endforeach; endfor; ?></div>
</div>
