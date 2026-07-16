<?php
$db = new PDO('sqlite:database/providers.db');
$r = $db->query('PRAGMA table_info(apiconfigs)')->fetchAll(PDO::FETCH_ASSOC);
echo "apiconfigs columns:\n";
foreach ($r as $row) echo json_encode($row) . "\n";

// Try direct insert
$db->exec("INSERT OR IGNORE INTO apiconfigs (name, value) VALUES ('cacApi', 'test_key')");
$db->exec("INSERT OR IGNORE INTO apiconfigs (name, value) VALUES ('cacProvider', 'https://api.aspget.com/cac/')");
$db->exec("INSERT OR IGNORE INTO apiconfigs (name, value) VALUES ('cacStatus', 'On')");

echo "\nAfter direct insert:\n";
$r = $db->query("SELECT * FROM apiconfigs WHERE name LIKE '%cac%'")->fetchAll(PDO::FETCH_ASSOC);
foreach ($r as $row) echo json_encode($row) . "\n";
