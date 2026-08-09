<?php
declare(strict_types=1);

$judulHalaman = 'Overview Pekon';
$activeMenu = 'dashboard';
$totalBerita = countBerita();
$totalWisata = count(getWisataList());
$tren = getTrenKependudukan();
$logTerbaru = getLogTerbaru(8);
$wisataSorotan = getWisataWithGambar(getWisataList(true, 5));
$jiwa = $tren['jumlah_jiwa'] !== [] ? (int) end($tren['jumlah_jiwa']) : 0;

require __DIR__ . '/layout.php';
?>

<!-- ===== HEADER ===== -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-on-surface leading-tight">Ringkasan Pekon</h1>
    <p class="text-sm text-on-surface-variant mt-1">Pengelolaan data digital Desa Padang Cermin</p>
</div>

<!-- ===== STAT CARDS — tiap card berbeda karakter ===== -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    <!-- Berita -->
    <div class="bg-surface-container rounded-2xl border border-glass-border p-4 flex flex-col gap-3 hover:border-primary/30 transition-colors">
        <div class="flex items-center justify-between">
            <span class="text-xs text-on-surface-variant font-medium">Berita</span>
            <a href="<?= APP_BASE ?>/dashboard/berita" class="text-primary hover:opacity-70 transition-opacity" title="Kelola berita">
                <span class="material-symbols-outlined text-[18px]">open_in_new</span>
            </a>
        </div>
        <div class="flex items-end gap-2">
            <span class="text-3xl font-bold font-mono text-on-surface leading-none"><?= formatAngka($totalBerita) ?></span>
            <span class="text-xs text-on-surface-variant mb-0.5">artikel</span>
        </div>
        <span class="material-symbols-outlined text-on-surface-variant/30 text-[40px] self-end -mt-2" style="font-variation-settings: 'FILL' 1">newspaper</span>
    </div>

    <!-- Wisata -->
    <div class="bg-surface-container rounded-2xl border border-glass-border p-4 flex flex-col gap-3 hover:border-primary/30 transition-colors">
        <div class="flex items-center justify-between">
            <span class="text-xs text-on-surface-variant font-medium">Wisata</span>
            <a href="<?= APP_BASE ?>/dashboard/wisata" class="text-primary hover:opacity-70 transition-opacity" title="Kelola wisata">
                <span class="material-symbols-outlined text-[18px]">open_in_new</span>
            </a>
        </div>
        <div class="flex items-end gap-2">
            <span class="text-3xl font-bold font-mono text-primary leading-none"><?= formatAngka($totalWisata) ?></span>
            <span class="text-xs text-on-surface-variant mb-0.5">destinasi</span>
        </div>
        <span class="material-symbols-outlined text-primary/20 text-[40px] self-end -mt-2" style="font-variation-settings: 'FILL' 1">landscape</span>
    </div>

    <!-- Kependudukan -->
    <div class="bg-surface-container rounded-2xl border border-glass-border p-4 flex flex-col gap-3 hover:border-primary/30 transition-colors">
        <div class="flex items-center justify-between">
            <span class="text-xs text-on-surface-variant font-medium">Penduduk</span>
            <a href="<?= APP_BASE ?>/dashboard/kependudukan" class="text-primary hover:opacity-70 transition-opacity" title="Data kependudukan">
                <span class="material-symbols-outlined text-[18px]">open_in_new</span>
            </a>
        </div>
        <div class="flex items-end gap-2">
            <span class="text-3xl font-bold font-mono text-on-surface leading-none"><?= $jiwa > 0 ? formatAngka($jiwa) : '—' ?></span>
            <span class="text-xs text-on-surface-variant mb-0.5">jiwa</span>
        </div>
        <span class="material-symbols-outlined text-on-surface-variant/30 text-[40px] self-end -mt-2" style="font-variation-settings: 'FILL' 1">group</span>
    </div>

    <!-- Status -->
    <div class="bg-primary/5 rounded-2xl border border-primary/20 p-4 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <span class="text-xs text-primary/70 font-medium">Status Sistem</span>
            <span class="w-2 h-2 rounded-full bg-primary"></span>
        </div>
        <div class="flex items-end gap-2">
            <span class="text-2xl font-bold text-primary leading-none">Aktif</span>
        </div>
        <span class="text-xs text-primary/60 mt-auto">Semua layanan berjalan normal</span>
    </div>
</div>

<!-- ===== SOROTAN WISATA ===== -->
<?php if ($wisataSorotan !== []): ?>
<section class="mb-8">
    <div class="bg-surface-container rounded-2xl border border-glass-border overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-glass-border/50">
            <div>
                <h2 class="text-base font-semibold text-on-surface">Sorotan Wisata</h2>
                <p class="text-xs text-on-surface-variant mt-0.5">Destinasi terbaru yang dipublikasikan</p>
            </div>
            <a href="<?= APP_BASE ?>/wisata" target="_blank"
               class="text-xs text-primary hover:opacity-70 transition-opacity flex items-center gap-1">
                Lihat semua <span class="material-symbols-outlined text-[14px]">arrow_outward</span>
            </a>
        </div>

        <div class="p-4">
        <?php if (count($wisataSorotan) > 1): ?>
        <div class="relative" data-carousel data-carousel-interval="1500">
        <?php endif; ?>

            <div class="overflow-hidden rounded-xl">
                <?php foreach ($wisataSorotan as $w):
                    $gambarUtama = $w['gambar'][0] ?? null; ?>
                <div class="carousel-slide">
                    <a href="<?= APP_BASE ?>/wisata/<?= e($w['slug']) ?>"
                       class="group relative flex w-full aspect-[21/9] overflow-hidden rounded-xl block">
                        <div class="absolute inset-0 bg-gradient-to-t from-surface/90 via-surface/20 to-transparent z-10"></div>
                        <?php if ($gambarUtama !== null): ?>
                        <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                             data-skeleton
                             alt="<?= e($gambarUtama['alt_text'] !== '' ? $gambarUtama['alt_text'] : $w['nama']) ?>"
                             loading="lazy"
                             src="<?= e(uploadUrl($gambarUtama['path_gambar'])) ?>"/>
                        <?php else: ?>
                        <div class="w-full h-full bg-surface-container-high flex items-center justify-center">
                            <span class="material-symbols-outlined text-on-surface-variant text-[48px]">landscape</span>
                        </div>
                        <?php endif; ?>
                        <div class="absolute bottom-0 left-0 right-0 z-20 p-5 flex items-end justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <h3 class="text-base font-semibold text-on-surface group-hover:text-primary transition-colors"><?= e($w['nama']) ?></h3>
                                <p class="text-xs text-on-surface-variant line-clamp-1"><?= e(truncate($w['deskripsi'], 90)) ?></p>
                            </div>
                            <span class="shrink-0 text-primary text-sm font-medium flex items-center gap-1">
                                Detail <span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                            </span>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($wisataSorotan) > 1): ?>
            <div class="flex items-center justify-center gap-3 mt-4">
                <button type="button" data-carousel-prev
                    class="carousel-btn w-9 h-9 rounded-full border border-glass-border bg-surface-container-high text-on-surface-variant flex items-center justify-center"
                    aria-label="Sebelumnya">
                    <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                </button>
                <div class="carousel-dots flex items-center gap-1.5">
                    <?php foreach ($wisataSorotan as $i => $w): ?>
                    <button type="button" data-carousel-dot class="carousel-dot" aria-label="Slide <?= $i + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
                <button type="button" data-carousel-next
                    class="carousel-btn w-9 h-9 rounded-full border border-glass-border bg-surface-container-high text-on-surface-variant flex items-center justify-center"
                    aria-label="Berikutnya">
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </button>
            </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== GRID: CHART + LOG ===== -->
<div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

    <!-- Chart -->
    <section class="lg:col-span-3">
        <div class="bg-surface-container rounded-2xl border border-glass-border flex flex-col" style="min-height:380px">
            <div class="px-6 py-4 border-b border-glass-border/50 shrink-0">
                <h2 class="text-base font-semibold text-on-surface">Tren Kependudukan</h2>
                <p class="text-xs text-on-surface-variant mt-0.5">Jumlah jiwa &amp; KK per periode</p>
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <div class="w-full flex-1 min-h-[240px]" id="chart"></div>
            </div>
        </div>
    </section>

    <!-- Log Aktivitas -->
    <section class="lg:col-span-2">
        <div class="bg-surface-container rounded-2xl border border-glass-border h-full flex flex-col">
            <div class="px-6 py-4 border-b border-glass-border/50 shrink-0">
                <h2 class="text-base font-semibold text-on-surface">Aktivitas Terakhir</h2>
            </div>
            <div class="divide-y divide-glass-border/40 flex-1 overflow-y-auto">
                <?php foreach ($logTerbaru as $log): ?>
                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-surface-container-high/50 transition-colors">
                    <div class="w-7 h-7 rounded-lg bg-surface-container-high border border-glass-border flex items-center justify-center shrink-0 mt-0.5">
                        <span class="material-symbols-outlined text-on-surface-variant text-[14px]">history</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-on-surface leading-snug truncate">
                            <span class="font-medium text-primary"><?= e($log['admin_nama'] ?? 'Admin') ?></span>
                            <?= e($log['aksi']) ?> — <?= e($log['tabel_terkait']) ?>
                        </p>
                        <p class="text-[11px] text-on-surface-variant mt-0.5"><?= e($log['waktu']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if ($logTerbaru === []): ?>
                <div class="px-5 py-6 text-sm text-on-surface-variant text-center">Belum ada aktivitas.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/layout_close.php'; ?>
<script>
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var trenData = {
            jiwa: <?= json_encode(array_map('intval', $tren['jumlah_jiwa'])) ?>,
            kk:   <?= json_encode(array_map('intval', $tren['jumlah_kk'])) ?>,
            periode: <?= json_encode($tren['periode']) ?>
        };

        var el = document.querySelector('#chart');
        if (!el || typeof ApexCharts === 'undefined') return;

        new ApexCharts(el, {
            series: [
                { name: 'Jiwa', data: trenData.jiwa },
                { name: 'KK',   data: trenData.kk }
            ],
            chart: {
                height: '100%',
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                background: 'transparent'
            },
            colors: ['#c8ff80', '#6b9e7a'],
            fill: {
                type: 'gradient',
                gradient: { opacityFrom: 0.3, opacityTo: 0.02, stops: [0, 100] }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: [2, 1.5] },
            xaxis: {
                categories: trenData.periode,
                labels: { style: { colors: '#8fa896', fontSize: '11px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { labels: { style: { colors: '#8fa896', fontSize: '11px' } } },
            grid: { borderColor: 'rgba(255,255,255,0.06)', strokeDashArray: 4 },
            theme: { mode: 'dark' },
            legend: { position: 'top', horizontalAlign: 'right', labels: { colors: '#afbcb2' }, fontSize: '12px' },
            tooltip: { theme: 'dark' }
        }).render();
    });
}());
</script>

