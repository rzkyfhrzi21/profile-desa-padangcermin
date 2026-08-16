<?php
declare(strict_types=1);

final class WisataTest extends TestCase
{
    protected array $tables = ['wisata_gambar', 'wisata_fasilitas', 'wisata_desa'];

    private function wisataData(array $o = []): array
    {
        return array_merge([
            'nama' => 'W', 'slug' => 'w', 'deskripsi' => 'd', 'alamat' => 'a',
            'maps_embed_url' => '', 'harga_tiket' => '', 'jam_buka' => '', 'wa_kontak' => '',
            'status' => 'draft',
        ], $o);
    }

    private function saveW(array $o = []): int
    {
        return saveWisata($this->wisataData($o));
    }

    public function testSaveDanGetWisataById(): void
    {
        $id = $this->saveW(['nama' => 'Curug Embun', 'slug' => 'curug-embun', 'deskripsi' => 'Air terjun indah', 'alamat' => 'Dusun Embun']);
        $this->assertGreaterThan(0, $id);

        $w = getWisataById((int) $id);
        $this->assertNotNull($w);
        $this->assertSame('Curug Embun', $w['nama']);
        $this->assertSame('draft', $w['status']);
        $this->assertNull($w['maps_embed_url']);
        $this->assertNull($w['harga_tiket']);
    }

    public function testSlugUnique(): void
    {
        $this->saveW(['slug' => 'satu']);
        $this->assertTrue(slugExistsWisata('satu'));
        $id = $this->saveW(['slug' => 'dua']);
        // 'dua' milik baris itu sendiri (dikecualikan) -> tidak terhitung sebagai duplikat
        $this->assertFalse(slugExistsWisata('dua', (int) $id));
        // 'satu' milik baris lain -> terdeteksi sebagai duplikat
        $this->assertTrue(slugExistsWisata('satu', (int) $id));
    }

    public function testUpdateWisata(): void
    {
        $id = $this->saveW();
        $ok = updateWisata((int) $id, $this->wisataData(['nama' => 'B', 'slug' => 'b', 'deskripsi' => 'd2', 'alamat' => 'al2', 'status' => 'publish']));
        $this->assertTrue($ok);
        $w = getWisataById((int) $id);
        $this->assertSame('B', $w['nama']);
        $this->assertSame('publish', $w['status']);
    }

    public function testGetWisataListFilterPublish(): void
    {
        $this->saveW(['nama' => 'P', 'slug' => 'p', 'status' => 'publish']);
        $this->saveW(['nama' => 'D', 'slug' => 'd', 'status' => 'draft']);
        $this->assertCount(2, getWisataList());
        $this->assertCount(1, getWisataList(true));
        $this->assertSame('P', getWisataList(true)[0]['nama']);
    }

    public function testGetWisataBySlugHanyaPublish(): void
    {
        $this->saveW(['slug' => 'a', 'status' => 'draft']);
        $this->assertNull(getWisataBySlug('a'));
        $this->saveW(['slug' => 'b', 'status' => 'publish']);
        $this->assertNotNull(getWisataBySlug('b'));
        $this->assertNull(getWisataBySlug('tidak-ada'));
    }

    public function testGetWisataWithGambarBatchSatuQuery(): void
    {
        $id1 = $this->saveW(['slug' => 'w1']);
        $id2 = $this->saveW(['slug' => 'w2']);
        addWisataImage((int) $id1, 'gambar-a.jpg', 1);
        addWisataImage((int) $id1, 'gambar-b.jpg', 2);
        addWisataImage((int) $id2, 'gambar-c.jpg', 1);

        $rows = getWisataWithGambar(getWisataList());
        $this->assertCount(2, $rows);
        foreach ($rows as $r) {
            $this->assertArrayHasKey('gambar', $r);
        }
        $byId = [];
        foreach ($rows as $r) {
            $byId[$r['id']] = $r['gambar'];
        }
        $this->assertCount(2, $byId[$id1]);
        $this->assertCount(1, $byId[$id2]);
    }

    public function testGetWisataWithGambarKosong(): void
    {
        $this->assertSame([], getWisataWithGambar([]));
    }

    public function testAddGetDeleteWisataImage(): void
    {
        $id = $this->saveW();
        addWisataImage((int) $id, 'foto1.jpg', 0);
        addWisataImage((int) $id, 'foto2.jpg', 1);

        $imgs = getWisataImages((int) $id);
        $this->assertCount(2, $imgs);

        $path = deleteWisataImage((int) $id, (int) $imgs[0]['id']);
        $this->assertSame('foto1.jpg', $path);
        $this->assertCount(1, getWisataImages((int) $id));

        $this->assertNull(deleteWisataImage((int) $id, 9999));
    }

    public function testFasilitasCRUD(): void
    {
        $id = $this->saveW();
        $f1 = saveWisataFasilitas((int) $id, 'eco', 'Taman', 'Taman asri', 1);
        $f2 = saveWisataFasilitas((int) $id, 'local_cafe', 'Kafe', 'Kafe lokal', 2);

        $this->assertCount(2, getWisataFasilitas((int) $id));
        $this->assertSame('Taman', getWisataFasilitasById((int) $f1)['judul']);

        $this->assertTrue(updateWisataFasilitas((int) $id, (int) $f1, 'eco', 'Taman Indah', 'Taman asri', 3));
        $this->assertSame('Taman Indah', getWisataFasilitasById((int) $f1)['judul']);

        $this->assertTrue(deleteWisataFasilitas((int) $id, (int) $f2));
        $this->assertCount(1, getWisataFasilitas((int) $id));
        // id yang tidak ada: execute sukses (tidak error), tidak ada baris terpengaruh
        $this->assertTrue(deleteWisataFasilitas((int) $id, 9999));
        $this->assertCount(1, getWisataFasilitas((int) $id));
    }

    public function testDeleteWisataMenghapusDataRelasi(): void
    {
        $id = $this->saveW();
        addWisataImage((int) $id, 'foto.jpg', 0);
        saveWisataFasilitas((int) $id, 'eco', 'Taman', 'Taman asri', 1);

        $this->assertTrue(deleteWisata((int) $id));
        $this->assertNull(getWisataById((int) $id));
        $this->assertCount(0, getWisataImages((int) $id));
        $this->assertCount(0, getWisataFasilitas((int) $id));
    }
}
