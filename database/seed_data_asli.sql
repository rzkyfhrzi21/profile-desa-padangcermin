-- Seed data asli Desa Padang Cermin (sumber: docs/drive)
-- Jalankan sekali: mysql -u root -P 3309 --default-character-set=utf8mb4 padang_cermin_db -e "source database/seed_data_asli.sql"
-- Menghapus seluruh data dummy sebelumnya (kecuali admins & profil_desa).

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE wisata_gambar;
TRUNCATE TABLE wisata_desa;
TRUNCATE TABLE berita_desa;
TRUNCATE TABLE berita_kategori;
TRUNCATE TABLE potensi_desa;
TRUNCATE TABLE kependudukan_dusun;
TRUNCATE TABLE data_kependudukan;
TRUNCATE TABLE struktur_organisasi;
SET FOREIGN_KEY_CHECKS = 1;

-- ============ PROFIL DESA ============
UPDATE profil_desa
SET nama_pekon = 'Desa Padang Cermin'
WHERE id = 1;

-- ============ STRUKTUR ORGANISASI (isi minimal, bagan asli belum tersedia) ============
INSERT INTO struktur_organisasi (id, parent_id, nama, jabatan, pendidikan_terakhir, foto, urutan) VALUES
(1, NULL, 'Dra. Purwanti', 'Kepala Desa', 'S1', NULL, 1);

-- ============ KEPENDUDUKAN (rekap April 2026, data per 01 Juli 2026) ============
INSERT INTO data_kependudukan (periode, jumlah_kk, jumlah_jiwa, jumlah_laki, jumlah_perempuan, keterangan) VALUES
('2026-04', 1926, 6886, 3425, 3461, 'Rekap April 2026 (per 01 Juli 2026)');

INSERT INTO kependudukan_dusun (periode, nama_dusun, jumlah_laki, jumlah_perempuan, jumlah_kk, jumlah_jiwa) VALUES
('2026-04', 'Padang Cermin', 276, 266, 170, 542),
('2026-04', 'Tanjung Mas', 268, 243, 152, 511),
('2026-04', 'Rawa Subur', 347, 350, 217, 697),
('2026-04', 'Rawa Tunggal', 495, 518, 311, 1013),
('2026-04', 'Kejadian', 310, 278, 169, 588),
('2026-04', 'Kecapi', 479, 463, 284, 942),
('2026-04', 'Bambu Kuning', 331, 439, 182, 770),
('2026-04', 'Lubuk Bakak', 919, 904, 441, 1823);

-- ============ POTENSI DESA ============
INSERT INTO potensi_desa (judul, deskripsi, gambar, kategori, urutan, status) VALUES
('Sentra Produksi Pisang', 'Desa Padang Cermin menjadi sentra produksi pisang, terutama pisang ambon, dengan luas lahan produksi sekitar 152 hektare. Pisang menjadi mata pencaharian dan komoditas utama sebagian besar masyarakat.', NULL, 'Pertanian', 1, 'aktif'),
('Pertanian & Perkebunan', 'Desa terluas di Kecamatan Padang Cermin dengan luas areal mencapai sekitar 3.278 hektare. Komoditas utama meliputi pertanian padi serta perkebunan kopi dan lada.', NULL, 'Pertanian', 2, 'aktif'),
('Jalur Strategis Pariwisata', 'Letak Desa Padang Cermin berfungsi sebagai jalur akses penting dari Kota Bandar Lampung menuju kawasan wisata bahari di Pesawaran dan sekitarnya.', NULL, 'Infrastruktur', 3, 'aktif'),
('Agrowisata Air Terjun', 'Kawasan agrowisata air terjun yang terletak di Dusun Lubuk Bakak RT 005 Desa Padang Cermin. Saat ini sangat banyak dikunjungi wisatawan, baik lokal, luar daerah, maupun dari luar pulau.', NULL, 'Pariwisata', 4, 'aktif');

-- ============ WISATA ============
INSERT INTO wisata_desa (id, nama, slug, deskripsi, alamat, latitude, longitude, harga_tiket, jam_buka, status) VALUES
(1, 'Air Terjun Penyarian', 'air-terjun-penyarian', 'Air terjun yang terletak di Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin. Destinasi ini dibuka pada tahun 2026 dan dikelola oleh Gapoktan setempat. Kini menjadi salah satu tempat wisata alam yang sangat ramai dikunjungi, baik oleh wisatawan lokal, luar kabupaten/kota, maupun dari luar provinsi.', 'Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin', NULL, NULL, NULL, NULL, 'publish'),
(2, 'Air Terjun Tanah Longsor', 'air-terjun-tanah-longsor', 'Salah satu dari dua lokasi wisata alam air terjun di Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin. Bersama Air Terjun Penyarian, kawasan ini menjadi destinasi wisata alam yang menarik minat banyak wisatawan.', 'Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin', NULL, NULL, NULL, NULL, 'publish');

INSERT INTO wisata_gambar (wisata_id, path_gambar, alt_text, urutan) VALUES
(1, 'wisata/air-terjun-penyarian-1.jpg', 'Air Terjun Penyarian di Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin', 1),
(1, 'wisata/air-terjun-penyarian-2.jpg', 'Aliran air Air Terjun Penyarian, Desa Padang Cermin', 2),
(2, 'wisata/air-terjun-tanah-longsor-1.jpg', 'Air Terjun Tanah Longsor di Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin', 1);

-- ============ BERITA ============
INSERT INTO berita_kategori (id, nama, slug) VALUES
(1, 'Pemerintahan', 'pemerintahan'),
(2, 'Pembangunan', 'pembangunan'),
(3, 'Kesehatan', 'kesehatan'),
(4, 'Pariwisata', 'pariwisata'),
(5, 'Pertanian', 'pertanian');

INSERT INTO berita_desa (judul, slug, kategori_id, konten, gambar_utama, alt_gambar, penulis_id, status, views, published_at) VALUES
('Musyawarah Desa Penyusunan RKPDes dan APBDes Tahun Anggaran 2027',
 'musyawarah-desa-penyusunan-rkpdes-dan-apbdes-tahun-anggaran-2027',
 1,
 'Pemerintah Desa Padang Cermin mengadakan kegiatan Musyawarah Desa Penyusunan RKPDes dan APBDes tahun anggaran 2027 yang dilaksanakan pada 8 Juni 2026, diselenggarakan oleh BPD Desa Padang Cermin.

Kegiatan ini dihadiri langsung oleh Bapak Camat yang memberikan arahan terkait penyusunan RKPDes dan APBDes serta realisasinya. Berbagai musyawarah serta tanya jawab terkait kegiatan dan pengajuan pembangunan di wilayah Desa Padang Cermin berlangsung dengan tertib dan khidmat.

Kegiatan ini dilaksanakan guna menentukan arah perencanaan, baik secara pembangunan maupun kegiatan lainnya, demi kemajuan Desa Padang Cermin.',
 'berita/musdes-rkpdes-apbdes-2027.jpg',
 'Musyawarah Desa penyusunan RKPDes dan APBDes tahun anggaran 2027',
 1, 'publish', 0, '2026-06-08 09:00:00'),
('Rembug Stunting dalam Rangka Pencegahan dan Penurunan Angka Stunting Desa Padang Cermin',
 'rembug-stunting-dalam-rangka-pencegahan-dan-penurunan-angka-stunting-desa-padang-cermin',
 3,
 'Kegiatan rembug stunting ini rutin dilakukan setiap tahun guna meminimalisir angka stunting bagi anak di Desa Padang Cermin, Kecamatan Padang Cermin. Beberapa stakeholder turut berpartisipasi dalam kegiatan tersebut, seperti Camat Padang Cermin, Kepala Puskesmas Padang Cermin, bidan desa, serta kader-kader yang bersinggungan langsung dengan kegiatan posyandu desa.

Hal ini dilakukan guna mengetahui sejauh mana angka penurunan stunting di Desa Padang Cermin, demi menciptakan generasi emas menuju Indonesia Emas tahun 2045 sejak dini.',
 'berita/rembug-stunting-2026.jpg',
 'Rembug stunting dalam rangka pencegahan dan penurunan angka stunting Desa Padang Cermin',
 1, 'publish', 0, '2026-08-01 09:00:00');
