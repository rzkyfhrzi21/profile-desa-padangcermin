-- Seeder: Data Staff Pemerintah Desa Padang Cermin
-- Berdasarkan docs/staff.md (diagram struktur organisasi)
-- Jalankan sekali: php Zzz/seed_struktur.php
-- ATAU jalankan SQL ini langsung di phpMyAdmin/MySQL CLI

-- Bersihkan data lama (hati-hati di produksi!)
-- SET FOREIGN_KEY_CHECKS = 0;
-- TRUNCATE TABLE struktur_organisasi;
-- SET FOREIGN_KEY_CHECKS = 1;

-- Level 0: Kepala Desa (sudah ada di DB, update saja)
INSERT INTO struktur_organisasi (id, parent_id, nama, jabatan, urutan)
VALUES (1, NULL, 'Dra. Purwanti', 'Kepala Desa', 0)
ON DUPLICATE KEY UPDATE
    nama = VALUES(nama),
    jabatan = VALUES(jabatan),
    urutan = VALUES(urutan);

-- Level 1: Sekretaris Desa
INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT 1, 'Husen', 'Sekretaris Desa', 0
WHERE NOT EXISTS (
    SELECT 1 FROM struktur_organisasi WHERE nama = 'Husen' AND jabatan = 'Sekretaris Desa'
);

-- Level 2: KASI (anak dari Sekretaris Desa)
-- Gunakan subquery untuk ambil id Sekdes dinamis
SET @sekdes_id = (SELECT id FROM struktur_organisasi WHERE jabatan = 'Sekretaris Desa' LIMIT 1);

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @sekdes_id, 'Wawan Andriawan', 'Kasi Pemerintahan', 1
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Wawan Andriawan');

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @sekdes_id, 'Deden Surahman', 'Kasi Kesejahteraan', 2
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Deden Surahman');

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @sekdes_id, 'Irma Fauziah', 'Kasi Pelayanan', 3
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Irma Fauziah');

-- Level 2: KAUR (anak dari Sekretaris Desa)
INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @sekdes_id, 'Gita Suci Veronissa', 'Kaur Tata Usaha dan Umum', 4
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Gita Suci Veronissa');

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @sekdes_id, 'Archy Ollyvia', 'Kaur Keuangan', 5
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Archy Ollyvia');

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @sekdes_id, 'Indera Herlangga', 'Kaur Perencanaan', 6
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Indera Herlangga');

-- Level 3: Kepala Dusun (anak dari salah satu KASI)
-- Kepala Dusun di-assign ke Kasi Pemerintahan (parent paling umum untuk Kadus)
SET @kasi_id = (SELECT id FROM struktur_organisasi WHERE jabatan = 'Kasi Pemerintahan' LIMIT 1);

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @kasi_id, 'Ady Kurnia', 'Kepala Dusun Padang Cermin', 1
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Ady Kurnia');

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @kasi_id, 'Taufik', 'Kepala Dusun Tanjung Mas', 2
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Taufik' AND jabatan LIKE '%Dusun%');

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @kasi_id, 'Mutiono', 'Kepala Dusun Rawa Subur', 3
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Mutiono');

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @kasi_id, 'Suhaimi', 'Kepala Dusun Rawa Tunggal', 4
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Suhaimi');

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @kasi_id, 'Rodi Pranata', 'Kepala Dusun Kejadian', 5
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Rodi Pranata');

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @kasi_id, 'Nursiah', 'Kepala Dusun Kecapi', 6
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Nursiah' AND jabatan LIKE '%Kecapi%');

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @kasi_id, 'Matnuri', 'Kepala Dusun Bambu Kuning', 7
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Matnuri');

INSERT INTO struktur_organisasi (parent_id, nama, jabatan, urutan)
SELECT @kasi_id, 'Nursiah', 'Kepala Dusun Bambu Kuning II', 8
WHERE NOT EXISTS (SELECT 1 FROM struktur_organisasi WHERE nama = 'Nursiah' AND jabatan LIKE '%Bambu Kuning%');
