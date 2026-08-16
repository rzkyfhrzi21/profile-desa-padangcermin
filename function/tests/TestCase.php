<?php
declare(strict_types=1);

/**
 * Kelas dasar untuk semua test case.
 * Menyediakan helper assertion + isolasi data per test via truncate.
 */

abstract class TestCase
{
    protected static array $testsRun = [];
    protected static array $failures = [];
    protected static array $errors = [];

    /** Daftar tabel yang di-truncate di awal & akhir tiap test. */
    protected array $tables = [];

    public function __construct()
    {
    }

    public function setUp(): void
    {
        TestBootstrap::truncate($this->tables);
    }

    public function tearDown(): void
    {
        TestBootstrap::truncate($this->tables);
    }

    protected function db(): PDO
    {
        return TestBootstrap::pdo();
    }

    // ---- Assertions -------------------------------------------------------

    protected function fail(string $message = 'Test gagal.'): void
    {
        throw new AssertionFailedException($message);
    }

    protected function assertTrue($cond, string $message = 'assertTrue gagal'): void
    {
        if ($cond !== true) {
            $this->fail($message . ' (nilai: ' . var_export($cond, true) . ')');
        }
    }

    protected function assertFalse($cond, string $message = 'assertFalse gagal'): void
    {
        if ($cond !== false) {
            $this->fail($message . ' (nilai: ' . var_export($cond, true) . ')');
        }
    }

    protected function assertNull($val, string $message = 'assertNull gagal'): void
    {
        if ($val !== null) {
            $this->fail($message . ' (nilai: ' . var_export($val, true) . ')');
        }
    }

    protected function assertNotNull($val, string $message = 'assertNotNull gagal'): void
    {
        if ($val === null) {
            $this->fail($message);
        }
    }

    protected function assertEquals($expected, $actual, string $message = 'assertEquals gagal'): void
    {
        if ($expected != $actual) {
            $this->fail($message . " (diharapkan: " . var_export($expected, true)
                . ", aktual: " . var_export($actual, true) . ')');
        }
    }

    protected function assertSame($expected, $actual, string $message = 'assertSame gagal'): void
    {
        if ($expected !== $actual) {
            $this->fail($message . " (diharapkan: " . var_export($expected, true)
                . ", aktual: " . var_export($actual, true) . ')');
        }
    }

    protected function assertNotSame($expected, $actual, string $message = 'assertNotSame gagal'): void
    {
        if ($expected === $actual) {
            $this->fail($message . " (keduanya: " . var_export($expected, true) . ')');
        }
    }

    protected function assertArrayHasKey(string $key, array $array, string $message = 'assertArrayHasKey gagal'): void
    {
        if (!array_key_exists($key, $array)) {
            $this->fail($message . " (key '$key' tidak ada; keys: " . implode(', ', array_keys($array)) . ')');
        }
    }

    protected function assertCount(int $expected, array $array, string $message = 'assertCount gagal'): void
    {
        if (count($array) !== $expected) {
            $this->fail($message . " (diharapkan: $expected, aktual: " . count($array) . ')');
        }
    }

    protected function assertGreaterThan($expected, $actual, string $message = 'assertGreaterThan gagal'): void
    {
        if ($actual <= $expected) {
            $this->fail($message . " (diharapkan > $expected, aktual: " . var_export($actual, true) . ')');
        }
    }

    protected function assertContains($needle, array $haystack, string $message = 'assertContains gagal'): void
    {
        if (!in_array($needle, $haystack, true)) {
            $this->fail($message . " ('" . var_export($needle, true) . "' tidak ada dalam array)");
        }
    }

    protected function assertNotContains($needle, array $haystack, string $message = 'assertNotContains gagal'): void
    {
        if (in_array($needle, $haystack, true)) {
            $this->fail($message . " ('" . var_export($needle, true) . "' ternyata ada dalam array)");
        }
    }

    protected function assertNotEmpty($val, string $message = 'assertNotEmpty gagal'): void
    {
        if (empty($val)) {
            $this->fail($message . ' (nilai kosong)');
        }
    }

    protected function assertArrayNotHasKey(string $key, array $array, string $message = 'assertArrayNotHasKey gagal'): void
    {
        if (array_key_exists($key, $array)) {
            $this->fail($message . " (key '$key' ternyata ada)");
        }
    }

    protected function assertStringContainsString(string $needle, string $haystack, string $message = 'assertStringContainsString gagal'): void
    {
        if (strpos($haystack, $needle) === false) {
            $this->fail($message . " ('$needle' tidak ditemukan dalam: " . substr($haystack, 0, 80) . ')');
        }
    }
}

final class AssertionFailedException extends Exception
{
}
