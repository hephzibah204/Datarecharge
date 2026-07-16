<?php
header('Content-Type: text/plain');
echo "CWD: " . getcwd() . "\n";
echo "DIR: " . __DIR__ . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";

// Test both path variants
echo "\n--- Path tests ---\n";
echo "Testing ../../core/helpers/vendor/autoload.php: ";
echo file_exists('../../core/helpers/vendor/autoload.php') ? 'EXISTS' : 'NOT FOUND';
echo "\n";

echo "Testing ../../../core/helpers/vendor/autoload.php: ";
echo file_exists('../../../core/helpers/vendor/autoload.php') ? 'EXISTS' : 'NOT FOUND';
echo "\n";
