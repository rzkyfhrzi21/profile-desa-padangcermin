<?php
declare(strict_types=1);

$judulHalaman = 'Manajemen Berita';

$kategoriList = getBeritaKategoriList();
$statistik = getStatistikBerita();
$viewsChart = getViewsPerKategori();

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 md:mb-section-gap gap-4 md:gap-0">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Publikasi Desa</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0">Manajemen Berita &amp; Publikasi</h1>
</div>
<a class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 group whitespace-nowrap" href="<?= APP_BASE ?>/dashboard/berita/form">
<span class="material-symbols-outlined text-[20px] transition-transform group-hover:rotate-90">edit_document</span>
Tulis Berita Baru
</a>
</div>

<div class="grid grid-cols-12 gap-gutter mb-8 md:mb-section-gap relative z-10 flex-1">
<div class="col-span-12 grid grid-cols-1 md:grid-cols-3 gap-gutter mb-4">
<div class="bg-surface-container rounded-[20px] p-4 relative overflow-hidden group">
<div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
<div class="bg-background rounded-xl p-4 border border-glass-border/30 flex flex-col gap-1 relative z-10">
<span class="text-label-mono font-label-mono text-on-surface-variant">Total Berita</span>
<span class="text-headline-lg font-headline-lg text-primary"><?= $statistik['total'] ?></span>
</div>
</div>
<div class="bg-surface-container rounded-[20px] p-4 relative overflow-hidden group">
<div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
<div class="bg-background rounded-xl p-4 border border-glass-border/30 flex flex-col gap-1 relative z-10">
<span class="text-label-mono font-label-mono text-on-surface-variant">Bulan Ini</span>
<span class="text-headline-lg font-headline-lg text-on-surface">+<?= $statistik['bulan_ini'] ?></span>
</div>
</div>
<div class="bg-surface-container rounded-[20px] p-4 relative overflow-hidden group flex flex-col">
<div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
<span class="text-label-mono font-label-mono text-on-surface-variant relative z-10 mb-2">News Engagement (Views)</span>
<div class="w-full h-[60px] relative z-10" id="engagementChart"></div>
</div>
</div>

<div class="col-span-12 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg relative overflow-hidden group hover:border-primary/40 transition-colors duration-500 hover:-translate-y-[3px] flex-1 flex flex-col">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div id="berita-table" class="relative z-10 flex flex-col flex-1" data-endpoint="<?= APP_BASE ?>/dashboard/ajax/berita/list">
<div class="flex flex-col md:flex-row items-center gap-4 mb-stack-md relative z-10">
<input class="flex-1 w-full bg-surface-container-highest border border-glass-border rounded-xl py-3 pl-4 pr-4 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" name="q" data-live-search placeholder="Cari judul berita atau konten..." type="text"/>
<div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 scrollbar-hide">
<select class="bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-caption font-caption text-on-surface focus:outline-none focus:border-primary transition-all" name="kategori" data-filter>
<option value="">Semua Kategori</option>
<?php foreach ($kategoriList as $k): ?>
<option value="<?= (int) $k['id'] ?>"><?= e($k['nama']) ?></option>
<?php endforeach; ?>
</select>
<select class="bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-caption font-caption text-on-surface focus:outline-none focus:border-primary transition-all" name="status" data-filter>
<option value="">Semua Status</option>
<option value="publish">Published</option>
<option value="draft">Draft</option>
</select>
<button type="button" data-reset-filter class="shrink-0 px-4 py-3 rounded-xl border border-glass-border text-on-surface-variant hover:text-primary hover:border-primary/40 transition-colors text-caption font-caption flex items-center gap-1" title="Reset filter">
<span class="material-symbols-outlined text-[18px]">restart_alt</span> Reset
</button>
</div>
</div>

<div class="table-box relative flex-1">
<div class="overflow-x-auto relative z-10">
<div class="flex items-center justify-between py-4 px-4 text-caption font-caption text-on-surface-variant border-b border-glass-border/50">
<span data-table-info>Memuat data...</span>
</div>
<table class="w-full text-left border-collapse min-w-[600px]">
<thead>
<tr class="text-label-mono font-label-mono text-on-surface-variant border-b border-glass-border/50">
<th class="py-4 px-4 font-medium w-16">Foto</th>
<th class="py-4 px-4 font-medium w-1/2">Judul Artikel</th>
<th class="py-4 px-4 font-medium">Kategori</th>
<th class="py-4 px-4 font-medium">Tanggal</th>
<th class="py-4 px-4 font-medium text-right">Status</th>
<th class="py-4 px-4 font-medium text-right">Aksi</th>
</tr>
</thead>
<tbody data-table-body class="text-body-md font-body-md text-on-surface"></tbody>
</table>
</div>
</div>

<div data-table-foot class="flex flex-col sm:flex-row items-center justify-between mt-6 pt-4 border-t border-glass-border/30 relative z-10 gap-4 sm:gap-0"></div>
</div>
</div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    AdminUI.initAjaxTable({
        name: 'berita',
        container: '#berita-table',
        onRender: function () { MediaHelpers.initSkeleton(); },
        actions: {
            delete: function (btn) {
                AdminUI.confirmModal('Hapus Berita', btn.dataset.pesan || 'Yakin ingin menghapus data ini?', 'Hapus', function () {
                    AdminUI.ajax(btn.closest('[data-endpoint]').dataset.endpoint.replace('/list', '/delete'), { id: btn.dataset.id })
                        .then(function (res) {
                            if (res.ok) {
                                AdminUI.showToast('success', res.message);
                                AdminUI.loadTable('berita');
                            } else {
                                AdminUI.showToast('error', res.message || 'Gagal menghapus data.');
                            }
                        })
                        .catch(function () {});
                });
            }
        }
    });

    var options = {
        series: [{
            name: 'Views',
            data: <?= json_encode(array_map(static fn($v) => (int) $v['total_views'], $viewsChart)) ?>
        }],
        chart: {
            type: 'bar',
            height: 60,
            toolbar: { show: false },
            background: 'transparent',
            sparkline: { enabled: true }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
                barHeight: '60%',
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: <?= json_encode(array_map(static fn($v) => $v['nama'], $viewsChart)) ?>,
            labels: { show: false },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                show: true,
                style: { colors: '#c1cab0', fontSize: '12px', fontFamily: 'Plus Jakarta Sans' }
            }
        },
        colors: ['#c8ff80'],
        grid: { show: false },
        theme: { mode: 'dark' }
    };
    var chart = new ApexCharts(document.querySelector("#engagementChart"), options);
    chart.render();
});
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>
