<?php
/** Renders a newline list; "Title :: detail" lines become titled items. Vars: $text, $style (check|steps|cards) */
$items = lines($text ?? '');
$style = $style ?? 'check';
if (!$items) return;
?>
<?php if ($style === 'steps'): ?>
<ol class="steps">
    <?php foreach ($items as $n => $line): list($t, $d) = split_item($line); ?>
    <li class="steps__item reveal"><span class="steps__n mono"><?= str_pad((string) ($n + 1), 2, '0', STR_PAD_LEFT) ?></span><div><h4><?= e($t) ?></h4><?php if ($d): ?><p><?= e($d) ?></p><?php endif; ?></div></li>
    <?php endforeach; ?>
</ol>
<?php elseif ($style === 'cards'): ?>
<div class="mini-cards">
    <?php foreach ($items as $line): list($t, $d) = split_item($line); ?>
    <div class="mini-card reveal"><span class="mini-card__icon"><?= icon('check') ?></span><div><h4><?= e($t) ?></h4><?php if ($d): ?><p><?= e($d) ?></p><?php endif; ?></div></div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<ul class="checklist">
    <?php foreach ($items as $line): list($t, $d) = split_item($line); ?>
    <li><?= icon('check-circle', 'icon icon--ok') ?><span><?php if ($d): ?><strong><?= e($t) ?></strong> — <?= e($d) ?><?php else: ?><?= e($t) ?><?php endif; ?></span></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
