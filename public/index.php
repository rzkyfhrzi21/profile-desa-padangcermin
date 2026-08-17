<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/function/db.php';
require dirname(__DIR__) . '/function/helpers.php';

$routes = require dirname(__DIR__) . '/config/routes.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$base = APP_BASE;
if ($base !== '' && str_starts_with($path, $base)) {
    $path = substr($path, strlen($base));
}
$path = '/' . ltrim($path, '/');

$route = null;
$params = [];

if (isset($routes[$path])) {
    $route = $routes[$path];
} else {
    foreach ($routes as $pattern => $r) {
        if (!str_contains($pattern, '{')) {
            continue;
        }
        $regex = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';
        if (preg_match($regex, $path, $m)) {
            $route = $r;
            foreach ($m as $k => $v) {
                if (is_string($k)) {
                    $params[$k] = $v;
                }
            }
            break;
        }
    }
}

if ($route === null) {
    http_response_code(404);
    echo 'Halaman tidak ditemukan.';
    exit;
}

// A01 Broken Access Control — satu titik enforcement untuk semua route dashboard.
// Tidak boleh dilewati per-file view. /auth/login sengaja publik.
$isDashboard = str_starts_with($path, '/dashboard/') || $path === '/dashboard';
if ($isDashboard) {
    require dirname(__DIR__) . '/function/auth.php';
    requireAdmin();
}

// A09/SEO — pastikan area privat (auth & dashboard) tidak pernah terindeks Google,
// baik via meta robots (sudah ada di view) maupun HTTP header X-Robots-Tag.
if ($isDashboard || str_starts_with($path, '/auth')) {
    header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet', true);
}

foreach ($route['function'] as $fn) {
    require dirname(__DIR__) . '/function/' . $fn;
}

if ($path === '/sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
}

require dirname(__DIR__) . '/view/' . $route['view'];
