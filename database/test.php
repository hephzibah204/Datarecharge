<?php
$pdo = new PDO('sqlite:database/providers.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== PROVIDERS ===\n";
$r = $pdo->query('SELECT id, name, type, display_name, is_active FROM providers ORDER BY priority DESC');
foreach($r as $row){
    printf("  %d. %s (%s) - %s [%s]\n", $row['id'], $row['name'], $row['type'],
           $row['display_name'] ?? '-', $row['is_active'] ? 'ACTIVE' : 'INACTIVE');
}

echo "\n=== PRICING COUNT ===\n";
$r = $pdo->query('SELECT COUNT(*) AS cnt FROM provider_pricing');
echo '  Total pricing entries: ' . $r->fetch()['cnt'] . "\n";

echo "\n=== OVERRIDES ===\n";
$r = $pdo->query('SELECT COUNT(*) AS cnt FROM price_overrides');
echo '  Total overrides: ' . $r->fetch()['cnt'] . "\n";

echo "\n=== SAMPLE: user_pricing VIEW ===\n";
$r = $pdo->query('SELECT name, service_type, base_fee, user_fee, agent_fee, vendor_fee FROM user_pricing LIMIT 5');
foreach($r as $row){
    printf("  %s - %s: base=%.2f user=%.2f agent=%.2f vendor=%.2f\n",
           $row['name'], $row['service_type'], $row['base_fee'],
           $row['user_fee'], $row['agent_fee'], $row['vendor_fee']);
}
