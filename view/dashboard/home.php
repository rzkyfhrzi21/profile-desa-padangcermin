<?php

declare(strict_types=1);

$judulHalaman = 'Overview Pekon';
$activeMenu   = 'dashboard';

$totalBerita  = countBerita();
$totalWisata  = (function () {
    $db   = getDb();
    $stmt = $db->query('SELECT COUNT(*) FROM wisata_desa');
    return (int) $stmt->fetchColumn();
})();
$totalPotensi = (function () {
    $db   = getDb();
    $stmt = $db->query('SELECT COUNT(*) FROM potensi_desa');
    return (int) $stmt->fetchColumn();
})();

$tren    = getTrenKependudukan();
$logTerbaru  = getLogTerbaru(8);
$wisataSorotan = getWisataWithGambar(getWisataList(true, 5));
$jiwa    = $tren['jumlah_jiwa'] !== [] ? (int) end($tren['jumlah_jiwa']) : 0;

require __DIR__ . '/layout.php';
?>

<!-- ===== HEADER ===== -->
<div class="mb-8">
    <div class="flex items-center gap-2 mb-2">
        <span class="w-2 h-2 rounded-full bg-primary shadow-lime-glow animate-pulse"></span>
        <span class="text-label-mono font-label-mono text-primary uppercase tracking-widest text-[11px]">Panel Utama</span>
    </div>
    <h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0 leading-tight">Ringkasan Pekon</h1>
    <p class="text-body-md font-body-md text-on-surface-variant mt-1 m-0">Pengelolaan data digital Desa Padang Cermin — pantau semua modul dari sini.</p>
</div>

<!-- ===== STAT CARDS — 4 cards full width ===== -->
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

    <!-- Berita -->
    <div class="group relative bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-5 flex flex-col gap-3 hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 overflow-hidden">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-primary/8 rounded-full blur-2xl group-hover:bg-primary/15 transition-colors"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="w-9 h-9 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings:'FILL' 1">newspaper</span>
            </div>
            <a href="<?= APP_BASE ?>/dashboard/berita" class="text-on-surface-variant hover:text-primary transition-colors" title="Kelola berita">
                <span class="material-symbols-outlined text-[16px]">arrow_outward</span>
            </a>
        </div>
        <div class="relative z-10">
            <p class="text-[11px] text-on-surface-variant font-medium uppercase tracking-wider mb-1">Berita</p>
            <div class="flex items-end gap-2">
                <span class="text-[36px] font-bold font-mono text-on-surface leading-none"><?= formatAngka($totalBerita) ?></span>
                <span class="text-xs text-on-surface-variant mb-1">artikel</span>
            </div>
        </div>
    </div>

    <!-- Wisata -->
    <div class="group relative bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-5 flex flex-col gap-3 hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 overflow-hidden">
        <div class="absolute -left-8 -bottom-8 w-32 h-32 bg-primary/8 rounded-full blur-2xl group-hover:bg-primary/15 transition-colors"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="w-9 h-9 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings:'FILL' 1">landscape</span>
            </div>
            <a href="<?= APP_BASE ?>/dashboard/wisata" class="text-on-surface-variant hover:text-primary transition-colors" title="Kelola wisata">
                <span class="material-symbols-outlined text-[16px]">arrow_outward</span>
            </a>
        </div>
        <div class="relative z-10">
            <p class="text-[11px] text-on-surface-variant font-medium uppercase tracking-wider mb-1">Wisata</p>
            <div class="flex items-end gap-2">
                <span class="text-[36px] font-bold font-mono text-primary leading-none"><?= formatAngka($totalWisata) ?></span>
                <span class="text-xs text-on-surface-variant mb-1">destinasi</span>
            </div>
        </div>
    </div>

    <!-- Potensi -->
    <div class="group relative bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-5 flex flex-col gap-3 hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-primary/8 rounded-full blur-2xl group-hover:bg-primary/15 transition-colors"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="w-9 h-9 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings:'FILL' 1">psychiatry</span>
            </div>
            <a href="<?= APP_BASE ?>/dashboard/potensi" class="text-on-surface-variant hover:text-primary transition-colors" title="Kelola potensi">
                <span class="material-symbols-outlined text-[16px]">arrow_outward</span>
            </a>
        </div>
        <div class="relative z-10">
            <p class="text-[11px] text-on-surface-variant font-medium uppercase tracking-wider mb-1">Potensi Desa</p>
            <div class="flex items-end gap-2">
                <span class="text-[36px] font-bold font-mono text-on-surface leading-none"><?= formatAngka($totalPotensi) ?></span>
                <span class="text-xs text-on-surface-variant mb-1">item</span>
            </div>
        </div>
    </div>

    <!-- Kependudukan -->
    <div class="group relative bg-glass-fill backdrop-blur-md rounded-[20px] border border-primary/25 bg-primary/5 p-5 flex flex-col gap-3 hover:-translate-y-0.5 transition-all duration-300 overflow-hidden">
        <div class="absolute -left-8 -top-8 w-32 h-32 bg-primary/15 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="w-9 h-9 rounded-xl bg-primary/20 border border-primary/30 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings:'FILL' 1">groups</span>
            </div>
            <a href="<?= APP_BASE ?>/dashboard/kependudukan" class="text-primary/60 hover:text-primary transition-colors" title="Data kependudukan">
                <span class="material-symbols-outlined text-[16px]">arrow_outward</span>
            </a>
        </div>
        <div class="relative z-10">
            <p class="text-[11px] text-primary/70 font-medium uppercase tracking-wider mb-1">Total Penduduk</p>
            <div class="flex items-end gap-2">
                <span class="text-[36px] font-bold font-mono text-primary leading-none"><?= $jiwa > 0 ? formatAngka($jiwa) : '—' ?></span>
                <span class="text-xs text-primary/60 mb-1">jiwa</span>
            </div>
        </div>
    </div>
</div>

<!-- ===== CHART + LOG grid ===== -->
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">

    <!-- ApexChart: Tren Kependudukan -->
    <section class="lg:col-span-3">
        <div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border overflow-hidden" style="min-height:420px">
            <div class="px-6 py-4 border-b border-glass-border/50 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-on-surface">Tren Kependudukan</h2>
                    <p class="text-xs text-on-surface-variant mt-0.5">Jumlah jiwa &amp; KK per periode</p>
                </div>
                <span class="text-caption font-label-mono text-primary bg-primary/10 px-3 py-1 rounded-full text-[11px]"><?= count($tren['periode']) ?> periode</span>
            </div>
            <div class="p-4">
                <?php if ($tren['periode'] === []): ?>
                    <div class="flex flex-col items-center justify-center py-16 gap-3 text-center">
                        <span class="material-symbols-outlined text-[48px] text-on-surface-variant/30">show_chart</span>
                        <p class="text-sm text-on-surface-variant">Belum ada data kependudukan.</p>
                    </div>
                <?php else: ?>
                    <div id="home-chart" style="height:300px;width:100%"></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Log Aktivitas -->
    <section class="lg:col-span-2">
        <div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border h-full flex flex-col" style="min-height:360px">
            <div class="px-6 py-4 border-b border-glass-border/50 shrink-0 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[18px]">history</span>
                <h2 class="text-base font-semibold text-on-surface">Aktivitas Terakhir</h2>
            </div>
            <div class="divide-y divide-glass-border/40 flex-1 overflow-y-auto">
                <?php foreach ($logTerbaru as $log): ?>
                    <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-surface-container-highest/50 transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-surface-container border border-glass-border flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-on-surface-variant text-[14px]">history</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-on-surface leading-snug">
                                <span class="font-medium text-primary"><?= e($log['admin_nama'] ?? 'Admin') ?></span>
                                <?= e($log['aksi']) ?> — <span class="text-on-surface-variant"><?= e($log['tabel_terkait']) ?></span>
                            </p>
                            <p class="text-[11px] text-on-surface-variant mt-0.5"><?= e($log['waktu']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if ($logTerbaru === []): ?>
                    <div class="px-5 py-8 text-sm text-on-surface-variant text-center flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-[32px] text-on-surface-variant/30">history</span>
                        Belum ada aktivitas.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<!-- ===== SOROTAN WISATA ===== -->
<?php if ($wisataSorotan !== []): ?>
    <section class="mb-6">
        <div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border overflow-hidden">
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
                <div class="relative" data-carousel data-carousel-interval="1500">
                    <div class="overflow-hidden rounded-xl">
                        <?php foreach ($wisataSorotan as $w):
                            $gambarUtama = $w['gambar'][0] ?? null; ?>
                            <div class="carousel-slide">
                                <a href="<?= APP_BASE ?>/wisata/<?= e($w['slug']) ?>"
                                    class="group relative flex w-full aspect-[21/9] overflow-hidden rounded-xl block">
                                    <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-white/20 to-transparent z-10"></div>
                                    <?php if ($gambarUtama !== null): ?>
                                        <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                            data-skeleton
                                            alt="<?= e($w['nama']) ?>"
                                            loading="lazy"
                                            src="<?= e(uploadUrl($gambarUtama['path_gambar'])) ?>" />
                                    <?php else: ?>
                                        <div class="w-full h-full bg-surface-container-high flex items-center justify-center">
                                            <span class="material-symbols-outlined text-on-surface-variant text-[48px]">landscape</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="absolute bottom-0 left-0 right-0 z-20 p-5 flex items-end justify-between gap-4">
                                        <div class="flex flex-col gap-1">
                                            <h3 class="text-base font-semibold text-coklat group-hover:text-primary transition-colors"><?= e($w['nama']) ?></h3>
                                            <p class="text-xs text-[#6B655C] line-clamp-1"><?= e(truncate($w['deskripsi'], 90)) ?></p>
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

<script>
    (function() {
        var trenData = {
            jiwa: <?= json_encode(array_map('intval', $tren['jumlah_jiwa'])) ?>,
            kk: <?= json_encode(array_map('intval', $tren['jumlah_kk'])) ?>,
            periode: <?= json_encode($tren['periode']) ?>
        };

        window.addEventListener('load', function() {
            var el = document.querySelector('#home-chart');
            if (!el || typeof ApexCharts === 'undefined') return;

            try {
                new ApexCharts(el, {
                    series: [{
                            name: 'Jiwa',
                            data: trenData.jiwa
                        },
                        {
                            name: 'KK',
                            data: trenData.kk
                        }
                    ],
                    chart: {
                        height: 300,
                        type: 'area',
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Plus Jakarta Sans, sans-serif',
                        background: 'transparent',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 600
                        }
                    },
                    colors: ['#c8ff80', '#6b9e7a'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            opacityFrom: 0.3,
                            opacityTo: 0.02,
                            stops: [0, 100]
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: [2, 1.5]
                    },
                    markers: {
                        size: 3,
                        colors: ['#c8ff80', '#6b9e7a'],
                        strokeWidth: 0
                    },
                    xaxis: {
                        categories: trenData.periode,
                        labels: {
                            style: {
                                colors: '#8fa896',
                                fontSize: '11px'
                            }
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#8fa896',
                                fontSize: '11px'
                            }
                        }
                    },
                    grid: {
                        borderColor: 'rgba(255,255,255,0.06)',
                        strokeDashArray: 4
                    },
                    theme: {
                        mode: 'dark'
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        labels: {
                            colors: '#afbcb2'
                        },
                        fontSize: '12px'
                    },
                    tooltip: {
                        theme: 'dark'
                    }
                }).render();
            } catch (err) {
                console.warn('Home chart error:', err);
            }
        });
    }());
</script>
<?php require __DIR__ . '/layout_close.php'; ?>