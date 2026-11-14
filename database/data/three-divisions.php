<?php
/* Restructures services into three divisions: Offensive Security, Defensive Security, GRC.
   Old divisions are unpublished (not deleted); old URLs redirect. Idempotent.
   Run: php database/data/three-divisions.php */
$cfg = require __DIR__ . '/../../config/database.php';
$pdo = new PDO("mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['database']};charset=utf8mb4", $cfg['username'], $cfg['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->beginTransaction();

$testNote = 'All testing is performed only after written authorisation from the asset owner, within an agreed scope, testing window and rules of engagement. We never test third-party systems without their owner\'s permission.';
$divs = [
    'offensive-security' => ['Offensive Security', 'target', 'security', 1, 'Test your defences the way a real attacker would.',
        'Authorised penetration testing, vulnerability assessment and adversary simulation across applications, networks, cloud, wireless, physical and operational technology.', $testNote],
    'defensive-security' => ['Defensive Security', 'shield', 'security', 2, 'Detect, respond to and recover from cyber threats.',
        'Security operations, monitoring, incident response, architecture and configuration reviews, and the processes and people that keep your organisation protected.',
        'Our defensive services are delivered alongside your teams. Any technical testing within these services is performed only with written authorisation and an agreed scope.'],
    'grc' => ['Governance, Risk & Compliance (GRC)', 'scale', 'compliance', 3, 'Standards-aligned governance, risk management and compliance.',
        'ISO, SOC 2, PCI DSS, HIPAA, NIST, GDPR and DPDPA readiness and internal audit, IT controls (ITGC/ITAC), risk assessments, policies and IT governance.', null],
];
$ids = [];
foreach ($divs as $slug => [$name, $icon, $type, $order, $tag, $sum, $note]) {
    $q = $pdo->prepare('SELECT id FROM service_categories WHERE slug=?'); $q->execute([$slug]); $id = $q->fetchColumn();
    if ($id) {
        $pdo->prepare('UPDATE service_categories SET name=?,icon=?,page_type=?,sort_order=?,tagline=?,summary=?,description=?,engagement_note=?,is_published=1 WHERE id=?')->execute([$name, $icon, $type, $order, $tag, $sum, $sum, $note, $id]);
    } else {
        $pdo->prepare('INSERT INTO service_categories (slug,name,icon,page_type,sort_order,tagline,summary,description,engagement_note,is_published) VALUES (?,?,?,?,?,?,?,?,?,1)')->execute([$slug, $name, $icon, $type, $order, $tag, $sum, $sum, $note]);
        $id = $pdo->lastInsertId();
    }
    $ids[$slug] = (int) $id;
}

// slug => division, in display order (audits/assessments first, then services)
$map = [
    'offensive-security' => ['web-application-vapt', 'mobile-application-vapt', 'api-security-assessment', 'network-vapt', 'vapt-network', 'infrastructure-vapt',
        'vulnerability-assessment', 'penetration-testing', 'external-penetration-testing', 'wireless-security-assessment', 'secure-code-review',
        'cloud-security-assessment', 'dos-ddos-assessment', 'physical-security', 'telecom-security', 'ot-security',
        'phishing-vishing-campaign', 'cyber-security-mock-drills', 'threat-hunting', 'attack-surface-management'],
    'defensive-security' => ['it-security-audit', 'network-architecture-review', 'security-architecture-review', 'configuration-security-assessment', 'log-analysis',
        'ransomware-readiness', 'cyber-security-threat-assessment',
        'soc-designing-creation', 'soc-consulting', 'soc-operational-services', 'soc-as-a-service', 'monitoring-and-detection', 'incident-response-advisory',
        'security-operations-consulting', 'vulnerability-management', 'threat-intelligence', 'soc-network-security', 'tabletop-exercise', 'awareness-and-training',
        'digital-evidence-preservation', 'cyber-incident-documentation', 'cybercrime-reporting-guidance', 'cyber-incident-legal-coordination', 'it-infrastructure-consulting'],
    'grc' => ['iso-27001-consulting', 'soc-2-readiness', 'pci-dss-compliance', 'hipaa-compliance', 'nist-cybersecurity-framework', 'compliance-gap-assessment',
        'itgc', 'itac', 'third-party-risk-assessment', 'cyber-risk-assessment',
        'iso-22301-business-continuity', 'iso-42001-ai-management', 'gdpr-compliance', 'dpdpa-compliance', 'data-privacy-regulatory-guidance',
        'cyber-compliance-advisory', 'policy-procedure-development', 'it-governance', 'technology-risk-advisory', 'grc-consulting',
        'technology-process-improvement', 'staff-augmentation'],
];
$upd = $pdo->prepare('UPDATE services SET category_id=?, menu_group=?, sort_order=? WHERE slug=?');
foreach ($map as $div => $slugs) foreach ($slugs as $k => $s) { $upd->execute([$ids[$div], $div, $k + 1, $s]); if (!$upd->rowCount() && !$pdo->query("SELECT 1 FROM services WHERE slug=" . $pdo->quote($s))->fetchColumn()) echo "missing: $s\n"; }

// merged duplicates: unpublish the second copy
$pdo->exec("UPDATE services SET is_published=0 WHERE slug IN ('red-team-external-penetration-testing','soc-incident-response')");
// Incident Response keeps one entry under a clear name
$pdo->exec("UPDATE services SET title='Incident Response' WHERE slug='incident-response-advisory'");
// retire the old divisions (kept in the database, unpublished)
$in = implode(',', array_map('intval', $ids));
$pdo->exec("UPDATE service_categories SET is_published=0 WHERE id NOT IN ($in)");
// any published service left in an old division?
foreach ($pdo->query("SELECT s.slug FROM services s WHERE s.is_published=1 AND s.category_id NOT IN ($in)") as $r) echo "unassigned: {$r['slug']}\n";

$pdo->commit();
echo "done\n";
