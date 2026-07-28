<?php
$p = new PDO('sqlite:database/providers.db');
$p->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$services = [
    ['name' => 'New Enrollment', 'type' => 'nin_enrollment', 'display' => 'New NIN Enrollment'],
    ['name' => 'Child Enrollment', 'type' => 'nin_enrollment', 'display' => 'Child NIN Enrollment'],
    ['name' => 'Validation Bulk', 'type' => 'nin_verification', 'display' => 'Bulk NIN Validation'],
    ['name' => 'IPE Bulk', 'type' => 'nin_verification', 'display' => 'Bulk IPE Clearance'],
    ['name' => 'BVN Generated NIN', 'type' => 'nin_service', 'display' => 'BVN Generated NIN'],
    ['name' => 'Suspended NIN', 'type' => 'nin_service', 'display' => 'Suspended NIN Retrieval'],
    ['name' => 'Gender Modification', 'type' => 'nin_modification', 'display' => 'Gender Modification'],
    ['name' => 'DOB > 10 Years', 'type' => 'nin_modification', 'display' => 'DOB Modification (>10yrs)'],
    ['name' => 'Delinking', 'type' => 'nin_service', 'display' => 'NIN Delinking'],
    ['name' => 'Email Retrieved', 'type' => 'nin_service', 'display' => 'NIN Email Retrieval'],
    ['name' => 'BVN Retrieve', 'type' => 'bvn_service', 'display' => 'BVN Retrieval']
];

$stmt = $p->prepare("INSERT INTO providers (name, type, display_name, description, is_active) VALUES (?, ?, ?, ?, 1)");

foreach ($services as $s) {
    $stmt->execute([$s['name'], $s['type'], $s['display'], $s['display']]);
    echo "Added: " . $s['name'] . "\n";
}
