<?php
declare(strict_types=1);
$namaPekon = $profil['nama_pekon'] ?? 'Desa Padang Cermin';
?>
<header class="fixed top-0 w-full z-50 flex justify-center py-4">
<div class="h-auto md:h-16 w-full max-w-[1200px] mx-margin-mobile lg:mx-margin-desktop bg-glass-fill backdrop-blur-xl border border-glass-border rounded-2xl md:rounded-full flex flex-col md:flex-row items-center justify-between p-4 md:px-8 shadow-lg gap-4 md:gap-0">
<div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-start">
<a class="flex items-center gap-3 shrink-0" href="<?= APP_BASE ?>/">
<img alt="Logo <?= e($namaPekon) ?>" class="h-8 w-auto object-contain" src="<?= assetUrl('img/logo.png') ?>"/>
<span class="font-headline-md text-[18px] md:text-headline-md text-primary tracking-tight"><?= e($namaPekon) ?></span>
</a>
<div class="md:hidden flex items-center gap-1.5 bg-glass-fill/70 border border-glass-border rounded-full px-3 py-1.5" title="Waktu lokal WIB">
<span class="material-symbols-outlined text-[14px] text-primary">schedule</span>
<span class="font-label-mono text-[11px] text-on-surface tabular-nums" id="mobile-clock-time">--:--:--</span>
</div>
</div>
<nav class="flex items-center gap-1 md:gap-2 lg:gap-stack-lg w-full md:w-auto overflow-x-auto whitespace-nowrap [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden" id="main-nav">
<a class="nav-link text-on-surface-variant hover:text-primary transition-colors font-body-md px-3 py-1.5 rounded-full" data-path="beranda" href="<?= APP_BASE ?>/">Beranda</a>
<a class="nav-link text-on-surface-variant hover:text-primary transition-colors font-body-md px-3 py-1.5 rounded-full" data-path="profil" href="<?= APP_BASE ?>/#profil">Profil</a>
<a class="nav-link text-on-surface-variant hover:text-primary transition-colors font-body-md px-3 py-1.5 rounded-full" data-path="struktur" href="<?= APP_BASE ?>/#struktur">Struktur</a>
<a class="nav-link text-on-surface-variant hover:text-primary transition-colors font-body-md px-3 py-1.5 rounded-full" data-path="data" href="<?= APP_BASE ?>/#data">Data Penduduk</a>
<a class="nav-link text-on-surface-variant hover:text-primary transition-colors font-body-md px-3 py-1.5 rounded-full" data-path="potensi" href="<?= APP_BASE ?>/#potensi">Potensi</a>
<a class="nav-link text-on-surface-variant hover:text-primary transition-colors font-body-md px-3 py-1.5 rounded-full" data-path="wisata" href="<?= APP_BASE ?>/wisata">Wisata</a>
<a class="nav-link text-on-surface-variant hover:text-primary transition-colors font-body-md px-3 py-1.5 rounded-full" data-path="berita" href="<?= APP_BASE ?>/berita">Berita</a>
</nav>
<div class="hidden md:flex items-center gap-2 bg-glass-fill/70 border border-glass-border rounded-full px-4 py-2" title="Waktu lokal WIB">
<span class="material-symbols-outlined text-[16px] text-primary">schedule</span>
<span class="flex flex-col leading-tight text-right">
<span class="font-label-mono text-[13px] text-on-surface tabular-nums" id="desktop-clock-time">--:--:--</span>
<span class="font-caption text-[10px] text-on-surface-variant" id="desktop-clock-date">WIB</span>
</span>
</div>
</div>
</header>
<script>
(function () {
    var appBase = <?= json_encode(APP_BASE, JSON_UNESCAPED_SLASHES) ?>;
    var links = Array.prototype.slice.call(document.querySelectorAll('#main-nav a[data-path]'));
    function setActive(key) {
        links.forEach(function (a) {
            var on = a.getAttribute('data-path') === key;
            a.classList.toggle('text-primary', on);
            a.classList.toggle('font-bold', on);
            a.classList.toggle('text-on-surface-variant', !on);
        });
    }
    function currentPath() {
        var p = window.location.pathname;
        if (appBase && p.indexOf(appBase) === 0) { p = p.slice(appBase.length); }
        return p === '' ? '/' : p;
    }
    function resolve() {
        var p = currentPath();
        var m = p.match(/^\/(wisata|berita)(\/|$)/);
        if (m) { setActive(m[1]); return; }
        if (p === '/') {
            var h = window.location.hash;
            if (h) {
                var k = links.filter(function (a) { return (a.getAttribute('href') || '').indexOf(h) !== -1; })[0];
                if (k) { setActive(k.getAttribute('data-path')); return; }
            }
            setActive('beranda'); return;
        }
        setActive('beranda');
    }
    resolve();
    window.addEventListener('hashchange', resolve);
    if (currentPath() === '/') {
        var spy = links
            .filter(function (a) { return (a.getAttribute('href') || '').indexOf('#') !== -1; })
            .map(function (a) {
                return { key: a.getAttribute('data-path'), id: (a.getAttribute('href') || '').split('#')[1] };
            });
        var ticking = false;
        function onScroll() {
            if (ticking) { return; }
            ticking = true;
            window.requestAnimationFrame(function () {
                var active = null, best = -1;
                spy.forEach(function (t) {
                    var el = document.getElementById(t.id);
                    if (!el) { return; }
                    var r = el.getBoundingClientRect();
                    if (r.top <= 160 && r.bottom > 0 && r.top > best) { best = r.top; active = t.key; }
                });
                setActive(active || 'beranda');
                ticking = false;
            });
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }
})();
(function () {
    function updateClock() {
        var now = new Date();
        var t = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        var d = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
        var dt = document.getElementById('desktop-clock-time');
        var dd = document.getElementById('desktop-clock-date');
        if (dt) { dt.textContent = t; }
        if (dd) { dd.textContent = d + ' WIB'; }
        var mt = document.getElementById('mobile-clock-time');
        if (mt) { mt.textContent = t; }
    }
    updateClock();
    setInterval(updateClock, 1000);
})();
</script>
