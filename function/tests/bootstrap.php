<?php
declare(strict_types=1);

/**
 * Bootstrap test: memuat environment & seluruh modul function.
 * TIDAK memuat config/config.php (yang bergantung pada $_SERVER['HTTP_HOST']
 * dan session); sebaliknya mendefinisikan konstanta DB sendiri yang menunjuk
 * ke database test `padang_cermin_test` agar TIDAK pernah menyentuh data produksi.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Test hanya bisa dijalankan lewat CLI.\n");
    exit(1);
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('ROOT_PATH', dirname(__DIR__, 2));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('APP_BASE', '/kkn-padangcermin');
define('APP_URL', 'http://localhost/kkn-padangcermin');
define('APP_ENV', 'testing');

define('DB_HOST', 'localhost');
define('DB_PORT', '3309');
define('DB_NAME', 'padang_cermin_test');
define('DB_USER', 'root');
define('DB_PASS', '');

if (!isset($_SESSION)) {
    $_SESSION = [];
}
$_SESSION['admin_id'] = 1;

final class TestBootstrap
{
    public const TEST_DB = 'padang_cermin_test';

    private static ?PDO $pdo = null;
    private static ?PDO $pdoNoDb = null;

    public static function pdoWithoutDb(): PDO
    {
        if (self::$pdoNoDb === null) {
            self::$pdoNoDb = new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return self::$pdoNoDb;
    }

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . self::TEST_DB . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        }
        return self::$pdo;
    }

    /** Ambil statement CREATE TABLE / CREATE DATABASE dari isi schema.sql. */
    public static function extractCreateTables(string $content): array
    {
        $statements = [];
        // Pisahkan per statement dengan delimiter ';' yang berada di akhir baris.
        $tokens = preg_split('/;\s*(\r?\n|$)/', $content);
        foreach ($tokens as $t) {
            $t = trim($t);
            if ($t === '') {
                continue;
            }
            if (preg_match('/^CREATE\s+(DATABASE|TABLE)\b/i', $t)) {
                $statements[] = $t . ';';
            }
        }
        return $statements;
    }

    public static function truncate(array $tables): void
    {
        $db = self::pdo();
        $db->exec('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $t) {
            $db->exec('TRUNCATE TABLE `' . $t . '`');
        }
        $db->exec('SET FOREIGN_KEY_CHECKS=1');
    }
}

require ROOT_PATH . '/function/db.php';
require ROOT_PATH . '/function/helpers.php';
require ROOT_PATH . '/function/wisata.php';
require ROOT_PATH . '/function/berita.php';
require ROOT_PATH . '/function/potensi.php';
require ROOT_PATH . '/function/struktur.php';
require ROOT_PATH . '/function/kependudukan.php';
require ROOT_PATH . '/function/profil.php';

require __DIR__ . '/TestCase.php';
