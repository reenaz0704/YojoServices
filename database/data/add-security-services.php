<?php
/* Adds three service divisions and their services (idempotent).
   Run: php database/data/add-security-services.php */
$cfg = require __DIR__ . '/../../config/database.php';
$pdo = new PDO("mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['database']};charset=utf8mb4", $cfg['username'], $cfg['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$divisions = [
    ['network-security-assessment', 'Network Security Assessment', 'lock', 'security', 'Assess and harden your networks, wireless, logging and operational technology.',
        'Independent assessments of network architecture and exposure, carried out only with written authorisation and an agreed scope.', [
        ['network-architecture-review', 'Network Architecture Review', 'network', 'Design review', 'Review of network design, segmentation and trust boundaries against good practice and your risk profile.'],
        ['vapt-network', 'Vulnerability Assessment and Penetration Testing (VAPT)', 'radar', 'Authorised security testing', 'Identify and validate exploitable weaknesses across your network infrastructure before attackers do.'],
        ['external-penetration-testing', 'External Penetration Testing', 'globe', 'Authorised security testing', 'Authorised testing of internet-facing systems to find weaknesses reachable from outside your organisation.'],
        ['wireless-security-assessment', 'Wireless Security Assessment', 'activity', 'Authorised security testing', 'Assessment of wireless networks, encryption, authentication and rogue access point exposure.'],
        ['log-analysis', 'Log Analysis', 'file-search', 'Review & advisory', 'Review of log sources, coverage and retention, and analysis of logs for suspicious activity.'],
        ['ot-security', 'Operation Technology (OT) Security', 'factory', 'Assessment & advisory', 'Security assessment of industrial and operational technology environments with safety-first methods.'],
        ['telecom-security', 'Telecom Security', 'phone', 'Assessment & advisory', 'Assessment of telecom infrastructure and signalling exposure within an agreed, authorised scope.'],
    ]],
    ['red-teaming', 'Red Teaming', 'target', 'security', 'Adversary-style exercises that test people, process and technology together.',
        'Goal-based exercises run under written rules of engagement to measure how well your organisation prevents, detects and responds.', [
        ['phishing-vishing-campaign', 'Phishing / Vishing Campaign', 'mail', 'Authorised simulation', 'Controlled phishing and voice-phishing simulations to measure and improve staff awareness.'],
        ['ransomware-readiness', 'Ransomware Readiness', 'shield-alert', 'Assessment', 'Assessment of preventive controls, backups and recovery readiness against ransomware scenarios.'],
        ['red-team-external-penetration-testing', 'External Penetration Testing (Red Team)', 'globe', 'Authorised adversarial testing', 'Objective-driven external attack simulation as part of a red team engagement.'],
        ['threat-intelligence', 'Threat Intelligence', 'eye', 'Advisory', 'Relevant threat information about actors and techniques that may target your sector and assets.'],
        ['cyber-security-mock-drills', 'Cyber Security Mock Drills', 'refresh', 'Exercise facilitation', 'Scenario-based drills that rehearse detection, escalation and response procedures.'],
        ['dos-ddos-assessment', 'DoS / DDoS Assessment', 'server', 'Authorised testing & review', 'Review of resilience and mitigation against denial-of-service attacks, with authorised testing where agreed.'],
        ['tabletop-exercise', 'Tabletop Exercise', 'users', 'Exercise facilitation', 'Facilitated discussion-based exercises that test incident roles, decisions and communication.'],
        ['physical-security', 'Physical Security', 'building', 'Authorised assessment', 'Assessment of physical access controls that protect people, premises and information systems.'],
    ]],
    ['security-operation-center', 'Security Operation Center', 'shield', 'security', 'Design, build and improve security monitoring and response operations.',
        'Support for building and improving SOC capability: people, processes and technology for monitoring, detection and response.', [
        ['soc-designing-creation', 'SOC Designing & Creation', 'layers', 'Design & build', 'Design of SOC structure, processes, tooling and playbooks suited to your size and risks.'],
        ['soc-consulting', 'SOC Consulting', 'briefcase', 'Advisory', 'Advisory to assess and improve the maturity and effectiveness of an existing SOC.'],
        ['soc-operational-services', 'SOC Operational Services', 'activity', 'Operational support', 'Support for day-to-day SOC operations, triage and escalation processes.'],
        ['soc-as-a-service', 'SOC as a Service', 'cloud', 'Managed service (where applicable)', 'Security monitoring delivered as a service, where suitable for your environment.'],
        ['attack-surface-management', 'Attack Surface Management', 'search', 'Assessment & monitoring', 'Discovery and review of internet-facing assets and exposures across your organisation.'],
        ['monitoring-and-detection', 'Monitoring and Detection', 'eye', 'Design & advisory', 'Design and tuning of detection use cases, alerting and log coverage.'],
        ['soc-incident-response', 'Incident Response', 'alert', 'Plan design & support', 'Incident response planning and support to contain, investigate and recover from incidents.'],
        ['cyber-security-threat-assessment', 'Cyber Security Threat Assessment', 'shield-alert', 'Assessment', 'Assessment of the threats relevant to your organisation and the controls that address them.'],
        ['threat-hunting', 'Threat Hunting', 'target', 'Proactive investigation', 'Hypothesis-driven searches for threats that have evaded existing detections.'],
        ['grc-consulting', 'Governance, Risk & Compliance Consulting', 'scale', 'Advisory', 'Governance, risk and compliance advisory aligned to your SOC and security programme.'],
        ['soc-network-security', 'Network Security', 'network', 'Assessment & advisory', 'Review and strengthening of network security controls monitored by the SOC.'],
        ['awareness-and-training', 'Awareness and Training', 'book', 'Training', 'Security awareness and role-based training for staff and SOC teams.'],
    ]],
];

$order = (int) $pdo->query('SELECT COALESCE(MAX(sort_order),0) FROM service_categories WHERE sort_order < 100')->fetchColumn();
foreach ($divisions as [$slug, $name, $icon, $type, $tagline, $summary, $items]) {
    $id = $pdo->prepare('SELECT id FROM service_categories WHERE slug=?'); $id->execute([$slug]); $id = $id->fetchColumn();
    if (!$id) {
        $pdo->prepare('INSERT INTO service_categories (slug,name,tagline,summary,description,page_type,icon,sort_order,is_published) VALUES (?,?,?,?,?,?,?,?,1)')
            ->execute([$slug, $name, $tagline, $summary, $summary, $type, $icon, ++$order]);
        $id = (int) $pdo->lastInsertId();
        echo "+ division $name\n";
    }
    foreach ($items as $k => [$s, $title, $ic, $eng, $sum]) {
        $ex = $pdo->prepare('SELECT 1 FROM services WHERE slug=?'); $ex->execute([$s]);
        if ($ex->fetchColumn()) continue;
        $pdo->prepare('INSERT INTO services (category_id,slug,title,menu_group,engagement_type,icon,summary,intro,meta_title,meta_description,is_published,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,1,?)')
            ->execute([$id, $s, $title, $slug, $eng, $ic, $sum, $sum, $title . ' | Yojo Solutions', $sum, $k + 1]);
        echo "  + $title\n";
    }
}
echo "done\n";
