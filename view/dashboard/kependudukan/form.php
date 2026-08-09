<?php
declare(strict_types=1);

$judulHalaman = 'Form Kependudukan';
$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = $editId > 0 ? getDataKependudukanById($editId) : null;
if ($editId > 0 && $data === null) {
    flash('error', 'Data tidak ditemukan.');
    redirect('/dashboard/kependudukan');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    $periode = trim((string) ($_POST['periode'] ?? ''));
    $jumlahKk = filter_var(trim((string) ($_POST['jumlah_kk'] ?? '')), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $jumlahJiwa = filter_var(trim((string) ($_POST['jumlah_jiwa'] ?? '')), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $jumlahLaki = filter_var(trim((string) ($_POST['jumlah_laki'] ?? '')), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $jumlahPerempuan = filter_var(trim((string) ($_POST['jumlah_perempuan'] ?? '')), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $keterangan = trim((string) ($_POST['keterangan'] ?? ''));

    $errors = [];
    if ($periode === '') {
        $errors[] = 'Periode wajib diisi.';
    }
    if (mb_strlen($periode) > 20) {
        $errors[] = 'Periode maksimal 20 karakter.';
    }
    if ($jumlahKk === false) {
        $errors[] = 'Jumlah KK harus berupa angka bulat minimal 0.';
    }
    if ($jumlahJiwa === false) {
        $errors[] = 'Jumlah jiwa harus berupa angka bulat minimal 0.';
    }
    if ($jumlahLaki === false) {
        $errors[] = 'Jumlah laki-laki harus berupa angka bulat minimal 0.';
    }
    if ($jumlahPerempuan === false) {
        $errors[] = 'Jumlah perempuan harus berupa angka bulat minimal 0.';
    }
    if ($jumlahLaki !== false && $jumlahPerempuan !== false && $jumlahLaki + $jumlahPerempuan !== $jumlahJiwa) {
        $errors[] = 'Jumlah jiwa harus sama dengan laki-laki + perempuan.';
    }
    if (mb_strlen($keterangan) > 255) {
        $errors[] = 'Keterangan maksimal 255 karakter.';
    }

    if ($errors !== []) {
        foreach ($errors as $err) {
            flash('error', $err);
        }
        redirect('/dashboard/kependudukan/form' . ($editId > 0 ? '?id=' . $editId : ''));
    }

    $dataBaru = [
        'periode' => $periode,
        'jumlah_kk' => (int) $jumlahKk,
        'jumlah_jiwa' => (int) $jumlahJiwa,
        'jumlah_laki' => (int) $jumlahLaki,
        'jumlah_perempuan' => (int) $jumlahPerempuan,
        'keterangan' => $keterangan,
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

$v = static function (string $field) use ($data): string {
    return e(trim((string) ($_POST[$field] ?? ($data[$field] ?? ''))));
};

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 md:mb-section-gap gap-4 md:gap-0">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Data Demografi</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0"><?= $editId > 0 ? 'Edit' : 'Tambah' ?> Data Kependudukan</h1>
</div>
<a class="text-caption font-caption text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="<?= APP_BASE ?>/dashboard/kependudukan">
<span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke daftar
</a>
</div>

<form method="post" action="<?= APP_BASE ?>/dashboard/kependudukan/form<?= $editId > 0 ? '?id=' . $editId : '' ?>">
<?= csrfField() ?>
<div class="grid grid-cols-12 gap-gutter">
<div class="col-span-12 xl:col-span-8 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="flex flex-col gap-2 relative z-10">
<div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md relative z-10">
<div class="flex flex-col gap-2">
<?php
    $b = '';
    $t = '';
    if ($v('periode') !== '') {
        $parts = explode('-', $v('periode'));
        if (count($parts) === 2) {
            $t = $parts[0];
            $b = $parts[1];
        } else {
            $t = $v('periode'); // fallback
        }
    }
?>
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="bulan">Bulan</label>
<select class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="bulan">
<option value="">-- Pilih Bulan --</option>
<?php
$months = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
foreach ($months as $k => $m) {
    echo '<option value="' . $k . '" ' . ($b === $k ? 'selected' : '') . '>' . $m . '</option>';
}
?>
</select>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="tahun">Tahun</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="tahun" placeholder="Contoh: 2026" type="number" min="2000" max="2100" value="<?= e($t) ?>"/>
</div>
</div>
<input type="hidden" id="periode" name="periode" value="<?= $v('periode') ?>" required/>
<p class="text-caption font-caption text-on-surface-variant m-0 relative z-10">Harus unik, satu baris data untuk satu periode (Format YYYY-MM).</p>
</div>
<p class="text-caption font-caption text-primary m-0 mt-4 relative z-10">Catatan: Total agregat dari semua dusun (isi manual atau akan diisi otomatis)</p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md relative z-10">
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="jumlah_kk">Jumlah Kepala Keluarga (KK)</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-label-mono font-label-mono text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="jumlah_kk" name="jumlah_kk" min="0" placeholder="Contoh: 1926" required type="number" value="<?= $v('jumlah_kk') ?>"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="jumlah_jiwa">Jumlah Jiwa</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-label-mono font-label-mono text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="jumlah_jiwa" name="jumlah_jiwa" min="0" placeholder="Contoh: 6886" required type="number" value="<?= $v('jumlah_jiwa') ?>"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="jumlah_laki">Laki-laki</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-label-mono font-label-mono text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="jumlah_laki" name="jumlah_laki" min="0" placeholder="Contoh: 3425" required type="number" value="<?= $v('jumlah_laki') ?>"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="jumlah_perempuan">Perempuan</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-label-mono font-label-mono text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="jumlah_perempuan" name="jumlah_perempuan" min="0" placeholder="Contoh: 3461" required type="number" value="<?= $v('jumlah_perempuan') ?>"/>
</div>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="keterangan">Keterangan</label>
<textarea class="w-full min-h-[120px] bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50 resize-y" id="keterangan" name="keterangan" placeholder="Contoh: Data hasil pemutakhiran akhir tahun..."><?= $v('keterangan') ?></textarea>
</div>
</div>
</div>

<div class="col-span-12 xl:col-span-4 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex items-center justify-between gap-4">
<div class="flex flex-col gap-1">
<span class="text-caption font-caption text-on-surface-variant">Data sudah sesuai?</span>
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest text-[11px]"><?= $editId > 0 ? 'Edit periode ' . e($data['periode']) : 'Periode baru' ?></span>
</div>
<button class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 whitespace-nowrap" type="submit">
<span class="material-symbols-outlined text-[20px]">check</span>
<?= $editId > 0 ? 'Simpan Perubahan' : 'Simpan Data' ?>
</button>
</div>
</div>
</div>
</form>

<div class="col-span-12 flex flex-col gap-stack-lg mt-8">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden" id="dusun-section" style="display: none;">
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Data Per Dusun</h2>
<div class="overflow-x-auto relative z-10">
<table class="w-full text-left border-collapse">
<thead>
<tr class="text-label-mono font-label-mono text-on-surface-variant border-b border-glass-border/50">
<th class="py-2 px-3 font-medium">Nama Dusun</th>
<th class="py-2 px-3 font-medium text-right">Laki-laki</th>
<th class="py-2 px-3 font-medium text-right">Perempuan</th>
<th class="py-2 px-3 font-medium text-right">KK</th>
<th class="py-2 px-3 font-medium text-right">Jiwa</th>
<th class="py-2 px-3 font-medium text-right">Aksi</th>
</tr>
</thead>
<tbody id="dusun-tbody">
</tbody>
</table>
</div>
<div class="mt-4 pt-4 border-t border-glass-border/50 relative z-10">
<form id="form-dusun" class="flex flex-col md:flex-row gap-3 items-end">
<div class="flex-1 min-w-[150px]">
<label class="text-[11px] uppercase text-on-surface-variant">Nama Dusun</label>
<input type="text" id="d_nama" required class="w-full bg-surface-container-highest border border-glass-border rounded-lg px-3 py-2 text-body-md focus:border-primary">
</div>
<div class="w-24">
<label class="text-[11px] uppercase text-on-surface-variant">Laki-laki</label>
<input type="number" id="d_laki" required min="0" class="w-full bg-surface-container-highest border border-glass-border rounded-lg px-3 py-2 text-body-md focus:border-primary">
</div>
<div class="w-24">
<label class="text-[11px] uppercase text-on-surface-variant">Perempuan</label>
<input type="number" id="d_perempuan" required min="0" class="w-full bg-surface-container-highest border border-glass-border rounded-lg px-3 py-2 text-body-md focus:border-primary">
</div>
<div class="w-24">
<label class="text-[11px] uppercase text-on-surface-variant">KK</label>
<input type="number" id="d_kk" required min="0" class="w-full bg-surface-container-highest border border-glass-border rounded-lg px-3 py-2 text-body-md focus:border-primary">
</div>
<div class="w-24">
<label class="text-[11px] uppercase text-on-surface-variant">Jiwa</label>
<input type="number" id="d_jiwa" required min="0" class="w-full bg-surface-container-highest border border-glass-border rounded-lg px-3 py-2 text-body-md focus:border-primary">
</div>
<button type="submit" class="bg-primary text-on-primary px-4 py-2 rounded-lg font-caption text-caption hover:shadow-lime-glow transition-all whitespace-nowrap h-[42px]">
Tambah Dusun
</button>
</form>
</div>
</div>
</div>

</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var bulan = document.getElementById('bulan');
    var tahun = document.getElementById('tahun');
    var periode = document.getElementById('periode');
    var dusunSection = document.getElementById('dusun-section');
    var dusunTbody = document.getElementById('dusun-tbody');
    
    function updatePeriode() {
        var b = bulan.value;
        var t = tahun.value;
        if (b && t) {
            periode.value = t + '-' + b;
            loadDusun();
        } else {
            periode.value = '';
            dusunSection.style.display = 'none';
        }
    }
    
    bulan.addEventListener('change', updatePeriode);
    tahun.addEventListener('input', updatePeriode);
    
    function loadDusun() {
        if (!periode.value) return;
        dusunSection.style.display = 'flex';
        dusunTbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-on-surface-variant">Memuat...</td></tr>';
        AdminUI.ajax('<?= APP_BASE ?>/dashboard/ajax/kependudukan/dusun-list', { periode: periode.value })
            .then(res => {
                if(res.ok) {
                    dusunTbody.innerHTML = res.html || '<tr><td colspan="6" class="text-center py-4 text-on-surface-variant">Belum ada data dusun.</td></tr>';
                    updateAgregat(res.rows);
                } else {
                    dusunTbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-error">Gagal memuat.</td></tr>';
                }
            }).catch(() => {
                dusunTbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-error">Gagal memuat.</td></tr>';
            });
    }
    
    function updateAgregat(rows) {
        if (!rows || rows.length === 0) return;
        var kk = 0, jiwa = 0, laki = 0, perempuan = 0;
        rows.forEach(r => {
            kk += parseInt(r.jumlah_kk);
            jiwa += parseInt(r.jumlah_jiwa);
            laki += parseInt(r.jumlah_laki);
            perempuan += parseInt(r.jumlah_perempuan);
        });
        document.getElementById('jumlah_kk').value = kk;
        document.getElementById('jumlah_jiwa').value = jiwa;
        document.getElementById('jumlah_laki').value = laki;
        document.getElementById('jumlah_perempuan').value = perempuan;
    }
    
    document.getElementById('form-dusun').addEventListener('submit', function(e) {
        e.preventDefault();
        if(!periode.value) {
            AdminUI.showToast('error', 'Isi bulan dan tahun periode dulu.');
            return;
        }
        var data = {
            periode: periode.value,
            nama_dusun: document.getElementById('d_nama').value,
            jumlah_laki: document.getElementById('d_laki').value,
            jumlah_perempuan: document.getElementById('d_perempuan').value,
            jumlah_kk: document.getElementById('d_kk').value,
            jumlah_jiwa: document.getElementById('d_jiwa').value
        };
        AdminUI.ajax('<?= APP_BASE ?>/dashboard/ajax/kependudukan/dusun-save', data)
            .then(res => {
                AdminUI.showToast(res.ok ? 'success' : 'error', res.message);
                if(res.ok) {
                    document.getElementById('form-dusun').reset();
                    loadDusun();
                }
            });
    });
    
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-dusun-delete]');
        if (btn) {
            AdminUI.confirmModal('Hapus Data Dusun', 'Yakin ingin menghapus data dusun ini?', 'Hapus', function () {
                AdminUI.ajax('<?= APP_BASE ?>/dashboard/ajax/kependudukan/dusun-delete', { id: btn.dataset.id })
                    .then(function (res) {
                        AdminUI.showToast(res.ok ? 'success' : 'error', res.message);
                        if (res.ok) loadDusun();
                    });
            });
        }
    });
    
    if (periode.value) loadDusun();
});
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>