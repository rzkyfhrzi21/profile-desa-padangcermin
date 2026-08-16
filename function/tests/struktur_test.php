<?php
declare(strict_types=1);

final class StrukturTest extends TestCase
{
    protected array $tables = ['struktur_organisasi'];

    private function st(array $o = []): int
    {
        return saveStruktur(array_merge([
            'parent_id' => '', 'nama' => 'X', 'jabatan' => 'J',
            'pendidikan_terakhir' => '', 'foto' => null, 'urutan' => 0,
        ], $o));
    }

    public function testSaveDanGetStrukturById(): void
    {
        $id = $this->st(['nama' => 'Kepala Pekon', 'jabatan' => 'Kepala Pekon', 'urutan' => 1]);
        $this->assertGreaterThan(0, $id);
        $s = getStrukturById((int) $id);
        $this->assertNotNull($s);
        $this->assertNull($s['parent_id']);
        $this->assertSame('Kepala Pekon', $s['jabatan']);
    }

    public function testSaveStrukturDenganParent(): void
    {
        $parent = $this->st(['nama' => 'Kepala', 'jabatan' => 'Kepala Pekon', 'urutan' => 1]);
        $child = $this->st(['parent_id' => (string) $parent, 'nama' => 'Sekretaris', 'jabatan' => 'Sekretaris', 'urutan' => 2]);
        $this->assertSame((int) $parent, (int) getStrukturById((int) $child)['parent_id']);
    }

    public function testUpdateStruktur(): void
    {
        $id = $this->st(['nama' => 'A', 'jabatan' => 'J', 'urutan' => 1]);
        $this->assertTrue(updateStruktur((int) $id, [
            'parent_id' => '', 'nama' => 'B', 'jabatan' => 'Kadus', 'pendidikan_terakhir' => 'S1', 'foto' => null, 'urutan' => 5,
        ]));
        $s = getStrukturById((int) $id);
        $this->assertSame('B', $s['nama']);
        $this->assertSame('S1', $s['pendidikan_terakhir']);
        $this->assertSame(5, (int) $s['urutan']);
    }

    public function testBuildStrukturTreeMembangunHierarki(): void
    {
        $root = $this->st(['nama' => 'Kepala', 'jabatan' => 'Kepala Pekon', 'urutan' => 1]);
        $c1 = $this->st(['parent_id' => (string) $root, 'nama' => 'Sekretaris', 'jabatan' => 'Sekretaris', 'urutan' => 2]);
        $c2 = $this->st(['parent_id' => (string) $root, 'nama' => 'Bendahara', 'jabatan' => 'Bendahara', 'urutan' => 3]);

        $tree = buildStrukturTree(getStrukturAll());
        $this->assertCount(1, $tree); // satu root
        $this->assertCount(2, $tree[0]['children']);

        // children tersusun urutan naik
        $names = array_column($tree[0]['children'], 'nama');
        $this->assertSame(['Sekretaris', 'Bendahara'], $names);
    }

    public function testGetStrukturTreeDanAllTerurut(): void
    {
        $this->st(['nama' => 'Z', 'jabatan' => 'J', 'urutan' => 9]);
        $this->st(['nama' => 'A', 'jabatan' => 'J', 'urutan' => 1]);
        $all = getStrukturAll();
        $this->assertSame(['A', 'Z'], array_column($all, 'nama'));
        $this->assertCount(2, getStrukturTree());
    }

    public function testDeleteStrukturDenganAnakDitolak(): void
    {
        $parent = $this->st(['nama' => 'Kepala', 'jabatan' => 'Kepala Pekon', 'urutan' => 1]);
        $child = $this->st(['parent_id' => (string) $parent, 'nama' => 'Sekretaris', 'jabatan' => 'Sekretaris', 'urutan' => 2]);

        $this->assertFalse(deleteStruktur((int) $parent)); // punya anak -> ditolak
        $this->assertNotNull(getStrukturById((int) $parent));

        $this->assertTrue(deleteStruktur((int) $child)); // leaf -> boleh
        $this->assertNull(getStrukturById((int) $child));
    }

    public function testStrukturOptionsMengecualikanDiriSendiri(): void
    {
        $a = $this->st(['nama' => 'A', 'jabatan' => 'J1', 'urutan' => 1]);
        $b = $this->st(['nama' => 'B', 'jabatan' => 'J2', 'urutan' => 2]);

        $options = strukturOptions(getStrukturAll(), (int) $a);
        $this->assertArrayNotHasKey((string) $a, $options);
        $this->assertArrayHasKey((string) $b, $options);
        $this->assertSame('B — J2', $options[(string) $b]);

        $all = strukturOptions(getStrukturAll());
        $this->assertCount(2, $all);
    }
}
