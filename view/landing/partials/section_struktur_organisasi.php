<?php
declare(strict_types=1);
$strukturRoot = $strukturRoot ?? null;
$strukturAnak = $strukturAnak ?? [];
?>
<section id="struktur" class="w-full py-section-gap px-margin-mobile lg:px-margin-desktop bg-surface-dim relative reveal">
<div class="max-w-container-max mx-auto">
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-stack-lg gap-4">
<div>
<h2 class="font-label-mono text-label-mono text-primary mb-2">STRUKTUR</h2>
<h3 class="font-headline-lg text-[28px] md:text-headline-lg text-on-surface max-w-lg">Perangkat Desa Padang Cermin.</h3>
</div>
</div>
<?php if ($strukturRoot === null): ?>
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] p-6 border border-glass-border text-center text-on-surface-variant font-body-md">Data struktur organisasi belum tersedia.</div>
<?php else: ?>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
<div class="lg:col-span-4">
<div class="bg-glass-fill backdrop-blur-md rounded-[24px] border border-glass-border p-6 md:p-8 flex flex-col items-center text-center relative overflow-hidden group hover:border-primary/40 transition-colors duration-500 hover:-translate-y-1">
<div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>
<?php if (!empty($strukturRoot['foto'])): ?>
<img class="w-28 h-28 rounded-full object-cover border-2 border-primary/40 shadow-lg relative z-10" data-skeleton alt="Foto <?= e($strukturRoot['nama']) ?>" loading="lazy" src="<?= e(uploadUrl($strukturRoot['foto'])) ?>"/>
<?php else: ?>
<div class="w-28 h-28 rounded-full bg-surface-container-highest border border-glass-border flex items-center justify-center text-primary relative z-10">
<span class="material-symbols-outlined text-[48px]">person</span>
</div>
<?php endif; ?>
<h4 class="font-headline-md text-headline-md text-on-surface mt-5 relative z-10"><?= e($strukturRoot['nama']) ?></h4>
<span class="inline-flex items-center px-4 py-1.5 rounded-full bg-primary/15 border border-primary/30 text-label-mono text-label-mono text-primary uppercase tracking-widest mt-3 relative z-10"><?= e($strukturRoot['jabatan']) ?></span>
<?php if (!empty($strukturRoot['pendidikan_terakhir'])): ?>
<p class="font-caption text-caption text-on-surface-variant mt-3 relative z-10">Pendidikan terakhir: <?= e($strukturRoot['pendidikan_terakhir']) ?></p>
<?php endif; ?>
</div>
</div>
<div class="lg:col-span-8">
<?php if ($strukturAnak === []): ?>
<div class="bg-glass-fill backdrop-blur-md rounded-[24px] border border-glass-border p-6 md:p-8 flex items-center gap-4">
<span class="material-symbols-outlined text-primary text-[32px]">groups</span>
<div>
<p class="font-body-md text-body-md text-on-surface-variant">Data perangkat desa (sekretaris, kasi, dan kaur) akan ditampilkan di sini setelah dilengkapi melalui halaman admin.</p>
</div>
</div>
<?php else: ?>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-gutter">
<?php foreach ($strukturAnak as $anak): ?>
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-5 flex items-center gap-4 hover:border-primary/40 transition-colors">
<?php if (!empty($anak['foto'])): ?>
<img class="w-12 h-12 rounded-full object-cover border border-glass-border" data-skeleton alt="Foto <?= e($anak['nama']) ?>" loading="lazy" src="<?= e(uploadUrl($anak['foto'])) ?>"/>
<?php else: ?>
<div class="w-12 h-12 rounded-full bg-surface-container-highest border border-glass-border flex items-center justify-center text-on-surface-variant shrink-0">
<span class="material-symbols-outlined text-[22px]">person</span>
</div>
<?php endif; ?>
<div class="flex flex-col min-w-0">
<span class="font-headline-md text-[15px] text-on-surface truncate"><?= e($anak['nama']) ?></span>
<span class="font-caption text-caption text-on-surface-variant truncate"><?= e($anak['jabatan']) ?></span>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</div>
<?php endif; ?>
</div>
</section>
