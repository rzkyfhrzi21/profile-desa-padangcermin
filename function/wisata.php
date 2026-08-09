<?php
declare(strict_types=1);

function getWisataList(bool $publishSaja = false, int $limit = 0): array
{
    $db = getDb();
    $sql = 'SELECT * FROM wisata_desa';
    if ($publishSaja) {
        $sql .= ' WHERE status = "publish"';
    }
    $sql .= ' ORDER BY created_at DESC, id DESC';
    if ($limit > 0) {
        $sql .= ' LIMIT ' . (int) $limit;
    }
    return $db->query($sql)->fetchAll();
}

function getWisataBySlug(string $slug): ?array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM wisata_desa WHERE slug = ? AND status = "publish"');
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

function getWisataById(int $id): ?array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM wisata_desa WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function getWisataWithGambar(array $rows): array
{
    if ($rows === []) {
        return [];
    }
    $db = getDb();
    $ids = array_column($rows, 'id');
    $ph = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $db->prepare("SELECT * FROM wisata_gambar WHERE wisata_id IN ($ph) ORDER BY urutan ASC, id ASC");
    $stmt->execute($ids);
    $gambar = [];
    foreach ($stmt->fetchAll() as $g) {
        $gambar[$g['wisata_id']][] = $g;
    }
    foreach ($rows as &$w) {
        $w['gambar'] = $gambar[$w['id']] ?? [];
    }
    return $rows;
}

function getWisataImages(int $wisataId): array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM wisata_gambar WHERE wisata_id = ? ORDER BY urutan ASC, id ASC');
    $stmt->execute([$wisataId]);
    return $stmt->fetchAll();
}

function slugExistsWisata(string $slug, ?int $excludeId = null): bool
{
    $db = getDb();
    $sql = 'SELECT COUNT(*) FROM wisata_desa WHERE slug = ?';
    $params = [$slug];
    if ($excludeId !== null) {
        $sql .= ' AND id != ?';
        $params[] = $excludeId;
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn() > 0;
}

function saveWisata(array $data): int
{
    $db = getDb();
    $stmt = $db->prepare(
        'INSERT INTO wisata_desa (nama, slug, deskripsi, alamat, latitude, longitude, harga_tiket, jam_buka, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $data['nama'],
        $data['slug'],
        $data['deskripsi'],
        $data['alamat'],
        $data['latitude'] === '' ? null : (float) $data['latitude'],
        $data['longitude'] === '' ? null : (float) $data['longitude'],
        $data['harga_tiket'] === '' ? null : $data['harga_tiket'],
        $data['jam_buka'] === '' ? null : $data['jam_buka'],
        $data['status'] ?? 'draft',
    ]);
    return (int) $db->lastInsertId();
}

function updateWisata(int $id, array $data): bool
{
    $db = getDb();
    $stmt = $db->prepare(
        'UPDATE wisata_desa SET nama = ?, slug = ?, deskripsi = ?, alamat = ?, latitude = ?, longitude = ?,
         harga_tiket = ?, jam_buka = ?, status = ? WHERE id = ?'
    );
    return $stmt->execute([
        $data['nama'],
        $data['slug'],
        $data['deskripsi'],
        $data['alamat'],
        $data['latitude'] === '' ? null : (float) $data['latitude'],
        $data['longitude'] === '' ? null : (float) $data['longitude'],
        $data['harga_tiket'] === '' ? null : $data['harga_tiket'],
        $data['jam_buka'] === '' ? null : $data['jam_buka'],
        $data['status'] ?? 'draft',
        $id,
    ]);
}

function addWisataImage(int $wisataId, string $path, int $urutan = 0): void
{
    $db = getDb();
    $stmt = $db->prepare('INSERT INTO wisata_gambar (wisata_id, path_gambar, urutan) VALUES (?, ?, ?)');
    $stmt->execute([$wisataId, $path, $urutan]);
}

function deleteWisataImage(int $gambarId): ?string
{
    $db = getDb();
    $stmt = $db->prepare('SELECT path_gambar FROM wisata_gambar WHERE id = ?');
    $stmt->execute([$gambarId]);
    $path = $stmt->fetchColumn();
    if ($path === false) {
        return null;
    }
    $db->prepare('DELETE FROM wisata_gambar WHERE id = ?')->execute([$gambarId]);
    return (string) $path;
}

function deleteWisata(int $id): bool
{
    $db = getDb();
    $gambar = getWisataImages($id);
    foreach ($gambar as $g) {
        $file = UPLOAD_PATH . '/' . $g['path_gambar'];
        if (is_file($file)) {
            @unlink($file);
        }
    }
    $db->prepare('DELETE FROM wisata_gambar WHERE wisata_id = ?')->execute([$id]);
    $stmt = $db->prepare('DELETE FROM wisata_desa WHERE id = ?');
    return $stmt->execute([$id]);
}
