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
$fasilitasList = $editId > 0 ? getWisataFasilitas($editId) : [];
$ikonFasilitas = [
    'photo_camera', 'restaurant', 'directions_walk', 'local_florist',
    'forest', 'camera_roll', 'hiking', 'water', 'terrain',
    'night_shelter', 'wc', 'local_parking', 'storefront',
    'volunteer_activism', 'celebration', 'park', 'kayaking', 'shower',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    if (isset($_POST['hapus_gambar'])) {
        $gambarId = (int) $_POST['hapus_gambar'];
        $path = deleteWisataImage($editId, $gambarId);
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
    $mapsUrl = trim((string) ($_POST['maps_embed_url'] ?? ''));
    if (str_contains($mapsUrl, '<iframe')) {
        preg_match('/src=["\']([^"\']+)["\']/', $mapsUrl, $m);
        $mapsUrl = $m[1] ?? '';
    }
    $hargaTiket = trim((string) ($_POST['harga_tiket'] ?? ''));
    $jamBuka = trim((string) ($_POST['jam_buka'] ?? ''));
    $waKontak = preg_replace('/[^0-9]/', '', (string) ($_POST['wa_kontak'] ?? ''));
    $status = ($_POST['status'] ?? 'draft') === 'publish' ? 'publish' : 'draft';

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

    $galeriBaru = [];
    if (($files = $_FILES['galeri'] ?? null) !== null && isset($files['error']) && is_array($files['error'])) {
        foreach ($files['error'] as $i => $err) {
            if ($err === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $galeriBaru[] = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $err,
                'size'     => $files['size'][$i],
            ];
        }
    }

    if ($errors !== []) {
        foreach ($errors as $err) {
            flash('error', $err);
        }
        redirect('/dashboard/wisata/form' . ($editId > 0 ? '?id=' . $editId : ''));
    }

    $data = [
        'nama'           => $nama,
        'slug'           => $slug,
        'deskripsi'      => $deskripsi,
        'alamat'         => $alamat,
        'maps_embed_url' => $mapsUrl,
        'harga_tiket'    => $hargaTiket,
        'jam_buka'       => $jamBuka,
        'wa_kontak'      => $waKontak,
        'status'         => $status,
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
            flash('success', 'Wisata berhasil disimpan.');
        } else {
            flash('error', 'Gagal menyimpan wisata.');
            redirect('/dashboard/wisata/form');
        }
    }

    foreach ($galeriBaru as $bagian) {
        $up = handleUpload($bagian, 'wisata', $nama);
        if ($up['ok']) {
            addWisataImage($editId, $up['path']);
        } else {
            flash('error', 'Galeri: ' . $up['error']);
        }
    }

    $hapusFasilitas = array_map('intval', (array) ($_POST['hapus_fasilitas'] ?? []));
    foreach ($hapusFasilitas as $fid) {
        if ($fid > 0) {
            deleteWisataFasilitas($editId, $fid);
        }
    }

    foreach ((array) ($_POST['fas_lama'] ?? []) as $fid => $row) {
        if (!is_array($row) || (int) $fid <= 0) {
            continue;
        }
        updateWisataFasilitas(
            $editId,
            (int) $fid,
            trim((string) ($row['ikon'] ?? 'eco')) !== '' ? trim((string) $row['ikon']) : 'eco',
            trim((string) ($row['judul'] ?? '')),
            trim((string) ($row['deskripsi'] ?? '')),
            (int) ($row['urutan'] ?? 0)
        );
    }

    foreach ((array) ($_POST['fas_baru'] ?? []) as $row) {
        if (!is_array($row)) {
            continue;
        }
        $fJudul = trim((string) ($row['judul'] ?? ''));
        if ($fJudul === '') {
            continue;
        }
        saveWisataFasilitas(
            $editId,
            trim((string) ($row['ikon'] ?? 'eco')) !== '' ? trim((string) $row['ikon']) : 'eco',
            $fJudul,
            trim((string) ($row['deskripsi'] ?? '')),
            (int) ($row['urutan'] ?? 0)
        );
    }

    redirect('/dashboard/wisata');
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

<form id="wisata-form" method="post" action="<?= APP_BASE ?>/dashboard/wisata/form<?= $editId > 0 ? '?id=' . $editId : '' ?>" enctype="multipart/form-data">
<?= csrfField() ?>
<div class="grid grid-cols-12 gap-gutter">
<div class="col-span-12 xl:col-span-8 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md relative z-10">
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="nama">Nama Wisata</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="nama" name="nama" placeholder="Contoh: Curug Embun, Kebun Teh..." required type="text" value="<?= $v('nama') ?>"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="slug">Slug URL</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-label-mono font-label-mono text-on-surface-variant focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="slug" name="slug" placeholder="otomatis dari nama" type="text" value="<?= $v('slug') ?>"/>
</div>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="deskripsi">Deskripsi</label>
<textarea class="w-full min-h-[200px] bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50 resize-y" id="deskripsi" name="deskripsi" placeholder="Ceritakan tentang wisata ini: keindahan alam, fasilitas, akses, dll..." required><?= $v('deskripsi') ?></textarea>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="alamat">Alamat / Lokasi</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="alamat" name="alamat" placeholder="Contoh: Dusun II, Pekon Padang Cermin" type="text" value="<?= $v('alamat') ?>"/>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="maps-wisata">Peta Google Maps <span class="text-on-surface-variant/50 normal-case">(opsional)</span></label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50"
       id="maps-wisata" name="maps_embed_url" type="text"
       placeholder="https://maps.app.goo.gl/... atau paste kode &lt;iframe&gt;"
       value="<?= e($wisata['maps_embed_url'] ?? '') ?>"/>
<p class="text-caption font-caption text-on-surface-variant m-0">Paste link share (<code class="text-primary">goo.gl/...</code>) atau kode <code class="text-primary">&lt;iframe&gt;</code> — sistem otomatis ekstrak URL.</p>
</div>
<?php
$wMapsVal = trim((string) ($wisata['maps_embed_url'] ?? ''));
$wMapsIsEmbed = str_contains($wMapsVal, 'google.com/maps/embed');
$wMapsIsShort = !$wMapsIsEmbed && ($wMapsVal !== '') && (str_contains($wMapsVal, 'goo.gl') || str_contains($wMapsVal, 'maps.google') || str_contains($wMapsVal, 'maps.app.goo.gl'));
?>
<div id="wisata-maps-preview" class="<?= empty($wMapsVal) ? 'hidden' : '' ?>">
<div id="wisata-maps-iframe-wrap" class="overflow-hidden rounded-xl <?= $wMapsIsShort ? 'hidden' : '' ?>">
<iframe id="wisata-maps-iframe" class="w-full h-48 border-0 rounded-xl"
        src="<?= $wMapsIsEmbed ? e($wMapsVal) : '' ?>"
        allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
</div>
<a id="wisata-maps-link" href="<?= e($wMapsIsShort ? $wMapsVal : '#') ?>" target="_blank" rel="noopener"
   class="<?= $wMapsIsShort ? '' : 'hidden' ?> mt-2 inline-flex items-center gap-2 bg-surface-container border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-primary hover:bg-surface-container-highest transition-colors">
<span class="material-symbols-outlined text-[20px]">open_in_new</span>
Buka di Google Maps
</a>
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
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="wa_kontak">WhatsApp Kontak <span class="text-on-surface-variant/50 normal-case">(untuk pesan tiket)</span></label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="wa_kontak" name="wa_kontak" placeholder="6285173200421" type="tel" value="<?= $v('wa_kontak') ?>"/>
<p class="text-caption font-caption text-on-surface-variant m-0">Format internasional, angka saja (contoh: 6285173200421).</p>
</div>
<div class="flex flex-col gap-2 relative z-10">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Status</span>
<?php $statusAktif = $v('status') === 'publish' ? 'publish' : 'draft'; ?>
<div class="flex gap-2" data-status-control>
<label class="flex-1 cursor-pointer" data-status-option="draft">
<input class="sr-only" name="status" type="radio" value="draft" <?= $statusAktif === 'draft' ? 'checked' : '' ?>/>
<div class="px-4 py-3 rounded-xl border text-center text-caption font-caption transition-all <?= $statusAktif === 'draft' ? 'border-primary bg-primary text-on-primary shadow-lime-glow' : 'border-glass-border bg-surface-container-highest text-on-surface-variant' ?>" data-status-label>Draft</div>
</label>
<label class="flex-1 cursor-pointer" data-status-option="publish">
<input class="sr-only" name="status" type="radio" value="publish" <?= $statusAktif === 'publish' ? 'checked' : '' ?>/>
<div class="px-4 py-3 rounded-xl border text-center text-caption font-caption transition-all <?= $statusAktif === 'publish' ? 'border-primary bg-primary text-on-primary shadow-lime-glow' : 'border-glass-border bg-surface-container-highest text-on-surface-variant' ?>" data-status-label>Publish</div>
</label>
</div>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="flex items-center gap-3 relative z-10">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">deck</span>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Aktivitas &amp; Fasilitas</h2>
</div>
<div id="fas-list" class="flex flex-col gap-3 relative z-10">
<?php foreach ($fasilitasList as $f): ?>
<div class="fas-row flex flex-col gap-2 p-3 rounded-xl border border-glass-border bg-surface-container-highest/60">
<div class="flex gap-2 items-center">
<select name="fas_lama[<?= (int) $f['id'] ?>][ikon]" class="flex-1 min-w-0 bg-surface-container-highest border border-glass-border rounded-lg px-2 py-2 text-caption font-caption text-on-surface focus:outline-none focus:border-primary transition-all">
<?php foreach ($ikonFasilitas as $ikon): ?>
<option value="<?= e($ikon) ?>" <?= $f['ikon'] === $ikon ? 'selected' : '' ?>><?= e($ikon) ?></option>
<?php endforeach; ?>
</select>
<input name="fas_lama[<?= (int) $f['id'] ?>][urutan]" type="number" min="0" value="<?= (int) $f['urutan'] ?>" class="w-16 bg-surface-container-highest border border-glass-border rounded-lg px-2 py-2 text-caption font-caption text-on-surface focus:outline-none focus:border-primary transition-all" title="Urutan"/>
<label class="flex items-center gap-1 cursor-pointer shrink-0" title="Hapus fasilitas">
<input type="checkbox" name="hapus_fasilitas[]" value="<?= (int) $f['id'] ?>" class="peer sr-only"/>
<span class="material-symbols-outlined text-on-surface-variant peer-checked:text-red-400 text-[20px]">delete</span>
</label>
</div>
<input name="fas_lama[<?= (int) $f['id'] ?>][judul]" type="text" value="<?= e($f['judul']) ?>" placeholder="Judul fasilitas" class="w-full bg-surface-container-highest border border-glass-border rounded-lg px-3 py-2 text-caption font-caption text-on-surface focus:outline-none focus:border-primary transition-all"/>
<textarea name="fas_lama[<?= (int) $f['id'] ?>][deskripsi]" rows="2" placeholder="Deskripsi singkat" class="w-full bg-surface-container-highest border border-glass-border rounded-lg px-3 py-2 text-caption font-caption text-on-surface focus:outline-none focus:border-primary transition-all resize-y"><?= e($f['deskripsi']) ?></textarea>
</div>
<?php endforeach; ?>
</div>
<div id="fas-new-wrap" class="flex flex-col gap-3 relative z-10"></div>
<button type="button" id="fas-add-btn" class="relative z-10 w-full flex items-center justify-center gap-2 border border-dashed border-primary/40 rounded-xl px-4 py-3 text-caption font-caption text-primary hover:bg-primary/10 transition-colors">
<span class="material-symbols-outlined text-[18px]">add</span> Tambah Fasilitas
</button>
<template id="fas-template">
<div class="fas-row flex flex-col gap-2 p-3 rounded-xl border border-glass-border bg-surface-container-highest/60">
<div class="flex gap-2 items-center">
<select name="fas_baru[__INDEX__][ikon]" class="flex-1 min-w-0 bg-surface-container-highest border border-glass-border rounded-lg px-2 py-2 text-caption font-caption text-on-surface focus:outline-none focus:border-primary transition-all">
<?php foreach ($ikonFasilitas as $ikon): ?>
<option value="<?= e($ikon) ?>"><?= e($ikon) ?></option>
<?php endforeach; ?>
</select>
<input name="fas_baru[__INDEX__][urutan]" type="number" min="0" value="0" class="w-16 bg-surface-container-highest border border-glass-border rounded-lg px-2 py-2 text-caption font-caption text-on-surface focus:outline-none focus:border-primary transition-all" title="Urutan"/>
<button type="button" data-fas-hapus class="shrink-0 text-on-surface-variant hover:text-red-400 transition-colors" title="Buang baris">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<input name="fas_baru[__INDEX__][judul]" type="text" placeholder="Judul fasilitas" class="w-full bg-surface-container-highest border border-glass-border rounded-lg px-3 py-2 text-caption font-caption text-on-surface focus:outline-none focus:border-primary transition-all"/>
<textarea name="fas_baru[__INDEX__][deskripsi]" rows="2" placeholder="Deskripsi singkat" class="w-full bg-surface-container-highest border border-glass-border rounded-lg px-3 py-2 text-caption font-caption text-on-surface focus:outline-none focus:border-primary transition-all resize-y"></textarea>
</div>
</template>
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
<?php if (fotoAda($g['path_gambar'])): ?>
    <img class="w-full h-24 object-cover block" alt="Galeri <?= e($wisata['nama']) ?>" src="<?= uploadUrl($g['path_gambar']) ?>"/>
<?php else: ?>
    <div class="w-full h-24 flex items-center justify-center bg-gradient-to-br from-primary/70 to-primary/30">
        <span class="text-[20px] font-bold text-white"><?= e(inisialNama($wisata['nama'])) ?></span>
    </div>
<?php endif; ?>
    <div class="absolute inset-0 bg-black/55 opacity-0 group-hover/thumb:opacity-100 transition-opacity flex items-center justify-center gap-2 pointer-events-none group-hover/thumb:pointer-events-auto">
        <?php if (fotoAda($g['path_gambar'])): ?>
        <button type="button"
                class="w-9 h-9 rounded-full bg-white/15 hover:bg-white/30 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white transition-all"
                title="Lihat gambar"
                data-gallery-preview
                data-src="<?= e(uploadUrl($g['path_gambar'])) ?>"
                data-alt="Galeri <?= e($wisata['nama']) ?>">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1">zoom_in</span>
        </button>
        <?php endif; ?>
        <button type="button"
                class="w-9 h-9 rounded-full bg-red-500/70 hover:bg-red-500 backdrop-blur-sm border border-red-400/30 flex items-center justify-center text-white transition-all"
                title="Hapus gambar"
                data-gallery-delete
                data-gambar-id="<?= (int) $g['id'] ?>">
            <span class="material-symbols-outlined text-[18px]">delete</span>
        </button>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="galeri">Tambah Gambar (bisa banyak)</label>
<input class="w-full text-caption font-caption text-on-surface-variant file:mr-4 file:rounded-xl file:border-0 file:bg-surface-container-highest file:px-4 file:py-2.5 file:text-on-surface file:cursor-pointer hover:file:bg-surface-container transition-colors" id="galeri" name="galeri[]" type="file" accept="image/jpeg,image/png,image/webp,image/gif" multiple/>
<p class="text-[11px] text-on-surface-variant m-0">Max 2 MB per file · JPG, PNG, WEBP · Kosongkan jika tidak ganti gambar</p>
</div>
<div id="galeri-preview-wrap" class="hidden relative z-10">
<div id="galeri-preview-grid" class="grid grid-cols-3 gap-2"></div>
<p class="text-[11px] text-hijau mt-1">Preview gambar yang akan diupload</p>
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

(function() {
    var control = document.querySelector('[data-status-control]');
    if (!control) return;
    var activeClasses = ['border-primary', 'bg-primary', 'text-on-primary', 'shadow-lime-glow'];
    var inactiveClasses = ['border-glass-border', 'bg-surface-container-highest', 'text-on-surface-variant'];
    function syncStatus() {
        control.querySelectorAll('[data-status-option]').forEach(function (option) {
            var input = option.querySelector('input[type="radio"]');
            var label = option.querySelector('[data-status-label]');
            if (!input || !label) return;
            var add = input.checked ? activeClasses : inactiveClasses;
            var remove = input.checked ? inactiveClasses : activeClasses;
            label.classList.remove.apply(label.classList, remove);
            label.classList.add.apply(label.classList, add);
        });
    }
    control.addEventListener('change', syncStatus);
    syncStatus();
})();

(function () {
    var input = document.getElementById('galeri');
    var wrap  = document.getElementById('galeri-preview-wrap');
    var grid  = document.getElementById('galeri-preview-grid');
    if (!input || !wrap || !grid) return;
    input.addEventListener('change', function () {
        grid.innerHTML = '';
        var files = Array.from(this.files || []);
        if (!files.length) { wrap.classList.add('hidden'); return; }
        wrap.classList.remove('hidden');
        files.forEach(function (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Preview';
                img.className = 'w-full h-20 object-cover rounded-lg border border-primary/30';
                grid.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
})();

(function() {
    var CSS = {
        overlay: 'position:fixed;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.85);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);',
        overlayConfirm: 'position:fixed;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.72);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);',
        lbWrap: 'position:relative;max-width:92vw;max-height:92vh;display:flex;flex-direction:column;align-items:center;',
        lbClose: 'position:absolute;top:-40px;right:0;width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;color:#fff;cursor:pointer;transition:background .2s;',
        lbImg: 'max-width:100%;max-height:85vh;object-fit:contain;border-radius:12px;box-shadow:0 25px 50px rgba(0,0,0,0.5);display:block;',
        confirmBox: 'background:var(--color-surface-container-highest,#182b21);border:1px solid rgba(255,255,255,0.1);border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.5);padding:24px;max-width:360px;width:calc(100% - 32px);display:flex;flex-direction:column;gap:20px;',
        confirmHeader: 'display:flex;align-items:center;gap:12px;',
        confirmIcon: 'width:40px;height:40px;border-radius:10px;background:rgba(239,68,68,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;',
        confirmTitle: 'font-size:15px;font-weight:600;color:var(--color-on-surface,#e2ede7);line-height:1.3;margin:0;',
        confirmDesc: 'font-size:13px;color:var(--color-on-surface-variant,#9aab9e);margin:4px 0 0;',
        confirmFooter: 'display:flex;gap:10px;justify-content:flex-end;',
        btnCancel: 'padding:8px 16px;border-radius:10px;border:1px solid rgba(255,255,255,0.12);color:var(--color-on-surface-variant,#9aab9e);background:transparent;font-size:13px;cursor:pointer;',
        btnDelete: 'padding:8px 16px;border-radius:10px;border:none;background:#ef4444;color:#fff;font-size:13px;font-weight:500;cursor:pointer;',
    };
    function openLightbox(src, alt) {
        var overlay = document.createElement('div'); overlay.setAttribute('style', CSS.overlay);
        var wrap = document.createElement('div'); wrap.setAttribute('style', CSS.lbWrap);
        var closeBtn = document.createElement('button'); closeBtn.type = 'button'; closeBtn.setAttribute('style', CSS.lbClose);
        closeBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px;line-height:1">close</span>';
        var img = document.createElement('img'); img.src = src; img.alt = alt; img.setAttribute('style', CSS.lbImg);
        wrap.appendChild(closeBtn); wrap.appendChild(img); overlay.appendChild(wrap); document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        function closeLb() { overlay.remove(); document.body.style.overflow = ''; document.removeEventListener('keydown', onKey); }
        function onKey(e) { if (e.key === 'Escape') closeLb(); }
        document.addEventListener('keydown', onKey);
        overlay.addEventListener('click', function(e) { if (e.target === overlay) closeLb(); });
        closeBtn.addEventListener('click', closeLb);
    }
    function openConfirmDelete(gambarId) {
        var overlay = document.createElement('div'); overlay.setAttribute('style', CSS.overlayConfirm);
        var box = document.createElement('div'); box.setAttribute('style', CSS.confirmBox);
        var header = document.createElement('div'); header.setAttribute('style', CSS.confirmHeader);
        var iconWrap = document.createElement('div'); iconWrap.setAttribute('style', CSS.confirmIcon);
        iconWrap.innerHTML = '<span class="material-symbols-outlined" style="color:#f87171;font-size:22px;font-variation-settings:\'FILL\' 1">delete</span>';
        var textWrap = document.createElement('div');
        var title = document.createElement('p'); title.setAttribute('style', CSS.confirmTitle); title.textContent = 'Hapus Gambar';
        var desc = document.createElement('p'); desc.setAttribute('style', CSS.confirmDesc); desc.textContent = 'Gambar yang dihapus tidak dapat dikembalikan.';
        textWrap.appendChild(title); textWrap.appendChild(desc);
        header.appendChild(iconWrap); header.appendChild(textWrap);
        var footer = document.createElement('div'); footer.setAttribute('style', CSS.confirmFooter);
        var btnCancel = document.createElement('button'); btnCancel.type = 'button'; btnCancel.setAttribute('style', CSS.btnCancel); btnCancel.textContent = 'Batal';
        var btnOk = document.createElement('button'); btnOk.type = 'button'; btnOk.setAttribute('style', CSS.btnDelete); btnOk.textContent = 'Hapus';
        footer.appendChild(btnCancel); footer.appendChild(btnOk);
        box.appendChild(header); box.appendChild(footer); overlay.appendChild(box); document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        function closeConfirm() { overlay.remove(); document.body.style.overflow = ''; document.removeEventListener('keydown', onKey); }
        function onKey(e) { if (e.key === 'Escape') closeConfirm(); }
        document.addEventListener('keydown', onKey);
        overlay.addEventListener('click', function(e) { if (e.target === overlay) closeConfirm(); });
        btnCancel.addEventListener('click', closeConfirm);
        btnOk.addEventListener('click', function() {
            var form = document.getElementById('wisata-form');
            if (!form) return;
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'hapus_gambar';
            input.value = gambarId;
            form.appendChild(input);
            form.submit();
        });
    }
    document.addEventListener('click', function(e) {
        var previewBtn = e.target.closest('[data-gallery-preview]');
        if (previewBtn) { openLightbox(previewBtn.dataset.src, previewBtn.dataset.alt || ''); return; }
        var deleteBtn = e.target.closest('[data-gallery-delete]');
        if (deleteBtn) { openConfirmDelete(deleteBtn.dataset.gambarId); }
    });
})();

(function() {
    var addBtn = document.getElementById('fas-add-btn');
    var tpl = document.getElementById('fas-template');
    var wrap = document.getElementById('fas-new-wrap');
    var index = 0;
    if (addBtn && tpl && wrap) {
        addBtn.addEventListener('click', function () {
            var row = tpl.content.firstElementChild.cloneNode(true);
            row.querySelectorAll('[name]').forEach(function (field) {
                field.name = field.name.replace(/__INDEX__/g, String(index));
            });
            index += 1;
            wrap.appendChild(row);
            row.scrollIntoView({ block: 'nearest' });
        });
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-fas-hapus]');
            if (btn && wrap.contains(btn)) {
                btn.closest('.fas-row').remove();
            }
        });
    }
})();

(function() {
    var mapsInput   = document.getElementById('maps-wisata');
    var mapsPreview = document.getElementById('wisata-maps-preview');
    var iframeWrap  = document.getElementById('wisata-maps-iframe-wrap');
    var mapsIframe  = document.getElementById('wisata-maps-iframe');
    var mapsLink    = document.getElementById('wisata-maps-link');
    if (!mapsInput) return;
    function classifyUrl(raw) {
        var m = raw.match(/src=["']([^"']+)["']/i);
        var url = m ? m[1].trim() : raw.trim();
        if (!url) return { url: '', type: 'empty' };
        if (url.includes('google.com/maps/embed')) return { url: url, type: 'embed' };
        if (url.includes('goo.gl') || url.includes('maps.google') || url.includes('maps.app.goo.gl')) return { url: url, type: 'short' };
        return { url: url, type: 'unknown' };
    }
    function updateMapsPreview() {
        var val = mapsInput.value.trim();
        var result = classifyUrl(val);
        mapsInput.dataset.cleanUrl = result.url;
        if (result.type === 'embed') {
            if (mapsIframe) mapsIframe.src = result.url;
            if (iframeWrap) iframeWrap.classList.remove('hidden');
            if (mapsLink) mapsLink.classList.add('hidden');
            if (mapsPreview) mapsPreview.classList.remove('hidden');
        } else if (result.type === 'short') {
            if (iframeWrap) iframeWrap.classList.add('hidden');
            if (mapsLink) { mapsLink.href = result.url; mapsLink.classList.remove('hidden'); }
            if (mapsPreview) mapsPreview.classList.remove('hidden');
        } else {
            if (mapsPreview) mapsPreview.classList.add('hidden');
        }
    }
    mapsInput.addEventListener('input', updateMapsPreview);
    updateMapsPreview();
    var form = mapsInput.closest('form');
    if (form) {
        form.addEventListener('submit', function() {
            if (mapsInput.dataset.cleanUrl) mapsInput.value = mapsInput.dataset.cleanUrl;
        });
    }
})();
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>
