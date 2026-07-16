<?php
$db = new PDO('sqlite:database/providers.db');

// Check existing providers
echo "=== providers ===\n";
$r = $db->query("SELECT * FROM providers")->fetchAll(PDO::FETCH_ASSOC);
foreach ($r as $row) echo "id={$row['id']}: {$row['name']} ({$row['type']}) - {$row['display_name']}\n";

echo "\n=== apiconfigs (NIN/BVN related) ===\n";
$r = $db->query("SELECT * FROM apiconfigs WHERE name LIKE '%nin%' OR name LIKE '%bvn%' OR name LIKE '%cac%' OR name LIKE '%vin%' OR name LIKE '%passport%' OR name LIKE '%dl%' OR name LIKE '%driver%' OR name LIKE '%aspget%'")->fetchAll(PDO::FETCH_ASSOC);
foreach ($r as $row) echo json_encode($row) . "\n";

echo "\n=== provider_pricing for aspget (provider_id=1) ===\n";
$r = $db->query("SELECT * FROM provider_pricing WHERE provider_id=1")->fetchAll(PDO::FETCH_ASSOC);
foreach ($r as $row) echo "id={$row['id']}: {$row['service_type']} - N{$row['base_fee']} (cost: N{$row['cost_price']})\n";
