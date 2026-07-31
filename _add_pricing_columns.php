<?php
try {
    $p = new PDO('sqlite:database/providers.db');
    $p->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $columns = [
        'fee_bulk_validation' => 500,
        'fee_ipe_clearance' => 1000,
        'fee_bulk_nin_modification' => 5000,
        'fee_bulk_dob_modification' => 5000,
        'fee_bulk_phone_modification' => 5000,
        'fee_bulk_address_modification' => 5000,
    ];
    
    foreach ($columns as $col => $default) {
        try {
            $p->exec("ALTER TABLE sitesettings ADD COLUMN $col REAL DEFAULT $default");
            echo "Added $col\n";
        } catch (Exception $e) {
            echo "Column $col might already exist: " . $e->getMessage() . "\n";
        }
    }
    echo "Done\n";
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage();
}