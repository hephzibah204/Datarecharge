<?php
// PHP built-in server router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If an actual FILE exists, serve it directly
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false;
}

// Admin dashboard clean URL routing: /admin/dashboard/page-name
if (preg_match('#^/admin/dashboard/([a-z0-9_-]+)$#', $uri, $m)) {
    $_GET['url'] = $m[1];
    chdir(__DIR__ . '/admin/dashboard');
    require __DIR__ . '/admin/dashboard/index.php';
    return true;
}

// Mobile home clean URL routing: /mobile/home/ (homepage)
if ($uri === '/mobile/home' || $uri === '/mobile/home/') {
    $_GET['url'] = '';
    chdir(__DIR__ . '/mobile/home');
    require __DIR__ . '/mobile/home/index.php';
    return true;
}

// Mobile home clean URL routing: /mobile/home/page-name
if (preg_match('#^/mobile/home/([a-z0-9_-]+)$#', $uri, $m)) {
    $_GET['url'] = $m[1];
    chdir(__DIR__ . '/mobile/home');
    require __DIR__ . '/mobile/home/index.php';
    return true;
}

// Fallback: let PHP return 404
return false;
