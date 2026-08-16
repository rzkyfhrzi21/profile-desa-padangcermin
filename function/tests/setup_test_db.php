<?php
declare(strict_types=1);

/**
 * Membuat database test `padang_cermin_test` dari schema.sql.
 * Aman: hanya menyentuh database test, TIDAK menyentuh database produksi.
 *
 * Usage (CMD):
 *   php function/tests/setup_test_db.php
 */

require __DIR__ . '/bootstrap.php';

$pdo = TestBootstrap::pdoWithoutDb();
$dbName = TestBootstrap::TEST_DB;

$pdo->exec('DROP DATABASE IF EXISTS `' . $dbName . '`');
$pdo->exec('CREATE DATABASE `' . $dbName . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$pdo->exec('USE `' . $dbName . '`');

$schemaFile = dirname(__DIR__, 2) . '/database/schema.sql';
if (!is_file($schemaFile)) {
    fwrite(STDERR, "Tidak menemukan database/schema.sql\n");
    exit(1);
}

$content = file_get_contents($schemaFile);
$statements = TestBootstrap::extractCreateTables($content);

$created = 0;
foreach ($statements as $stmt) {
    try {
        $pdo->exec($stmt);
        $created++;
    } catch (Throwable $t) {
        fwrite(STDERR, "Gagal eksekusi statement:\n$stmt\nError: " . $t->getMessage() . "\n");
        exit(1);
    }
}

// Seed minimal: profil default (single-row) + 1 admin untuk FK penulis berita
$pdo->exec(
    "INSERT INTO profil_desa (id, nama_pekon, visi, misi, alamat_kantor, maps_embed_url)
     VALUES (1, 'Pekon Padang Cermin', '', '', '', NULL)
     ON DUPLICATE KEY UPDATE id = id"
);
$pdo->exec(
    "INSERT INTO admins (id, username, password_hash, nama)
     VALUES (1, 'testadmin', '" . password_hash('testpass', PASSWORD_BCRYPT) . "', 'Test Admin')
     ON DUPLICATE KEY UPDATE id = id"
);

echo "Database test '{$dbName}' siap. Tabel dibuat: {$created}\n";
