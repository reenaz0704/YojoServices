<?php
/** Illustrations for the "Why choose us" cards, keyed by the item's icon.
 *  Pure SVG (no external images); colours come from the theme via CSS classes. */
$why_art = [
// Structured audit methodology — stacked process layers with a checklist
'layers' => '<svg viewBox="0 0 320 200" class="why-art"><defs><linearGradient id="wa-g1" x1="0" x2="1"><stop offset="0" class="s1"/><stop offset="1" class="s2"/></linearGradient></defs>
<path class="wa-soft" d="M160 150 270 120 160 90 50 120z"/><path class="wa-a" d="M160 132 270 102 160 72 50 102z" opacity=".55"/><path fill="url(#wa-g1)" d="M160 112 270 82 160 52 50 82z"/>
<path class="wa-line" d="M50 82v20l110 30 110-30V82M50 102v18l110 30 110-30v-18" fill="none"/>
<g transform="translate(118 58) skewY(-15) scale(1 .8)"><rect class="wa-paper" width="84" height="56" rx="6"/><rect class="wa-c" x="10" y="10" width="10" height="10" rx="2"/><rect class="wa-c" x="10" y="26" width="10" height="10" rx="2"/><rect class="wa-mute" x="10" y="42" width="10" height="8" rx="2"/><rect class="wa-ink" x="26" y="12" width="46" height="5" rx="2"/><rect class="wa-ink" x="26" y="28" width="38" height="5" rx="2"/><rect class="wa-mute" x="26" y="43" width="30" height="5" rx="2"/></g>
<text x="276" y="62" class="wa-num">01</text><text x="276" y="96" class="wa-num">02</text><text x="276" y="128" class="wa-num">03</text></svg>',
// Risk-based assessments — 5x5 risk heat map with a target on the hot spot
'target' => '<svg viewBox="0 0 320 200" class="why-art"><g transform="translate(70 26)">' .
    implode('', array_map(function ($i) { $r = intdiv($i, 5); $c = $i % 5; $lv = $c + (4 - $r); $cls = $lv >= 6 ? 'wa-hot' : ($lv >= 4 ? 'wa-b' : ($lv >= 2 ? 'wa-c' : 'wa-cool')); return '<rect class="' . $cls . '" x="' . ($c * 30) . '" y="' . ($r * 30) . '" width="27" height="27" rx="5"/>'; }, range(0, 24))) .
    '<path class="wa-line" d="M-8 0v150h158" fill="none"/><text x="-14" y="-6" class="wa-lbl">Impact</text><text x="112" y="166" class="wa-lbl">Likelihood</text></g>
<g transform="translate(232 54)"><circle r="34" class="wa-paper"/><circle r="26" class="wa-a"/><circle r="17" class="wa-paper"/><circle r="8" class="wa-a"/><path class="wa-ink-s" d="M40-36 2-2"/><path class="wa-b" d="m40-36 14-4-6 12z"/></g></svg>',
// Experienced consulting professionals — two consultants with credentials card
'users' => '<svg viewBox="0 0 320 200" class="why-art"><circle cx="160" cy="104" r="78" class="wa-soft"/>
<g transform="translate(118 60)"><circle cx="0" cy="0" r="22" class="wa-b"/><path class="wa-b" d="M-38 82c0-30 16-50 38-50s38 20 38 50z"/></g>
<g transform="translate(196 52)"><circle cx="0" cy="0" r="24" class="wa-a"/><path class="wa-a" d="M-42 94c0-34 18-56 42-56s42 22 42 56z"/><path class="wa-paper" d="M-6 40 0 64 6 40 0 34z"/></g>
<g transform="translate(220 112)"><rect class="wa-paper" width="74" height="50" rx="8"/><circle cx="18" cy="20" r="8" class="wa-c"/><rect class="wa-ink" x="32" y="14" width="32" height="5" rx="2"/><rect class="wa-mute" x="32" y="24" width="24" height="4" rx="2"/><rect class="wa-a" x="10" y="36" width="54" height="6" rx="3"/></g>
<g transform="translate(64 130)"><circle r="16" class="wa-c"/><path class="wa-ink-s" d="m-7 0 5 5 9-10" fill="none"/></g></svg>',
// Practical remediation — gear + wrench and a closing-findings progress bar
'settings' => '<svg viewBox="0 0 320 200" class="why-art"><g transform="translate(110 92)"><path class="wa-a" d="' . (function () { $d = ''; for ($i = 0; $i < 16; $i++) { $a = $i * M_PI / 8; $r = $i % 2 ? 44 : 56; $d .= ($i ? 'L' : 'M') . round(cos($a) * $r, 1) . ' ' . round(sin($a) * $r, 1); } return $d . 'Z'; })() . '"/><circle r="20" class="wa-paper"/></g>
<g transform="translate(150 60) rotate(40)"><rect class="wa-b" x="-8" y="0" width="16" height="96" rx="6"/><path class="wa-b" d="M-22 0a22 22 0 1 1 44 0l-12 0 0-10-20 0 0 10z"/></g>
<g transform="translate(196 54)"><rect class="wa-paper" width="104" height="96" rx="10"/><text x="12" y="22" class="wa-lbl">Findings closed</text>
<rect class="wa-mute" x="12" y="34" width="80" height="8" rx="4"/><rect class="wa-c" x="12" y="34" width="68" height="8" rx="4"/>
<rect class="wa-mute" x="12" y="52" width="80" height="8" rx="4"/><rect class="wa-b" x="12" y="52" width="50" height="8" rx="4"/>
<rect class="wa-mute" x="12" y="70" width="80" height="8" rx="4"/><rect class="wa-a" x="12" y="70" width="30" height="8" rx="4"/></g></svg>',
// Compliance-focused approach — shield with check over mapped framework documents
'shield-check' => '<svg viewBox="0 0 320 200" class="why-art"><rect class="wa-paper" x="66" y="40" width="70" height="92" rx="8" transform="rotate(-10 100 86)"/><rect class="wa-paper" x="184" y="40" width="70" height="92" rx="8" transform="rotate(10 220 86)"/>
<g class="wa-mute"><rect x="78" y="62" width="40" height="5" rx="2" transform="rotate(-10 100 86)"/><rect x="78" y="76" width="30" height="5" rx="2" transform="rotate(-10 100 86)"/><rect x="200" y="62" width="40" height="5" rx="2" transform="rotate(10 220 86)"/><rect x="200" y="76" width="30" height="5" rx="2" transform="rotate(10 220 86)"/></g>
<path class="wa-line" d="M136 120c10 20 38 20 48 0" fill="none" stroke-dasharray="4 5"/>
<path class="wa-a" d="M160 28c18 12 34 14 48 14 0 56-18 92-48 112-30-20-48-56-48-112 14 0 30-2 48-14z"/><path class="wa-paper" d="M160 44c12 8 24 10 34 10-2 42-14 68-34 84-20-16-32-42-34-84 10 0 22-2 34-10z"/>
<path class="wa-c-s" d="m142 92 12 12 26-28" fill="none"/></svg>',
// Transparent reporting — report with chart and severity ratings
'eye' => '<svg viewBox="0 0 320 200" class="why-art"><rect class="wa-paper" x="70" y="22" width="180" height="160" rx="12"/><rect class="wa-ink" x="88" y="40" width="80" height="7" rx="3"/><rect class="wa-mute" x="88" y="54" width="120" height="5" rx="2"/>
<rect class="wa-a" x="92" y="104" width="18" height="40" rx="3"/><rect class="wa-b" x="118" y="88" width="18" height="56" rx="3"/><rect class="wa-c" x="144" y="116" width="18" height="28" rx="3"/><rect class="wa-cool" x="170" y="124" width="18" height="20" rx="3"/><path class="wa-line" d="M86 146h120" fill="none"/>
<g transform="translate(212 92)"><rect class="wa-hot" width="26" height="10" rx="5"/><rect class="wa-b" y="16" width="26" height="10" rx="5"/><rect class="wa-c" y="32" width="26" height="10" rx="5"/></g>
<g transform="translate(250 150)"><ellipse rx="34" ry="20" class="wa-a"/><ellipse rx="22" ry="20" class="wa-paper"/><circle r="11" class="wa-ink"/><circle cx="4" cy="-4" r="3" class="wa-paper"/></g></svg>',
// Continuous improvement — cycle arrows around a rising maturity chart
'refresh' => '<svg viewBox="0 0 320 200" class="why-art"><circle cx="160" cy="100" r="74" class="wa-soft"/>
<path class="wa-a-s" d="M96 72a72 72 0 0 1 116-22" fill="none"/><path class="wa-a" d="m216 32 6 28-28-4z"/><path class="wa-b-s" d="M224 128a72 72 0 0 1-116 22" fill="none"/><path class="wa-b" d="m104 168-6-28 28 4z"/>
<g transform="translate(118 74)"><rect class="wa-cool" x="0" y="44" width="14" height="14" rx="3"/><rect class="wa-c" x="20" y="32" width="14" height="26" rx="3"/><rect class="wa-b" x="40" y="18" width="14" height="40" rx="3"/><rect class="wa-a" x="60" y="2" width="14" height="56" rx="3"/><path class="wa-ink-s" d="M4 38 28 24l20-10L72-6" fill="none"/></g></svg>',
];
