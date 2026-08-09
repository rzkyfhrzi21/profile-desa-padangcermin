<?php
declare(strict_types=1);
?>
        </div>
    </main>
    <footer class="w-full text-center py-6 border-t border-glass-border/40 text-on-surface-variant text-xs">
        &copy; <?= date('Y') ?> Desa Padang Cermin. All rights reserved.
    </footer>
</div><!-- /admin-main-wrap -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.0/dist/apexcharts.min.js"></script>
<script src="<?= assetUrl('js/security-warning.js') ?>"></script>
<script src="<?= assetUrl('js/media.js') ?>"></script>
<script src="<?= assetUrl('js/carousel.js') ?>"></script>
<script src="<?= assetUrl('js/admin.js') ?>"></script>

<script>
(function () {
    'use strict';

    /* ---- Expose APP_BASE untuk modul JS ---- */
    window.APP_BASE = '<?= APP_BASE ?>';

    /* ---- Clock + Date realtime ---- */
    var DAYS   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    var MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];

    function updateClock() {
        var now   = new Date();
        var h     = String(now.getHours()).padStart(2, '0');
        var m     = String(now.getMinutes()).padStart(2, '0');
        var s     = String(now.getSeconds()).padStart(2, '0');
        var clock = document.getElementById('live-clock');
        var date  = document.getElementById('live-date');
        if (clock) clock.textContent = h + ':' + m + ':' + s;
        if (date)  date.textContent  = DAYS[now.getDay()] + ', ' + now.getDate() + ' ' + MONTHS[now.getMonth()] + ' ' + now.getFullYear();
    }
    /* Jalankan sekali langsung, lalu setiap detik */
    updateClock();
    setInterval(updateClock, 1000);

    /* ---- Admin Dropdown ---- */
    var dropBtn  = document.getElementById('admin-dropdown-btn');
    var dropMenu = document.getElementById('admin-dropdown-menu');
    var dropChev = document.getElementById('admin-dropdown-chevron');

    function openDropdown() {
        if (!dropMenu) return;
        dropMenu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
        dropMenu.classList.add('opacity-100', 'scale-100');
        dropMenu.setAttribute('aria-hidden', 'false');
        if (dropBtn)  dropBtn.setAttribute('aria-expanded', 'true');
        if (dropChev) dropChev.style.transform = 'rotate(180deg)';
    }

    function closeDropdown() {
        if (!dropMenu) return;
        dropMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        dropMenu.classList.remove('opacity-100', 'scale-100');
        dropMenu.setAttribute('aria-hidden', 'true');
        if (dropBtn)  dropBtn.setAttribute('aria-expanded', 'false');
        if (dropChev) dropChev.style.transform = '';
    }

    if (dropBtn) {
        dropBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = dropMenu && !dropMenu.classList.contains('pointer-events-none');
            isOpen ? closeDropdown() : openDropdown();
        });
    }
    document.addEventListener('click', function (e) {
        if (dropMenu && !dropMenu.contains(e.target) && dropBtn && !dropBtn.contains(e.target)) {
            closeDropdown();
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDropdown();
    });

    /* ---- Sidebar toggle (desktop collapse + mobile drawer) ---- */
    var toggleBtn  = document.getElementById('sidebar-toggle');
    var sidebar    = document.getElementById('admin-sidebar');
    var mainWrap   = document.getElementById('admin-main-wrap');
    var topbar     = document.getElementById('admin-topbar');
    var backdrop   = document.getElementById('sidebar-backdrop');
    var htmlEl     = document.documentElement;

    var SIDEBAR_W  = 288; /* w-72 = 18rem = 288px */
    var COLLAPSED_W = 80; /* w-20 = 5rem = 80px */

    function isDesktop() { return window.innerWidth >= 1024; }

    /* Baca state awal dari CSS class yang di-set FOUC script */
    var collapsed = htmlEl.classList.contains('sidebar-collapsed');
    var mobileOpen = false;

    function applyDesktopState() {
        if (collapsed) {
            sidebar.style.width  = COLLAPSED_W + 'px';
            mainWrap.style.paddingLeft = COLLAPSED_W + 'px';
            topbar.style.left          = COLLAPSED_W + 'px';
        } else {
            sidebar.style.width  = SIDEBAR_W + 'px';
            mainWrap.style.paddingLeft = SIDEBAR_W + 'px';
            topbar.style.left          = SIDEBAR_W + 'px';
        }
        sidebar.style.transform = '';
        backdrop.style.opacity = '0';
        backdrop.style.pointerEvents = 'none';
        mobileOpen = false;
    }

    function applyMobileState() {
        /* Pada mobile sidebar selalu full w-72, main wrap tidak geser */
        sidebar.style.width = SIDEBAR_W + 'px';
        mainWrap.style.paddingLeft = '0px';
        topbar.style.left = '0px';
        if (mobileOpen) {
            sidebar.style.transform = 'translateX(0)';
            backdrop.style.opacity = '1';
            backdrop.style.pointerEvents = 'auto';
        } else {
            sidebar.style.transform = 'translateX(-100%)';
            backdrop.style.opacity = '0';
            backdrop.style.pointerEvents = 'none';
        }
    }

    function applyLayout() {
        if (isDesktop()) {
            applyDesktopState();
        } else {
            applyMobileState();
        }
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            if (isDesktop()) {
                collapsed = !collapsed;
                htmlEl.classList.toggle('sidebar-collapsed', collapsed);
                try { localStorage.setItem('admin_sidebar_collapsed', collapsed ? '1' : '0'); } catch (e) {}
            } else {
                mobileOpen = !mobileOpen;
            }
            applyLayout();
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', function () {
            mobileOpen = false;
            applyLayout();
        });
    }

    window.addEventListener('resize', function () {
        if (isDesktop()) {
            mobileOpen = false;
        }
        applyLayout();
    });

    /* Jalankan layout segera */
    applyLayout();

})();
</script>
</body>
</html>
