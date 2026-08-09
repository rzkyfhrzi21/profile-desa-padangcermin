<?php
declare(strict_types=1);
$potensiList = $potensiList ?? [];
$iconPotensi = static function (string $kategori): string {
    return match (strtolower($kategori)) {
        'pertanian' => 'psychiatry',
        'pariwisata' => 'tour',
        'infrastruktur' => 'alt_route',
        'umkm' => 'storefront',
        default => 'eco',
    };
};
?>
<section id="potensi" class="w-full pt-16 md:pt-32 pb-section-gap px-margin-mobile lg:px-margin-desktop bg-surface-dim relative">
<div class="max-w-container-max mx-auto">
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-stack-lg gap-4 reveal">
<div>
<h2 class="font-label-mono text-label-mono text-primary mb-2">POTENSI DESA</h2>
<h3 class="font-headline-lg text-[28px] md:text-headline-lg text-on-surface max-w-lg">Sumber Daya &amp; Komoditas Unggulan.</h3>
</div>
<a class="font-body-md text-primary flex items-center gap-2 hover:underline" href="<?= APP_BASE ?>/wisata">
Jelajahi Wisata <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
</div>
<?php if ($potensiList === []): ?>
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] p-6 border border-glass-border text-center text-on-surface-variant font-body-md">Belum ada data potensi desa.</div>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
<?php foreach ($potensiList as $i => $p): ?>
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] p-6 hover:bg-glass-fill/80 transition-colors group cursor-pointer border border-glass-border hover:border-primary/50 reveal delay-<?= ($i % 4) * 100 ?>">
<div class="w-12 h-12 rounded-lg bg-[#0E2818] flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined"><?= e($iconPotensi((string) $p['kategori'])) ?></span>
</div>
<h4 class="font-headline-md text-headline-md text-on-surface mb-2"><?= e($p['judul']) ?></h4>
<p class="font-body-md text-body-md text-on-surface-variant line-clamp-4"><?= e($p['deskripsi']) ?></p>
<?php if (!empty($p['kategori'])): ?>
<span class="inline-flex items-center mt-4 px-3 py-1 rounded-full bg-surface-container-highest border border-glass-border/50 text-label-mono text-[11px] uppercase tracking-widest text-primary"><?= e($p['kategori']) ?></span>
<?php endif; ?>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</section>
