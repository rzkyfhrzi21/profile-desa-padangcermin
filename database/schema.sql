-- Schema Pekon Padang Cermin — source of truth skema (ARSITEKTUR.md §3 & §5)
CREATE DATABASE IF NOT EXISTS if0_42538523_padangcermin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE if0_42538523_padangcermin;

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS profil_desa (
    id INT UNSIGNED PRIMARY KEY,
    nama_pekon VARCHAR(100) NOT NULL,
    visi TEXT NOT NULL,
    misi TEXT NOT NULL,
    sambutan_kepala_pekon TEXT NULL,
    foto_kepala_pekon VARCHAR(255) NULL,
    alamat_kantor VARCHAR(255) NOT NULL,
    maps_embed_url VARCHAR(500) NULL,
    telepon VARCHAR(50) NULL,
    email VARCHAR(50) NULL,
    whatsapp VARCHAR(50) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS struktur_organisasi (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    parent_id INT UNSIGNED NULL,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    pendidikan_terakhir VARCHAR(20) NULL,
    foto VARCHAR(255) NULL,
    urutan INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_struktur_parent FOREIGN KEY (parent_id) REFERENCES struktur_organisasi(id) ON DELETE SET NULL,
    INDEX idx_struktur_parent (parent_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS data_kependudukan (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    periode VARCHAR(20) NOT NULL UNIQUE,
    jumlah_kk INT NOT NULL,
    jumlah_jiwa INT NOT NULL,
    jumlah_laki INT NOT NULL DEFAULT 0,
    jumlah_perempuan INT NOT NULL DEFAULT 0,
    keterangan VARCHAR(255) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kependudukan_dusun (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    periode VARCHAR(20) NOT NULL,
    nama_dusun VARCHAR(100) NOT NULL,
    jumlah_laki INT NOT NULL DEFAULT 0,
    jumlah_perempuan INT NOT NULL DEFAULT 0,
    jumlah_kk INT NOT NULL DEFAULT 0,
    jumlah_jiwa INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_kd_periode_dusun (periode, nama_dusun),
    INDEX idx_kd_periode (periode)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dusun_master (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    urutan TINYINT UNSIGNED NOT NULL DEFAULT 0,
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_dusun_nama (nama)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS potensi_desa (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    gambar VARCHAR(255) NULL,
    kategori VARCHAR(100) NULL,
    ikon VARCHAR(100) NULL,
    urutan INT NOT NULL DEFAULT 0,
    status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS wisata_desa (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    deskripsi TEXT NOT NULL,
    alamat VARCHAR(255) NOT NULL,
    maps_embed_url VARCHAR(500) NULL,
    harga_tiket VARCHAR(100) NULL,
    jam_buka VARCHAR(100) NULL,
    wa_kontak VARCHAR(20) NULL,
    status ENUM('draft','publish') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_wisata_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS wisata_gambar (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    wisata_id INT UNSIGNED NOT NULL,
    path_gambar VARCHAR(255) NOT NULL,
    urutan INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_wisata_gambar FOREIGN KEY (wisata_id) REFERENCES wisata_desa(id) ON DELETE CASCADE,
    INDEX idx_wisata_gambar (wisata_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS wisata_fasilitas (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    wisata_id INT UNSIGNED NOT NULL,
    ikon VARCHAR(60) NOT NULL DEFAULT 'eco',
    judul VARCHAR(120) NOT NULL,
    deskripsi VARCHAR(500) NOT NULL,
    urutan INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_wisata_fasilitas FOREIGN KEY (wisata_id) REFERENCES wisata_desa(id) ON DELETE CASCADE,
    INDEX idx_wisata_fasilitas (wisata_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS berita_kategori (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS berita_desa (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    kategori_id INT UNSIGNED NULL,
    konten MEDIUMTEXT NOT NULL,
    gambar_utama VARCHAR(255) NULL,
    penulis_id INT UNSIGNED NOT NULL,
    status ENUM('draft','publish') NOT NULL DEFAULT 'draft',
    views INT NOT NULL DEFAULT 0,
    published_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_berita_kategori FOREIGN KEY (kategori_id) REFERENCES berita_kategori(id) ON DELETE SET NULL,
    CONSTRAINT fk_berita_penulis FOREIGN KEY (penulis_id) REFERENCES admins(id),
    INDEX idx_berita_status (status, published_at),
    INDEX idx_berita_kategori (kategori_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inventaris (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    kode_barang VARCHAR(50) NOT NULL UNIQUE,
    nama_barang VARCHAR(150) NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    satuan VARCHAR(20) NOT NULL DEFAULT 'unit',
    kondisi ENUM('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik',
    lokasi_penyimpanan VARCHAR(150) NOT NULL,
    tahun_perolehan YEAR NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_inventaris_kategori (kategori),
    INDEX idx_inventaris_kondisi (kondisi)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS log_aktivitas (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    admin_id INT UNSIGNED NOT NULL,
    aksi VARCHAR(100) NOT NULL,
    tabel_terkait VARCHAR(50) NOT NULL,
    data_id INT UNSIGNED NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_log_admin FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX idx_log_waktu (waktu)
) ENGINE=InnoDB;

-- Baris profil default (single-row config)
INSERT INTO profil_desa (id, nama_pekon, visi, misi, alamat_kantor, maps_embed_url)
VALUES (1, 'Pekon Padang Cermin', '', '', '', NULL)
ON DUPLICATE KEY UPDATE id = id;
