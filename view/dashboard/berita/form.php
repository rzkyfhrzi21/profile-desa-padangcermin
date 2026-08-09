<?php
declare(strict_types=1);

$judulHalaman = 'Form Berita';
$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$berita = $editId > 0 ? getBeritaById($editId) : null;
if ($editId > 0 && $berita === null) {
    flash('error', 'Berita tidak ditemukan.');
    redirect('/dashboard/berita');
}

$kategoriList = getBeritaKategoriList();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    if (isset($_POST['kategori_baru']) && trim((string) $_POST['kategori_baru']) !== '') {
        $nama = trim((string) $_POST['kategori_baru']);
        if (mb_strlen($nama) > 50) {
            flash('error', 'Nama kategori maksimal 50 karakter.');
        } else {
            saveBeritaKategori($nama);
            catatLog('tambah kategori berita: ' . $nama, 'berita_kategori');
            flash('success', 'Kategori "' . $nama . '" ditambahkan.');
        }
        redirect('/dashboard/berita/form' . ($editId > 0 ? '?id=' . $editId : ''));
    }

    $judul = trim((string) ($_POST['judul'] ?? ''));
    $konten = trim((string) ($_POST['konten'] ?? ''));
    $slug = trim((string) ($_POST['slug'] ?? ''));
    $kategoriId = (int) ($_POST['kategori_id'] ?? 0);
    $status = ($_POST['status'] ?? 'draft') === 'publish' ? 'publish' : 'draft';
    $alt = trim((string) ($_POST['alt_gambar'] ?? ''));
    $gambarLama = $berita['gambar_utama'] ?? null;

    $errors = [];
    if ($judul === '') {
        $errors[] = 'Judul berita wajib diisi.';
    }
    if ($konten === '') {
        $errors[] = 'Konten berita wajib diisi.';
    }
    if ($slug === '') {
        $slug = slugify($judul);
    }
    if ($slug === '' || slugExistsBerita($slug, $editId > 0 ? $editId : null)) {
        $errors[] = 'Slug kosong atau sudah dipakai berita lain.';
    }

    $gambar = $gambarLama;
    $adaFile = ($_FILES['gambar_utama']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    if ($adaFile) {
        $up = handleUpload($_FILES['gambar_utama'], 'berita', $alt);
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
        redirect('/dashboard/berita/form' . ($editId > 0 ? '?id=' . $editId : ''));
    }

    $data = [
        'judul' => $judul,
        'slug' => $slug,
        'kategori_id' => $kategoriId > 0 ? (string) $kategoriId : '',
        'konten' => $konten,
        'gambar_utama' => $gambar,
        'status' => $status,
    ];

    if ($editId > 0) {
        if (updateBerita($editId, $data)) {
            catatLog('edit berita: ' . $judul, 'berita_desa', $editId);
            flash('success', 'Berita berhasil diperbarui.');
        } else {
            flash('error', 'Gagal memperbarui berita.');
        }
    } else {
        $newId = saveBerita($data);
        if ($newId > 0) {
            catatLog('tambah berita: ' . $judul, 'berita_desa', $newId);
            flash('success', 'Berita berhasil disimpan.');
        } else {
            flash('error', 'Gagal menyimpan berita.');
        }
    }
    redirect('/dashboard/berita');
}

$v = static function (string $field) use ($berita): string {
    return e(trim((string) ($_POST[$field] ?? ($berita[$field] ?? ''))));
};

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 md:mb-section-gap gap-4 md:gap-0">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Publikasi Desa</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0"><?= $editId > 0 ? 'Edit' : 'Tulis' ?> Berita</h1>
</div>
<a class="text-caption font-caption text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="<?= APP_BASE ?>/dashboard/berita">
<span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke daftar
</a>
</div>

<form method="post" action="<?= APP_BASE ?>/dashboard/berita/form<?= $editId > 0 ? '?id=' . $editId : '' ?>" enctype="multipart/form-data">
<?= csrfField() ?>
<div class="grid grid-cols-12 gap-gutter">
<div class="col-span-12 xl:col-span-8 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="judul">Judul Berita</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="judul" name="judul" placeholder="Contoh: Penyelesaian Pengaspalan Jalan Dusun III" required type="text" value="<?= $v('judul') ?>"/>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="slug">Slug URL</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-label-mono font-label-mono text-on-surface-variant focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="slug" name="slug" placeholder="otomatis dari judul" type="text" value="<?= $v('slug') ?>"/>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="konten">Konten Berita</label>
<textarea class="w-full min-h-[320px] bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50 resize-y" id="konten" name="konten" placeholder="Tulis isi berita disini..." required><?= $v('konten') ?></textarea>
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
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="kategori_id">Kategori</label>
<select class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="kategori_id" name="kategori_id">
<option value="">— Pilih kategori —</option>
<?php foreach ($kategoriList as $k): ?>
<option value="<?= (int) $k['id'] ?>" <?= (int) $v('kategori_id') === (int) $k['id'] ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
<?php endforeach; ?>
</select>
<div class="flex gap-2">
<input class="flex-1 min-w-0 bg-surface-container-highest border border-glass-border rounded-xl px-3 py-2 text-caption font-caption text-on-surface focus:outline-none focus:border-primary transition-all placeholder:text-on-surface-variant/50" name="kategori_baru" placeholder="Kategori baru..."/>
<button class="px-3 py-2 rounded-xl bg-surface-container-high text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors border border-glass-border text-caption font-caption whitespace-nowrap" type="submit">Tambah</button>
</div>
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
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">image</span>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Gambar Utama</h2>
</div>
<?php if ($editId > 0 && !empty($berita['gambar_utama'])): ?>
<div class="relative z-10">
<img class="w-full h-40 object-cover rounded-xl border border-glass-border" alt="Gambar utama berita <?= e($berita['judul']) ?>" src="<?= uploadUrl($berita['gambar_utama']) ?>"/>
</div>
<?php endif; ?>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="gambar_utama">Unggah Gambar</label>
<input class="w-full text-caption font-caption text-on-surface-variant file:mr-4 file:rounded-xl file:border-0 file:bg-surface-container-highest file:px-4 file:py-2.5 file:text-on-surface file:cursor-pointer hover:file:bg-surface-container transition-colors" id="gambar_utama" name="gambar_utama" type="file" accept="image/jpeg,image/png,image/webp,image/gif"/>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="alt_gambar">Alt Text (wajib)</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-caption font-caption text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="alt_gambar" name="alt_gambar" placeholder="Deskripsi singkat gambar untuk aksesibilitas & SEO" type="text" value="<?= e(trim((string) ($_POST['alt_gambar'] ?? ''))) ?>"/>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex items-center justify-between gap-4">
<div class="flex flex-col gap-1">
<span class="text-caption font-caption text-on-surface-variant">Siap dipublikasikan?</span>
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest text-[11px]"><?= $editId > 0 ? 'Edit ' . e($berita['judul']) : 'Berita baru' ?></span>
</div>
<button class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 whitespace-nowrap" type="submit">
<span class="material-symbols-outlined text-[20px]">check</span>
<?= $editId > 0 ? 'Simpan Perubahan' : 'Simpan Berita' ?>
</button>
</div>
</div>
</div>
</form>
</section>
<script>
(function() {
    var judul = document.getElementById('judul');
    var slug = document.getElementById('slug');
    if (!judul || !slug) return;
    var manual = false;
    slug.addEventListener('input', function() { manual = slug.value.length > 0; });
    judul.addEventListener('input', function() {
        if (manual) return;
        slug.value = judul.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim().replace(/[\s-]+/g, '-');
    });
})();
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>
