-- ============================================================
-- Dump database padang_cermin_db (di-generate 2026-08-09 19:25:47)
-- Siap diimpor ke database online (menimpa schema + data).
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Struktur tabel `admins`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `admins`: 1 baris
INSERT INTO `admins` (`id`,`username`,`email`,`foto`,`password_hash`,`nama`,`role`,`created_at`,`updated_at`) VALUES
(1, 'admin', NULL, 'admin/1dd58f05ae36e503.jpg', '$2y$10$x79QAtfTC82E8u4bm7RcPeme2udoI9E5I1NwPv.cB1hgi/9EuoAO2', 'Admin Pekon', 'admin', '2026-08-07 14:57:07', '2026-08-09 20:44:25');

-- ------------------------------------------------------------
-- Struktur tabel `berita_desa`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `berita_desa`;
CREATE TABLE `berita_desa` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_id` int unsigned DEFAULT NULL,
  `konten` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar_utama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penulis_id` int unsigned NOT NULL,
  `status` enum('draft','publish') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `views` int NOT NULL DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_berita_penulis` (`penulis_id`),
  KEY `idx_berita_status` (`status`,`published_at`),
  KEY `idx_berita_kategori` (`kategori_id`),
  CONSTRAINT `fk_berita_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `berita_kategori` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_berita_penulis` FOREIGN KEY (`penulis_id`) REFERENCES `admins` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `berita_desa`: 3 baris
INSERT INTO `berita_desa` (`id`,`judul`,`slug`,`kategori_id`,`konten`,`gambar_utama`,`penulis_id`,`status`,`views`,`published_at`,`created_at`,`updated_at`) VALUES
(1, 'Musyawarah Desa Penyusunan RKPDes dan APBDes Tahun Anggaran 2027', 'musyawarah-desa-penyusunan-rkpdes-dan-apbdes-tahun-anggaran-2027', 1, 'Pemerintah Desa Padang Cermin mengadakan kegiatan Musyawarah Desa Penyusunan RKPDes dan APBDes tahun anggaran 2027 yang dilaksanakan pada 8 Juni 2026, diselenggarakan oleh BPD Desa Padang Cermin.\n\nKegiatan ini dihadiri langsung oleh Bapak Camat yang memberikan arahan terkait penyusunan RKPDes dan APBDes serta realisasinya. Berbagai musyawarah serta tanya jawab terkait kegiatan dan pengajuan pembangunan di wilayah Desa Padang Cermin berlangsung dengan tertib dan khidmat.\n\nKegiatan ini dilaksanakan guna menentukan arah perencanaan, baik secara pembangunan maupun kegiatan lainnya, demi kemajuan Desa Padang Cermin.', 'berita/musdes-rkpdes-apbdes-2027.jpg', 1, 'publish', 8, '2026-06-08 09:00:00', '2026-08-08 15:07:04', '2026-08-10 02:11:57'),
(2, 'Rembug Stunting dalam Rangka Pencegahan dan Penurunan Angka Stunting Desa Padang Cermin', 'rembug-stunting-dalam-rangka-pencegahan-dan-penurunan-angka-stunting-desa-padang-cermin', 3, 'Kegiatan rembug stunting ini rutin dilakukan setiap tahun guna meminimalisir angka stunting bagi anak di Desa Padang Cermin, Kecamatan Padang Cermin. Beberapa stakeholder turut berpartisipasi dalam kegiatan tersebut, seperti Camat Padang Cermin, Kepala Puskesmas Padang Cermin, bidan desa, serta kader-kader yang bersinggungan langsung dengan kegiatan posyandu desa.\n\nHal ini dilakukan guna mengetahui sejauh mana angka penurunan stunting di Desa Padang Cermin, demi menciptakan generasi emas menuju Indonesia Emas tahun 2045 sejak dini.', 'berita/rembug-stunting-2026.jpg', 1, 'publish', 2, '2026-08-01 09:00:00', '2026-08-08 15:07:04', '2026-08-10 01:54:12'),
(3, 'tesdadadadad adad', 'tesdadadadad-adad', 1, 'tes', 'berita/09630b2dc3f9279d.png', 1, 'publish', 0, '2026-08-09 08:28:47', '2026-08-09 15:28:47', '2026-08-09 15:28:47');

-- ------------------------------------------------------------
-- Struktur tabel `berita_kategori`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `berita_kategori`;
CREATE TABLE `berita_kategori` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `berita_kategori`: 6 baris
INSERT INTO `berita_kategori` (`id`,`nama`,`slug`) VALUES
(1, 'Pemerintahan', 'pemerintahan'),
(2, 'Pembangunan', 'pembangunan'),
(3, 'Kesehatan', 'kesehatan'),
(4, 'Pariwisata', 'pariwisata'),
(5, 'Pertanian', 'pertanian'),
(6, 'adad', 'adad');

-- ------------------------------------------------------------
-- Struktur tabel `data_kependudukan`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `data_kependudukan`;
CREATE TABLE `data_kependudukan` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `periode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_kk` int NOT NULL,
  `jumlah_jiwa` int NOT NULL,
  `jumlah_laki` int NOT NULL DEFAULT '0',
  `jumlah_perempuan` int NOT NULL DEFAULT '0',
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `periode` (`periode`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `data_kependudukan`: 1 baris
INSERT INTO `data_kependudukan` (`id`,`periode`,`jumlah_kk`,`jumlah_jiwa`,`jumlah_laki`,`jumlah_perempuan`,`keterangan`,`updated_at`) VALUES
(1, '2026-04', 1926, 6886, 3425, 3461, 'Rekap April 2026 (per 01 Juli 2026)', '2026-08-08 15:07:04');

-- ------------------------------------------------------------
-- Struktur tabel `dusun_master`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `dusun_master`;
CREATE TABLE `dusun_master` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` tinyint unsigned NOT NULL DEFAULT '0',
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `dusun_master`: 8 baris
INSERT INTO `dusun_master` (`id`,`nama`,`urutan`,`aktif`,`created_at`) VALUES
(1, 'Lubuk Bakak', 1, 1, '2026-08-09 19:40:46'),
(2, 'Rawa Tunggal', 2, 1, '2026-08-09 19:40:46'),
(3, 'Kecapi', 3, 1, '2026-08-09 19:40:46'),
(4, 'Bambu Kuning', 4, 1, '2026-08-09 19:40:46'),
(5, 'Rawa Subur', 5, 1, '2026-08-09 19:40:46'),
(6, 'Kejadian', 6, 1, '2026-08-09 19:40:46'),
(7, 'Padang Cermin', 7, 1, '2026-08-09 19:40:46'),
(8, 'Tanjung Mas', 8, 1, '2026-08-09 19:40:46');

-- ------------------------------------------------------------
-- Struktur tabel `inventaris`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `inventaris`;
CREATE TABLE `inventaris` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `satuan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unit',
  `kondisi` enum('baik','rusak_ringan','rusak_berat') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `lokasi_penyimpanan` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_perolehan` year DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_barang` (`kode_barang`),
  KEY `idx_inventaris_kategori` (`kategori`),
  KEY `idx_inventaris_kondisi` (`kondisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `inventaris`: (kosong)

-- ------------------------------------------------------------
-- Struktur tabel `kependudukan_dusun`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `kependudukan_dusun`;
CREATE TABLE `kependudukan_dusun` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `periode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dusun` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_laki` int NOT NULL DEFAULT '0',
  `jumlah_perempuan` int NOT NULL DEFAULT '0',
  `jumlah_kk` int NOT NULL DEFAULT '0',
  `jumlah_jiwa` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kd_periode_dusun` (`periode`,`nama_dusun`),
  KEY `idx_kd_periode` (`periode`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `kependudukan_dusun`: 9 baris
INSERT INTO `kependudukan_dusun` (`id`,`periode`,`nama_dusun`,`jumlah_laki`,`jumlah_perempuan`,`jumlah_kk`,`jumlah_jiwa`,`updated_at`) VALUES
(1, '2026-04', 'Padang Cermin', 276, 266, 170, 542, '2026-08-08 15:07:04'),
(2, '2026-04', 'Tanjung Mas', 268, 243, 152, 511, '2026-08-09 19:57:10'),
(3, '2026-04', 'Rawa Subur', 347, 350, 217, 697, '2026-08-08 15:07:04'),
(4, '2026-04', 'Rawa Tunggal', 495, 518, 311, 1013, '2026-08-09 19:57:22'),
(5, '2026-04', 'Kejadian', 310, 278, 169, 588, '2026-08-08 15:07:04'),
(6, '2026-04', 'Kecapi', 479, 463, 284, 942, '2026-08-08 15:07:04'),
(7, '2026-04', 'Bambu Kuning', 331, 439, 182, 770, '2026-08-08 15:07:04'),
(8, '2026-04', 'Lubuk Bakak', 919, 904, 441, 1823, '2026-08-09 19:56:59'),
(9, '2026-04', 'Tes', 1, 1, 11, 1, '2026-08-09 14:10:21');

-- ------------------------------------------------------------
-- Struktur tabel `log_aktivitas`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `log_aktivitas`;
CREATE TABLE `log_aktivitas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int unsigned NOT NULL,
  `aksi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabel_terkait` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_id` int unsigned DEFAULT NULL,
  `waktu` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_log_admin` (`admin_id`),
  KEY `idx_log_waktu` (`waktu`),
  CONSTRAINT `fk_log_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `log_aktivitas`: 36 baris
INSERT INTO `log_aktivitas` (`id`,`admin_id`,`aksi`,`tabel_terkait`,`data_id`,`waktu`) VALUES
(1, 1, 'login', 'admins', 1, '2026-08-07 14:57:50'),
(2, 1, 'login', 'admins', 1, '2026-08-07 14:57:57'),
(3, 1, 'login', 'admins', 1, '2026-08-07 14:58:54'),
(4, 1, 'login', 'admins', 1, '2026-08-07 14:59:28'),
(5, 1, 'login', 'admins', 1, '2026-08-07 15:08:11'),
(6, 1, 'login', 'admins', 1, '2026-08-07 15:15:26'),
(7, 1, 'login', 'admins', 1, '2026-08-07 15:15:56'),
(8, 1, 'login', 'admins', 1, '2026-08-07 15:16:20'),
(9, 1, 'LOGIN', 'auth', NULL, '2026-08-07 15:48:58'),
(10, 1, 'LOGIN', 'auth', NULL, '2026-08-07 15:49:05'),
(11, 1, 'LOGIN', 'auth', NULL, '2026-08-07 15:54:08'),
(12, 1, 'LOGIN', 'auth', NULL, '2026-08-07 16:00:46'),
(13, 1, 'LOGIN', 'auth', NULL, '2026-08-07 16:55:30'),
(14, 1, 'LOGIN', 'auth', NULL, '2026-08-07 16:55:57'),
(15, 1, 'Seed data dummy', 'seed', NULL, '2026-08-07 17:08:04'),
(16, 1, 'Aktivasi konten awal', 'seed', NULL, '2026-08-07 17:08:04'),
(17, 1, 'LOGIN', 'auth', NULL, '2026-08-07 17:09:14'),
(18, 1, 'LOGIN', 'auth', NULL, '2026-08-07 17:09:22'),
(19, 1, 'LOGIN', 'auth', NULL, '2026-08-07 17:10:21'),
(20, 1, 'LOGIN', 'auth', NULL, '2026-08-08 15:35:05'),
(21, 1, 'LOGIN', 'auth', NULL, '2026-08-08 15:43:42'),
(29, 1, 'tambah kategori berita: adad', 'berita_kategori', NULL, '2026-08-08 17:11:07'),
(30, 1, 'LOGIN', 'auth', NULL, '2026-08-09 13:23:12'),
(31, 1, 'tambah berita: tesdadadadad adad', 'berita_desa', 3, '2026-08-09 15:28:47'),
(32, 1, 'tambah struktur: tes', 'struktur_organisasi', 17, '2026-08-09 15:29:56'),
(33, 1, 'LOGIN', 'auth', NULL, '2026-08-09 19:27:19'),
(34, 1, 'tambah berita: tes', 'berita_desa', 4, '2026-08-09 19:30:18'),
(35, 1, 'update profil admin', 'admins', 1, '2026-08-09 20:16:07'),
(36, 1, 'edit potensi: Sentra Produksi Pisang', 'potensi_desa', 1, '2026-08-09 20:27:28'),
(37, 1, 'hapus berita: tes', 'berita_desa', 4, '2026-08-09 20:28:14'),
(38, 1, 'tambah wisata: dad', 'wisata_desa', 3, '2026-08-09 20:34:02'),
(39, 1, 'update profil admin', 'admins', 1, '2026-08-09 20:44:25'),
(40, 1, 'LOGIN', 'auth', NULL, '2026-08-09 22:55:46'),
(41, 1, 'LOGIN', 'auth', NULL, '2026-08-10 01:40:55'),
(42, 1, 'LOGIN', 'auth', NULL, '2026-08-10 01:46:20'),
(43, 1, 'hapus struktur: tes', 'struktur_organisasi', 17, '2026-08-10 01:46:28');

-- ------------------------------------------------------------
-- Struktur tabel `potensi_desa`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `potensi_desa`;
CREATE TABLE `potensi_desa` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategori` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ikon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `potensi_desa`: 4 baris
INSERT INTO `potensi_desa` (`id`,`judul`,`deskripsi`,`gambar`,`kategori`,`ikon`,`urutan`,`status`,`created_at`,`updated_at`) VALUES
(1, 'Sentra Produksi Pisang', 'Desa Padang Cermin menjadi sentra produksi pisang, terutama pisang ambon, dengan luas lahan produksi sekitar 152 hektare. Pisang menjadi mata pencaharian dan komoditas utama sebagian besar masyarakat.', 'potensi/dc59c9c74d9d5810.png', NULL, '', 1, 'aktif', '2026-08-08 15:07:04', '2026-08-09 20:27:28'),
(2, 'Pertanian & Perkebunan', 'Desa terluas di Kecamatan Padang Cermin dengan luas areal mencapai sekitar 3.278 hektare. Komoditas utama meliputi pertanian padi serta perkebunan kopi dan lada.', NULL, 'Pertanian', NULL, 2, 'aktif', '2026-08-08 15:07:04', '2026-08-08 15:07:04'),
(3, 'Jalur Strategis Pariwisata', 'Letak Desa Padang Cermin berfungsi sebagai jalur akses penting dari Kota Bandar Lampung menuju kawasan wisata bahari di Pesawaran dan sekitarnya.', NULL, 'Infrastruktur', NULL, 3, 'aktif', '2026-08-08 15:07:04', '2026-08-08 15:07:04'),
(4, 'Agrowisata Air Terjun', 'Kawasan agrowisata air terjun yang terletak di Dusun Lubuk Bakak RT 005 Desa Padang Cermin. Saat ini sangat banyak dikunjungi wisatawan, baik lokal, luar daerah, maupun dari luar pulau.', NULL, 'Pariwisata', NULL, 4, 'aktif', '2026-08-08 15:07:04', '2026-08-08 15:07:04');

-- ------------------------------------------------------------
-- Struktur tabel `profil_desa`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `profil_desa`;
CREATE TABLE `profil_desa` (
  `id` int unsigned NOT NULL,
  `nama_pekon` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `misi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sambutan_kepala_pekon` text COLLATE utf8mb4_unicode_ci,
  `foto_kepala_pekon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_kantor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maps_embed_url` text COLLATE utf8mb4_unicode_ci,
  `telepon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `profil_desa`: 1 baris
INSERT INTO `profil_desa` (`id`,`nama_pekon`,`visi`,`misi`,`sambutan_kepala_pekon`,`foto_kepala_pekon`,`alamat_kantor`,`maps_embed_url`,`telepon`,`email`,`whatsapp`,`updated_at`) VALUES
(1, 'Desa Padang Cermin', 'MEMBANGUN DESA PADANG CERMIN YANG BERTAQWA, DESA KUAT, DESA SEHAT, DESA MANDIRI, DESA BERKEADILAN, DESA AMAN, BERLANDASKAN PENINGKATAN EKONOMI KERAKYATAN', 'PROGRAM KEHIDUPAN SEHAT DAN SEJAHTERA\nPROGRAM PENDIDIKAN BERKUALITAS\nPROGRAM DESA TANPA KELAPARAN\nPROGRAM AIR BERSIH DAN SANITASI\nPROGRAM PEKERJAAN LAYAK DAN PERTUMBUHAN EKONOMI\nPROGRAM PENANGAN PERUBAHAN IKLIM\nPROGRAM KEAMANAN DAN KELEMBAGAAN TANGGUH', 'Assalamualaikum warahmatullahi wabarakatuh. Selamat datang di portal resmi Pekon Padang Cermin. Portal ini kami hadirkan sebagai wujud komitmen pemerintahan pekon dalam keterbukaan informasi, sehingga seluruh warga dapat mengakses data dan kegiatan pekon secara mudah dan transparan. Kami berharap kehadiran portal ini semakin mendekatkan pemerintah pekon dengan masyarakat, serta mendorong partisipasi aktif seluruh warga dalam membangun Pekon Padang Cermin yang lebih maju, sejahtera, dan berkelanjutan.', 'struktur/struktur-kades.jpg', 'Jl. Lintas Barat No. 45, Padang Cermin, Lampung', NULL, '0812-3456-7890', 'info@pekonpadangcermin.go.id', '0812-3456-7890', '2026-08-08 15:07:04');

-- ------------------------------------------------------------
-- Struktur tabel `struktur_organisasi`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `struktur_organisasi`;
CREATE TABLE `struktur_organisasi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int unsigned DEFAULT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pendidikan_terakhir` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_struktur_parent` (`parent_id`),
  CONSTRAINT `fk_struktur_parent` FOREIGN KEY (`parent_id`) REFERENCES `struktur_organisasi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `struktur_organisasi`: 16 baris
INSERT INTO `struktur_organisasi` (`id`,`parent_id`,`nama`,`jabatan`,`pendidikan_terakhir`,`foto`,`urutan`,`created_at`,`updated_at`) VALUES
(1, NULL, 'Dra. Purwanti', 'Kepala Desa', 'S1', 'struktur/struktur-kades.jpg', 1, '2026-08-08 15:07:04', '2026-08-09 13:49:31'),
(2, 1, 'Husen', 'Sekretaris Desa', NULL, 'struktur/struktur-sekdes.jpg', 0, '2026-08-09 13:36:28', '2026-08-09 13:49:31'),
(3, 2, 'Wawan Andriawan', 'Kasi Pemerintahan', NULL, 'struktur/struktur-kasi-pemerintahan.jpg', 1, '2026-08-09 13:36:28', '2026-08-09 13:49:31'),
(4, 2, 'Deden Surahman', 'Kasi Kesejahteraan', NULL, NULL, 2, '2026-08-09 13:36:28', '2026-08-09 13:36:28'),
(5, 2, 'Irma Fauziah', 'Kasi Pelayanan', NULL, NULL, 3, '2026-08-09 13:36:28', '2026-08-09 13:36:28'),
(6, 2, 'Gita Suci Veronissa', 'Kaur Tata Usaha dan Umum', NULL, NULL, 4, '2026-08-09 13:36:28', '2026-08-09 13:36:28'),
(7, 2, 'Archy Ollyvia', 'Kaur Keuangan', NULL, 'struktur/struktur-kaur-keuangan.jpg', 5, '2026-08-09 13:36:28', '2026-08-09 13:49:31'),
(8, 2, 'Indera Herlangga', 'Kaur Perencanaan', NULL, NULL, 6, '2026-08-09 13:36:28', '2026-08-09 13:36:28'),
(9, 2, 'Ady Kurnia', 'Kepala Dusun Padang Cermin', NULL, NULL, 7, '2026-08-09 13:36:28', '2026-08-09 13:36:28'),
(10, 2, 'Taufik', 'Kepala Dusun Tanjung Mas', NULL, NULL, 8, '2026-08-09 13:36:28', '2026-08-09 13:36:28'),
(11, 2, 'Mutiono', 'Kepala Dusun Rawa Subur', NULL, NULL, 9, '2026-08-09 13:36:28', '2026-08-09 13:36:28'),
(12, 2, 'Suhaimi', 'Kepala Dusun Rawa Tunggal', NULL, NULL, 10, '2026-08-09 13:36:28', '2026-08-09 13:36:28'),
(13, 2, 'Rodi Pranata', 'Kepala Dusun Kejadian', NULL, NULL, 11, '2026-08-09 13:36:28', '2026-08-09 13:36:28'),
(14, 2, 'Nursiah', 'Kepala Dusun Kecapi', NULL, NULL, 12, '2026-08-09 13:36:28', '2026-08-09 13:36:28'),
(15, 2, 'Matnuri', 'Kepala Dusun Bambu Kuning', NULL, NULL, 13, '2026-08-09 13:36:28', '2026-08-09 13:36:28'),
(16, 2, 'Nursiah', 'Kepala Dusun Bambu Kuning II', NULL, NULL, 14, '2026-08-09 13:36:28', '2026-08-09 13:36:28');

-- ------------------------------------------------------------
-- Struktur tabel `wisata_desa`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `wisata_desa`;
CREATE TABLE `wisata_desa` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maps_embed_url` text COLLATE utf8mb4_unicode_ci,
  `harga_tiket` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_buka` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wa_kontak` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','publish') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_wisata_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `wisata_desa`: 3 baris
INSERT INTO `wisata_desa` (`id`,`nama`,`slug`,`deskripsi`,`alamat`,`maps_embed_url`,`harga_tiket`,`jam_buka`,`wa_kontak`,`status`,`created_at`,`updated_at`) VALUES
(1, 'Air Terjun Penyarian', 'air-terjun-penyarian', 'Air terjun yang terletak di Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin. Destinasi ini dibuka pada tahun 2026 dan dikelola oleh Gapoktan setempat. Kini menjadi salah satu tempat wisata alam yang sangat ramai dikunjungi, baik oleh wisatawan lokal, luar kabupaten/kota, maupun dari luar provinsi.', 'Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin', NULL, NULL, NULL, '6285173200421', 'publish', '2026-08-08 15:07:04', '2026-08-10 02:05:24'),
(2, 'Air Terjun Tanah Longsor', 'air-terjun-tanah-longsor', 'Salah satu dari dua lokasi wisata alam air terjun di Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin. Bersama Air Terjun Penyarian, kawasan ini menjadi destinasi wisata alam yang menarik minat banyak wisatawan.', 'Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin', NULL, NULL, NULL, '6285173200421', 'publish', '2026-08-08 15:07:04', '2026-08-10 02:05:24'),
(3, 'dad', 'dad', 'dad', 'dada', 'https://www.google.com/maps/@-6.2062592,106.8302336,12z?entry=ttu&g_ep=EgoyMDI2MDgwNS4xIKXMDSoASAFQAw%3D%3D', 'tes', 'tes', '6285173200421', 'draft', '2026-08-09 20:34:02', '2026-08-10 02:05:24');

-- ------------------------------------------------------------
-- Struktur tabel `wisata_fasilitas`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `wisata_fasilitas`;
CREATE TABLE `wisata_fasilitas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `wisata_id` int unsigned NOT NULL,
  `ikon` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'eco',
  `judul` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_wisata_fasilitas` (`wisata_id`),
  CONSTRAINT `fk_wisata_fasilitas` FOREIGN KEY (`wisata_id`) REFERENCES `wisata_desa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `wisata_fasilitas`: 12 baris
INSERT INTO `wisata_fasilitas` (`id`,`wisata_id`,`ikon`,`judul`,`deskripsi`,`urutan`) VALUES
(1, 3, 'photo_camera', 'Spot Fotografi', 'Gardu pandang khusus disediakan untuk mengambil foto lanskap terbaik saat matahari terbit dan terbenam.', 1),
(2, 3, 'restaurant', 'Saung Kuliner', 'Menikmati kopi lokal dan hidangan tradisional khas desa di saung bambu yang menghadap langsung ke hamparan alam.', 2),
(3, 3, 'directions_walk', 'Trekking Alam', 'Jalur trekking ringan menyusuri pinggiran bukit dan sungai kecil yang mengaliri area wisata.', 3),
(4, 3, 'local_florist', 'Edukasi Lingkungan', 'Program edukasi singkat tentang konservasi alam dan budaya lokal bersama warga sekitar.', 4),
(5, 1, 'photo_camera', 'Spot Fotografi', 'Gardu pandang khusus disediakan untuk mengambil foto lanskap terbaik saat matahari terbit dan terbenam.', 1),
(6, 1, 'restaurant', 'Saung Kuliner', 'Menikmati kopi lokal dan hidangan tradisional khas desa di saung bambu yang menghadap langsung ke hamparan alam.', 2),
(7, 1, 'directions_walk', 'Trekking Alam', 'Jalur trekking ringan menyusuri pinggiran bukit dan sungai kecil yang mengaliri area wisata.', 3),
(8, 1, 'local_florist', 'Edukasi Lingkungan', 'Program edukasi singkat tentang konservasi alam dan budaya lokal bersama warga sekitar.', 4),
(9, 2, 'photo_camera', 'Spot Fotografi', 'Gardu pandang khusus disediakan untuk mengambil foto lanskap terbaik saat matahari terbit dan terbenam.', 1),
(10, 2, 'restaurant', 'Saung Kuliner', 'Menikmati kopi lokal dan hidangan tradisional khas desa di saung bambu yang menghadap langsung ke hamparan alam.', 2),
(11, 2, 'directions_walk', 'Trekking Alam', 'Jalur trekking ringan menyusuri pinggiran bukit dan sungai kecil yang mengaliri area wisata.', 3),
(12, 2, 'local_florist', 'Edukasi Lingkungan', 'Program edukasi singkat tentang konservasi alam dan budaya lokal bersama warga sekitar.', 4);

-- ------------------------------------------------------------
-- Struktur tabel `wisata_gambar`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `wisata_gambar`;
CREATE TABLE `wisata_gambar` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `wisata_id` int unsigned NOT NULL,
  `path_gambar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_wisata_gambar` (`wisata_id`),
  CONSTRAINT `fk_wisata_gambar` FOREIGN KEY (`wisata_id`) REFERENCES `wisata_desa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data tabel `wisata_gambar`: 3 baris
INSERT INTO `wisata_gambar` (`id`,`wisata_id`,`path_gambar`,`urutan`) VALUES
(1, 1, 'wisata/air-terjun-penyarian-1.jpg', 1),
(2, 1, 'wisata/air-terjun-penyarian-2.jpg', 2),
(3, 2, 'wisata/air-terjun-tanah-longsor-1.jpg', 1);

SET FOREIGN_KEY_CHECKS = 1;
