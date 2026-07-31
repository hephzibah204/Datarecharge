<?php
declare(strict_types=1);

/**
 * Environment configuration for the Error Boundary system.
 *
 * Swap 'environment' to 'production' before deploying — this is the single
 * switch that decides whether visitors see a stack trace or a safe message.
 */
return [
    // 'development' shows full stack traces in the browser.
    // 'production' hides them and shows the generic fallback message instead.
    'environment' => 'development',

    // Where uncaught errors get logged, regardless of environment.
    'log_file' => __DIR__ . '/logs/error.log',

    // Shown in the fallback UI's title/header.
    'app_name' => 'DataRecharge',
];
