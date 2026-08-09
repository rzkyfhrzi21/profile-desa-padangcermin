<?php
declare(strict_types=1);
$dusunList = $dusunList ?? [];
$totalLaki = $totalLaki ?? 0;
$totalPerempuan = $totalPerempuan ?? 0;
$totalKk = $totalKk ?? 0;
$totalJiwa = $totalJiwa ?? 0;
?>
<section id="data" class="w-full py-14 px-margin-mobile lg:px-margin-desktop bg-surface relative reveal">
<div class="max-w-container-max mx-auto">
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-stack-lg gap-4">
<div>
<span class="font-label-mono text-label-mono text-primary">TRANSPARANSI DATA</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface max-w-lg">Data Penduduk,<br/>Per Dusun.</h2>
<p class="font-body-md text-body-md text-on-surface-variant max-w-xl mt-2">
Rekapitulasi jumlah penduduk <?= e($periodeKependudukan) ?> dari 8 dusun di Desa Padang Cermin. Data diperbarui berkala oleh perangkat desa.
</p>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
<div class="lg:col-span-7 flex flex-col gap-6">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-3 md:p-5 overflow-hidden relative">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="overflow-x-auto relative z-10">
<table class="w-full text-left border-collapse min-w-[560px]">
<thead>
<tr class="text-label-mono font-label-mono text-on-surface-variant border-b border-glass-border/50 text-[12px] uppercase tracking-wider">
<th class="py-2.5 px-3 font-medium">Dusun</th>
<th class="py-2.5 px-3 font-medium text-right">Laki-laki</th>
<th class="py-2.5 px-3 font-medium text-right">Perempuan</th>
<th class="py-2.5 px-3 font-medium text-right">KK</th>
<th class="py-2.5 px-3 font-medium text-right">Jiwa</th>
</tr>
</thead>
<tbody class="text-body-md font-body-md text-on-surface">
<?php foreach ($dusunList as $d): ?>
<tr class="border-b border-glass-border/30 hover:bg-surface-container-highest/50 transition-colors">
<td class="py-2.5 px-3 font-medium text-on-surface"><?= e($d['nama_dusun']) ?></td>
<td class="py-2.5 px-3 text-right text-label-mono font-label-mono"><?= formatAngka($d['jumlah_laki']) ?></td>
<td class="py-2.5 px-3 text-right text-label-mono font-label-mono"><?= formatAngka($d['jumlah_perempuan']) ?></td>
<td class="py-2.5 px-3 text-right text-label-mono font-label-mono"><?= formatAngka($d['jumlah_kk']) ?></td>
<td class="py-2.5 px-3 text-right text-label-mono font-label-mono text-primary"><?= formatAngka($d['jumlah_jiwa']) ?></td>
</tr>
<?php endforeach; ?>
<tr class="bg-surface-container-highest/60">
<td class="py-2.5 px-3 font-bold text-on-surface">Total</td>
<td class="py-2.5 px-3 text-right text-label-mono font-label-mono font-bold"><?= formatAngka($totalLaki) ?></td>
<td class="py-2.5 px-3 text-right text-label-mono font-label-mono font-bold"><?= formatAngka($totalPerempuan) ?></td>
<td class="py-2.5 px-3 text-right text-label-mono font-label-mono font-bold"><?= formatAngka($totalKk) ?></td>
<td class="py-2.5 px-3 text-right text-label-mono font-label-mono font-bold text-primary"><?= formatAngka($totalJiwa) ?></td>
</tr>
</tbody>
</table>
</div>
</div>
<p class="font-caption text-caption text-on-surface-variant"><?= e($penduduk['keterangan'] ?? '') ?></p>
</div>
<div class="lg:col-span-5 flex flex-col gap-6">
<div class="bg-glass-fill backdrop-blur-md rounded-[24px] border border-glass-border p-5 md:p-6 shadow-2xl relative w-full overflow-hidden">
<div class="absolute -top-4 -right-4 w-24 h-24 bg-primary/20 rounded-full blur-2xl"></div>
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-5 gap-2">
<h4 class="font-headline-md text-headline-md text-on-surface">Komposisi Penduduk</h4>
<span class="bg-glass-fill backdrop-blur-md text-on-surface-variant font-label-mono text-[12px] px-3 py-1 rounded-full border border-glass-border w-max"><?= e($periodeKependudukan) ?></span>
</div>
<div class="relative w-full max-w-[220px] mx-auto">
<canvas id="chart-penduduk" role="img" aria-label="Diagram komposisi penduduk laki-laki dan perempuan"></canvas>
<div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
<span class="font-headline-lg text-headline-lg text-on-surface"><?= formatAngka($totalJiwa) ?></span>
<span class="font-caption text-caption text-on-surface-variant">Total Jiwa</span>
</div>
</div>
<div class="flex items-center justify-center gap-6 mt-5">
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-full bg-[#9EE638]"></span>
<span class="font-caption text-caption text-on-surface-variant">Laki-laki <?= formatAngka($totalLaki) ?></span>
</div>
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-full bg-[#8CBEFF]"></span>
<span class="font-caption text-caption text-on-surface-variant">Perempuan <?= formatAngka($totalPerempuan) ?></span>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
