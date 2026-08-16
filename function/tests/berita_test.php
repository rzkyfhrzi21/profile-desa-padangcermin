<?php
declare(strict_types=1);

final class BeritaTest extends TestCase
{
    protected array $tables = ['berita_desa', 'berita_kategori', 'admins'];

    public function setUp(): void
    {
        parent::setUp();
        $this->db()->exec(
            "INSERT INTO admins (id, username, password_hash, nama)
             VALUES (1, 'testadmin', 'x', 'Test Admin')"
        );
    }

    public function testKategoriCRUD(): void
    {
        $id = saveBeritaKategori('Agenda Desa');
        $this->assertGreaterThan(0, $id);

        $k = $this->db()->query("SELECT * FROM berita_kategori WHERE id = $id")->fetch();
        $this->assertSame('agenda-desa', $k['slug']);
        $this->assertCount(1, getBeritaKategoriList());

        $this->assertTrue(deleteBeritaKategori((int) $id));
        $this->assertCount(0, getBeritaKategoriList());
    }

    public function testSaveBeritaDraftTidakSetPublishedAt(): void
    {
        $id = saveBerita([
            'judul' => 'Berita Draft',
            'slug' => 'berita-draft',
            'kategori_id' => '',
            'konten' => 'Isi berita',
            'status' => 'draft',
        ]);
        $b = getBeritaById((int) $id);
        $this->assertNotNull($b);
        $this->assertSame('draft', $b['status']);
        $this->assertNull($b['published_at']);
        $this->assertSame(1, (int) $b['penulis_id']);
    }

    public function testSaveBeritaPublishSetPublishedAt(): void
    {
        $id = saveBerita([
            'judul' => 'Berita Publish',
            'slug' => 'berita-publish',
            'kategori_id' => '',
            'konten' => 'Isi',
            'status' => 'publish',
        ]);
        $b = getBeritaById((int) $id);
        $this->assertNotNull($b['published_at']);
        $this->assertSame('publish', $b['status']);
    }

    public function testSlugUniqueBerita(): void
    {
        saveBerita(['judul' => 'A', 'slug' => 'satu', 'kategori_id' => '', 'konten' => 'x', 'status' => 'draft']);
        $this->assertTrue(slugExistsBerita('satu'));
        $id = saveBerita(['judul' => 'B', 'slug' => 'dua', 'kategori_id' => '', 'konten' => 'x', 'status' => 'draft']);
        // 'dua' milik baris itu sendiri (dikecualikan) -> bukan duplikat
        $this->assertFalse(slugExistsBerita('dua', (int) $id));
        // 'satu' milik baris lain -> duplikat
        $this->assertTrue(slugExistsBerita('satu', (int) $id));
    }

    public function testUpdateBerita(): void
    {
        $id = saveBerita(['judul' => 'A', 'slug' => 'a', 'kategori_id' => '', 'konten' => 'x', 'status' => 'draft']);
        $this->assertTrue(updateBerita((int) $id, [
            'judul' => 'B', 'slug' => 'b', 'kategori_id' => '', 'konten' => 'y', 'status' => 'publish',
        ]));
        $b = getBeritaById((int) $id);
        $this->assertSame('B', $b['judul']);
        $this->assertSame('publish', $b['status']);
    }

    public function testGetBeritaBySlugHanyaPublish(): void
    {
        saveBerita(['judul' => 'D', 'slug' => 'd', 'kategori_id' => '', 'konten' => 'x', 'status' => 'draft']);
        $this->assertNull(getBeritaBySlug('d'));
        saveBerita(['judul' => 'P', 'slug' => 'p', 'kategori_id' => '', 'konten' => 'x', 'status' => 'publish']);
        $this->assertNotNull(getBeritaBySlug('p'));
        $this->assertNull(getBeritaBySlug('tidak-ada'));
    }

    public function testGetBeritaListPaginationDanFilterPublish(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $status = $i % 2 === 0 ? 'publish' : 'draft';
            saveBerita(['judul' => "B$i", 'slug' => "b$i", 'kategori_id' => '', 'konten' => 'x', 'status' => $status]);
        }
        $this->assertCount(5, getBeritaList());
        $this->assertCount(2, getBeritaList(true));
        $this->assertCount(2, getBeritaList(false, 2, 1));
        $this->assertCount(2, getBeritaList(false, 2, 2));
        $this->assertCount(1, getBeritaList(false, 2, 3));
    }

    public function testCountBerita(): void
    {
        saveBerita(['judul' => 'A', 'slug' => 'a', 'kategori_id' => '', 'konten' => 'x', 'status' => 'draft']);
        saveBerita(['judul' => 'B', 'slug' => 'b', 'kategori_id' => '', 'konten' => 'x', 'status' => 'publish']);
        $this->assertSame(2, countBerita());
        $this->assertSame(1, countBerita(true));
    }

    public function testTambahViewBerita(): void
    {
        $id = saveBerita(['judul' => 'A', 'slug' => 'a', 'kategori_id' => '', 'konten' => 'x', 'status' => 'draft']);
        tambahViewBerita((int) $id);
        tambahViewBerita((int) $id);
        $b = getBeritaById((int) $id);
        $this->assertSame(2, (int) $b['views']);
    }

    public function testGetBeritaListAdminFilter(): void
    {
        $k = saveBeritaKategori('Wisata');
        saveBerita(['judul' => 'Berita Wisata Satu', 'slug' => 'b1', 'kategori_id' => (string) $k, 'konten' => 'isi', 'status' => 'publish']);
        saveBerita(['judul' => 'Lainnya', 'slug' => 'b2', 'kategori_id' => '', 'konten' => 'isi', 'status' => 'draft']);

        $this->assertCount(2, getBeritaListAdmin(10));
        $this->assertCount(1, getBeritaListAdmin(10, 1, 'Wisata'));
        $this->assertCount(1, getBeritaListAdmin(10, 1, '', (int) $k));
        $this->assertCount(1, getBeritaListAdmin(10, 1, '', null, 'draft'));
        $this->assertSame(2, countBeritaAdmin());
        $this->assertSame(1, countBeritaAdmin('Wisata'));
        $this->assertSame(1, countBeritaAdmin('', (int) $k));
        $this->assertSame(1, countBeritaAdmin('', null, 'draft'));
    }

    public function testGetViewsPerKategori(): void
    {
        $k = saveBeritaKategori('Pertanian');
        $id = saveBerita(['judul' => 'B', 'slug' => 'b', 'kategori_id' => (string) $k, 'konten' => 'x', 'status' => 'publish']);
        tambahViewBerita((int) $id);
        tambahViewBerita((int) $id);
        $views = getViewsPerKategori();
        $this->assertNotEmpty($views);
        $found = null;
        foreach ($views as $v) {
            if ($v['nama'] === 'Pertanian') {
                $found = $v;
            }
        }
        $this->assertNotNull($found);
        $this->assertSame(2, (int) $found['total_views']);
    }

    public function testGetStatistikBerita(): void
    {
        saveBerita(['judul' => 'A', 'slug' => 'a', 'kategori_id' => '', 'konten' => 'x', 'status' => 'draft']);
        saveBerita(['judul' => 'B', 'slug' => 'b', 'kategori_id' => '', 'konten' => 'x', 'status' => 'publish']);
        $stat = getStatistikBerita();
        $this->assertSame(2, $stat['total']);
        $this->assertSame(1, $stat['published']);
        $this->assertSame(1, $stat['draft']);
    }

    public function testDeleteBerita(): void
    {
        $id = saveBerita(['judul' => 'A', 'slug' => 'a', 'kategori_id' => '', 'konten' => 'x', 'status' => 'draft']);
        $this->assertTrue(deleteBerita((int) $id));
        $this->assertNull(getBeritaById((int) $id));
    }
}
