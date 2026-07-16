<?php
$db = new PDO('sqlite:database/providers.db');

// Get next aId
$maxId = $db->query("SELECT MAX(aId) FROM apiconfigs")->fetchColumn();
$nextId = (int)$maxId + 1;

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

$insert = $db->prepare("INSERT OR IGNORE INTO apiconfigs (aId, name, value) VALUES (:id, :name, :value)");
foreach ($configs as $c) {
    $existing = $db->prepare("SELECT COUNT(*) FROM apiconfigs WHERE name = ?");
    $existing->execute([$c[0]]);
    if ($existing->fetchColumn() == 0) {
        $insert->execute([':id' => $nextId, ':name' => $c[0], ':value' => $c[1]]);
        echo "Added: {$c[0]}\n";
        $nextId++;
    } else {
        echo "Exists: {$c[0]}\n";
    }
}

// Verify
echo "\nNew verification configs:\n";
$names = implode("','", array_map(fn($c) => $c[0], $configs));
$r = $db->query("SELECT * FROM apiconfigs WHERE name IN ('$names')")->fetchAll(PDO::FETCH_ASSOC);
foreach ($r as $row) echo "  {$row['name']} = {$row['value']}\n";
