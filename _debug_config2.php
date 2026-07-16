<?php
$db = new PDO('sqlite:database/providers.db');
$r = $db->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='apiconfigs'")->fetch();
echo "CREATE TABLE SQL:\n" . $r['sql'] . "\n\n";

// Try without IGNORE
$db->exec("INSERT INTO apiconfigs (name, value) VALUES ('cacApi', 'test_key2')");
echo "Inserted\n";
$r = $db->query("SELECT * FROM apiconfigs WHERE name='cacApi'")->fetchAll(PDO::FETCH_ASSOC);
foreach ($r as $row) echo json_encode($row) . "\n";
