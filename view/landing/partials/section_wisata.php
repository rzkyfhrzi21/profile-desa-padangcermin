<?php
declare(strict_types=1);
$wisataList = $wisataList ?? [];
?>
<section id="wisata" class="w-full py-section-gap px-margin-mobile lg:px-margin-desktop bg-surface relative reveal">
<div class="max-w-container-max mx-auto">
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-stack-lg gap-4">
<div>
<h2 class="font-label-mono text-label-mono text-primary mb-2">WISATA ALAM</h2>
<h3 class="font-headline-lg text-[28px] md:text-headline-lg text-on-surface max-w-lg">Air Terjun di Kampung Penyarian.</h3>
</div>
<a class="font-body-md text-primary flex items-center gap-2 hover:underline" href="<?= APP_BASE ?>/wisata">
Semua Wisata <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
</div>
<?php if ($wisataList === []): ?>
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] p-6 border border-glass-border text-center text-on-surface-variant font-body-md">Belum ada destinasi wisata yang dipublikasikan.</div>
<?php else: ?>
<?php $totalWisata = count($wisataList); ?>
<div class="relative mx-auto max-w-3xl" <?= $totalWisata > 1 ? 'data-carousel data-carousel-interval="1500"' : '' ?>>
<div class="overflow-hidden rounded-[20px]">
<?php foreach ($wisataList as $w): $gambarUtama = $w['gambar'][0] ?? null; ?>
<div class="carousel-slide">
<article class="group relative bg-glass-fill backdrop-blur-md border border-glass-border rounded-[20px] overflow-hidden flex flex-col transition-all duration-300 hover:border-primary/40">
<a href="<?= APP_BASE ?>/wisata/<?= e($w['slug']) ?>" class="relative w-full aspect-video overflow-hidden block">
<div class="absolute inset-0 bg-gradient-to-t from-surface to-transparent z-10 opacity-60"></div>
<?php if ($gambarUtama !== null): ?>
<img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="<?= e($gambarUtama['alt_text'] !== '' ? $gambarUtama['alt_text'] : $w['nama']) ?>" loading="lazy" src="<?= e(uploadUrl($gambarUtama['path_gambar'])) ?>"/>
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
<div class="flex items-center gap-1 text-on-surface-variant font-label-mono text-label-mono text-[12px]">
<span class="material-symbols-outlined text-[16px]">location_on</span>
<span class="truncate"><?= e($w['alamat']) ?></span>
</div>
<a href="<?= APP_BASE ?>/wisata/<?= e($w['slug']) ?>" class="inline-flex items-center gap-2 text-primary font-body-md font-bold hover:text-primary-fixed transition-colors">
Lihat Detail <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
</a>
</div>
</div>
</article>
</div>
<?php endforeach; ?>
</div>
<?php if ($totalWisata > 1): ?>
<div class="flex items-center justify-center gap-4 mt-6">
<button type="button" data-carousel-prev class="carousel-btn w-11 h-11 rounded-full border border-glass-border bg-glass-fill backdrop-blur-md text-on-surface-variant flex items-center justify-center" aria-label="Sebelumnya">
<span class="material-symbols-outlined text-[20px]">chevron_left</span>
</button>
<div class="carousel-dots flex items-center gap-2">
<?php foreach ($wisataList as $i => $w): ?>
<button type="button" data-carousel-dot class="carousel-dot" aria-label="Slide <?= $i + 1 ?>"></button>
<?php endforeach; ?>
</div>
<button type="button" data-carousel-next class="carousel-btn w-11 h-11 rounded-full border border-glass-border bg-glass-fill backdrop-blur-md text-on-surface-variant flex items-center justify-center" aria-label="Berikutnya">
<span class="material-symbols-outlined text-[20px]">chevron_right</span>
</button>
</div>
<?php endif; ?>
</div>
<?php endif; ?>
</div>
</section>
