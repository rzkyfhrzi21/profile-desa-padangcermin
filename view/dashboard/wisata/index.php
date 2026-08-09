<?php
declare(strict_types=1);

$judulHalaman = 'Manajemen Wisata';

/* Stats */
$db = getDb();
$statWisata = $db->query("
    SELECT
        COUNT(*) AS total,
        SUM(status = 'publish') AS published,
        SUM(status = 'draft') AS draft
    FROM wisata_desa
")->fetch();
$totalGambar = (int) $db->query("SELECT COUNT(*) FROM wisata_gambar")->fetchColumn();

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-6 gap-4">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Potensi Desa</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0">Manajemen Wisata</h1>
</div>
<a class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 group whitespace-nowrap" href="<?= APP_BASE ?>/dashboard/wisata/form">
<span class="material-symbols-outlined text-[20px] transition-transform group-hover:rotate-90">add_location_alt</span>
Tambah Wisata
</a>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-surface-container rounded-2xl p-4 flex items-center gap-3 border border-glass-border/40">
    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
      <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings:'FILL' 1">landscape</span>
    </div>
    <div><p class="text-[11px] text-on-surface-variant uppercase tracking-wide">Total Wisata</p><p class="text-[22px] font-bold font-mono text-on-surface leading-none"><?= (int) $statWisata['total'] ?></p></div>
  </div>
  <div class="bg-surface-container rounded-2xl p-4 flex items-center gap-3 border border-glass-border/40">
    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
      <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings:'FILL' 1">public</span>
    </div>
    <div><p class="text-[11px] text-on-surface-variant uppercase tracking-wide">Published</p><p class="text-[22px] font-bold font-mono text-on-surface leading-none"><?= (int) $statWisata['published'] ?></p></div>
  </div>
  <div class="bg-surface-container rounded-2xl p-4 flex items-center gap-3 border border-glass-border/40">
    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
      <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings:'FILL' 1">edit_document</span>
    </div>
    <div><p class="text-[11px] text-on-surface-variant uppercase tracking-wide">Draft</p><p class="text-[22px] font-bold font-mono text-on-surface leading-none"><?= (int) $statWisata['draft'] ?></p></div>
  </div>
  <div class="bg-surface-container rounded-2xl p-4 flex items-center gap-3 border border-glass-border/40">
    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
      <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings:'FILL' 1">photo_library</span>
    </div>
    <div><p class="text-[11px] text-on-surface-variant uppercase tracking-wide">Total Foto</p><p class="text-[22px] font-bold font-mono text-on-surface leading-none"><?= $totalGambar ?></p></div>
  </div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>

<!-- Container AJAX: data-endpoint dipakai oleh loadTable -->
<div id="wisata-table" class="relative z-10" data-endpoint="<?= APP_BASE ?>/dashboard/ajax/wisata/list">

<!-- Search + Tabs -->
<div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 mb-5">
<div class="relative flex-1 min-w-0">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl py-3 pl-10 pr-4 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" name="q" data-live-search placeholder="Cari nama wisata..." type="text"/>
</div>
<div class="flex gap-2 flex-wrap">
<button type="button" class="wisata-tab-btn px-4 py-2.5 rounded-xl text-caption font-caption border transition-all bg-primary/10 text-primary border-primary/40" data-status="">Semua</button>
<button type="button" class="wisata-tab-btn px-4 py-2.5 rounded-xl text-caption font-caption border transition-all border-glass-border text-on-surface-variant hover:border-primary/40 hover:text-primary" data-status="publish">Published</button>
<button type="button" class="wisata-tab-btn px-4 py-2.5 rounded-xl text-caption font-caption border transition-all border-glass-border text-on-surface-variant hover:border-primary/40 hover:text-primary" data-status="draft">Draft</button>
<input type="hidden" name="status" data-filter value="">
</div>
<button type="button" data-reset-filter class="shrink-0 px-4 py-3 rounded-xl border border-glass-border text-on-surface-variant hover:text-primary hover:border-primary/40 transition-colors text-caption font-caption flex items-center gap-1" title="Reset filter">
<span class="material-symbols-outlined text-[18px]">restart_alt</span> Reset
</button>
</div>

<!-- Info baris -->
<div class="flex items-center py-2 mb-3 text-caption font-caption text-on-surface-variant">
<span data-table-info>Memuat data...</span>
</div>

<!-- Card Grid — data-table-body = div, bukan tbody -->
<div data-table-body class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 min-h-[200px]"></div>

<!-- Pagination -->
<div data-table-foot class="flex flex-col sm:flex-row items-center justify-between mt-6 pt-4 border-t border-glass-border/30 gap-4 sm:gap-0"></div>

</div><!-- /wisata-table -->
</div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var AJAX_BASE = '<?= APP_BASE ?>/dashboard/ajax/wisata';

    /* ---- Tab switching ---- */
    document.querySelectorAll('.wisata-tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.wisata-tab-btn').forEach(function (b) {
                b.classList.remove('bg-primary/10', 'text-primary', 'border-primary/40');
                b.classList.add('border-glass-border', 'text-on-surface-variant');
            });
            this.classList.add('bg-primary/10', 'text-primary', 'border-primary/40');
            this.classList.remove('border-glass-border', 'text-on-surface-variant');
            var statusInput = document.querySelector('#wisata-table [name="status"][data-filter]');
            if (statusInput) {
                statusInput.value = this.dataset.status || '';
                statusInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });

    /* ---- Init AJAX table ---- */
    AdminUI.initAjaxTable({
        name: 'wisata',
        container: '#wisata-table',
        onRender: function () {
            var body = document.querySelector('#wisata-table [data-table-body]');
            if (window.MediaHelpers) {
                if (MediaHelpers.initSkeleton) MediaHelpers.initSkeleton(body);
            }
        },
        actions: {
            delete: function (btn) {
                var id = btn.dataset.id;
                var pesan = btn.dataset.pesan || 'Yakin ingin menghapus wisata ini beserta galerinya?';
                AdminUI.confirmModal('Hapus Wisata', pesan, 'Hapus', function () {
                    AdminUI.ajax(AJAX_BASE + '/delete', { id: id })
                        .then(function (res) {
                            if (res.ok) {
                                AdminUI.showToast('success', res.message);
                                AdminUI.loadTable('wisata');
                            } else {
                                AdminUI.showToast('error', res.message || 'Gagal menghapus data.');
                            }
                        })
                        .catch(function () {
                            AdminUI.showToast('error', 'Gagal terhubung ke server.');
                        });
                });
            }
        }
    });
});
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>
