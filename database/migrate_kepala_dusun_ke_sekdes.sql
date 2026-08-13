-- Pindahkan Kepala Dusun agar menjadi anak langsung Sekretaris Desa.
-- Aman dijalankan ulang karena parent_id dan urutan akan ditetapkan ke nilai yang sama.
SET @sekdes_id = (
    SELECT id
    FROM struktur_organisasi
    WHERE jabatan = 'Sekretaris Desa'
    ORDER BY id
    LIMIT 1
);

UPDATE struktur_organisasi
SET parent_id = @sekdes_id,
    urutan = urutan + 6
WHERE jabatan LIKE 'Kepala Dusun%'
  AND @sekdes_id IS NOT NULL
  AND (parent_id IS NULL OR parent_id <> @sekdes_id);
