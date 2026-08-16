<?php
declare(strict_types=1);

final class ProfilTest extends TestCase
{
    protected array $tables = ['profil_desa'];

    public function testGetProfilMembuatDefaultJikaBelumAda(): void
    {
        $this->db()->exec('DELETE FROM profil_desa');
        $p = getProfil();
        $this->assertSame(1, (int) $p['id']);
        $this->assertSame('Pekon Padang Cermin', $p['nama_pekon']);
        // baris default tersimpan di DB
        $row = $this->db()->query('SELECT COUNT(*) FROM profil_desa')->fetchColumn();
        $this->assertSame(1, (int) $row);
    }

    public function testGetProfilMengembalikanDataYangAda(): void
    {
        $this->db()->exec("INSERT INTO profil_desa (id, nama_pekon, visi, misi, alamat_kantor) VALUES (1, 'Pekon Test', 'visi', 'misi', 'Jl. Raya')");
        $p = getProfil();
        $this->assertSame('Pekon Test', $p['nama_pekon']);
        $this->assertSame('visi', $p['visi']);
    }

    public function testUpdateProfil(): void
    {
        $this->db()->exec("INSERT INTO profil_desa (id, nama_pekon, visi, misi, alamat_kantor) VALUES (1, 'Pekon A', 'v1', 'm1', 'Alamat 1')");
        $ok = updateProfil([
            'nama_pekon' => 'Pekon B',
            'visi' => 'Visi Baru',
            'misi' => 'Misi Baru',
            'sambutan_kepala_pekon' => 'Sambutan',
            'alamat_kantor' => 'Alamat 2',
            'maps_embed_url' => 'https://maps.example',
            'telepon' => '081234',
            'email' => 'a@b.com',
            'whatsapp' => '0812345',
        ]);
        $this->assertTrue($ok);
        $p = getProfil();
        $this->assertSame('Pekon B', $p['nama_pekon']);
        $this->assertSame('Sambutan', $p['sambutan_kepala_pekon']);
        $this->assertSame('a@b.com', $p['email']);
        $this->assertSame('081234', $p['telepon']);
    }

    public function testUpdateProfilSambutanKosongMenjadiNull(): void
    {
        $this->db()->exec("INSERT INTO profil_desa (id, nama_pekon, visi, misi, alamat_kantor, sambutan_kepala_pekon) VALUES (1, 'P', 'v', 'm', 'A', 'lama')");
        updateProfil(['nama_pekon' => 'P', 'visi' => 'v', 'misi' => 'm', 'sambutan_kepala_pekon' => '', 'alamat_kantor' => 'A', 'maps_embed_url' => '', 'telepon' => '', 'email' => '', 'whatsapp' => '']);
        $this->assertNull(getProfil()['sambutan_kepala_pekon']);
    }

    public function testUpdateFotoKepalaPekon(): void
    {
        $this->db()->exec("INSERT INTO profil_desa (id, nama_pekon, visi, misi, alamat_kantor) VALUES (1, 'P', 'v', 'm', 'A')");
        updateFotoKepalaPekon('foto.jpg');
        $this->assertSame('foto.jpg', getProfil()['foto_kepala_pekon']);
    }

    public function testGetSambutanKepalaPekon(): void
    {
        $this->db()->exec("INSERT INTO profil_desa (id, nama_pekon, visi, misi, alamat_kantor) VALUES (1, 'P', 'v', 'm', 'A')");
        $this->assertNull(getSambutanKepalaPekon());

        $this->db()->exec("UPDATE profil_desa SET sambutan_kepala_pekon = 'Selamat datang' WHERE id = 1");
        $s = getSambutanKepalaPekon();
        $this->assertNotNull($s);
        $this->assertSame('Selamat datang', $s['sambutan_kepala_pekon']);
    }
}
