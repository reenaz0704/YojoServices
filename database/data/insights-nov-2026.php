<?php
/* Adds five cybersecurity insight articles with thumbnails (idempotent by slug).
   Thumbnails live in storage/uploads. Run: php database/data/insights-nov-2026.php */
$cfg = require __DIR__ . '/../../config/database.php';
$pdo = new PDO("mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['database']};charset=utf8mb4", $cfg['username'], $cfg['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$posts = [
[
 'slug' => 'ransomware-readiness-controls-to-test-before-an-incident',
 'title' => 'Ransomware Readiness: Ten Controls to Test Before an Incident',
 'cat' => 'cybersecurity-insights', 'thumb' => 'insight-ransomware-readiness.webp', 'mins' => 7, 'date' => '2026-11-12 10:00:00',
 'tags' => ['risk', 'governance', 'vapt'],
 'excerpt' => 'Most organisations have backups and an incident plan on paper. Ransomware readiness is about proving those controls actually work — before an attacker tests them for you.',
 'content' => '<p>Ransomware remains one of the most disruptive threats an organisation can face, because it attacks two things at once: the availability of your systems and the confidentiality of your data. Most organisations already <em>have</em> backups, antivirus and an incident response plan. The real question is whether those controls will hold up on the worst day of the year.</p>
<p>Readiness is not a document. It is evidence that each control works as intended. Below are ten controls we recommend testing, not just reviewing.</p>
<h2>Prevent the initial foothold</h2>
<ol>
<li><strong>Multi-factor authentication on every remote entry point.</strong> VPN, remote desktop gateways, email and cloud admin consoles should all require a second factor. Test it: attempt a login with a valid password and confirm the second factor is enforced, including for service and break-glass accounts.</li>
<li><strong>Patching of internet-facing systems.</strong> Maintain an inventory of what is exposed to the internet and confirm critical updates are applied within an agreed window. An external vulnerability assessment is a practical way to verify this.</li>
<li><strong>Email and attachment filtering.</strong> Confirm that macro-enabled documents, executable attachments and known-malicious links are blocked or sandboxed before they reach users.</li>
</ol>
<h2>Limit how far an attacker can move</h2>
<ol start="4">
<li><strong>Least privilege for administrators.</strong> Day-to-day accounts should not hold domain or global admin rights. Review who holds privileged access and remove what is not needed.</li>
<li><strong>Network segmentation.</strong> Workstations should not be able to reach backup servers, management interfaces or production databases directly. Test the rules from a standard user network segment.</li>
<li><strong>Endpoint detection and response.</strong> Confirm that every server and workstation has an active agent, that alerts reach someone who will act on them, and that the agent cannot be disabled by a standard user.</li>
</ol>
<h2>Make recovery possible</h2>
<ol start="7">
<li><strong>Offline or immutable backups.</strong> At least one copy of critical data should be unreachable from the production network or protected from deletion. Ransomware operators routinely target backups first.</li>
<li><strong>Restore testing.</strong> A backup you have never restored is an assumption. Restore a representative system end to end and measure how long it actually takes against your recovery time objective.</li>
<li><strong>An incident response plan people have rehearsed.</strong> Run a tabletop exercise with IT, leadership, legal and communications. Decide in advance who can authorise taking systems offline and who speaks to customers and regulators.</li>
<li><strong>Logging you can investigate with.</strong> Confirm that authentication, endpoint and firewall logs are retained centrally for long enough to reconstruct what happened.</li>
</ol>
<h2>Turning the list into evidence</h2>
<p>For each control, record who owns it, how it was tested, the date of the test and the result. Gaps become a short, prioritised remediation plan rather than a vague worry. Repeat the exercise at least annually, and after any significant change to your environment.</p>
<p>If you would like an independent view, a ransomware readiness assessment combines configuration review, authorised testing and a facilitated tabletop exercise to show where your defences and recovery plans stand today.</p>'
],
[
 'slug' => 'preparing-for-an-iso-27001-internal-audit-evidence-checklist',
 'title' => 'Preparing for an ISO 27001 Internal Audit: An Evidence Checklist',
 'cat' => 'audit-compliance-guides', 'thumb' => 'insight-iso27001-internal-audit.webp', 'mins' => 6, 'date' => '2026-11-10 10:00:00',
 'tags' => ['iso-27001', 'compliance', 'governance'],
 'excerpt' => 'An internal audit is the rehearsal for certification. Knowing what evidence auditors expect — and where it lives — turns a stressful week into a routine review.',
 'content' => '<p>ISO/IEC 27001 requires organisations to conduct internal audits of their Information Security Management System (ISMS) at planned intervals. Done well, the internal audit is the most useful rehearsal you will get before a certification or surveillance audit. Done badly, it becomes a scramble for documents the week before.</p>
<p>The difference is preparation. Here is the evidence an internal auditor will typically ask to see, grouped so you can assign owners in advance.</p>
<h2>Context, scope and leadership</h2>
<ul>
<li>The documented ISMS scope, including locations, teams, systems and any exclusions with justification.</li>
<li>The information security policy, approved by top management, and evidence it has been communicated.</li>
<li>Defined roles and responsibilities for information security.</li>
<li>Records of management review meetings, including inputs, decisions and actions.</li>
</ul>
<h2>Risk management</h2>
<ul>
<li>The risk assessment methodology and the current risk register.</li>
<li>The risk treatment plan, with owners, target dates and status.</li>
<li>The Statement of Applicability, showing which Annex A controls apply, why, and how they are implemented.</li>
</ul>
<h2>Controls in operation</h2>
<p>Policies describe intent; auditors look for proof that controls operate. Typical samples include:</p>
<ul>
<li>Joiner, mover and leaver records showing access was granted and removed on time.</li>
<li>Periodic user access reviews for critical systems.</li>
<li>Change records showing approval and testing before deployment.</li>
<li>Vulnerability scan results and evidence of remediation.</li>
<li>Backup logs and at least one restore test.</li>
<li>Security awareness training completion records.</li>
<li>Supplier assessments for vendors that handle your information.</li>
<li>Incident records, including lessons learned.</li>
</ul>
<h2>Performance evaluation and improvement</h2>
<ul>
<li>Security objectives and the metrics used to measure them.</li>
<li>Previous internal audit reports and the status of their findings.</li>
<li>The corrective action log, showing root cause analysis and closure.</li>
</ul>
<h2>Practical tips</h2>
<p><strong>Keep evidence where it is produced.</strong> Rather than copying documents into an audit folder, maintain an index that points to the live record in your ticketing system, HR platform or cloud console.</p>
<p><strong>Ensure auditor independence.</strong> Internal auditors should not audit their own work. Smaller organisations often use an external consultant to provide this independence.</p>
<p><strong>Treat findings as improvement, not failure.</strong> Nonconformities found internally are far better than those found by a certification body. Record them, fix the root cause and keep the evidence.</p>
<p>A structured internal audit, carried out against the standard and your own procedures, gives leadership an honest picture of readiness and gives your team time to close gaps calmly.</p>'
],
[
 'slug' => 'cloud-misconfiguration-common-gaps-found-in-security-reviews',
 'title' => 'Cloud Misconfiguration: Common Gaps Found in Security Reviews',
 'cat' => 'technical-articles', 'thumb' => 'insight-cloud-misconfiguration.webp', 'mins' => 6, 'date' => '2026-11-08 10:00:00',
 'tags' => ['vapt', 'risk', 'compliance'],
 'excerpt' => 'Cloud platforms are secure by design, but they are configured by people. These are the misconfigurations that most often turn a well-built environment into an exposed one.',
 'content' => '<p>Major cloud providers invest heavily in the security <em>of</em> the cloud. Security <em>in</em> the cloud — how you configure identities, storage, networks and logging — remains your responsibility under the shared responsibility model. Most cloud security incidents trace back not to a flaw in the platform but to a setting that was left open.</p>
<p>The following gaps come up repeatedly in cloud configuration reviews, regardless of provider.</p>
<h2>Identity and access</h2>
<ul>
<li><strong>Overly broad permissions.</strong> Roles granted wildcard access "to get things working" and never narrowed.</li>
<li><strong>Long-lived access keys.</strong> Static keys embedded in scripts, CI pipelines or developer laptops, sometimes years old.</li>
<li><strong>Root or owner accounts used day to day</strong>, or without multi-factor authentication.</li>
<li><strong>No regular access review</strong>, leaving former staff and unused service accounts active.</li>
</ul>
<h2>Storage and data</h2>
<ul>
<li>Storage buckets or blobs that allow public read access unintentionally.</li>
<li>Database snapshots or backups shared more widely than intended.</li>
<li>Encryption at rest not enabled, or keys managed without clear ownership.</li>
</ul>
<h2>Network exposure</h2>
<ul>
<li>Security groups allowing administrative ports such as SSH or RDP from anywhere on the internet.</li>
<li>Management interfaces and databases with public IP addresses that do not need them.</li>
<li>Flat networks where development and production can reach each other freely.</li>
</ul>
<h2>Visibility</h2>
<ul>
<li>Audit logging not enabled in every account, region or subscription.</li>
<li>Logs retained for too short a period to support an investigation.</li>
<li>Alerts configured but routed to a mailbox nobody monitors.</li>
</ul>
<h2>How to reduce the risk</h2>
<p><strong>Start with an inventory.</strong> You cannot secure accounts, subscriptions and projects you do not know about.</p>
<p><strong>Measure against a recognised benchmark.</strong> Provider security baselines and independent benchmarks offer a consistent checklist and make progress measurable.</p>
<p><strong>Use guardrails, not just reviews.</strong> Organisation-level policies that prevent public storage or unencrypted resources stop misconfigurations before they happen.</p>
<p><strong>Review after change.</strong> Cloud environments change daily. Pair periodic assessments with automated configuration monitoring.</p>
<p>A cloud security assessment combines configuration review against benchmarks with authorised testing of exposed services, producing a prioritised list of fixes your engineers can act on.</p>'
],
[
 'slug' => 'phishing-simulations-done-right-measuring-awareness-without-blame',
 'title' => 'Phishing Simulations Done Right: Measuring Awareness Without Blame',
 'cat' => 'cybersecurity-insights', 'thumb' => 'insight-phishing-simulation.webp', 'mins' => 5, 'date' => '2026-11-06 10:00:00',
 'tags' => ['leadership', 'risk', 'governance'],
 'excerpt' => 'A phishing simulation should build a reporting culture, not catch people out. Here is how to design campaigns that measure what matters and leave staff more confident.',
 'content' => '<p>Phishing remains one of the most common ways attackers gain a first foothold. Simulated phishing campaigns help organisations understand how staff respond to realistic lures and where further training is needed. But a poorly run campaign can damage trust and discourage the behaviour you actually want: people reporting suspicious messages quickly.</p>
<h2>Set the right objective</h2>
<p>The goal is not to achieve a perfect click rate. Someone, eventually, will click. The goal is a workforce that <strong>recognises</strong> suspicious messages and <strong>reports</strong> them fast enough for the security team to respond. Design and measure campaigns around that outcome.</p>
<h2>Measure more than clicks</h2>
<ul>
<li><strong>Report rate:</strong> the share of recipients who reported the message using the approved button or process.</li>
<li><strong>Time to first report:</strong> how quickly the first person raised the alarm — often the most useful operational metric.</li>
<li><strong>Credential submission:</strong> a more serious outcome than a click alone, and worth tracking separately.</li>
<li><strong>Trends over time</strong> rather than the result of any single campaign.</li>
</ul>
<h2>Design realistic, fair campaigns</h2>
<ul>
<li>Base lures on threats relevant to your organisation and sector, at varying levels of difficulty.</li>
<li>Avoid themes that exploit genuine personal distress, such as bonuses, layoffs or health scares, which erode trust.</li>
<li>Agree scope and rules in advance with HR, legal and leadership, and keep results confidential to the programme.</li>
</ul>
<h2>Respond with learning, not punishment</h2>
<p>Anyone who clicks should land on a short, friendly explanation of the warning signs they missed. Naming and shaming discourages reporting — the opposite of what you need during a real attack. Recognise and thank people who report.</p>
<h2>Close the loop</h2>
<p>Use campaign results to shape awareness training, and share organisation-level trends with leadership. Make the reporting process as easy as one click, and make sure someone triages what is reported.</p>
<p>Run consistently and respectfully, phishing simulations become a measure of security culture rather than a test people fear.</p>'
],
[
 'slug' => 'third-party-risk-how-to-assess-the-vendors-that-handle-your-data',
 'title' => 'Third-Party Risk: How to Assess the Vendors That Handle Your Data',
 'cat' => 'audit-compliance-guides', 'thumb' => 'insight-third-party-risk.webp', 'mins' => 6, 'date' => '2026-11-04 10:00:00',
 'tags' => ['risk', 'compliance', 'governance', 'privacy'],
 'excerpt' => 'Your security is only as strong as the suppliers you trust with your data and systems. A proportionate vendor risk process keeps assessments meaningful without slowing the business down.',
 'content' => '<p>Modern organisations rely on cloud platforms, software providers, payment processors, outsourced IT and many other suppliers. Each one may hold your data or have access to your systems, which means their weaknesses can become your incidents. Frameworks such as ISO/IEC 27001, SOC 2, PCI DSS and data protection laws all expect organisations to manage this third-party risk.</p>
<h2>Start with an inventory and tiering</h2>
<p>List every vendor that stores, processes or can access your information or systems. Then tier them by risk, considering:</p>
<ul>
<li>the sensitivity and volume of data they handle;</li>
<li>the level of access they have to your networks or applications;</li>
<li>how critical the service is to your operations;</li>
<li>any regulatory requirements attached to the data or service.</li>
</ul>
<p>Tiering lets you apply deeper assessment where it matters and a lighter touch elsewhere.</p>
<h2>Assess proportionately</h2>
<ul>
<li><strong>Low risk:</strong> a short questionnaire and confirmation of basic security practices.</li>
<li><strong>Medium risk:</strong> a detailed questionnaire plus supporting evidence such as policies or a recent penetration test summary.</li>
<li><strong>High risk:</strong> independent assurance such as an ISO 27001 certificate with its scope or a SOC 2 Type II report, reviewed by someone who knows what to look for.</li>
</ul>
<p>When reviewing assurance reports, check that the scope covers the service you actually use, note any exceptions, and confirm the complementary controls you are expected to operate yourself.</p>
<h2>Put it in the contract</h2>
<p>Contracts should set out security and confidentiality obligations, breach notification timelines, data location and return or deletion at the end of the relationship, the right to request assurance evidence, and controls over the vendor\'s own subcontractors.</p>
<h2>Monitor, don\'t just onboard</h2>
<p>Risk changes over time. Reassess high-risk vendors at least annually, track remediation of any issues you raised, and respond quickly when a supplier reports an incident. Offboarding matters too: remove access and confirm data has been returned or destroyed.</p>
<h2>Keep it sustainable</h2>
<p>A vendor risk process only works if the business follows it. Keep questionnaires focused, reuse standard assurance where possible and give procurement teams a simple route for engaging security early.</p>
<p>A third-party risk assessment programme gives leadership a clear view of supplier risk and gives auditors the evidence they expect, without turning every purchase into a bottleneck.</p>'
],
];

$cats = $pdo->query('SELECT slug, id FROM post_categories')->fetchAll(PDO::FETCH_KEY_PAIR);
$tags = $pdo->query('SELECT slug, id FROM tags')->fetchAll(PDO::FETCH_KEY_PAIR);
$author = $pdo->query('SELECT id FROM users ORDER BY id LIMIT 1')->fetchColumn() ?: null;
foreach ($posts as $p) {
    $q = $pdo->prepare('SELECT id FROM posts WHERE slug = ?'); $q->execute([$p['slug']]);
    if ($q->fetchColumn()) { echo "exists: {$p['slug']}\n"; continue; }
    $pdo->prepare('INSERT INTO posts (category_id, author_id, slug, title, excerpt, content, thumbnail, reading_minutes, meta_title, meta_description, status, published_at) VALUES (?,?,?,?,?,?,?,?,?,?,\'published\',?)')
        ->execute([$cats[$p['cat']], $author, $p['slug'], $p['title'], $p['excerpt'], $p['content'], $p['thumb'], $p['mins'], $p['title'] . ' | Yojo Solutions', $p['excerpt'], $p['date']]);
    $id = $pdo->lastInsertId();
    foreach ($p['tags'] as $t) if (isset($tags[$t])) $pdo->prepare('INSERT IGNORE INTO post_tag (post_id, tag_id) VALUES (?,?)')->execute([$id, $tags[$t]]);
    echo "+ {$p['title']}\n";
}

// Thumbnails for the four earlier articles (only set when none is chosen yet)
$older = [
    'ISO 27001 vs SOC 2%' => 'insight-iso27001-vs-soc2.webp',
    'DPDPA Readiness%' => 'insight-dpdpa-readiness.webp',
    'Why Broken Object-Level Authorisation%' => 'insight-api-authorisation.webp',
    'Reporting Cyber Risk to the Board%' => 'insight-board-cyber-metrics.webp',
];
foreach ($older as $like => $file) {
    $u = $pdo->prepare("UPDATE posts SET thumbnail = ? WHERE title LIKE ? AND (thumbnail IS NULL OR thumbnail = '')");
    $u->execute([$file, $like]);
    if ($u->rowCount()) echo "+ image for $like\n";
}
