<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Models\Post;
use App\Services\AdminResources;
use App\Services\HtmlSanitizer;
use App\Services\Icons;
use App\Services\Uploader;

/**
 * Generic CRUD for every resource defined in AdminResources.
 */
class CrudController
{
    const PER_PAGE = 25;

    private function resource(string $key): array
    {
        $r = AdminResources::get($key);
        if (!$r) {
            abort(404, 'Unknown section');
        }
        if (!empty($r['role']) && !Auth::can($r['role'])) {
            \App\Middleware\RoleMiddleware::handle($r['role']);
        }
        $r['key'] = $key;
        return $r;
    }

    public function index(string $key): string
    {
        $r = $this->resource($key);
        $q = mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 100);
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $base = $r['list_join'] ?? "SELECT t.* FROM `{$r['table']}` t";
        $where = '1';
        $params = [];
        if ($q !== '') {
            $parts = [];
            foreach ($r['search'] as $i => $col) {
                $parts[] = "t.`$col` LIKE :q$i";
                $params["q$i"] = '%' . $q . '%';
            }
            $where = '(' . implode(' OR ', $parts) . ')';
        }
        $total = (int) db()->value("SELECT COUNT(*) FROM ($base WHERE $where) x", $params);
        // Qualify order columns with the main table alias to avoid ambiguity in joins.
        $order = preg_replace('/(?<![.\w])(?!(?:ASC|DESC)\b)([a-z_]+)(?=\s*(,|\)|$|\s+(ASC|DESC)))/i', 't.$1', $r['order']);
        $rows = db()->fetchAll("$base WHERE $where ORDER BY $order LIMIT " . self::PER_PAGE . ' OFFSET ' . (($page - 1) * self::PER_PAGE), $params);
        return view('admin/crud/index', [
            'title' => $r['label'], 'r' => $r, 'rows' => $rows, 'q' => $q, 'page' => $page,
            'pages' => (int) ceil($total / self::PER_PAGE), 'total' => $total,
        ], 'layouts/admin');
    }

    public function create(string $key): string
    {
        $r = $this->resource($key);
        $item = [];
        foreach ($r['fields'] as $name => $f) {
            $item[$name] = $f['default'] ?? null;
        }
        if ($key === 'posts') {
            $item['author_id'] = Auth::user()['id'];
            $item['status'] = 'draft';
        }
        return $this->form($r, $item, $_SESSION['_old'] ?? null);
    }

    public function edit(string $key, string $id): string
    {
        $r = $this->resource($key);
        $item = db()->fetch("SELECT * FROM `{$r['table']}` WHERE id = ?", [(int) $id]);
        if (!$item) {
            abort(404, 'Record not found');
        }
        foreach ($r['fields'] as $name => $f) {
            if ($f['type'] === 'pivot') {
                list($tbl, $own, $other) = $f['pivot'];
                $item[$name] = array_map('intval', array_column(db()->fetchAll("SELECT `$other` FROM `$tbl` WHERE `$own` = ?", [(int) $id]), $other));
            }
            if ($f['type'] === 'tags') {
                $item[$name] = implode(', ', array_column(Post::tags((int) $id), 'name'));
            }
        }
        return $this->form($r, $item, $_SESSION['_old'] ?? null);
    }

    private function form(array $r, array $item, ?array $old): string
    {
        unset($_SESSION['_old']);
        if ($old) {
            $item = array_merge($item, $old);
        }
        $options = [];
        foreach ($r['fields'] as $name => $f) {
            if (isset($f['options'])) {
                $options[$name] = AdminResources::options($f['options']);
            }
        }
        return view('admin/crud/form', [
            'title' => (empty($item['id']) ? 'New ' : 'Edit ') . strtolower($r['singular']),
            'r' => $r, 'item' => $item, 'options' => $options, 'icons' => Icons::names(),
            'errors' => $_SESSION['_errors'] ?? [],
        ], 'layouts/admin') . $this->clearErrors();
    }

    private function clearErrors(): string
    {
        unset($_SESSION['_errors']);
        return '';
    }

    public function store(string $key): string
    {
        return $this->save($this->resource($key), null);
    }

    public function update(string $key, string $id): string
    {
        $r = $this->resource($key);
        if (!db()->fetch("SELECT id FROM `{$r['table']}` WHERE id = ?", [(int) $id])) {
            abort(404);
        }
        return $this->save($r, (int) $id);
    }

    /** Validate, normalise and persist a record from POST data. */
    private function save(array $r, ?int $id): string
    {
        $data = [];
        $errors = [];
        $pivots = [];
        $tags = null;
        foreach ($r['fields'] as $name => $f) {
            $raw = $_POST[$name] ?? null;
            switch ($f['type']) {
                case 'bool':
                    $data[$name] = !empty($raw) ? 1 : 0;
                    continue 2;
                case 'number':
                    $data[$name] = ($raw === '' || $raw === null) ? 0 : (int) $raw;
                    continue 2;
                case 'pivot':
                    $pivots[$name] = array_map('intval', is_array($raw) ? $raw : []);
                    continue 2;
                case 'tags':
                    $tags = (string) $raw;
                    continue 2;
                case 'image':
                    if (!empty($_FILES[$name]['name'])) {
                        list($ok, $res) = Uploader::store($_FILES[$name]);
                        if ($ok) {
                            $data[$name] = $res;
                        } else {
                            $errors[$name] = $res;
                        }
                    } elseif (!empty($_POST[$name . '_remove'])) {
                        $data[$name] = null;
                    } elseif (!empty($_POST[$name . '_url'])) {
                        $data[$name] = basename((string) $_POST[$name . '_url']);
                    }
                    continue 2;
                case 'password':
                    if ((string) $raw !== '') {
                        if ($err = AuthController::passwordError((string) $raw)) {
                            $errors[$name] = $err;
                        } else {
                            $data['password_hash'] = password_hash((string) $raw, PASSWORD_DEFAULT);
                        }
                    } elseif (!$id) {
                        $errors[$name] = 'Password is required for new users.';
                    }
                    continue 2;
                case 'rich':
                    $value = HtmlSanitizer::clean((string) $raw);
                    break;
                case 'datetime':
                    $value = $raw ? date('Y-m-d H:i:s', strtotime((string) $raw)) : null;
                    break;
                case 'select':
                    $value = ($raw === '' || $raw === null) ? null : (string) $raw;
                    $opts = AdminResources::options($f['options']);
                    if ($value !== null && !array_key_exists($value, $opts)) {
                        $errors[$name] = 'Invalid selection.';
                    }
                    break;
                default:
                    $value = trim((string) $raw);
                    if ($value === '' && $f['type'] !== 'slug') {
                        $value = null;
                    }
            }
            if ($f['type'] === 'slug') {
                $value = slugify($value !== '' ? $value : (string) ($_POST[$f['from']] ?? ''));
            }
            if (!empty($f['required']) && ($value === null || $value === '' || ($f['type'] === 'rich' && trim(strip_tags($value)) === ''))) {
                $errors[$name] = $f['label'] . ' is required.';
            }
            if ($value !== null && !empty($f['max']) && mb_strlen((string) $value) > $f['max']) {
                $errors[$name] = $f['label'] . ' may not exceed ' . $f['max'] . ' characters.';
            }
            if ($value !== null && $f['type'] === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$name] = 'Enter a valid email.';
            }
            if ($value !== null && $f['type'] === 'url' && !filter_var($value, FILTER_VALIDATE_URL)) {
                $errors[$name] = 'Enter a valid URL (https://…).';
            }
            $data[$name] = $value;
        }
        if (isset($data['slug'])) {
            $dupe = db()->value("SELECT id FROM `{$r['table']}` WHERE slug = ? AND id <> ?", [$data['slug'], (int) $id]);
            if ($dupe) {
                $errors['slug'] = 'This slug is already used. Choose another.';
            }
        }
        if ($r['key'] === 'users' && isset($data['email'])) {
            if (db()->value('SELECT id FROM users WHERE email = ? AND id <> ?', [$data['email'], (int) $id])) {
                $errors['email'] = 'Another user already uses this email.';
            }
            if ($id === (int) Auth::user()['id'] && (empty($data['is_active']) || (int) $data['role_id'] !== 4)) {
                $errors['role_id'] = 'You cannot deactivate or demote your own account.';
            }
        }
        if ($r['key'] === 'posts' && $data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $back = $id ? "admin/manage/{$r['key']}/$id/edit" : "admin/manage/{$r['key']}/create";
        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old'] = array_intersect_key($_POST, $r['fields']);
            unset($_SESSION['_old']['password']);
            flash('error', 'Please fix the highlighted fields.');
            redirect($back);
        }

        $id = db()->transaction(function ($db) use ($r, $data, $id, $pivots) {
            if ($id) {
                $db->update($r['table'], $data, $id);
            } else {
                $id = $db->insert($r['table'], $data);
            }
            foreach ($pivots as $name => $ids) {
                list($tbl, $own, $other) = $r['fields'][$name]['pivot'];
                $db->query("DELETE FROM `$tbl` WHERE `$own` = ?", [$id]);
                foreach (array_unique($ids) as $oid) {
                    $db->query("INSERT INTO `$tbl` (`$own`, `$other`) VALUES (?, ?)", [$id, $oid]);
                }
            }
            return $id;
        });
        if ($tags !== null) {
            Post::syncTags($id, $tags);
        }
        Auth::log($id ? 'save' : 'create', $r['table'], $id, $data['title'] ?? $data['name'] ?? $data['question'] ?? $data['label'] ?? $data['client_name'] ?? '');
        flash('success', $r['singular'] . ' saved.');
        redirect(isset($_POST['_stay']) ? "admin/manage/{$r['key']}/$id/edit" : "admin/manage/{$r['key']}");
        return '';
    }

    public function destroy(string $key, string $id): string
    {
        $r = $this->resource($key);
        $id = (int) $id;
        if ($key === 'users' && $id === (int) Auth::user()['id']) {
            flash('error', 'You cannot delete your own account.');
            redirect("admin/manage/$key");
        }
        try {
            db()->delete($r['table'], $id);
            Auth::log('delete', $r['table'], $id);
            flash('success', $r['singular'] . ' deleted.');
        } catch (\PDOException $e) {
            flash('error', 'This record is in use by other content and cannot be deleted. Unpublish it instead.');
        }
        redirect("admin/manage/$key");
        return '';
    }

    /** Quick publish/unpublish toggle from the list view. */
    public function toggle(string $key, string $id): string
    {
        $r = $this->resource($key);
        $id = (int) $id;
        if (isset($r['toggle'])) {
            list($col, $on, $off) = $r['toggle'];
            $cur = db()->value("SELECT `$col` FROM `{$r['table']}` WHERE id = ?", [$id]);
            $new = $cur === $on ? $off : $on;
            $set = [$col => $new];
            if ($key === 'posts' && $new === 'published') {
                $set['published_at'] = db()->value('SELECT published_at FROM posts WHERE id = ?', [$id]) ?: date('Y-m-d H:i:s');
            }
            db()->update($r['table'], $set, $id);
        } elseif (isset($r['fields']['is_published'])) {
            db()->query("UPDATE `{$r['table']}` SET is_published = 1 - is_published WHERE id = ?", [$id]);
        } elseif (isset($r['fields']['is_active'])) {
            if ($id !== (int) Auth::user()['id']) {
                db()->query('UPDATE users SET is_active = 1 - is_active WHERE id = ?', [$id]);
            }
        }
        Auth::log('toggle', $r['table'], $id);
        flash('success', 'Status updated.');
        redirect("admin/manage/$key" . (!empty($_POST['_q']) ? '?q=' . urlencode((string) $_POST['_q']) : ''));
        return '';
    }

    /** Move a record up or down in display order (resources with a sort_order field). */
    public function move(string $key, string $id): string
    {
        $r = $this->resource($key);
        $id = (int) $id;
        if (isset($r['fields']['sort_order'])) {
            $scope = $r['sort_scope'] ?? null;
            $where = '1 = 1';
            $params = [];
            if ($scope) {
                $where = "`$scope` <=> ?";
                $params[] = db()->value("SELECT `$scope` FROM `{$r['table']}` WHERE id = ?", [$id]);
            }
            $ids = array_map('intval', array_column(db()->fetchAll("SELECT id FROM `{$r['table']}` WHERE $where ORDER BY sort_order, id", $params), 'id'));
            $pos = array_search($id, $ids, true);
            $to = ($_POST['dir'] ?? '') === 'up' ? $pos - 1 : $pos + 1;
            if ($pos !== false && isset($ids[$to])) {
                $ids[$pos] = $ids[$to];
                $ids[$to] = $id;
            }
            foreach ($ids as $n => $rowId) {
                db()->query("UPDATE `{$r['table']}` SET sort_order = ? WHERE id = ?", [$n + 1, $rowId]);
            }
            Auth::log('reorder', $r['table'], $id);
        }
        redirect("admin/manage/$key" . (!empty($_POST['_q']) ? '?q=' . urlencode((string) $_POST['_q']) : ''));
        return '';
    }
}
