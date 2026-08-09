# BRIEF STANDAR INTERAKSI UI/UX, AJAX, DAN KEAMANAN (OWASP TOP 10)
**Workspace AI Agent System**

---

## 1. Modals & Toast Notifications Standard

### 1.1 Modal Aksi (Tambah, Edit, Hapus)
- **Penggunaan Modal:** Seluruh operasi pembaruan data (Tambah/Create, Edit/Update, Hapus/Delete) pada area Admin **WAJIB** menggunakan Modal (Bootstrap JS 5 + Tailwind styling).
- **Modal Konfirmasi Hapus:** Sebelum memicu pemanggilan AJAX Delete, wajib tampil Modal Konfirmasi yang menyebutkan nama/judul spesifik entitas yang akan dihapus (contoh: *"Apakah Anda yakin ingin menghapus data ini?"*).
- **Behavior Modal:**
  - Menutup otomatis setelah aksi AJAX berhasil.
  - Form di dalam modal di-reset secara bersih (*clear inputs & validation states*) saat modal dibuka kembali.
  - Memiliki tombol silang (×) di sudut kanan atas header modal dan tombol *"Batal"*.

### 1.2 Toast Notification Format Standard
Seluruh respon aksi (berhasil maupun error) dari server **WAJIB** ditampilkan menggunakan Toast Notification di sudut kanan atas layar (`fixed top-4 right-4 z-50`):

- **Toast Berhasil (Success Toast):**
  - **Auto-dismiss:** Otomatis menghilang setelah jeda **2 detik**.
  - **Progress Slider:** Memiliki indikator garis waktu (*progress slider bar*) di bagian bawah toast yang bergerak (menyusut 100% → 0%) menyesuaikan dengan jeda waktu 2 detik sebelum toast hilang.
  - **Visual:** Warna aksen hijau/emerald, ikon centang sukses, dan pesan detail dari server (contoh: *"Data berhasil disimpan."*).

- **Toast Gagal / Error (Error Toast):**
  - **No Auto-dismiss:** **TIDAK HILANG OTOMATIS**. Toast akan tetap muncul sampai pengguna menutupnya secara manual.
  - **Tombol Close (×):** Wajib dilengkapi tombol silang (×) yang jelas untuk menutup toast.
  - **Visual:** Warna aksen merah/rose, ikon peringatan, dan keterangan detail penyebab error yang dikirim dari backend (contoh: *"Gagal mengunggah foto: Ukuran file melebihi batas maksimal 2MB."*).

---

## 2. Standar Upload File & Manajemen Media

### 2.1 Batasan Ukuran & Format File
- **Foto:** Maksimal **2MB**. Ekstensi yang didukung: `jpg`, `jpeg`, `png`, `webp`, `gif`, `ico`, `heic`, `heif`.
- **Video:** Maksimal **15MB**. Ekstensi yang didukung: `mp4`, `mkv`, `mov`, `webm`.
- **Larangan URL Eksternal:** Dilarang menyimpan atau menggunakan URL gambar mentah dari luar (*external image URL*). Seluruh gambar wajib diunggah dan disimpan secara lokal di `/public/uploads/`.

### 2.2 Validasi & Keamanan Upload Server-Side
- **MIME Type Validation:** Validasi ekstensi HTML5 (`accept="..."`) saja TIDAK CUKUP. Backend PHP **WAJIB** mengecek MIME type asli file menggunakan `finfo_file(finfo_open(FILEINFO_MIME_TYPE), $_FILES['file']['tmp_name'])`.
- **Penanganan Format HEIC/HEIF & MKV:**
  - File foto dari perangkat Apple (HEIC/HEIF) dan video MKV memerlukan penanganan khusus agar dapat dirender dengan benar di browser desktop/HTML5.
  - Backend PHP harus mengonversi gambar HEIC/HEIF ke format JPG/WebP via Imagick/GD jika tersedia, atau memberikan pemberitahuan validasi awal.
- **Nama File Unik & Hash:** Setiap file yang diunggah wajib di-rename menggunakan string unik hash acak (`md5(uniqid(microtime(), true)) . '.' . $extension`) untuk mencegah penimpaan file dan serangan *directory traversal*.
- **Perlindungan Folder Uploads:** Eksekusi script PHP di dalam folder `/public/uploads/` wajib dimatikan menggunakan file `.htaccess` berisi `php_flag engine off`.

---

## 3. Lightbox Media Preview Modal

### 3.1 Pratinjau Foto & Video
- Seluruh media foto dan video—baik di halaman publik (Beranda, Galeri, Modul Konten) maupun di Dashboard Admin (tabel & form)—**wajib bisa diklik** untuk menampilkan **Modal Preview (Lightbox)**.
- **Tampilan Modal Preview:**
  - Ukuran modal large/full-screen dengan *backdrop overlay* gelap berkaca (*dark glassmorphism*).
  - Terdapat tombol silang (×) berwarna kontras di pojok kanan atas untuk menutup.
  - Menutup modal dapat dilakukan via klik tombol ×, klik luar area modal (backdrop), atau menekan tombol `ESC` keyboard.
  - Gambar ditampilkan utuh (*aspect-ratio preserved*); video diputar menggunakan tag native `<video controls class="w-full max-h-[80vh] rounded">`.

---

## 4. Spesifikasi Carousel

- **Interval Auto-Slide:** Tepat **1.5 detik** (`data-bs-interval="1500"`).
- **Tombol Navigasi:** Tombol *Previous* (<) dan *Next* (>) wajib selalu terlihat (*visible*) dan dapat diakses dengan jelas.
- **Pause-on-Hover (Aksesibilitas WCAG 2.2.2):** *Pause-on-hover* **TIDAK BOLEH DIMATIKAN**. Saat kursor diarahkan ke atas carousel, jalannya alur slide akan berhenti sementara agar pengguna sempat membaca konten/teks.

---

## 5. Standar Tabel AJAX & Data Interaction (Admin)

### 5.1 Protokol AJAX POST Only
- **Protokol:** Seluruh transaksi data tabel admin (list data, pagination, live search, filter, store, update, delete) **WAJIB menggunakan HTTP POST Method** (bukan GET).
- **Security Header:** Membawa CSRF Token di dalam payload body POST `_token` atau HTTP Header `X-CSRF-TOKEN`.

### 5.2 Fitur Tabel Wajib
1. **Live Search:** Pencarian instan berbasis AJAX POST dengan jeda *debounce* 300ms.
2. **AJAX Filtering:** Filter kategori/status terintegrasi.
3. **Pagination Prev/Next:** Navigasi halaman AJAX tanpa reload seluruh halaman (*full page refresh*).
4. **Kolom Aksi (Icon Buttons):** Kolom terakhir berisi tombol aksi berupa **Icon Button** (bukan teks link):
   - Ikon Mata (👁️) / Detail: Membuka modal detail info.
   - Ikon Pensil (✏️) / Edit: Membuka modal edit data.
   - Ikon Tempat Sampah (🗑️) / Hapus: Membuka modal konfirmasi hapus.

---

## 6. Loading States (Lazy Loading Spinner & Skeleton Loading)

- **Teks / Data Tabel JSON:** Saat AJAX sedang mengambil atau memperbarui data teks, tampilkan **Lazy Loading Spinner** (animasi putar halus) di tengah tabel atau kontainer data.
- **Media Foto & Video:** Gunakan **Skeleton Loading** (efek pulsa abu-abu dengan dimensi aspek rasio yang persis sama dengan media asli) sebelum media foto/video selesai dimuat sempurna. Hal ini wajib diterapkan untuk mencegah *Cumulative Layout Shift* (CLS).

---

## 7. Multi-Filter & Reset Filter

- **Tombol Reset Filter:** Jika suatu halaman atau tabel memiliki **lebih dari 1 kriteria filter** (misalnya Filter Kategori + Filter Status + Filter Tanggal), **WAJIB menyertakan tombol "Reset Filter"**.
- **Perilaku Reset Filter:**
  - Tombol "Reset Filter" otomatis aktif/muncul ketika ada 1 atau lebih filter yang diubah dari nilai bawaan.
  - Saat diklik, seluruh input/select filter kembali ke posisi default (kosong/semua) dan memicu pemanggilan AJAX re-fetch data dari halaman 1.
- **Large Image Column Preview:** Jika kolom tabel menampilkan foto/gambar mini (*thumbnail*), gambar tersebut harus dapat diklik untuk membuka modal preview berukuran *Large* yang dilengkapi tombol silang (×).

---

## 8. Sidebar Admin Collapsible & Persistensi Status

### 8.1 Hamburger Toggle & Icon-Only Mode
- Memiliki tombol Hamburger Menu di bagian topbar/header admin untuk menyembunyikan (*hide*) atau menampilkan (*show*) sidebar.
- **Collapsed Mode (Hide State):** Saat sidebar disembunyikan, sidebar tidak hilang total pada tampilan desktop, melainkan mengecil menjadi **Icon-Only Mode** (lebar ~64px) di mana ikon-ikon navigasi tetap terlihat.
- **Mobile Mode:** Pada tampilan mobile (<768px), sidebar disembunyikan sepenuhnya ke luar layar dan muncul sebagai *slide-over drawer* dengan backdrop overlay saat tombol hamburger diklik.

### 8.2 Persistensi Status (Anti-FOUC)
- Status kelipat (collapsed/expanded) wajib disimpan di `localStorage` (misal `admin_sidebar_collapsed = 'true'`).
- Script pembaca `localStorage` ditempatkan sebelum elemen `<body>` dirender agar posisi sidebar langsung disesuaikan tanpa terjadinya *Flash of Unstyled Content* (FOUC/flicker) saat berpindah halaman admin.

---

## 9. Responsif & Mobile-Friendly Admin

- Seluruh layout dan komponen dashboard admin wajib **100% Responsive & Mobile-Friendly**.
- Tabel admin wajib dibungkus kontainer `overflow-x-auto` agar dapat di-scroll horizontal secara mulus pada layar HP, atau menggunakan layout *responsive card* pada breakpoint seluler (<640px).
- Ukuran area klik (*tap target*) untuk tombol aksi dan menu minimal berukuran **44x44px** untuk kenyamanan penggunaan perangkat sentuh (*touchscreen*).

---

## 10. SEO Meta Tags (Halaman Publik vs Admin)

### 10.1 Halaman Publik (Beranda, Konten Publik, dll)
Seluruh halaman publik yang di-render oleh PHP wajib memuat meta tag SEO lengkap secara dinamis pada bagian `<head>`:
- `<title>... - Official Site</title>`
- `<meta name="description" content="...">`
- `<meta name="keywords" content="...">`
- **Open Graph (Facebook/WhatsApp/LinkedIn):** `<meta property="og:title">`, `<meta property="og:description">`, `<meta property="og:image">`, `<meta property="og:url">`.
- **Twitter Card:** `<meta name="twitter:card" content="summary_large_image">`.
- `<link rel="canonical" href="...">`

### 10.2 Halaman Admin & Login
Halaman Login Admin (`/admin/login.php`) dan Dashboard Admin (`/admin/*`) **TIDAK BOLEH** di-index oleh mesin pencari. Wajib memuat meta tag:
```html
<meta name="robots" content="noindex, nofollow">
```

---

## 11. Implementasi Keamanan OWASP Top 10

Seluruh pengkodean backend dan frontend wajib mematuhi standar **OWASP Top 10**:

1. **A01: Broken Access Control:**
   - Seluruh endpoint AJAX admin (`/admin/ajax/*.php`) wajib memeriksa ketersediaan sesi valid (`session_start()`, `isset($_SESSION['admin'])`). Jika tidak valid, kembalikan HTTP `401 Unauthorized`.
2. **A02: Cryptographic Failures:**
   - Kata sandi admin wajib dienkripsi menggunakan `password_hash()` (Bcrypt/Argon2ID). Kredensial rahasia disimpan di luar folder publik (`/secure/admin_credentials.json`).
3. **A03: Injection (SQL/JSON & Command):**
   - Sanitasi seluruh input pengguna menggunakan `htmlspecialchars()` dan filter variabel server-side.
   - Penulisan berkas JSON wajib mengunci berkas secara eksklusif menggunakan `flock($fp, LOCK_EX)` untuk mencegah *race condition* dan korupsi data.
4. **A04: Insecure Design:**
   - Setiap operasi overwrite data JSON wajib melakukan backup otomatis berkas lama ke `/secure/backup/` terlebih dahulu.
5. **A05: Security Misconfiguration:**
   - Matikan `display_errors` pada lingkungan production.
   - Nonaktifkan eksekusi PHP di folder `/public/uploads/` via `.htaccess`.
6. **A06: Vulnerable and Outdated Components:**
   - Gunakan versi library terverifikasi (Bootstrap JS 5 lokal & Tailwind CSS Browser CDN v4 resmi).
7. **A07: Identification and Authentication Failures:**
   - Terapkan pembatasan percobaan login (*rate limiting*) dan regenerasi ID sesi (`session_regenerate_id(true)`) setelah login berhasil.
8. **A08: Software and Data Integrity Failures:**
   - Terapkan validasi skema data dan whitelist entitas sebelum menerima atau menyimpan data dari payload POST.
9. **A09: Security Logging and Monitoring Failures:**
   - Catat setiap kegagalan login dan aktivitas penting perubahan data ke berkas log aman di `/secure/logs/`.
10. **A10: Server-Side Request Forgery (SSRF) & Anti-CSRF:**
    - Wajib memvalidasi Token CSRF di setiap pemrosesan form POST dan AJAX write endpoint.

---

## 12. Robustness, Handling Error, & Aksesibilitas Tambahan

### 12.1 Handling Error Network / Server Crash (HTTP 500 / Timeout)
- Jika pemanggilan AJAX POST mengalami kegagalan koneksi (*network error*, server timeout, atau HTTP status 500 Internal Server Error):
  - Frontend **WAJIB** menangkap error tersebut dalam blok `.catch()` / handler error.
  - Hentikan seluruh spinner / skeleton loading.
  - Tampilkan **Error Toast Notification** yang berisi pesan tegas: *"Gagal terhubung ke server atau terjadi kesalahan internal. Periksa koneksi internet Anda dan coba lagi."*

### 12.2 Broken Image / Fallback Photo Handling
- Jika berkas foto tidak ditemukan di folder `/uploads/` (*broken link / 404*):
  - Elemen `<img>` **WAJIB** menggunakan atribut `onerror="this.onerror=null; this.src='/assets/images/placeholder.webp';"` untuk menampilkan gambar *placeholder default* lokal.
  - Penggunaan atribut `loading="lazy"` disarankan pada seluruh gambar publik untuk menghemat bandwidth.

### 12.3 Auto Timeout & Handling Expiration Sesi Admin
- Sesi login admin otomatis berakhir setelah **60 menit** inaktivitas.
- Jika admin mencoba mengirimkan form / aksi AJAX saat sesi telah habis:
  - Backend merespons dengan HTTP status `401 Unauthorized` dan JSON `{"success": false, "message": "Sesi login Anda telah berakhir. Silakan login kembali."}`.
  - Frontend menampilkan Error Toast dan secara otomatis mengarahkan admin ke halaman `/admin/login.php` setelah 2 detik.

### 12.4 Keyboard Accessibility (Focus Trap)
- Saat modal dibuka (preview, tambah, edit, hapus), fokus keyboard (tombol `Tab`) dikunci di dalam area modal (*focus trap*) dan secara otomatis mengarah pada input pertama atau tombol ×.
- Saat modal ditutup via tombol `ESC` atau tombol silang, fokus keyboard dikembalikan ke tombol pemicu awal.

---
*Dokumen brief ini menjadi acuan mutlak pengembangan antarmuka, AJAX, dan keamanan sistem.*
