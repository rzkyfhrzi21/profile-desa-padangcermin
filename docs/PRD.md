# PRD — Website Resmi Pekon Padang Cermin

> **Catatan nama:** sudah dikonfirmasi user — pekon yang dimaksud adalah **Pekon Padang Cermin** (nama sebelumnya, Banjarnegeri, diambil dari tulisan di bagan struktur organisasi yang diunggah dan ternyata pekon yang salah). [Menebak] "Padang Cermin" lebih dikenal sebagai nama kecamatan di Kabupaten Pesawaran, Lampung, bukan nama pekon/desa tunggal — kalau yang dimaksud memang pekon dengan nama itu (bukan tertukar dengan nama kecamatannya), tidak ada yang perlu diubah lagi; kalau ternyata beda, cari-ganti string `Padang Cermin` di keempat dokumen.

## 1. Ringkasan

Website profil + informasi publik untuk Pekon Padang Cermin, terdiri dari:
- **Landing page publik** — satu halaman utama berisi ringkasan semua informasi wajib, dengan dua sub-modul (wisata, berita) yang punya halaman index/detail sendiri.
- **Dashboard admin** — perangkat pekon (operator: Kepala Urusan Tata Usaha & Umum atau staf yang ditunjuk) mengelola seluruh konten dari satu tempat, termasuk inventaris kantor yang tidak tampil ke publik.

## 2. Tujuan

- Warga dan pihak luar (calon wisatawan, instansi lain, mahasiswa KKN, dst) bisa mendapat informasi resmi pekon tanpa datang langsung ke kantor.
- Perangkat pekon bisa memperbarui data (berita, wisata, jumlah penduduk, dst) sendiri tanpa minta bantuan developer tiap kali ada perubahan.
- Data administratif dasar (struktur organisasi, kependudukan, inventaris) tercatat rapi dan terpusat, bukan tersebar di file Excel/Word masing-masing staf.

## 3. Target Pengguna

| Peran | Akses | Kebutuhan utama |
|---|---|---|
| Warga / pengunjung publik | Read-only, tanpa login | Info cepat: kontak, lokasi, berita terbaru, potensi & wisata desa |
| Admin pekon | Login, CRUD penuh | Update konten rutin (berita, wisata, kependudukan) + kelola inventaris internal |

Tidak ada peran "Kepala Pekon sebagai approver/reviewer" di v1 — semua admin punya hak yang sama. Kalau nanti perlu approval berjenjang, itu perubahan scope, bukan bagian dokumen ini.

## 4. Lingkup Fitur

### 4.1 Halaman Publik (wajib)

| # | Fitur | Sumber data | Catatan |
|---|---|---|---|
| 1 | Visi & Misi | `profil_desa` | Teks, dikelola admin |
| 2 | Sambutan Kepala Pekon | `profil_desa` (opsional) | Boleh kosong — kalau kosong, section disembunyikan otomatis, bukan tampil placeholder kosong |
| 3 | Struktur Organisasi | `struktur_organisasi` (tree) | Diagram visual, lihat §4.4 |
| 4 | Peta Kantor Pekon | `profil_desa.maps_embed_url` | URL Google Maps share/embed, bukan lat/lng |
| 6 | Data Kependudukan | `data_kependudukan` | Jumlah KK + jiwa per periode + grafik tren antar periode |
| 7 | Potensi Desa | `potensi_desa` | Kartu/list, gambar + deskripsi |
| 8 | Wisata Desa | `wisata_desa` + `wisata_gambar` | Preview di landing, detail penuh di halaman terpisah |
| 9 | Berita Desa | `berita_desa` (+ `berita_kategori`) | Preview di landing, detail penuh di halaman terpisah |

### 4.2 Dashboard Admin (wajib)

Semua 9 modul di atas **bisa dikelola (CRUD)** dari admin, ditambah:

| # | Fitur | Catatan |
|---|---|---|
| 10 | Kelola Inventaris | **Khusus admin** — tidak ada halaman publik untuk ini sama sekali |
| — | Login admin | Wajib walau tidak eksplisit disebut — tanpa ini "dikelola di admin" tidak mungkin aman |
| — | Manajemen struktur organisasi sebagai tree | Tambah/edit/hapus jabatan, atur urutan, atur siapa yang muncul di kontak person |
| — | Upload gambar | Untuk wisata, berita, potensi desa, foto staf, foto sambutan kepala pekon |

### 4.3 Arsitektur Informasi — kenapa "satu halaman tapi bisa dipisah"

Permintaan asli sedikit kontradiktif secara teknis (satu halaman vs bisa dipisah). Interpretasi yang dipakai:

- **Landing page (`/`)** = one-page scroll dengan anchor navigation, berisi **ringkasan** tiap section (visi-misi penuh karena pendek, tapi wisata/berita cuma preview 3-4 item terbaru).
- **`/wisata`** (index) dan **`/wisata/{slug}`** (detail) — halaman sendiri.
- **`/berita`** (index) dan **`/berita/{slug}`** (detail) — halaman sendiri.

Alasan: begitu wisata atau berita bertambah lebih dari beberapa item, menumpuknya di satu scroll bikin landing page berat dan UX-nya buruk (scroll tak berujung). Preview di landing selalu diakhiri tombol "Lihat semua →" ke halaman index masing-masing.

### 4.4 Struktur Organisasi — mengikuti bagan yang diunggah

Bagan menunjukkan hierarki tidak simetris:

```
Kepala Pekon
├── Juru Tulis Pekon (sekretaris)
│   ├── Kepala Urusan Tata Usaha & Umum
│   ├── Kepala Urusan Keuangan
│   └── Urusan Perencanaan
└── (langsung di bawah Kepala Pekon, sejajar dengan Juru Tulis)
    ├── Kepala Seksi Pemerintahan
    ├── Kepala Seksi Kesejahteraan
    └── Kepala Seksi Pelayanan
        ├── Kepala Suku I–VI (6 dusun)
        │   └── (baris berikutnya terpotong di foto — kemungkinan RT, tidak bisa dipastikan)
```

Karena baris paling bawah foto terpotong, struktur database **tidak** dihardcode 3 level seperti di atas — dibuat sebagai adjacency list (`parent_id` self-referencing) supaya admin bebas menambah level RT atau jabatan baru tanpa developer mengubah skema. Detail tabel ada di ARSITEKTUR.md.

## 5. Non-Fungsional

- **SEO** — checklist lengkap di AGENTS.md.
- **Keamanan** — OWASP Top 10, checklist di AGENTS.md.
- **Performa** — hindari N+1 query, indexing, checklist di AGENTS.md + skema di ARSITEKTUR.md.
- **Desain** — putih (utama) + kuning (aksen), gaya modern-minimalis (radius lembut, shadow tipis, micro-interaction secukupnya) — lihat DESIGN.md untuk token lengkap.
- **Responsif** — mobile-first, karena warga kemungkinan besar akses via HP.
- **Bahasa** — Indonesia penuh, termasuk pesan error dan label admin.

## 6. Alur Pengguna Utama

**Warga:**
`Landing page` → scroll ke section yang dicari (atau klik anchor nav) → kalau wisata/berita → klik "Lihat semua" → index → klik item → detail.

**Admin:**
`/admin/login` → dashboard (ringkasan jumlah konten per modul) → pilih menu di sidebar → CRUD → validasi CSRF + hak akses → simpan → redirect dengan notifikasi sukses.

## 7. Di Luar Lingkup (v1)

Supaya tidak scope creep, berikut yang **sengaja tidak** dibangun dulu:
- Sistem pengaduan/aspirasi warga online
- Modul keuangan/APBDes
- Pendaftaran surat-menyurat online (surat pengantar, dst)
- Sistem informasi kependudukan penuh (data per-warga, KTP, KK detail) — yang ada cuma agregat jumlah
- Multi-level approval/reviewer sebelum konten tayang
- Galeri foto umum di luar wisata/berita (bisa ditambah belakangan sebagai tabel `galeri` generik)

## 8. Metrik Keberhasilan (sederhana, realistis untuk situs pekon)

- Admin bisa publish berita baru dalam < 5 menit tanpa bantuan developer.
- Semua 9 fitur wajib tampil dan bisa diedit tanpa error di perangkat mobile.
- Lighthouse SEO + Accessibility score ≥ 90 (pakai skill `lighthouse.md` yang sudah ada).

## 9. Risiko & Pertanyaan Terbuka

| Risiko/Pertanyaan | Status | Dampak kalau diabaikan |
|---|---|---|
| Nama pekon sempat salah (Banjarnegeri → dikoreksi jadi Padang Cermin) | **Selesai**, dikonfirmasi user | — |
| "Padang Cermin" umumnya nama kecamatan (Kab. Pesawaran, Lampung), bukan nama pekon tunggal | [Menebak] diasumsikan memang ada pekon dengan nama ini, bukan tertukar level administratif | Kalau salah, identitas resmi di seluruh situs perlu dikoreksi lagi — cek ke perangkat pekon langsung sebelum situs live |
| Data kependudukan cuma agregat KK+jiwa, tanpa breakdown gender/usia/dusun | Diasumsikan cukup agregat per periode | Kalau ternyata butuh breakdown, tabel `data_kependudukan` perlu 2-3 kolom tambahan — perubahan kecil, bukan redesign |
| Level RT di bawah Kepala Suku tidak terlihat penuh di foto | Ditangani lewat tree fleksibel (§4.4) | Minim, karena skema sudah mengantisipasi |
| Siapa yang boleh jadi admin — 1 akun umum atau per-jabatan? | Diasumsikan 1-2 akun admin umum (operator TU) | Kalau butuh multi-role nanti, tabel `admins.role` sudah disiapkan tapi belum dipakai enforcement-nya |
