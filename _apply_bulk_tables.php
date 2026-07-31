<?php
try {
    $p = new PDO('sqlite:database/providers.db');
    $p->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = file_get_contents('database/bulk_validation_tables.sql');
    $p->exec($sql);
    echo 'Tables created successfully';
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage();
}