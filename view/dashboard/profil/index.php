<?php
declare(strict_types=1);

$judulHalaman = 'Manajemen Profil';

$profil = getProfil();

/* Ambil data Kepala Desa dari tabel struktur (parent_id = NULL, urutan terendah) */
$db = getDb();
$stmtKades = $db->query("SELECT * FROM struktur_organisasi WHERE parent_id IS NULL ORDER BY urutan ASC LIMIT 1");
$kades = $stmtKades->fetch() ?: null;

$fieldsKelengkapan = ['nama_pekon', 'visi', 'misi', 'sambutan_kepala_pekon', 'alamat_kantor', 'telepon', 'email', 'whatsapp'];
$terisi = 0;
foreach ($fieldsKelengkapan as $f) {
    if (trim((string) ($profil[$f] ?? '')) !== '') {
        $terisi++;
    }
}
$persenKelengkapan = (int) round($terisi / count($fieldsKelengkapan) * 100);

$tampil = static function (string $field) use ($profil): string {
    $nilai = trim((string) ($profil[$field] ?? ''));
    return $nilai === '' ? '-' : e($nilai);
};

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 md:mb-section-gap gap-4 md:gap-0">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Identitas Pekon</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0">Manajemen Profil &amp; Identitas</h1>
<p class="text-body-md font-body-md text-on-surface-variant mt-3 max-w-2xl">Kelola identitas inti Pekon Padang Cermin. Perubahan yang disimpan akan langsung diperbarui pada portal publik desa.</p>
</div>
<a class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 group whitespace-nowrap" href="<?= APP_BASE ?>/dashboard/profil/form">
<span class="material-symbols-outlined text-[20px] transition-transform group-hover:scale-110">edit</span>
Edit Profil
</a>
</div>

<div class="grid grid-cols-12 gap-gutter relative z-10">
<div class="col-span-12 xl:col-span-8 flex flex-col gap-stack-lg">
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="flex items-center gap-3 relative z-10">
<div class="w-10 h-10 rounded-xl bg-surface-container border border-glass-border flex items-center justify-center text-primary">
<span class="material-symbols-outlined">home_work</span>
</div>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Identitas Pekon</h2>
</div>
<div class="relative z-10 divide-y divide-glass-border/50">
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-1 py-4">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Nama Pekon</span>
<span class="text-body-md font-body-md text-on-surface text-right"><?= $tampil('nama_pekon') ?></span>
</div>
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-1 py-4">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Alamat Kantor</span>
<span class="text-body-md font-body-md text-on-surface text-right max-w-md"><?= $tampil('alamat_kantor') ?></span>
</div>
<div class="flex flex-col gap-2 py-4">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Visi</span>
<p class="text-body-md font-body-md text-on-surface m-0 leading-relaxed"><?= $tampil('visi') ?></p>
</div>
<div class="flex flex-col gap-2 py-4">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Misi</span>
<p class="text-body-md font-body-md text-on-surface m-0 leading-relaxed whitespace-pre-line"><?= $tampil('misi') ?></p>
</div>
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
<div class="relative z-10">
<p class="text-body-md font-body-md text-on-surface-variant m-0 leading-relaxed whitespace-pre-line"><?= $tampil('sambutan_kepala_pekon') ?></p>
</div>
</div>

<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3"></div>
<div class="flex items-center gap-3 relative z-10">
<div class="w-10 h-10 rounded-xl bg-surface-container border border-glass-border flex items-center justify-center text-primary">
<span class="material-symbols-outlined">location_on</span>
</div>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Lokasi &amp; Kontak</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-x-gutter gap-y-2 relative z-10 divide-y md:divide-y-0 md:divide-x divide-glass-border/50">
<div class="flex flex-col gap-1 py-4 md:py-0 md:pr-gutter md:col-span-2">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Google Maps</span>
<?php
$mapsVal = trim((string) ($profil['maps_embed_url'] ?? ''));
if ($mapsVal !== ''):
?>
<a href="<?= e($mapsVal) ?>" target="_blank" rel="noopener" class="text-body-md font-body-md text-primary hover:underline break-all"><?= e(mb_strlen($mapsVal) > 60 ? mb_substr($mapsVal, 0, 60) . '…' : $mapsVal) ?></a>
<?php else: ?>
<span class="text-body-md font-body-md text-on-surface">-</span>
<?php endif; ?>
</div>
<div class="flex flex-col gap-1 py-4 md:py-0 md:pr-gutter">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Telepon</span>
<span class="text-body-md font-body-md text-on-surface"><?= $tampil('telepon') ?></span>
</div>
<div class="flex flex-col gap-1 py-4 md:py-0 md:px-gutter">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Email</span>
<span class="text-body-md font-body-md text-on-surface"><?= $tampil('email') ?></span>
</div>
<div class="flex flex-col gap-1 py-4 md:py-0 md:pr-gutter">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">WhatsApp</span>
<span class="text-body-md font-body-md text-on-surface"><?= $tampil('whatsapp') ?></span>
</div>
</div>
</div>
</div>

<div class="col-span-12 xl:col-span-4 flex flex-col gap-stack-lg">
<div class="bg-surface-container rounded-[20px] p-4 md:p-stack-lg relative overflow-hidden group">
<div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
<div class="flex items-center gap-3 mb-6 relative z-10">
<span class="material-symbols-outlined text-primary text-[28px]" style="font-variation-settings: 'FILL' 1;">photo_camera</span>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Foto Kepala Pekon</h2>
</div>
<div class="relative z-10">
<?php
/* Prioritas foto: dari tabel struktur (kepala desa), fallback ke profil */
$fotoKades = null;
$namaKades = '-';
$jabatanKades = 'Kepala Pekon';
if ($kades !== null) {
    $namaKades    = $kades['nama'];
    $jabatanKades = $kades['jabatan'];
    $fotoKades    = !empty($kades['foto']) ? uploadUrl($kades['foto']) : null;
}
/* Fallback ke foto_kepala_pekon dari tabel profil */
if ($fotoKades === null && !empty($profil['foto_kepala_pekon'])) {
    $fotoKades = uploadUrl($profil['foto_kepala_pekon']);
}
?>
<?php if ($fotoKades !== null): ?>
<div class="aspect-[3/4] rounded-xl overflow-hidden border border-glass-border relative group cursor-pointer">
<img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
     data-lightbox="<?= e($fotoKades) ?>"
     data-skeleton
     alt="Foto <?= e($namaKades) ?>"
     src="<?= e($fotoKades) ?>"/>
<div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/40">
<span class="material-symbols-outlined text-white text-[32px]">zoom_in</span>
</div>
</div>
<div class="mt-3 text-center">
<p class="text-body-md font-semibold text-on-surface"><?= e($namaKades) ?></p>
<p class="text-caption text-primary"><?= e($jabatanKades) ?></p>
<?php if ($kades !== null && !empty($kades['pendidikan_terakhir'])): ?>
<p class="text-[11px] text-on-surface-variant"><?= e($kades['pendidikan_terakhir']) ?></p>
<?php endif; ?>
<a href="<?= APP_BASE ?>/dashboard/struktur/form?id=<?= (int) ($kades['id'] ?? 0) ?>"
   class="mt-2 inline-flex items-center gap-1 text-[11px] text-primary/70 hover:text-primary transition-colors">
<span class="material-symbols-outlined text-[13px]">edit</span> Edit data kepala desa
</a>
</div>
<?php else: ?>
<div class="aspect-[3/4] rounded-xl border-2 border-dashed border-glass-border bg-surface-container-highest flex flex-col items-center justify-center gap-3">
<span class="material-symbols-outlined text-[40px] text-on-surface-variant/40">person</span>
<span class="text-caption font-caption text-on-surface-variant text-center px-4">
<?= $kades !== null ? 'Foto ' . e($kades['nama']) . ' belum diupload di modul Struktur' : 'Belum ada data Kepala Desa di modul Struktur' ?>
</span>
<?php if ($kades !== null): ?>
<a href="<?= APP_BASE ?>/dashboard/struktur/form?id=<?= (int) $kades['id'] ?>"
   class="px-4 py-2 rounded-xl bg-primary/10 border border-primary/30 text-caption text-primary hover:bg-primary/20 transition-all flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">add_photo_alternate</span> Upload Foto
</a>
<?php else: ?>
<a href="<?= APP_BASE ?>/dashboard/struktur/form"
   class="px-4 py-2 rounded-xl bg-primary/10 border border-primary/30 text-caption text-primary hover:bg-primary/20 transition-all flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">add</span> Tambah Kepala Desa
</a>
<?php endif; ?>
</div>
<?php endif; ?>
</div>
</div>

<div class="bg-surface-container rounded-[20px] p-4 md:p-stack-lg relative overflow-hidden">
<div class="flex items-center gap-3 mb-6 relative z-10">
<span class="material-symbols-outlined text-primary text-[28px]" style="font-variation-settings: 'FILL' 1;">insights</span>
<h2 class="text-headline-md font-headline-md text-on-surface m-0">Status Profil</h2>
</div>
<div class="relative z-10 flex flex-col gap-4">
<div class="flex flex-col gap-2 bg-background rounded-xl p-4 border border-glass-border/30">
<span class="text-label-mono font-label-mono text-on-surface-variant">Kelengkapan Profil</span>
<span class="text-headline-lg font-headline-lg text-primary"><?= $persenKelengkapan ?>%</span>
<div class="w-full h-2 bg-surface-container-highest rounded-full overflow-hidden">
<div class="h-full bg-primary rounded-full transition-all duration-500" style="width: <?= $persenKelengkapan ?>%;"></div>
</div>
</div>
<div class="flex items-center justify-between bg-background rounded-xl p-4 border border-glass-border/30">
<span class="text-label-mono font-label-mono text-on-surface-variant">Terakhir Diperbarui</span>
<span class="text-caption font-caption text-on-surface"><?= !empty($profil['updated_at']) ? e(date('d M Y H:i', strtotime($profil['updated_at']))) : '-' ?></span>
</div>
</div>
</div>

</div>
</div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof MediaHelpers !== 'undefined') MediaHelpers.initSkeleton(document.body);
});
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>

