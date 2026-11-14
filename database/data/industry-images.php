<?php
/* Sets an illustration for each industry (files in storage/uploads); only fills empty images.
   Run: php database/data/industry-images.php */
$cfg = require __DIR__ . '/../../config/database.php';
$pdo = new PDO("mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['database']};charset=utf8mb4", $cfg['username'], $cfg['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$map = ['banking-financial-services' => 'industry-banking.webp', 'healthcare' => 'industry-healthcare.webp', 'it-software' => 'industry-it-software.webp',
    'saas-startups' => 'industry-saas.webp', 'education' => 'industry-education.webp', 'manufacturing' => 'industry-manufacturing.webp',
    'e-commerce' => 'industry-ecommerce.webp', 'professional-services' => 'industry-professional-services.webp'];
$u = $pdo->prepare("UPDATE industries SET image = ? WHERE slug = ? AND (image IS NULL OR image = '')");
foreach ($map as $slug => $file) { $u->execute([$file, $slug]); echo ($u->rowCount() ? '+ ' : '= ') . "$slug\n"; }
