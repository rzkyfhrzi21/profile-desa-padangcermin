-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: localhost    Database: padang_cermin_db
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin',NULL,NULL,'$2y$10$x79QAtfTC82E8u4bm7RcPeme2udoI9E5I1NwPv.cB1hgi/9EuoAO2','Admin Pekon','admin','2026-08-07 07:57:07','2026-08-07 09:55:57');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `berita_desa`
--

DROP TABLE IF EXISTS `berita_desa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `berita_desa` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_id` int unsigned DEFAULT NULL,
  `konten` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar_utama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_gambar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `berita_desa`
--

LOCK TABLES `berita_desa` WRITE;
/*!40000 ALTER TABLE `berita_desa` DISABLE KEYS */;
INSERT INTO `berita_desa` VALUES (1,'Musyawarah Desa Penyusunan RKPDes dan APBDes Tahun Anggaran 2027','musyawarah-desa-penyusunan-rkpdes-dan-apbdes-tahun-anggaran-2027',1,'Pemerintah Desa Padang Cermin mengadakan kegiatan Musyawarah Desa Penyusunan RKPDes dan APBDes tahun anggaran 2027 yang dilaksanakan pada 8 Juni 2026, diselenggarakan oleh BPD Desa Padang Cermin.\n\nKegiatan ini dihadiri langsung oleh Bapak Camat yang memberikan arahan terkait penyusunan RKPDes dan APBDes serta realisasinya. Berbagai musyawarah serta tanya jawab terkait kegiatan dan pengajuan pembangunan di wilayah Desa Padang Cermin berlangsung dengan tertib dan khidmat.\n\nKegiatan ini dilaksanakan guna menentukan arah perencanaan, baik secara pembangunan maupun kegiatan lainnya, demi kemajuan Desa Padang Cermin.','berita/musdes-rkpdes-apbdes-2027.jpg','Musyawarah Desa penyusunan RKPDes dan APBDes tahun anggaran 2027',1,'publish',6,'2026-06-08 09:00:00','2026-08-08 08:07:04','2026-08-08 08:34:23'),(2,'Rembug Stunting dalam Rangka Pencegahan dan Penurunan Angka Stunting Desa Padang Cermin','rembug-stunting-dalam-rangka-pencegahan-dan-penurunan-angka-stunting-desa-padang-cermin',3,'Kegiatan rembug stunting ini rutin dilakukan setiap tahun guna meminimalisir angka stunting bagi anak di Desa Padang Cermin, Kecamatan Padang Cermin. Beberapa stakeholder turut berpartisipasi dalam kegiatan tersebut, seperti Camat Padang Cermin, Kepala Puskesmas Padang Cermin, bidan desa, serta kader-kader yang bersinggungan langsung dengan kegiatan posyandu desa.\n\nHal ini dilakukan guna mengetahui sejauh mana angka penurunan stunting di Desa Padang Cermin, demi menciptakan generasi emas menuju Indonesia Emas tahun 2045 sejak dini.','berita/rembug-stunting-2026.jpg','Rembug stunting dalam rangka pencegahan dan penurunan angka stunting Desa Padang Cermin',1,'publish',1,'2026-08-01 09:00:00','2026-08-08 08:07:04','2026-08-08 08:11:55');
/*!40000 ALTER TABLE `berita_desa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `berita_kategori`
--

DROP TABLE IF EXISTS `berita_kategori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `berita_kategori` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `berita_kategori`
--

LOCK TABLES `berita_kategori` WRITE;
/*!40000 ALTER TABLE `berita_kategori` DISABLE KEYS */;
INSERT INTO `berita_kategori` VALUES (1,'Pemerintahan','pemerintahan'),(2,'Pembangunan','pembangunan'),(3,'Kesehatan','kesehatan'),(4,'Pariwisata','pariwisata'),(5,'Pertanian','pertanian'),(6,'adad','adad');
/*!40000 ALTER TABLE `berita_kategori` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `data_kependudukan`
--

DROP TABLE IF EXISTS `data_kependudukan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_kependudukan`
--

LOCK TABLES `data_kependudukan` WRITE;
/*!40000 ALTER TABLE `data_kependudukan` DISABLE KEYS */;
INSERT INTO `data_kependudukan` VALUES (1,'2026-04',1926,6886,3425,3461,'Rekap April 2026 (per 01 Juli 2026)','2026-08-08 08:07:04');
/*!40000 ALTER TABLE `data_kependudukan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventaris`
--

DROP TABLE IF EXISTS `inventaris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventaris`
--

LOCK TABLES `inventaris` WRITE;
/*!40000 ALTER TABLE `inventaris` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventaris` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kependudukan_dusun`
--

DROP TABLE IF EXISTS `kependudukan_dusun`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kependudukan_dusun`
--

LOCK TABLES `kependudukan_dusun` WRITE;
/*!40000 ALTER TABLE `kependudukan_dusun` DISABLE KEYS */;
INSERT INTO `kependudukan_dusun` VALUES (1,'2026-04','Padang Cermin',276,266,170,542,'2026-08-08 08:07:04'),(2,'2026-04','Tanjung Mas',268,243,152,511,'2026-08-08 08:07:04'),(3,'2026-04','Rawa Subur',347,350,217,697,'2026-08-08 08:07:04'),(4,'2026-04','Rawa Tunggal',495,518,311,1013,'2026-08-08 08:07:04'),(5,'2026-04','Kejadian',310,278,169,588,'2026-08-08 08:07:04'),(6,'2026-04','Kecapi',479,463,284,942,'2026-08-08 08:07:04'),(7,'2026-04','Bambu Kuning',331,439,182,770,'2026-08-08 08:07:04'),(8,'2026-04','Lubuk Bakak',919,904,441,1823,'2026-08-08 08:07:04');
/*!40000 ALTER TABLE `kependudukan_dusun` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_aktivitas`
--

DROP TABLE IF EXISTS `log_aktivitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_aktivitas`
--

LOCK TABLES `log_aktivitas` WRITE;
/*!40000 ALTER TABLE `log_aktivitas` DISABLE KEYS */;
INSERT INTO `log_aktivitas` VALUES (1,1,'login','admins',1,'2026-08-07 07:57:50'),(2,1,'login','admins',1,'2026-08-07 07:57:57'),(3,1,'login','admins',1,'2026-08-07 07:58:54'),(4,1,'login','admins',1,'2026-08-07 07:59:28'),(5,1,'login','admins',1,'2026-08-07 08:08:11'),(6,1,'login','admins',1,'2026-08-07 08:15:26'),(7,1,'login','admins',1,'2026-08-07 08:15:56'),(8,1,'login','admins',1,'2026-08-07 08:16:20'),(9,1,'LOGIN','auth',NULL,'2026-08-07 08:48:58'),(10,1,'LOGIN','auth',NULL,'2026-08-07 08:49:05'),(11,1,'LOGIN','auth',NULL,'2026-08-07 08:54:08'),(12,1,'LOGIN','auth',NULL,'2026-08-07 09:00:46'),(13,1,'LOGIN','auth',NULL,'2026-08-07 09:55:30'),(14,1,'LOGIN','auth',NULL,'2026-08-07 09:55:57'),(15,1,'Seed data dummy','seed',NULL,'2026-08-07 10:08:04'),(16,1,'Aktivasi konten awal','seed',NULL,'2026-08-07 10:08:04'),(17,1,'LOGIN','auth',NULL,'2026-08-07 10:09:14'),(18,1,'LOGIN','auth',NULL,'2026-08-07 10:09:22'),(19,1,'LOGIN','auth',NULL,'2026-08-07 10:10:21'),(20,1,'LOGIN','auth',NULL,'2026-08-08 08:35:05'),(21,1,'LOGIN','auth',NULL,'2026-08-08 08:43:42'),(29,1,'tambah kategori berita: adad','berita_kategori',NULL,'2026-08-08 10:11:07');
/*!40000 ALTER TABLE `log_aktivitas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `potensi_desa`
--

DROP TABLE IF EXISTS `potensi_desa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `potensi_desa`
--

LOCK TABLES `potensi_desa` WRITE;
/*!40000 ALTER TABLE `potensi_desa` DISABLE KEYS */;
INSERT INTO `potensi_desa` VALUES (1,'Sentra Produksi Pisang','Desa Padang Cermin menjadi sentra produksi pisang, terutama pisang ambon, dengan luas lahan produksi sekitar 152 hektare. Pisang menjadi mata pencaharian dan komoditas utama sebagian besar masyarakat.',NULL,'Pertanian',NULL,1,'aktif','2026-08-08 08:07:04','2026-08-08 08:07:04'),(2,'Pertanian & Perkebunan','Desa terluas di Kecamatan Padang Cermin dengan luas areal mencapai sekitar 3.278 hektare. Komoditas utama meliputi pertanian padi serta perkebunan kopi dan lada.',NULL,'Pertanian',NULL,2,'aktif','2026-08-08 08:07:04','2026-08-08 08:07:04'),(3,'Jalur Strategis Pariwisata','Letak Desa Padang Cermin berfungsi sebagai jalur akses penting dari Kota Bandar Lampung menuju kawasan wisata bahari di Pesawaran dan sekitarnya.',NULL,'Infrastruktur',NULL,3,'aktif','2026-08-08 08:07:04','2026-08-08 08:07:04'),(4,'Agrowisata Air Terjun','Kawasan agrowisata air terjun yang terletak di Dusun Lubuk Bakak RT 005 Desa Padang Cermin. Saat ini sangat banyak dikunjungi wisatawan, baik lokal, luar daerah, maupun dari luar pulau.',NULL,'Pariwisata',NULL,4,'aktif','2026-08-08 08:07:04','2026-08-08 08:07:04');
/*!40000 ALTER TABLE `potensi_desa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profil_desa`
--

DROP TABLE IF EXISTS `profil_desa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profil_desa` (
  `id` int unsigned NOT NULL,
  `nama_pekon` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `misi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sambutan_kepala_pekon` text COLLATE utf8mb4_unicode_ci,
  `foto_kepala_pekon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_kantor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) NOT NULL DEFAULT '0.0000000',
  `longitude` decimal(10,7) NOT NULL DEFAULT '0.0000000',
  `maps_embed_url` text COLLATE utf8mb4_unicode_ci,
  `telepon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profil_desa`
--

LOCK TABLES `profil_desa` WRITE;
/*!40000 ALTER TABLE `profil_desa` DISABLE KEYS */;
INSERT INTO `profil_desa` VALUES (1,'Desa Padang Cermin','MEMBANGUN DESA PADANG CERMIN YANG BERTAQWA, DESA KUAT, DESA SEHAT, DESA MANDIRI, DESA BERKEADILAN, DESA AMAN, BERLANDASKAN PENINGKATAN EKONOMI KERAKYATAN','PROGRAM KEHIDUPAN SEHAT DAN SEJAHTERA\nPROGRAM PENDIDIKAN BERKUALITAS\nPROGRAM DESA TANPA KELAPARAN\nPROGRAM AIR BERSIH DAN SANITASI\nPROGRAM PEKERJAAN LAYAK DAN PERTUMBUHAN EKONOMI\nPROGRAM PENANGAN PERUBAHAN IKLIM\nPROGRAM KEAMANAN DAN KELEMBAGAAN TANGGUH','Assalamualaikum warahmatullahi wabarakatuh. Selamat datang di portal resmi Pekon Padang Cermin. Portal ini kami hadirkan sebagai wujud komitmen pemerintahan pekon dalam keterbukaan informasi, sehingga seluruh warga dapat mengakses data dan kegiatan pekon secara mudah dan transparan. Kami berharap kehadiran portal ini semakin mendekatkan pemerintah pekon dengan masyarakat, serta mendorong partisipasi aktif seluruh warga dalam membangun Pekon Padang Cermin yang lebih maju, sejahtera, dan berkelanjutan.','struktur/struktur-kades.jpg','Jl. Lintas Barat No. 45, Padang Cermin, Lampung',0.0000000,0.0000000,NULL,'0812-3456-7890','info@pekonpadangcermin.go.id','0812-3456-7890','2026-08-08 08:07:04');
/*!40000 ALTER TABLE `profil_desa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `struktur_organisasi`
--

DROP TABLE IF EXISTS `struktur_organisasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `struktur_organisasi`
--

LOCK TABLES `struktur_organisasi` WRITE;
/*!40000 ALTER TABLE `struktur_organisasi` DISABLE KEYS */;
INSERT INTO `struktur_organisasi` VALUES (1,NULL,'Dra. Purwanti','Kepala Desa','S1',NULL,1,'2026-08-08 08:07:04','2026-08-08 08:07:04');
/*!40000 ALTER TABLE `struktur_organisasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wisata_desa`
--

DROP TABLE IF EXISTS `wisata_desa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wisata_desa` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `harga_tiket` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_buka` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','publish') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_wisata_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wisata_desa`
--

LOCK TABLES `wisata_desa` WRITE;
/*!40000 ALTER TABLE `wisata_desa` DISABLE KEYS */;
INSERT INTO `wisata_desa` VALUES (1,'Air Terjun Penyarian','air-terjun-penyarian','Air terjun yang terletak di Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin. Destinasi ini dibuka pada tahun 2026 dan dikelola oleh Gapoktan setempat. Kini menjadi salah satu tempat wisata alam yang sangat ramai dikunjungi, baik oleh wisatawan lokal, luar kabupaten/kota, maupun dari luar provinsi.','Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin',NULL,NULL,NULL,NULL,'publish','2026-08-08 08:07:04','2026-08-08 08:07:04'),(2,'Air Terjun Tanah Longsor','air-terjun-tanah-longsor','Salah satu dari dua lokasi wisata alam air terjun di Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin. Bersama Air Terjun Penyarian, kawasan ini menjadi destinasi wisata alam yang menarik minat banyak wisatawan.','Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin',NULL,NULL,NULL,NULL,'publish','2026-08-08 08:07:04','2026-08-08 08:07:04');
/*!40000 ALTER TABLE `wisata_desa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wisata_gambar`
--

DROP TABLE IF EXISTS `wisata_gambar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wisata_gambar` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `wisata_id` int unsigned NOT NULL,
  `path_gambar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `urutan` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_wisata_gambar` (`wisata_id`),
  CONSTRAINT `fk_wisata_gambar` FOREIGN KEY (`wisata_id`) REFERENCES `wisata_desa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wisata_gambar`
--

LOCK TABLES `wisata_gambar` WRITE;
/*!40000 ALTER TABLE `wisata_gambar` DISABLE KEYS */;
INSERT INTO `wisata_gambar` VALUES (1,1,'wisata/air-terjun-penyarian-1.jpg','Air Terjun Penyarian di Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin',1),(2,1,'wisata/air-terjun-penyarian-2.jpg','Aliran air Air Terjun Penyarian, Desa Padang Cermin',2),(3,2,'wisata/air-terjun-tanah-longsor-1.jpg','Air Terjun Tanah Longsor di Kampung Penyarian, Dusun Lubuk Bakak, Desa Padang Cermin',1);
/*!40000 ALTER TABLE `wisata_gambar` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-09 13:08:08
