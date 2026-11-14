<?php
use App\Core\Auth;
use App\Services\AdminResources;
$user = Auth::user();
$newCount = 0;
try { $newCount = (int) db()->value("SELECT COUNT(*) FROM enquiries WHERE status = 'new'"); } catch (\Throwable $e) {}
$groups = [];
foreach (AdminResources::all() as $key => $r) {
    if (!empty($r['role']) && !Auth::can($r['role'])) continue;
    $groups[$r['group']][$key] = $r;
}
$success = flash('success');
$error = flash('error');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="base-url" content="<?= e(base_path_url()) ?>">
    <title><?= e($title ?? 'Admin') ?> · <?= e(setting('company_name')) ?> Admin</title>
    <link rel="icon" href="<?= asset('images/favicon.ico') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('admin/admin.css') ?>">
</head>
<body class="adm">
<aside class="adm-side" id="admSide">
    <a class="adm-brand" href="<?= url('admin') ?>"><img src="<?= asset('images/logo-circle.png') ?>" alt="" width="36" height="36"><span><strong><?= e(setting('company_name')) ?></strong><small>Control Center</small></span></a>
    <nav class="adm-nav">
        <p>Overview</p>
        <a class="<?= current_path() === '/admin' ? 'is-active' : '' ?>" href="<?= url('admin') ?>"><?= icon('grid') ?>Dashboard</a>
        <a class="<?= is_active('/admin/enquiries') ? 'is-active' : '' ?>" href="<?= url('admin/enquiries') ?>"><?= icon('inbox') ?>Enquiries<?php if ($newCount): ?><span class="adm-count"><?= $newCount ?></span><?php endif; ?></a>
        <a class="<?= is_active('/admin/assessments') ? 'is-active' : '' ?>" href="<?= url('admin/assessments') ?>"><?= icon('checklist') ?>Assessments</a>
        <?php foreach ($groups as $g => $items): ?>
        <p><?= e($g) ?></p>
        <?php foreach ($items as $key => $r): ?><a class="<?= is_active('/admin/manage/' . $key) ? 'is-active' : '' ?>" href="<?= url('admin/manage/' . $key) ?>"><?= icon($r['icon']) ?><?= e($r['label']) ?></a><?php endforeach; ?>
        <?php if ($g === 'System' || ($g === 'Credibility' && !isset($groups['System']))): ?><?php endif; ?>
        <?php endforeach; ?>
        <?php if (Auth::can('admin')): ?>
        <p>Configuration</p>
        <a class="<?= is_active('/admin/settings') ? 'is-active' : '' ?>" href="<?= url('admin/settings') ?>"><?= icon('settings') ?>Site settings</a>
        <a class="<?= is_active('/admin/activity') ? 'is-active' : '' ?>" href="<?= url('admin/activity') ?>"><?= icon('activity') ?>Activity log</a>
        <?php endif; ?>
    </nav>
</aside>
<div class="adm-main">
    <header class="adm-top">
        <button class="adm-burger" id="admBurger" aria-label="Toggle navigation"><?= icon('menu') ?></button>
        <h1><?= e($title ?? '') ?></h1>
        <a class="adm-btn adm-btn--ghost adm-hide-sm" href="<?= url('/') ?>" target="_blank" rel="noopener"><?= icon('external', 'icon icon--sm') ?> View site</a>
        <div class="adm-user">
            <span class="adm-avatar"><?= e(mb_substr($user['name'], 0, 1)) ?></span>
            <span class="adm-hide-sm"><strong><?= e($user['name']) ?></strong><small><?= e($user['role_name']) ?></small></span>
            <div class="adm-user__menu">
                <a href="<?= url('admin/profile') ?>"><?= icon('user', 'icon icon--sm') ?> My profile</a>
                <form method="post" action="<?= url('admin/logout') ?>"><?= csrf_field() ?><button><?= icon('logout', 'icon icon--sm') ?> Sign out</button></form>
            </div>
        </div>
    </header>
    <div class="adm-content">
        <?php if ($success): ?><div class="adm-flash adm-flash--ok"><?= icon('check-circle', 'icon icon--sm') ?><?= e($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="adm-flash adm-flash--err"><?= icon('alert', 'icon icon--sm') ?><?= e($error) ?></div><?php endif; ?>
        <?= $content ?>
    </div>
</div>
<script src="<?= asset('admin/admin.js') ?>" defer></script>
</body>
</html>
