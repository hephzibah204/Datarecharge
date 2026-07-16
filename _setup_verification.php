<?php
$db = new PDO('sqlite:database/providers.db');

// 1. Add aspget API configs for new services
$configs = [
    ['cacApi', 'lv_aspget_gadrkmobcew897u1hp1n684s5z61v3q7'],
    ['cacProvider', 'https://api.aspget.com/cac/'],
    ['cacStatus', 'On'],
    ['vinApi', 'lv_aspget_gadrkmobcew897u1hp1n684s5z61v3q7'],
    ['vinProvider', 'https://api.aspget.com/vin/'],
    ['vinStatus', 'On'],
    ['dlApi', 'lv_aspget_gadrkmobcew897u1hp1n684s5z61v3q7'],
    ['dlProvider', 'https://api.aspget.com/drivers-license/'],
    ['dlStatus', 'On'],
    ['passportApi', 'lv_aspget_gadrkmobcew897u1hp1n684s5z61v3q7'],
    ['passportProvider', 'https://api.aspget.com/passport/'],
    ['passportStatus', 'On'],
];

$insert = $db->prepare("INSERT OR IGNORE INTO apiconfigs (name, value) VALUES (:name, :value)");
foreach ($configs as $c) {
    $insert->execute([':name' => $c[0], ':value' => $c[1]]);
    echo "Config: {$c[0]} = {$c[1]}\n";
}

// 2. Add provider_pricing entries
$pricing = [
    [1, 'verify_cac', 500, 300, 1.5, 50, 10, 1, 0, 0, 2],
    [1, 'verify_vin', 300, 200, 1.5, 30, 10, 1, 0, 0, 1],
    [1, 'verify_dl', 500, 350, 1.5, 50, 10, 1, 0, 0, 2],
    [1, 'verify_passport', 500, 350, 1.5, 50, 10, 1, 0, 0, 2],
];

foreach ($pricing as $p) {
    $exists = $db->prepare("SELECT COUNT(*) FROM provider_pricing WHERE provider_id=? AND service_type=?");
    $exists->execute([$p[0], $p[1]]);
    if ($exists->fetchColumn() == 0) {
        $db->prepare("INSERT INTO provider_pricing (provider_id, service_type, base_fee, cost_price, urgency_multiplier, priority_fee, max_discount, is_urgent_available, is_express_available, min_processing_hours, max_processing_hours, is_active, effective_start, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,1,'1970-01-01',datetime('now'),datetime('now'))")->execute($p);
        echo "Added pricing: {$p[1]}\n";
    } else {
        echo "Pricing exists: {$p[1]}\n";
    }
}

echo "\nDone. Verify:\n";
$r = $db->query("SELECT * FROM apiconfigs WHERE name LIKE '%cac%' OR name LIKE '%vin%' OR name LIKE '%dl%' OR name LIKE '%passport%' OR name LIKE '%driver%'")->fetchAll(PDO::FETCH_ASSOC);
echo "New configs:\n";
foreach ($r as $row) echo "  {$row['name']} = {$row['value']}\n";
$r = $db->query("SELECT * FROM provider_pricing WHERE service_type IN ('verify_cac','verify_vin','verify_dl','verify_passport')")->fetchAll(PDO::FETCH_ASSOC);
echo "New pricing:\n";
foreach ($r as $row) echo "  {$row['service_type']}: N{$row['base_fee']} (cost: N{$row['cost_price']})\n";
