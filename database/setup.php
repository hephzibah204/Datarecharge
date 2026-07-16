<?php
/**
 * Provider Database Setup Script
 *
 * Usage:
 *   php database/setup.php           # uses .env settings (defaults to SQLite)
 *   php database/setup.php mysql     # force MySQL mode
 *   php database/setup.php sqlite    # force SQLite mode
 */

$forceDriver = $argv[1] ?? null;

// Load .env
$envFile = __DIR__ . '/../.env';
if(file_exists($envFile)){
    echo "[INFO] Loading .env file...\n";
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($lines as $line){
        $line = trim($line);
        if($line === '' || str_starts_with($line, '#')) continue;
        if(str_contains($line, '=')){
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

$driver = $forceDriver ?? getenv('DB_DRIVER') ?: 'sqlite';

if($driver === 'sqlite'){
    setupSQLite();
} else {
    setupMySQL();
}

function setupSQLite(){
    $dbPath = __DIR__ . '/../' . (getenv('DB_NAME') ?: 'database/providers.db');
    $dir = dirname($dbPath);
    if(!is_dir($dir)){ mkdir($dir, 0777, true); }

    echo "[SQLITE] Database: $dbPath\n";
    echo "[SQLITE] Creating schema...\n";

    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA foreign_keys = ON");

    $schema = file_get_contents(__DIR__ . '/schema.sqlite.sql');
    $statements = explode(';', $schema);

    $count = 0;
    foreach($statements as $stmt){
        $stmt = trim($stmt);
        if(empty($stmt)) continue;
        try {
            $pdo->exec($stmt);
            $count++;
        } catch(Exception $e){
            echo "[WARN] Statement failed: " . $e->getMessage() . "\n";
        }
    }

    echo "[OK] $count statements executed.\n";
    echo "[OK] Database ready at: $dbPath\n";
}

function setupMySQL(){
    $host = getenv('DB_HOST') ?: 'localhost';
    $dbName = getenv('DB_NAME') ?: 'xtfphfml_data';
    $username = getenv('DB_USERNAME') ?: 'xtfphfml_data';
    $password = getenv('DB_PASSWORD') ?: 'Anuoluwapo@';

    echo "[MYSQL] Host: $host, Database: $dbName\n";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(Exception $e){
        echo "[ERROR] Cannot connect to MySQL: " . $e->getMessage() . "\n";
        echo "[HINT] Check your .env file or run with 'sqlite' for local dev.\n";
        exit(1);
    }

    $schema = file_get_contents(__DIR__ . '/provider_pricing_system.sql');
    $statements = explode(';', $schema);

    $count = 0;
    foreach($statements as $stmt){
        $stmt = trim($stmt);
        if(empty($stmt)) continue;
        try {
            $pdo->exec($stmt);
            $count++;
        } catch(Exception $e){
            echo "[WARN] Statement failed: " . $e->getMessage() . "\n";
        }
    }

    echo "[OK] $count statements executed.\n";
}

echo "\n[DONE] Provider system setup complete.\n";
