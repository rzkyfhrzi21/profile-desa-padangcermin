<?php
declare(strict_types=1);

function getDataKependudukan(int $limit = 12): array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM data_kependudukan ORDER BY periode DESC LIMIT ?');
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getDataKependudukanTerbaru(): ?array
{
    $db = getDb();
    $stmt = $db->query('SELECT * FROM data_kependudukan ORDER BY periode DESC LIMIT 1');
    return $stmt->fetch() ?: null;
}

/**
 * Jumlah dusun unik pada periode kependudukan terbaru.
 * Membaca dari kependudukan_dusun (bukan tabel dusun_master) agar
 * angka di homepage konsisten dengan data yang benar-benar terisi.
 */
function getJumlahDusunTerbaru(): int
{
    $db = getDb();
    $periode = $db->query('SELECT periode FROM data_kependudukan ORDER BY periode DESC LIMIT 1')->fetchColumn();
    if ($periode === false) {
        return 0;
    }
    $stmt = $db->prepare('SELECT COUNT(DISTINCT nama_dusun) FROM kependudukan_dusun WHERE periode = ?');
    $stmt->execute([$periode]);
    return (int) $stmt->fetchColumn();
}

function getKependudukanDusun(?string $periode = null): array
{
    $db = getDb();
    if ($periode === null) {
        $periode = $db->query('SELECT periode FROM data_kependudukan ORDER BY periode DESC LIMIT 1')->fetchColumn();
        if ($periode === false) {
            return [];
        }
    }
    $stmt = $db->prepare('SELECT * FROM kependudukan_dusun WHERE periode = ? ORDER BY jumlah_jiwa DESC, nama_dusun ASC');
    $stmt->execute([$periode]);
    return $stmt->fetchAll();
}

function getTrenKependudukan(): array
{
    $rows = getDataKependudukan(12);
    return [
        'periode'     => array_reverse(array_column($rows, 'periode')),
        'jumlah_kk'   => array_reverse(array_map(fn($r) => (int) $r['jumlah_kk'], $rows)),
        'jumlah_jiwa' => array_reverse(array_map(fn($r) => (int) $r['jumlah_jiwa'], $rows)),
    ];
}

function getDataKependudukanById(int $id): ?array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM data_kependudukan WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function saveKependudukan(array $data): int
{
    $db = getDb();
    $stmt = $db->prepare('INSERT INTO data_kependudukan (periode, jumlah_kk, jumlah_jiwa, jumlah_laki, jumlah_perempuan, keterangan) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $data['periode'],
        (int) $data['jumlah_kk'],
        (int) $data['jumlah_jiwa'],
        (int) ($data['jumlah_laki'] ?? 0),
        (int) ($data['jumlah_perempuan'] ?? 0),
        $data['keterangan'] === '' ? null : $data['keterangan'],
    ]);
    return (int) $db->lastInsertId();
}

function updateKependudukan(int $id, array $data): bool
{
    $db = getDb();
    $stmt = $db->prepare('UPDATE data_kependudukan SET periode = ?, jumlah_kk = ?, jumlah_jiwa = ?, jumlah_laki = ?, jumlah_perempuan = ?, keterangan = ? WHERE id = ?');
    return $stmt->execute([
        $data['periode'],
        (int) $data['jumlah_kk'],
        (int) $data['jumlah_jiwa'],
        (int) ($data['jumlah_laki'] ?? 0),
        (int) ($data['jumlah_perempuan'] ?? 0),
        $data['keterangan'] === '' ? null : $data['keterangan'],
        $id,
    ]);
}

function deleteKependudukan(int $id): bool
{
    $db = getDb();
    $stmt = $db->prepare('DELETE FROM data_kependudukan WHERE id = ?');
    return $stmt->execute([$id]);
}

function getDusunByPeriode(string $periode): array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM kependudukan_dusun WHERE periode = ? ORDER BY nama_dusun ASC');
    $stmt->execute([$periode]);
    return $stmt->fetchAll();
}

function saveDusunKependudukan(array $data): int
{
    $db = getDb();
    // Upsert: update if same periode + nama_dusun exists
    $stmt = $db->prepare('SELECT id FROM kependudukan_dusun WHERE periode = ? AND nama_dusun = ?');
    $stmt->execute([$data['periode'], $data['nama_dusun']]);
    $existing = $stmt->fetchColumn();
    if ($existing) {
        $stmt2 = $db->prepare('UPDATE kependudukan_dusun SET jumlah_laki = ?, jumlah_perempuan = ?, jumlah_kk = ?, jumlah_jiwa = ? WHERE id = ?');
        $stmt2->execute([
            (int) ($data['jumlah_laki'] ?? 0),
            (int) ($data['jumlah_perempuan'] ?? 0),
            (int) ($data['jumlah_kk'] ?? 0),
            (int) ($data['jumlah_jiwa'] ?? 0),
            (int) $existing,
        ]);
        return (int) $existing;
    }
    $stmt2 = $db->prepare('INSERT INTO kependudukan_dusun (periode, nama_dusun, jumlah_laki, jumlah_perempuan, jumlah_kk, jumlah_jiwa) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt2->execute([
        $data['periode'],
        $data['nama_dusun'],
        (int) ($data['jumlah_laki'] ?? 0),
        (int) ($data['jumlah_perempuan'] ?? 0),
        (int) ($data['jumlah_kk'] ?? 0),
        (int) ($data['jumlah_jiwa'] ?? 0),
    ]);
    return (int) $db->lastInsertId();
}

function deleteDusunKependudukan(int $id): bool
{
    $db = getDb();
    $stmt = $db->prepare('DELETE FROM kependudukan_dusun WHERE id = ?');
    return $stmt->execute([$id]);
}
