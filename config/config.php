<?php
declare(strict_types=1);

session_set_cookie_params([
    'httponly' => true,
    'secure'   => isset($_SERVER['HTTPS']),
    'samesite' => 'Lax',
]);
session_start();

define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');

function env(string $key, string $default = ''): string
{
    static $env = null;
    if ($env === null) {
        $env = [];
        $file = ROOT_PATH . '/config/.env';
        if (is_file($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$k, $v] = array_map('trim', explode('=', $line, 2));
                $env[$k] = $v;
            }
        }
    }
    return $env[$key] ?? $default;
}

define('APP_URL', rtrim(env('APP_URL', 'http://localhost'), '/'));
define('APP_BASE', rtrim(env('APP_BASE', ''), '/'));
define('APP_ENV', env('APP_ENV', 'production'));
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'padang_cermin_db'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT_PATH . '/config/logs/php-error.log');
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

define('LOGIN_ATTEMPT_LIMIT', 5);
define('LOGIN_ATTEMPT_WINDOW', 900);
