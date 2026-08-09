<?php
declare(strict_types=1);

function getBeritaKategoriList(): array
{
    $db = getDb();
    return $db->query('SELECT * FROM berita_kategori ORDER BY nama ASC')->fetchAll();
}

function saveBeritaKategori(string $nama): int
{
    $db = getDb();
    $stmt = $db->prepare('INSERT INTO berita_kategori (nama, slug) VALUES (?, ?)');
    $stmt->execute([$nama, slugify($nama)]);
    return (int) $db->lastInsertId();
}

function deleteBeritaKategori(int $id): bool
{
    $db = getDb();
    $stmt = $db->prepare('DELETE FROM berita_kategori WHERE id = ?');
    return $stmt->execute([$id]);
}

function getBeritaList(bool $publishSaja = false, int $limit = 0, int $page = 1): array
{
    $db = getDb();
    $where = $publishSaja ? 'WHERE b.status = "publish"' : '';
    $sql = "SELECT b.*, k.nama AS kategori_nama FROM berita_desa b
            LEFT JOIN berita_kategori k ON k.id = b.kategori_id
            $where ORDER BY b.published_at DESC, b.id DESC";
    if ($limit > 0) {
        $offset = max(0, $page - 1) * $limit;
        $sql .= ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
    }
    return $db->query($sql)->fetchAll();
}

function countBerita(bool $publishSaja = false): int
{
    $db = getDb();
    $sql = 'SELECT COUNT(*) FROM berita_desa';
    if ($publishSaja) {
        $sql .= ' WHERE status = "publish"';
    }
    return (int) $db->query($sql)->fetchColumn();
}

function getBeritaBySlug(string $slug): ?array
{
    $db = getDb();
    $stmt = $db->prepare(
        'SELECT b.*, k.nama AS kategori_nama FROM berita_desa b
         LEFT JOIN berita_kategori k ON k.id = b.kategori_id
         WHERE b.slug = ? AND b.status = "publish"'
    );
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

function getBeritaById(int $id): ?array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM berita_desa WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function slugExistsBerita(string $slug, ?int $excludeId = null): bool
{
    $db = getDb();
    $sql = 'SELECT COUNT(*) FROM berita_desa WHERE slug = ?';
    $params = [$slug];
    if ($excludeId !== null) {
        $sql .= ' AND id != ?';
        $params[] = $excludeId;
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn() > 0;
}

function saveBerita(array $data): int
{
    $db = getDb();
    $publishedAt = $data['status'] === 'publish' ? date('Y-m-d H:i:s') : null;
    $stmt = $db->prepare(
        'INSERT INTO berita_desa (judul, slug, kategori_id, konten, gambar_utama, penulis_id, status, published_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $data['judul'],
        $data['slug'],
        $data['kategori_id'] === '' ? null : (int) $data['kategori_id'],
        $data['konten'],
        $data['gambar_utama'] ?? null,
        (int) $_SESSION['admin_id'],
        $data['status'] ?? 'draft',
        $publishedAt,
    ]);
    return (int) $db->lastInsertId();
}

function updateBerita(int $id, array $data): bool
{
    $db = getDb();
    $stmt = $db->prepare(
        'UPDATE berita_desa SET judul = ?, slug = ?, kategori_id = ?, konten = ?, gambar_utama = ?, status = ?
         WHERE id = ?'
    );
    return $stmt->execute([
        $data['judul'],
        $data['slug'],
        $data['kategori_id'] === '' ? null : (int) $data['kategori_id'],
        $data['konten'],
        $data['gambar_utama'] ?? null,
        $data['status'] ?? 'draft',
        $id,
    ]);
}

function deleteBerita(int $id): bool
{
    $db = getDb();
    $berita = getBeritaById($id);
    if ($berita !== null && $berita['gambar_utama'] !== null) {
        $file = UPLOAD_PATH . '/' . $berita['gambar_utama'];
        if (is_file($file)) {
            @unlink($file);
        }
    }
    $stmt = $db->prepare('DELETE FROM berita_desa WHERE id = ?');
    return $stmt->execute([$id]);
}

function tambahViewBerita(int $id): void
{
    $db = getDb();
    $db->prepare('UPDATE berita_desa SET views = views + 1 WHERE id = ?')->execute([$id]);
}

function getBeritaListAdmin(int $limit, int $page = 1, string $q = '', ?int $kategoriId = null, string $status = ''): array
{
    $db = getDb();
    $where = [];
    $params = [];
    if ($q !== '') {
        $where[] = '(b.judul LIKE ? OR b.konten LIKE ?)';
        $params[] = "%$q%";
        $params[] = "%$q%";
    }
    if ($kategoriId !== null && $kategoriId > 0) {
        $where[] = 'b.kategori_id = ?';
        $params[] = $kategoriId;
    }
    if ($status !== '') {
        $where[] = 'b.status = ?';
        $params[] = $status;
    }
    $sql = 'SELECT b.*, k.nama AS kategori_nama, a.nama AS penulis_nama FROM berita_desa b
            LEFT JOIN berita_kategori k ON k.id = b.kategori_id
            LEFT JOIN admins a ON a.id = b.penulis_id';
    if ($where !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY b.published_at DESC, b.id DESC LIMIT ' . (int) $limit . ' OFFSET ' . (int) max(0, $page - 1) * $limit;
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function countBeritaAdmin(string $q = '', ?int $kategoriId = null, string $status = ''): int
{
    $db = getDb();
    $where = [];
    $params = [];
    if ($q !== '') {
        $where[] = '(judul LIKE ? OR konten LIKE ?)';
        $params[] = "%$q%";
        $params[] = "%$q%";
    }
    if ($kategoriId !== null && $kategoriId > 0) {
        $where[] = 'kategori_id = ?';
        $params[] = $kategoriId;
    }
    if ($status !== '') {
        $where[] = 'status = ?';
        $params[] = $status;
    }
    $sql = 'SELECT COUNT(*) FROM berita_desa';
    if ($where !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function getViewsPerKategori(): array
{
    $db = getDb();
    return $db->query(
        'SELECT k.nama, COALESCE(SUM(b.views), 0) AS total_views
         FROM berita_kategori k
         LEFT JOIN berita_desa b ON b.kategori_id = k.id
         GROUP BY k.id, k.nama ORDER BY total_views DESC'
    )->fetchAll();
}

function getStatistikBerita(): array
{
    $db = getDb();
    $total = (int) $db->query('SELECT COUNT(*) FROM berita_desa')->fetchColumn();
    $stmt = $db->prepare("SELECT COUNT(*) FROM berita_desa WHERE published_at IS NOT NULL AND DATE_FORMAT(published_at, '%Y-%m') = ?");
    $stmt->execute([date('Y-m')]);
    $bulanIni = (int) $stmt->fetchColumn();
    return ['total' => $total, 'bulan_ini' => $bulanIni];
}
