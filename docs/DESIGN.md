# DESIGN.md — Sistem Desain Website Pekon Padang Cermin

Dokumen ini punya dua fungsi: (1) referensi desain buat developer/AI agent yang nge-code, dan (2) sumber teks yang bisa langsung ditempel ke Stitch (§7) tanpa perlu ditulis ulang.

## 1. Risiko "AI Slop" yang harus dihindari — dan kenapa

Ada dua jenis template generik yang gampang jatuh ke sana kalau tidak sengaja dihindari:

1. **Template "web desa" generik** — hero foto kantor desa full-width dengan teks putih di tengah, lalu 3-4 kartu ikon default Bootstrap ("Profil", "Berita", "Galeri", "Kontak") dengan shadow tipis dan border-radius seragam. Ini yang paling relevan untuk brief ini, karena hampir semua situs desa di Indonesia terlihat seperti ini.
2. **Klise desain AI generik** — palet krem hangat + serif kontras + aksen terracotta, atau latar nyaris hitam + satu aksen neon, atau layout ala koran dengan garis tipis dan sudut siku semua. Brief kamu (putih + kuning) sudah otomatis menghindari kombinasi #1 dan #2, tapi tetap harus dijaga supaya kuningnya tidak jadi kuning generik ala warning-sign/caution-tape.

Keputusan di bawah ini secara sengaja menjauh dari ketiganya.

## 2. Token System

### Warna (bukan kuning generik — dinamai dari konteks agraris/pekon)

| Nama | Hex | Peran |
|---|---|---|
| Putih Utama | `#FFFFFF` | Background dasar |
| Putih Hangat | `#FBF8F1` | Background section selang-seling (bukan putih polos terus supaya ada ritme) |
| Kuning Gabah | `#E7A825` | Aksen utama/signature — dipakai sedikit tapi tegas: garis atas kartu, angka statistik, tombol utama |
| Hijau Sawah | `#3C6E47` | Aksen sekunder — tag kategori wisata, series kedua di grafik, badge status "Aktif" |
| Coklat Tanah | `#33291E` | Warna teks judul & garis penghubung diagram (bukan hitam pekat, bukan abu-abu generik) |
| Abu Netral | `#6B655C` | Teks sekunder/deskripsi |

Aturan pemakaian: kuning **tidak** jadi warna background besar-besaran (biar tidak terkesan warning/caution). Kuning dipakai sebagai aksen presisi — border-top kartu, underline judul section, tombol CTA, batang grafik. Putih tetap dominan di lebih dari 70% area.

### Bentuk & Kedalaman (radius, shadow — arah "sedikit modern")

Versi sebelumnya sengaja bergaya institusional-kaku (sudut siku, border tipis, nyaris tanpa shadow) supaya terasa resmi. Itu justru yang bikin kesannya kurang modern. Perubahan di sini bukan lompat ke tren SaaS generik (rounded penuh + glassmorphism + shadow tebal), tapi kompromi terukur:

| Token | Nilai | Peran |
|---|---|---|
| Radius Kartu | `14px` | Kartu wisata/berita/potensi, kartu node struktur organisasi — cukup lembut, tidak sampai pill |
| Radius Tombol | `10px` | Naik dari versi kaku sebelumnya (6-8px), tetap bukan pill penuh |
| Radius Badge/Tag | `999px` (pill) | Khusus elemen kecil (badge kategori, status) — pill di elemen kecil tidak terasa generik, beda dengan pill di tombol besar |
| Shadow Kartu | `0 4px 16px rgba(51,41,30,0.08)` | Shadow lembut pakai warna Coklat Tanah bertransparansi rendah — bukan abu-abu generik bawaan Tailwind |
| Shadow Hover | `0 8px 24px rgba(51,41,30,0.14)` + `translateY(-2px)` | Kartu & tombol terasa hidup saat disentuh/di-hover, transisi 150-200ms ease |

### Tipografi

| Peran | Font | Kenapa |
|---|---|---|
| Display (judul besar, section header) | **Space Grotesk** | Geometris, tegas, terasa seperti signage resmi — bukan serif hangat klise |
| Body (paragraf, deskripsi) | **Plus Jakarta Sans** | Dibuat untuk identitas Jakarta/pemerintahan DKI — pas secara konteks (institusi pemerintah lokal), sangat mudah dibaca bilingual |
| Data/angka (statistik kependudukan, kode inventaris) | **JetBrains Mono** | Kesan buku register/ledger resmi untuk angka-angka administratif |

Skala: H1 40-48px / H2 28-32px / H3 20-22px / body 16px / caption 13px. Line-height body 1.6.

### Layout — wireframe ASCII landing page

```
┌─────────────────────────────────────────────┐
│ [Logo Pekon]      Visi·Struktur·Wisata·Berita·Kontak │  ← nav sticky, backdrop-blur tipis
│                                                │     saat discroll (solid putih di top page)
├─────────────────────────────────────────────┤
│  HERO — asimetris, BUKAN center generic       │
│  ┌──────────────┐  Selamat Datang di          │
│  │ foto asli     │  Pekon Padang Cermin         │
│  │ suasana desa  │  (deskripsi singkat 1-2      │
│  │ (bukan stock  │   kalimat + tombol           │
│  │  icon/ilustrasi)│  "Lihat Potensi Desa")      │
│  │  radius 14px  │                              │
│  └──────────────┘                              │
│  ambient glow tipis Kuning Gabah (opacity ~8%)│
│  di belakang foto, bukan gradient layar penuh │
├─────────────────────────────────────────────┤
│  VISI & MISI  (background Putih Hangat)       │
│  garis kuning tipis di atas judul section     │
├─────────────────────────────────────────────┤
│  STRUKTUR ORGANISASI — signature element      │
│  (lihat §2.4)                                  │
├─────────────────────────────────────────────┤
│  DATA KEPENDUDUKAN                             │
│  [Angka besar: 1.234 Jiwa] [567 KK]  [grafik] │
├─────────────────────────────────────────────┤
│  POTENSI DESA — grid kartu 3 kolom             │
├─────────────────────────────────────────────┤
│  WISATA DESA — preview 3 kartu + "Lihat Semua"│
├─────────────────────────────────────────────┤
│  BERITA DESA — preview 3 kartu + "Lihat Semua"│
├─────────────────────────────────────────────┤
│  KONTAK + PETA — dua kolom: kontak person kiri,│
│  Google Maps embed kanan                       │
├─────────────────────────────────────────────┤
│  FOOTER — coklat tanah, teks putih hangat      │
└─────────────────────────────────────────────┘
```

### 2.4 Signature element — diagram struktur organisasi

Ini elemen yang paling dibedakan dari flowchart generik (kotak rounded biru muda + garis abu-abu tipis default draw.io):

- Tiap node = kartu kecil mirip **papan nama/ID badge**: border-top solid 3px Kuning Gabah, radius `14px`, shadow lembut (`Shadow Kartu`, lihat token di atas) menggantikan border tebal di semua sisi — kesan lebih ringan tanpa kehilangan identitas "papan resmi".
- Isi kartu: nama (Space Grotesk, bold), jabatan (Plus Jakarta Sans, uppercase, letter-spacing kecil), pendidikan terakhir (caption, Abu Netral).
- Garis penghubung antar level: solid 2px Coklat Tanah, sudut/elbow dibuat radius kecil (~8px) alih-alih siku 90° tajam — tetap terbaca sebagai diagram organisasi resmi, tapi tidak sekaku bagan sumber.
- Level hierarki dibedakan lewat **ukuran kartu**, bukan warna berbeda-beda (Kepala Pekon paling besar, menyusut tiap level ke bawah) — supaya tetap satu identitas warna yang konsisten.
- Hover di desktop: kartu naik tipis (`translateY(-2px)` + `Shadow Hover`) — micro-interaction kecil yang menandakan interaktif tanpa berlebihan.
- Di mobile: diagram jadi accordion vertikal per level (tap untuk expand), bukan dipaksa horizontal-scroll kecil yang susah dibaca.

## 3. Grafik / Visualisasi Data

Rekomendasi: **Chart.js** via CDN (ringan, tanpa build step, cocok untuk PHP native tanpa Node runtime saat produksi).

- Grafik tren jumlah KK & jiwa antar periode → line chart dengan `tension: 0.35` (kurva halus, bukan garis patah-patah tajam), 2 series: Kuning Gabah (jiwa) + Hijau Sawah (KK).
- Kalau pakai bar chart di bagian lain (misal perbandingan kondisi inventaris di admin), ujung atas batang dibuat rounded (`borderRadius` Chart.js) — konsisten dengan radius kartu 14px.
- Tidak pakai efek 3D, tidak pakai gradient fill tebal — flat dengan sedikit rounded, sesuai token warna di atas.
- Statistik ringkasan (jumlah KK/jiwa terkini) ditampilkan sebagai angka besar pakai font JetBrains Mono, bukan cuma di dalam grafik — supaya tetap terbaca jelas di layar kecil sebelum grafik selesai render.

## 4. Komponen Dasar

- **Tombol primer**: background Kuning Gabah, teks Coklat Tanah (bukan putih — kontras lebih baik di atas kuning), radius `10px` (token "Radius Tombol"). Hover: `Shadow Hover` + `translateY(-2px)`, transisi 150-200ms — bukan cuma ganti warna statis.
- **Kartu berita/wisata**: gambar rasio 4:3 dengan radius `14px` di sudut atas (menyatu dengan radius kartu), judul Space Grotesk, badge kategori Hijau Sawah pill kecil di pojok. Hover: kartu naik tipis + shadow membesar (`Shadow Hover`).
- **Badge status admin** (draft/publish, kondisi barang baik/rusak): pill radius `999px`, warna solid kecil — bukan outline tipis susah dibaca.

## 4.1 Motion — dipakai secukupnya, bukan dekorasi berlebihan

- Sticky nav: solid putih saat di atas halaman, jadi `backdrop-blur` tipis + sedikit transparan begitu discroll — efek umum tapi ringan, bukan animasi mencolok.
- Scroll-reveal: tiap section fade-up halus (~300ms, translateY 12px→0) saat pertama masuk viewport. Satu kali per section, tidak berulang tiap scroll naik-turun — supaya tidak mengganggu warga yang scroll cepat cari info.
- Hormati `prefers-reduced-motion`: kalau aktif di device pengguna, semua transisi di atas dimatikan/dipercepat ke instant.
- Tidak ada animasi loop otomatis (marquee, particle, dsb) — itu yang bikin situs pemerintah desa terasa norak, bukan modern.

## 5. Copywriting — suara antarmuka

- Sudut pandang warga/admin, bukan sistem: tombol simpan berita bilang **"Simpan Berita"**, bukan "Submit".
- Konsisten: kalau tombol bilang "Terbitkan", notifikasi sukses bilang "Berita berhasil diterbitkan" — bukan berubah jadi "Published".
- Error jelas dan actionable: "Nomor telepon harus 10-13 digit" — bukan "Input tidak valid".
- Empty state jadi ajakan, bukan cuma "Tidak ada data": misal wisata kosong → "Belum ada wisata ditambahkan. Klik 'Tambah Wisata' untuk mulai."

## 6. Aksesibilitas & Responsif

- Kontras teks Coklat Tanah di atas Putih/Putih Hangat sudah AA-compliant; kuning **tidak** dipakai sebagai warna teks di atas putih (kontras kurang) — kuning cuma untuk elemen non-teks atau teks di atas Coklat Tanah.
- Focus ring keyboard terlihat jelas (outline 2px Hijau Sawah), jangan dihapus dengan `outline: none` tanpa pengganti.
- Semua gambar wajib alt text (divalidasi di form admin, lihat AGENTS.md).
- Breakpoint mobile-first Tailwind default (sm/md/lg) sudah cukup, tidak perlu breakpoint kustom.

## 7. Prompt Siap Tempel ke Stitch

**Prompt 1 — Landing Page:**
> Design a one-page government village website landing page for "Pekon Padang Cermin" in Indonesia, with a sticky navigation bar (Visi Misi, Struktur Organisasi, Wisata, Berita, Kontak) that turns into a subtle frosted/blurred bar on scroll. Use a white-dominant palette (#FFFFFF, #FBF8F1) with a precise gold-yellow accent (#E7A825, named "Kuning Gabah") used sparingly on card top-borders, section underlines, and buttons — never as a large background. Support colors: deep green #3C6E47 and dark earth-brown #33291E for text and connector lines. Typography: Space Grotesk for headings, Plus Jakarta Sans for body text, JetBrains Mono for statistics. Give cards a soft 14px border radius and a gentle warm-toned shadow (not generic gray), with a subtle lift-on-hover effect. Hero section is asymmetric (real village photo on one side, welcome text and CTA on the other) with a faint ambient glow of the gold accent behind the photo — not a full-screen gradient. Include sections: hero, visi-misi, an organizational chart styled like soft-shadow ID badge cards connected by earth-brown lines with slightly rounded elbow corners (not a sharp flowchart, not a generic rounded SaaS diagram either), a population statistics section with a large mono-font number plus a smooth-curved line chart, potensi desa card grid, wisata desa preview cards linking to a full listing page, berita desa preview cards linking to a full listing page, and a contact section with an embedded map. Subtle fade-up reveal on scroll for each section, nothing looping or flashy. Avoid generic government-portal blue navbars, avoid stock icon illustrations, avoid warm cream+serif+terracotta combinations, avoid heavy glassmorphism, avoid pill-shaped primary buttons.

**Prompt 2 — Dashboard Admin:**
> Design an admin dashboard for managing a village government website, using the same design tokens as the public site (white #FFFFFF/#FBF8F1 background, gold accent #E7A825, deep green #3C6E47, earth-brown #33291E text, Space Grotesk headings, Plus Jakarta Sans body, JetBrains Mono for data tables, 14px card radius, soft warm-toned shadows, 10px button radius). Sidebar navigation with modules: Profil Desa, Struktur Organisasi (tree editor with drag-to-reorder), Kependudukan, Potensi Desa, Wisata Desa, Berita Desa, Inventaris, and Pengaturan Akun. Main content area shows a data table with search, pagination, and pill-shaped inline status badges (draft/publish, kondisi baik/rusak). Forms use clear Indonesian labels and inline validation messages. Buttons and cards have a gentle hover lift. Keep the interface clean and functional — light, contemporary shadows instead of flat borders everywhere, but no decorative gradients, no glassmorphism, clear focus states for keyboard use.
