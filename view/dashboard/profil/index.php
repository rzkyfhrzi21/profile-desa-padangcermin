<?php
declare(strict_types=1);

$judulHalaman = 'Manajemen Profil';

$profil = getProfil();

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
<div class="grid grid-cols-1 md:grid-cols-3 gap-stack-md relative z-10">
<?php if (!empty($profil['foto_kepala_pekon'])): ?>
<div class="md:col-span-1">
<img class="w-full aspect-[3/4] object-cover rounded-xl border border-glass-border" alt="Foto Kepala Pekon Padang Cermin" src="<?= uploadUrl($profil['foto_kepala_pekon']) ?>"/>
</div>
<?php endif; ?>
<div class="<?= !empty($profil['foto_kepala_pekon']) ? 'md:col-span-2' : 'md:col-span-3' ?> flex items-center">
<p class="text-body-md font-body-md text-on-surface-variant m-0 leading-relaxed whitespace-pre-line"><?= $tampil('sambutan_kepala_pekon') ?></p>
</div>
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
<div class="flex flex-col gap-1 py-4 md:py-0 md:pr-gutter">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Titik Koordinat</span>
<span class="text-body-md font-body-md text-on-surface font-label-mono"><?= $tampil('latitude') ?>, <?= $tampil('longitude') ?></span>
</div>
<div class="flex flex-col gap-1 py-4 md:py-0 md:px-gutter">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Telepon</span>
<span class="text-body-md font-body-md text-on-surface"><?= $tampil('telepon') ?></span>
</div>
<div class="flex flex-col gap-1 py-4 md:py-0 md:pr-gutter">
<span class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Email</span>
<span class="text-body-md font-body-md text-on-surface"><?= $tampil('email') ?></span>
</div>
<div class="flex flex-col gap-1 py-4 md:py-0 md:px-gutter">
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
<?php if (!empty($profil['foto_kepala_pekon'])): ?>
<div class="aspect-[3/4] rounded-xl overflow-hidden border border-glass-border">
<img class="w-full h-full object-cover" alt="Foto Kepala Pekon Padang Cermin" src="<?= uploadUrl($profil['foto_kepala_pekon']) ?>"/>
</div>
<?php else: ?>
<div class="aspect-[3/4] rounded-xl border-2 border-dashed border-glass-border bg-surface-container-highest flex flex-col items-center justify-center gap-2">
<span class="material-symbols-outlined text-[40px] text-on-surface-variant/40">person</span>
<span class="text-caption font-caption text-on-surface-variant">Belum ada foto</span>
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

<div class="bg-gradient-to-br from-surface-container-high to-surface-container rounded-[20px] p-6 border border-glass-border shadow-lg">
<div class="flex items-start gap-3">
<span class="material-symbols-outlined text-primary mt-1">lightbulb</span>
<p class="font-body-md text-on-surface-variant text-sm leading-relaxed m-0">
<strong class="text-on-surface block mb-1">Tips Penulisan Profil</strong>
Gunakan bahasa yang jelas dan inspiratif untuk Visi &amp; Misi. Hindari singkatan internal agar mudah dipahami masyarakat umum dan pihak luar.
</p>
</div>
</div>
</div>
</div>
</section>
<?php require __DIR__ . '/../layout_close.php'; ?>
