<?php
declare(strict_types=1);

function catatLog(string $aksi, string $tabelTerkait, ?int $dataId = null): void
{
    if (!isLoggedIn()) {
        return;
    }
    try {
        $db = getDb();
        $stmt = $db->prepare('INSERT INTO log_aktivitas (admin_id, aksi, tabel_terkait, data_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_SESSION['admin_id'], $aksi, $tabelTerkait, $dataId]);
    } catch (Throwable $e) {
        error_log('catatLog gagal: ' . $e->getMessage());
    }
}

function getLogTerbaru(int $limit = 10): array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT l.*, a.nama AS admin_nama FROM log_aktivitas l LEFT JOIN admins a ON a.id = l.admin_id ORDER BY l.waktu DESC LIMIT ?');
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
