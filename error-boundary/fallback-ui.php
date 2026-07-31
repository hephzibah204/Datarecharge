<?php
declare(strict_types=1);

/**
 * Fallback UI template.
 *
 * Rendered exclusively by ErrorBoundary::renderFallbackUi() after the
 * output buffer has been wiped clean, so this is guaranteed to be the
 * only markup on the page — never appended to a broken partial layout.
 *
 * Expected in scope:
 * @var bool      $isDevelopment
 * @var string    $appName
 * @var Throwable $exception
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Something went wrong — <?= htmlspecialchars($appName, ENT_QUOTES) ?></title>
<style>
    :root { color-scheme: light dark; }
    body {
        margin: 0;
        padding: 2.5rem 1.5rem;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background: #f5f6f8;
        color: #1f2328;
    }
    .boundary-card {
        max-width: 720px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #e2e4e8;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .boundary-header {
        padding: 1.5rem 2rem;
        background: #b91c1c;
        color: #fff;
    }
    .boundary-header h1 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
    }
    .boundary-body {
        padding: 1.5rem 2rem 2rem;
    }
    .boundary-body p {
        line-height: 1.5;
        color: #444;
    }
    .boundary-meta {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0;
    }
    pre {
        background: #1f2328;
        color: #f5f6f8;
        padding: 1rem;
        border-radius: 6px;
        overflow-x: auto;
        font-size: 0.85rem;
        line-height: 1.4;
    }
    code { font-family: "SFMono-Regular", Consolas, Menlo, monospace; }
</style>
</head>
<body>
    <div class="boundary-card">
        <div class="boundary-header">
            <h1>Something went wrong</h1>
        </div>
        <div class="boundary-body">
            <?php if ($isDevelopment): ?>
                <p class="boundary-meta">
                    <strong><?= htmlspecialchars(get_class($exception), ENT_QUOTES) ?></strong>
                    in <code><?= htmlspecialchars($exception->getFile(), ENT_QUOTES) ?>:<?= (int) $exception->getLine() ?></code>
                </p>
                <pre><?= htmlspecialchars($exception->getMessage(), ENT_QUOTES) ?></pre>
                <pre><?= htmlspecialchars($exception->getTraceAsString(), ENT_QUOTES) ?></pre>
            <?php else: ?>
                <p>
                    We hit an unexpected error while handling your request.
                    Our team has been notified — please try again in a moment.
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
