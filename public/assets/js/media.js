/* media.js — lightbox preview + skeleton loading untuk foto/video (landing & admin) */
/* v2: fix lightbox baca value data-lightbox (bukan hanya keberadaan atribut), scope-aware skeleton */
(function () {
    'use strict';

    /* ---------- Skeleton loading ---------- */
    /**
     * @param {Element|Document} [scope=document] — scope elemen untuk scan img[data-skeleton]
     */
    function initSkeleton(scope) {
        var root = scope || document;
        root.querySelectorAll('img[data-skeleton]').forEach(function (img) {
            /* Skip jika sudah selesai load atau sudah ada skeleton */
            if (img._skeletonInit) return;
            img._skeletonInit = true;

            var wrap = img.parentElement;
            if (!wrap) return;

            /* Jika sudah complete & tidak error, tidak perlu skeleton */
            if (img.complete && img.naturalWidth > 0) return;

            if (!wrap.classList.contains('relative')) {
                wrap.classList.add('relative');
            }
            /* Jangan duplikasi skeleton */
            if (wrap.querySelector('.skeleton-overlay')) return;

            var sk = document.createElement('div');
            sk.className = 'skeleton skeleton-overlay absolute inset-0 rounded-inherit';
            /* Match parent border-radius */
            sk.style.borderRadius = window.getComputedStyle(wrap).borderRadius || '0.5rem';
            wrap.appendChild(sk);

            img.classList.add('relative', 'z-[1]');
            if (!img.complete || img.naturalWidth === 0) {
                img.style.opacity = '0';
            }

            function done() {
                sk.remove();
                img.style.opacity = '';
                img.style.transition = 'opacity 0.3s ease';
            }

            img.addEventListener('load', done, { once: true });
            img.addEventListener('error', function () {
                done();
                /* Broken image fallback */
                if (!img.src.includes('placeholder')) {
                    img.onerror = null;
                    img.src = (window.APP_BASE || '') + '/assets/img/placeholder.webp';
                }
            }, { once: true });
        });
    }

    /* ---------- Lightbox ---------- */
    var overlay = null;

    function closeLightbox() {
        if (!overlay) return;
        document.removeEventListener('keydown', escHandler);
        var trigger = overlay._trigger || null;
        overlay.remove();
        overlay = null;
        document.body.style.overflow = '';
        if (trigger && trigger.focus) trigger.focus();
    }

    function escHandler(e) {
        if (e.key === 'Escape') closeLightbox();
    }

    function openLightbox(el) {
        var isVideo = el.matches('[data-lightbox-video]');

        /* Fix: read the VALUE of data-lightbox attribute (URL), not just its presence */
        var src = '';
        if (isVideo) {
            src = el.getAttribute('data-lightbox-video') || el.getAttribute('src') || '';
        } else {
            src = el.getAttribute('data-lightbox') || '';
            /* If data-lightbox has no value, try the img src itself */
            if (src === '' || src === 'true') {
                src = el.getAttribute('src') || '';
            }
        }
        if (!src) return;

        var alt = el.getAttribute('alt') || '';

        overlay = document.createElement('div');
        overlay._trigger = el;
        overlay.className = 'fixed inset-0 z-[90] flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm modal-overlay';

        var panel = document.createElement('div');
        panel.className = 'relative max-w-5xl w-full modal-panel';

        /* Close button — above the image */
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Tutup');
        btn.className = 'absolute -top-14 right-0 w-12 h-12 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/25 text-white transition-colors z-10 border border-white/20';
        btn.innerHTML = '<span class="material-symbols-outlined text-[22px]">close</span>';
        btn.addEventListener('click', closeLightbox);

        var media;
        if (isVideo) {
            media = document.createElement('video');
            media.src = src;
            media.controls = true;
            media.autoplay = false;
            media.className = 'w-full max-h-[80vh] rounded-2xl bg-black shadow-2xl';
        } else {
            media = document.createElement('img');
            media.src = src;
            media.alt = alt;
            media.className = 'w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl';
            media.style.background = 'rgba(0,0,0,0.5)';
        }

        panel.appendChild(btn);
        panel.appendChild(media);
        overlay.appendChild(panel);

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeLightbox();
        });

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', escHandler);
        btn.focus();
    }

    /**
     * @param {Element|Document|null} [scope] — scope untuk bind klik. null = global document (dipanggil sekali).
     * Jika scope diberikan (mis. panel modal), bind delegasi klik pada scope tsb.
     */
    function initLightbox(scope) {
        if (scope) {
            /* Scope-bound: bind pada elemen tertentu (mis. modal panel) */
            if (scope._lightboxBound) return;
            scope._lightboxBound = true;
            scope.addEventListener('click', function (e) {
                /* Jangan tangkap klik dari tombol aksi admin */
                if (e.target.closest('[data-aksi]')) return;
                var el = e.target.closest('[data-lightbox], [data-lightbox-video]');
                if (!el) return;
                var src = el.getAttribute('data-lightbox') || el.getAttribute('data-lightbox-video') || el.getAttribute('src') || '';
                if (!src || src === 'true') return;
                e.preventDefault();
                e.stopPropagation();
                openLightbox(el);
            });
        } else {
            /* Global: bind pada document (dipanggil sekali saat DOMContentLoaded) */
            document.addEventListener('click', function (e) {
                /* Jangan tangkap klik dari tombol aksi admin */
                if (e.target.closest('[data-aksi]')) return;
                var el = e.target.closest('[data-lightbox], [data-lightbox-video]');
                if (!el) return;
                var src = el.getAttribute('data-lightbox') || el.getAttribute('data-lightbox-video') || el.getAttribute('src') || '';
                if (!src || src === 'true') return;
                e.preventDefault();
                openLightbox(el);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initSkeleton();
        initLightbox();
    });

    window.MediaHelpers = {
        initSkeleton: initSkeleton,
        initLightbox: initLightbox,
        openLightbox: openLightbox,
        closeLightbox: closeLightbox
    };
})();
