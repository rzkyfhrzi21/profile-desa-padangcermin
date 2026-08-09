-- Migrasi: hapus kolom latitude/longitude dan alt text
-- Jalankan sekali di database yang sudah ada

-- Hapus latitude & longitude dari wisata_desa
ALTER TABLE wisata_desa
    DROP COLUMN IF EXISTS latitude,
    DROP COLUMN IF EXISTS longitude;

-- Hapus latitude & longitude dari profil_desa
ALTER TABLE profil_desa
    DROP COLUMN IF EXISTS latitude,
    DROP COLUMN IF EXISTS longitude;

-- Hapus alt_text dari wisata_gambar (alt tidak disimpan per gambar lagi)
ALTER TABLE wisata_gambar
    DROP COLUMN IF EXISTS alt_text;

-- Hapus alt_gambar dari berita_desa
ALTER TABLE berita_desa
    DROP COLUMN IF EXISTS alt_gambar;
