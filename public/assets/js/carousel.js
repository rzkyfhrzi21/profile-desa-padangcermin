/* carousel.js — auto-slide 1.5s, prev/next, pause-on-hover, swipe touch, dots */
/* v2: fix auto-slide timer reset on prev/next click */
(function () {
    'use strict';

    function initCarousel(root) {
        if (root.dataset.carouselInit) return;
        root.dataset.carouselInit = '1';

        var slides = Array.prototype.slice.call(root.querySelectorAll('.carousel-slide'));
        if (slides.length <= 1) {
            /* Only 1 slide: show it immediately, no auto-slide needed */
            if (slides.length === 1) slides[0].classList.add('carousel-active');
            return;
        }

        var dotsWrap = root.querySelector('.carousel-dots');
        var interval = parseInt(root.dataset.carouselInterval || '1500', 10);
        var index = 0;
        var timer = null;
        var paused = false;

        function render(i) {
            index = ((i % slides.length) + slides.length) % slides.length;
            slides.forEach(function (s, n) {
                s.classList.toggle('carousel-active', n === index);
            });
            if (dotsWrap) {
                var dots = dotsWrap.querySelectorAll('[data-carousel-dot]');
                Array.prototype.forEach.call(dots, function (d, n) {
                    d.classList.toggle('carousel-dot-active', n === index);
                });
            }
        }

        function resetTimer() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
            if (!paused && interval > 0) {
                timer = setInterval(function () { render(index + 1); }, interval);
            }
        }

        function pause() {
            paused = true;
            if (timer) { clearInterval(timer); timer = null; }
        }

        function resume() {
            paused = false;
            resetTimer();
        }

        /* Prev / Next buttons */
        var prevBtn = root.querySelector('[data-carousel-prev]');
        var nextBtn = root.querySelector('[data-carousel-next]');
        if (prevBtn) {
            prevBtn.addEventListener('click', function (e) {
                e.preventDefault();
                render(index - 1);
                resetTimer(); /* Reset timer so user action doesn't overlap auto-slide */
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', function (e) {
                e.preventDefault();
                render(index + 1);
                resetTimer();
            });
        }

        /* Dot navigation */
        if (dotsWrap) {
            var allDots = dotsWrap.querySelectorAll('[data-carousel-dot]');
            Array.prototype.forEach.call(allDots, function (d, n) {
                d.addEventListener('click', function () {
                    render(n);
                    resetTimer();
                });
            });
        }

        /* Pause on hover / focus */
        root.addEventListener('mouseenter', pause);
        root.addEventListener('mouseleave', resume);
        root.addEventListener('focusin', pause);
        root.addEventListener('focusout', resume);

        /* Touch swipe */
        var startX = null;
        root.addEventListener('touchstart', function (e) {
            startX = e.changedTouches[0].clientX;
        }, { passive: true });
        root.addEventListener('touchend', function (e) {
            if (startX === null) return;
            var dx = e.changedTouches[0].clientX - startX;
            if (Math.abs(dx) > 40) {
                if (dx < 0) render(index + 1);
                else render(index - 1);
                resetTimer();
            }
            startX = null;
        }, { passive: true });

        /* Pause when tab is hidden */
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) pause();
            else resume();
        });

        /* Init first slide */
        render(0);

        /* Init skeleton for images in carousel */
        if (window.MediaHelpers && window.MediaHelpers.initSkeleton) {
            window.MediaHelpers.initSkeleton(root);
        }

        resetTimer();
    }

    function initAll() {
        Array.prototype.forEach.call(document.querySelectorAll('[data-carousel]'), initCarousel);
    }

    window.CarouselHelpers = { init: initCarousel, initAll: initAll };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
