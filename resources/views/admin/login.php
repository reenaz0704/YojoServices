<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow">
    <title>Sign in · <?= e(setting('company_name')) ?> Admin</title>
    <link rel="icon" href="<?= asset('images/favicon.ico') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('admin/admin.css') ?>">
</head>
<body class="adm-login">
    <div class="adm-login__art" aria-hidden="true"><div class="grid"></div></div>
    <main class="adm-login__card">
        <img src="<?= asset('images/logo.png') ?>" alt="<?= e(setting('company_name')) ?>" width="220" height="50">
        <h1>Control Center</h1>
        <p>Sign in to manage content, enquiries and assessments.</p>
        <?php if ($err = flash('error')): ?><div class="adm-flash adm-flash--err"><?= e($err) ?></div><?php endif; ?>
        <form method="post" action="<?= url('admin/login') ?>" autocomplete="on">
            <?= csrf_field() ?>
            <label>Email<input type="email" name="email" required autofocus autocomplete="username"></label>
            <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
            <button class="adm-btn adm-btn--primary adm-btn--block" type="submit">Sign in</button>
        </form>
        <a class="adm-login__back" href="<?= url('/') ?>">← Back to website</a>
    </main>
</body>
</html>
