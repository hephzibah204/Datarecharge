<?php
/**
 * MySQL-to-SQLite import script.
 * Reads MySQL dump file line-by-line, skips MySQL-specific constructs,
 * converts CREATE TABLE to SQLite, and executes.
 */

$dbPath = __DIR__ . '/providers.db';
$sqlFile = 'C:/Users/hephz/Downloads/xtfphfml_data (1).sql';

if (!file_exists($sqlFile)) die("[ERROR] MySQL dump not found: $sqlFile\n");

// Delete existing DB
if (file_exists($dbPath)) unlink($dbPath);

$pdo = new PDO("sqlite:$dbPath");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("PRAGMA journal_mode=WAL");
$pdo->exec("PRAGMA foreign_keys=OFF");

// ------------------------------------------------------------------
// PHASE 1: Provider & Pricing system from schema.sqlite.sql
// ------------------------------------------------------------------
echo "Phase 1: Setting up Provider & Pricing system...\n";
$schema = file_get_contents(__DIR__ . '/schema.sqlite.sql');
if ($schema === false) die("[ERROR] Could not read schema.sqlite.sql\n");

try { $pdo->exec($schema); } catch (Exception $e) {
    echo "  [WARN] Schema: " . $e->getMessage() . "\n";
}
$pdo->exec("PRAGMA foreign_keys=OFF");

// Get list of tables already created by Phase 1
$existingTables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
echo "  Existing tables: " . implode(', ', $existingTables) . "\n";

// ------------------------------------------------------------------
// PHASE 2: Read MySQL dump line-by-line and extract SQLite-compatible statements
// ------------------------------------------------------------------
echo "Phase 2: Parsing MySQL dump...\n";

$lines = file($sqlFile, FILE_IGNORE_NEW_LINES);
if ($lines === false) die("[ERROR] Could not read MySQL dump\n");

$primaryKeys = [];
$autoIncrements = [];
$currentAlterTable = null;
foreach ($lines as $line) {
    $trimmed = trim($line);
    if (preg_match('/ALTER\s+TABLE\s+`?(\w+)`?/i', $trimmed, $m)) {
        $currentAlterTable = $m[1];
    }
    if ($currentAlterTable) {
        if (preg_match('/ADD\s+PRIMARY\s+KEY\s*\(\s*`?(\w+)`?\s*\)/i', $trimmed, $m)) {
            $primaryKeys[$currentAlterTable] = $m[1];
        }
        if (preg_match('/MODIFY\s+`?(\w+)`?[^,]*AUTO_INCREMENT/i', $trimmed, $m)) {
            $autoIncrements[$currentAlterTable] = $m[1];
        }
        if (str_ends_with($trimmed, ';')) {
            $currentAlterTable = null;
        }
    }
}

$extracted = [];
$buffer = '';
$skipBlock = false;  // inside a trigger/procedure/view we're skipping
$skipBlockNest = 0;  // nesting depth for BEGIN...END blocks

foreach ($lines as $line) {
    $trimmed = trim($line);
    $upper = strtoupper($trimmed);

    // Skip empty lines, comments, MySQL directives
    if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*')) continue;
    $upperNoSemi = rtrim($upper, ';');
    if (str_starts_with($upper, 'SET ') || str_starts_with($upperNoSemi, 'SET ')) continue;
    if (str_starts_with($upper, 'DELIMITER')) continue;
    if ($upperNoSemi === 'START TRANSACTION' || $upperNoSemi === 'COMMIT' || $upperNoSemi === 'ROLLBACK') continue;

    // Detect start of MySQL definitions to skip entirely
    if (str_starts_with($upper, 'CREATE TRIGGER') ||
        str_starts_with($upper, 'CREATE DEFINER') ||
        str_starts_with($upper, 'CREATE ALGORITHM') ||
        str_starts_with($upper, 'CREATE VIEW') ||
        str_starts_with($upper, 'CREATE PROCEDURE') ||
        str_starts_with($upper, 'CREATE FUNCTION')) {
        $skipBlock = true;
        $skipBlockNest = 1;
        continue;
    }

    // Inside trigger/procedure - track BEGIN/END nesting
    if ($skipBlock) {
        if (str_starts_with($upper, 'BEGIN')) $skipBlockNest++;
        if ($trimmed === 'END IF' || $trimmed === 'END') {
            $skipBlockNest--;
            if ($skipBlockNest <= 0) {
                $skipBlock = false;
                $skipBlockNest = 0;
            }
        }
        if ($trimmed === '$$') $skipBlock = false;
        continue;
    }

    // Skip ALTER TABLE, ADD, MODIFY, DROP TABLE statements
    if (str_starts_with($upper, 'ALTER TABLE')) continue;
    if (str_starts_with($upper, 'ADD ')) continue;
    if (str_starts_with($upper, 'MODIFY ')) continue;
    if (str_starts_with($upper, 'DROP TABLE')) continue;

    // Skip stand-alone $$ lines
    if ($trimmed === '$$') continue;

    // Accumulate line
    $buffer .= $line . "\n";

    // End of statement: line ends with semicolon
    if (str_ends_with($trimmed, ';')) {
        $stmt = trim(substr($buffer, 0, -1)); // strip trailing semicolon
        $buffer = '';
        if (!empty($stmt)) {
            $extracted[] = $stmt;
        }
    }
}

echo "  Extracted " . count($extracted) . " raw statements\n";

// ------------------------------------------------------------------
// PHASE 3: Convert and execute
// ------------------------------------------------------------------
echo "Phase 3: Converting MySQL syntax to SQLite...\n";

if ($pdo->inTransaction()) {
    $pdo->rollBack();
}
$pdo->beginTransaction();
$executed = 0;
$skipped = 0;
$errors = 0;

foreach ($extracted as $raw) {
    $upper = strtoupper($raw);
    $sql = $raw . ';';

    // Detect CREATE TABLE
    if (str_starts_with($upper, 'CREATE TABLE')) {
        // Extract table name
        if (preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?/i', $sql, $m)) {
            $tbl = $m[1];
            if (in_array($tbl, $existingTables)) {
                $skipped++;
                echo "  [SKIP] Table '$tbl' already exists from Phase 1\n";
                continue;
            }
        }
        $sql = convertCreateTable($sql, $tbl, $primaryKeys, $autoIncrements);
    }

    // Detect INSERT
    if (str_starts_with($upper, 'INSERT INTO')) {
        $sql = convertInsert($sql);
    }

    try {
        $pdo->exec($sql);
        $executed++;
        if ($executed % 5 === 0) echo "  Executed $executed...\n";
    } catch (Exception $e) {
        $errors++;
        $preview = mb_substr(strtok($sql, "\n"), 0, 120);
        echo "  [WARN] $preview ... " . $e->getMessage() . "\n";
    }
}

$pdo->commit();

// ------------------------------------------------------------------
// PHASE 4: NIN API Configuration seed data
// ------------------------------------------------------------------
$ninConfigs = [
    ['name' => 'ninApi',      'value' => 'lv_aspget_gadrkmobcew897u1hp1n684s5z61v3q7'],
    ['name' => 'ninProvider', 'value' => 'https://api.aspget.com/nin/'],
    ['name' => 'ninStatus',   'value' => 'On'],
];
$maxId = (int)$pdo->query('SELECT MAX(aId) FROM apiconfigs')->fetchColumn();
$aId = $maxId + 1;
$ninStmt = $pdo->prepare("INSERT OR IGNORE INTO apiconfigs (aId, name, value, updateOn) VALUES (:id, :name, :value, datetime('now'))");
foreach ($ninConfigs as $cfg) {
    $ninStmt->execute([':id' => $aId++, ':name' => $cfg['name'], ':value' => $cfg['value']]);
}
echo "  NIN API config seeded: " . count($ninConfigs) . " rows\n";

// ------------------------------------------------------------------
// PHASE 5: Add sitesettings fee columns if not exist
// ------------------------------------------------------------------
$feeCols = [
    'fee_name_mod' => 5000,
    'fee_phone_mod' => 5000,
    'fee_address_mod' => 4000,
    'fee_email_mod' => 4000,
    'fee_dob_mod' => 28574,
    'fee_lga_mod' => 3000,
    'fee_gender_mod' => 8000,
    'fee_marital_mod' => 6000,
    'fee_nin_verification' => 1000,
    'fee_affidavit' => 5000,
    'fee_birth_certificate' => 10000
];
foreach ($feeCols as $col => $default) {
    try {
        $pdo->exec("ALTER TABLE sitesettings ADD COLUMN `$col` REAL DEFAULT $default");
    } catch (Exception $e) {
        // Column probably already exists or table is missing
    }
}
echo "  Site settings fee columns checked/added.\n";

// ------------------------------------------------------------------
// SUMMARY
// ------------------------------------------------------------------
echo "\n[DONE] Import completed.\n";
echo "  Raw statements extracted: " . count($extracted) . "\n";
echo "  Executed: $executed\n";
echo "  Skipped (already exist): $skipped\n";
echo "  Errors: $errors\n";
echo "  Database: $dbPath (" . number_format(filesize($dbPath)) . " bytes)\n";

$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
echo "  Tables: " . count($tables) . "\n";
foreach ($tables as $t) {
    $cnt = $pdo->query("SELECT COUNT(*) FROM \"$t\"")->fetchColumn();
    if ($cnt > 0) echo "    $t: $cnt rows\n";
}

// ------------------------------------------------------------------
// HELPERS
// ------------------------------------------------------------------
function convertCreateTable($sql, $tbl = null, $primaryKeys = [], $autoIncrements = []) {
    // Remove ENGINE=InnoDB and everything after on the same line
    $sql = preg_replace('/\s+ENGINE\s*=\s*\w+.*$/i', '', $sql);
    // Remove DEFAULT CHARSET
    $sql = preg_replace('/\s+DEFAULT\s+CHARSET\s*=\s*\w+/i', '', $sql);
    // Remove inline CHARACTER SET ... optional COLLATE ...
    $sql = preg_replace('/\s+CHARACTER\s+SET\s+\w+(?:\s+COLLATE\s+\w+)?/i', '', $sql);
    // Remove inline COLLATE (stand-alone)
    $sql = preg_replace('/\s+COLLATE\s+\w+/i', '', $sql);
    // Remove COLLATE = ...
    $sql = preg_replace('/\s+COLLATE\s*=\s*\w+/i', '', $sql);
    
    // 1. Rewrite the pre-parsed primary key for this table to INTEGER PRIMARY KEY AUTOINCREMENT
    if ($tbl && isset($primaryKeys[$tbl])) {
        $pkCol = $primaryKeys[$tbl];
        $sql = preg_replace('/`' . $pkCol . '`\s+(?:tinyint|smallint|mediumint|bigint|int)(?:\([^)]*\))?(\s+NOT\s+NULL)?(\s+DEFAULT\s+\'[^\']*\')?/i', '`' . $pkCol . '` INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
        $sql = preg_replace('/,\s*PRIMARY\s+KEY\s*\(\s*`?' . $pkCol . '`?\s*\)/i', '', $sql);
    }
    
    // 2. Fallback: Parse inline primary key constraints if defined inside the CREATE TABLE statement itself
    if (preg_match('/PRIMARY\s+KEY\s*\(\s*`?(\w+)`?\s*\)/i', $sql, $pkMatches)) {
        $pkCol = $pkMatches[1];
        $sql = preg_replace('/`' . $pkCol . '`\s+(?:tinyint|smallint|mediumint|bigint|int)(?:\([^)]*\))?(\s+NOT\s+NULL)?(\s+AUTO_INCREMENT)?/i', '`' . $pkCol . '` INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
        $sql = preg_replace('/,\s*PRIMARY\s+KEY\s*\(\s*`?' . $pkCol . '`?\s*\)/i', '', $sql);
    }
    // Remove AUTO_INCREMENT
    $sql = preg_replace('/\s+AUTO_INCREMENT/i', '', $sql);
    // Remove unsigned
    $sql = preg_replace('/\s+unsigned/i', '', $sql);
    // Remove ON UPDATE CURRENT_TIMESTAMP (including with parentheses)
    $sql = preg_replace('/\s+ON\s+UPDATE\s+CURRENT_TIMESTAMP\s*(\(\))?/i', '', $sql);
    // Remove DEFAULT CURRENT_TIMESTAMP (for datetime/timestamp -> TEXT columns)
    $sql = preg_replace('/DEFAULT\s+CURRENT_TIMESTAMP\s*(\(\))?/i', '', $sql);

    // Convert MySQL types to SQLite types (order matters: longer first)
    $sql = preg_replace('/\btinyint\s*\([^)]*\)/i', 'INTEGER', $sql);
    $sql = preg_replace('/\bsmallint\s*\([^)]*\)/i', 'INTEGER', $sql);
    $sql = preg_replace('/\bmediumint\s*\([^)]*\)/i', 'INTEGER', $sql);
    $sql = preg_replace('/\bbigint\s*\([^)]*\)/i', 'INTEGER', $sql);
    $sql = preg_replace('/\bint\s*\([^)]*\)/i', 'INTEGER', $sql);
    $sql = preg_replace('/\bfloat\s*(\([^)]*\))?/i', 'REAL', $sql);
    $sql = preg_replace('/\bdouble\s*(\([^)]*\))?/i', 'REAL', $sql);
    $sql = preg_replace('/\bdecimal\s*\([^)]*\)/i', 'REAL', $sql);
    $sql = preg_replace('/\b(varchar|nvarchar)\s*\([^)]*\)/i', 'TEXT', $sql);
    $sql = preg_replace('/\bchar\s*\([^)]*\)/i', 'TEXT', $sql);
    $sql = preg_replace('/\blongtext\b/i', 'TEXT', $sql);
    $sql = preg_replace('/\bmediumtext\b/i', 'TEXT', $sql);
    $sql = preg_replace('/\btinytext\b/i', 'TEXT', $sql);
    $sql = preg_replace('/\btext\b/i', 'TEXT', $sql);
    $sql = preg_replace('/\bdatetime\b/i', 'TEXT', $sql);
    $sql = preg_replace('/\btimestamp\b/i', 'TEXT', $sql);
    // Guard simple type keywords from matching column names (e.g., `date` column)
    $sql = preg_replace('/(?<!\x60)\bdate\b(?!\x60)/i', 'TEXT', $sql);
    $sql = preg_replace('/(?<!\x60)\btime\b(?!\x60)/i', 'TEXT', $sql);
    $sql = preg_replace('/(?<!\x60)\byear\b(?!\x60)/i', 'INTEGER', $sql);
    $sql = preg_replace('/\benum\s*\([^)]*\)/i', 'TEXT', $sql);
    $sql = preg_replace('/\bset\s*\([^)]*\)/i', 'TEXT', $sql);
    $sql = preg_replace('/\bjson\b/i', 'TEXT', $sql);

    // Remove standalone KEY/INDEX/UNIQUE/FULLTKEY lines
    $sql = preg_replace('/,\s*\n\s*(UNIQUE\s+)?(KEY|INDEX)\s+`[^`]+`\s*\([^)]+\)/i', '', $sql);
    $sql = preg_replace('/,\s*\n\s*(UNIQUE\s+)?(KEY|INDEX)\s+\w+\s*\([^)]+\)/i', '', $sql);
    $sql = preg_replace('/,\s*\n\s*FULLTEXT\s+(KEY|INDEX)\s+[^)]+\)/i', '', $sql);

    // Clean up trailing comma before closing paren
    $sql = preg_replace('/,\s*\n\s*\)/', "\n)", $sql);
    $sql = preg_replace('/,\s*\)/', ')', $sql);

    // Collapse whitespace
    $sql = preg_replace('/\s{2,}/', ' ', $sql);

    return $sql;
}

function convertInsert($sql) {
    // Skip conversion for statements with no backslash escapes (avoids issues)
    if (!str_contains($sql, '\\')) return $sql;
    // Convert hex literals 0x... -> X'...'
    // Note: this runs BEFORE the backslash unescape to avoid false matches
    $sql = preg_replace('/\b0x([0-9A-Fa-f]+)\b/', "X'$1'", $sql);
    // MySQL uses \' for escaped single quotes; SQLite uses '' (doubled quote)
    $sql = str_replace("\\'", "''", $sql);
    // Unescape other MySQL backslash sequences: \", \\, \/, \n, \r
    $sql = str_replace(['\\"', '\\\\', '\\/', "\\n", "\\r"], ['"', '\\', '/', "\n", "\r"], $sql);
    return $sql;
}
