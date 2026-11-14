<?php
/**
 * Migration 002 — website enhancement (idempotent; safe to run more than once).
 * Adds hero slides, frameworks, FAQ categories, industry storytelling fields,
 * five service divisions, popup / footer / Who We Are settings.
 *
 * Usage:  php database/migrate.php
 * (database/install.php runs it automatically after importing seed.sql)
 */
if (PHP_SAPI !== 'cli') { exit("CLI only\n"); }
require_once dirname(__DIR__) . '/app/Core/bootstrap.php';

$d = require __DIR__ . '/data/enhancement.php';
$db = db();
$pdo = $db->pdo();

$hasColumn = function (string $table, string $col) use ($db): bool {
    return (bool) $db->value('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?', [$table, $col]);
};
$addColumn = function (string $table, string $col, string $def) use ($pdo, $hasColumn) {
    if (!$hasColumn($table, $col)) { $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$col` $def"); }
};

// ---------------------------------------------------------------- Schema
$pdo->exec("CREATE TABLE IF NOT EXISTS hero_slides (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  label VARCHAR(120) NOT NULL,
  title VARCHAR(190) NOT NULL,
  subtitle VARCHAR(255) NULL,
  description TEXT NULL,
  cta_label VARCHAR(80) NOT NULL,
  cta_url VARCHAR(255) NOT NULL,
  cta2_label VARCHAR(80) NULL,
  cta2_url VARCHAR(255) NULL,
  image VARCHAR(255) NULL,
  theme VARCHAR(20) NOT NULL DEFAULT 'navy',
  icon VARCHAR(40) NOT NULL DEFAULT 'shield',
  duration_seconds TINYINT UNSIGNED NOT NULL DEFAULT 5,
  sort_order SMALLINT NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS frameworks (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  slug VARCHAR(80) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  short_name VARCHAR(40) NOT NULL,
  region VARCHAR(80) NULL,
  icon VARCHAR(40) NOT NULL DEFAULT 'shield-check',
  image VARCHAR(255) NULL,
  overview TEXT NOT NULL,
  purpose TEXT NULL,
  requirements TEXT NULL,
  why_needed TEXT NULL,
  how_we_help TEXT NULL,
  service_id INT UNSIGNED NULL,
  sort_order SMALLINT NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_fw_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS faq_categories (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  slug VARCHAR(80) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  icon VARCHAR(40) NOT NULL DEFAULT 'info',
  sort_order SMALLINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$addColumn('faqs', 'category_id', 'INT UNSIGNED NULL AFTER id');
$addColumn('industries', 'image', 'VARCHAR(255) NULL AFTER icon');
$addColumn('industries', 'risk_tags', 'VARCHAR(255) NULL AFTER summary');
$addColumn('industries', 'governance', 'TEXT NULL AFTER challenges');
$addColumn('industries', 'solutions', 'TEXT NULL AFTER governance');
$addColumn('industries', 'approach', 'TEXT NULL AFTER solutions');
$addColumn('service_categories', 'image', 'VARCHAR(255) NULL AFTER icon');
$addColumn('services', 'image', 'VARCHAR(255) NULL AFTER icon');
$pdo->exec("ALTER TABLE service_categories MODIFY page_type ENUM('compliance','security','assurance','advisory','legal') NOT NULL DEFAULT 'compliance'");
$pdo->exec('ALTER TABLE services MODIFY menu_group VARCHAR(40) NOT NULL DEFAULT \'\'');

// ---------------------------------------------------------------- Service divisions
$n = 0;
$catIds = [];
foreach ($d['divisions'] as $slug => $c) {
    $n++;
    $row = ['name' => $c['name'], 'tagline' => $c['tagline'], 'summary' => $c['summary'], 'description' => $c['description'],
        'engagement_note' => $c['engagement_note'], 'page_type' => $c['page_type'], 'icon' => $c['icon'], 'sort_order' => $n, 'is_published' => 1];
    $id = $db->value('SELECT id FROM service_categories WHERE slug = ?', [$slug]);
    if ($id) { $db->update('service_categories', $row, (int) $id); }
    else { $id = $db->insert('service_categories', ['slug' => $slug] + $row); }
    $catIds[$slug] = (int) $id;
}
// Older categories are retired from navigation (kept for history, unpublished).
$in = implode(',', array_fill(0, count($catIds), '?'));
$db->query("UPDATE service_categories SET is_published = 0, sort_order = sort_order + 100 WHERE id NOT IN ($in) AND sort_order < 100", array_values($catIds));

foreach ($d['remap'] as $slug => $m) {
    $set = ['category_id' => $catIds[$m[0]], 'menu_group' => $m[0], 'sort_order' => $m[2], 'is_published' => 1];
    if ($m[1]) { $set['title'] = $m[1]; $set['meta_title'] = $m[1] . ' | Yojo Solutions'; }
    $sid = $db->value('SELECT id FROM services WHERE slug = ?', [$slug]);
    if ($sid) { $db->update('services', $set, (int) $sid); }
}
if ($d['retire']) {
    $in = implode(',', array_fill(0, count($d['retire']), '?'));
    $db->query("UPDATE services SET is_published = 0 WHERE slug IN ($in)", $d['retire']);
}

foreach ($d['newServices'] as $div => $list) {
    $def = $d['defaults'][$div] ?? null;
    foreach ($list as $s) {
        list($slug, $title, $icon, $eng, $summary, $intro, $why, $acts, $sort) = $s;
        if ($db->value('SELECT id FROM services WHERE slug = ?', [$slug])) { continue; }
        if (!$def) { // reuse defaults of an existing service in the division
            $def = $db->fetch('SELECT methodology, deliverables, benefits FROM services WHERE category_id = ? AND methodology IS NOT NULL LIMIT 1', [$catIds[$div]]) ?: [];
        }
        $id = $db->insert('services', [
            'category_id' => $catIds[$div], 'slug' => $slug, 'title' => $title, 'menu_group' => $div, 'engagement_type' => $eng,
            'icon' => $icon, 'summary' => $summary, 'intro' => $intro, 'why_needed' => $why, 'requirements' => implode("\n", $acts),
            'methodology' => $def['methodology'] ?? null, 'deliverables' => $def['deliverables'] ?? null, 'benefits' => $def['benefits'] ?? null,
            'meta_title' => $title . ' | Yojo Solutions', 'meta_description' => mb_substr($summary, 0, 300), 'sort_order' => $sort, 'is_published' => 1,
        ]);
        $k = 0;
        foreach ($d['defaults'][$div]['faqs'] ?? [] as $f) {
            $db->insert('service_faqs', ['service_id' => $id, 'question' => $f[0], 'answer' => $f[1], 'sort_order' => ++$k]);
        }
        $db->insert('service_faqs', ['service_id' => $id, 'question' => 'How do we get started with ' . $title . '?',
            'answer' => 'Book a short consultation. We will discuss your objectives, the systems or processes in scope and timelines, then share a proposal with a clear scope, deliverables and fees.', 'sort_order' => 10]);
    }
}

// ---------------------------------------------------------------- Slides
if (!(int) $db->value('SELECT COUNT(*) FROM hero_slides')) {
    foreach ($d['slides'] as $k => $s) {
        $db->insert('hero_slides', ['label' => $s[0], 'title' => $s[1], 'subtitle' => $s[2], 'description' => $s[3], 'cta_label' => $s[4], 'cta_url' => $s[5],
            'cta2_label' => $s[6], 'cta2_url' => $s[7], 'theme' => $s[8], 'icon' => $s[9], 'duration_seconds' => 5, 'sort_order' => $k + 1]);
    }
}

// ---------------------------------------------------------------- Frameworks
foreach ($d['frameworks'] as $k => $f) {
    if ($db->value('SELECT id FROM frameworks WHERE slug = ?', [$f[0]])) { continue; }
    $db->insert('frameworks', ['slug' => $f[0], 'name' => $f[1], 'short_name' => $f[2], 'region' => $f[3], 'icon' => $f[4],
        'service_id' => $db->value('SELECT id FROM services WHERE slug = ?', [$f[5]]) ?: null,
        'overview' => $f[6], 'purpose' => $f[7], 'requirements' => implode("\n", $f[8]), 'why_needed' => $f[9], 'how_we_help' => $f[10], 'sort_order' => $k + 1]);
}

// ---------------------------------------------------------------- FAQ categories & FAQs
$fcIds = [];
foreach ($d['faqCategories'] as $k => $c) {
    $id = $db->value('SELECT id FROM faq_categories WHERE slug = ?', [$c[0]]);
    if (!$id) { $id = $db->insert('faq_categories', ['slug' => $c[0], 'name' => $c[1], 'icon' => $c[2], 'sort_order' => $k + 1]); }
    $fcIds[$c[0]] = (int) $id;
}
foreach ($d['faqMap'] as $prefix => $cat) {
    $db->query('UPDATE faqs SET category_id = ?, sort_order = sort_order + 50 WHERE category_id IS NULL AND question LIKE ?', [$fcIds[$cat], $prefix . '%']);
}
foreach ($d['faqs'] as $k => $f) {
    if ($db->value('SELECT id FROM faqs WHERE question = ?', [$f[0]])) { continue; }
    $db->insert('faqs', ['category_id' => $fcIds[$f[2]], 'question' => $f[0], 'answer' => $f[1], 'topic' => '', 'sort_order' => $k + 1, 'is_published' => 1]);
}
$db->query('UPDATE faqs SET category_id = ? WHERE category_id IS NULL', [$fcIds['general']]);
// Plain-text legacy answers -> paragraphs (answers are now sanitised HTML)
$db->query("UPDATE faqs SET answer = CONCAT('<p>', answer, '</p>') WHERE answer NOT LIKE '<%'");

// ---------------------------------------------------------------- Industries
foreach ($d['industryExtra'] as $slug => $x) {
    $db->query('UPDATE industries SET risk_tags = COALESCE(risk_tags, ?), governance = COALESCE(governance, ?), solutions = COALESCE(solutions, ?), approach = COALESCE(approach, ?) WHERE slug = ?',
        [$x[0], implode("\n", $x[1]), implode("\n", $x[2]), implode("\n", $x[3]), $slug]);
}
// Recommended services that were retired -> nearest published equivalents
$swap = ['itgc-audit' => 'itgc', 'privacy-impact-assessment' => 'data-privacy-regulatory-guidance', 'hitrust-readiness' => 'iso-27001-consulting',
    'vendor-risk-assessment' => 'third-party-risk-assessment', 'business-continuity-assessment' => 'iso-22301-business-continuity',
    'cloud-infrastructure-audit' => 'cloud-security-assessment', 'access-management-audit' => 'it-security-audit', 'data-privacy-assessment' => 'dpdpa-compliance',
    'enterprise-risk-assessment' => 'technology-risk-advisory', 'data-protection-governance' => 'dpdpa-compliance', 'change-management-audit' => 'itgc',
    'it-operations-audit' => 'it-security-audit', 'itac-audit' => 'itac'];
foreach ($swap as $old => $new) {
    $o = $db->value('SELECT id FROM services WHERE slug = ?', [$old]);
    $nw = $db->value('SELECT id FROM services WHERE slug = ?', [$new]);
    if ($o && $nw) {
        $db->query('INSERT IGNORE INTO industry_service (industry_id, service_id) SELECT industry_id, ? FROM industry_service WHERE service_id = ?', [$nw, $o]);
        $db->query('DELETE FROM industry_service WHERE service_id = ?', [$o]);
    }
}

// ---------------------------------------------------------------- Settings
$max = (int) $db->value('SELECT COALESCE(MAX(sort_order), 0) FROM settings');
foreach ($d['settings'] as $s) {
    if ($db->value('SELECT COUNT(*) FROM settings WHERE setting_key = ?', [$s[0]])) { continue; }
    $db->insert('settings', ['setting_key' => $s[0], 'setting_value' => $s[1], 'setting_group' => $s[2], 'label' => $s[3], 'input_type' => $s[4], 'sort_order' => ++$max]);
}
// The hero is now the slide presentation (Admin → Homepage Slides)
$db->query("DELETE FROM settings WHERE setting_key IN ('hero_eyebrow', 'hero_title', 'hero_subtitle')");
$db->query("UPDATE settings SET label = 'Short introduction' WHERE setting_key = 'company_intro'");

echo "Migration 002 applied: " . $db->value('SELECT COUNT(*) FROM services WHERE is_published = 1') . " published services in "
    . $db->value('SELECT COUNT(*) FROM service_categories WHERE is_published = 1') . " divisions, "
    . $db->value('SELECT COUNT(*) FROM hero_slides') . " slides, " . $db->value('SELECT COUNT(*) FROM frameworks') . " frameworks, "
    . $db->value('SELECT COUNT(*) FROM faqs') . " FAQs.\n";
