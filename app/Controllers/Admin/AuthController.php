<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Validator;

class AuthController
{
    public function loginForm(): string
    {
        if (Auth::check()) {
            redirect('admin');
        }
        return view('admin/login', ['title' => 'Sign in'], null);
    }

    public function login(): string
    {
        $email = strtolower(trim((string) input('email', '')));
        $password = (string) input('password', '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            flash('error', 'Enter your email and password.');
            redirect('admin/login');
        }
        list($ok, $msg) = Auth::attempt($email, $password);
        if (!$ok) {
            flash('error', $msg);
            redirect('admin/login');
        }
        redirect('admin');
        return '';
    }

    public function logout(): string
    {
        Auth::log('logout', 'users', (int) ($_SESSION['user_id'] ?? 0));
        Auth::logout();
        redirect('admin/login');
        return '';
    }

    public function profile(): string
    {
        return view('admin/profile', ['title' => 'My profile', 'user' => db()->fetch('SELECT id, name, email, designation, bio FROM users WHERE id = ?', [Auth::user()['id']])], 'layouts/admin');
    }

    public function updateProfile(): string
    {
        $id = (int) Auth::user()['id'];
        $data = ['name' => trim((string) input('name')), 'designation' => trim((string) input('designation')), 'bio' => trim((string) input('bio'))];
        $v = Validator::make($data, ['name' => 'required|max:120', 'designation' => 'max:120', 'bio' => 'max:1000']);
        if ($v->fails()) {
            flash('error', implode(' ', $v->errors()));
            redirect('admin/profile');
        }
        $new = (string) input('new_password', '');
        if ($new !== '') {
            $row = db()->fetch('SELECT password_hash FROM users WHERE id = ?', [$id]);
            if (!password_verify((string) input('current_password', ''), $row['password_hash'])) {
                flash('error', 'Current password is incorrect.');
                redirect('admin/profile');
            }
            if ($err = self::passwordError($new)) {
                flash('error', $err);
                redirect('admin/profile');
            }
            $data['password_hash'] = password_hash($new, PASSWORD_DEFAULT);
        }
        db()->update('users', $data, $id);
        Auth::log('profile_update', 'users', $id);
        flash('success', 'Profile updated.');
        redirect('admin/profile');
        return '';
    }

    public static function passwordError(string $pw): ?string
    {
        if (strlen($pw) < 10 || !preg_match('/[A-Za-z]/', $pw) || !preg_match('/\d/', $pw)) {
            return 'Password must be at least 10 characters and include letters and numbers.';
        }
        return null;
    }
}
