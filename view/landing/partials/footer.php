<?php
declare(strict_types=1);
$namaPekon = $profil['nama_pekon'] ?? 'Desa Padang Cermin';
$alamat = $profil['alamat_kantor'] ?? '';
$telepon = $profil['telepon'] ?? '';
$email = $profil['email'] ?? '';
$whatsapp = $profil['whatsapp'] ?? '';

$waNum = preg_replace('/[^0-9]/', '', $whatsapp);
if ($waNum !== '' && str_starts_with($waNum, '0')) {
    $waNum = '62' . substr($waNum, 1);
}
$mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($alamat !== '' ? $namaPekon . ', ' . $alamat : $namaPekon);
?>
<footer class="bg-surface-container-low py-14 border-t border-glass-border">
<div class="max-w-container-max mx-auto px-margin-mobile lg:px-margin-desktop grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-stack-lg">
<!-- Brand -->
<div class="lg:col-span-5 flex flex-col gap-4">
<div class="flex items-center gap-4">
<img alt="Logo <?= e($namaPekon) ?>" class="h-11 w-auto" src="<?= assetUrl('img/logo.png') ?>"/>
<span class="font-headline-md text-headline-md text-primary"><?= e($namaPekon) ?></span>
</div>
<p class="text-on-surface-variant max-w-sm font-body-md text-body-md m-0">Menuju tata kelola desa yang transparan, digital, dan berkelanjutan untuk masa depan hijau.</p>
<?php if ($alamat !== ''): ?>
<a class="flex items-start gap-2 text-on-surface-variant hover:text-primary transition-colors font-caption text-caption" href="<?= e($mapsUrl) ?>" target="_blank" rel="noopener">
<span class="material-symbols-outlined text-[16px] text-primary mt-0.5">location_on</span>
<span><?= e($alamat) ?></span>
</a>
<?php endif; ?>
</div>

<!-- Navigasi -->
<div class="lg:col-span-3 flex flex-col gap-3">
<h4 class="text-primary font-headline-md text-headline-md m-0 mb-1">Navigasi</h4>
<a class="text-on-surface-variant hover:text-primary transition-colors font-caption text-caption flex items-center gap-2" href="<?= APP_BASE ?>/#profil"><span class="material-symbols-outlined text-[14px]">chevron_right</span>Profil Desa</a>
<a class="text-on-surface-variant hover:text-primary transition-colors font-caption text-caption flex items-center gap-2" href="<?= APP_BASE ?>/wisata"><span class="material-symbols-outlined text-[14px]">chevron_right</span>Galeri Wisata</a>
<a class="text-on-surface-variant hover:text-primary transition-colors font-caption text-caption flex items-center gap-2" href="<?= APP_BASE ?>/berita"><span class="material-symbols-outlined text-[14px]">chevron_right</span>Berita Desa</a>
<a class="text-on-surface-variant hover:text-primary transition-colors font-caption text-caption flex items-center gap-2" href="<?= APP_BASE ?>/#struktur"><span class="material-symbols-outlined text-[14px]">chevron_right</span>Struktur Organisasi</a>
<a class="text-on-surface-variant hover:text-primary transition-colors font-caption text-caption flex items-center gap-2" href="<?= APP_BASE ?>/#data"><span class="material-symbols-outlined text-[14px]">chevron_right</span>Data Kependudukan</a>
</div>

<!-- Kontak -->
<div class="lg:col-span-4 flex flex-col gap-3">
<h4 class="text-primary font-headline-md text-headline-md m-0 mb-1">Hubungi Kami</h4>
<?php if ($telepon !== ''): ?>
<a class="flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors font-caption text-caption" href="tel:<?= e(preg_replace('/[^0-9+]/', '', $telepon)) ?>">
<span class="material-symbols-outlined text-[16px] text-primary">call</span><?= e($telepon) ?>
</a>
<?php endif; ?>
<?php if ($whatsapp !== ''): ?>
<a class="flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors font-caption text-caption" href="https://wa.me/<?= e($waNum) ?>" target="_blank" rel="noopener">
<span class="material-symbols-outlined text-[16px] text-primary">chat</span>WhatsApp: <?= e($whatsapp) ?>
</a>
<?php endif; ?>
<?php if ($email !== ''): ?>
<a class="flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors font-caption text-caption" href="mailto:<?= e($email) ?>">
<span class="material-symbols-outlined text-[16px] text-primary">mail</span><?= e($email) ?>
</a>
<?php endif; ?>
<div class="flex items-start gap-2 text-on-surface-variant font-caption text-caption">
<span class="material-symbols-outlined text-[16px] text-primary mt-0.5">schedule</span>
<div>
<p class="m-0">Senin – Jumat: 08.00 – 16.00 WIB</p>
<p class="m-0">Sabtu: 08.00 – 12.00 WIB</p>
</div>
</div>
<a class="inline-flex items-center gap-2 mt-1 bg-primary/10 border border-primary/30 text-primary rounded-full px-4 py-2 font-caption text-caption hover:bg-primary/20 transition-all w-max" href="<?= e($mapsUrl) ?>" target="_blank" rel="noopener">
<span class="material-symbols-outlined text-[16px]">map</span>Buka di Google Maps
</a>
</div>
</div>
<div class="mt-stack-lg pt-stack-lg border-t border-glass-border max-w-container-max mx-auto px-margin-mobile lg:px-margin-desktop flex flex-col md:flex-row items-center justify-between gap-2 text-caption text-on-surface-variant pb-3">
<span>© <?= date('Y') ?> <?= e($namaPekon) ?> Digital.</span>
<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px] text-primary">public</span>Kabupaten Pesawaran, Lampung</span>
</div>
</footer>
<script src="<?= assetUrl('js/media.js') ?>"></script>
<script src="<?= assetUrl('js/carousel.js') ?>"></script>
