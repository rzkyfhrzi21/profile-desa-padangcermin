-- Seed dummy Pekon Padang Cermin — data contoh dari template stitch (docs/stitch)
-- Wajib dijalankan SETELAH schema.sql. Gambar di folder uploads/ sudah tersedia.
-- Catatan: admin (username admin) dibuat terpisah via Zzz/cek-admin.php reset.
USE padang_cermin_db;

-- ============================================================
-- profil_desa (single-row, id 1 sudah dibuat schema.sql)
-- ============================================================
UPDATE profil_desa SET
  nama_pekon = 'Pekon Padang Cermin',
  visi = 'Menuju tata kelola pekon yang transparan, digital, dan berkelanjutan untuk masa depan hijau.',
  misi = 'Mewujudkan pelayanan publik yang cepat, transparan, dan berbasis digital.\nMengembangkan ekonomi desa melalui pertanian organik dan produk unggulan lokal.\nMelestarikan lingkungan dan kearifan lokal melalui pengelolaan sumber daya berkelanjutan.\nMeningkatkan partisipasi masyarakat dalam pembangunan dan musyawarah pekon.',
  sambutan_kepala_pekon = 'Assalamualaikum warahmatullahi wabarakatuh. Selamat datang di portal resmi Pekon Padang Cermin. Portal ini kami hadirkan sebagai wujud komitmen pemerintahan pekon dalam keterbukaan informasi, sehingga seluruh warga dapat mengakses data dan kegiatan pekon secara mudah dan transparan. Kami berharap kehadiran portal ini semakin mendekatkan pemerintah pekon dengan masyarakat, serta mendorong partisipasi aktif seluruh warga dalam membangun Pekon Padang Cermin yang lebih maju, sejahtera, dan berkelanjutan.',
  foto_kepala_pekon = 'struktur/struktur-kades.jpg',
  alamat_kantor = 'Jl. Lintas Barat No. 45, Padang Cermin, Lampung',
  telepon = '0812-3456-7890',
  email = 'info@pekonpadangcermin.go.id',
  whatsapp = '0812-3456-7890'
WHERE id = 1;

-- ============================================================
-- struktur_organisasi
-- ============================================================
INSERT INTO struktur_organisasi (id, parent_id, nama, jabatan, pendidikan_terakhir, foto, urutan) VALUES
(1, NULL, 'Budi Santoso',       'Kepala Pekon',       'S2', 'struktur/struktur-kades.jpg',           1),
(2, 1,     'Siti Aminah',       'Sekretaris Desa',    'S1', 'struktur/struktur-sekdes.jpg',          2),
(3, 1,     'Ahmad Fauzi',       'Kasi Pemerintahan',  'SMA', 'struktur/struktur-kasi-pemerintahan.jpg', 3),
(4, 1,     '',                  'Kasi Kesejahteraan', NULL, NULL,                                    4),
(5, 1,     'Ratna Dewi',        'Kaur Keuangan',      'S1', 'struktur/struktur-kaur-keuangan.jpg',  5);

-- ============================================================
-- data_kependudukan
-- ============================================================
INSERT INTO data_kependudukan (periode, jumlah_kk, jumlah_jiwa, keterangan) VALUES
('2021', 398, 1487, 'Tahun 2021'),
('2022', 407, 1520, 'Tahun 2022'),
('2023', 418, 1560, 'Tahun 2023'),
('2024', 428, 1592, 'Per Desember 2024: laki-laki 810 jiwa, perempuan 782 jiwa');

-- ============================================================
-- potensi_desa
-- ============================================================
INSERT INTO potensi_desa (judul, deskripsi, gambar, kategori, urutan, status) VALUES
('Kopi Robusta Unggulan', 'Perkebunan kopi rakyat yang menghasilkan biji robusta berkualitas tinggi dengan cita rasa khas dataran tinggi. Kopi Padang Cermin diproses secara semi-wash dan telah menarik minat pembeli dari luar daerah.', 'potensi/potensi-kopi-robusta.jpg', 'Pertanian', 1, 'aktif'),
('Kerajinan Bambu Lestari', 'Produk kerajinan tangan berbahan dasar bambu lokal yang dibuat oleh kelompok tani wanita, memiliki nilai seni dan fungsi tinggi. Dikembangkan sebagai produk UMKM unggulan pekon dengan jangkauan pemasaran hingga ke luar provinsi.', 'potensi/potensi-bambu-lestari.jpg', 'UMKM', 2, 'aktif');

-- ============================================================
-- wisata_desa
-- ============================================================
INSERT INTO wisata_desa (id, nama, slug, deskripsi, alamat, harga_tiket, jam_buka, status) VALUES
(1, 'Sawah Terasering', 'sawah-terasering',
 'Menyuguhkan panorama hijau sejauh mata memandang, Sawah Terasering Padang Cermin adalah mahakarya agrikultur tradisional yang memukau. Berada di ketinggian yang sejuk, lokasi ini tidak hanya menawarkan pemandangan alam yang indah, tetapi juga ketenangan yang jauh dari hiruk-pikuk perkotaan.\n\nDibangun secara turun-temurun oleh masyarakat lokal menggunakan sistem irigasi subak yang berkelanjutan, setiap undakan sawah ini mencerminkan kearifan lokal dalam menjaga keseimbangan alam. Pengunjung dapat menyusuri pematang sawah, merasakan angin sepoi-sepoi, dan berinteraksi langsung dengan para petani yang sedang bercocok tanam atau memanen padi.',
 'Dusun 1, Desa Way Urang, Kec. Padang Cermin, Kab. Pesawaran, Lampung',
 'Rp 15.000/orang', '06:00 - 18:00 WIB', 'publish'),
(2, 'Air Terjun Way Jernih', 'air-terjun-way-jernih',
 'Keindahan air terjun tersembunyi dengan debit air stabil sepanjang tahun. Airnya jernih kebiruan dengan kolam alami yang aman untuk berenang, cocok untuk healing dan relaksasi di tengah hutan tropis yang asri.',
 'Hutan Lindung, Pekon Padang Cermin, Kec. Padang Cermin, Kab. Pesawaran, Lampung',
 'Rp 10.000/orang', '08:00 - 17:00 WIB', 'publish'),
(3, 'Hutan Pinus Eco-Park', 'hutan-pinus-eco-park',
 'Kawasan konservasi yang dilengkapi fasilitas edukasi lingkungan, area berkemah, dan jalur trekking yang ramah keluarga. Pengunjung dapat belajar mengenal flora lokal melalui papan informasi interaktif di sepanjang jalur kayu yang ditinggikan.',
 'Dusun 3, Pekon Padang Cermin, Kec. Padang Cermin, Kab. Pesawaran, Lampung',
 'Rp 20.000/orang', '07:00 - 17:00 WIB', 'publish'),
(4, 'Curug Bidadari', 'curug-bidadari',
 'Air terjun bertingkat dengan kolam alami yang jernih, dikelilingi hutan tropis yang asri. Cocok untuk ekowisata dan fotografi alam dengan suasana yang masih sangat alami.',
 'Kawasan hutan, Pekon Padang Cermin, Kec. Padang Cermin, Kab. Pesawaran, Lampung',
 'Rp 10.000/orang', '08:00 - 17:00 WIB', 'publish');

-- ============================================================
-- wisata_gambar
-- ============================================================
INSERT INTO wisata_gambar (wisata_id, path_gambar, alt_text, urutan) VALUES
(1, 'wisata/wisata-sawah-terasering.jpg', 'Hamparan sawah terasering hijau Pekon Padang Cermin dilihat dari ketinggian', 1),
(1, 'wisata/wisata-sawah-galeri-1.jpg', 'Panorama sawah terasering Padang Cermin saat golden hour', 2),
(1, 'wisata/wisata-sawah-galeri-2.jpg', 'Undakan sawah dengan padi menguning menanti panen', 3),
(1, 'wisata/wisata-sawah-galeri-3.jpg', 'Pengunjung berjalan di pematang sawah sambil menikmati pemandangan', 4),
(1, 'wisata/wisata-sawah-galeri-4.jpg', 'Matahari terbenam terpantul di genangan air sawah', 5),
(1, 'wisata/wisata-sawah-galeri-5.jpg', 'Kabut pagi menyelimuti lembah di sekitar sawah terasering', 6),
(1, 'wisata/wisata-sawah-galeri-6.jpg', 'Sistem irigasi subak mengaliri undakan sawah', 7),
(2, 'wisata/wisata-way-jernih.jpg', 'Air terjun Way Jernih dengan kolam alami yang jernih di tengah hutan', 1),
(3, 'wisata/wisata-hutan-pinus.jpg', 'Jalur kayu di Hutan Pinus Eco-Park dengan pepohonan pinus menjulang', 1),
(4, 'wisata/wisata-curug-bidadari.jpg', 'Curug Bidadari air terjun bertingkat dengan kolam alami', 1);

-- ============================================================
-- berita_kategori
-- ============================================================
INSERT INTO berita_kategori (id, nama, slug) VALUES
(1, 'Pemerintahan', 'pemerintahan'),
(2, 'Pengumuman',   'pengumuman'),
(3, 'Kegiatan',     'kegiatan'),
(4, 'Pembangunan',  'pembangunan'),
(5, 'Pertanian',    'pertanian'),
(6, 'Ekonomi',      'ekonomi'),
(7, 'Infrastruktur','infrastruktur'),
(8, 'Sosialisasi',  'sosialisasi'),
(9, 'Teknologi',    'teknologi'),
(10, 'Lingkungan',  'lingkungan'),
(11, 'Budaya',      'budaya');

-- ============================================================
-- berita_desa (penulis_id 1 = admin)
-- ============================================================
INSERT INTO berita_desa (judul, slug, kategori_id, konten, gambar_utama, alt_gambar, penulis_id, status, views, published_at) VALUES
('Panen Raya Padi Organik Tingkatkan Ekonomi Petani Pekon Padang Cermin', 'panen-raya-padi-organik', 5,
 'Pekon Padang Cermin kembali menorehkan prestasi di sektor pertanian. Musim panen raya padi organik tahun ini menunjukkan peningkatan hasil yang signifikan, membawa angin segar bagi perekonomian warga desa. Transisi dari pertanian konvensional ke metode organik yang diinisiasi sejak dua tahun lalu kini membuahkan hasil manis.\n\nKepala Pekon Padang Cermin, dalam sambutannya saat acara syukuran panen, menyampaikan rasa bangganya terhadap komitmen para petani. "Ini bukan sekadar soal kuantitas panen, tapi juga kualitas lingkungan hidup kita. Padi organik terbukti mengembalikan kesuburan tanah dan memutus ketergantungan kita pada pupuk kimia yang mahal dan merusak," ujarnya.\n\nBerdasarkan data dari BUMDes setempat, harga gabah organik yang dipanen mencapai 30% lebih tinggi dibandingkan gabah konvensional. Hal ini menarik minat tengkulak dari luar daerah, bahkan beberapa restoran sehat di kota besar telah menandatangani kontrak jangka panjang untuk menyerap hasil panen desa.\n\nKe depan, pemerintah pekon berencana memperluas area lahan organik dan membangun fasilitas penggilingan modern yang dikelola sepenuhnya oleh BUMDes. Diharapkan, Pekon Padang Cermin dapat menjadi pusat pembelajaran agroekologi bagi pekon-pekon tetangga di kabupaten.',
 'berita/berita-panen-raya-padi.jpg', 'Petani memanen padi organik di hamparan sawah Pekon Padang Cermin', 1, 'publish', 1247, '2024-10-12 09:00:00'),
('Pemasangan Sensor Air Pintar Optimalkan Irigasi Sawah', 'pemasangan-sensor-air-pintar', 9,
 'Pemerintah pekon bersama tim mahasiswa KKN memasang sensor pemantau ketinggian air pintar di jaringan irigasi utama. Alat ini mengirim data real-time ke ponsel petani, sehingga pengaturan air sawah dapat dilakukan tepat waktu dan lebih efisien.\n\nSensor yang dipasang di tiga titik irigasi ini terhubung dengan aplikasi sederhana berbasis web. Petani dapat memantau debit air dari rumah tanpa harus berjalan ke sawah, menghemat waktu sekaligus mencegah pemborosan air pada musim kemarau.\n\nProgram ini merupakan tahap pertama dari sistem digitalisasi pertanian pekon. Ke depan, sensor juga akan dipasang di area perkebunan kopi dan tambak untuk memperluas cakupan pemantauan.',
 'berita/berita-sensor-air-pintar.jpg', 'Petugas memasang sensor air pintar di saluran irigasi sawah', 1, 'publish', 856, '2024-10-10 14:30:00'),
('Pasar Malam Pekon Padang Cermin Raup Omzet Rp 50 Juta', 'pasar-malam-pekon-raup-omzet', 6,
 'Gelaran pasar malam akhir pekan di lapangan pekon berlangsung meriah dan sukses meraup omzet mencapai Rp 50 juta selama dua hari penyelenggaraan. Puluhan pelaku UMKM lokal ikut memeriahkan dengan beragam produk kerajinan dan kuliner khas.\n\nPasar malam ini menjadi wadah promosi produk unggulan pekon, mulai dari kopi robusta, kerajinan bambu, hingga makanan olahan khas Padang Cermin. Pembeli tidak hanya datang dari warga setempat, tetapi juga dari luar kecamatan.\n\nKeberhasilan ini mendorong pemerintah pekon menjadwalkan pasar malam secara rutin setiap bulan serta menambah stan pelatihan pemasaran digital bagi UMKM pemula.',
 'berita/berita-pasar-malam-pekon.jpg', 'Suasana pasar malam pekon dengan stan produk lokal yang ramai pengunjung', 1, 'publish', 412, '2024-10-05 20:00:00'),
('Pemuda Tanam Seribu Bibit Pohon di Kawasan Hutan Lindung', 'pemuda-tanam-seribu-bibit-pohon', 10,
 'Ratusan pemuda dari berbagai dusun bergotong royong menanam seribu bibit pohon di kawasan penyangga hutan lindung. Kegiatan ini bertujuan memulihkan lahan kritis sekaligus menjaga sumber mata air yang dimanfaatkan warga.\n\nJenis bibit yang ditanam antara lain durian, petai, dan pohon kayu keras lokal. Penanaman dilakukan bersama penyuluh kehutanan yang memberikan pendampingan cara merawat hingga pohon siap tumbuh mandiri.\n\nKegiatan ini menjadi agenda tahunan yang diharapkan memperkuat kesadaran lingkungan generasi muda sekaligus menambah kawasan hijau pekon.',
 'berita/berita-pemuda-tanam-pohon.jpg', 'Pemuda pekon menanam bibit pohon di kawasan hutan lindung', 1, 'publish', 320, '2024-10-01 08:30:00'),
('Peluncuran Sistem Digitalisasi Pertanian Desa Tahap Pertama', 'peluncuran-sistem-digitalisasi-pertanian', 1,
 'Pemerintah Pekon Padang Cermin resmi meluncurkan platform pemantauan hasil panen cerdas, sebuah sistem digital yang membantu petani mencatat dan memantau produksi secara real-time. Sistem ini dikembangkan bekerja sama dengan tim KKN dan perguruan tinggi.\n\nMelalui platform ini, data luas lahan, jenis tanaman, dan estimasi panen setiap kelompok tani dapat dilihat dalam satu dasbor. Data tersebut juga menjadi dasar perencanaan bantuan dan pemasaran hasil tani oleh BUMDes.\n\nPeluncuran ini menjadi penanda awal transformasi digital tata kelola pekon. Berikutnya, sistem akan diperluas untuk layanan administrasi warga secara daring.',
 'berita/berita-digitalisasi-pertanian.jpg', 'Peluncuran sistem digitalisasi pertanian di aula pekon dengan layar data hasil panen', 1, 'publish', 934, '2024-05-20 10:00:00'),
('BUMDes Panen Raya Sayur Organik dari Lahan Demfarm', 'bumdes-panen-raya-sayur-organik', 6,
 'Badan Usaha Milik Desa (BUMDes) Padang Cermin menggelar panen raya sayur organik dari lahan demonstrasi farming (demfarm). Cabai, tomat, dan sayuran daun yang dibudidayakan tanpa pupuk kimia berhasil dipanen dalam jumlah besar.\n\nHasil panen diserap langsung oleh warung-warung dan pasar modern di kabupaten melalui perjanjian kerja sama yang sudah diteken BUMDes. Sebagian hasil juga disalurkan untuk program bantuan pangan warga kurang mampu.\n\nDemfarm ini menjadi percontohan bagi kelompok tani lain. BUMDes menyediakan bibit, pelatihan, dan jaminan pembelian sehingga petani tidak khawatir dengan fluktuasi harga pasar.',
 'berita/berita-panen-sayur-bumdes.jpg', 'Sayur organik hasil panen demfarm BUMDes dikemas dalam keranjang kayu', 1, 'publish', 601, '2024-05-15 09:30:00'),
('Peresmian Lampu Jalan Tenaga Surya di Dusun Harapan', 'peresmian-lampu-jalan-tenaga-surya', 7,
 'Sebanyak 40 unit lampu jalan bertenaga surya resmi beroperasi di sepanjang jalan utama Dusun Harapan. Fasilitas ini diresmikan dalam acara sederhana yang dihadiri warga dan perwakilan pemerintah kecamatan.\n\nLampu tenaga surya dipilih karena hemat biaya operasional dan ramah lingkungan. Setiap unit dilengkapi panel surya dan baterai penyimpanan sehingga mampu menyala otomatis dari senja hingga dini hari.\n\nKehadiran penerangan jalan diharapkan meningkatkan keamanan dan mendukung aktivitas ekonomi warga di malam hari, termasuk para petani yang mulai bekerja sebelum subuh.',
 'berita/berita-lampu-tenaga-surya.jpg', 'Lampu jalan tenaga surya menyala di sepanjang jalan Dusun Harapan saat senja', 1, 'publish', 745, '2024-05-10 19:00:00'),
('Sosialisasi Kesehatan Ibu dan Anak oleh Posyandu Melati', 'sosialisasi-kesehatan-ibu-anak', 8,
 'Posyandu Melati bersama bidan desa menggelar sosialisasi kesehatan ibu dan anak di balai pekon. Kegiatan dihadiri ibu hamil, ibu menyusui, dan kader kesehatan dari seluruh dusun.\n\nMateri yang disampaikan meliputi pemenuhan gizi seimbang, imunisasi dasar lengkap, serta deteksi dini stunting. Peserta juga mendapat pemeriksaan kesehatan gratis dan konsultasi langsung dengan tenaga kesehatan.\n\nKegiatan serupa akan dijadwalkan rutin setiap bulan dengan lokasi berpindah-pindah agar menjangkau seluruh dusun di Pekon Padang Cermin.',
 'berita/berita-kesehatan-ibu-anak.jpg', 'Sosialisasi kesehatan ibu dan anak di balai pekon yang dihadiri warga', 1, 'publish', 512, '2024-05-05 09:00:00'),
('Penyelesaian Pengaspalan Jalan Dusun III Suka Makmur', 'penyelesaian-pengaspalan-jalan-dusun-iii', 4,
 'Pengerjaan pengaspalan jalan sepanjang 1,2 kilometer di Dusun III Suka Makmur dinyatakan selesai. Pembangunan ini bersumber dari dana desa tahap pertama dan dikerjakan oleh kelompok swadaya masyarakat setempat.\n\nJalan yang sebelumnya berupa tanah merah dan sulit dilalui saat hujan kini menjadi akses utama yang nyaman bagi warga, khususnya untuk pengangkutan hasil pertanian menuju pasar.\n\nPemerintah pekon mengucapkan terima kasih atas partisipasi warga yang menjaga kualitas pengerjaan. Jalan ini diharapkan mempercepat mobilitas dan pertumbuhan ekonomi Dusun III.',
 'berita/berita-pengaspalan-jalan.jpg', 'Jalan desa yang baru diaspal melintasi hamparan sawah dengan lampu penerangan', 1, 'publish', 1580, '2023-10-12 11:00:00'),
('Hasil Musyawarah Musrenbang Desa Tahun 2024', 'hasil-musyawarah-musrenbang-2024', 3,
 'Musyawarah Perencanaan Pembangunan (Musrenbang) Pekon Padang Cermin tahun 2024 menghasilkan sejumlah prioritas pembangunan yang akan diusulkan pada pemerintah kabupaten.\n\nPrioritas utama meliputi pembangunan irigasi tersier, pengadaan alat penggilingan padi modern untuk BUMDes, serta pembangunan gedung serbaguna di Dusun II. Seluruh usulan disepakati melalui musyawarah mufakat yang dihadiri perwakilan tiap dusun.\n\nHasil musrenbang dituangkan dalam RKPDes dan menjadi pedoman pelaksanaan pembangunan pekon pada tahun anggaran berikutnya.',
 'berita/berita-musrenbang-2024.jpg', 'Warga berdiskusi dalam musyawarah perencanaan pembangunan pekon', 1, 'publish', 689, '2023-09-25 10:00:00'),
('Jadwal Penyaluran BLT Dana Desa Tahap III', 'jadwal-penyaluran-blt-dana-desa-tahap-iii', 2,
 'Pemerintah Pekon Padang Cermin akan menyalurkan Bantuan Langsung Tunai (BLT) Dana Desa tahap III kepada penerima manfaat yang telah ditetapkan.\n\nPenyaluran akan dilaksanakan di balai pekon dengan membawa undangan dan dokumen pendukung berupa KTP serta kartu keluarga. Warga diimbau hadir sesuai jadwal per dusun untuk menghindari antrean.\n\nBagi warga yang berhalangan hadir, pengambilan bantuan dapat diwakilkan oleh keluarga satu kartu dengan membawa surat kuasa sederhana.',
 'berita/berita-jadwal-bltdd.jpg', 'Ilustrasi penyaluran bantuan langsung tunai dana desa kepada warga', 1, 'draft', 0, NULL);

-- ============================================================
-- log_aktivitas (contoh)
-- ============================================================
INSERT INTO log_aktivitas (admin_id, aksi, tabel_terkait, data_id) VALUES
(1, 'Seed data dummy', 'seed', NULL),
(1, 'Aktivasi konten awal', 'seed', NULL);
