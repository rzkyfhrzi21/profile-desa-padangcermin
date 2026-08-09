<?php
declare(strict_types=1);
$beritaList = $beritaList ?? [];
?>
<section id="berita" class="w-full py-section-gap px-margin-mobile lg:px-margin-desktop bg-surface-dim relative reveal">
<div class="max-w-container-max mx-auto">
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-stack-lg gap-4">
<div>
<h2 class="font-label-mono text-label-mono text-primary mb-2">WARTA DESA</h2>
<h3 class="font-headline-lg text-[28px] md:text-headline-lg text-on-surface max-w-lg">Kabar &amp; Kegiatan Terbaru.</h3>
</div>
<a class="font-body-md text-primary flex items-center gap-2 hover:underline" href="<?= APP_BASE ?>/berita">
Semua Berita <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
</div>
<?php if ($beritaList === []): ?>
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] p-6 border border-glass-border text-center text-on-surface-variant font-body-md">Belum ada berita yang dipublikasikan.</div>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
<?php foreach ($beritaList as $b): ?>
<article class="group relative bg-glass-fill backdrop-blur-md border border-glass-border rounded-[20px] overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:border-primary/40">
<a href="<?= APP_BASE ?>/berita/<?= e($b['slug']) ?>" class="relative w-full aspect-[16/9] overflow-hidden block">
<div class="absolute inset-0 bg-gradient-to-t from-surface to-transparent z-10 opacity-60"></div>
<?php if (!empty($b['gambar_utama'])): ?>
<img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="<?= e($b['alt_gambar'] !== '' ? $b['alt_gambar'] : $b['judul']) ?>" loading="lazy" src="<?= e(uploadUrl($b['gambar_utama'])) ?>"/>
<?php else: ?>
<div class="w-full h-full bg-surface-container-high flex items-center justify-center">
<span class="material-symbols-outlined text-on-surface-variant text-[48px]">article</span>
</div>
<?php endif; ?>
</a>
<div class="p-stack-lg flex flex-col flex-grow relative z-20 bg-gradient-to-b from-surface/50 to-surface/90">
<div class="flex items-center gap-3 mb-3">
<span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-surface-container-high text-primary font-label-mono text-label-mono text-[11px] uppercase tracking-widest border border-glass-border"><?= e($b['kategori_nama'] ?? 'Berita') ?></span>
<span class="text-on-surface-variant font-caption text-caption flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">calendar_today</span> <?= e(formatTanggal($b['published_at'])) ?>
</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-2 group-hover:text-primary transition-colors leading-snug"><a href="<?= APP_BASE ?>/berita/<?= e($b['slug']) ?>"><?= e($b['judul']) ?></a></h3>
<p class="font-body-md text-body-md text-on-surface-variant line-clamp-2 mb-6"><?= e(truncate($b['konten'], 160)) ?></p>
<a href="<?= APP_BASE ?>/berita/<?= e($b['slug']) ?>" class="mt-auto inline-flex items-center gap-2 text-primary font-body-md font-bold hover:text-primary-fixed transition-colors">
Baca Selengkapnya <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
</a>
</div>
</article>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</section>
