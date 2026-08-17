<?php
declare(strict_types=1);

$profil = getProfil();
$page = max(1, (int) ($_GET['page'] ?? 1));
$perHalaman = 7;
$beritaList = getBeritaList(true, $perHalaman, $page);
$totalBerita = countBerita(true);
$totalHalaman = max(1, (int) ceil($totalBerita / $perHalaman));
if ($page > $totalHalaman) {
    redirect('/berita');
}
$beritaUtama = $beritaList[0] ?? null;
$beritaLain = $beritaUtama !== null ? array_slice($beritaList, 1) : [];
$judulPage = 'Berita Desa — Desa Padang Cermin';
$deskripsiPage = truncate('Kabar terbaru seputar kegiatan, program, dan perkembangan Pekon Padang Cermin, Kecamatan Padang Cermin, Kabupaten Pesawaran, Lampung.', 155);
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
<link rel="canonical" href="<?= e(APP_URL . '/berita') ?>"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="<?= e($judulPage) ?>"/>
<meta property="og:description" content="<?= e($deskripsiPage) ?>"/>
<meta property="og:image" content="<?= e(APP_URL . '/assets/img/logo.png') ?>"/>
<meta property="og:url" content="<?= e(APP_URL . '/berita') ?>"/>
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
<div class="flex flex-col w-full relative min-h-screen pb-10">
<!-- Decorative Background Layer -->
<div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
<div class="absolute top-[5%] right-[-10%] w-[45%] h-[45%] rounded-full bg-lime-glow blur-[140px] opacity-30 mix-blend-screen"></div>
<div class="absolute bottom-[-5%] left-[-8%] w-[40%] h-[40%] rounded-full bg-primary-container blur-[140px] opacity-15 mix-blend-screen"></div>
</div>
<!-- Page Header -->
<section class="w-full relative z-10 pt-margin-desktop px-margin-mobile lg:px-margin-desktop">
<div class="max-w-container-max mx-auto flex flex-col gap-stack-lg">
<div class="flex flex-col gap-stack-sm max-w-2xl">
<div class="inline-flex items-center gap-2 mb-2">
<span class="w-2 h-2 rounded-full bg-primary shadow-lime-glow animate-pulse"></span>
<span class="font-label-mono text-label-mono text-primary uppercase tracking-widest">Berita Desa</span>
</div>
<h1 class="font-headline-xl text-headline-xl text-on-surface">Berita Desa Padang Cermin</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">Kabar terbaru seputar kegiatan, program, dan perkembangan pekon yang layak untuk diketahui masyarakat.</p>
</div>
</div>
</section>
<!-- Featured Berita -->
<section class="w-full relative z-10 mt-section-gap px-margin-mobile lg:px-margin-desktop">
<div class="max-w-container-max mx-auto">
<?php if ($beritaUtama !== null): ?>
<article class="group grid grid-cols-1 lg:grid-cols-2 gap-gutter bg-glass-fill backdrop-blur-md border border-glass-border rounded-3xl overflow-hidden shadow-xl hover:border-primary/40 transition-colors duration-300">
<div class="relative aspect-[16/10] overflow-hidden">
<?php $gU = $beritaUtama['gambar_utama'] ?? ''; ?>
<?php if ($gU !== '' && !fotoAda($gU)): ?>
<div class="absolute inset-0 bg-gradient-to-br from-primary/70 to-primary/30 flex items-center justify-center">
<span class="text-[44px] font-bold text-white"><?= e(inisialNama($beritaUtama['judul'])) ?></span>
</div>
<?php else: ?>
<img class="object-cover w-full h-full transform group-hover:scale-105 transition-transform duration-700 ease-out" alt="<?= e($beritaUtama['judul']) ?>" src="<?= $gU !== '' ? e(uploadUrl($gU)) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuBRcM4Qyr3XfXm-elbFFZwnQziilLYVjnNWRZVJ24IbcbNyC_D06SmeCNOZdZYipLindZ5VQajSxi-9e9hI0GKZdXfaw8OWcXhsctKBM-07N8D1dqiWDw7Q9BxYd9vs6uruIFfJ_d4_CKxcAq5C31MLiM1uW24_NXMCusXSu7dROEmOf9qNKLd7A_BIIquHndusafowOemhKnVA5ZfQnWIQzIbM27UyYhQfvi_GZKNLAo7qjPCc96IQ' ?>" />
<?php endif; ?>
<div class="absolute top-4 left-4 flex gap-2">
<span class="bg-muted-forest text-primary font-caption text-caption px-3 py-1 rounded-full flex items-center gap-1 backdrop-blur-md">
<span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
<?= e($beritaUtama['kategori_nama'] ?? 'Berita') ?>
</span>
</div>
<span class="absolute bottom-4 left-4 z-20 inline-flex items-center gap-1.5 bg-black/60 backdrop-blur-md text-white text-[11px] px-2.5 py-1 rounded-full">
<span class="material-symbols-outlined text-[12px]">calendar_today</span> <?= e(formatTanggal($beritaUtama['published_at'])) ?>
</span>
</div>
<div class="relative flex flex-col justify-center gap-3 z-10 p-4 md:p-5">
<h2 class="font-headline-lg text-[22px] text-on-surface tracking-tight leading-tight group-hover:text-primary transition-colors duration-300 line-clamp-2"><a href="<?= APP_BASE ?>/berita/<?= e($beritaUtama['slug']) ?>"><?= e($beritaUtama['judul']) ?></a></h2>
<p class="font-body-md text-[14px] text-on-surface-variant line-clamp-3"><?= e(truncate($beritaUtama['konten'], 220)) ?></p>
<a class="inline-flex items-center gap-2 text-primary font-body-md font-bold mt-1 group/btn w-fit" href="<?= APP_BASE ?>/berita/<?= e($beritaUtama['slug']) ?>">
<span>Baca Selengkapnya</span>
<span class="material-symbols-outlined transform group-hover/btn:translate-x-1 transition-transform duration-300">arrow_forward</span>
</a>
</div>
</article>
<?php endif; ?>
<!-- Grid Berita -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter mt-stack-md">
<?php foreach ($beritaLain as $b): ?>
<article class="bg-surface-container rounded-[20px] p-4 flex flex-col gap-3 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 shadow-md hover:shadow-lime-glow/20">
<div class="absolute inset-0 rounded-[20px] shadow-[inset_0_0_0_1px_rgba(255,255,255,0.08)] pointer-events-none group-hover:shadow-[inset_0_0_0_1px_rgba(158,230,56,0.4)] transition-shadow duration-300"></div>
<a href="<?= APP_BASE ?>/berita/<?= e($b['slug']) ?>" class="w-full aspect-[16/10] rounded-xl overflow-hidden relative block">
<?php $gB = $b['gambar_utama'] ?? ''; ?>
<?php if ($gB !== '' && !fotoAda($gB)): ?>
<div class="w-full h-full bg-gradient-to-br from-primary/70 to-primary/30 flex items-center justify-center">
<span class="text-[32px] font-bold text-white"><?= e(inisialNama($b['judul'])) ?></span>
</div>
<?php else: ?>
<img class="object-cover w-full h-full transform group-hover:scale-105 transition-transform duration-500" alt="<?= e($b['judul']) ?>" loading="lazy" src="<?= $gB !== '' ? e(uploadUrl($gB)) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuA1JrYpRDj9x6e2os6Ci4mAYfH7XwxBjwPcKe7pBFw6cQ2kB_kJyHamwvz5_coKAOksyoabWG-BXALRgcW_9dr6yGJTZLCzc7ppyhYNPXq-JmfyjsXDrmO19x4KWTZA8jJ8P0J42A6KDROoFotGU2xXUi0FeJBB9DJRbVklKfns4Jm1IVFoDPhOQoi4UXZ1F_TZlZ8cQ4CSdxXEPRzKD1mE6nmDn_Fu-S5c863L-W5T4UTGjmajO1Ge' ?>" />
<?php endif; ?>
<div class="absolute top-3 left-3">
<span class="bg-surface-container/80 backdrop-blur-md text-on-surface font-caption text-caption px-3 py-1 rounded-full shadow-sm"><?= e($b['kategori_nama'] ?? 'Berita') ?></span>
</div>
<span class="absolute bottom-3 left-3 z-20 inline-flex items-center gap-1 bg-black/60 backdrop-blur-md text-white text-[10px] px-2 py-0.5 rounded-full">
<span class="material-symbols-outlined text-[11px]">calendar_today</span> <?= e(formatTanggal($b['published_at'])) ?>
</span>
</a>
<div class="flex flex-col flex-grow gap-1.5 z-10">
<h3 class="font-headline-md text-[15px] text-on-surface group-hover:text-primary transition-colors line-clamp-2 leading-snug"><a href="<?= APP_BASE ?>/berita/<?= e($b['slug']) ?>"><?= e($b['judul']) ?></a></h3>
<p class="font-body-md text-[13px] text-on-surface-variant line-clamp-2 mt-0.5"><?= e(truncate($b['konten'], 150)) ?></p>
</div>
<div class="mt-auto pt-3 flex justify-between items-center z-10 border-t border-glass-border/30">
<a class="text-primary font-label-mono text-label-mono hover:underline decoration-primary/50 underline-offset-4 flex items-center gap-1 group/link" href="<?= APP_BASE ?>/berita/<?= e($b['slug']) ?>">
Selengkapnya
<span class="material-symbols-outlined text-[16px] group-hover/link:translate-x-0.5 transition-transform">arrow_right_alt</span>
</a>
</div>
</article>
<?php endforeach; ?>
<?php if ($beritaList === []): ?>
<div class="col-span-full py-16 text-center text-on-surface-variant font-body-lg">Belum ada berita yang dipublikasikan.</div>
<?php endif; ?>
</div>
<!-- Pagination -->
<?php if ($totalHalaman > 1): ?>
<div class="flex justify-center items-center gap-2 mt-stack-lg">
<?php if ($page > 1): ?>
<a class="w-10 h-10 rounded-full flex items-center justify-center bg-surface-container hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface transition-colors" href="<?= APP_BASE ?>/berita?page=<?= $page - 1 ?>">
<span class="material-symbols-outlined text-[20px]">chevron_left</span>
</a>
<?php endif; ?>
<div class="flex gap-1 font-label-mono text-label-mono">
<?php
$mulai = max(1, $page - 2);
$akhir = min($totalHalaman, $page + 2);
for ($i = $mulai; $i <= $akhir; $i++):
?>
<a class="w-10 h-10 rounded-full flex items-center justify-center <?= $i === $page ? 'bg-primary text-on-primary font-bold shadow-lime-glow' : 'bg-transparent hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors' ?>" href="<?= APP_BASE ?>/berita?page=<?= $i ?>"><?= $i ?></a>
<?php endfor; ?>
</div>
<?php if ($page < $totalHalaman): ?>
<a class="w-10 h-10 rounded-full flex items-center justify-center bg-surface-container hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface transition-colors" href="<?= APP_BASE ?>/berita?page=<?= $page + 1 ?>">
<span class="material-symbols-outlined text-[20px]">chevron_right</span>
</a>
<?php endif; ?>
</div>
<?php endif; ?>
</div>
</section>
</div>
</main>
<?php require dirname(__DIR__) . '/landing/partials/footer.php'; ?>
<script src="<?= assetUrl('js/security-warning.js') ?>"></script>
</body>
</html>
