<?php
declare(strict_types=1);

$profil = getProfil();
$berita = getBeritaBySlug($params['slug'] ?? '');

if ($berita === null) {
    http_response_code(404);
    echo 'Halaman tidak ditemukan.';
    exit;
}

tambahViewBerita((int) $berita['id']);
$penulisNama = getAdminName((int) $berita['penulis_id']);
$beritaTerkait = array_values(array_filter(getBeritaList(true, 5), fn($b) => $b['id'] !== $berita['id']));
$judulPage = $berita['judul'] . ' — Warta Pekon';
$deskripsiPage = truncate($berita['konten'], 155);
$ogImage = $berita['gambar_utama'] !== null && $berita['gambar_utama'] !== '' ? APP_URL . '/uploads/' . $berita['gambar_utama'] : '';
$shareUrl = urlencode(APP_URL . '/berita/' . $berita['slug']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= e($judulPage) ?></title>
<link rel="icon" type="image/x-icon" href="<?= APP_BASE ?>/favicon.ico"/>
<meta name="description" content="<?= e($deskripsiPage) ?>"/>
<link rel="canonical" href="<?= e(APP_URL . '/berita/' . $berita['slug']) ?>"/>
<meta property="og:type" content="article"/>
<meta property="og:title" content="<?= e($judulPage) ?>"/>
<meta property="og:description" content="<?= e($deskripsiPage) ?>"/>
<?php if ($ogImage !== ''): ?><meta property="og:image" content="<?= e($ogImage) ?>"/><?php endif; ?>
<meta property="og:url" content="<?= e(APP_URL . '/berita/' . $berita['slug']) ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:title" content="<?= e($judulPage) ?>"/>
<meta name="twitter:description" content="<?= e($deskripsiPage) ?>"/>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": <?= json_encode($berita['judul'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    "description": <?= json_encode(truncate($berita['konten'], 300), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    "datePublished": <?= json_encode($berita['published_at'], JSON_UNESCAPED_SLASHES) ?>,
    "dateModified": <?= json_encode($berita['updated_at'], JSON_UNESCAPED_SLASHES) ?>,
    "author": <?= json_encode(['@type' => 'Person', 'name' => $penulisNama], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    "publisher": <?= json_encode(['@type' => 'Organization', 'name' => $profil['nama_pekon'] ?? 'Pekon Padang Cermin'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    "url": <?= json_encode(APP_URL . '/berita/' . $berita['slug'], JSON_UNESCAPED_SLASHES) ?>
}
</script>
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
<div class="absolute top-[8%] left-[-10%] w-[40%] h-[40%] rounded-full bg-lime-glow blur-[140px] opacity-25 mix-blend-screen"></div>
</div>
<div class="max-w-container-max mx-auto px-margin-mobile lg:px-margin-desktop py-section-gap w-full grid grid-cols-1 lg:grid-cols-12 gap-gutter relative z-10">
<div class="lg:col-span-8 flex flex-col gap-stack-lg relative">
<div class="absolute -top-[150px] -left-[150px] w-[300px] h-[300px] bg-primary/10 rounded-full blur-[100px] pointer-events-none"></div>
<header class="flex flex-col gap-stack-sm z-10">
<div class="flex items-center gap-3">
<span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-surface-container-high text-primary font-label-mono text-label-mono uppercase tracking-widest border border-glass-border"><?= e($berita['kategori_nama'] ?? 'Berita') ?></span>
<span class="text-on-surface-variant font-caption text-caption flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">calendar_today</span> <?= e(formatTanggal($berita['published_at'])) ?>
</span>
<span class="text-on-surface-variant font-caption text-caption flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">visibility</span> <?= formatAngka($berita['views']) ?> dilihat
</span>
</div>
<h1 class="font-headline-xl text-headline-xl-mobile lg:text-headline-xl text-on-surface text-balance mt-4"><?= e($berita['judul']) ?></h1>
<div class="flex items-center gap-3 mt-4">
<div class="w-10 h-10 rounded-full bg-surface-container-highest flex items-center justify-center text-primary font-bold">
<span class="material-symbols-outlined text-[20px]">person</span>
</div>
<div class="flex flex-col">
<span class="font-headline-md text-[16px] text-on-surface"><?= e($penulisNama) ?></span>
<span class="font-caption text-caption text-on-surface-variant">Jurnalis Desa</span>
</div>
</div>
</header>
<div class="w-full aspect-[16/9] lg:aspect-[21/9] rounded-[24px] overflow-hidden relative shadow-xl mt-4 z-10 group">
<img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 cursor-zoom-in" data-lightbox data-skeleton alt="<?= e($berita['alt_gambar'] !== '' ? $berita['alt_gambar'] : $berita['judul']) ?>" src="<?= $berita['gambar_utama'] !== null && $berita['gambar_utama'] !== '' ? e(uploadUrl($berita['gambar_utama'])) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuDmjxvf4L6LYk7lrcIgYMfBWQG9c09vKS2AwmbY-AhPdZPBaeY5X5IaVfBULE8PLX_T-Tdi-fr0l2RoFrXpceuB2TfZRkQVBF9fgWfxxAgimEE4TnY7suqXbTCgJ5pRYpgyH3CLOoZ3mBcu27pg6_NjUq9fHz4QjntigxOcEAaEJRE4NAOlArsJ9Q6WUdBIohHAVxSqAdbi7IoarvMh2yUUqEIBoe-X_a8OVIAjQZgi2CGi-GgHIb_F' ?>"/>
<div class="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent opacity-80 pointer-events-none"></div>
</div>
<div class="prose prose-invert prose-lg max-w-none text-on-surface-variant font-body-lg z-10 mt-6 space-y-6">
<p class="font-body-lg text-body-lg text-on-surface leading-relaxed whitespace-pre-line"><?= nl2br(e($berita['konten'])) ?></p>
</div>
<div class="flex flex-wrap items-center gap-4 pt-8 mt-12 border-t border-glass-border">
<span class="font-label-mono text-label-mono text-on-surface-variant">Bagikan:</span>
<a class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center hover:bg-primary hover:text-on-primary transition-colors border border-glass-border" href="https://wa.me/?text=<?= $shareUrl ?>" target="_blank" rel="noopener" aria-label="Bagikan ke WhatsApp">
<span class="material-symbols-outlined text-[20px]">share</span>
</a>
<button class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center hover:bg-primary hover:text-on-primary transition-colors border border-glass-border" type="button" onclick="navigator.clipboard.writeText(window.location.href);" aria-label="Salin tautan">
<span class="material-symbols-outlined text-[20px]">link</span>
</button>
</div>
</div>
<aside class="lg:col-span-4 mt-12 lg:mt-0 flex flex-col gap-stack-lg">
<div class="sticky top-28 bg-surface-container-low p-6 rounded-[20px] border border-glass-border backdrop-blur-lg">
<h3 class="font-headline-md text-headline-md text-on-surface mb-6 flex items-center gap-2">
<span class="material-symbols-outlined text-primary">article</span> Berita Terkait
</h3>
<div class="flex flex-col gap-6">
<?php foreach ($beritaTerkait as $bt): ?>
<a class="group flex gap-4 items-start" href="<?= APP_BASE ?>/berita/<?= e($bt['slug']) ?>">
<div class="w-24 h-24 rounded-lg overflow-hidden shrink-0 bg-surface-container-highest">
<img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy" alt="<?= e($bt['alt_gambar'] !== '' ? $bt['alt_gambar'] : $bt['judul']) ?>" src="<?= $bt['gambar_utama'] !== null && $bt['gambar_utama'] !== '' ? e(uploadUrl($bt['gambar_utama'])) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuCEZQXtT2xMXJJ_v5WDTXOEBOKrh8mhkb5gMIUWVVmtI8cDhaE_ABlW39pVJspXTcgAFf5p8ab2QuhNKrxGZ0KT08hhlwmCi_uu2fl2jl5ingVeAfHWDk5hhTDghG7P44flHU2_cL08r1Y_GiG_pS530cHkygnRTbGh8SILyGMURHUNESYfJJ74rJTD_45bDzu9qBrmijPQYdurYJczXAaYKHjzenwYr7bjwUq-RDe1Xz0HlsCW5hqM' ?>"/>
</div>
<div class="flex flex-col gap-1">
<span class="font-label-mono text-[11px] text-primary uppercase tracking-wider"><?= e($bt['kategori_nama'] ?? 'Berita') ?></span>
<h4 class="font-headline-md text-[15px] leading-tight text-on-surface group-hover:text-primary transition-colors line-clamp-2"><?= e($bt['judul']) ?></h4>
<span class="font-caption text-[12px] text-on-surface-variant mt-1"><?= e(formatTanggal($bt['published_at'])) ?></span>
</div>
</a>
<?php endforeach; ?>
<?php if ($beritaTerkait === []): ?>
<p class="text-on-surface-variant font-caption">Belum ada berita lain.</p>
<?php endif; ?>
</div>
</div>
</aside>
</div>
</div>
</main>
<?php require dirname(__DIR__) . '/landing/partials/footer.php'; ?>
<script src="<?= assetUrl('js/security-warning.js') ?>"></script>
</body>
</html>
