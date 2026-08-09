# AGENTS.md — Panduan untuk AI Coding Agent

Dokumen ini untuk agent (Claude Code/OpenCode/dst) yang mengerjakan proyek ini. Baca ini sebelum menulis kode apa pun.

## 0. Dokumen Wajib Dibaca Sebelum Menulis Kode

Di awal sesi kerja, AI **wajib** membaca dokumen-dokumen berikut secara berurutan **SEBELUM** menulis kode apa pun:

1. `AGENTS.md` (file ini) — aturan kerja, SEO, keamanan, performa, dan Definition of Done
2. `docs/ARSITEKTUR.md` — struktur folder project, alur request, skema database, index wajib, konvensi penamaan
3. `docs/DESIGN.md` — sistem desain (token warna, tipografi, komponen, motion, aksesibilitas)
4. `docs/PRD.md` — Product Requirements Document: lingkup fitur, target pengguna, non-fungsional
5. `docs/BRIEF_STANDAR_INTERAKSI_DAN_KEAMANAN.md` — standar interaksi UI/UX, AJAX, dan keamanan (modal, toast, upload, tabel AJAX, OWASP)

Jika ada konflik antar dokumen, `AGENTS.md` ini yang menang; `docs/ARSITEKTUR.md`, `docs/DESIGN.md`, `docs/PRD.md`, dan `docs/BRIEF_STANDAR_INTERAKSI_DAN_KEAMANAN.md` adalah referensi detail yang saling melengkapi.

## 1. Tech Stack (wajib, jangan diganti tanpa alasan kuat)

- **Backend**: PHP 8.3 native, prosedural terorganisir per modul (folder `/function` + `/view`, tanpa OOP MVC, tanpa framework Laravel/Symfony/dll) — rujuk docs/ARSITEKTUR.md §1
- **Database**: MySQL 8, akses lewat PDO — **tidak ada** query mentah tanpa prepared statement
- **CSS**: Tailwind CSS via standalone CLI (compile sekali sebelum deploy, bukan runtime/CDN version di production)
- **Chart**: Chart.js via CDN untuk grafik data kependudukan (lihat docs/DESIGN.md §3)
- **Deploy**: FTP manual ke cPanel — build asset lokal dulu (`tailwindcss --minify`), jangan pernah build di server produksi
- **Struktur project**: rujuk docs/ARSITEKTUR.md, jangan improvisasi folder baru tanpa dicatat di sana

## 2. Aturan Pembacaan File

### 2.1 Aturan Pembacaan Folder Skill (`@agents/AI-SKILLS/`)

AI **DILARANG KERAS** membaca secara otomatis (*auto-read*) seluruh file di folder-folder skill raksasa berikut:
- 🚫 `@agents/AI-SKILLS/ECC_SKILLS`
- 🚫 `@agents/AI-SKILLS/SUPERPOWERS`
- 🚫 `@agents/AI-SKILLS/TASTE_SKILLS`
- 🚫 `@agents/AI-SKILLS/UIUX_SKILLS`
- 🚫 `@agents/AI-SKILLS/IMPECCABLE_SKILLS`
- 🚫 `@agents/AI-SKILLS/AGENT_SKILLS`

> **⚠️ ATURAN MUTLAK:**
> AI **HANYA DIIZINKAN** membaca file skill dari folder-folder di atas **JIKA DAN HANYA JIKA** pengguna (*user*) menyebutkan atau meminta skill tersebut secara **EKSPLISIT** dalam pesan prompt.

#### Ringkasan Fungsi Folder Skill (Referensi Pembacaan Eksplisit):

- **📁 PERSONA_SKILLS**: Kumpulan persona dan gaya komunikasi AI.
  - `CAVEMAN_SKILL.md`: Mode komunikasi sangat singkat dan padat (hemat token).
  - `PONYTAIL_SKILL.md`: Mode "Senior Dev Malas", memprioritaskan solusi paling simpel, bersih, dan menolak *over-engineering* (YAGNI).
  - `KOMENTAR_ORANG_TUA.md`: (Pengecualian) Jangan dibaca/diterapkan kecuali diminta secara eksplisit oleh pengguna.

- **📁 TOOL_SKILLS**: Panduan integrasi dan penggunaan alat (tools/MCP) pendukung (dibaca hanya file spesifik saat tool digunakan).
  - `CODEBASE_MEMORY.md`: Digunakan saat membutuhkan pemetaan struktur repository.
  - `CONTEXT7.md`: Digunakan untuk mencari dokumentasi resmi teknologi utama proyek.
  - `FIRECRAWL.md`: Digunakan untuk *web scraping* atau mencari dokumentasi dari sumber luar.
  - `RTK_SKILL.md`: Panduan inisialisasi Repository Toolkit (RTK) demi optimasi token terminal.
  - `BROWSER_USE_SKILL.md`: Panduan penggunaan library `browser-use` untuk otomasi browser.
  - `GRAPHIFY_SKILL.md`: Panduan `graphify` untuk Knowledge Graph (beserta dokumentasi teknis di `TOOL_SKILLS/graphify/`).

- **📁 WORKFLOW_SKILLS**: Aturan standar operasional, alur kerja, dan lingkungan kerja AI.
  - `AGENTS-GENERAL.md` / `WORKFLOW_MODE.md` / `AI_SETUP_MACHINE.md`: Aturan alur kerja & environment.
  - `CICD_SSH_SKILL.md`: Prosedur wajib untuk setup otomatis CI/CD menggunakan SSH.

- **📁 SUPERPOWERS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - `SUPERPOWERS_SKILLS.md`: Kumpulan *skill* modular untuk *brainstorming*, *planning*, dan *debugging*.

- **📁 AGENT_SKILLS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - Kumpulan *skill* engineering (TDD, CI/CD, Code Review, SDD, Debugging, Refactoring, dsb).

- **📁 TASTE_SKILLS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - Kumpulan *skill* (`brandkit`, `minimalist-skill`, dll) untuk merancang antarmuka (UI/UX) premium.

- **📁 ECC_SKILLS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - Kumpulan masif ratusan *skill* modular (frontend, backend, security, DevOps).

- **📁 IMPECCABLE_SKILLS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - *Skill* sistem desain (*design tokens*, tipografi, spasi) untuk konsistensi antarmuka.

- **📁 UIUX_SKILLS**: *(DIBACA HANYA JIKA USER MEMINTA EKSPLISIT)*
  - Koleksi skill UI/UX lengkap (84 gaya visual, 192 palet warna, 74 font pairing, 98 UX guide, 16 GSAP preset).

### 2.2 Aturan Pembacaan Dokumen Panduan (`docs/`)

AI **wajib** membaca panduan dari file dan subfolder di dalam `docs/` berikut:
- `docs/PRD.md`
- `docs/DESIGN.md`
- `docs/BRIEF_STANDAR_INTERAKSI_DAN_KEAMANAN.md`
- File panduan lain yang ada di `docs/` (kecuali subfolder yang dilarang di bawah)

**DILARANG KERAS** membaca subfolder berikut di dalam `docs/`:
1. `docs/BRIEF SAAT DEPLOY/` (Folder brief saat deploy - JANGAN DIBACA)
2. `docs/PROJECT LAIN (CONTOH)/` (Folder project lain - JANGAN DIBACA)
3. `docs/DATA KLIEN/` (Folder data klien - JANGAN DIBACA)

### 2.3 Aturan Pembacaan README Utama

- **DILARANG KERAS** membaca file `README.md` yang berada di root project karena itu hanya panduan untuk instalasi & deployment saja (JANGAN DIBACA). Gunakan dokumen panduan `docs/` yang diperbolehkan di atas untuk referensi teknis.

## 3. SEO — checklist wajib tiap halaman publik

- [ ] `<title>` unik per halaman (landing beda dari `/wisata`, beda lagi dari tiap `/wisata/{slug}`)
- [ ] `<meta name="description">` unik, diambil dari ringkasan konten (potong ~155 karakter)
- [ ] Open Graph + Twitter Card (`og:title`, `og:description`, `og:image` pakai gambar utama konten)
- [ ] JSON-LD structured data: `GovernmentOrganization` untuk profil pekon di landing, `TouristAttraction` untuk tiap halaman detail wisata, `NewsArticle` untuk tiap detail berita
- [ ] `sitemap.xml` di-generate otomatis dari tabel `wisata_desa` + `berita_desa` (slug + `updated_at`), bukan ditulis manual
- [ ] `robots.txt` mengizinkan crawl halaman publik, blokir `/admin/`
- [ ] URL slug bersih via `.htaccess` rewrite — `/wisata/curug-embun`, bukan `/wisata.php?id=12`
- [ ] Alt text gambar **wajib diisi** — validasi di form admin (tidak boleh submit tanpa alt text), bukan cuma disarankan
- [ ] Satu `<h1>` per halaman, hierarki heading tidak loncat level
- [ ] Gambar dikompres & di-lazy-load (`loading="lazy"`) kecuali gambar hero pertama

## 4. Keamanan — OWASP Top 10 (2021), checklist per kategori

| # | Kategori | Tindakan konkret di proyek ini |
|---|---|---|
| A01 | Broken Access Control | Middleware cek session admin di **setiap** route `/admin/*` di level Router, bukan cuma sembunyikan menu di view. Validasi bahwa ID yang diedit memang ada sebelum proses update/delete. |
| A02 | Cryptographic Failures | `password_hash()` (bcrypt/argon2id) untuk password admin. Kredensial DB di `.env`, **tidak** di-commit ke repo. Force HTTPS lewat redirect di `.htaccess`. |
| A03 | Injection | 100% PDO prepared statement. Tidak ada `$sql = "... WHERE id=$id"`. Sanitasi & validasi input di server-side, jangan andalkan validasi JS saja. |
| A04 | Insecure Design | Rate-limit percobaan login admin (misal: 5x gagal → lock 15 menit, simpan counter di DB/session). |
| A05 | Security Misconfiguration | `display_errors=Off` di produksi (log ke file, bukan tampil ke user). Disable directory listing (`Options -Indexes`). Hapus file installer/test sebelum deploy final. |
| A06 | Vulnerable Components | Library via CDN pakai versi terkunci (misal `chart.js@4.4.0`), bukan `@latest`. |
| A07 | Identification & Auth Failures | `session_regenerate_id(true)` setelah login sukses. Cookie sesi: `httponly`, `secure`, `samesite=Lax`. |
| A08 | Software & Data Integrity Failures | Validasi file upload: cek MIME asli (`finfo`), bukan cuma ekstensi. Rename file ke nama random saat simpan. Folder `/uploads` di-set non-executable PHP (`php_flag engine off` via `.htaccess` di folder itu). |
| A09 | Logging & Monitoring Failures | Tabel `log_aktivitas` sederhana: `admin_id`, `aksi`, `tabel_terkait`, `waktu` — dicatat tiap create/update/delete di admin. |
| A10 | SSRF | Tidak relevan di v1 (tidak ada fitur fetch URL dari input user). Kalau nanti ditambah fitur ambil gambar dari URL eksternal, whitelist domain dulu sebelum fetch. |

## 5. Performa — hindari N+1 query & pastikan indexing benar

**Contoh N+1 yang harus dihindari di proyek ini:**

```php
// SALAH — 1 query wisata + N query gambar (N+1)
$wisataList = $db->query("SELECT * FROM wisata_desa")->fetchAll();
foreach ($wisataList as $w) {
    $gambar = $db->query("SELECT * FROM wisata_gambar WHERE wisata_id = {$w['id']}")->fetchAll();
}

// BENAR — 2 query total, digabung di PHP pakai array key
$wisataList = $db->query("SELECT * FROM wisata_desa")->fetchAll();
$ids = array_column($wisataList, 'id');
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $db->prepare("SELECT * FROM wisata_gambar WHERE wisata_id IN ($placeholders)");
$stmt->execute($ids);
// group by wisata_id di PHP, lalu merge ke $wisataList
```

**Struktur organisasi**: fetch **semua** baris `struktur_organisasi` sekali (`SELECT * FROM struktur_organisasi ORDER BY urutan`), lalu bangun tree di memori PHP pakai reference array berdasarkan `parent_id` — jangan query berulang per level/per node.

**Indexing wajib** (detail per tabel ada di docs/ARSITEKTUR.md §5), ringkasnya:
- Semua foreign key (`parent_id`, `wisata_id`, `kategori_id`, dst) diberi index.
- Kolom yang dipakai `WHERE`/`ORDER BY` sering (`slug`, `status`, `published_at`, `periode`) diberi index atau `UNIQUE` kalau relevan.

**Pagination wajib** di `berita_desa` dan `inventaris` — tidak ada `SELECT *` tanpa `LIMIT` begitu data sudah puluhan baris.

## 6. Definition of Done — tiap fitur/modul admin dianggap selesai kalau:

- [ ] CRUD berfungsi + validasi server-side (bukan cuma `required` di HTML)
- [ ] CSRF token ada di semua form POST
- [ ] Cek akses admin aktif di route-nya
- [ ] Tidak ada query N+1 (cek manual atau lewat query log)
- [ ] Index database sudah sesuai docs/ARSITEKTUR.md
- [ ] Tampil benar di breakpoint mobile
- [ ] Alt text gambar tervalidasi wajib diisi (untuk modul yang ada upload gambar)

## 7. Aturan Refactoring & Modifikasi Kode Lama

- **DILARANG KERAS** mengubah, merombak (refactor), atau menulis ulang seluruh kode pada file yang sudah ada secara tiba-tiba hanya karena membaca aturan di `AGENTS.md` atau file skill lainnya.
- Aturan penulisan kode **hanya berlaku** untuk kode baru yang sedang ditulis atau fitur baru yang sedang ditambahkan.
- Pengecualian: AI hanya diizinkan melakukan refactor pada kode lama **JIKA DAN HANYA JIKA** pengguna secara eksplisit menginstruksikan atau meminta refactor tersebut.

**Peringatan (setelah baca standar ini):**
- Standar di atas **bukan** undangan untuk rename/refactor file yang sudah ada.
- Kalau file lama belum ikut standar (nama beda, logic di page, modal legacy, dll.) — **biarkan**. Hanya file/fitur **baru** yang wajib ikut.
- Jangan sentuh kode existing kecuali user **minta eksplisit**.

## 8. Inisialisasi Proyek Baru (AUTO-SETUP)

Jika AI mendeteksi bahwa ini adalah proyek atau *workspace* baru, AI **wajib** secara proaktif menjalankan (atau mengingatkan pengguna untuk menjalankan) langkah-langkah inisialisasi *tools* berikut:
1. **RTK (Repository Toolkit):** Jalankan perintah `./@agents/RTK/rtk.exe init` (atau sesuaikan path rtk) di terminal untuk menghasilkan folder `.rtk` (berisi konfigurasi `filters.toml`) dan folder `rules` (berisi aturan `antigravity-rtk-rules.md`).
2. **Context7:** Pastikan integrasi sudah terpasang dengan menjalankan perintah `npx ctx7 setup` untuk autentikasi dan pembuatan *rules* otomatis di dalam agen.
3. **Codebase Memory MCP:** Lakukan pemetaan struktur proyek secara otomatis. Jika server MCP sudah tersambung, AI harus segera memindai (melakukan aksi *"Index this project"*) agar grafik pengetahuan kode terbentuk di memori.
4. **Firecrawl MCP:** Ingatkan pengguna untuk memastikan variabel lingkungan `FIRECRAWL_API_KEY` sudah terpasang jika proyek membutuhkan fitur pencarian web/*scraping* lanjutan.
5. **Graphify:** Jika proyek membutuhkan pemetaan pengetahuan multimodal (dokumen, diagram, riset, & kode), jalankan perintah `graphify .` di terminal root proyek untuk menghasilkan folder `graphify-out/`.

## 9. Manajemen File/Script Sementara

- Simpan semua file eksekusi, script sementara (temporary scripts), atau file uji coba/scratch di dalam folder `Zzz/` yang ada di root proyek.
- DILARANG mengotori root directory atau folder lain dengan script sekali pakai.

## 10. Environment Terminal User

- User menggunakan **CMD (Command Prompt) mode Administrator**, BUKAN PowerShell.
- AI **wajib** memberikan perintah dalam sintaks CMD, bukan PowerShell.
- Contoh perbedaan:
  - PowerShell: `New-Item`, `Remove-Item`, `Move-Item`
  - CMD: `mkdir`, `del`, `move`, `rmdir /s /q`

## 11. Fitur & Standar UI/UX Admin (terkini)

Standar interaksi UI/UX admin mengikuti `docs/BRIEF_STANDAR_INTERAKSI_DAN_KEAMANAN.md`. Ringkasan wajib:

- **Semua tabel index admin = AJAX POST** (`/dashboard/ajax/{modul}/list|detail|delete`) via `public/assets/js/admin.js` → `AdminUI.initAjaxTable()`. Endpoint di `function/ajax.php` (render rows + pagination), view `view/dashboard/ajax.php` (POST-only + CSRF + auth). **DILARANG** menambah tabel index admin yang di-render full server-side tanpa AJAX.
- **Toast**: sukses auto-hide 2 detik + progress slider (CSS `transition width 2s linear` dimulai via `getBoundingClientRect()` reflow setelah append); error tetap tampil + tombol ×. Flash server dirender ke `#flash-data` JSON di `view/dashboard/layout.php`.
- **Modal**: aksi hapus via modal konfirmasi (bukan `confirm()`), detail via `detailModal`. Semua modal punya tombol × (`data-modal-close`), tutup saat klik backdrop & ESC. Modal mendukung callback `onOpen(panel)` yang dipanggil setelah modal ter-append ke DOM — gunakan ini untuk re-init skeleton/lightbox di dalam modal.
- **Upload**: `function/upload.php` — foto max **2 MB** (`jpg/jpeg/png/webp/gif/ico/heic/heif`, cek MIME finfo), video max **15 MB** (`mp4/mkv/mov/webm`) via `handleUploadVideo()`. **Tidak ada kolom URL foto** — semua gambar wajib diunggah lokal.
- **Lightbox media**: atribut `data-lightbox="URL_FOTO"` (nilai = URL foto) / `data-lightbox-video="URL_VIDEO"` dari `public/assets/js/media.js`. **WAJIB mengisi nilai atribut** — `data-lightbox` tanpa nilai tidak akan membuka gambar. Skeleton `data-skeleton` untuk gambar (loading state). `MediaHelpers.initSkeleton(scopeEl)` wajib dipanggil ulang setelah render AJAX/modal, dengan scope element sebagai parameter (bukan global).
- **Sidebar admin**: hamburger `#sidebar-toggle`, icon-only collapsed (desktop) via class `sidebar-collapsed` di `<html>` element & `<body>`; drawer (mobile) via class `sidebar-mobile-open` di `<body>`; state tersimpan di `localStorage['admin_sidebar_collapsed']`. FOUC prevention script di `layout.php` wajib set class di `documentElement` agar terbaca sebelum body ter-render.
- **Pagination & filter**: semua tabel admin wajib pagination + filter + live search (`data-live-search`, debounce 300ms) + tombol reset (`data-reset-filter`). Event listener filter/search hanya di-bind sekali (`_searchBound`, `_filterBound`). Event delegasi aksi hanya di-bind sekali per container (`container._actionsBound`).
- **Carousel**: `data-carousel` + `data-carousel-interval="1500"` + slides `.carousel-slide` + tombol `data-carousel-prev`/`data-carousel-next`. **Timer auto-slide wajib direset** (`resetTimer()`) setiap kali prev/next/dot diklik agar tidak overlap. Pause-on-hover aktif (WCAG 2.2.2).
- **`window.APP_BASE`**: Di-expose via `layout_close.php` agar JS modules (`media.js`, `admin.js`) bisa membangun URL auth redirect dan placeholder yang benar di semua environment (lokal dengan `APP_BASE=/kkn-padangcermin` atau produksi dengan `APP_BASE=`).
- **Aksi tabel**: kolom terakhir berisi icon button (`data-aksi="detail|delete"`), bukan teks link. Edit tetap pakai `<a href="...">` link ke halaman form jika halaman form sudah ada; hapus wajib via modal konfirmasi.

### Changelog Perbaikan Bug (2026-08-08)

| File | Bug | Fix |
|---|---|---|
| `function/ajax.php` | `data-lightbox` tanpa nilai URL → lightbox tidak buka | Semua thumbnail sekarang punya `data-lightbox="URL"` |
| `public/assets/js/admin.js` | Toast progress bar tidak beranimasi | Gunakan CSS transition + `getBoundingClientRect()` reflow trigger |
| `public/assets/js/admin.js` | Sidebar FOUC: toggle di `body` tapi script baca `documentElement` | Toggle sekarang di `documentElement` + sync ke `body` |
| `public/assets/js/admin.js` | Event listener duplikasi tiap `loadTable()` | Delegasi container hanya di-bind sekali via flag `_actionsBound`/`_paginationBound` |
| `public/assets/js/admin.js` | Skeleton tidak ter-init di detail modal | `detailModal()` punya callback `onOpen` → `MediaHelpers.initSkeleton(panel)` |
| `public/assets/js/media.js` | Lightbox tidak baca value `data-lightbox`, fallback ke `src` gagal | Fix: baca nilai atribut `data-lightbox`, fallback ke `src` img jika kosong/`"true"` |
| `public/assets/js/media.js` | `initSkeleton()` tanpa scope → duplikasi skeleton | `initSkeleton(scope?)` sekarang menerima scope element, guard `_skeletonInit` |
| `public/assets/js/carousel.js` | Timer tidak reset saat prev/next → slide bisa meloncat | `resetTimer()` dipanggil setelah setiap navigasi manual |
| `view/dashboard/layout.php` | FOUC: `body && body.classList` bisa null sebelum body ada | Tambah guard + sync ke body di inline script |
| `view/dashboard/layout_close.php` | `window.APP_BASE` tidak ter-expose untuk JS modules | Tambah `window.APP_BASE = '<?= APP_BASE ?>'` sebelum script |

