<?php
declare(strict_types=1);

$judulHalaman = 'Form Wisata';
$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$wisata = $editId > 0 ? getWisataById($editId) : null;
if ($editId > 0 && $wisata === null) {
    flash('error', 'Wisata tidak ditemukan.');
    redirect('/dashboard/wisata');
}
$galeri = $editId > 0 ? getWisataImages($editId) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    if (isset($_POST['hapus_gambar'])) {
        $gambarId = (int) $_POST['hapus_gambar'];
        $path = deleteWisataImage($gambarId);
        if ($path !== null) {
            $file = UPLOAD_PATH . '/' . $path;
            if (is_file($file)) {
                @unlink($file);
            }
            flash('success', 'Gambar dihapus.');
        }
        redirect('/dashboard/wisata/form?id=' . $editId);
    }

    $nama = trim((string) ($_POST['nama'] ?? ''));
    $slug = trim((string) ($_POST['slug'] ?? ''));
    $deskripsi = trim((string) ($_POST['deskripsi'] ?? ''));
    $alamat = trim((string) ($_POST['alamat'] ?? ''));
    $latitude = trim((string) ($_POST['latitude'] ?? ''));
    $longitude = trim((string) ($_POST['longitude'] ?? ''));
    $hargaTiket = trim((string) ($_POST['harga_tiket'] ?? ''));
    $jamBuka = trim((string) ($_POST['jam_buka'] ?? ''));
    $status = ($_POST['status'] ?? 'draft') === 'publish' ? 'publish' : 'draft';
    $alt = trim((string) ($_POST['alt_gambar'] ?? ''));

    $errors = [];
    if ($nama === '') {
        $errors[] = 'Nama wisata wajib diisi.';
    }
    if ($slug === '') {
        $slug = slugify($nama);
    }
    if ($slug === '' || slugExistsWisata($slug, $editId > 0 ? $editId : null)) {
        $errors[] = 'Slug kosong atau sudah dipakai wisata lain.';
    }
    if ($latitude !== '' && !is_numeric($latitude)) {
        $errors[] = 'Latitude harus berupa angka.';
    }
    if ($longitude !== '' && !is_numeric($longitude)) {
        $errors[] = 'Longitude harus berupa angka.';
    }

    $galeriBaru = [];
    if (($files = $_FILES['galeri'] ?? null) !== null && isset($files['error']) && is_array($files['error'])) {
        foreach ($files['error'] as $i => $err) {
            if ($err === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $bagian = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $err,
                'size' => $files['size'][$i],
            ];
            $galeriBaru[] = $bagian;
        }
    }
    if ($galeriBaru !== [] && $alt === '') {
        $errors[] = 'Alt text wajib diisi jika mengunggah gambar galeri.';
    }

    if ($errors !== []) {
        foreach ($errors as $err) {
            flash('error', $err);
        }
        redirect('/dashboard/wisata/form' . ($editId > 0 ? '?id=' . $editId : ''));
    }

    $data = [
        'nama' => $nama,
        'slug' => $slug,
        'deskripsi' => $deskripsi,
        'alamat' => $alamat,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'harga_tiket' => $hargaTiket,
        'jam_buka' => $jamBuka,
        'status' => $status,
    ];

    if ($editId > 0) {
        if (updateWisata($editId, $data)) {
            catatLog('edit wisata: ' . $nama, 'wisata_desa', $editId);
            flash('success', 'Wisata berhasil diperbarui.');
        } else {
            flash('error', 'Gagal memperbarui wisata.');
        }
    } else {
        $newId = saveWisata($data);
        if ($newId > 0) {
            $editId = $newId;
            catatLog('tambah wisata: ' . $nama, 'wisata_desa', $newId);
            flash('success', 'Wisata berhasil disimpan. Tambahkan galeri gambar di bawah.');
        } else {
            flash('error', 'Gagal menyimpan wisata.');
        }
    }

    foreach ($galeriBaru as $bagian) {
        $up = handleUpload($bagian, 'wisata', $alt);
        if ($up['ok']) {
            addWisataImage($editId, $up['path']);
        } else {
            flash('error', 'Galeri: ' . $up['error']);
        }
    }

    redirect('/dashboard/wisata/form' . ($editId > 0 ? '?id=' . $editId : ''));
}

$v = static function (string $field) use ($wisata): string {
    return e(trim((string) ($_POST[$field] ?? ($wisata[$field] ?? ''))));
};

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 md:mb-section-gap gap-4 md:gap-0">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Potensi Desa</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0"><?= $editId > 0 ? 'Edit' : 'Tambah' ?> Wisata</h1>
</div>
<a class="text-caption font-caption text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="<?= APP_BASE ?>/dashboard/wisata">
<span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke daftar
</a>
</div>

<form method="post" action="<?= APP_BASE ?>/dashboard/wisata/form<?= $editId > 0 ? '?id=' . $editId : '' ?>" enctype="multipart/form-data">
<?= csrfField() ?>
<div class="grid grid-cols-12 gap-gutter">
<div class="col-span-12 xl:col-span-8 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md relative z-10">
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="nama">Nama Wisata</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="nama" name="nama" required type="text" value="<?= $v('nama') ?>"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="slug">Slug URL</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-label-mono font-label-mono text-on-surface-variant focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="slug" name="slug" type="text" value="<?= $v('slug') ?>"/>
</div>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="deskripsi">Deskripsi</label>
<textarea class="w-full min-h-[200px] bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50 resize-y" id="deskripsi" name="deskripsi" required><?= $v('deskripsi') ?></textarea>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="alamat">Alamat / Lokasi</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="alamat" name="alamat" type="text" value="<?= $v('alamat') ?>"/>
</div>
<div class="grid grid-cols-2 gap-stack-md relative z-10">
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="latitude">Latitude</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-label-mono font-label-mono text-on-surface-variant focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="latitude" name="latitude" type="text" placeholder="-5.3123" value="<?= $v('latitude') ?>"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="longitude">Longitude</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-label-mono font-label-mono text-on-surface-variant focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="longitude" name="longitude" type="text" placeholder="104.8123" value="<?= $v('longitude') ?>"/>
</div>
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
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="harga_tiket">Harga Tiket</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="harga_tiket" name="harga_tiket" placeholder="Rp 10.000 / Gratis" type="text" value="<?= $v('harga_tiket') ?>"/>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="jam_buka">Jam Buka</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="jam_buka" name="jam_buka" placeholder="08.00 - 17.00 WIB" type="text" value="<?= $v('jam_buka') ?>"/>
</div>
<div class="flex flex-col gap-2 relative z-10">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Status</span>
<div class="flex gap-2">
<label class="flex-1 cursor-pointer">
<input class="peer sr-only" name="status" type="radio" value="draft" <?= $v('status') === 'draft' || ($editId === 0 && $v('status') === '') ? 'checked' : '' ?>/>
<div class="px-4 py-3 rounded-xl border border-glass-border bg-surface-container-highest text-on-surface-variant text-center text-caption font-caption peer-checked:border-primary/50 peer-checked:bg-primary/10 peer-checked:text-primary transition-all">Draft</div>
</label>
<label class="flex-1 cursor-pointer">
<input class="peer sr-only" name="status" type="radio" value="publish" <?= $v('status') === 'publish' ? 'checked' : '' ?>/>
<div class="px-4 py-3 rounded-xl border border-glass-border bg-surface-container-highest text-on-surface-variant text-center text-caption font-caption peer-checked:border-primary/50 peer-checked:bg-primary/10 peer-checked:text-primary transition-all">Publish</div>
</label>
</div>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="flex items-center gap-3 relative z-10">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">photo_library</span>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Galeri Gambar</h2>
</div>
<?php if ($galeri !== []): ?>
<div class="grid grid-cols-2 gap-3 relative z-10">
<?php foreach ($galeri as $g): ?>
<div class="relative group/thumb rounded-xl overflow-hidden border border-glass-border bg-surface-container-high">
    <img class="w-full h-24 object-cover block" alt="Galeri <?= e($wisata['nama']) ?>" src="<?= uploadUrl($g['path_gambar']) ?>"/>
    <!-- Overlay tengah: muncul saat hover -->
    <div class="absolute inset-0 bg-black/55 opacity-0 group-hover/thumb:opacity-100 transition-opacity flex items-center justify-center gap-2 pointer-events-none group-hover/thumb:pointer-events-auto">
        <!-- Tombol lihat besar -->
        <button type="button"
                class="w-9 h-9 rounded-full bg-white/15 hover:bg-white/30 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white transition-all"
                title="Lihat gambar"
                data-gallery-preview
                data-src="<?= e(uploadUrl($g['path_gambar'])) ?>"
                data-alt="Galeri <?= e($wisata['nama']) ?>">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1">zoom_in</span>
        </button>
        <!-- Tombol hapus -->
        <button type="button"
                class="w-9 h-9 rounded-full bg-red-500/70 hover:bg-red-500 backdrop-blur-sm border border-red-400/30 flex items-center justify-center text-white transition-all"
                title="Hapus gambar"
                data-gallery-delete
                data-gambar-id="<?= (int) $g['id'] ?>">
            <span class="material-symbols-outlined text-[18px]">delete</span>
        </button>
    </div>
    <!-- Hidden form hapus per gambar (di-submit via JS) -->
    <form class="hidden" method="post" action="<?= APP_BASE ?>/dashboard/wisata/form?id=<?= $editId ?>" data-delete-form="<?= (int) $g['id'] ?>">
        <?= csrfField() ?>
        <input type="hidden" name="hapus_gambar" value="<?= (int) $g['id'] ?>"/>
    </form>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="galeri">Tambah Gambar (bisa banyak)</label>
<input class="w-full text-caption font-caption text-on-surface-variant file:mr-4 file:rounded-xl file:border-0 file:bg-surface-container-highest file:px-4 file:py-2.5 file:text-on-surface file:cursor-pointer hover:file:bg-surface-container transition-colors" id="galeri" name="galeri[]" type="file" accept="image/jpeg,image/png,image/webp,image/gif" multiple/>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="alt_gambar">Alt Text (wajib)</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-caption font-caption text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="alt_gambar" name="alt_gambar" placeholder="Deskripsi gambar untuk aksesibilitas & SEO" type="text"/>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex items-center justify-between gap-4">
<div class="flex flex-col gap-1">
<span class="text-caption font-caption text-on-surface-variant">Siap dipublikasikan?</span>
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest text-[11px]"><?= $editId > 0 ? 'Edit ' . e($wisata['nama']) : 'Wisata baru' ?></span>
</div>
<button class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 whitespace-nowrap" type="submit">
<span class="material-symbols-outlined text-[20px]">check</span>
<?= $editId > 0 ? 'Simpan Perubahan' : 'Simpan Wisata' ?>
</button>
</div>
</div>
</div>
</form>
</section>
<script>
/* ---- Slug auto-generate ---- */
(function() {
    var nama = document.getElementById('nama');
    var slug = document.getElementById('slug');
    if (!nama || !slug) return;
    var manual = false;
    slug.addEventListener('input', function() { manual = slug.value.length > 0; });
    nama.addEventListener('input', function() {
        if (manual) return;
        slug.value = nama.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim().replace(/[\s-]+/g, '-');
    });
})();

/* ---- Galeri: lightbox preview + confirm delete ---- */
(function() {
    /* ======= LIGHTBOX ======= */
    function openLightbox(src, alt) {
        var overlay = document.createElement('div');
        overlay.id = 'gallery-lightbox';
        overlay.className = 'fixed inset-0 z-[200] flex items-center justify-center bg-black/85 backdrop-blur-sm';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.innerHTML =
            '<div class="relative max-w-[92vw] max-h-[92vh] flex flex-col items-center">' +
            '  <button type="button" id="gallery-lb-close"' +
            '    class="absolute -top-10 right-0 w-9 h-9 rounded-full bg-white/15 hover:bg-white/30 border border-white/20 flex items-center justify-center text-white transition-all"' +
            '    aria-label="Tutup">' +
            '    <span class="material-symbols-outlined text-[20px]">close</span>' +
            '  </button>' +
            '  <img src="' + src + '" alt="' + alt + '"' +
            '    class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl block"/>' +
            '</div>';

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';

        function closeLb() {
            overlay.remove();
            document.body.style.overflow = '';
            document.removeEventListener('keydown', onKey);
        }
        function onKey(e) { if (e.key === 'Escape') closeLb(); }
        document.addEventListener('keydown', onKey);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeLb();
        });
        document.getElementById('gallery-lb-close').addEventListener('click', closeLb);
    }

    /* ======= CONFIRM DELETE MODAL ======= */
    function openConfirmDelete(gambarId) {
        var overlay = document.createElement('div');
        overlay.id = 'gallery-confirm';
        overlay.className = 'fixed inset-0 z-[200] flex items-center justify-center bg-black/70 backdrop-blur-sm';
        overlay.innerHTML =
            '<div class="bg-surface-container-highest border border-glass-border rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 flex flex-col gap-5">' +
            '  <div class="flex items-center gap-3">' +
            '    <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center shrink-0">' +
            '      <span class="material-symbols-outlined text-red-400" style="font-variation-settings:\'FILL\' 1">delete</span>' +
            '    </div>' +
            '    <div>' +
            '      <h3 class="text-base font-semibold text-on-surface leading-tight">Hapus Gambar</h3>' +
            '      <p class="text-sm text-on-surface-variant mt-0.5">Gambar yang dihapus tidak dapat dikembalikan.</p>' +
            '    </div>' +
            '  </div>' +
            '  <div class="flex gap-3 justify-end">' +
            '    <button type="button" id="gallery-confirm-cancel"' +
            '      class="px-4 py-2 rounded-xl border border-glass-border text-on-surface-variant hover:text-on-surface text-sm transition-colors">Batal</button>' +
            '    <button type="button" id="gallery-confirm-ok"' +
            '      class="px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-medium transition-colors">Hapus</button>' +
            '  </div>' +
            '</div>';

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';

        function closeConfirm() {
            overlay.remove();
            document.body.style.overflow = '';
            document.removeEventListener('keydown', onKey);
        }
        function onKey(e) { if (e.key === 'Escape') closeConfirm(); }
        document.addEventListener('keydown', onKey);
        overlay.addEventListener('click', function(e) { if (e.target === overlay) closeConfirm(); });
        document.getElementById('gallery-confirm-cancel').addEventListener('click', closeConfirm);
        document.getElementById('gallery-confirm-ok').addEventListener('click', function() {
            var form = document.querySelector('[data-delete-form="' + gambarId + '"]');
            if (form) form.submit();
        });
    }

    /* ======= BIND EVENTS ======= */
    document.addEventListener('click', function(e) {
        /* Preview */
        var previewBtn = e.target.closest('[data-gallery-preview]');
        if (previewBtn) {
            openLightbox(previewBtn.dataset.src, previewBtn.dataset.alt || '');
            return;
        }
        /* Delete */
        var deleteBtn = e.target.closest('[data-gallery-delete]');
        if (deleteBtn) {
            openConfirmDelete(deleteBtn.dataset.gambarId);
        }
    });
})();
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>
