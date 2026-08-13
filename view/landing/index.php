<?php

declare(strict_types=1);

$profil = getProfil();
$namaPekon = $profil['nama_pekon'] ?? 'Desa Padang Cermin';
$penduduk = getDataKependudukanTerbaru();
$periodeKependudukan = $penduduk['periode'] ?? '';
$dusunList = getKependudukanDusun();
$jumlahDusun = getJumlahDusunTerbaru();  // jumlah dusun unik dari data kependudukan terbaru
$totalKK = array_sum(array_map(static fn(array $d): int => (int) $d['jumlah_kk'], $dusunList));
$totalJiwa = array_sum(array_map(static fn(array $d): int => (int) $d['jumlah_jiwa'], $dusunList));
$totalLaki = array_sum(array_map(static fn(array $d): int => (int) $d['jumlah_laki'], $dusunList));
$totalPerempuan = array_sum(array_map(static fn(array $d): int => (int) $d['jumlah_perempuan'], $dusunList));
$potensiList = getPotensiList(true);
$wisataList = getWisataWithGambar(getWisataList(true, 6));
$wisataCount = count(getWisataList(true));
$beritaList = getBeritaList(true, 3);
$strukturTree = getStrukturTree();
$pctLaki = $totalJiwa > 0 ? (int) round($totalLaki / $totalJiwa * 100) : 0;
$judulPage = $namaPekon . ' — Profil, Potensi & Wisata Alam';
$deskripsiPage = truncate('Portal resmi ' . $namaPekon . ', Kecamatan Padang Cermin, Kabupaten Pesawaran, Lampung. Sentra produksi pisang 152 hektare, 8 dusun, dan wisata air terjun Penyarian.', 155);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($judulPage) ?></title>
    <link rel="icon" type="image/x-icon" href="<?= APP_BASE ?>/favicon.ico" />
    <link rel="shortcut icon" href="<?= APP_BASE ?>/favicon.ico" />
    <meta name="description" content="<?= e($deskripsiPage) ?>" />
    <link rel="canonical" href="<?= e(APP_URL . '/') ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= e($judulPage) ?>" />
    <meta property="og:description" content="<?= e($deskripsiPage) ?>" />
    <meta property="og:image" content="<?= e(APP_URL . '/assets/img/logo.png') ?>" />
    <meta property="og:url" content="<?= e(APP_URL . '/') ?>" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?= e($judulPage) ?>" />
    <meta name="twitter:description" content="<?= e($deskripsiPage) ?>" />
    <meta name="twitter:image" content="<?= e(APP_URL . '/assets/img/logo.png') ?>" />
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "GovernmentOrganization",
            "name": <?= json_encode($namaPekon, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            "url": <?= json_encode(APP_URL . '/', JSON_UNESCAPED_SLASHES) ?>,
            "address": <?= json_encode(['@type' => 'PostalAddress', 'streetAddress' => $profil['alamat_kantor'] ?? '', 'addressCountry' => 'ID'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@100..900&family=Plus+Jakarta+Sans:wght@100..900&family=Space+Grotesk:wght@100..900&display=swap" rel="stylesheet" />
    <link href="<?= assetUrl('css/tailwind.css') ?>" rel="stylesheet" />
    <style>
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="bg-background font-body-md text-on-surface">
    <?php require __DIR__ . '/partials/header.php'; ?>
    <main class="relative">
        <!-- Atmospheric Background Decoration -->
        <div class="absolute top-0 left-0 w-full h-[921px] pointer-events-none z-0">
            <div class="absolute inset-0 bg-gradient-to-b from-background/80 via-background/60 to-background z-10"></div>
            <img alt="Sawah dan perbukitan Desa Padang Cermin" class="w-full h-full object-cover opacity-50" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB8Af1po4q5qXWAGBT5sc2MwF6TM6d-l0Ky90uxwrl2oaw0Jloy0VA76WayM3KaBpNO0B0Q8PSpdR9hr9sWhiZqipxP_j6A7N9DOFKlKatSO3_W8Ac_rQe-4pu0AWFWPIRn8u7WrHYYS7cuGyht8_Nljs7SDBqIEqcG9Mn5ZuzrTXOIl2yZ5kxURMoDyUXwa6JCHjrsJJT-5q_Pl_z5cPHF0zTGuIFRSE52oMfL06PG5jh9ABrZ0301" />
        </div>
        <!-- Hero Section -->
        <section class="relative w-full min-h-[80vh] flex flex-col justify-center px-margin-mobile lg:px-margin-desktop py-section-gap z-10 pt-28 reveal">
            <div class="max-w-container-max mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
                <!-- Hero Content -->
                <div class="lg:col-span-7 flex flex-col gap-stack-lg z-20">
                    <div class="inline-flex items-center gap-2 bg-glass-fill backdrop-blur-md rounded-full px-4 py-2 w-max shadow-sm border border-glass-border">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        <span class="font-label-mono text-label-mono text-primary">KECAMATAN PADANG CERMIN — KAB. PESAWARAN</span>
                    </div>
                    <h1 class="font-headline-xl text-headline-xl-mobile lg:text-headline-xl text-on-surface leading-tight">
                        Potensi Alam &amp; Kehidupan <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-surface-tint"><?= e($namaPekon) ?></span>.
                    </h1>
                    <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl">
                        Desa terluas di Kecamatan Padang Cermin dengan 8 dusun. Sentra produksi pisang ambon seluas 152 hektare, pertanian dan perkebunan, serta wisata air terjun yang viral di Penyarian, Dusun Lubuk Bakak.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 mt-4">
                        <a href="#data" class="bg-primary text-on-primary px-8 py-4 rounded-full font-body-md font-bold hover:shadow-[0_0_20px_rgba(158,230,56,0.4)] transition-all flex items-center gap-2 group">
                            Lihat Data Desa
                            <span class="material-symbols-outlined transition-transform group-hover:translate-x-1">arrow_forward</span>
                        </a>
                        <a href="<?= APP_BASE ?>/wisata" class="bg-glass-fill backdrop-blur-md text-on-surface border border-glass-border px-8 py-4 rounded-full font-body-md hover:bg-surface-container-high transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined">landscape</span> Jelajahi Wisata
                        </a>
                    </div>
                </div>
                <!-- Live Insights Widget (Glassmorphic) -->
                <div class="lg:col-span-5 relative z-20 mt-12 lg:mt-0">
                    <div class="bg-glass-fill backdrop-blur-[16px] rounded-[20px] p-stack-lg border border-glass-border shadow-2xl group hover:-translate-y-1 transition-transform duration-500 hover:border-primary/40 relative overflow-hidden">
                        <!-- Decorative chart line in background -->
                        <svg class="absolute bottom-0 right-0 w-full h-32 opacity-20" preserveAspectRatio="none" viewBox="0 0 100 30">
                            <path d="M0,30 L10,25 L25,28 L40,15 L60,20 L80,5 L100,10 L100,30 Z" fill="url(#chartGrad)"></path>
                            <path class="text-primary" d="M0,30 L10,25 L25,28 L40,15 L60,20 L80,5 L100,10" fill="none" stroke="currentColor" stroke-width="0.5"></path>
                            <defs>
                                <linearGradient id="chartGrad" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#9EE638" stop-opacity="0.3"></stop>
                                    <stop offset="100%" stop-color="#9EE638" stop-opacity="0"></stop>
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div>
                                <h3 class="font-headline-md text-headline-md text-on-surface">Statistik Desa</h3>
                                <p class="font-caption text-caption text-on-surface-variant">Data ringkas wilayah &amp; komoditas</p>
                            </div>
                            <span class="material-symbols-outlined text-primary bg-muted-forest p-2 rounded-lg">sensors</span>
                        </div>
                        <div class="grid grid-cols-2 gap-6 relative z-10">
                            <div class="flex flex-col gap-1">
                                <span class="font-label-mono text-label-mono text-on-surface-variant">LUAS WILAYAH</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="font-headline-lg text-headline-lg text-primary">3.278</span>
                                    <span class="font-caption text-caption text-secondary">Ha</span>
                                </div>
                                <span class="font-caption text-caption text-primary flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">eco</span> Terluas di Kecamatan</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="font-label-mono text-label-mono text-on-surface-variant">SENTRA PISANG</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="font-headline-lg text-headline-lg text-on-surface">152</span>
                                    <span class="font-caption text-caption text-secondary">Ha</span>
                                </div>
                                <span class="font-caption text-caption text-secondary flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">psychiatry</span> Komoditas Utama</span>
                            </div>
                            <div class="col-span-2 bg-glass-fill backdrop-blur-md rounded-xl p-4 border border-glass-border">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-label-mono text-label-mono text-on-surface-variant">PENDUDUK (<?= e($periodeKependudukan) ?>)</span>
                                    <span class="text-primary font-caption text-caption"><?= formatAngka($totalJiwa) ?> jiwa</span>
                                </div>
                                <div class="w-full bg-surface-container-lowest rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full w-[<?= $pctLaki ?>%]" style="box-shadow: 0 0 10px rgba(158,230,56,0.5);"></div>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="font-caption text-caption text-on-surface-variant">Laki-laki <?= $pctLaki ?>%</span>
                                    <span class="font-caption text-caption text-on-surface-variant"><?= $jumlahDusun ?> dusun</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Floating Stats Bar -->
            <div class="absolute bottom-0 left-0 w-full translate-y-1/2 px-margin-mobile lg:px-margin-desktop z-30 pointer-events-none hidden md:block">
                <div class="max-w-container-max mx-auto">
                    <div class="bg-glass-fill backdrop-blur-xl border border-glass-border rounded-2xl p-6 lg:p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 shadow-2xl pointer-events-auto">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-glass-fill backdrop-blur-md flex items-center justify-center text-primary border border-glass-border">
                                <span class="material-symbols-outlined">group</span>
                            </div>
                            <div>
                                <h4 class="font-headline-lg text-headline-lg text-on-surface"><?= formatAngka($totalJiwa) ?>+</h4>
                                <p class="font-label-mono text-label-mono text-on-surface-variant">JIWA PENDUDUK<?= $periodeKependudukan !== '' ? ' (' . e($periodeKependudukan) . ')' : '' ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-glass-fill backdrop-blur-md flex items-center justify-center text-primary border border-glass-border">
                                <span class="material-symbols-outlined">home_work</span>
                            </div>
                            <div>
                                <h4 class="font-headline-lg text-headline-lg text-on-surface"><?= formatAngka($totalKK) ?>+</h4>
                                <p class="font-label-mono text-label-mono text-on-surface-variant">KEPALA KELUARGA</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-glass-fill backdrop-blur-md flex items-center justify-center text-primary border border-glass-border">
                                <span class="material-symbols-outlined">holiday_village</span>
                            </div>
                            <div>
                                <h4 class="font-headline-lg text-headline-lg text-on-surface"><?= $jumlahDusun ?></h4>
                                <p class="font-label-mono text-label-mono text-on-surface-variant">DUSUN</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-glass-fill backdrop-blur-md flex items-center justify-center text-primary border border-glass-border">
                                <span class="material-symbols-outlined">tour</span>
                            </div>
                            <div>
                                <h4 class="font-headline-lg text-headline-lg text-on-surface"><?= (int) $wisataCount ?></h4>
                                <p class="font-label-mono text-label-mono text-on-surface-variant">DESTINASI WISATA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php require __DIR__ . '/partials/section_potensi.php'; ?>
        <?php require __DIR__ . '/partials/section_kependudukan.php'; ?>
        <?php require __DIR__ . '/partials/section_wisata.php'; ?>
        <?php require __DIR__ . '/partials/section_berita.php'; ?>
        <!-- Profil Desa -->
        <section id="profil" class="w-full py-14 px-margin-mobile lg:px-margin-desktop bg-surface relative reveal">
            <div class="max-w-container-max mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-stack-lg gap-4">
                    <div>
                        <h2 class="font-label-mono text-label-mono text-primary mb-2">PROFIL</h2>
                        <h3 class="font-headline-lg text-[20px] md:text-[22px] text-on-surface max-w-lg">Visi &amp; Misi <?= e($namaPekon) ?>.</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-gutter items-stretch">
                    <div class="bg-glass-fill backdrop-blur-md rounded-[20px] p-5 lg:p-6 border border-glass-border relative overflow-hidden flex flex-col">
                        <div class="absolute -top-6 -right-6 text-[96px] text-primary/10 leading-none select-none pointer-events-none">"</div>
                        <div class="flex items-center gap-3 mb-4 relative z-10">
                            <span class="w-10 h-10 rounded-none bg-muted-forest/60 flex items-center justify-center text-primary"><span class="material-symbols-outlined text-[22px]">visibility</span></span>
                            <h4 class="font-headline-md text-headline-md text-on-surface">Visi</h4>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface leading-relaxed border-l-4 border-primary pl-4"><?= e($profil['visi'] ?? '') ?></p>
                    </div>
                    <div class="bg-glass-fill backdrop-blur-md rounded-[20px] p-5 lg:p-6 border border-glass-border flex flex-col">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-10 h-10 rounded-none bg-muted-forest/60 flex items-center justify-center text-primary"><span class="material-symbols-outlined text-[22px]">flag</span></span>
                            <h4 class="font-headline-md text-headline-md text-on-surface">Misi</h4>
                        </div>
                        <ol class="flex flex-col">
                            <?php
                            $misiBaris = array_values(array_filter(array_map('trim', explode("\n", $profil['misi'] ?? '')), fn($m) => $m !== ''));
                            foreach ($misiBaris as $i => $m):
                            ?>
                                <li class="flex items-start gap-4 py-3 <?= $i < count($misiBaris) - 1 ? 'border-b border-glass-border/40' : 'pb-0' ?>">
                                    <span class="shrink-0 w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center font-label-mono text-[12px] text-primary font-bold"><?= $i + 1 ?></span>
                                    <span class="font-body-md text-body-md text-on-surface-variant leading-relaxed pt-1"><?= e($m) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <?php require __DIR__ . '/partials/section_struktur_organisasi.php'; ?>
    </main>
    <?php require __DIR__ . '/partials/footer.php'; ?>
    <script src="<?= assetUrl('js/security-warning.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const chartEl = document.getElementById('chart-penduduk');
        if (chartEl) {
            new Chart(chartEl, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        data: [<?= (int) $totalLaki ?>, <?= (int) $totalPerempuan ?>],
                        backgroundColor: ['#9EE638', '#8CBEFF'],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    cutout: '72%',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        function reveal() {
            var reveals = document.querySelectorAll(".reveal");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }
        window.addEventListener("scroll", reveal);
        reveal();
    </script>
</body>

</html>
