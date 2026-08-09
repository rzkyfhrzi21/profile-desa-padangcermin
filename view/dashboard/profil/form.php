<?php
declare(strict_types=1);

$judulHalaman = 'Form Profil';

$profil = getProfil();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    $nama = trim((string) ($_POST['nama_pekon'] ?? ''));
    $visi = trim((string) ($_POST['visi'] ?? ''));
    $misi = trim((string) ($_POST['misi'] ?? ''));
    $sambutan = trim((string) ($_POST['sambutan_kepala_pekon'] ?? ''));
    $alamat = trim((string) ($_POST['alamat_kantor'] ?? ''));
    $latitude = trim((string) ($_POST['latitude'] ?? ''));
    $longitude = trim((string) ($_POST['longitude'] ?? ''));
    $mapsUrl = trim((string) ($_POST['maps_embed_url'] ?? ''));
    if (str_contains($mapsUrl, '<iframe')) {
        preg_match('/src=["\']([^"\']+)["\']/', $mapsUrl, $m);
        $mapsUrl = $m[1] ?? '';
    }
    $telepon = trim((string) ($_POST['telepon'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $whatsapp = trim((string) ($_POST['whatsapp'] ?? ''));
    $altFoto = trim((string) ($_POST['alt_foto'] ?? ''));
    $fotoLama = $profil['foto_kepala_pekon'] ?? null;

    $errors = [];
    if ($nama === '') {
        $errors[] = 'Nama pekon wajib diisi.';
    }
    if ($latitude !== '' && !is_numeric($latitude)) {
        $errors[] = 'Latitude harus berupa angka.';
    }
    if ($longitude !== '' && !is_numeric($longitude)) {
        $errors[] = 'Longitude harus berupa angka.';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    $fotoBaru = null;
    $adaFile = ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    if ($adaFile) {
        $up = handleUpload($_FILES['foto'], 'profil', $altFoto);
        if (!$up['ok']) {
            $errors[] = $up['error'];
        } else {
            $fotoBaru = $up['path'];
        }
    }

    if ($errors !== []) {
        foreach ($errors as $err) {
            flash('error', $err);
        }
        redirect('/dashboard/profil/form');
    }

    $data = [
        'nama_pekon' => $nama,
        'visi' => $visi,
        'misi' => $misi,
        'sambutan_kepala_pekon' => $sambutan,
        'alamat_kantor' => $alamat,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'maps_embed_url' => $mapsUrl,
        'telepon' => $telepon,
        'email' => $email,
        'whatsapp' => $whatsapp,
    ];

    if (updateProfil($data)) {
        if ($fotoBaru !== null) {
            updateFotoKepalaPekon($fotoBaru);
            if ($fotoLama !== null && $fotoLama !== '' && $fotoLama !== $fotoBaru) {
                $fileLama = UPLOAD_PATH . '/' . $fotoLama;
                if (is_file($fileLama)) {
                    @unlink($fileLama);
                }
            }
            catatLog('update profil pekon (termasuk foto kepala pekon)', 'profil_desa', 1);
        } else {
            catatLog('update profil pekon', 'profil_desa', 1);
        }
        flash('success', 'Profil pekon berhasil diperbarui.');
    } else {
        flash('error', 'Gagal memperbarui profil pekon.');
    }
    redirect('/dashboard/profil');
}

$v = static function (string $field) use ($profil): string {
    return e(trim((string) ($_POST[$field] ?? ($profil[$field] ?? ''))));
};

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 md:mb-section-gap gap-4 md:gap-0">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Identitas Pekon</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0">Edit Profil Pekon</h1>
</div>
<a class="text-caption font-caption text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="<?= APP_BASE ?>/dashboard/profil">
<span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke profil
</a>
</div>

<form method="post" action="<?= APP_BASE ?>/dashboard/profil/form" enctype="multipart/form-data">
<?= csrfField() ?>
<div class="grid grid-cols-12 gap-gutter">
<div class="col-span-12 xl:col-span-8 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="flex items-center gap-3 relative z-10">
<div class="w-10 h-10 rounded-xl bg-surface-container border border-glass-border flex items-center justify-center text-primary">
<span class="material-symbols-outlined">home_work</span>
</div>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Identitas Pekon</h2>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="nama_pekon">Nama Pekon</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="nama_pekon" name="nama_pekon" placeholder="Contoh: Pekon Padang Cermin" required type="text" value="<?= $v('nama_pekon') ?>"/>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="alamat_kantor">Alamat Kantor Pekon</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="alamat_kantor" name="alamat_kantor" placeholder="Jl. Raya Lintas Barat, Pekon Padang Cermin, Kec. Way Khilau, Pesawaran" type="text" value="<?= $v('alamat_kantor') ?>"/>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="flex items-center gap-3 relative z-10">
<div class="w-10 h-10 rounded-xl bg-surface-container border border-glass-border flex items-center justify-center text-primary">
<span class="material-symbols-outlined">flag</span>
</div>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Visi &amp; Misi</h2>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="visi">Visi Pekon</label>
<textarea class="w-full min-h-[100px] bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50 resize-y" id="visi" name="visi" placeholder="Tuliskan visi pekon..." rows="4"><?= $v('visi') ?></textarea>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="misi">Misi Pekon</label>
<textarea class="w-full min-h-[150px] bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50 resize-y" id="misi" name="misi" placeholder="1. Meningkatkan kesejahteraan ekonomi...&#10;2. Membangun infrastruktur pekon..." rows="5"><?= $v('misi') ?></textarea>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="flex items-center gap-3 relative z-10">
<div class="w-10 h-10 rounded-xl bg-surface-container border border-glass-border flex items-center justify-center text-primary">
<span class="material-symbols-outlined">campaign</span>
</div>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Sambutan Kepala Pekon</h2>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="sambutan_kepala_pekon">Teks Sambutan</label>
<textarea class="w-full min-h-[200px] bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50 resize-y" id="sambutan_kepala_pekon" name="sambutan_kepala_pekon" placeholder="Tuliskan sambutan kepala pekon..." rows="6"><?= $v('sambutan_kepala_pekon') ?></textarea>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="flex items-center gap-3 relative z-10">
<div class="w-10 h-10 rounded-xl bg-surface-container border border-glass-border flex items-center justify-center text-primary">
<span class="material-symbols-outlined">contact_phone</span>
</div>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Kontak</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md relative z-10">
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="telepon">Telepon</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="telepon" name="telepon" placeholder="(0721) 123456" type="tel" value="<?= $v('telepon') ?>"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="whatsapp">WhatsApp</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="whatsapp" name="whatsapp" placeholder="0812 3456 7890" type="tel" value="<?= $v('whatsapp') ?>"/>
</div>
<div class="flex flex-col gap-2 md:col-span-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="email">Email Resmi</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="email" name="email" placeholder="admin@padangcermin.go.id" type="email" value="<?= $v('email') ?>"/>
</div>
</div>
</div>
</div>

<div class="col-span-12 xl:col-span-4 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="flex items-center gap-3 relative z-10">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">photo_camera</span>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Foto Kepala Pekon</h2>
</div>
<?php if (!empty($profil['foto_kepala_pekon'])): ?>
<div class="relative z-10">
<img class="w-full aspect-[3/4] object-cover rounded-xl border border-glass-border" alt="Foto Kepala Pekon saat ini" src="<?= uploadUrl($profil['foto_kepala_pekon']) ?>"/>
</div>
<?php endif; ?>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="foto">Unggah Foto</label>
<input class="w-full text-caption font-caption text-on-surface-variant file:mr-4 file:rounded-xl file:border-0 file:bg-surface-container-highest file:px-4 file:py-2.5 file:text-on-surface file:cursor-pointer hover:file:bg-surface-container transition-colors" id="foto" name="foto" type="file" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif,image/heic,image/heif,image/x-icon"/>
<div id="foto-preview-container" class="relative z-10 mt-2 <?= !empty($profil['foto_kepala_pekon']) ? '' : 'hidden' ?>">
    <img id="foto-preview" class="w-full aspect-[3/4] object-cover rounded-xl border border-glass-border" 
         alt="Preview foto" 
         src="<?= !empty($profil['foto_kepala_pekon']) ? uploadUrl($profil['foto_kepala_pekon']) : '' ?>"/>
</div>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="alt_foto">Alt Text (wajib)</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-caption font-caption text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="alt_foto" name="alt_foto" placeholder="Deskripsi singkat foto untuk aksesibilitas & SEO" type="text" value="<?= e(trim((string) ($_POST['alt_foto'] ?? ''))) ?>"/>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="flex items-center gap-3 relative z-10">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">location_on</span>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Peta Lokasi</h2>
</div>
<div class="flex flex-col gap-2 relative z-10">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="maps_embed_url">URL Google Maps</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" 
       id="maps_embed_url" name="maps_embed_url" 
       placeholder="https://maps.app.goo.gl/... atau paste kode iframe" 
       type="text" 
       value="<?= e($profil['maps_embed_url'] ?? '') ?>"/>
<p class="text-caption font-caption text-on-surface-variant m-0">Paste link Google Maps share atau kode embed &lt;iframe&gt; — sistem otomatis ekstrak URL embed-nya.</p>
</div>
<div id="maps-preview" class="relative z-10 overflow-hidden rounded-xl <?= empty($profil['maps_embed_url']) ? 'hidden' : '' ?>">
<iframe id="maps-iframe" class="w-full h-56 border-0 rounded-xl" 
        src="<?= e($profil['maps_embed_url'] ?? '') ?>" 
        allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex items-center justify-between gap-4">
<div class="flex flex-col gap-1">
<span class="text-caption font-caption text-on-surface-variant">Perubahan langsung tampil di portal publik.</span>
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest text-[11px]">Profil Pekon Padang Cermin</span>
</div>
<button class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 whitespace-nowrap" type="submit">
<span class="material-symbols-outlined text-[20px]">check</span>
Simpan Perubahan
</button>
</div>
</div>
</div>
</form>
</section>
<script>
document.getElementById('foto').addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(ev) {
        var img = document.getElementById('foto-preview');
        var container = document.getElementById('foto-preview-container');
        if (img) img.src = ev.target.result;
        if (container) container.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
});
(function() {
    var input = document.getElementById('maps_embed_url');
    var preview = document.getElementById('maps-preview');
    var iframe = document.getElementById('maps-iframe');
    if (!input) return;
    function updatePreview() {
        var val = input.value.trim();
        // Try to extract src from iframe HTML
        var m = val.match(/src=["']([^"']+)["']/i);
        var url = m ? m[1] : val;
        // Store clean URL in the input value before form submit
        input.dataset.cleanUrl = url;
        if (url && url.includes('google.com/maps')) {
            if (iframe) iframe.src = url;
            if (preview) preview.classList.remove('hidden');
        } else {
            if (preview) preview.classList.add('hidden');
        }
    }
    input.addEventListener('input', updatePreview);
    updatePreview();
    // On form submit, clean up the value if it was an iframe paste
    var form = input.closest('form');
    if (form) {
        form.addEventListener('submit', function() {
            if (input.dataset.cleanUrl) input.value = input.dataset.cleanUrl;
        });
    }
})();
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>
