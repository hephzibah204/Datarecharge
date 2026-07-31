<?php
declare(strict_types=1);

/**
 * Demo entry point.
 *
 * Registers the boundary, prints a bit of real markup first (so you can
 * see it get wiped away), then triggers one of three failure modes based
 * on ?mode=:
 *
 *   ?mode=exception  - an uncaught exception, thrown manually
 *   ?mode=warning    - a classic PHP warning, escalated via handleError()
 *   ?mode=fatal      - a true fatal error, caught via the shutdown handler
 *
 * Try: index.php?mode=exception, index.php?mode=warning, index.php?mode=fatal
 * Toggle 'environment' in config.php between 'development' and 'production'
 * to see the different fallback UI states.
 */

require __DIR__ . '/ErrorBoundary.php';

$config = require __DIR__ . '/config.php';
ErrorBoundary::register($config);

// This proves the boundary discards partial output: if you view source
// after a crash below, this paragraph will NOT be present — the buffer
// holding it was wiped before the fallback UI was rendered.
echo '<p>Rendering started — if you see this text after the page loads, the crash below did not happen.</p>';

$mode = $_GET['mode'] ?? 'fatal';

switch ($mode) {
    case 'exception':
        throw new RuntimeException('Demo: an uncaught exception was thrown manually.');

    case 'warning':
        /** @noinspection PhpUndefinedVariableInspection */
        echo $thisVariableWasNeverDefined; // triggers E_WARNING -> ErrorException

        break;

    case 'fatal':
    default:
        nonExistentFunction(); // triggers a true fatal error, caught by the shutdown handler
}

// Unreachable whenever a failure above was triggered.
echo '<p>Finished without errors.</p>';
