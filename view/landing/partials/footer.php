<?php
declare(strict_types=1);
$namaPekon = $profil['nama_pekon'] ?? 'Desa Padang Cermin';
$alamat = $profil['alamat_kantor'] ?? '';
$email = $profil['email'] ?? '';
?>
<footer class="bg-surface-container-low py-section-gap border-t border-glass-border">
<div class="max-w-container-max mx-auto px-margin-mobile lg:px-margin-desktop grid grid-cols-1 md:grid-cols-4 gap-gutter">
<div class="col-span-1 md:col-span-2">
<div class="flex items-center gap-4 mb-stack-md">
<img alt="Logo <?= e($namaPekon) ?>" class="h-10 w-auto" src="<?= assetUrl('img/logo.png') ?>"/>
<span class="font-headline-md text-headline-md text-primary"><?= e($namaPekon) ?></span>
</div>
<p class="text-on-surface-variant max-w-sm">Menuju tata kelola desa yang transparan, digital, dan berkelanjutan untuk masa depan hijau.</p>
</div>
<div class="flex flex-col gap-4">
<h4 class="text-primary font-headline-md">Navigasi</h4>
<a class="text-on-surface-variant hover:text-primary transition-colors" href="<?= APP_BASE ?>/#profil">Profil Desa</a>
<a class="text-on-surface-variant hover:text-primary transition-colors" href="<?= APP_BASE ?>/wisata">Galeri Wisata</a>
<a class="text-on-surface-variant hover:text-primary transition-colors" href="<?= APP_BASE ?>/berita">Warta Desa</a>
</div>
<div class="flex flex-col gap-4">
<h4 class="text-primary font-headline-md">Kontak</h4>
<?php if ($alamat !== ''): ?><p class="text-on-surface-variant"><?= e($alamat) ?></p><?php endif; ?>
<?php if ($email !== ''): ?><p class="text-on-surface-variant"><?= e($email) ?></p><?php endif; ?>
</div>
</div>
<div class="mt-stack-lg pt-stack-lg border-t border-glass-border max-w-container-max mx-auto px-margin-mobile lg:px-margin-desktop text-center text-caption text-on-surface-variant pb-8">© <?= date('Y') ?> <?= e($namaPekon) ?> Digital.</div>
</footer>
<script src="<?= assetUrl('js/media.js') ?>"></script>
<script src="<?= assetUrl('js/carousel.js') ?>"></script>
