<?php
declare(strict_types=1);

$judulHalaman = 'Form Kependudukan';
$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$dataKep = $editId > 0 ? getDataKependudukanById($editId) : null;
if ($editId > 0 && $dataKep === null) {
    flash('error', 'Data tidak ditemukan.');
    redirect('/dashboard/kependudukan');
}

/* ----------------------------------------------------------------
   POST: Hanya simpan header (periode + keterangan).
   Angka agregat di-hitung ulang dari kependudukan_dusun oleh server.
----------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    $aksi     = trim((string) ($_POST['aksi'] ?? 'save'));
    $periode  = trim((string) ($_POST['periode'] ?? ''));
    $keterangan = trim((string) ($_POST['keterangan'] ?? ''));

    /* Validasi periode format YYYY-MM */
    $errors = [];
    if ($periode === '') {
        $errors[] = 'Periode wajib diisi.';
    } elseif (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode)) {
        $errors[] = 'Format periode harus YYYY-MM (contoh: 2026-04).';
    }

    if ($errors !== []) {
        foreach ($errors as $err) { flash('error', $err); }
        redirect('/dashboard/kependudukan/form' . ($editId > 0 ? '?id=' . $editId : ''));
    }

    /* Hitung agregat dari dusun */
    $db    = getDb();
    $stmt  = $db->prepare('SELECT COALESCE(SUM(jumlah_kk),0) kk, COALESCE(SUM(jumlah_jiwa),0) jiwa, COALESCE(SUM(jumlah_laki),0) laki, COALESCE(SUM(jumlah_perempuan),0) perempuan FROM kependudukan_dusun WHERE periode = ?');
    $stmt->execute([$periode]);
    $agg = $stmt->fetch();

    $dataBaru = [
        'periode'           => $periode,
        'jumlah_kk'         => (int) $agg['kk'],
        'jumlah_jiwa'       => (int) $agg['jiwa'],
        'jumlah_laki'       => (int) $agg['laki'],
        'jumlah_perempuan'  => (int) $agg['perempuan'],
        'keterangan'        => $keterangan,
    ];

    try {
        if ($editId > 0) {
            if (updateKependudukan($editId, $dataBaru)) {
                catatLog('edit data kependudukan: ' . $periode, 'data_kependudukan', $editId);
                flash('success', 'Data periode ' . $periode . ' berhasil diperbarui.');
            } else {
                flash('error', 'Gagal memperbarui data kependudukan.');
            }
        } else {
            $newId = saveKependudukan($dataBaru);
            if ($newId > 0) {
                catatLog('tambah data kependudukan: ' . $periode, 'data_kependudukan', $newId);
                flash('success', 'Data periode ' . $periode . ' berhasil disimpan.');
            } else {
                flash('error', 'Gagal menyimpan data kependudukan.');
            }
        }
    } catch (Throwable $t) {
        flash('error', 'Gagal menyimpan data. Periode "' . $periode . '" mungkin sudah terpakai.');
    }
    redirect('/dashboard/kependudukan');
}

/* Periode awal (edit) */
$periodeAwal = $dataKep['periode'] ?? '';
$bulanAwal   = '';
$tahunAwal   = '';
if ($periodeAwal !== '') {
    $parts = explode('-', $periodeAwal);
    $tahunAwal = $parts[0] ?? '';
    $bulanAwal = $parts[1] ?? '';
}

$months = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
    '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
    '10' => 'Oktober', '11' => 'November',  '12' => 'Desember',
];

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 md:mb-section-gap gap-4 md:gap-0">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Data Demografi</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-coklat m-0"><?= $editId > 0 ? 'Edit' : 'Tambah' ?> Data Kependudukan</h1>
<p class="text-body-md font-body-md text-abu m-0">Input data per dusun — total agregat dihitung otomatis.</p>
</div>
<a class="text-caption font-caption text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="<?= APP_BASE ?>/dashboard/kependudukan">
<span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke daftar
</a>
</div>

<div class="grid grid-cols-12 gap-gutter">

<!-- KOLOM KIRI: Periode + data per dusun -->
<div class="col-span-12 xl:col-span-8 flex flex-col gap-stack-lg">

<!-- Card Periode -->
<div class="bg-white rounded-[20px] border border-black/8 p-4 md:p-stack-lg relative overflow-hidden shadow-sm">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="flex items-center gap-3 relative z-10 mb-4">
<div class="w-10 h-10 rounded-xl bg-admin-bg border border-black/8 flex items-center justify-center text-hijau">
<span class="material-symbols-outlined">calendar_month</span>
</div>
<h2 class="text-headline-md font-headline-md text-coklat m-0">Periode Data</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md relative z-10">
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-abu uppercase tracking-widest text-[12px]" for="bulan">Bulan</label>
<select class="w-full bg-admin-bg border border-black/10 rounded-xl px-4 py-3 text-body-md font-body-md text-coklat focus:outline-none focus:border-hijau transition-all" id="bulan">
<option value="">-- Pilih Bulan --</option>
<?php foreach ($months as $k => $m): ?>
<option value="<?= $k ?>" <?= $bulanAwal === $k ? 'selected' : '' ?>><?= $m ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-abu uppercase tracking-widest text-[12px]" for="tahun">Tahun</label>
<input class="w-full bg-admin-bg border border-black/10 rounded-xl px-4 py-3 text-body-md font-body-md text-coklat focus:outline-none focus:border-hijau transition-all" id="tahun" placeholder="2026" type="number" min="2000" max="2100" value="<?= e($tahunAwal) ?>"/>
</div>
</div>
<p class="text-caption font-caption text-abu mt-3 m-0 relative z-10">Format YYYY-MM. Satu periode = satu baris data unik.</p>
</div>

<!-- Card Data Per Dusun -->
<div class="bg-white rounded-[20px] border border-black/8 p-4 md:p-stack-lg relative overflow-hidden shadow-sm" id="dusun-section" style="display:none">
<div class="flex items-center justify-between relative z-10 mb-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-xl bg-admin-bg border border-black/8 flex items-center justify-center text-hijau">
<span class="material-symbols-outlined">holiday_village</span>
</div>
<div>
<h2 class="text-headline-md font-headline-md text-coklat m-0">Data Per Dusun</h2>
<p class="text-caption font-caption text-abu m-0" id="periode-label"></p>
</div>
</div>
</div>
<!-- Tabel Dusun -->
<div class="overflow-x-auto relative z-10">
<table class="w-full text-left border-collapse min-w-[620px]">
<thead>
<tr class="text-label-mono font-label-mono text-abu border-b border-black/8">
<th class="py-3 px-3 font-medium">Nama Dusun</th>
<th class="py-3 px-3 font-medium text-right">Laki-laki</th>
<th class="py-3 px-3 font-medium text-right">Perempuan</th>
<th class="py-3 px-3 font-medium text-right">KK</th>
<th class="py-3 px-3 font-medium text-right">Jiwa</th>
</tr>
</thead>
<tbody id="dusun-tbody">
<tr><td colspan="5" class="text-center py-6 text-on-surface-variant">Pilih periode untuk memuat data dusun...</td></tr>
</tbody>
</table>
</div>
<!-- Tombol Simpan Semua -->
<div class="mt-3 flex justify-end relative z-10">
<button type="button" id="btn-save-all-dusun" class="bg-primary text-on-primary px-5 py-2.5 rounded-xl font-caption text-caption hover:shadow-lime-glow transition-all flex items-center gap-2 disabled:opacity-50">
<span class="material-symbols-outlined text-[18px]">save</span> Simpan Semua Dusun
</button>
</div>
<p class="text-[11px] text-abu mt-2 relative z-10">Edit nilai langsung di tabel → klik <strong>Simpan Semua Dusun</strong> untuk menyimpan seluruh data sekaligus.</p>
</div>

<!-- Card Kelola Daftar Dusun -->
<div class="bg-white rounded-[20px] border border-black/8 p-4 md:p-stack-lg relative overflow-hidden shadow-sm">
<div class="flex items-center gap-3 relative z-10 mb-4">
<div class="w-10 h-10 rounded-xl bg-admin-bg border border-black/8 flex items-center justify-center text-hijau">
<span class="material-symbols-outlined">manage_accounts</span>
</div>
<div>
<h2 class="text-headline-md font-headline-md text-coklat m-0">Kelola Daftar Dusun</h2>
<p class="text-caption font-caption text-abu m-0">Tambah atau hapus nama dusun secara permanen</p>
</div>
</div>
<div class="overflow-x-auto relative z-10 mb-4">
<table class="w-full text-left border-collapse min-w-[300px] text-body-md font-body-md">
<thead>
<tr class="text-label-mono font-label-mono text-abu border-b border-black/8">
<th class="py-2 px-3 font-medium w-8">No</th>
<th class="py-2 px-3 font-medium">Nama Dusun</th>
<th class="py-2 px-3 font-medium text-center w-16">Urutan</th>
<th class="py-2 px-3 font-medium text-right w-12">Hapus</th>
</tr>
</thead>
<tbody id="master-tbody">
<tr><td colspan="4" class="text-center py-4 text-on-surface-variant text-caption">Memuat...</td></tr>
</tbody>
</table>
</div>
<form id="form-master-dusun" class="flex gap-3 items-end flex-wrap relative z-10" autocomplete="off">
<div class="flex flex-col gap-1 flex-1 min-w-[160px]">
<label class="text-[11px] text-abu uppercase tracking-wide">Nama Dusun Baru</label>
<input type="text" id="master-nama" required maxlength="100" placeholder="Dusun Baru" class="w-full bg-admin-bg border border-black/10 rounded-lg px-3 py-2.5 text-body-md text-coklat focus:border-hijau focus:outline-none transition-all"/>
</div>
<button type="submit" class="shrink-0 bg-primary text-on-primary px-5 py-2.5 rounded-xl font-caption text-caption hover:shadow-lime-glow transition-all flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]">add</span> Tambah Dusun
</button>
</form>
</div>

</div>

<!-- KOLOM KANAN: Total agregat (readonly) + simpan -->
<div class="col-span-12 xl:col-span-4 flex flex-col gap-stack-lg">

<!-- Card Ringkasan Agregat (READONLY) -->
<div class="bg-white rounded-[20px] border border-black/8 bg-hijau/5 p-4 md:p-stack-lg relative overflow-hidden shadow-sm">
<div class="absolute -left-8 -top-8 w-32 h-32 bg-primary/15 rounded-full blur-2xl"></div>
<div class="flex items-center gap-3 relative z-10 mb-4">
<div class="w-10 h-10 rounded-xl bg-primary/20 border border-primary/30 flex items-center justify-center">
<span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">bar_chart</span>
</div>
<div>
<h2 class="text-headline-md font-headline-md text-coklat m-0">Ringkasan Agregat</h2>
<p class="text-[11px] text-hijau m-0">Dihitung otomatis dari data dusun</p>
</div>
</div>
<div class="grid grid-cols-2 gap-3 relative z-10">
<div class="bg-admin-bg rounded-xl p-3 border border-black/8">
<p class="text-[11px] text-abu uppercase tracking-wide mb-1">Jumlah KK</p>
<p class="text-[28px] font-bold font-mono text-coklat leading-none" id="preview-kk">0</p>
</div>
<div class="bg-admin-bg rounded-xl p-3 border border-black/8">
<p class="text-[11px] text-abu uppercase tracking-wide mb-1">Total Jiwa</p>
<p class="text-[28px] font-bold font-mono text-primary leading-none" id="preview-jiwa">0</p>
</div>
<div class="bg-admin-bg rounded-xl p-3 border border-black/8">
<p class="text-[11px] text-abu uppercase tracking-wide mb-1">Laki-laki</p>
<p class="text-[28px] font-bold font-mono text-coklat leading-none" id="preview-laki">0</p>
</div>
<div class="bg-admin-bg rounded-xl p-3 border border-black/8">
<p class="text-[11px] text-abu uppercase tracking-wide mb-1">Perempuan</p>
<p class="text-[28px] font-bold font-mono text-coklat leading-none" id="preview-perempuan">0</p>
</div>
</div>
<p class="text-[11px] text-abu mt-3 relative z-10">Total di atas dihitung ulang dari tabel data per dusun saat menyimpan.</p>
</div>

<!-- Form Simpan Periode (hidden fields) -->
<form method="post" action="<?= APP_BASE ?>/dashboard/kependudukan/form<?= $editId > 0 ? '?id=' . $editId : '' ?>" id="form-periode">
<?= csrfField() ?>
<input type="hidden" name="aksi" value="save"/>
<input type="hidden" id="periode-hidden" name="periode" required/>
<div class="bg-white rounded-[20px] border border-black/8 p-4 md:p-stack-lg flex flex-col gap-3 shadow-sm">
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-abu uppercase tracking-widest text-[12px]" for="keterangan">Keterangan <span class="text-abu/50 normal-case">(opsional)</span></label>
<textarea class="w-full min-h-[80px] bg-admin-bg border border-black/10 rounded-xl px-4 py-3 text-body-md font-body-md text-coklat focus:outline-none focus:border-hijau transition-all placeholder:text-abu/50 resize-none" id="keterangan" name="keterangan" placeholder="Contoh: Data hasil pemutakhiran akhir tahun..."><?= e(trim((string) ($dataKep['keterangan'] ?? ''))) ?></textarea>
</div>
<div class="flex items-center justify-between pt-2 border-t border-black/8">
<div class="flex flex-col gap-0.5">
<span class="text-caption font-caption text-abu">Data sudah sesuai?</span>
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest text-[11px]" id="label-periode-simpan"><?= $editId > 0 ? e($dataKep['periode']) : 'Belum dipilih' ?></span>
</div>
<button class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 whitespace-nowrap" type="submit" id="btn-simpan">
<span class="material-symbols-outlined text-[20px]">check</span>
<?= $editId > 0 ? 'Simpan Perubahan' : 'Simpan Data' ?>
</button>
</div>
</div>
</form>

</div>
</div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var BASE       = '<?= APP_BASE ?>/dashboard/ajax/kependudukan';
    var elBulan    = document.getElementById('bulan');
    var elTahun    = document.getElementById('tahun');
    var elPeriode  = document.getElementById('periode-hidden');
    var elDusunSection  = document.getElementById('dusun-section');
    var elDusunTbody    = document.getElementById('dusun-tbody');
    var elPeriodeLabel  = document.getElementById('periode-label');
    var elLabelSimpan   = document.getElementById('label-periode-simpan');
    var elMasterTbody   = document.getElementById('master-tbody');

    /* ── Periode init (edit mode) ── */
    <?php if ($periodeAwal !== ''): ?>
    elPeriode.value = '<?= e($periodeAwal) ?>';
    setLabelPeriode('<?= e($periodeAwal) ?>');
    loadDusun();
    <?php endif; ?>

    /* ── Helpers ── */
    function getPeriode() {
        var b = elBulan.value, t = (elTahun.value || '').trim();
        return (b && t.length === 4) ? t + '-' + b : '';
    }
    function setLabelPeriode(p) {
        if (elPeriodeLabel) elPeriodeLabel.textContent = p ? 'Periode: ' + p : '';
        if (elLabelSimpan)  elLabelSimpan.textContent  = p || 'Belum dipilih';
    }
    function onPeriodeChange() {
        var p = getPeriode();
        elPeriode.value = p;
        setLabelPeriode(p);
        if (p) loadDusun(); else elDusunSection.style.display = 'none';
    }
    elBulan.addEventListener('change', onPeriodeChange);
    elTahun.addEventListener('input',  onPeriodeChange);

    /* ── Load dusun (semua dari master, inline input) ── */
    function loadDusun() {
        var p = elPeriode.value;
        if (!p) return;
        elDusunSection.style.display = '';
            elDusunTbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-on-surface-variant text-sm">Memuat data...</td></tr>';
        AdminUI.ajax(BASE + '/dusun-list', { periode: p })
            .then(function (res) {
                if (res.ok) {
                    elDusunTbody.innerHTML = res.html || '<tr><td colspan="5" class="text-center py-5 text-on-surface-variant text-sm">Belum ada data. Tambah dusun di bagian Kelola Daftar Dusun.</td></tr>';
                    updateAgregat(res.rows || []);
                } else {
                    elDusunTbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-on-surface-variant text-sm">Gagal memuat data.</td></tr>';
                }
            }).catch(function () {
                elDusunTbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-on-surface-variant text-sm">Gagal memuat data.</td></tr>';
            });
    }

    /* ── Update agregat dari rows ── */
    function updateAgregat(rows) {
        var kk = 0, jiwa = 0, laki = 0, per = 0;
        (rows || []).forEach(function (r) {
            kk   += parseInt(r.jumlah_kk) || 0;
            jiwa += parseInt(r.jumlah_jiwa) || 0;
            laki += parseInt(r.jumlah_laki) || 0;
            per  += parseInt(r.jumlah_perempuan) || 0;
        });
        document.getElementById('preview-kk').textContent        = kk.toLocaleString('id-ID');
        document.getElementById('preview-jiwa').textContent      = jiwa.toLocaleString('id-ID');
        document.getElementById('preview-laki').textContent      = laki.toLocaleString('id-ID');
        document.getElementById('preview-perempuan').textContent = per.toLocaleString('id-ID');
    }

    /* ── Hitung ulang agregat dari input yang ada di tabel ── */
    function recalcFromInputs() {
        var rows = elDusunTbody.querySelectorAll('tr');
        var kk = 0, jiwa = 0, laki = 0, per = 0;
        rows.forEach(function (tr) {
            var get = function(f) { var el = tr.querySelector('[data-field="' + f + '"]'); return el ? (parseInt(el.value) || 0) : 0; };
            laki += get('laki'); per += get('perempuan'); kk += get('kk'); jiwa += get('jiwa');
        });
        document.getElementById('preview-kk').textContent        = kk.toLocaleString('id-ID');
        document.getElementById('preview-jiwa').textContent      = jiwa.toLocaleString('id-ID');
        document.getElementById('preview-laki').textContent      = laki.toLocaleString('id-ID');
        document.getElementById('preview-perempuan').textContent = per.toLocaleString('id-ID');
    }

    /* ── Live recalc saat user mengetik di input tabel ── */
    elDusunTbody.addEventListener('input', function (e) {
        if (e.target.classList.contains('dusun-inp')) recalcFromInputs();
    });

    /* ── Simpan Semua Dusun (satu tombol) ── */
    var btnSaveAll = document.getElementById('btn-save-all-dusun');
    if (btnSaveAll) {
        btnSaveAll.addEventListener('click', function () {
            var p = elPeriode.value;
            if (!p) { AdminUI.showToast('error', 'Pilih periode terlebih dahulu.'); return; }
            var rows = elDusunTbody.querySelectorAll('tr[data-nama]');
            if (rows.length === 0) { AdminUI.showToast('error', 'Tidak ada data dusun untuk disimpan.'); return; }
            btnSaveAll.disabled = true;
            var promises = [];
            rows.forEach(function (tr) {
                var nama = tr.dataset.nama;
                var get  = function (f) { var el = tr.querySelector('[data-field="' + f + '"]'); return el ? (parseInt(el.value) || 0) : 0; };
                promises.push(AdminUI.ajax(BASE + '/dusun-save', { periode: p, nama_dusun: nama, jumlah_laki: get('laki'), jumlah_perempuan: get('perempuan'), jumlah_kk: get('kk'), jumlah_jiwa: get('jiwa') }));
            });
            Promise.all(promises)
                .then(function (results) {
                    var allOk = results.every(function (r) { return r.ok; });
                    AdminUI.showToast(allOk ? 'success' : 'error', allOk ? 'Semua data dusun berhasil disimpan.' : 'Beberapa data gagal disimpan.');
                    btnSaveAll.disabled = false;
                })
                .catch(function () {
                    AdminUI.showToast('error', 'Gagal menyimpan data dusun.');
                    btnSaveAll.disabled = false;
                });
        });
    }

    /* ── Form periode simpan: validasi ── */
    document.getElementById('form-periode').addEventListener('submit', function (e) {
        if (!elPeriode.value) {
            e.preventDefault();
            AdminUI.showToast('error', 'Pilih bulan dan tahun periode terlebih dahulu.');
            elBulan.focus();
        }
    });

    /* ═══════════════════════════════════════════
       KELOLA DAFTAR DUSUN (dusun_master)
    ═══════════════════════════════════════════ */
    function loadMaster() {
        if (!elMasterTbody) return;
        elMasterTbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-on-surface-variant text-caption">Memuat...</td></tr>';
        AdminUI.ajax(BASE + '/dusun-master-list', {})
            .then(function (res) {
                if (res.ok) {
                    elMasterTbody.innerHTML = res.html || '<tr><td colspan="4" class="text-center py-3 text-on-surface-variant text-caption">Belum ada dusun.</td></tr>';
                } else {
                    elMasterTbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-on-surface-variant text-caption">Gagal memuat.</td></tr>';
                }
            });
    }
    loadMaster();

    /* Tambah dusun master */
    var formMaster = document.getElementById('form-master-dusun');
    if (formMaster) {
        formMaster.addEventListener('submit', function (e) {
            e.preventDefault();
            var nama = document.getElementById('master-nama').value.trim();
            if (!nama) return;
            AdminUI.ajax(BASE + '/dusun-master-save', { nama: nama })
                .then(function (res) {
                    AdminUI.showToast(res.ok ? 'success' : 'error', res.message);
                    if (res.ok) { formMaster.reset(); loadMaster(); loadDusun(); }
                });
        });
    }

    /* Hapus dusun master (delegasi) */
    if (elMasterTbody) {
        elMasterTbody.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-master-delete]');
            if (!btn) return;
            AdminUI.confirmModal('Hapus Dusun Master', 'Hapus dusun "' + (btn.dataset.nama || '') + '" dari daftar? Data kependudukan yang sudah tersimpan tidak terhapus.', 'Hapus', function () {
                AdminUI.ajax(BASE + '/dusun-master-delete', { id: btn.dataset.id })
                    .then(function (res) {
                        AdminUI.showToast(res.ok ? 'success' : 'error', res.message);
                        if (res.ok) loadMaster();
                    });
            });
        });
    }
});
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>