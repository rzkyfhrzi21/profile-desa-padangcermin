<?php
declare(strict_types=1);

$admin = currentAdmin();
$activeMenu = $activeMenu ?? '';
$judulHalaman = $judulHalaman ?? 'Dashboard';
$pagePath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePrefix = APP_BASE;
if ($basePrefix !== '' && str_starts_with($pagePath, $basePrefix)) {
    $pagePath = substr($pagePath, strlen($basePrefix));
}

$menuGroups = [
    'main' => [
        'label' => 'Menu Utama',
        'items' => [
            'dashboard' => ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => '/dashboard', 'aktif' => $pagePath === '/dashboard'],
        ],
    ],
    'konten' => [
        'label' => 'Konten Desa',
        'items' => [
            'wisata'  => ['label' => 'Wisata',    'icon' => 'landscape', 'route' => '/dashboard/wisata',  'aktif' => str_starts_with($pagePath, '/dashboard/wisata')],
            'berita'  => ['label' => 'Berita',    'icon' => 'newspaper', 'route' => '/dashboard/berita',  'aktif' => str_starts_with($pagePath, '/dashboard/berita')],
        ],
    ],
    'data' => [
        'label' => 'Data & Profil',
        'items' => [
            'penduduk' => ['label' => 'Kependudukan', 'icon' => 'group',        'route' => '/dashboard/kependudukan', 'aktif' => str_starts_with($pagePath, '/dashboard/kependudukan')],
            'potensi'  => ['label' => 'Potensi Desa', 'icon' => 'psychiatry',   'route' => '/dashboard/potensi',      'aktif' => str_starts_with($pagePath, '/dashboard/potensi')],
            'profil'   => ['label' => 'Profil Desa',  'icon' => 'home_work',    'route' => '/dashboard/profil',       'aktif' => str_starts_with($pagePath, '/dashboard/profil') && !str_starts_with($pagePath, '/dashboard/profil_')],
            'struktur' => ['label' => 'Struktur',     'icon' => 'account_tree', 'route' => '/dashboard/struktur',     'aktif' => str_starts_with($pagePath, '/dashboard/struktur')],
        ],
    ],
    'akun' => [
        'label' => 'Pengaturan',
        'items' => [
            'adminprofil' => ['label' => 'Profil Admin', 'icon' => 'manage_accounts', 'route' => '/dashboard/admin/profil', 'aktif' => str_starts_with($pagePath, '/dashboard/admin')],
        ],
    ],
];

$flashJson = json_encode(getFlash(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

/* Hitung initial date untuk SSR (tidak ada blank flash) */
$initDay   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][(int) date('w')];
$initDate  = $initDay . ', ' . date('j') . ' ' . ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'][(int) date('n') - 1] . ' ' . date('Y');
$initClock = date('H:i:s');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="robots" content="noindex, nofollow"/>
<meta name="csrf-token" content="<?= e(csrfToken()) ?>"/>
<title><?= e($judulHalaman) ?> | Admin Desa Padang Cermin</title>
<link rel="icon" type="image/x-icon" href="<?= APP_BASE ?>/favicon.ico"/>
<link rel="shortcut icon" href="<?= APP_BASE ?>/favicon.ico"/>
<!-- FOUC prevention: set sidebar state sebelum paint pertama -->
<script>
(function () {
    try {
        if (localStorage.getItem('admin_sidebar_collapsed') === '1') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    } catch (e) {}
})();
</script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<link href="<?= assetUrl('css/tailwind.css') ?>" rel="stylesheet"/>
</head>
<body class="bg-admin-bg font-body-md text-on-surface">

<!-- ===== SIDEBAR ===== -->
<aside id="admin-sidebar" class="fixed left-0 top-0 h-full w-72 bg-gradient-to-br from-[#123B28] to-[#0A2418] z-50 flex flex-col border-r border-glass-border overflow-hidden transition-[width,transform] duration-300 ease-in-out">

    <!-- Brand -->
    <div class="sidebar-brand flex items-center gap-3 px-5 py-4 border-b border-glass-border/40 shrink-0 overflow-hidden">
        <img alt="Logo Desa Padang Cermin"
             class="h-8 w-8 object-contain flex-shrink-0 rounded"
             src="<?= assetUrl('img/logo.png') ?>"
             loading="eager"/>
        <div class="sidebar-label-wrap flex flex-col min-w-0 overflow-hidden">
            <span class="text-on-surface text-sm font-semibold leading-tight truncate">Padang Cermin</span>
            <span class="text-on-surface-variant text-[11px] truncate"><?= e($admin['nama'] ?? 'Admin') ?></span>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3">
        <?php foreach ($menuGroups as $group): ?>
        <div class="sidebar-label px-5 pt-4 pb-1.5 text-[10px] font-semibold tracking-[0.16em] uppercase text-primary/50 whitespace-nowrap"><?= e($group['label']) ?></div>
        <?php foreach ($group['items'] as $key => $item): ?>
        <a class="sidebar-link group flex items-center gap-4 px-5 py-3.5 mx-2 rounded-xl transition-all duration-200 whitespace-nowrap overflow-hidden
            <?= $item['aktif']
                ? 'bg-hijau text-white shadow-lg shadow-black/25'
                : 'text-on-surface-variant hover:bg-hijau/25 hover:text-white' ?>"
           title="<?= e($item['label']) ?>"
           href="<?= APP_BASE . $item['route'] ?>">
            <span class="material-symbols-outlined flex-shrink-0 text-[22px]
                <?= $item['aktif'] ? '' : 'group-hover:text-primary' ?>"
                  style="font-variation-settings: 'FILL' <?= $item['aktif'] ? '1' : '0' ?>">
                <?= $item['icon'] ?>
            </span>
            <span class="sidebar-label text-sm font-medium truncate"><?= $item['label'] ?></span>
            <?php if ($item['aktif']): ?>
            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-white flex-shrink-0 sidebar-label"></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>

    <!-- Logout -->
    <div class="shrink-0 px-2 py-3 border-t border-glass-border/40">
        <form action="<?= APP_BASE ?>/auth/logout" method="post" id="logout-form">
            <?= csrfField() ?>
            <button type="button" onclick="document.getElementById('logout-form').submit()"
                class="sidebar-link group flex items-center gap-4 px-5 py-3.5 w-full rounded-xl transition-all duration-200 whitespace-nowrap overflow-hidden text-on-surface-variant hover:bg-red-500/10 hover:text-red-400"
                title="Keluar">
                <span class="material-symbols-outlined flex-shrink-0 text-[22px]">logout</span>
                <span class="sidebar-label text-sm font-medium">Keluar</span>
            </button>
        </form>
    </div>
</aside>

<!-- Backdrop mobile -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 pointer-events-none opacity-0 transition-opacity duration-300 lg:hidden"></div>

<!-- ===== MAIN WRAPPER ===== -->
<div id="admin-main-wrap" class="admin-main transition-[padding] duration-300 ease-in-out min-h-screen flex flex-col">

    <!-- ===== TOPBAR ===== -->
    <header id="admin-topbar" class="fixed top-0 right-0 h-16 bg-admin-topbar backdrop-blur-xl border-b border-black/8 z-[45] flex items-center justify-between gap-3 px-4 lg:px-6 transition-[left] duration-300 ease-in-out" style="left: 0;">

        <!-- Left: hamburger + breadcrumb -->
        <div class="flex items-center gap-3 min-w-0">
            <button id="sidebar-toggle" type="button"
                class="w-10 h-10 shrink-0 flex items-center justify-center rounded-xl border border-black/10 bg-white/70 text-hijau hover:text-primary hover:border-primary/40 transition-colors shadow-sm"
                aria-label="Toggle sidebar">
                <span class="material-symbols-outlined text-[20px]">menu</span>
            </button>
            <span class="hidden sm:block text-sm text-hijau/70 font-medium truncate"><?= e($judulHalaman) ?></span>
        </div>

        <!-- Right: clock + notif + admin dropdown -->
        <div class="flex items-center gap-2 md:gap-4 shrink-0">

            <!-- Clock + Date -->
            <div class="hidden md:flex items-center gap-2 bg-white/70 px-3 py-1.5 rounded-full border border-black/10 font-mono text-xs text-hijau/80 select-none shadow-sm">
                <span class="material-symbols-outlined text-[15px] text-hijau">schedule</span>
                <span id="live-clock"><?= $initClock ?></span>
                <span class="text-hijau/30 mx-0.5">|</span>
                <span id="live-date"><?= $initDate ?></span>
            </div>


            <!-- Admin Dropdown -->
            <div class="relative" id="admin-dropdown-wrap">
                <button type="button" id="admin-dropdown-btn"
                    class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-xl hover:bg-black/5 transition-colors border border-transparent hover:border-black/10"
                    aria-haspopup="true" aria-expanded="false">
                    <div class="hidden lg:flex flex-col text-right leading-tight">
                        <span class="text-sm font-medium text-coklat"><?= e($admin['nama'] ?? 'Admin') ?></span>
                        <span class="text-xs text-hijau">Super Admin</span>
                    </div>
                    <?php if (!empty($admin['foto'])): ?>
                    <?php if (fotoAda($admin['foto'])): ?>
                    <img src="<?= e(uploadUrl($admin['foto'])) ?>" alt="Foto <?= e($admin['nama'] ?? 'Admin') ?>"
                         class="w-9 h-9 rounded-lg object-cover border border-primary/30 shrink-0"/>
                    <?php else: ?>
                    <?= avatarInisial($admin['nama'] ?? 'Admin', 'w-9 h-9 shrink-0', 'text-[14px]', 'rounded-lg') ?>
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="w-9 h-9 rounded-lg bg-surface-container-highest border border-glass-border flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings: 'FILL' 1">admin_panel_settings</span>
                    </div>
                    <?php endif; ?>
                    <span class="material-symbols-outlined text-hijau/60 text-[18px] transition-transform duration-200" id="admin-dropdown-chevron">expand_more</span>
                </button>

                <!-- Dropdown Menu -->
                <div id="admin-dropdown-menu"
                    class="absolute top-[calc(100%+8px)] right-0 w-64 bg-surface-container-highest border border-glass-border rounded-2xl shadow-2xl z-[55]
                           opacity-0 scale-95 pointer-events-none transition-all duration-150 origin-top-right overflow-hidden"
                    aria-hidden="true">

                    <!-- Profile card di atas menu -->
                    <div class="flex items-center gap-3 px-4 py-3.5 border-b border-glass-border bg-surface-container-low">
                        <?php if (!empty($admin['foto'])): ?>
                        <?php if (fotoAda($admin['foto'])): ?>
                        <img src="<?= e(uploadUrl($admin['foto'])) ?>" alt="Foto <?= e($admin['nama'] ?? 'Admin') ?>"
                             class="w-11 h-11 rounded-xl object-cover border border-primary/30 shrink-0"/>
                        <?php else: ?>
                        <?= avatarInisial($admin['nama'] ?? 'Admin', 'w-11 h-11 shrink-0', 'text-[16px]', 'rounded-xl') ?>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="w-11 h-11 rounded-xl bg-primary/15 border border-primary/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary text-[22px]" style="font-variation-settings: 'FILL' 1">admin_panel_settings</span>
                        </div>
                        <?php endif; ?>
                        <div class="flex flex-col min-w-0">
                            <span class="text-sm font-semibold text-on-surface truncate"><?= e($admin['nama'] ?? 'Admin') ?></span>
                            <span class="text-xs text-primary truncate">Super Admin</span>
                        </div>
                    </div>

                    <!-- Menu items -->
                    <div class="py-1.5">
                        <a href="<?= APP_BASE ?>/" target="_blank"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-surface-container-high/60 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">open_in_new</span>Lihat Beranda
                        </a>
                        <a href="<?= APP_BASE ?>/dashboard"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-surface-container-high/60 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">dashboard</span>Dashboard
                        </a>
                        <a href="<?= APP_BASE ?>/dashboard/admin/profil"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-surface-container-high/60 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">manage_accounts</span>Profil Admin
                        </a>
                        <div class="h-px bg-glass-border mx-3 my-1"></div>
                        <form action="<?= APP_BASE ?>/auth/logout" method="post">
                            <?= csrfField() ?>
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">logout</span>Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== PAGE CONTENT ===== -->
    <!-- pt-20 = tinggi topbar 64px + safety 16px. px dan pb terpisah agar tidak override pt -->
    <main class="flex-1 pt-20 pb-10 px-4 lg:px-8">
        <div class="flex flex-col w-full relative">
            <script id="flash-data" type="application/json"><?= $flashJson ?></script>
