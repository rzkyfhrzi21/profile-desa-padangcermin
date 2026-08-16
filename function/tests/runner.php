<?php
declare(strict_types=1);

/**
 * Test runner CLI.
 * Menemukan semua file *_test.php, menjalankan tiap metode test*(),
 * lalu mencetak ringkasan & exit code (0 = sukses, 1 = ada yang gagal/error).
 *
 * Usage (CMD):
 *   php function/tests/runner.php
 */

require __DIR__ . '/bootstrap.php';

$testFiles = glob(__DIR__ . '/*_test.php') ?: [];
sort($testFiles);

if ($testFiles === []) {
    fwrite(STDERR, "Tidak ada file *_test.php ditemukan di function/tests/\n");
    exit(1);
}

$passed = 0;
$failed = 0;
$errors = 0;
$total = 0;
$results = []; // [class, method, status, message]

foreach ($testFiles as $file) {
    require_once $file;
}

$declared = get_declared_classes();
foreach ($declared as $class) {
    if (!is_subclass_of($class, TestCase::class)) {
        continue;
    }
    $ref = new ReflectionClass($class);
    if ($ref->isAbstract()) {
        continue;
    }
    $instance = $ref->newInstance();
    $methods = $ref->getMethods(ReflectionMethod::IS_PUBLIC);
    foreach ($methods as $m) {
        $name = $m->getName();
        if (strpos($name, 'test') !== 0) {
            continue;
        }
        $total++;
        try {
            $instance->setUp();
            $instance->$name();
            $instance->tearDown();
            $passed++;
            $results[] = ['class' => $class, 'method' => $name, 'status' => 'PASS', 'message' => ''];
        } catch (AssertionFailedException $e) {
            try {
                $instance->tearDown();
            } catch (Throwable $ignore) {
            }
            $failed++;
            $results[] = ['class' => $class, 'method' => $name, 'status' => 'FAIL', 'message' => $e->getMessage()];
        } catch (Throwable $e) {
            try {
                $instance->tearDown();
            } catch (Throwable $ignore) {
            }
            $errors++;
            $results[] = ['class' => $class, 'method' => $name, 'status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }
}

foreach ($results as $r) {
    $badge = $r['status'] === 'PASS' ? 'PASS' : ($r['status'] === 'FAIL' ? 'FAIL' : 'ERROR');
    $line = sprintf("[%s] %s::%s", $badge, $r['class'], $r['method']);
    if ($r['message'] !== '') {
        $line .= "\n      -> " . $r['message'];
    }
    echo $line . "\n";
}

echo "\n============================================\n";
echo "Total: {$total}  |  Pass: {$passed}  |  Fail: {$failed}  |  Error: {$errors}\n";
echo "============================================\n";

exit($failed + $errors > 0 ? 1 : 0);
