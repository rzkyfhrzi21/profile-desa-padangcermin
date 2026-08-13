<?php
declare(strict_types=1);

$judulHalaman = 'Manajemen Kependudukan';

$terbaru    = getDataKependudukanTerbaru();
$dusunList  = getKependudukanDusun();   // periode terbaru otomatis dari function
$tren       = getTrenKependudukan();

// Periode dusun: ambil dari dusunList jika ada, fallback dari $terbaru
$periodeLabel = '';
if ($dusunList !== []) {
    $periodeLabel = $dusunList[0]['periode'];
} elseif ($terbaru !== null) {
    $periodeLabel = $terbaru['periode'];
}

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 md:mb-section-gap gap-4 md:gap-0">
<div class="flex flex-col gap-2">
<div class="flex items-center gap-2 mb-2">
<span class="w-2 h-2 rounded-full bg-primary shadow-lime-glow animate-pulse"></span>
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Data Demografi</span>
</div>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-coklat m-0">Statistik &amp; Data Kependudukan</h1>
<p class="text-body-md font-body-md text-abu m-0">Manajemen agregat data demografi Desa Padang Cermin. Perbarui dan pantau fluktuasi jumlah penduduk per periode.</p>
</div>
<a class="group relative bg-primary text-on-primary font-label-mono text-label-mono font-bold px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 whitespace-nowrap" href="<?= APP_BASE ?>/dashboard/kependudukan/form">
<span class="material-symbols-outlined text-[20px] transition-transform group-hover:rotate-90">add</span>
Tambah Data Periode
</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
<div class="md:col-span-12 flex flex-col gap-gutter">

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-gutter">
<div class="group relative bg-white rounded-[20px] border border-black/8 hover:border-hijau/30 transition-colors shadow-sm overflow-hidden">
<div class="absolute -right-12 -top-12 w-48 h-48 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-colors"></div>
<div class="relative z-10 p-stack-lg">
<div class="flex items-center justify-between mb-8">
<span class="font-body-md text-body-md text-abu">Jumlah Kepala Keluarga (KK)</span>
<div class="w-10 h-10 rounded-xl bg-admin-bg border border-black/8 flex items-center justify-center">
<span class="material-symbols-outlined text-hijau">family_home</span>
</div>
</div>
<div class="flex items-end gap-4">
<span class="font-label-mono text-[48px] leading-none text-coklat"><?= $terbaru !== null ? formatAngka($terbaru['jumlah_kk']) : '-' ?></span>
<span class="font-caption text-caption text-primary flex items-center mb-1">
<span class="material-symbols-outlined text-[16px]">home</span> <?= count($dusunList) ?> dusun
</span>
</div>
<div class="mt-6">
<div class="h-1.5 w-full bg-admin-bg rounded-full overflow-hidden">
<div class="h-full bg-primary rounded-full shadow-lime-glow" style="width: <?= $terbaru !== null && (int) $terbaru['jumlah_jiwa'] > 0 ? min(100, (int) round((int) $terbaru['jumlah_kk'] / (int) $terbaru['jumlah_jiwa'] * 100)) : 0 ?>%"></div>
</div>
</div>
</div>
</div>
<div class="group relative bg-white rounded-[20px] border border-black/8 hover:border-hijau/30 transition-colors shadow-sm overflow-hidden">
<div class="absolute -left-12 -bottom-12 w-48 h-48 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-colors"></div>
<div class="relative z-10 p-stack-lg">
<div class="flex items-center justify-between mb-8">
<span class="font-body-md text-body-md text-abu">Total Jiwa</span>
<div class="w-10 h-10 rounded-xl bg-admin-bg border border-black/8 flex items-center justify-center">
<span class="material-symbols-outlined text-hijau">groups</span>
</div>
</div>
<div class="flex items-end gap-4">
<span class="font-label-mono text-[48px] leading-none text-coklat"><?= $terbaru !== null ? formatAngka($terbaru['jumlah_jiwa']) : '-' ?></span>
<span class="font-caption text-caption text-abu mb-1"><?= $terbaru !== null ? 'periode ' . e($terbaru['periode']) : '' ?></span>
</div>
<div class="mt-6 flex justify-between font-caption text-caption text-abu">
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-primary"></span> Laki-laki: <span class="font-label-mono text-coklat"><?= $terbaru !== null ? formatAngka($terbaru['jumlah_laki']) : '-' ?></span>
</div>
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-secondary"></span> Perempuan: <span class="font-label-mono text-coklat"><?= $terbaru !== null ? formatAngka($terbaru['jumlah_perempuan']) : '-' ?></span>
</div>
</div>
</div>
</div>
</div>

<!-- Tren Pertumbuhan Chart -->
<div class="bg-white rounded-[20px] border border-black/8 p-stack-lg shadow-sm">
<div class="flex items-center justify-between mb-6">
<div class="flex flex-col">
<h2 class="font-headline-md text-headline-md text-coklat">Tren Pertumbuhan Penduduk</h2>
<p class="font-body-md text-body-md text-abu">Jumlah jiwa &amp; kepala keluarga per periode</p>
</div>
<span class="font-label-mono text-caption text-primary bg-primary/10 px-3 py-1.5 rounded-full"><?= count($tren['periode']) ?> periode</span>
</div>
<?php if ($tren['periode'] === []): ?>
<div class="flex flex-col items-center justify-center py-16 gap-3 text-center">
<span class="material-symbols-outlined text-[48px] text-abu/40">show_chart</span>
<p class="text-body-md font-body-md text-abu">Belum ada data tren. Tambah minimal 2 periode.</p>
</div>
<?php else: ?>
<div class="w-full" id="trendChart" style="min-height:256px;"></div>
<?php endif; ?>
</div>

<!-- Tabel Riwayat Periode -->
<div class="bg-white rounded-[20px] border border-black/8 p-4 md:p-stack-lg shadow-sm overflow-hidden">
<div id="penduduk-table" class="relative z-10" data-endpoint="<?= APP_BASE ?>/dashboard/ajax/kependudukan/list">
<form id="penduduk-filter" class="flex flex-col md:flex-row items-stretch md:items-center gap-3 mb-4 relative z-10" onsubmit="return false;">
<input class="flex-1 min-w-0 bg-admin-bg border border-black/10 rounded-xl py-3 pl-4 pr-4 text-body-md font-body-md text-coklat focus:outline-none focus:border-hijau transition-all placeholder:text-abu/60" name="q" data-live-search placeholder="Cari periode atau keterangan..." type="text"/>
<button type="button" data-reset-filter class="shrink-0 px-4 py-3 rounded-xl border border-black/10 text-abu hover:text-hijau hover:border-hijau/40 transition-colors text-caption font-caption flex items-center gap-1" title="Reset filter">
<span class="material-symbols-outlined text-[18px]">restart_alt</span> Reset
</button>
</form>
<div class="table-box relative">
<div class="overflow-x-auto">
<div class="flex items-center justify-between py-4 px-4 text-caption font-caption text-abu border-b border-black/8">
<span data-table-info>Memuat data...</span>
</div>
<table class="w-full text-left border-collapse min-w-[680px]">
<thead>
<tr class="text-label-mono font-label-mono text-abu border-b border-black/8">
<th class="py-4 px-4 font-medium">Periode</th>
<th class="py-4 px-4 font-medium text-right">Jumlah KK</th>
<th class="py-4 px-4 font-medium text-right">Jumlah Jiwa</th>
<th class="py-4 px-4 font-medium text-right">Laki-laki</th>
<th class="py-4 px-4 font-medium text-right">Perempuan</th>
<th class="py-4 px-4 font-medium">Keterangan</th>
<th class="py-4 px-4 font-medium">Diperbarui</th>
<th class="py-4 px-4 font-medium text-right">Aksi</th>
</tr>
</thead>
<tbody data-table-body class="text-body-md font-body-md text-coklat"></tbody>
</table>
</div>
<div data-table-foot class="flex flex-col sm:flex-row items-center justify-between mt-6 pt-4 border-t border-black/8 gap-4 sm:gap-0"></div>
</div>
</div>
</div>

<!-- Rekap per Dusun — periode terbaru dari DB -->
<?php if ($dusunList !== []): ?>
<div class="bg-white rounded-[20px] border border-black/8 p-4 md:p-stack-lg shadow-sm overflow-hidden">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-hijau">holiday_village</span>
<h2 class="text-headline-md font-headline-md text-coklat m-0">Rekap per Dusun
<?php if ($periodeLabel !== ''): ?>
<span class="text-label-mono font-label-mono text-primary text-sm ml-2">(<?= e($periodeLabel) ?>)</span>
<?php endif; ?>
</h2>
</div>
<a href="<?= APP_BASE ?>/dashboard/kependudukan/form<?= $terbaru !== null ? '?id=' . (int) $terbaru['id'] : '' ?>" class="text-caption font-caption text-primary hover:opacity-75 flex items-center gap-1 transition-opacity">
<span class="material-symbols-outlined text-[16px]">edit</span>Kelola Data Dusun
</a>
</div>
<div class="overflow-x-auto relative z-10">
<table class="w-full text-left border-collapse min-w-[640px]">
<thead>
<tr class="text-label-mono font-label-mono text-abu border-b border-black/8">
<th class="py-3 px-4 font-medium">Dusun</th>
<th class="py-3 px-4 font-medium text-right">Laki-laki</th>
<th class="py-3 px-4 font-medium text-right">Perempuan</th>
<th class="py-3 px-4 font-medium text-right">KK</th>
<th class="py-3 px-4 font-medium text-right">Jiwa</th>
</tr>
</thead>
<tbody class="text-body-md font-body-md text-coklat">
<?php $sumDusun = ['laki' => 0, 'perempuan' => 0, 'kk' => 0, 'jiwa' => 0]; ?>
<?php foreach ($dusunList as $d): ?>
<?php $sumDusun['laki'] += (int) $d['jumlah_laki']; $sumDusun['perempuan'] += (int) $d['jumlah_perempuan']; $sumDusun['kk'] += (int) $d['jumlah_kk']; $sumDusun['jiwa'] += (int) $d['jumlah_jiwa']; ?>
<tr class="border-b border-black/6 hover:bg-admin-bg/70 transition-colors">
<td class="py-3 px-4 font-medium"><?= e($d['nama_dusun']) ?></td>
<td class="py-3 px-4 text-right text-label-mono font-label-mono"><?= formatAngka($d['jumlah_laki']) ?></td>
<td class="py-3 px-4 text-right text-label-mono font-label-mono"><?= formatAngka($d['jumlah_perempuan']) ?></td>
<td class="py-3 px-4 text-right text-label-mono font-label-mono"><?= formatAngka($d['jumlah_kk']) ?></td>
<td class="py-3 px-4 text-right text-label-mono font-label-mono text-primary"><?= formatAngka($d['jumlah_jiwa']) ?></td>
</tr>
<?php endforeach; ?>
<tr class="bg-admin-bg font-bold border-t border-black/10">
<td class="py-3 px-4 text-coklat">Total</td>
<td class="py-3 px-4 text-right text-label-mono font-label-mono"><?= formatAngka($sumDusun['laki']) ?></td>
<td class="py-3 px-4 text-right text-label-mono font-label-mono"><?= formatAngka($sumDusun['perempuan']) ?></td>
<td class="py-3 px-4 text-right text-label-mono font-label-mono"><?= formatAngka($sumDusun['kk']) ?></td>
<td class="py-3 px-4 text-right text-label-mono font-label-mono text-primary"><?= formatAngka($sumDusun['jiwa']) ?></td>
</tr>
</tbody>
</table>
</div>
</div>
<?php endif; ?>

</div>
</div>
</section>
<script>
/* Tabel AJAX diinisiasi di DOMContentLoaded */
document.addEventListener('DOMContentLoaded', function () {
    AdminUI.initAjaxTable({
        name: 'penduduk',
        container: '#penduduk-table',
        actions: {
            delete: function (btn) {
                AdminUI.confirmModal('Hapus Data Periode', btn.dataset.pesan || 'Yakin ingin menghapus data ini?', 'Hapus', function () {
                    AdminUI.ajax(btn.closest('[data-endpoint]').dataset.endpoint.replace('/list', '/delete'), { id: btn.dataset.id })
                        .then(function (res) {
                            if (res.ok) {
                                AdminUI.showToast('success', res.message);
                                AdminUI.loadTable('penduduk');
                            } else {
                                AdminUI.showToast('error', res.message || 'Gagal menghapus data.');
                            }
                        })
                        .catch(function () {});
                });
            }
        }
    });
});

/* ApexCharts diinisiasi setelah window load agar CDN pasti tersedia */
<?php if ($tren['periode'] !== []): ?>
window.addEventListener('load', function () {
    try {
        if (typeof ApexCharts === 'undefined') return;
        var chartEl = document.querySelector('#trendChart');
        if (!chartEl) return;
        new ApexCharts(chartEl, {
            series: [
                { name: 'Jumlah Jiwa', data: <?= json_encode(array_map('intval', $tren['jumlah_jiwa'])) ?> },
                { name: 'KK', data: <?= json_encode(array_map('intval', $tren['jumlah_kk'])) ?> }
            ],
            chart: {
                type: 'area',
                height: 260,
                toolbar: { show: false },
                background: 'transparent',
                animations: { enabled: true, easing: 'easeinout', speed: 600 }
            },
            stroke: { curve: 'smooth', width: [2, 2] },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.2, opacityTo: 0.02, stops: [0, 90, 100] }
            },
            xaxis: {
                categories: <?= json_encode($tren['periode']) ?>,
                labels: { style: { colors: '#6B655C', fontSize: '11px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { labels: { style: { colors: '#6B655C', fontSize: '11px' } } },
            colors: ['#3C6E47', '#E7A825'],
            grid: { borderColor: 'rgba(0,0,0,0.06)', strokeDashArray: 4 },
            legend: { labels: { colors: '#33291E' }, position: 'top' },
            tooltip: { theme: 'light' },
            theme: { mode: 'light' },
            markers: { size: 4, colors: ['#3C6E47', '#E7A825'], strokeWidth: 0 }
        }).render();
    } catch (err) {
        console.warn('Kependudukan chart error:', err);
    }
});
<?php endif; ?>
</script>

<?php require __DIR__ . '/../layout_close.php'; ?>
