<?php
declare(strict_types=1);

final class PotensiTest extends TestCase
{
    protected array $tables = ['potensi_desa'];

    public function testSaveDanGetPotensiById(): void
    {
        $id = savePotensi([
            'judul' => 'Sawah Organik',
            'deskripsi' => 'Lahan sawah',
            'kategori' => 'Pertanian',
            'urutan' => 1,
        ]);
        $this->assertGreaterThan(0, $id);

        $p = getPotensiById((int) $id);
        $this->assertNotNull($p);
        $this->assertSame('Sawah Organik', $p['judul']);
        $this->assertSame('Pertanian', $p['kategori']);
        $this->assertSame(1, (int) $p['urutan']);
        $this->assertSame('aktif', $p['status']);
        $this->assertNull($p['ikon']);
    }

    public function testSavePotensiIkonDanKategoriKosong(): void
    {
        $id = savePotensi([
            'judul' => 'Kolam Ikan',
            'deskripsi' => 'Budidaya',
            'kategori' => '',
            'ikon' => 'waves',
            'status' => 'nonaktif',
        ]);
        $p = getPotensiById((int) $id);
        $this->assertNull($p['kategori']);
        $this->assertSame('waves', $p['ikon']);
        $this->assertSame('nonaktif', $p['status']);
    }

    public function testUpdatePotensi(): void
    {
        $id = savePotensi(['judul' => 'A', 'deskripsi' => 'd', 'kategori' => 'UMKM', 'urutan' => 1]);
        $this->assertTrue(updatePotensi((int) $id, [
            'judul' => 'B', 'deskripsi' => 'd2', 'kategori' => 'Kerajinan', 'urutan' => 3, 'status' => 'nonaktif',
        ]));
        $p = getPotensiById((int) $id);
        $this->assertSame('B', $p['judul']);
        $this->assertSame('Kerajinan', $p['kategori']);
        $this->assertSame(3, (int) $p['urutan']);
    }

    public function testGetPotensiListFilterAktif(): void
    {
        savePotensi(['judul' => 'A', 'deskripsi' => 'd', 'kategori' => 'Pertanian', 'status' => 'aktif']);
        savePotensi(['judul' => 'B', 'deskripsi' => 'd', 'kategori' => 'UMKM', 'status' => 'nonaktif']);
        $this->assertCount(2, getPotensiList());
        $this->assertCount(1, getPotensiList(true));
        $this->assertSame('A', getPotensiList(true)[0]['judul']);
    }

    public function testGetPotensiKategoriListMenggabungDefaultDanDatabase(): void
    {
        $list = getPotensiKategoriList();
        $this->assertContains('Pertanian', $list);
        $this->assertContains('UMKM', $list);

        savePotensi(['judul' => 'Kopi', 'deskripsi' => 'd', 'kategori' => 'Kopi', 'status' => 'aktif']);
        $list2 = getPotensiKategoriList();
        $this->assertContains('Kopi', $list2);

        savePotensi(['judul' => 'Kopi Lagi', 'deskripsi' => 'd', 'kategori' => 'kopi', 'status' => 'aktif']);
        $list3 = getPotensiKategoriList();
        $kopi = array_values(array_filter($list3, fn($c) => strtolower($c) === 'kopi'));
        $this->assertCount(1, $kopi);
    }

    public function testDeletePotensi(): void
    {
        $id = savePotensi(['judul' => 'A', 'deskripsi' => 'd', 'kategori' => 'Pertanian']);
        $this->assertTrue(deletePotensi((int) $id));
        $this->assertNull(getPotensiById((int) $id));
    }
}
