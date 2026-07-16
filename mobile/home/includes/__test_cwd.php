<?php
echo "CWD: " . getcwd() . "\n";
echo "DIR: " . __DIR__ . "\n";
echo "Trying require '../../core/helpers/vendor/autoload.php':\n";
$r = @require_once "../../core/helpers/vendor/autoload.php";
if (class_exists('ComposerAutoloaderInit438c7742ed76532b6fbced995d044d2a')) {
    echo "SUCCESS: autoloader loaded\n";
} else {
    echo "FAILED: autoloader not loaded\n";
}
