<?php
declare(strict_types=1);

/**
 * ErrorBoundary
 *
 * Centralized catch-all for a PHP request: uncaught exceptions, classic
 * PHP errors/warnings/notices, and true fatal crashes (E_ERROR, parse
 * errors, out-of-memory) that bypass normal error handling entirely.
 *
 * Usage:
 *   ErrorBoundary::register(require __DIR__ . '/config.php');
 *
 * Call this as the very first thing in your entry script, before any
 * output is produced, so the internal output buffer captures everything
 * that follows.
 */
final class ErrorBoundary
{
    private static ?self $instance = null;

    private array $config;

    /**
     * Reentrancy guard. If rendering the fallback UI itself throws
     * (e.g. a broken template), this stops us from looping back into
     * handleException()/handleShutdown() forever.
     */
    private bool $isRendering = false;

    private function __construct(array $config)
    {
        $this->config = $config;
        $this->ensureLogDirectoryExists();
    }

    public static function register(array $config): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
            self::$instance->installHandlers();
        }

        return self::$instance;
    }

    private function installHandlers(): void
    {
        // Buffer all output from here on. This is what lets us throw away
        // a half-rendered page and replace it cleanly with the fallback UI
        // if something fails midway through — see wipeOutputBuffer().
        ob_start();

        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * Converts classic PHP errors, warnings, and notices into ErrorException
     * so they flow through the exact same handling path as thrown
     * exceptions instead of being a separate, inconsistently-handled case.
     */
    public function handleError(int $severity, string $message, string $file = '', int $line = 0): bool
    {
        // Respect error_reporting() level and the @-suppression operator:
        // if the currently configured mask doesn't include this severity,
        // let PHP's normal (silent) behavior proceed instead of escalating it.
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    public function handleException(Throwable $exception): void
    {
        $this->render($exception);
    }

    /**
     * Runs on every request shutdown, success or failure. We only act if
     * error_get_last() reports one of the fatal types that never reach
     * set_error_handler (E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR).
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error === null) {
            return;
        }

        $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
        if (!in_array($error['type'], $fatalTypes, true)) {
            return;
        }

        $exception = new ErrorException(
            $error['message'],
            0,
            $error['type'],
            $error['file'],
            $error['line']
        );

        $this->render($exception);
    }

    private function render(Throwable $exception): void
    {
        if ($this->isRendering) {
            return;
        }
        $this->isRendering = true;

        $this->logException($exception);
        $this->wipeOutputBuffer();
        $this->sendServerErrorStatus();
        $this->renderFallbackUi($exception);

        exit(1);
    }

    /**
     * Discards whatever partial HTML has been captured in the output
     * buffer(s) so far. Without this, the fallback UI would be appended
     * after — not instead of — a half-open <table> or broken <div> tree,
     * producing garbled output. ob_end_clean() is looped because some
     * environments (or nested ob_start() calls elsewhere in the app) can
     * leave more than one buffering level active.
     */
    private function wipeOutputBuffer(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    private function sendServerErrorStatus(): void
    {
        if (!headers_sent()) {
            http_response_code(500);
        }
    }

    private function logException(Throwable $exception): void
    {
        $entry = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n%s\n",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString(),
            str_repeat('-', 60)
        );

        error_log($entry, 3, $this->config['log_file']);
    }

    private function renderFallbackUi(Throwable $exception): void
    {
        $isDevelopment = ($this->config['environment'] ?? 'production') === 'development';
        $appName = $this->config['app_name'] ?? 'Application';

        // fallback-ui.php is a plain template that reads $isDevelopment,
        // $appName, and $exception from this local scope.
        require __DIR__ . '/fallback-ui.php';
    }

    private function ensureLogDirectoryExists(): void
    {
        $dir = dirname($this->config['log_file']);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }
}
