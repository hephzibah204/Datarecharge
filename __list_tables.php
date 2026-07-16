<?php
try {
    $db = new PDO("sqlite:database/providers.db");
    $q = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    $tables = [];
    foreach ($q as $r) { $tables[] = $r["name"]; }
    echo "Tables (" . count($tables) . "): " . implode(", ", $tables) . "\n";

    $q2 = $db->query("SELECT name FROM sqlite_master WHERE type='view' ORDER BY name");
    $views = [];
    foreach ($q2 as $r) { $views[] = $r["name"]; }
    echo "Views (" . count($views) . "): " . implode(", ", $views) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
