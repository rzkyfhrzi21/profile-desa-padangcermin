<?php
declare(strict_types=1);

$judulHalaman = 'Form Struktur';
$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$struktur = $editId > 0 ? getStrukturById($editId) : null;
if ($editId > 0 && $struktur === null) {
    flash('error', 'Data tidak ditemukan.');
    redirect('/dashboard/struktur');
}

$parentOptions = strukturOptions(getStrukturAll(), $editId > 0 ? $editId : null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    $nama = trim((string) ($_POST['nama'] ?? ''));
    $jabatan = trim((string) ($_POST['jabatan'] ?? ''));
    $pendidikan = trim((string) ($_POST['pendidikan_terakhir'] ?? ''));
    $urutan = (int) ($_POST['urutan'] ?? 0);
    $parentId = (int) ($_POST['parent_id'] ?? 0);
    $fotoLama = $struktur['foto'] ?? null;

    $errors = [];
    if ($nama === '') {
        $errors[] = 'Nama pegawai wajib diisi.';
    }
    if (mb_strlen($nama) > 100) {
        $errors[] = 'Nama maksimal 100 karakter.';
    }
    if ($jabatan === '') {
        $errors[] = 'Jabatan wajib diisi.';
    }
    if (mb_strlen($jabatan) > 100) {
        $errors[] = 'Jabatan maksimal 100 karakter.';
    }
    if (mb_strlen($pendidikan) > 20) {
        $errors[] = 'Pendidikan terakhir maksimal 20 karakter.';
    }

    $foto = $fotoLama;
    $adaFile = ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    if ($adaFile) {
        $up = handleUpload($_FILES['foto'], 'struktur', $nama);
        if (!$up['ok']) {
            $errors[] = $up['error'];
        } else {
            $foto = $up['path'];
        }
    }

    if ($errors !== []) {
        foreach ($errors as $err) {
            flash('error', $err);
        }
        redirect('/dashboard/struktur/form' . ($editId > 0 ? '?id=' . $editId : ''));
    }

    $data = [
        'parent_id' => $parentId > 0 ? (string) $parentId : '',
        'nama' => $nama,
        'jabatan' => $jabatan,
        'pendidikan_terakhir' => $pendidikan,
        'foto' => $foto,
        'urutan' => $urutan,
    ];

    if ($editId > 0) {
        if (updateStruktur($editId, $data)) {
            catatLog('edit struktur: ' . $nama, 'struktur_organisasi', $editId);
            flash('success', 'Struktur berhasil diperbarui.');
        } else {
            flash('error', 'Gagal memperbarui struktur.');
        }
    } else {
        $newId = saveStruktur($data);
        if ($newId > 0) {
            catatLog('tambah struktur: ' . $nama, 'struktur_organisasi', $newId);
            flash('success', 'Struktur berhasil disimpan.');
        } else {
            flash('error', 'Gagal menyimpan struktur.');
        }
    }
    redirect('/dashboard/struktur');
}

$v = static function (string $field) use ($struktur): string {
    return e(trim((string) ($_POST[$field] ?? ($struktur[$field] ?? ''))));
};

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 md:mb-section-gap gap-4 md:gap-0">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Aparatur Desa</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0"><?= $editId > 0 ? 'Edit' : 'Tambah' ?> Jabatan</h1>
</div>
<a class="text-caption font-caption text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="<?= APP_BASE ?>/dashboard/struktur">
<span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke daftar
</a>
</div>

<form method="post" action="<?= APP_BASE ?>/dashboard/struktur/form<?= $editId > 0 ? '?id=' . $editId : '' ?>" enctype="multipart/form-data">
<?= csrfField() ?>
<div class="grid grid-cols-12 gap-gutter">
<div class="col-span-12 xl:col-span-8 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="nama">Nama Lengkap</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="nama" name="nama" placeholder="Contoh: Budi Santoso" required type="text" value="<?= $v('nama') ?>"/>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="jabatan">Jabatan</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="jabatan" name="jabatan" placeholder="Contoh: Kepala Pekon / Kasi Pemerintahan" required type="text" value="<?= $v('jabatan') ?>"/>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md relative z-10">
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="pendidikan_terakhir">Pendidikan Terakhir</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="pendidikan_terakhir" name="pendidikan_terakhir" placeholder="Contoh: S1" type="text" value="<?= $v('pendidikan_terakhir') ?>"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="urutan">Urutan</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-label-mono font-label-mono text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="urutan" name="urutan" min="0" placeholder="0" type="number" value="<?= $v('urutan') !== '' ? $v('urutan') : '0' ?>"/>
</div>
</div>
</div>
</div>

<div class="col-span-12 xl:col-span-4 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="flex items-center gap-3 relative z-10">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">account_tree</span>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Posisi Atasan</h2>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="parent_id">Atasan Langsung</label>
<select class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="parent_id" name="parent_id">
<option value="">— Jabatan puncak (Kepala Pekon) —</option>
<?php foreach ($parentOptions as $pid => $label): ?>
<option value="<?= (int) $pid ?>" <?= (string) $v('parent_id') === (string) $pid ? 'selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
</select>
<p class="text-caption font-caption text-on-surface-variant m-0">Kosongkan untuk menjadikan jabatan ini puncak hirarki.</p>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="flex items-center gap-3 relative z-10">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">image</span>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Foto Pegawai</h2>
</div>
<?php if ($editId > 0 && !empty($struktur['foto'])): ?>
<?php if (fotoAda($struktur['foto'])): ?>
<div class="relative z-10 flex items-center gap-4">
<a href="<?= uploadUrl($struktur['foto']) ?>" data-lightbox="<?= uploadUrl($struktur['foto']) ?>" title="Klik untuk preview">
<img class="w-20 h-20 rounded-2xl object-cover border border-glass-border hover:ring-2 hover:ring-primary/60 transition-all cursor-zoom-in" data-skeleton alt="Foto <?= e($struktur['nama']) ?>" src="<?= uploadUrl($struktur['foto']) ?>"/>
</a>
<div class="flex flex-col">
<span class="text-label-mono font-label-mono text-primary text-[12px] uppercase tracking-widest">Foto Saat Ini</span>
<span class="text-caption font-caption text-on-surface-variant">Klik foto untuk preview. Ganti dengan unggah foto baru.</span>
</div>
</div>
<?php else: ?>
<div class="relative z-10 flex items-center gap-4">
<?= avatarInisial($struktur['nama'], 'w-20 h-20', 'text-[24px]', 'rounded-2xl') ?>
<div class="flex flex-col">
<span class="text-label-mono font-label-mono text-primary text-[12px] uppercase tracking-widest">Foto Saat Ini</span>
<span class="text-caption font-caption text-on-surface-variant">File foto tidak ditemukan di penyimpanan. Unggah ulang foto baru.</span>
</div>
</div>
<?php endif; ?>
<?php endif; ?>
<!-- Preview foto baru sebelum upload -->
<div id="foto-preview-wrap" class="hidden relative z-10">
<img id="foto-preview-img" class="w-24 h-24 rounded-2xl object-cover border-2 border-primary/40" alt="Preview foto baru" src=""/>
<span class="text-[11px] text-primary mt-1 block">Preview foto yang akan diupload</span>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="foto">Unggah Foto Baru</label>
<input class="w-full text-caption font-caption text-on-surface-variant file:mr-4 file:rounded-xl file:border-0 file:bg-surface-container-highest file:px-4 file:py-2.5 file:text-on-surface file:cursor-pointer hover:file:bg-surface-container transition-colors" id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp,image/gif,image/heic,image/heif"/>
<p class="text-caption font-caption text-on-surface-variant m-0">Max 2 MB · JPG, PNG, WEBP, GIF · Kosongkan jika tidak ganti foto</p>
</div>
</div>
<script>
(function () {
    var fotoInput = document.getElementById('foto');
    var prevWrap  = document.getElementById('foto-preview-wrap');
    var prevImg   = document.getElementById('foto-preview-img');
    if (fotoInput && prevWrap && prevImg) {
        fotoInput.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) { prevWrap.classList.add('hidden'); return; }
            var reader = new FileReader();
            reader.onload = function (e) {
                prevImg.src = e.target.result;
                prevWrap.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    }
    if (window.MediaHelpers && window.MediaHelpers.initSkeleton) {
        MediaHelpers.initSkeleton(document.body);
    }
})();
</script>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex items-center justify-between gap-4">
<div class="flex flex-col gap-1">
<span class="text-caption font-caption text-on-surface-variant">Data jabatan siap?</span>
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest text-[11px]"><?= $editId > 0 ? 'Edit ' . e($struktur['nama']) : 'Jabatan baru' ?></span>
</div>
<button class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 whitespace-nowrap" type="submit">
<span class="material-symbols-outlined text-[20px]">check</span>
<?= $editId > 0 ? 'Simpan Perubahan' : 'Simpan Jabatan' ?>
</button>
</div>
</div>
</div>
</form>
</section>
<?php require __DIR__ . '/../layout_close.php'; ?>