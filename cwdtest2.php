<?php
echo "CWD: " . getcwd() . "\n";
echo "DIR: " . __DIR__ . "\n";
echo "test1: " . (file_exists('../../core/Models/AdminModel.php') ? 'YES' : 'NO') . "\n";
echo "test2: " . (file_exists('../../../core/Models/AdminModel.php') ? 'YES' : 'NO') . "\n";
echo "test3: " . (file_exists('C:/Users/hephz/Documents/_landingpageassets/core/Models/AdminModel.php') ? 'YES' : 'NO') . "\n";
echo "test4: " . (file_exists(__DIR__ . '/../../core/Models/AdminModel.php') ? 'YES' : 'NO') . "\n";
