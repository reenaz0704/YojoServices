<form method="post" action="<?= url('admin/profile') ?>" class="adm-narrow">
    <?= csrf_field() ?>
    <section class="panel">
        <header class="panel__head"><h2>Profile</h2></header>
        <div class="fld"><label>Email</label><input value="<?= e($user['email']) ?>" disabled></div>
        <div class="fld"><label>Name</label><input name="name" value="<?= e($user['name']) ?>" required maxlength="120"></div>
        <div class="fld"><label>Designation</label><input name="designation" value="<?= e($user['designation']) ?>" maxlength="120"></div>
        <div class="fld"><label>Short bio (shown on articles)</label><textarea name="bio" rows="3" maxlength="1000"><?= e($user['bio']) ?></textarea></div>
    </section>
    <section class="panel">
        <header class="panel__head"><h2>Change password</h2></header>
        <div class="fld"><label>Current password</label><input type="password" name="current_password" autocomplete="current-password"></div>
        <div class="fld"><label>New password</label><input type="password" name="new_password" autocomplete="new-password" minlength="10"><small>At least 10 characters with letters and numbers.</small></div>
    </section>
    <button class="adm-btn adm-btn--primary">Save profile</button>
</form>
