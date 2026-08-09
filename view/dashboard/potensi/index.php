<?php
declare(strict_types=1);

$judulHalaman = 'Manajemen Potensi Desa';

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 md:mb-section-gap gap-4 md:gap-0">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Potensi Desa</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0">Manajemen Potensi Desa</h1>
</div>
<a class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 group whitespace-nowrap" href="<?= APP_BASE ?>/dashboard/potensi/form">
<span class="material-symbols-outlined text-[20px] transition-transform group-hover:rotate-90">psychiatry</span>
Tambah Potensi
</a>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div id="potensi-table" class="relative z-10" data-endpoint="<?= APP_BASE ?>/dashboard/ajax/potensi/list">
<form id="potensi-filter" class="flex flex-col md:flex-row items-stretch md:items-center gap-3 mb-4 relative z-10" onsubmit="return false;">
<input class="flex-1 min-w-0 bg-surface-container-highest border border-glass-border rounded-xl py-3 pl-4 pr-4 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" name="q" data-live-search placeholder="Cari judul potensi..." type="text"/>
<select class="bg-surface-container-highest border border-glass-border rounded-xl py-3 px-4 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary transition-all" name="status" data-filter>
<option value="">Semua Status</option>
<option value="aktif">Aktif</option>
<option value="nonaktif">Nonaktif</option>
</select>
<button type="button" data-reset-filter class="shrink-0 px-4 py-3 rounded-xl border border-glass-border text-on-surface-variant hover:text-primary hover:border-primary/40 transition-colors text-caption font-caption flex items-center gap-1" title="Reset filter">
<span class="material-symbols-outlined text-[18px]">restart_alt</span> Reset
</button>
</form>
<div class="table-box relative">
<div class="overflow-x-auto">
<div class="flex items-center justify-between py-4 px-4 text-caption font-caption text-on-surface-variant border-b border-glass-border/50">
<span data-table-info>Memuat data...</span>
</div>
<table class="w-full text-left border-collapse min-w-[720px]">
<thead>
<tr class="text-label-mono font-label-mono text-on-surface-variant border-b border-glass-border/50">
<th class="py-4 px-4 font-medium">Judul Potensi</th>
<th class="py-4 px-4 font-medium">Kategori</th>
<th class="py-4 px-4 font-medium text-center">Urutan</th>
<th class="py-4 px-4 font-medium text-right">Status</th>
<th class="py-4 px-4 font-medium text-right">Aksi</th>
</tr>
</thead>
<tbody data-table-body class="text-body-md font-body-md text-on-surface"></tbody>
</table>
</div>
<div data-table-foot class="flex flex-col sm:flex-row items-center justify-between mt-6 pt-4 border-t border-glass-border/30 gap-4 sm:gap-0"></div>
</div>
</div>
</div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    AdminUI.initAjaxTable({
        name: 'potensi',
        container: '#potensi-table',
        onRender: function () { MediaHelpers.initSkeleton(); },
        actions: {
            delete: function (btn) {
                AdminUI.confirmModal('Hapus Potensi', btn.dataset.pesan || 'Yakin ingin menghapus data ini?', 'Hapus', function () {
                    AdminUI.ajax(btn.closest('[data-endpoint]').dataset.endpoint.replace('/list', '/delete'), { id: btn.dataset.id })
                        .then(function (res) {
                            if (res.ok) {
                                AdminUI.showToast('success', res.message);
                                AdminUI.loadTable('potensi');
                            } else {
                                AdminUI.showToast('error', res.message || 'Gagal menghapus data.');
                            }
                        })
                        .catch(function () {});
                });
            }
        }
    });
});
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>
