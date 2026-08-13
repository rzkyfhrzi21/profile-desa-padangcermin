-- Adminer 6.0.0 MariaDB 11.4.12-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admins` (`id`, `username`, `email`, `foto`, `password_hash`, `nama`, `role`, `created_at`, `updated_at`) VALUES
(1,	'admin',	NULL,	'admin/1dd58f05ae36e503.jpg',	'$2y$10$WczWR6rfiJhzHO4Z5E25FuHH68VOQzwJInnKrX1MsuX6iDGzdfqZm',	'Admin Desa Padang Cermin',	'admin',	'2026-08-07 21:57:07',	'2026-08-13 15:19:29');

DROP TABLE IF EXISTS `berita_desa`;
CREATE TABLE `berita_desa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `kategori_id` int(10) unsigned DEFAULT NULL,
  `konten` mediumtext NOT NULL,
  `gambar_utama` varchar(255) DEFAULT NULL,
  `penulis_id` int(10) unsigned NOT NULL,
  `status` enum('draft','publish') NOT NULL DEFAULT 'draft',
  `views` int(11) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_berita_penulis` (`penulis_id`),
  KEY `idx_berita_status` (`status`,`published_at`),
  KEY `idx_berita_kategori` (`kategori_id`),
  CONSTRAINT `fk_berita_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `berita_kategori` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_berita_penulis` FOREIGN KEY (`penulis_id`) REFERENCES `admins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `berita_desa` (`id`, `judul`, `slug`, `kategori_id`, `konten`, `gambar_utama`, `penulis_id`, `status`, `views`, `published_at`, `created_at`, `updated_at`) VALUES
(1,	'Musyawarah Desa Penyusunan RKPDes dan APBDes Tahun Anggaran 2027',	'musyawarah-desa-penyusunan-rkpdes-dan-apbdes-tahun-anggaran-2027',	1,	'Pemerintah Desa Padang Cermin mengadakan kegiatan Musyawarah Desa Penyusunan RKPDes dan APBDes tahun anggaran 2027 yang dilaksanakan pada 8 Juni 2026, diselenggarakan oleh BPD Desa Padang Cermin.\n\nKegiatan ini dihadiri langsung oleh Bapak Camat yang memberikan arahan terkait penyusunan RKPDes dan APBDes serta realisasinya. Berbagai musyawarah serta tanya jawab terkait kegiatan dan pengajuan pembangunan di wilayah Desa Padang Cermin berlangsung dengan tertib dan khidmat.\n\nKegiatan ini dilaksanakan guna menentukan arah perencanaan, baik secara pembangunan maupun kegiatan lainnya, demi kemajuan Desa Padang Cermin.',	'berita/musdes-rkpdes-apbdes-2027.jpg',	1,	'publish',	9,	'2026-06-08 09:00:00',	'2026-08-08 22:07:04',	'2026-08-10 03:50:51'),
(2,	'Rembug Stunting dalam Rangka Pencegahan dan Penurunan Angka Stunting Desa Padang Cermin',	'rembug-stunting-dalam-rangka-pencegahan-dan-penurunan-angka-stunting-desa-padang-cermin',	3,	'Kegiatan rembug stunting ini rutin dilakukan setiap tahun guna meminimalisir angka stunting bagi anak di Desa Padang Cermin, Kecamatan Padang Cermin. Beberapa stakeholder turut berpartisipasi dalam kegiatan tersebut, seperti Camat Padang Cermin, Kepala Puskesmas Padang Cermin, bidan desa, serta kader-kader yang bersinggungan langsung dengan kegiatan posyandu desa.\n\nHal ini dilakukan guna mengetahui sejauh mana angka penurunan stunting di Desa Padang Cermin, demi menciptakan generasi emas menuju Indonesia Emas tahun 2045 sejak dini.',	'berita/rembug-stunting-2026.jpg',	1,	'publish',	12,	'2026-08-01 09:00:00',	'2026-08-08 22:07:04',	'2026-08-13 18:58:52');

DROP TABLE IF EXISTS `berita_kategori`;
CREATE TABLE `berita_kategori` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `berita_kategori` (`id`, `nama`, `slug`) VALUES
(1,	'Pemerintahan',	'pemerintahan'),
(2,	'Pembangunan',	'pembangunan'),
(3,	'Kesehatan',	'kesehatan'),
(4,	'Pariwisata',	'pariwisata'),
(5,	'Pertanian',	'pertanian'),
(6,	'adad',	'adad');

DROP TABLE IF EXISTS `data_kependudukan`;
CREATE TABLE `data_kependudukan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `periode` varchar(20) NOT NULL,
  `jumlah_kk` int(11) NOT NULL,
  `jumlah_jiwa` int(11) NOT NULL,
  `jumlah_laki` int(11) NOT NULL DEFAULT 0,
  `jumlah_perempuan` int(11) NOT NULL DEFAULT 0,
  `keterangan` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `periode` (`periode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `data_kependudukan` (`id`, `periode`, `jumlah_kk`, `jumlah_jiwa`, `jumlah_laki`, `jumlah_perempuan`, `keterangan`, `updated_at`) VALUES
(1,	'2026-04',	1926,	6886,	3425,	3461,	'Rekap April 2026 (per 01 Juli 2026)',	'2026-08-08 22:07:04');

DROP TABLE IF EXISTS `dusun_master`;
CREATE TABLE `dusun_master` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `urutan` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dusun_master` (`id`, `nama`, `urutan`, `aktif`, `created_at`) VALUES
(1,	'Lubuk Bakak',	1,	1,	'2026-08-10 02:40:46'),
(2,	'Rawa Tunggal',	2,	1,	'2026-08-10 02:40:46'),
(3,	'Kecapi',	3,	1,	'2026-08-10 02:40:46'),
(4,	'Bambu Kuning',	4,	1,	'2026-08-10 02:40:46'),
(5,	'Rawa Subur',	5,	1,	'2026-08-10 02:40:46'),
(6,	'Kejadian',	6,	1,	'2026-08-10 02:40:46'),
(7,	'Padang Cermin',	7,	1,	'2026-08-10 02:40:46'),
(8,	'Tanjung Mas',	8,	1,	'2026-08-10 02:40:46'),
(9,	'f',	9,	1,	'2026-08-13 16:14:06');

DROP TABLE IF EXISTS `inventaris`;
CREATE TABLE `inventaris` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `satuan` varchar(20) NOT NULL DEFAULT 'unit',
  `kondisi` enum('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik',
  `lokasi_penyimpanan` varchar(150) NOT NULL,
  `tahun_perolehan` year(4) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_barang` (`kode_barang`),
  KEY `idx_inventaris_kategori` (`kategori`),
  KEY `idx_inventaris_kondisi` (`kondisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `kependudukan_dusun`;
CREATE TABLE `kependudukan_dusun` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `periode` varchar(20) NOT NULL,
  `nama_dusun` varchar(100) NOT NULL,
  `jumlah_laki` int(11) NOT NULL DEFAULT 0,
  `jumlah_perempuan` int(11) NOT NULL DEFAULT 0,
  `jumlah_kk` int(11) NOT NULL DEFAULT 0,
  `jumlah_jiwa` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kd_periode_dusun` (`periode`,`nama_dusun`),
  KEY `idx_kd_periode` (`periode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kependudukan_dusun` (`id`, `periode`, `nama_dusun`, `jumlah_laki`, `jumlah_perempuan`, `jumlah_kk`, `jumlah_jiwa`, `updated_at`) VALUES
(1,	'2026-04',	'Padang Cermin',	276,	266,	170,	542,	'2026-08-08 22:07:04'),
(2,	'2026-04',	'Tanjung Mas',	268,	243,	152,	511,	'2026-08-10 02:57:10'),
(3,	'2026-04',	'Rawa Subur',	347,	350,	217,	697,	'2026-08-08 22:07:04'),
(4,	'2026-04',	'Rawa Tunggal',	495,	518,	311,	1013,	'2026-08-10 02:57:22'),
(5,	'2026-04',	'Kejadian',	310,	278,	169,	588,	'2026-08-08 22:07:04'),
(6,	'2026-04',	'Kecapi',	479,	463,	284,	942,	'2026-08-08 22:07:04'),
(7,	'2026-04',	'Bambu Kuning',	331,	439,	182,	770,	'2026-08-08 22:07:04'),
(8,	'2026-04',	'Lubuk Bakak',	919,	904,	441,	1823,	'2026-08-10 02:56:59'),
(9,	'2026-04',	'Tes',	1,	1,	11,	1,	'2026-08-09 21:10:21');

DROP TABLE IF EXISTS `log_aktivitas`;
CREATE TABLE `log_aktivitas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL,
  `aksi` varchar(100) NOT NULL,
  `tabel_terkait` varchar(50) NOT NULL,
  `data_id` int(10) unsigned DEFAULT NULL,
  `waktu` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_log_admin` (`admin_id`),
  KEY `idx_log_waktu` (`waktu`),
  CONSTRAINT `fk_log_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `log_aktivitas` (`id`, `admin_id`, `aksi`, `tabel_terkait`, `data_id`, `waktu`) VALUES
(1,	1,	'login',	'admins',	1,	'2026-08-07 21:57:50'),
(2,	1,	'login',	'admins',	1,	'2026-08-07 21:57:57'),
(3,	1,	'login',	'admins',	1,	'2026-08-07 21:58:54'),
(4,	1,	'login',	'admins',	1,	'2026-08-07 21:59:28'),
(5,	1,	'login',	'admins',	1,	'2026-08-07 22:08:11'),
(6,	1,	'login',	'admins',	1,	'2026-08-07 22:15:26'),
(7,	1,	'login',	'admins',	1,	'2026-08-07 22:15:56'),
(8,	1,	'login',	'admins',	1,	'2026-08-07 22:16:20'),
(9,	1,	'LOGIN',	'auth',	NULL,	'2026-08-07 22:48:58'),
(10,	1,	'LOGIN',	'auth',	NULL,	'2026-08-07 22:49:05'),
(11,	1,	'LOGIN',	'auth',	NULL,	'2026-08-07 22:54:08'),
(12,	1,	'LOGIN',	'auth',	NULL,	'2026-08-07 23:00:46'),
(13,	1,	'LOGIN',	'auth',	NULL,	'2026-08-07 23:55:30'),
(14,	1,	'LOGIN',	'auth',	NULL,	'2026-08-07 23:55:57'),
(15,	1,	'Seed data dummy',	'seed',	NULL,	'2026-08-08 00:08:04'),
(16,	1,	'Aktivasi konten awal',	'seed',	NULL,	'2026-08-08 00:08:04'),
(17,	1,	'LOGIN',	'auth',	NULL,	'2026-08-08 00:09:14'),
(18,	1,	'LOGIN',	'auth',	NULL,	'2026-08-08 00:09:22'),
(19,	1,	'LOGIN',	'auth',	NULL,	'2026-08-08 00:10:21'),
(20,	1,	'LOGIN',	'auth',	NULL,	'2026-08-08 22:35:05'),
(21,	1,	'LOGIN',	'auth',	NULL,	'2026-08-08 22:43:42'),
(29,	1,	'tambah kategori berita: adad',	'berita_kategori',	NULL,	'2026-08-09 00:11:07'),
(30,	1,	'LOGIN',	'auth',	NULL,	'2026-08-09 20:23:12'),
(31,	1,	'tambah berita: tesdadadadad adad',	'berita_desa',	3,	'2026-08-09 22:28:47'),
(32,	1,	'tambah struktur: tes',	'struktur_organisasi',	17,	'2026-08-09 22:29:56'),
(33,	1,	'LOGIN',	'auth',	NULL,	'2026-08-10 02:27:19'),
(34,	1,	'tambah berita: tes',	'berita_desa',	4,	'2026-08-10 02:30:18'),
(35,	1,	'update profil admin',	'admins',	1,	'2026-08-10 03:16:07'),
(36,	1,	'edit potensi: Sentra Produksi Pisang',	'potensi_desa',	1,	'2026-08-10 03:27:28'),
(37,	1,	'hapus berita: tes',	'berita_desa',	4,	'2026-08-10 03:28:14'),
(38,	1,	'tambah wisata: dad',	'wisata_desa',	3,	'2026-08-10 03:34:02'),
(39,	1,	'update profil admin',	'admins',	1,	'2026-08-10 03:44:25'),
(40,	1,	'LOGIN',	'auth',	NULL,	'2026-08-10 05:55:46'),
(41,	1,	'LOGIN',	'auth',	NULL,	'2026-08-10 08:40:55'),
(42,	1,	'LOGIN',	'auth',	NULL,	'2026-08-10 08:46:20'),
(43,	1,	'hapus struktur: tes',	'struktur_organisasi',	17,	'2026-08-10 08:46:28'),
(44,	1,	'LOGIN',	'auth',	NULL,	'2026-08-10 04:05:25'),
(45,	1,	'LOGIN',	'auth',	NULL,	'2026-08-10 06:47:24'),
(46,	1,	'LOGIN',	'auth',	NULL,	'2026-08-13 15:18:16'),
(47,	1,	'ubah password admin',	'admins',	1,	'2026-08-13 15:19:06'),
(48,	1,	'update profil admin',	'admins',	1,	'2026-08-13 15:19:29'),
(49,	1,	'edit struktur: Dra. Purwanti',	'struktur_organisasi',	1,	'2026-08-13 15:21:12'),
(50,	1,	'edit struktur: Husen',	'struktur_organisasi',	2,	'2026-08-13 15:23:13'),
(51,	1,	'edit struktur: Gita Veronissa',	'struktur_organisasi',	6,	'2026-08-13 15:25:06'),
(52,	1,	'edit struktur: Wawan Andriawan',	'struktur_organisasi',	3,	'2026-08-13 15:25:26'),
(53,	1,	'edit struktur: Ady Kurnia',	'struktur_organisasi',	9,	'2026-08-13 15:25:45'),
(54,	1,	'edit struktur: Deden Surahman',	'struktur_organisasi',	4,	'2026-08-13 15:26:52'),
(55,	1,	'edit struktur: Taufik',	'struktur_organisasi',	10,	'2026-08-13 15:27:08'),
(56,	1,	'edit struktur: Irma Fauziyah',	'struktur_organisasi',	5,	'2026-08-13 15:28:19'),
(57,	1,	'edit struktur: Mutiono',	'struktur_organisasi',	11,	'2026-08-13 15:29:26'),
(58,	1,	'edit struktur: Suhaimi',	'struktur_organisasi',	12,	'2026-08-13 15:29:48'),
(59,	1,	'edit struktur: Archy Ollivia',	'struktur_organisasi',	7,	'2026-08-13 15:30:48'),
(60,	1,	'edit struktur: Rodi Pratama',	'struktur_organisasi',	13,	'2026-08-13 15:31:15'),
(61,	1,	'edit struktur: Indera Herlangga',	'struktur_organisasi',	8,	'2026-08-13 15:32:01'),
(62,	1,	'edit struktur: Nursiah',	'struktur_organisasi',	14,	'2026-08-13 15:32:21'),
(63,	1,	'edit struktur: Mat Nuri',	'struktur_organisasi',	15,	'2026-08-13 15:33:21'),
(64,	1,	'LOGIN',	'auth',	NULL,	'2026-08-13 15:33:48'),
(65,	1,	'hapus struktur: Nursiah',	'struktur_organisasi',	16,	'2026-08-13 15:34:36'),
(66,	1,	'hapus wisata: dad',	'wisata_desa',	3,	'2026-08-13 15:44:44'),
(67,	1,	'hapus berita: tesdadadadad adad',	'berita_desa',	3,	'2026-08-13 15:57:23'),
(68,	1,	'edit wisata: Air Terjun Tanah Longsor',	'wisata_desa',	2,	'2026-08-13 16:12:09'),
(69,	1,	'edit potensi: Sentra Produksi Pisang',	'potensi_desa',	1,	'2026-08-13 16:23:48'),
(70,	1,	'edit potensi: Perkebunan Kopi, Pala & Cokelat',	'potensi_desa',	1,	'2026-08-13 16:38:26'),
(71,	1,	'edit potensi: Pertanian Padi & Sentra Pisang',	'potensi_desa',	2,	'2026-08-13 16:39:30'),
(72,	1,	'edit wisata: Air Terjun Penyarian',	'wisata_desa',	1,	'2026-08-13 16:56:30'),
(73,	1,	'LOGIN',	'auth',	NULL,	'2026-08-13 17:10:31'),
(74,	1,	'LOGIN',	'auth',	NULL,	'2026-08-13 18:21:09');

DROP TABLE IF EXISTS `potensi_desa`;
CREATE TABLE `potensi_desa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `ikon` varchar(100) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `potensi_desa` (`id`, `judul`, `deskripsi`, `gambar`, `kategori`, `ikon`, `urutan`, `status`, `created_at`, `updated_at`) VALUES
(1,	'Perkebunan Kopi, Pala & Cokelat',	'Sektor perkebunan pekon menghasilkan berbagai komoditas bernilai komersial tinggi, meliputi budidaya kopi robusta, pala, dan cokelat (kakao) yang dikembangkan di lahan perkebunan masyarakat.',	'potensi/dc59c9c74d9d5810.png',	'perkebunan',	'nature',	1,	'aktif',	'2026-08-08 22:07:04',	'2026-08-13 16:38:26'),
(2,	'Pertanian Padi & Sentra Pisang',	'Desa Padang Cermin memiliki areal pertanian yang produktif dengan komoditas utama tanaman padi sawah serta menjadi sentra produksi pisang ambon seluas 152 hektare yang menopang ketahanan pangan dan ekonomi warga.',	NULL,	'pertanian',	'psychiatry',	2,	'aktif',	'2026-08-08 22:07:04',	'2026-08-13 16:39:30'),
(3,	'Jalur Strategis Pariwisata',	'Letak Desa Padang Cermin berfungsi sebagai jalur akses penting dari Kota Bandar Lampung menuju kawasan wisata bahari di Pesawaran dan sekitarnya.',	NULL,	'Infrastruktur',	NULL,	3,	'aktif',	'2026-08-08 22:07:04',	'2026-08-08 22:07:04'),
(4,	'Agrowisata Air Terjun',	'Kawasan agrowisata air terjun yang terletak di Dusun Lubuk Bakak RT 005 Desa Padang Cermin. Saat ini sangat banyak dikunjungi wisatawan, baik lokal, luar daerah, maupun dari luar pulau.',	NULL,	'Pariwisata',	NULL,	4,	'aktif',	'2026-08-08 22:07:04',	'2026-08-08 22:07:04');

DROP TABLE IF EXISTS `profil_desa`;
CREATE TABLE `profil_desa` (
  `id` int(10) unsigned NOT NULL,
  `nama_pekon` varchar(100) NOT NULL,
  `visi` text NOT NULL,
  `misi` text NOT NULL,
  `sambutan_kepala_pekon` text DEFAULT NULL,
  `foto_kepala_pekon` varchar(255) DEFAULT NULL,
  `alamat_kantor` varchar(255) NOT NULL,
  `maps_embed_url` text DEFAULT NULL,
  `telepon` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `profil_desa` (`id`, `nama_pekon`, `visi`, `misi`, `sambutan_kepala_pekon`, `foto_kepala_pekon`, `alamat_kantor`, `maps_embed_url`, `telepon`, `email`, `whatsapp`, `updated_at`) VALUES
(1,	'Desa Padang Cermin',	'MEMBANGUN DESA PADANG CERMIN YANG BERTAQWA, DESA KUAT, DESA SEHAT, DESA MANDIRI, DESA BERKEADILAN, DESA AMAN, BERLANDASKAN PENINGKATAN EKONOMI KERAKYATAN',	'PROGRAM KEHIDUPAN SEHAT DAN SEJAHTERA\nPROGRAM PENDIDIKAN BERKUALITAS\nPROGRAM DESA TANPA KELAPARAN\nPROGRAM AIR BERSIH DAN SANITASI\nPROGRAM PEKERJAAN LAYAK DAN PERTUMBUHAN EKONOMI\nPROGRAM PENANGAN PERUBAHAN IKLIM\nPROGRAM KEAMANAN DAN KELEMBAGAAN TANGGUH',	'Assalamualaikum warahmatullahi wabarakatuh. Selamat datang di portal resmi Pekon Padang Cermin. Portal ini kami hadirkan sebagai wujud komitmen pemerintahan pekon dalam keterbukaan informasi, sehingga seluruh warga dapat mengakses data dan kegiatan pekon secara mudah dan transparan. Kami berharap kehadiran portal ini semakin mendekatkan pemerintah pekon dengan masyarakat, serta mendorong partisipasi aktif seluruh warga dalam membangun Pekon Padang Cermin yang lebih maju, sejahtera, dan berkelanjutan.',	'struktur/struktur-kades.jpg',	'Jl. Lintas Barat No. 45, Padang Cermin, Lampung',	NULL,	'0812-3456-7890',	'info@pekonpadangcermin.go.id',	'0812-3456-7890',	'2026-08-08 22:07:04');

DROP TABLE IF EXISTS `struktur_organisasi`;
CREATE TABLE `struktur_organisasi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `pendidikan_terakhir` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_struktur_parent` (`parent_id`),
  CONSTRAINT `fk_struktur_parent` FOREIGN KEY (`parent_id`) REFERENCES `struktur_organisasi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `struktur_organisasi` (`id`, `parent_id`, `nama`, `jabatan`, `pendidikan_terakhir`, `foto`, `urutan`, `created_at`, `updated_at`) VALUES
(1,	NULL,	'Dra. Purwanti',	'Kepala Desa',	'S1',	'struktur/a5be56102dd31e38.jpg',	1,	'2026-08-08 22:07:04',	'2026-08-13 15:21:12'),
(2,	1,	'Husen',	'Sekretaris Desa',	NULL,	'struktur/99cc16865f53f8c0.jpg',	0,	'2026-08-09 20:36:28',	'2026-08-13 15:23:13'),
(3,	2,	'Wawan Andriawan',	'Kasi Pemerintahan',	NULL,	'struktur/3c2e0d02689009d2.jpg',	1,	'2026-08-09 20:36:28',	'2026-08-13 15:25:26'),
(4,	2,	'Deden Surahman',	'Kasi Kesejahteraan',	NULL,	'struktur/a56cac70b90a797b.png',	2,	'2026-08-09 20:36:28',	'2026-08-13 15:26:52'),
(5,	2,	'Irma Fauziyah',	'Kasi Pelayanan',	NULL,	'struktur/f0075dc005c98bc8.jpg',	3,	'2026-08-09 20:36:28',	'2026-08-13 15:28:19'),
(6,	2,	'Gita Veronissa',	'Kaur Tata Usaha dan Umum',	NULL,	'struktur/37535544bde54ae5.jpg',	4,	'2026-08-09 20:36:28',	'2026-08-13 15:25:06'),
(7,	2,	'Archy Ollivia',	'Kaur Keuangan',	NULL,	'struktur/16208b514e54b2fa.jpg',	5,	'2026-08-09 20:36:28',	'2026-08-13 15:30:48'),
(8,	2,	'Indera Herlangga',	'Kaur Perencanaan',	NULL,	'struktur/aa64da609edbecf6.jpg',	6,	'2026-08-09 20:36:28',	'2026-08-13 15:32:01'),
(9,	3,	'Ady Kurnia',	'Kepala Dusun Padang Cermin',	NULL,	'struktur/b0797b00ac6629a8.jpg',	1,	'2026-08-09 20:36:28',	'2026-08-13 15:25:45'),
(10,	3,	'Taufik',	'Kepala Dusun Tanjung Mas',	NULL,	'struktur/359d776be3fc8107.jpg',	2,	'2026-08-09 20:36:28',	'2026-08-13 15:27:08'),
(11,	3,	'Mutiono',	'Kepala Dusun Rawa Subur',	NULL,	'struktur/79b03cec78acc1ba.jpg',	3,	'2026-08-09 20:36:28',	'2026-08-13 15:29:26'),
(12,	3,	'Suhaimi',	'Kepala Dusun Rawa Tunggal',	NULL,	'struktur/751211009d7b8b3d.jpg',	4,	'2026-08-09 20:36:28',	'2026-08-13 15:29:48'),
(13,	3,	'Rodi Pratama',	'Kepala Dusun Kejadian',	NULL,	'struktur/c80f2ed2f5beb0ef.jpg',	5,	'2026-08-09 20:36:28',	'2026-08-13 15:31:15'),
(14,	3,	'Nursiah',	'Kepala Dusun Kecapi',	NULL,	'struktur/1c5dceda5803b3f9.jpg',	6,	'2026-08-09 20:36:28',	'2026-08-13 15:32:21'),
(15,	3,	'Mat Nuri',	'Kepala Dusun Bambu Kuning',	NULL,	'struktur/52eb2a6909ed72cc.jpg',	7,	'2026-08-09 20:36:28',	'2026-08-13 15:33:21');

DROP TABLE IF EXISTS `wisata_desa`;
CREATE TABLE `wisata_desa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `deskripsi` text NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `maps_embed_url` text DEFAULT NULL,
  `harga_tiket` varchar(100) DEFAULT NULL,
  `jam_buka` varchar(100) DEFAULT NULL,
  `wa_kontak` varchar(20) DEFAULT NULL,
  `status` enum('draft','publish') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_wisata_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wisata_desa` (`id`, `nama`, `slug`, `deskripsi`, `alamat`, `maps_embed_url`, `harga_tiket`, `jam_buka`, `wa_kontak`, `status`, `created_at`, `updated_at`) VALUES
(1,	'Air Terjun Penyarian',	'air-terjun-penyarian',	'Air terjun yang terletak di Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin. Destinasi ini dibuka pada tahun 2026 dan dikelola oleh Gapoktan setempat. Kini menjadi salah satu tempat wisata alam yang sangat ramai dikunjungi, baik oleh wisatawan lokal, luar kabupaten/kota, maupun dari luar provinsi.',	'Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin',	NULL,	NULL,	NULL,	'6285173200421',	'publish',	'2026-08-08 22:07:04',	'2026-08-13 16:56:30'),
(2,	'Air Terjun Tanah Longsor',	'air-terjun-tanah-longsor',	'Salah satu dari dua lokasi wisata alam air terjun di Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin. Bersama Air Terjun Penyarian, kawasan ini menjadi destinasi wisata alam yang menarik minat banyak wisatawan.',	'Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin',	NULL,	NULL,	NULL,	'6285173200421',	'draft',	'2026-08-08 22:07:04',	'2026-08-13 16:12:09');

DROP TABLE IF EXISTS `wisata_fasilitas`;
CREATE TABLE `wisata_fasilitas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wisata_id` int(10) unsigned NOT NULL,
  `ikon` varchar(60) NOT NULL DEFAULT 'eco',
  `judul` varchar(120) NOT NULL,
  `deskripsi` varchar(500) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_wisata_fasilitas` (`wisata_id`),
  CONSTRAINT `fk_wisata_fasilitas` FOREIGN KEY (`wisata_id`) REFERENCES `wisata_desa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wisata_fasilitas` (`id`, `wisata_id`, `ikon`, `judul`, `deskripsi`, `urutan`) VALUES
(5,	1,	'photo_camera',	'Spot Fotografi',	'Gardu pandang khusus disediakan untuk mengambil foto lanskap terbaik saat matahari terbit dan terbenam.',	1),
(6,	1,	'restaurant',	'Saung Kuliner',	'Menikmati kopi lokal dan hidangan tradisional khas desa di saung bambu yang menghadap langsung ke hamparan alam.',	2),
(7,	1,	'directions_walk',	'Trekking Alam',	'Jalur trekking ringan menyusuri pinggiran bukit dan sungai kecil yang mengaliri area wisata.',	3),
(8,	1,	'local_florist',	'Edukasi Lingkungan',	'Program edukasi singkat tentang konservasi alam dan budaya lokal bersama warga sekitar.',	4),
(9,	2,	'photo_camera',	'Spot Fotografi',	'Gardu pandang khusus disediakan untuk mengambil foto lanskap terbaik saat matahari terbit dan terbenam.',	1),
(10,	2,	'restaurant',	'Saung Kuliner',	'Menikmati kopi lokal dan hidangan tradisional khas desa di saung bambu yang menghadap langsung ke hamparan alam.',	2),
(11,	2,	'directions_walk',	'Trekking Alam',	'Jalur trekking ringan menyusuri pinggiran bukit dan sungai kecil yang mengaliri area wisata.',	3),
(12,	2,	'local_florist',	'Edukasi Lingkungan',	'Program edukasi singkat tentang konservasi alam dan budaya lokal bersama warga sekitar.',	4);

DROP TABLE IF EXISTS `wisata_gambar`;
CREATE TABLE `wisata_gambar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wisata_id` int(10) unsigned NOT NULL,
  `path_gambar` varchar(255) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_wisata_gambar` (`wisata_id`),
  CONSTRAINT `fk_wisata_gambar` FOREIGN KEY (`wisata_id`) REFERENCES `wisata_desa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wisata_gambar` (`id`, `wisata_id`, `path_gambar`, `urutan`) VALUES
(1,	1,	'wisata/air-terjun-penyarian-1.jpg',	1),
(2,	1,	'wisata/air-terjun-penyarian-2.jpg',	2);

-- 2026-08-13 20:15:08 UTC