<?php
declare(strict_types=1);

function getStrukturAll(): array
{
    $db = getDb();
    $stmt = $db->query('SELECT * FROM struktur_organisasi ORDER BY urutan ASC, id ASC');
    return $stmt->fetchAll();
}

/**
 * Bangun tree di memori dari satu query (hindari N+1).
 * Return [node_id => children[]], children berisi array node.
 */
function buildStrukturTree(array $rows): array
{
    $tree = [];
    $refs = [];
    foreach ($rows as $row) {
        $row['children'] = [];
        $refs[$row['id']] = $row;
    }
    foreach ($refs as &$node) {
        $pid = $node['parent_id'];
        if ($pid !== null && isset($refs[$pid])) {
            $refs[$pid]['children'][] = &$node;
        } else {
            $tree[] = &$node;
        }
    }
    unset($node);
    return $tree;
}

function getStrukturTree(): array
{
    return buildStrukturTree(getStrukturAll());
}

function getStrukturById(int $id): ?array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM struktur_organisasi WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function getKontakPerson(): array
{
    $db = getDb();
    $stmt = $db->query('SELECT * FROM struktur_organisasi WHERE tampil_di_kontak = 1 ORDER BY urutan ASC, id ASC');
    return $stmt->fetchAll();
}

function saveStruktur(array $data): int
{
    $db = getDb();
    $stmt = $db->prepare(
        'INSERT INTO struktur_organisasi (parent_id, nama, jabatan, pendidikan_terakhir, foto, tampil_di_kontak, urutan)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $data['parent_id'] === '' ? null : (int) $data['parent_id'],
        $data['nama'],
        $data['jabatan'],
        $data['pendidikan_terakhir'] === '' ? null : $data['pendidikan_terakhir'],
        $data['foto'] ?? null,
        isset($data['tampil_di_kontak']) ? 1 : 0,
        (int) ($data['urutan'] ?? 0),
    ]);
    return (int) $db->lastInsertId();
}

function updateStruktur(int $id, array $data): bool
{
    $db = getDb();
    $stmt = $db->prepare(
        'UPDATE struktur_organisasi SET parent_id = ?, nama = ?, jabatan = ?, pendidikan_terakhir = ?,
         foto = ?, tampil_di_kontak = ?, urutan = ? WHERE id = ?'
    );
    return $stmt->execute([
        $data['parent_id'] === '' ? null : (int) $data['parent_id'],
        $data['nama'],
        $data['jabatan'],
        $data['pendidikan_terakhir'] === '' ? null : $data['pendidikan_terakhir'],
        $data['foto'] ?? null,
        isset($data['tampil_di_kontak']) ? 1 : 0,
        (int) ($data['urutan'] ?? 0),
        $id,
    ]);
}

function deleteStruktur(int $id): bool
{
    $db = getDb();
    $anak = $db->prepare('SELECT id FROM struktur_organisasi WHERE parent_id = ?');
    $anak->execute([$id]);
    if ($anak->fetchColumn() !== false) {
        return false;
    }
    $stmt = $db->prepare('DELETE FROM struktur_organisasi WHERE id = ?');
    return $stmt->execute([$id]);
}

/** Daftar pasangan id → "nama (jabatan)" untuk dropdown parent, minus node itu sendiri. */
function strukturOptions(array $rows, ?int $excludeId = null): array
{
    $options = [];
    foreach ($rows as $row) {
        if ($row['id'] === $excludeId) {
            continue;
        }
        $options[$row['id']] = $row['nama'] . ' — ' . $row['jabatan'];
    }
    return $options;
}
