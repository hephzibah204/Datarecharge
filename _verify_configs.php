<?php
$db = new PDO('sqlite:database/providers.db');
$r = $db->query("SELECT * FROM apiconfigs WHERE name IN ('cacApi','cacProvider','cacStatus','vinApi','vinProvider','vinStatus','dlApi','dlProvider','dlStatus','passportApi','passportProvider','passportStatus')")->fetchAll(PDO::FETCH_ASSOC);
echo "New configs:\n";
foreach ($r as $row) echo "  {$row['name']} = {$row['value']}\n";
