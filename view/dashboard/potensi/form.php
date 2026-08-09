<?php
declare(strict_types=1);

$judulHalaman = 'Form Potensi Desa';
$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$potensi = $editId > 0 ? getPotensiById($editId) : null;
if ($editId > 0 && $potensi === null) {
    flash('error', 'Potensi tidak ditemukan.');
    redirect('/dashboard/potensi');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    $judul = trim((string) ($_POST['judul'] ?? ''));
    $deskripsi = trim((string) ($_POST['deskripsi'] ?? ''));
    $kategori = trim((string) ($_POST['kategori'] ?? ''));
    $urutan = trim((string) ($_POST['urutan'] ?? ''));
    $status = ($_POST['status'] ?? 'aktif') === 'nonaktif' ? 'nonaktif' : 'aktif';
    $ikon = trim((string) ($_POST['ikon'] ?? ''));
    $gambarLama = $potensi['gambar'] ?? null;

    $errors = [];
    if ($judul === '') {
        $errors[] = 'Judul potensi wajib diisi.';
    }
    if (mb_strlen($judul) > 150) {
        $errors[] = 'Judul potensi maksimal 150 karakter.';
    }
    if ($deskripsi === '') {
        $errors[] = 'Deskripsi potensi wajib diisi.';
    }
    if (mb_strlen($kategori) > 100) {
        $errors[] = 'Kategori maksimal 100 karakter.';
    }
    if ($urutan !== '' && !is_numeric($urutan)) {
        $errors[] = 'Urutan harus berupa angka.';
    }

    $gambar = $gambarLama;
    $adaFile = ($_FILES['gambar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    if ($adaFile && $errors === []) {
        $up = handleUpload($_FILES['gambar'], 'potensi', $judul);
        if (!$up['ok']) {
            $errors[] = $up['error'];
        } else {
            $gambar = $up['path'];
        }
    }

    if ($errors !== []) {
        foreach ($errors as $err) {
            flash('error', $err);
        }
        redirect('/dashboard/potensi/form' . ($editId > 0 ? '?id=' . $editId : ''));
    }

    $data = [
        'judul' => $judul,
        'deskripsi' => $deskripsi,
        'gambar' => $gambar,
        'ikon' => $ikon,
        'kategori' => $kategori,
        'urutan' => $urutan === '' ? 0 : (int) $urutan,
        'status' => $status,
    ];

    try {
        if ($editId > 0) {
            if (updatePotensi($editId, $data)) {
                if ($adaFile && !empty($gambarLama) && $gambarLama !== $gambar) {
                    $file = UPLOAD_PATH . '/' . $gambarLama;
                    if (is_file($file)) { @unlink($file); }
                }
                catatLog('edit potensi: ' . $judul, 'potensi_desa', $editId);
                flash('success', 'Potensi berhasil diperbarui.');
            } else {
                flash('error', 'Gagal memperbarui potensi.');
            }
        } else {
            $newId = savePotensi($data);
            if ($newId > 0) {
                catatLog('tambah potensi: ' . $judul, 'potensi_desa', $newId);
                flash('success', 'Potensi berhasil disimpan.');
            } else {
                flash('error', 'Gagal menyimpan potensi.');
            }
        }
    } catch (Throwable $ex) {
        flash('error', 'Terjadi kesalahan: ' . $ex->getMessage());
    }
    redirect('/dashboard/potensi');
}

$v = static function (string $field) use ($potensi): string {
    return e(trim((string) ($_POST[$field] ?? ($potensi[$field] ?? ''))));
};

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 md:mb-section-gap gap-4 md:gap-0">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Potensi Desa</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0"><?= $editId > 0 ? 'Edit' : 'Tambah' ?> Potensi</h1>
</div>
<a class="text-caption font-caption text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="<?= APP_BASE ?>/dashboard/potensi">
<span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke daftar
</a>
</div>

<form method="post" action="<?= APP_BASE ?>/dashboard/potensi/form<?= $editId > 0 ? '?id=' . $editId : '' ?>" enctype="multipart/form-data">
<?= csrfField() ?>
<div class="grid grid-cols-12 gap-gutter">
<div class="col-span-12 xl:col-span-8 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="judul">Judul Potensi</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="judul" name="judul" placeholder="Contoh: Kopi Robusta Unggulan" required type="text" value="<?= $v('judul') ?>"/>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="deskripsi">Deskripsi</label>
<textarea class="w-full min-h-[240px] bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50 resize-y" id="deskripsi" name="deskripsi" placeholder="Tulis deskripsi potensi unggulan desa..." required><?= $v('deskripsi') ?></textarea>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="kategori">Kategori</label>
<select class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="kategori" name="kategori">
<option value="">— Tanpa kategori —</option>
<option value="pertanian" <?= $v('kategori') === 'pertanian' ? 'selected' : '' ?>>Pertanian</option>
<option value="perkebunan" <?= $v('kategori') === 'perkebunan' ? 'selected' : '' ?>>Perkebunan</option>
<option value="peternakan" <?= $v('kategori') === 'peternakan' ? 'selected' : '' ?>>Peternakan</option>
<option value="perikanan" <?= $v('kategori') === 'perikanan' ? 'selected' : '' ?>>Perikanan</option>
<option value="umkm" <?= $v('kategori') === 'umkm' ? 'selected' : '' ?>>UMKM</option>
<option value="kerajinan" <?= $v('kategori') === 'kerajinan' ? 'selected' : '' ?>>Kerajinan</option>
</select>
</div>
</div>
</div>

<div class="col-span-12 xl:col-span-4 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="flex items-center gap-3 relative z-10">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">tune</span>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Pengaturan</h2>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="urutan">Urutan Tampil</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-label-mono font-label-mono text-on-surface-variant focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="urutan" name="urutan" min="0" placeholder="0" type="number" value="<?= $editId === 0 && $v('urutan') === '' ? '0' : $v('urutan') ?>"/>
</div>
<div class="flex flex-col gap-2 relative z-10">
<div class="flex items-center justify-between">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="ikon">Ikon Material</label>
<a href="https://fonts.google.com/icons" target="_blank" rel="noopener" class="flex items-center gap-1 text-primary text-[11px] hover:underline" title="Buka referensi ikon Material Symbols">
<span class="material-symbols-outlined text-[13px]">open_in_new</span>Referensi Ikon
</a>
</div>
<div class="relative flex items-center gap-2">
<div class="w-10 h-10 rounded-xl bg-surface-container border border-glass-border flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-primary text-[20px]" id="ikon-preview" style="font-variation-settings:'FILL' 1"><?= $v('ikon') !== '' ? $v('ikon') : 'add_reaction' ?></span>
</div>
<input class="flex-1 bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="ikon" name="ikon" placeholder="Contoh: local_florist" type="text" value="<?= $v('ikon') ?>"/>
</div>
<p class="text-[11px] text-on-surface-variant m-0">Nama ikon dari <a href="https://fonts.google.com/icons" target="_blank" class="text-primary hover:underline">Material Symbols</a> (huruf kecil, underscore).</p>
</div>
<div class="flex flex-col gap-2 relative z-10">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Status</span>
<div class="flex gap-2">
<label class="flex-1 cursor-pointer">
<input class="peer sr-only" name="status" type="radio" value="aktif" <?= $v('status') === 'aktif' || ($editId === 0 && $v('status') === '') ? 'checked' : '' ?>/>
<div class="px-4 py-3 rounded-xl border border-glass-border bg-surface-container-highest text-on-surface-variant text-center text-caption font-caption peer-checked:border-primary/50 peer-checked:bg-primary/10 peer-checked:text-primary transition-all">Aktif</div>
</label>
<label class="flex-1 cursor-pointer">
<input class="peer sr-only" name="status" type="radio" value="nonaktif" <?= $v('status') === 'nonaktif' ? 'checked' : '' ?>/>
<div class="px-4 py-3 rounded-xl border border-glass-border bg-surface-container-highest text-on-surface-variant text-center text-caption font-caption peer-checked:border-primary/50 peer-checked:bg-primary/10 peer-checked:text-primary transition-all">Nonaktif</div>
</label>
</div>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="flex items-center gap-3 relative z-10">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">image</span>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Gambar Potensi</h2>
</div>
<?php if ($editId > 0 && !empty($potensi['gambar'])): ?>
<div class="relative z-10">
<img class="w-full h-40 object-cover rounded-xl border border-glass-border cursor-pointer"
     data-lightbox="<?= uploadUrl($potensi['gambar']) ?>"
     data-skeleton
     alt="Gambar potensi <?= e($potensi['judul']) ?>"
     src="<?= uploadUrl($potensi['gambar']) ?>"/>
<p class="text-[11px] text-on-surface-variant mt-1">Klik gambar untuk lihat full-size</p>
</div>
<?php endif; ?>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="gambar">Unggah Gambar</label>
<input class="w-full text-caption font-caption text-on-surface-variant file:mr-4 file:rounded-xl file:border-0 file:bg-surface-container-highest file:px-4 file:py-2.5 file:text-on-surface file:cursor-pointer hover:file:bg-surface-container transition-colors" id="gambar" name="gambar" type="file" accept="image/jpeg,image/png,image/webp,image/gif"/>
</div>
<!-- Preview gambar baru -->
<div id="gambar-preview-wrap" class="hidden relative z-10">
<img id="gambar-preview-img" src="" alt="Preview gambar" class="w-full h-40 object-cover rounded-xl border border-primary/30"/>
<p class="text-[11px] text-primary mt-1">Preview gambar yang akan diupload</p>
</div>
<p class="text-[11px] text-on-surface-variant relative z-10 m-0">Max 2 MB · JPG, PNG, WEBP · Kosongkan jika tidak ganti gambar</p>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex items-center justify-between gap-4">
<div class="flex flex-col gap-1">
<span class="text-caption font-caption text-on-surface-variant">Siap ditampilkan?</span>
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest text-[11px]"><?= $editId > 0 ? 'Edit ' . e($potensi['judul']) : 'Potensi baru' ?></span>
</div>
<button class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 whitespace-nowrap" type="submit">
<span class="material-symbols-outlined text-[20px]">check</span>
<?= $editId > 0 ? 'Simpan Perubahan' : 'Simpan Potensi' ?>
</button>
</div>
</div>
</div>
</form>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    /* Ikon preview */
    var ikonInput = document.getElementById('ikon');
    var ikonPreview = document.getElementById('ikon-preview');
    if (ikonInput && ikonPreview) {
        ikonInput.addEventListener('input', function () {
            ikonPreview.textContent = this.value.trim() || 'add_reaction';
        });
    }

    /* Gambar file preview */
    var gambarInput = document.getElementById('gambar');
    var previewWrap = document.getElementById('gambar-preview-wrap');
    var previewImg  = document.getElementById('gambar-preview-img');
    if (gambarInput && previewWrap && previewImg) {
        gambarInput.addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (!file) { previewWrap.classList.add('hidden'); return; }
            var reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewWrap.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    }

    /* Skeleton & lightbox init */
    if (typeof MediaHelpers !== 'undefined') MediaHelpers.initSkeleton(document.body);
});
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>

