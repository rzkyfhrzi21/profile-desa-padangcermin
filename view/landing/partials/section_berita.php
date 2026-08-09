<?php
declare(strict_types=1);
$beritaList = $beritaList ?? [];
?>
<section id="berita" class="w-full py-14 px-margin-mobile lg:px-margin-desktop bg-surface-dim relative reveal">
<div class="max-w-container-max mx-auto">
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-stack-lg gap-4">
<div>
<h2 class="font-label-mono text-label-mono text-primary mb-2">BERITA DESA</h2>
<h3 class="font-headline-lg text-[24px] md:text-[26px] text-on-surface max-w-lg">Kabar &amp; Kegiatan Terbaru.</h3>
</div>
<a class="font-body-md text-primary flex items-center gap-2 hover:underline" href="<?= APP_BASE ?>/berita">
Semua Berita <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
</div>
<?php if ($beritaList === []): ?>
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] p-6 border border-glass-border text-center text-on-surface-variant font-body-md">Belum ada berita yang dipublikasikan.</div>
<?php else: ?>
<div class="flex flex-wrap justify-center gap-gutter">
<?php foreach ($beritaList as $b): ?>
<article class="group relative w-full sm:w-[42%] lg:w-[26%] bg-glass-fill backdrop-blur-md border border-glass-border rounded-[16px] overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:border-primary/40">
<a href="<?= APP_BASE ?>/berita/<?= e($b['slug']) ?>" class="relative w-full aspect-[16/10] overflow-hidden block">
<div class="absolute inset-0 bg-gradient-to-t from-surface to-transparent z-10 opacity-60"></div>
<?php if (!empty($b['gambar_utama'])): ?>
<img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="<?= e($b['judul']) ?>" loading="lazy" src="<?= e(uploadUrl($b['gambar_utama'])) ?>"/>
<?php else: ?>
<div class="w-full h-full bg-surface-container-high flex items-center justify-center">
<span class="material-symbols-outlined text-on-surface-variant text-[40px]">article</span>
</div>
<?php endif; ?>
<span class="absolute bottom-2.5 left-2.5 z-20 inline-flex items-center gap-1 bg-black/60 backdrop-blur-md text-white text-[11px] px-2 py-0.5 rounded-full">
<span class="material-symbols-outlined text-[12px]">calendar_today</span> <?= e(formatTanggal($b['published_at'])) ?>
</span>
</a>
<div class="p-3 flex flex-col flex-grow relative z-20 bg-gradient-to-b from-surface/50 to-surface/90">
<div class="flex items-center gap-2 mb-1.5 flex-wrap">
<span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-surface-container-high text-primary font-label-mono text-label-mono text-[9px] uppercase tracking-widest border border-glass-border"><?= e($b['kategori_nama'] ?? 'Berita') ?></span>
</div>
<h3 class="font-headline-md text-[12px] text-on-surface mb-1 group-hover:text-primary transition-colors leading-snug line-clamp-2"><a href="<?= APP_BASE ?>/berita/<?= e($b['slug']) ?>"><?= e($b['judul']) ?></a></h3>
<p class="font-body-md text-body-md text-on-surface-variant line-clamp-2 text-[11px] mb-2.5"><?= e(truncate($b['konten'], 120)) ?></p>
<a href="<?= APP_BASE ?>/berita/<?= e($b['slug']) ?>" class="mt-auto inline-flex items-center gap-1.5 text-primary font-body-md text-[11px] font-bold hover:text-primary-fixed transition-colors">
Baca Selengkapnya <span class="material-symbols-outlined text-[14px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
</a>
</div>
</article>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</section>
