<?php
declare(strict_types=1);

function getProfil(): array
{
    $db = getDb();
    $stmt = $db->query('SELECT * FROM profil_desa WHERE id = 1');
    $profil = $stmt->fetch();
    if ($profil === false) {
        $db->prepare('INSERT INTO profil_desa (id, nama_pekon, visi, misi, alamat_kantor, latitude, longitude, maps_embed_url) VALUES (1, "Pekon Padang Cermin", "", "", "", 0, 0, NULL)')->execute();
        $profil = ['id' => 1, 'nama_pekon' => 'Pekon Padang Cermin', 'visi' => '', 'misi' => '', 'sambutan_kepala_pekon' => null, 'foto_kepala_pekon' => null, 'alamat_kantor' => '', 'latitude' => 0, 'longitude' => 0, 'maps_embed_url' => null, 'telepon' => null, 'email' => null, 'whatsapp' => null, 'updated_at' => date('Y-m-d H:i:s')];
    }
    return $profil;
}

function updateProfil(array $data): bool
{
    $db = getDb();
    $stmt = $db->prepare(
        'UPDATE profil_desa SET nama_pekon = ?, visi = ?, misi = ?, sambutan_kepala_pekon = ?,
         alamat_kantor = ?, latitude = ?, longitude = ?, maps_embed_url = ?, telepon = ?, email = ?, whatsapp = ?, updated_at = NOW()
         WHERE id = 1'
    );
    return $stmt->execute([
        $data['nama_pekon'],
        $data['visi'],
        $data['misi'],
        $data['sambutan_kepala_pekon'] === '' ? null : $data['sambutan_kepala_pekon'],
        $data['alamat_kantor'],
        (float) $data['latitude'],
        (float) $data['longitude'],
        $data['maps_embed_url'] === '' ? null : $data['maps_embed_url'],
        $data['telepon'] === '' ? null : $data['telepon'],
        $data['email'] === '' ? null : $data['email'],
        $data['whatsapp'] === '' ? null : $data['whatsapp'],
    ]);
}

function updateFotoKepalaPekon(string $path): void
{
    $db = getDb();
    $stmt = $db->prepare('UPDATE profil_desa SET foto_kepala_pekon = ?, updated_at = NOW() WHERE id = 1');
    $stmt->execute([$path]);
}

function getSambutanKepalaPekon(): ?array
{
    $profil = getProfil();
    if (empty($profil['sambutan_kepala_pekon'])) {
        return null;
    }
    return $profil;
}
