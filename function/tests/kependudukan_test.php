<?php
declare(strict_types=1);

final class KependudukanTest extends TestCase
{
    protected array $tables = ['data_kependudukan', 'kependudukan_dusun', 'dusun_master'];

    private function insertDusunMaster(string $nama, int $urutan = 0, int $aktif = 1): int
    {
        $this->db()->prepare('INSERT INTO dusun_master (nama, urutan, aktif) VALUES (?, ?, ?)')->execute([$nama, $urutan, $aktif]);
        return (int) $this->db()->lastInsertId();
    }

    private function kd(array $o = []): int
    {
        return saveKependudukan(array_merge([
            'periode' => '2026-01', 'jumlah_kk' => 0, 'jumlah_jiwa' => 0,
            'jumlah_laki' => 0, 'jumlah_perempuan' => 0, 'keterangan' => '',
        ], $o));
    }

    public function testSaveDanGetDataKependudukan(): void
    {
        $id = $this->kd(['jumlah_kk' => 100, 'jumlah_jiwa' => 300, 'jumlah_laki' => 150, 'jumlah_perempuan' => 150]);
        $this->assertGreaterThan(0, $id);
        $d = getDataKependudukanById((int) $id);
        $this->assertNotNull($d);
        $this->assertSame('2026-01', $d['periode']);
        $this->assertSame(100, (int) $d['jumlah_kk']);
    }

    public function testGetDataKependudukanTerbaruDanOrdering(): void
    {
        $this->kd(['periode' => '2025-01', 'jumlah_kk' => 1, 'jumlah_jiwa' => 2]);
        $this->kd(['periode' => '2026-01', 'jumlah_kk' => 3, 'jumlah_jiwa' => 4]);
        $this->assertSame('2026-01', getDataKependudukanTerbaru()['periode']);
        $list = getDataKependudukan();
        $this->assertSame(['2026-01', '2025-01'], array_column($list, 'periode'));
        $this->assertCount(1, getDataKependudukan(1));
    }

    public function testUpdateDanDeleteKependudukan(): void
    {
        $id = $this->kd(['jumlah_kk' => 10, 'jumlah_jiwa' => 20]);
        $this->assertTrue(updateKependudukan((int) $id, ['periode' => '2026-01', 'jumlah_kk' => 11, 'jumlah_jiwa' => 22, 'keterangan' => 'baru']));
        $this->assertSame(11, (int) getDataKependudukanById((int) $id)['jumlah_kk']);
        $this->assertTrue(deleteKependudukan((int) $id));
        $this->assertNull(getDataKependudukanById((int) $id));
    }

    public function testSaveDusunKependudukanInsertDanUpsert(): void
    {
        $id = saveDusunKependudukan(['periode' => '2026-01', 'nama_dusun' => 'Dusun A', 'jumlah_laki' => 5, 'jumlah_perempuan' => 5, 'jumlah_kk' => 10, 'jumlah_jiwa' => 20]);
        $this->assertGreaterThan(0, $id);

        // Upsert: periode + nama_dusun sama -> update, bukan insert baru
        $id2 = saveDusunKependudukan(['periode' => '2026-01', 'nama_dusun' => 'Dusun A', 'jumlah_laki' => 6, 'jumlah_perempuan' => 6, 'jumlah_kk' => 12, 'jumlah_jiwa' => 24]);
        $this->assertSame((int) $id, (int) $id2);
        $this->assertCount(1, getDusunByPeriode('2026-01'));
        $this->assertSame(12, (int) getDusunByPeriode('2026-01')[0]['jumlah_kk']);
    }

    public function testGetKependudukanDusunJoinDusunAktif(): void
    {
        $this->insertDusunMaster('Dusun A');
        $this->insertDusunMaster('Dusun B');
        $this->insertDusunMaster('Dusun Nonaktif', 0, 0);
        $this->kd();
        saveDusunKependudukan(['periode' => '2026-01', 'nama_dusun' => 'Dusun A', 'jumlah_laki' => 1, 'jumlah_perempuan' => 1, 'jumlah_kk' => 1, 'jumlah_jiwa' => 2]);
        saveDusunKependudukan(['periode' => '2026-01', 'nama_dusun' => 'Dusun B', 'jumlah_laki' => 1, 'jumlah_perempuan' => 1, 'jumlah_kk' => 1, 'jumlah_jiwa' => 2]);
        saveDusunKependudukan(['periode' => '2026-01', 'nama_dusun' => 'Dusun Nonaktif', 'jumlah_laki' => 1, 'jumlah_perempuan' => 1, 'jumlah_kk' => 1, 'jumlah_jiwa' => 2]);

        $rows = getKependudukanDusun('2026-01');
        // hanya dusun aktif (A & B) yang muncul, Nonaktif tidak
        $names = array_column($rows, 'nama_dusun');
        $this->assertContains('Dusun A', $names);
        $this->assertContains('Dusun B', $names);
        $this->assertNotContains('Dusun Nonaktif', $names);
        $this->assertSame(2, getJumlahDusunTerbaru());
    }

    public function testGetTrenKependudukan(): void
    {
        $this->kd(['periode' => '2025-01', 'jumlah_kk' => 10, 'jumlah_jiwa' => 20]);
        $this->kd(['periode' => '2026-01', 'jumlah_kk' => 30, 'jumlah_jiwa' => 40]);
        $tren = getTrenKependudukan();
        $this->assertSame(['2025-01', '2026-01'], $tren['periode']);
        $this->assertSame([10, 30], $tren['jumlah_kk']);
        $this->assertSame([20, 40], $tren['jumlah_jiwa']);
    }

    public function testDeleteDusunMasterMenghapusDanReaggregasi(): void
    {
        $dusunA = $this->insertDusunMaster('Dusun A');
        $this->insertDusunMaster('Dusun B');

        saveKependudukan(['periode' => '2026-01', 'jumlah_kk' => 15, 'jumlah_jiwa' => 30, 'jumlah_laki' => 15, 'jumlah_perempuan' => 15, 'keterangan' => '']);
        saveDusunKependudukan(['periode' => '2026-01', 'nama_dusun' => 'Dusun A', 'jumlah_laki' => 5, 'jumlah_perempuan' => 5, 'jumlah_kk' => 10, 'jumlah_jiwa' => 20]);
        saveDusunKependudukan(['periode' => '2026-01', 'nama_dusun' => 'Dusun B', 'jumlah_laki' => 5, 'jumlah_perempuan' => 5, 'jumlah_kk' => 5, 'jumlah_jiwa' => 10]);

        $result = deleteDusunMaster($dusunA);
        $this->assertSame('Dusun A', $result);

        // dusun A sudah hilang dari master & kependudukan_dusun
        $this->assertFalse($this->db()->query("SELECT id FROM dusun_master WHERE id = $dusunA")->fetchColumn());
        $this->assertCount(1, getDusunByPeriode('2026-01'));

        // agregat data_kependudukan dihitung ulang dari sisa dusun (B)
        $d = getDataKependudukanById((int) $this->db()->query("SELECT id FROM data_kependudukan WHERE periode='2026-01'")->fetchColumn());
        $this->assertSame(5, (int) $d['jumlah_kk']);
        $this->assertSame(10, (int) $d['jumlah_jiwa']);
        $this->assertSame(5, (int) $d['jumlah_laki']);
        $this->assertSame(5, (int) $d['jumlah_perempuan']);
    }

    public function testDeleteDusunMasterIdTidakAda(): void
    {
        $this->assertNull(deleteDusunMaster(9999));
    }
}
