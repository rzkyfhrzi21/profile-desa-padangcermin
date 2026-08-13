/* Admin UI: toast, modal, sidebar toggle, tabel AJAX (POST + CSRF) */
/* v2: fix toast progress animation, sidebar FOUC sync, event listener dedup, skeleton timing */
(function () {
    'use strict';

    var csrfToken = '';
    var toastContainer = null;

    function ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    /* ============ CSRF ============ */
    function getCsrf() {
        if (!csrfToken) {
            var m = document.querySelector('meta[name="csrf-token"]');
            csrfToken = m ? m.getAttribute('content') : '';
        }
        return csrfToken;
    }

    /* ============ AJAX helper (POST FormData) ============ */
    function adminAjax(url, data) {
        var body = new FormData();
        if (data) {
            Object.keys(data).forEach(function (k) {
                body.append(k, data[k]);
            });
        }
        body.append('csrf_token', getCsrf());
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            body: body,
            headers: { 'X-CSRF-TOKEN': getCsrf() }
        }).then(function (res) {
            if (res.status === 401) {
                showToast('error', 'Sesi login Anda telah berakhir. Silakan login kembali.');
                setTimeout(function () {
                    window.location.href = window.location.origin + (window.APP_BASE || '') + '/auth/login';
                }, 2000);
                throw new Error('401');
            }
            if (!res.ok) {
                throw new Error('HTTP ' + res.status);
            }
            return res.json();
        });
    }

    /* ============ Toast ============ */
    function ensureToastContainer() {
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-[100] flex flex-col gap-3 w-[min(92vw,380px)] pointer-events-none';
            document.body.appendChild(toastContainer);
        }
        return toastContainer;
    }

    function showToast(type, message) {
        var wrap = ensureToastContainer();
        var isError = type === 'error';
        var el = document.createElement('div');
        el.className = 'toast-enter pointer-events-auto relative overflow-hidden rounded-xl border p-4 pr-12 shadow-2xl backdrop-blur-xl ' +
            (isError
                ? 'bg-red-950/95 border-red-500/40 text-red-100'
                : 'bg-green-950/95 border-green-400/30 text-green-50');
        var icon = isError ? 'error' : 'check_circle';
        var title = isError ? 'Gagal' : 'Berhasil';

        var progressHtml = isError
            ? ''
            : '<div class="toast-progress absolute bottom-0 left-0 h-0.5 bg-green-400" style="width:100%"></div>';

        el.innerHTML =
            '<div class="flex items-start gap-3">' +
            '<span class="material-symbols-outlined shrink-0 ' + (isError ? 'text-red-400' : 'text-green-400') + '" style="font-variation-settings:\'FILL\' 1">' + icon + '</span>' +
            '<div class="flex flex-col gap-0.5 min-w-0">' +
            '<span class="font-bold text-sm">' + title + '</span>' +
            '<span class="text-sm break-words">' + message + '</span>' +
            '</div>' +
            '</div>' +
            '<button type="button" class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/10 transition-colors" aria-label="Tutup"><span class="material-symbols-outlined text-[18px]">close</span></button>' +
            progressHtml;

        var closeBtn = el.querySelector('button');
        var closeToast = function () {
            el.style.transition = 'opacity 0.2s, transform 0.2s';
            el.style.opacity = '0';
            el.style.transform = 'translateX(24px)';
            setTimeout(function () { el.remove(); }, 220);
        };
        closeBtn.addEventListener('click', closeToast);
        wrap.appendChild(el);

        if (!isError) {
            /* Animate progress bar using CSS transition */
            var bar = el.querySelector('.toast-progress');
            if (bar) {
                /* Trigger reflow before starting transition to ensure animation works */
                bar.getBoundingClientRect();
                bar.style.transition = 'width 2s linear';
                bar.style.width = '0%';
            }
            var timer = setTimeout(closeToast, 2000);
            closeBtn.addEventListener('click', function () { clearTimeout(timer); }, { once: true });
        }
    }

    function toastFromFlash(messages) {
        (messages || []).forEach(function (f) {
            showToast(f.tipe === 'error' ? 'error' : 'success', f.pesan);
        });
    }

    /* ============ Modal ============ */
    function openModal(html, opts) {
        opts = opts || {};
        var existing = document.querySelector('.admin-modal');
        if (existing) existing.remove();
        var overlay = document.createElement('div');
        overlay.className = 'admin-modal modal-overlay fixed inset-0 z-[210] flex items-start md:items-center justify-center p-4 md:p-6 bg-black/75 backdrop-blur-sm overflow-y-auto';
        var panel = document.createElement('div');
        panel.className = 'modal-panel relative w-full z-[211] ' + (opts.large ? 'max-w-4xl' : 'max-w-lg') + ' bg-surface-container-high border border-glass-border rounded-2xl shadow-2xl my-auto';
        panel.innerHTML = html;
        overlay.appendChild(panel);
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        overlay._trigger = document.activeElement;

        /* Bind all [data-modal-close] buttons */
        panel.querySelectorAll('[data-modal-close]').forEach(function (btn) {
            btn.addEventListener('click', function () { closeModal(); });
        });

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });
        function esc(e) {
            if (e.key === 'Escape') {
                /* Lightbox di atas modal menangani ESC sendiri — jangan tutup modal */
                if (document.querySelector('.lightbox-overlay')) return;
                closeModal();
            }
        }
        document.addEventListener('keydown', esc);
        overlay._esc = esc;
        /* Jangan tutup modal saat lightbox di atasnya masih terbuka */
        document.addEventListener('keydown', escOverlayGuard);
        overlay._escGuard = escOverlayGuard;

        /* Focus trap: first focusable element */
        var focusable = panel.querySelectorAll('button, input, select, textarea, a[href], [tabindex]:not([tabindex="-1"])');
        if (focusable.length) focusable[0].focus();

        /* onOpen callback — called after modal is in DOM */
        if (opts.onOpen) {
            try { opts.onOpen(panel); } catch (e2) { /* ignore */ }
        }

        return overlay;
    }

    function closeModal() {
        var overlay = document.querySelector('.admin-modal');
        if (!overlay) return;
        document.removeEventListener('keydown', overlay._esc);
        var trigger = overlay._trigger || null;
        overlay.remove();
        document.body.style.overflow = '';
        if (trigger && trigger.focus) trigger.focus();
    }

    function confirmModal(title, message, okLabel, onOk) {
        var overlay = openModal(
            '<div class="p-6">' +
            '<div class="flex items-start justify-between mb-4">' +
            '<h3 class="font-bold text-lg text-on-surface">' + title + '</h3>' +
            '<button type="button" data-modal-close class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/10 transition-colors text-on-surface-variant" aria-label="Tutup"><span class="material-symbols-outlined">close</span></button>' +
            '</div>' +
            '<p class="text-sm text-on-surface-variant mb-6">' + message + '</p>' +
            '<div class="flex justify-end gap-3">' +
            '<button type="button" data-modal-close class="px-5 py-2.5 rounded-full border border-glass-border text-on-surface font-bold text-sm hover:bg-surface-container transition-colors">Batal</button>' +
            '<button type="button" id="confirm-ok" class="px-5 py-2.5 rounded-full bg-red-600/90 border border-red-500/40 text-white font-bold text-sm hover:bg-red-600 transition-all">' + (okLabel || 'Hapus') + '</button>' +
            '</div>' +
            '</div>'
        );
        overlay.querySelector('#confirm-ok').addEventListener('click', function () {
            closeModal();
            if (onOk) onOk();
        });
    }

    function detailModal(html, onOpenFn) {
        openModal(
            '<div class="relative">' +
            '<button type="button" data-modal-close class="absolute top-3 right-3 w-10 h-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-white/15 transition-colors text-on-surface z-10" aria-label="Tutup"><span class="material-symbols-outlined">close</span></button>' +
            '<div class="p-6">' + html + '</div>' +
            '</div>',
            {
                large: true,
                onOpen: function (panel) {
                    /* Re-init skeleton & lightbox for media in modal */
                    if (window.MediaHelpers) {
                        if (MediaHelpers.initSkeleton) MediaHelpers.initSkeleton(panel);
                        if (MediaHelpers.initLightbox) MediaHelpers.initLightbox(panel);
                    }
                    if (onOpenFn) onOpenFn(panel);
                }
            }
        );
    }

    /* ============ Sidebar ============ */
    /* Sidebar logic (toggle, collapse, mobile drawer) sudah dihandle
       sepenuhnya di layout_close.php via inline style.
       Fungsi ini dibiarkan kosong agar tidak konflik. */
    function initSidebar() {
        /* noop — sidebar JS ada di layout_close.php */
    }

    /* ============ AJAX Table ============ */
    var tables = {};

    function initAjaxTable(cfg) {
        var container = document.querySelector(cfg.container);
        if (!container) return;
        var table = {
            cfg: cfg,
            container: container,
            form: (cfg.form || container.dataset.form)
                ? document.querySelector(cfg.form || container.dataset.form)
                : null,
            page: 1,
            loading: false
        };
        tables[cfg.name] = table;

        /* Bind filter/search events (once only) */
        bindFilterEvents(table);

        /* Bind action buttons via event delegation (once only per container) */
        if (cfg.actions && !container._actionsBound) {
            container._actionsBound = true;
            container.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-aksi]');
                if (!btn) return;
                var aksi = btn.getAttribute('data-aksi');
                var fn = cfg.actions[aksi];
                if (fn) fn(btn, table);
            });
        }

        /* Bind pagination via event delegation (once only) */
        if (!container._paginationBound) {
            container._paginationBound = true;
            container.addEventListener('click', function (e) {
                var pageBtn = e.target.closest('[data-page]');
                if (pageBtn && !pageBtn.disabled) {
                    var pg = parseInt(pageBtn.getAttribute('data-page'), 10);
                    if (!isNaN(pg)) {
                        table.page = pg;
                        loadTable(table);
                    }
                }
                var resetBtn = e.target.closest('[data-reset-filter]');
                if (resetBtn) {
                    resetFilters(table);
                }
            });
        }

        loadTable(table);
    }

    function resetFilters(table) {
        var scope = table.form || table.container;
        scope.querySelectorAll('input[name], select[name]').forEach(function (el) {
            if (el.type === 'checkbox') {
                el.checked = false;
            } else {
                el.value = '';
            }
        });
        table.page = 1;
        loadTable(table);
    }

    function tableParams(table) {
        var params = { page: table.page };
        var scope = table.form || table.container;
        scope.querySelectorAll('input[name], select[name]').forEach(function (el) {
            if (el.getAttribute('data-skip') === '1') return;
            var v = el.value != null ? el.value.trim() : '';
            if (v !== '' && v !== '0') {
                params[el.name] = v;
            }
        });
        return params;
    }

    function loadTable(table) {
        if (table.loading) return;
        table.loading = true;
        var box = table.container.querySelector('.table-box');
        var spinnerEl = null;
        if (box) {
            spinnerEl = document.createElement('div');
            spinnerEl.className = 'table-spinner absolute inset-0 z-20 flex items-center justify-center bg-black/30 backdrop-blur-[1px] rounded-xl';
            spinnerEl.innerHTML = '<span class="lazy-spinner"></span>';
            box.style.position = 'relative';
            box.appendChild(spinnerEl);
        }
        var endpoint = table.cfg.endpoint || table.container.dataset.endpoint || '';
        adminAjax(endpoint, tableParams(table)).then(function (res) {
            table.loading = false;
            if (spinnerEl) spinnerEl.remove();
            if (!res.ok) {
                showToast('error', res.message || 'Gagal memuat data.');
                return;
            }
            var body = table.container.querySelector('[data-table-body]');
            var foot = table.container.querySelector('[data-table-foot]');
            var info = table.container.querySelector('[data-table-info]');
            if (body) body.innerHTML = Array.isArray(res.rows) ? res.rows.join('') : (res.rows || '');
            if (foot) foot.innerHTML = res.pagination;
            if (info) info.textContent = res.total_html || ('Total ' + res.total + ' data');
            /* Re-init skeleton for newly rendered thumbnails */
            if (window.MediaHelpers && window.MediaHelpers.initSkeleton) {
                window.MediaHelpers.initSkeleton(table.container);
            }
            if (table.cfg.onRender) table.cfg.onRender(table.container);
        }).catch(function (e) {
            table.loading = false;
            if (spinnerEl) spinnerEl.remove();
            if (e.message === '401') return;
            showToast('error', 'Gagal terhubung ke server atau terjadi kesalahan internal. Periksa koneksi internet Anda dan coba lagi.');
        });
    }

    function bindFilterEvents(table) {
        var scope = table.form || table.container;

        /* Live search — debounce 300ms */
        scope.querySelectorAll('[data-live-search]').forEach(function (input) {
            if (input._searchBound) return;
            input._searchBound = true;
            var timer = null;
            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    table.page = 1;
                    loadTable(table);
                }, 300);
            });
        });

        /* Filter selects — immediate */
        scope.querySelectorAll('[data-filter]').forEach(function (el) {
            if (el._filterBound) return;
            el._filterBound = true;
            el.addEventListener('change', function () {
                table.page = 1;
                loadTable(table);
            });
        });
    }

    /* ============ AJAX Form (data-ajax) ============ */
    function bindForms() {
        document.querySelectorAll('form[data-ajax]').forEach(function (form) {
            if (form._bound) return;
            form._bound = true;
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var btn = form.querySelector('[type="submit"]');
                if (btn) { btn.disabled = true; }
                var fd = new FormData(form);
                fd.append('csrf_token', getCsrf());
                fetch(form.getAttribute('action') || window.location.href, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: fd,
                    headers: { 'X-CSRF-TOKEN': getCsrf() }
                }).then(function (res) {
                    return res.json();
                }).then(function (res) {
                    if (btn) btn.disabled = false;
                    if (res.ok) {
                        showToast('success', res.message || 'Berhasil.');
                        if (form.dataset.redirect) {
                            setTimeout(function () {
                                window.location.href = form.dataset.redirect;
                            }, 1200);
                        } else if (form.dataset.reload === '1') {
                            setTimeout(function () {
                                window.location.reload();
                            }, 1200);
                        }
                    } else {
                        showToast('error', res.message || 'Terjadi kesalahan.');
                    }
                }).catch(function () {
                    if (btn) btn.disabled = false;
                    showToast('error', 'Gagal terhubung ke server atau terjadi kesalahan internal. Periksa koneksi internet Anda dan coba lagi.');
                });
            });
        });
    }

    /* ============ Public API ============ */
    window.AdminUI = {
        showToast: showToast,
        toastFromFlash: toastFromFlash,
        openModal: openModal,
        closeModal: closeModal,
        confirmModal: confirmModal,
        detailModal: detailModal,
        ajax: adminAjax,
        initAjaxTable: initAjaxTable,
        loadTable: function (name) {
            if (tables[name]) loadTable(tables[name]);
        }
    };

    /* ============ Init ============ */
    ready(function () {
        initSidebar();
        bindForms();
        var flashData = document.getElementById('flash-data');
        if (flashData && flashData.textContent.trim()) {
            try {
                toastFromFlash(JSON.parse(flashData.textContent));
            } catch (e) { /* ignore */ }
        }
    });
})();
