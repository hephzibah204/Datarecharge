<?php

if (function_exists('config')) return;

define('ENV_LOADED', true);

// Load .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $eqPos = strpos($line, '=');
        if ($eqPos === false) continue;
        $key = substr($line, 0, $eqPos);
        $value = substr($line, $eqPos + 1);
        $key = trim($key);
        $value = trim($value);
        if (getenv($key) === false || getenv($key) === '') {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

/**
 * Get a config value from env, DB, or default.
 * Priority: env > $appConfig (DB) > $default
 */
if (!function_exists('config')) {
    function config($name, $default = null) {
        $val = getenv($name);
        if ($val !== false && $val !== '') return $val;
        global $appConfig;
        return $appConfig[$name] ?? $default;
    }
}
