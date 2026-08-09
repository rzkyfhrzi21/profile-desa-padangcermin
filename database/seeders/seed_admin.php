<?php
declare(strict_types=1);

/**
 * Buat akun admin pertama (jalankan manual sekali lewat CLI, lalu hapus).
 * CMD: php database/seeders/seed_admin.php <username> <password> <nama>
 */
require __DIR__ . '/../../config/config.php';
require __DIR__ . '/../../function/db.php';

[$username, $password, $nama] = array_slice($argv ?? [], 1, 3);

if (!$username || !$password || !$nama) {
    fwrite(STDERR, "Gunakan: php seed_admin.php <username> <password> <nama>\n");
    exit(1);
}

$db = getDb();
$stmt = $db->prepare('INSERT INTO admins (username, password_hash, nama) VALUES (?, ?, ?)');
$stmt->execute([$username, password_hash($password, PASSWORD_BCRYPT), $nama]);
echo "Admin '{$username}' berhasil dibuat.\n";
