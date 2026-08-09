# ARSITEKTUR.md — Struktur Project & Rancangan Database

## 1. Struktur Folder (PHP 8.3 Native, berbasis function — tanpa MVC)

```
/public                      ← document root cPanel arahkan ke sini
  index.php                  ← satu-satunya entry point, routing sederhana di sini
  .htaccess                  ← rewrite semua request ke index.php
  /assets
    /css/tailwind.css        ← hasil build, bukan source
    /css/input.css           ← source Tailwind, di-compile ke tailwind.css
    /js
    /img
/config
  config.php                  ← load .env, define constants, start session
  routes.php                   ← daftar route → [file function, file view] (lihat §2)
  .env                         ← DB_HOST, DB_NAME, DB_USER, DB_PASS, APP_URL (tidak di-commit)
  /logs                        ← error log PHP (error_log di config.php, tidak di-commit)
/function
  db.php                       ← getDb(): PDO singleton, satu koneksi dipakai ulang
  auth.php                     ← isLoggedIn(), requireAdmin(), login(), logout()
  csrf.php                     ← csrfToken(), csrfValidate()
  upload.php                   ← handleUpload(): validasi MIME, rename random, simpan
  helpers.php                  ← slugify(), formatTanggal(), redirect(), dst
  profil.php                   ← getProfil(), updateProfil()
  struktur.php                 ← getStrukturTree(), saveStruktur(), deleteStruktur()
  kependudukan.php              ← getDataKependudukan(), getTrenKependudukan()
  potensi.php                  ← CRUD potensi desa
  wisata.php                   ← CRUD wisata + gambar (query IN, bukan loop per item)
  berita.php                   ← CRUD berita + kategori
  inventaris.php               ← CRUD inventaris (admin-only)
  log.php                       ← catatLog() untuk log_aktivitas
/view
  /landing
    index.php                   ← one-page: include tiap section_*.php di sini
    /partials
      nav.php
      footer.php
      section_visi_misi.php
      section_struktur_organisasi.php
      section_kependudukan.php
      section_potensi.php
      section_wisata.php
      section_berita.php
      section_kontak.php
  /wisata
    index.php                   ← listing
    detail.php
  /berita
    index.php
    detail.php
  /dashboard
    layout.php                  ← shell admin (sidebar, dst), dipanggil tiap halaman dashboard
    login.php
    home.php
    /profil, /struktur, /kependudukan, /potensi, /wisata, /berita, /inventaris
      (index.php, form.php per modul)
/database
  schema.sql                  ← DDL lengkap, source of truth skema
  seeders/seed_admin.php       ← buat akun admin pertama
/uploads                       ← di luar /public atau di dalam dgn proteksi .htaccess
  /wisata /berita /struktur /potensi
tailwind.config.js
composer.json                  ← opsional, cuma untuk vlucas/phpdotenv (load .env)
```

Tidak ada Controller/Model/Class — tiap file di `/function` isi kumpulan function terkait satu modul (procedural), dipanggil langsung dari `index.php` atau dari file view. Ini cukup untuk skala proyek ini (1-2 admin, 9 modul konten) dan lebih gampang dibaca AI agent maupun developer baru dibanding OOP MVC penuh.

**Satu risiko nyata dari pola procedural ini, dan cara menutupnya:** tanpa middleware class, gampang lupa memanggil `requireAdmin()` di salah satu file dashboard — itu langsung jadi lubang broken access control (OWASP A01, lihat AGENTS.md). Jangan andalkan tiap file `/view/dashboard/*` untuk memanggilnya sendiri-sendiri. Cek akses admin **dipusatkan di `index.php`**: sebelum include file view apa pun yang path route-nya diawali `dashboard/` (kecuali `dashboard/login`), panggil `requireAdmin()` di satu tempat itu saja. Satu titik enforcement, tidak bisa kelewat per-file.

## 2. Alur Request

`index.php` baca path dari URL → cocokkan ke `config/routes.php` (array asosiatif: path → `['function' => 'wisata.php', 'view' => 'wisata/index.php']`) → `index.php` include file function yang relevan (isi variabel data lewat pemanggilan function) → include file view yang sesuai (baca variabel yang sudah disiapkan) → HTML terkirim. Kalau path diawali `dashboard/` dan bukan `dashboard/login`, `requireAdmin()` dipanggil dulu sebelum langkah include manapun (lihat catatan risiko di §1).

### 2.1 Alur Request di Lingkungan Lokal (Laragon + nginx)

Di produksi (cPanel) document root diarahkan ke `/public`, jadi `public/index.php` otomatis jadi entry point. Di dev lokal Laragon, root nginx adalah `www/` dan project diakses via URL alias `/kkn-padangcermin/` — mapping-nya diatur lewat file `etc/nginx/alias/kkn-padangcermin.conf` (isi sebagai berikut, **wajib** disesuaikan kalau struktur berubah):

```nginx
# Routing khusus untuk project kkn-padangcermin (dev local Laragon)
# Front controller: public/index.php
# Semua URL di bawah /kkn-padangcermin/ yang bukan file/folder nyata
# diteruskan ke front controller lewat named location (internal redirect).

# Root URL project -> front controller (hindari directory listing)
location = /kkn-padangcermin/ {
    rewrite ^ /kkn-padangcermin/public/index.php last;
}

# File/folder nyata dilewati; sisanya ke front controller
location /kkn-padangcermin/ {
    autoindex off;
    try_files $uri $uri/ /kkn-padangcermin/public/index.php?$query_string;
}

# Blokir akses langsung ke area sensitif (padanan root .htaccess)
location ~ ^/kkn-padangcermin/(config|function|view|docs|Zzz|@agents)/ { deny all; }

# Blokir file & folder tersembunyi (.git, .env, .htaccess, dst)
location ~ ^/kkn-padangcermin/\. { deny all; }
```

Setelah mengubah file ini, reload nginx: `nginx.exe -p <laragon-nginx-dir> -s reload` (jalankan `nginx -t` dulu untuk cek sintaks).

Catatan penting agar routing dev sama dengan prod:
- `APP_BASE` di `.env` harus berisi prefix URL dev (`/kkn-padangcermin`). `public/index.php` memakai `APP_BASE` untuk strip prefix sebelum mencocokkan route — di produksi nilainya kosong (`''`) sehingga path dipakai apa adanya.
- Folder fungsi wajib bernama `/function` (tunggal) persis seperti di §1 — `public/index.php` me-require path `function/...` secara hardcode. Folder `functions/` (jamak) akan bikin fatal error.
- Eksekusi PHP di nginx ditangani blok `location ~ \.php$` di `00-default.conf` (fastcgi ke `php_upstream`) — konfigurasi Laragon default, tidak perlu diubah.
- `.htaccess` **tidak dibaca nginx**; blokir akses area sensitif di dev ditangani oleh dua `location` deny di atas (padanan aturan di root `.htaccess` untuk Apache/cPanel).

## 3. Skema Database

### `admins`
| Kolom | Tipe | Ket |
|---|---|---|
| id | INT PK AI | |
| username | VARCHAR(50) UNIQUE | |
| password_hash | VARCHAR(255) | bcrypt/argon2id |
| nama | VARCHAR(100) | |
| role | ENUM('admin') | disiapkan untuk multi-role di masa depan, belum di-enforce v1 |
| created_at, updated_at | TIMESTAMP | |

### `profil_desa` (single-row config)
| Kolom | Tipe | Ket |
|---|---|---|
| id | INT PK | selalu 1 baris |
| nama_pekon | VARCHAR(100) | |
| visi | TEXT | |
| misi | TEXT | disimpan sebagai list, satu poin per baris |
| sambutan_kepala_pekon | TEXT NULL | opsional, section publik disembunyikan kalau NULL |
| foto_kepala_pekon | VARCHAR(255) NULL | |
| alamat_kantor | VARCHAR(255) | |
| latitude, longitude | DECIMAL(10,7) | untuk embed Google Maps |
| telepon, email, whatsapp | VARCHAR(50) NULL | |
| updated_at | TIMESTAMP | |

### `struktur_organisasi`
| Kolom | Tipe | Ket |
|---|---|---|
| id | INT PK AI | |
| parent_id | INT NULL, FK → struktur_organisasi.id | NULL = root (Kepala Pekon) |
| nama | VARCHAR(100) | |
| jabatan | VARCHAR(100) | |
| pendidikan_terakhir | VARCHAR(20) NULL | SLTA/D3/S1 dst |
| foto | VARCHAR(255) NULL | |
| tampil_di_kontak | BOOLEAN DEFAULT 0 | dipakai ulang untuk section "Kontak Person" — hindari duplikasi data |
| urutan | INT DEFAULT 0 | urutan antar sibling |
| created_at, updated_at | TIMESTAMP | |

*Kenapa adjacency list, bukan hardcode level:* bagan sumber terpotong di baris paling bawah (kemungkinan RT), jadi jumlah level tidak pasti. Model self-referencing bebas menampung berapa pun level tanpa migrasi skema baru.

### `data_kependudukan`
| Kolom | Tipe | Ket |
|---|---|---|
| id | INT PK AI | |
| periode | VARCHAR(20) UNIQUE | mis. `2026-04` |
| jumlah_kk | INT | |
| jumlah_jiwa | INT | |
| jumlah_laki | INT DEFAULT 0 | breakdown L/P opsional tapi diisi (landing memakai komposisi) |
| jumlah_perempuan | INT DEFAULT 0 | |
| keterangan | VARCHAR(255) NULL | |
| updated_at | TIMESTAMP | |

*Catatan:* agregat total per periode. Rincian per dusun ada di `kependudukan_dusun` (dibawah) — total di sini harus = Σ per dusun.

### `kependudukan_dusun` (rincian per dusun per periode)
| Kolom | Tipe | Ket |
|---|---|---|
| id | INT PK AI | |
| periode | VARCHAR(20) | wajib sama dengan periode di `data_kependudukan` |
| nama_dusun | VARCHAR(100) | |
| jumlah_laki | INT DEFAULT 0 | |
| jumlah_perempuan | INT DEFAULT 0 | |
| jumlah_kk | INT DEFAULT 0 | |
| jumlah_jiwa | INT DEFAULT 0 | |
| updated_at | TIMESTAMP | |

*Catatan:* UNIQUE `(periode, nama_dusun)` — 1 baris per dusun per periode. Dusun: Padang Cermin, Tanjung Mas, Rawa Subur, Rawa Tunggal, Kejadian, Kecapi, Bambu Kuning, Lubuk Bakak.

### `potensi_desa`
id PK, judul, deskripsi TEXT, gambar, kategori VARCHAR NULL, urutan INT, status ENUM('aktif','nonaktif'), created_at, updated_at

### `wisata_desa`
id PK, nama, slug UNIQUE, deskripsi TEXT, alamat, latitude/longitude NULL, harga_tiket VARCHAR NULL, jam_buka VARCHAR NULL, status ENUM('draft','publish'), created_at, updated_at

### `wisata_gambar`
id PK, wisata_id FK → wisata_desa.id, path_gambar, urutan INT

### `berita_kategori`
id PK, nama, slug UNIQUE

### `berita_desa`
id PK, judul, slug UNIQUE, kategori_id FK NULL → berita_kategori.id, konten TEXT (HTML dari editor, sanitasi wajib — lihat AGENTS.md A08), gambar_utama, penulis_id FK → admins.id, status ENUM('draft','publish'), views INT DEFAULT 0, published_at DATETIME NULL, created_at, updated_at

### `inventaris` (admin-only, tidak tampil publik)
id PK, kode_barang VARCHAR UNIQUE, nama_barang, kategori VARCHAR, jumlah INT, satuan VARCHAR(20), kondisi ENUM('baik','rusak_ringan','rusak_berat'), lokasi_penyimpanan VARCHAR, tahun_perolehan YEAR NULL, keterangan TEXT NULL, created_at, updated_at

### `log_aktivitas`
id PK, admin_id FK → admins.id, aksi VARCHAR(100), tabel_terkait VARCHAR(50), data_id INT NULL, waktu TIMESTAMP

## 4. Relasi Antar Tabel

```
struktur_organisasi.parent_id  → struktur_organisasi.id   (self, tree)
wisata_gambar.wisata_id        → wisata_desa.id
berita_desa.kategori_id        → berita_kategori.id  (nullable)
berita_desa.penulis_id         → admins.id
log_aktivitas.admin_id         → admins.id
```

## 5. Index Wajib

| Tabel | Kolom | Tipe index | Alasan |
|---|---|---|---|
| struktur_organisasi | parent_id | INDEX | query tree by parent |
| struktur_organisasi | tampil_di_kontak | INDEX | filter section kontak person |
| wisata_desa | slug | UNIQUE | lookup detail by slug, cegah duplikat |
| wisata_desa | status | INDEX | filter publish-only di publik |
| wisata_gambar | wisata_id | INDEX | join/lookup gambar per wisata |
| berita_desa | slug | UNIQUE | lookup detail by slug |
| berita_desa | status, published_at | COMPOSITE INDEX | listing terurut + filter publish |
| berita_desa | kategori_id | INDEX | filter per kategori |
| data_kependudukan | periode | UNIQUE | 1 baris per periode, query tren terurut |
| kependudukan_dusun | (periode, nama_dusun) | UNIQUE | 1 baris per dusun per periode |
| kependudukan_dusun | periode | INDEX | filter/lookup seluruh dusun per periode |
| inventaris | kategori | INDEX | filter di dashboard admin |
| inventaris | kondisi | INDEX | filter/laporan kondisi barang |

## 6. Konvensi Penamaan

- Tabel & kolom: `snake_case`
- Function PHP: `camelCase`, diberi prefix modul kalau ambigu antar file (`getWisataBySlug()`, bukan `getBySlug()` generik yang bisa bentrok nama antar file `/function`)
- File di `/function` dan `/view`: `snake_case.php`, satu file = satu modul/section
- Nama variabel data yang dikirim ke view: samakan dengan nama tabel tunggal (`$wisata`, `$beritaList`) supaya jelas dari view mana asalnya tanpa buka file function-nya

## 7. Environment (`.env`)

```
DB_HOST=localhost
DB_NAME=padang_cermin_db
DB_USER=
DB_PASS=
APP_URL=https://pekonpadangcermin.id
APP_BASE=
APP_ENV=production
```

- `APP_BASE`: prefix URL saat project diakses dari subpath (contoh dev lokal: `/kkn-padangcermin`). Dipakai `public/index.php` untuk strip prefix sebelum pencocokan route (lihat §2.1). **Kosong** di produksi cPanel (docroot = `/public`).

## 8. Catatan Migrasi Data Lama

Kalau ada data yang sebelumnya sempat disimpan sebagai JSON flat file (dari eksperimen arsitektur awal), tulis satu skrip migrasi sekali-jalan (`database/migrate_json_to_mysql.php`) yang baca file JSON lama dan insert ke tabel terkait lewat PDO — bukan bagian dari alur aplikasi normal, jalankan manual sekali lalu buang skripnya.
