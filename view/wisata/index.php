<?php
declare(strict_types=1);

$profil = getProfil();
$wisataList = getWisataWithGambar(getWisataList(true));

$q = trim((string) ($_GET['q'] ?? ''));
if ($q !== '') {
    $wisataList = array_values(array_filter($wisataList, fn($w) => mb_stripos($w['nama'], $q) !== false || mb_stripos($w['deskripsi'], $q) !== false));
}
$judulPage = 'Destinasi Wisata — Pekon Padang Cermin';
$deskripsiPage = truncate('Temukan keindahan alam dan kekayaan budaya Pekon Padang Cermin melalui pengalaman ekowisata yang berkelanjutan.', 155);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= e($judulPage) ?></title>
<link rel="icon" type="image/x-icon" href="<?= APP_BASE ?>/favicon.ico"/>
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1"/>
<meta name="description" content="<?= e($deskripsiPage) ?>"/>
<link rel="canonical" href="<?= e(APP_URL . '/wisata') ?>"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="<?= e($judulPage) ?>"/>
<meta property="og:description" content="<?= e($deskripsiPage) ?>"/>
<meta property="og:image" content="<?= e(APP_URL . '/assets/img/logo.png') ?>"/>
<meta property="og:url" content="<?= e(APP_URL . '/wisata') ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:title" content="<?= e($judulPage) ?>"/>
<meta name="twitter:description" content="<?= e($deskripsiPage) ?>"/>
<meta name="twitter:image" content="<?= e(APP_URL . '/assets/img/logo.png') ?>"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@100..900&family=Plus+Jakarta+Sans:wght@100..900&family=Space+Grotesk:wght@100..900&display=swap" rel="stylesheet"/>
<link href="<?= assetUrl('css/tailwind.css') ?>" rel="stylesheet"/>
</head>
<body class="bg-background font-body-md text-on-surface">
<?php require dirname(__DIR__) . '/landing/partials/header.php'; ?>
<main class="w-full pt-24">
<div class="flex flex-col w-full relative min-h-screen pb-section-gap">
<!-- Decorative Background Layer -->
<div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
<div class="absolute top-[10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-lime-glow blur-[120px] opacity-40 mix-blend-screen"></div>
<div class="absolute bottom-[-10%] right-[-5%] w-[50%] h-[50%] rounded-full bg-primary-container blur-[150px] opacity-20 mix-blend-screen"></div>
</div>
<!-- Page Header & Filters -->
<section class="w-full relative z-10 pt-margin-desktop px-margin-mobile lg:px-margin-desktop">
<div class="max-w-container-max mx-auto flex flex-col gap-stack-lg">
<div class="flex flex-col md:flex-row justify-between items-end gap-stack-md">
<div class="flex flex-col gap-stack-sm max-w-2xl">
<div class="inline-flex items-center gap-2 mb-2">
<span class="w-2 h-2 rounded-full bg-primary shadow-lime-glow animate-pulse"></span>
<span class="font-label-mono text-label-mono text-primary uppercase tracking-widest">Eksplorasi Desa</span>
</div>
<h1 class="font-headline-xl text-headline-xl text-on-surface">Destinasi Wisata Hijau</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">Temukan keindahan alam dan kekayaan budaya Pekon Padang Cermin melalui pengalaman ekowisata yang berkelanjutan.</p>
</div>
</div>
<!-- Search & Filter Glass Container -->
<div class="w-full bg-glass-fill backdrop-blur-xl border border-glass-border rounded-xl p-stack-md flex flex-col md:flex-row gap-gutter items-center justify-between transition-all hover:shadow-lg hover:border-outline-variant">
<form class="relative w-full md:max-w-md" method="get" action="<?= APP_BASE ?>/wisata">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
<input name="q" class="w-full bg-surface-container-high text-on-surface font-body-md py-3 pl-12 pr-4 rounded-lg border border-transparent focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-inner transition-all placeholder:text-on-surface-variant" placeholder="Cari destinasi wisata..." type="text" value="<?= e($q) ?>"/>
</form>
</div>
</div>
</section>
<!-- Destinations Grid -->
<section class="w-full relative z-10 mt-section-gap px-margin-mobile lg:px-margin-desktop">
<div class="max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
<?php foreach ($wisataList as $w): $gambarUtama = $w['gambar'][0] ?? null; ?>
<article class="group relative bg-glass-fill backdrop-blur-md border border-glass-border rounded-[20px] overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:border-primary/40">
<a href="<?= APP_BASE ?>/wisata/<?= e($w['slug']) ?>" class="relative w-full aspect-video overflow-hidden block">
<div class="absolute inset-0 bg-gradient-to-t from-surface to-transparent z-10 opacity-60"></div>
<?php if ($gambarUtama !== null && fotoAda($gambarUtama['path_gambar'])): ?>
<img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="<?= e($w['nama']) ?>" loading="lazy" src="<?= e(uploadUrl($gambarUtama['path_gambar'])) ?>"/>
<?php elseif ($gambarUtama !== null): ?>
<div class="w-full h-full bg-gradient-to-br from-primary/70 to-primary/30 flex items-center justify-center">
<span class="text-[32px] font-bold text-white"><?= e(inisialNama($w['nama'])) ?></span>
</div>
<?php else: ?>
<div class="w-full h-full bg-surface-container-high flex items-center justify-center">
<span class="material-symbols-outlined text-on-surface-variant text-[48px]">landscape</span>
</div>
<?php endif; ?>
</a>
<div class="p-stack-lg flex flex-col flex-grow relative z-20 bg-gradient-to-b from-surface/50 to-surface/90">
<h3 class="font-headline-md text-headline-md text-on-surface mb-2 group-hover:text-primary transition-colors"><a href="<?= APP_BASE ?>/wisata/<?= e($w['slug']) ?>"><?= e($w['nama']) ?></a></h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-6 line-clamp-2"><?= e(truncate($w['deskripsi'], 120)) ?></p>
<div class="mt-auto flex items-center justify-between border-t border-glass-border pt-4">
<div class="flex items-center gap-1 text-on-surface-variant font-label-mono text-label-mono">
<span class="material-symbols-outlined text-[16px]">location_on</span>
<span><?= e($w['alamat']) ?></span>
</div>
<a href="<?= APP_BASE ?>/wisata/<?= e($w['slug']) ?>" class="inline-flex items-center gap-2 text-primary font-body-md font-bold hover:text-primary-fixed transition-colors">
Lihat Detail <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
</a>
</div>
</div>
</article>
<?php endforeach; ?>
<?php if ($wisataList === []): ?>
<div class="col-span-full py-16 text-center text-on-surface-variant font-body-lg">Belum ada destinasi wisata yang dipublikasikan<?= $q !== '' ? ' untuk pencarian "' . e($q) . '"' : '' ?>.</div>
<?php endif; ?>
</div>
</section>
<!-- Map Section -->
<section class="w-full relative z-10 mt-section-gap px-margin-mobile lg:px-margin-desktop">
<div class="max-w-container-max mx-auto bg-surface-container-low rounded-3xl overflow-hidden border border-glass-border shadow-xl flex flex-col lg:flex-row">
<div class="p-stack-lg lg:w-1/3 flex flex-col justify-center bg-gradient-to-br from-surface-container to-surface">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Peta Pariwisata</h2>
<p class="font-body-md text-body-md text-on-surface-variant mb-8">Rencanakan perjalanan Anda. Jelajahi lokasi destinasi wisata, fasilitas umum, dan rute terbaik di sekitar Pekon Padang Cermin.</p>
<div class="flex flex-col gap-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center border border-glass-border">
<span class="material-symbols-outlined text-primary">park</span>
</div>
<div class="flex flex-col">
<span class="font-body-md text-body-md text-on-surface font-bold">Titik Hijau</span>
<span class="font-caption text-caption text-on-surface-variant">Kawasan Alam &amp; Konservasi</span>
</div>
</div>
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center border border-glass-border">
<span class="material-symbols-outlined text-primary">museum</span>
</div>
<div class="flex flex-col">
<span class="font-body-md text-body-md text-on-surface font-bold">Titik Budaya</span>
<span class="font-caption text-caption text-on-surface-variant">Situs Sejarah &amp; Seni Lokal</span>
</div>
</div>
</div>
</div>
<div class="lg:w-2/3 h-[400px] lg:h-auto min-h-[400px] relative">
<div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAGqljW3U2Scs3-WfyBJSQxN4x_fU9vFVHulFCO45IFFk-zR-XbsvVtMbMrm_pfLRdj9eUq-VqdGpuXAj1g0KYUegE4Wul34TF-DrDTWLiwix14XLdURFZm2Jfnjrt9sOMGxw59AoTpRGdv65ayi-WkJ5Flq7aYpW3Pojcfk70bI5zD_aaDq2UcXA4rf3_QvT87vLEZ5B9RG2pFEnJkqhgXSd6_sK682gO6eId-kbNV4FFE0_H5HcM9')"></div>
<div class="absolute inset-y-0 left-0 w-24 bg-gradient-to-r from-surface-container-low to-transparent hidden lg:block"></div>
</div>
</div>
</section>
</div>
</main>
<?php require dirname(__DIR__) . '/landing/partials/footer.php'; ?>
<script src="<?= assetUrl('js/security-warning.js') ?>"></script>
</body>
</html>
