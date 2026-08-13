<?php
declare(strict_types=1);
$wisataList = $wisataList ?? [];
$totalWisata = count($wisataList);
$multiPage = $totalWisata > 3;
?>
<section id="wisata" class="w-full py-14 px-margin-mobile lg:px-margin-desktop bg-surface relative reveal">
<div class="max-w-container-max mx-auto">
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-stack-lg gap-4">
<div>
<h2 class="font-label-mono text-label-mono text-primary mb-2">WISATA ALAM</h2>
<h3 class="font-headline-lg text-[24px] md:text-[26px] text-on-surface max-w-lg">Jelajahi Keindahan Alam.</h3>
</div>
<a class="font-body-md text-primary flex items-center gap-2 hover:underline" href="<?= APP_BASE ?>/wisata">
Semua Wisata <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
</div>
<?php if ($wisataList === []): ?>
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] p-6 border border-glass-border text-center text-on-surface-variant font-body-md">Belum ada destinasi wisata yang dipublikasikan.</div>
<?php else: ?>
<div class="relative" data-wisata-carousel>
<div class="overflow-hidden rounded-[20px]" data-wisata-track-view>
<div class="flex gap-gutter transition-transform duration-500 ease-out <?= $multiPage ? '' : 'justify-center' ?>" data-wisata-track>
<?php foreach ($wisataList as $w): $gambarUtama = $w['gambar'][0] ?? null; ?>
<article class="group w-full md:w-[26%] shrink-0 bg-glass-fill backdrop-blur-md border border-glass-border rounded-[20px] overflow-hidden flex flex-col transition-all duration-300 hover:border-primary/40 hover:-translate-y-1">
<a href="<?= APP_BASE ?>/wisata/<?= e($w['slug']) ?>" class="relative w-full aspect-video overflow-hidden block">
<div class="absolute inset-0 bg-gradient-to-t from-surface to-transparent z-10 opacity-60"></div>
<?php if ($gambarUtama !== null && fotoAda($gambarUtama['path_gambar'])): ?>
<img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="<?= e($w['nama']) ?>" loading="lazy" src="<?= e(uploadUrl($gambarUtama['path_gambar'])) ?>"/>
<?php elseif ($gambarUtama !== null): ?>
<div class="w-full h-full bg-gradient-to-br from-primary/70 to-primary/30 flex items-center justify-center">
<span class="text-[32px] font-bold text-white"><?= e(inisialNama($w['nama'])) ?></span>
</div>
<?php else: ?>
<div class="w-full h-full bg-surface-container-high flex items-center justify-center">
<span class="material-symbols-outlined text-on-surface-variant text-[48px]">landscape</span>
</div>
<?php endif; ?>
</a>
<div class="p-4 flex flex-col flex-grow relative z-20 bg-gradient-to-b from-surface/50 to-surface/90">
<h3 class="font-headline-md text-[15px] text-on-surface mb-1.5 group-hover:text-primary transition-colors leading-snug line-clamp-1"><a href="<?= APP_BASE ?>/wisata/<?= e($w['slug']) ?>"><?= e($w['nama']) ?></a></h3>
<p class="font-body-md text-[12px] text-on-surface-variant mb-2.5 line-clamp-2"><?= e(truncate($w['deskripsi'], 120)) ?></p>
<div class="flex items-center gap-3 mb-3">
<span class="inline-flex items-center gap-1 bg-primary/10 border border-primary/25 text-primary rounded-full px-2.5 py-0.5 text-[11px] font-semibold shrink-0">
<span class="material-symbols-outlined text-[13px]">confirmation_number</span>
<?= e(($w['harga_tiket'] !== '' && $w['harga_tiket'] !== null) ? $w['harga_tiket'] : 'Gratis') ?>
</span>
<div class="flex items-center gap-1 text-on-surface-variant text-[11px] min-w-0 flex-1">
<span class="material-symbols-outlined text-[13px] shrink-0">location_on</span>
<span class="line-clamp-2 leading-snug"><?= e(truncate((string) $w['alamat'], 80)) ?></span>
</div>
</div>
<div class="mt-auto flex items-center justify-between border-t border-glass-border pt-3">
<a href="<?= APP_BASE ?>/wisata/<?= e($w['slug']) ?>" class="inline-flex items-center gap-2 text-primary font-body-md text-[12px] font-bold hover:text-primary-fixed transition-colors">
Lihat Detail <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
</a>
</div>
</div>
</article>
<?php endforeach; ?>
</div>
</div>
<?php if ($multiPage): ?>
<div class="flex items-center justify-center gap-4 mt-6">
<button type="button" data-wisata-prev class="carousel-btn w-11 h-11 rounded-full border border-glass-border bg-glass-fill backdrop-blur-md text-on-surface-variant flex items-center justify-center" aria-label="Sebelumnya">
<span class="material-symbols-outlined text-[20px]">chevron_left</span>
</button>
<div class="carousel-dots flex items-center gap-2" data-wisata-dots></div>
<button type="button" data-wisata-next class="carousel-btn w-11 h-11 rounded-full border border-glass-border bg-glass-fill backdrop-blur-md text-on-surface-variant flex items-center justify-center" aria-label="Berikutnya">
<span class="material-symbols-outlined text-[20px]">chevron_right</span>
</button>
</div>
<?php endif; ?>
</div>
<?php endif; ?>
</div>
</section>
<?php if ($totalWisata > 3): ?>
<script>
(function () {
    var root = document.querySelector('[data-wisata-carousel]');
    if (!root) return;
    var track = root.querySelector('[data-wisata-track]');
    var view = root.querySelector('[data-wisata-track-view]');
    var cards = Array.prototype.slice.call(track.children);
    var dotsWrap = root.querySelector('[data-wisata-dots]');
    var mq = window.matchMedia('(min-width: 768px)');
    var perPage = mq.matches ? 3 : 1;
    var pages = Math.max(1, Math.ceil(cards.length / perPage));
    var page = 0;
    var timer = null;
    var paused = false;

    function gapPx() {
        return parseFloat(window.getComputedStyle(track).gap) || 0;
    }

    function render(n) {
        page = ((n % pages) + pages) % pages;
        var cardW = cards[0].offsetWidth;
        var shift = page * perPage * (cardW + gapPx());
        track.style.transform = 'translateX(-' + shift + 'px)';
        var dots = dotsWrap ? dotsWrap.querySelectorAll('[data-wisata-dot]') : [];
        for (var i = 0; i < dots.length; i++) {
            dots[i].classList.toggle('carousel-dot-active', i === page);
        }
    }

    function buildDots() {
        if (!dotsWrap) return;
        dotsWrap.innerHTML = '';
        for (var i = 0; i < pages; i++) {
            var b = document.createElement('button');
            b.type = 'button';
            b.setAttribute('data-wisata-dot', '');
            b.className = 'carousel-dot' + (i === page ? ' carousel-dot-active' : '');
            b.setAttribute('aria-label', 'Slide ' + (i + 1));
            b.addEventListener('click', function (i) { return function () { render(i); resetTimer(); }; }(i));
            dotsWrap.appendChild(b);
        }
    }

    function resetTimer() {
        if (timer) { clearInterval(timer); timer = null; }
        if (!paused && pages > 1) {
            timer = setInterval(function () { render(page + 1); }, 4000);
        }
    }

    var prevBtn = root.querySelector('[data-wisata-prev]');
    var nextBtn = root.querySelector('[data-wisata-next]');
    if (prevBtn) prevBtn.addEventListener('click', function (e) { e.preventDefault(); render(page - 1); resetTimer(); });
    if (nextBtn) nextBtn.addEventListener('click', function (e) { e.preventDefault(); render(page + 1); resetTimer(); });

    root.addEventListener('mouseenter', function () { paused = true; if (timer) { clearInterval(timer); timer = null; } });
    root.addEventListener('mouseleave', function () { paused = false; resetTimer(); });

    mq.addEventListener('change', function () {
        perPage = mq.matches ? 3 : 1;
        pages = Math.max(1, Math.ceil(cards.length / perPage));
        if (page > pages - 1) page = pages - 1;
        buildDots();
        render(page);
        resetTimer();
    });

    buildDots();
    render(0);
    resetTimer();
})();
</script>
<?php endif; ?>
