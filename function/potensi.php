<?php
declare(strict_types=1);

function getPotensiList(bool $aktifSaja = false): array
{
    $db = getDb();
    $sql = 'SELECT * FROM potensi_desa';
    if ($aktifSaja) {
        $sql .= ' WHERE status = "aktif"';
    }
    $sql .= ' ORDER BY urutan ASC, id ASC';
    return $db->query($sql)->fetchAll();
}

function getPotensiById(int $id): ?array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM potensi_desa WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function savePotensi(array $data): int
{
    $db = getDb();
    $stmt = $db->prepare(
        'INSERT INTO potensi_desa (judul, deskripsi, gambar, ikon, kategori, urutan, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $data['judul'],
        $data['deskripsi'],
        $data['gambar'] ?? null,
        $data['ikon'] ?? null,
        $data['kategori'] === '' ? null : $data['kategori'],
        (int) ($data['urutan'] ?? 0),
        $data['status'] ?? 'aktif',
    ]);
    return (int) $db->lastInsertId();
}

function updatePotensi(int $id, array $data): bool
{
    $db = getDb();
    $stmt = $db->prepare(
        'UPDATE potensi_desa SET judul = ?, deskripsi = ?, gambar = ?, ikon = ?, kategori = ?, urutan = ?, status = ? WHERE id = ?'
    );
    return $stmt->execute([
        $data['judul'],
        $data['deskripsi'],
        $data['gambar'] ?? null,
        $data['ikon'] ?? null,
        $data['kategori'] === '' ? null : $data['kategori'],
        (int) ($data['urutan'] ?? 0),
        $data['status'] ?? 'aktif',
        $id,
    ]);
}

function deletePotensi(int $id): bool
{
    $db = getDb();
    $stmt = $db->prepare('DELETE FROM potensi_desa WHERE id = ?');
    return $stmt->execute([$id]);
}
