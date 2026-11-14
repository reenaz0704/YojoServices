<?php
namespace App\Controllers\Admin;

use App\Core\Auth;

class SettingsController
{
    public function index(): string
    {
        $rows = db()->fetchAll('SELECT * FROM settings ORDER BY setting_group, sort_order');
        $groups = [];
        foreach ($rows as $r) {
            $groups[$r['setting_group']][] = $r;
        }
        return view('admin/settings', ['title' => 'Site settings', 'groups' => $groups, 'mail' => config('mail')], 'layouts/admin');
    }

    public function save(): string
    {
        $errors = [];
        $posted = is_array($_POST['settings'] ?? null) ? $_POST['settings'] : [];
        foreach (db()->fetchAll('SELECT setting_key, input_type, label FROM settings') as $s) {
            if (!array_key_exists($s['setting_key'], $posted)) {
                continue;
            }
            $v = trim((string) $posted[$s['setting_key']]);
            if ($v !== '' && $s['input_type'] === 'email' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
                $errors[] = $s['label'] . ': invalid email.';
                continue;
            }
            if ($v !== '' && $s['input_type'] === 'url' && !filter_var($v, FILTER_VALIDATE_URL)) {
                $errors[] = $s['label'] . ': invalid URL.';
                continue;
            }
            if ($s['input_type'] === 'number') {
                $v = (string) max(1, min(120, (int) $v));
            } elseif ($s['input_type'] === 'layout') {
                $v = array_key_exists($v, site_layouts()) ? $v : '';
            } elseif ($s['input_type'] === 'scene') {
                $v = array_key_exists($v, site_scene_styles()) ? $v : '';
            } elseif ($s['input_type'] === 'template') {
                $v = array_key_exists($v, site_templates()) ? $v : 'emerald';
            } elseif ($s['input_type'] === 'bool') {
                $v = $v === '1' ? '1' : '0';
            }
            db()->query('UPDATE settings SET setting_value = ? WHERE setting_key = ?', [mb_substr($v, 0, 5000), $s['setting_key']]);
        }
        Auth::log('settings_update', 'settings');
        flash($errors ? 'error' : 'success', $errors ? implode(' ', $errors) : 'Settings saved.');
        redirect('admin/settings');
        return '';
    }
}
