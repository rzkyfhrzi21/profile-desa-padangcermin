<?php
declare(strict_types=1);

$profil = getProfil();
$wisata = getWisataBySlug($params['slug'] ?? '');

if ($wisata === null) {
    http_response_code(404);
    echo 'Halaman tidak ditemukan.';
    exit;
}

$gambar = getWisataImages((int) $wisata['id']);
$gambarUtama = $gambar[0] ?? null;
$judulPage = $wisata['nama'] . ' — Wisata Pekon Padang Cermin';
$deskripsiPage = truncate($wisata['deskripsi'], 155);
$ogImage = $gambarUtama !== null ? APP_URL . '/uploads/' . $gambarUtama['path_gambar'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= e($judulPage) ?></title>
<link rel="icon" type="image/x-icon" href="<?= APP_BASE ?>/favicon.ico"/>
<meta name="description" content="<?= e($deskripsiPage) ?>"/>
<link rel="canonical" href="<?= e(APP_URL . '/wisata/' . $wisata['slug']) ?>"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="<?= e($judulPage) ?>"/>
<meta property="og:description" content="<?= e($deskripsiPage) ?>"/>
<?php if ($ogImage !== ''): ?><meta property="og:image" content="<?= e($ogImage) ?>"/><?php endif; ?>
<meta property="og:url" content="<?= e(APP_URL . '/wisata/' . $wisata['slug']) ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:title" content="<?= e($judulPage) ?>"/>
<meta name="twitter:description" content="<?= e($deskripsiPage) ?>"/>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "TouristAttraction",
    "name": <?= json_encode($wisata['nama'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    "description": <?= json_encode(truncate($wisata['deskripsi'], 500), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    "url": <?= json_encode(APP_URL . '/wisata/' . $wisata['slug'], JSON_UNESCAPED_SLASHES) ?>,
    "address": <?= json_encode(['@type' => 'PostalAddress', 'streetAddress' => $wisata['alamat'], 'addressCountry' => 'ID'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    "openingHours": <?= json_encode($wisata['jam_buka'] ?: 'Buka setiap hari', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
}
</script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@100..900&family=Plus+Jakarta+Sans:wght@100..900&family=Space+Grotesk:wght@100..900&display=swap" rel="stylesheet"/>
<link href="<?= assetUrl('css/tailwind.css') ?>" rel="stylesheet"/>
</head>
<body class="bg-background font-body-md text-on-surface">
<?php require dirname(__DIR__) . '/landing/partials/header.php'; ?>
<main class="w-full pt-24">
<div class="flex flex-col w-full relative">
<div class="relative w-full h-[600px] -mt-20 overflow-hidden flex items-end pt-32 pb-16">
<?php if ($gambarUtama !== null): ?>
<div class="absolute inset-0 z-0 bg-cover bg-center" style="background-image: url('<?= e(uploadUrl($gambarUtama['path_gambar'])) ?>')"></div>
<?php else: ?>
<div class="absolute inset-0 z-0 bg-surface-container-high"></div>
<?php endif; ?>
<div class="absolute inset-0 z-10 bg-gradient-to-t from-background via-background/60 to-transparent"></div>
<div class="relative z-20 w-full max-w-container-max mx-auto px-margin-mobile lg:px-margin-desktop">
<div class="flex items-center gap-2 mb-4 text-caption text-secondary font-caption uppercase tracking-wider">
<a class="hover:text-primary transition-colors" href="<?= APP_BASE ?>/">Beranda</a>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
<a class="hover:text-primary transition-colors" href="<?= APP_BASE ?>/wisata">Wisata</a>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
<span class="text-primary font-bold">Detail</span>
</div>
<h1 class="font-headline-xl text-headline-xl text-on-surface mb-4 max-w-3xl">
<?= e($wisata['nama']) ?>
</h1>
<div class="flex flex-wrap items-center gap-4 mt-6">
<?php if ($wisata['harga_tiket'] !== '' && $wisata['harga_tiket'] !== null): ?>
<div class="flex items-center gap-2 bg-surface-container-high/80 backdrop-blur-md px-4 py-2 rounded-full border border-glass-border">
<span class="material-symbols-outlined text-primary text-[20px]">confirmation_number</span>
<span class="font-body-md text-on-surface font-bold"><?= e($wisata['harga_tiket']) ?></span>
</div>
<?php endif; ?>
<div class="flex items-center gap-2 bg-surface-container-high/80 backdrop-blur-md px-4 py-2 rounded-full border border-glass-border">
<span class="material-symbols-outlined text-primary text-[20px]">nature_people</span>
<span class="font-body-md text-on-surface font-bold">Wisata Alam</span>
</div>
<?php if ($wisata['jam_buka'] !== '' && $wisata['jam_buka'] !== null): ?>
<div class="flex items-center gap-2 bg-surface-container-high/80 backdrop-blur-md px-4 py-2 rounded-full border border-glass-border">
<span class="material-symbols-outlined text-status-success text-[20px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<span class="font-body-md text-on-surface font-bold"><?= e($wisata['jam_buka']) ?></span>
</div>
<?php endif; ?>
</div>
</div>
</div>
<div class="max-w-container-max mx-auto px-margin-mobile lg:px-margin-desktop py-section-gap w-full grid grid-cols-1 lg:grid-cols-12 gap-gutter relative z-30">
<div class="lg:col-span-8 flex flex-col gap-12">
<div class="prose prose-invert max-w-none">
<p class="font-body-lg text-body-lg text-on-surface leading-relaxed mb-6">
<?= nl2br(e($wisata['deskripsi'])) ?>
</p>
<h3 class="font-headline-md text-headline-md text-primary mt-10 mb-4">Aktivitas &amp; Fasilitas</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-8">
<div class="bg-surface-container-low p-6 rounded-2xl flex items-start gap-4 hover:-translate-y-1 transition-transform duration-300">
<div class="bg-surface-container p-3 rounded-xl">
<span class="material-symbols-outlined text-primary text-[24px]">photo_camera</span>
</div>
<div>
<h4 class="font-headline-md text-body-lg text-on-surface mb-2">Spot Fotografi</h4>
<p class="font-body-md text-caption text-on-surface-variant">Gardu pandang khusus disediakan untuk mengambil foto lanskap terbaik saat matahari terbit dan terbenam.</p>
</div>
</div>
<div class="bg-surface-container-low p-6 rounded-2xl flex items-start gap-4 hover:-translate-y-1 transition-transform duration-300">
<div class="bg-surface-container p-3 rounded-xl">
<span class="material-symbols-outlined text-primary text-[24px]">restaurant</span>
</div>
<div>
<h4 class="font-headline-md text-body-lg text-on-surface mb-2">Saung Kuliner</h4>
<p class="font-body-md text-caption text-on-surface-variant">Menikmati kopi lokal dan hidangan tradisional khas desa di saung bambu yang menghadap langsung ke hamparan alam.</p>
</div>
</div>
<div class="bg-surface-container-low p-6 rounded-2xl flex items-start gap-4 hover:-translate-y-1 transition-transform duration-300">
<div class="bg-surface-container p-3 rounded-xl">
<span class="material-symbols-outlined text-primary text-[24px]">directions_walk</span>
</div>
<div>
<h4 class="font-headline-md text-body-lg text-on-surface mb-2">Trekking Alam</h4>
<p class="font-body-md text-caption text-on-surface-variant">Jalur trekking ringan menyusuri pinggiran bukit dan sungai kecil yang mengaliri area wisata.</p>
</div>
</div>
<div class="bg-surface-container-low p-6 rounded-2xl flex items-start gap-4 hover:-translate-y-1 transition-transform duration-300">
<div class="bg-surface-container p-3 rounded-xl">
<span class="material-symbols-outlined text-primary text-[24px]">local_florist</span>
</div>
<div>
<h4 class="font-headline-md text-body-lg text-on-surface mb-2">Edukasi Lingkungan</h4>
<p class="font-body-md text-caption text-on-surface-variant">Program edukasi singkat tentang konservasi alam dan budaya lokal bersama warga sekitar.</p>
</div>
</div>
</div>
</div>
</div>
<div class="lg:col-span-4 flex flex-col gap-8">
<div class="bg-glass-fill backdrop-blur-xl border border-glass-border rounded-3xl p-8 sticky top-32 shadow-xl transition-all duration-300 hover:border-primary/40 group">
<div class="absolute top-0 left-0 w-full h-[3px] bg-primary rounded-t-3xl opacity-80"></div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-6 flex items-center gap-3">
<span class="material-symbols-outlined text-primary p-2 bg-primary/10 rounded-lg">info</span>
Informasi Kunjungan
</h3>
<div class="flex flex-col gap-6">
<?php if ($wisata['harga_tiket'] !== '' && $wisata['harga_tiket'] !== null): ?>
<div class="flex justify-between items-center border-b border-white/5 pb-4">
<div class="flex flex-col">
<span class="font-caption text-caption text-on-surface-variant uppercase tracking-wider mb-1">Harga Tiket Masuk</span>
<span class="font-body-lg text-body-lg font-bold text-on-surface"><?= e($wisata['harga_tiket']) ?></span>
</div>
</div>
<?php endif; ?>
<?php if ($wisata['jam_buka'] !== '' && $wisata['jam_buka'] !== null): ?>
<div class="flex justify-between items-center border-b border-white/5 pb-4">
<div class="flex flex-col">
<span class="font-caption text-caption text-on-surface-variant uppercase tracking-wider mb-1">Jam Operasional</span>
<span class="font-body-md text-body-md text-on-surface"><?= e($wisata['jam_buka']) ?></span>
</div>
</div>
<?php endif; ?>
<div class="flex flex-col gap-3">
<span class="font-caption text-caption text-on-surface-variant uppercase tracking-wider">Lokasi</span>
<div class="w-full h-32 rounded-xl overflow-hidden" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuD3a7EOOoJRHD7r0wz1Wk64TYuqKaQgFoNVfLWnIUZe7IaQkHamuKEvDQ7XIJb_h9hUBLw6EE-DK3zlIxZOPGeecpShyS32oaDp9Z3SZvIki3h_R32jbN0ZNhSmJW3vrBy-FddNstSzOU3oj2mV1Q7PkVn-0_uBNdjdeQj4Mlir5cRhbef-gMzKvYRA5zQXlPM3-SQH4gVv2dPpgqqGRDbx48k9L2kt4pjH0p5gDNo2nhrOllgK9k3Z'); background-size: cover; background-position: center;"></div>
<p class="font-body-md text-caption text-on-surface-variant mt-2 flex items-start gap-2">
<span class="material-symbols-outlined text-[16px] text-primary shrink-0 mt-0.5">location_on</span>
<?= e($wisata['alamat']) ?>
</p>
</div>
<button class="w-full bg-primary text-on-primary py-4 rounded-full font-body-md font-bold mt-4 hover:shadow-lime-glow transition-all duration-300 flex justify-center items-center gap-2 group-hover:-translate-y-1">
Pesan Tiket Sekarang <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
</button>
<button class="w-full bg-transparent border border-outline-variant text-on-surface py-3 rounded-full font-body-md hover:bg-surface-container transition-colors flex justify-center items-center gap-2">
Simpan ke Rencana <span class="material-symbols-outlined text-[18px]">bookmark_add</span>
</button>
</div>
</div>
<?php if ($gambar !== []): ?>
<?php $totalGambar = count($gambar); ?>
<div class="flex flex-col gap-4">
<h4 class="font-headline-md text-body-lg font-bold text-on-surface">Galeri Visual</h4>
<div class="relative" <?= $totalGambar > 1 ? 'data-carousel data-carousel-interval="1500"' : '' ?>>
<div class="overflow-hidden rounded-2xl">
<?php foreach ($gambar as $i => $g): ?>
<div class="carousel-slide">
<div class="w-full aspect-video rounded-2xl overflow-hidden cursor-pointer group">
<img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy" data-lightbox data-skeleton alt="<?= e($g['alt_text'] !== '' ? $g['alt_text'] : $wisata['nama']) ?>" src="<?= e(uploadUrl($g['path_gambar'])) ?>"/>
</div>
</div>
<?php endforeach; ?>
</div>
<?php if ($totalGambar > 1): ?>
<div class="flex items-center justify-center gap-4 mt-4">
<button type="button" data-carousel-prev class="carousel-btn w-10 h-10 rounded-full border border-glass-border bg-glass-fill text-on-surface-variant flex items-center justify-center" aria-label="Sebelumnya">
<span class="material-symbols-outlined text-[18px]">chevron_left</span>
</button>
<div class="carousel-dots flex items-center gap-2">
<?php foreach ($gambar as $i => $g): ?>
<button type="button" data-carousel-dot class="carousel-dot" aria-label="Gambar <?= $i + 1 ?>"></button>
<?php endforeach; ?>
</div>
<button type="button" data-carousel-next class="carousel-btn w-10 h-10 rounded-full border border-glass-border bg-glass-fill text-on-surface-variant flex items-center justify-center" aria-label="Berikutnya">
<span class="material-symbols-outlined text-[18px]">chevron_right</span>
</button>
</div>
<?php endif; ?>
</div>
</div>
<?php endif; ?>
</div>
</div>
</div>
</main>
<?php require dirname(__DIR__) . '/landing/partials/footer.php'; ?>
<script src="<?= assetUrl('js/security-warning.js') ?>"></script>
</body>
</html>
