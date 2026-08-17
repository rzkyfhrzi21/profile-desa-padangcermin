<?php
declare(strict_types=1);

return [
    '/'                     => ['function' => ['profil.php', 'struktur.php', 'kependudukan.php', 'potensi.php', 'wisata.php', 'berita.php'], 'view' => 'landing/index.php'],
    '/wisata'               => ['function' => ['profil.php', 'wisata.php'], 'view' => 'wisata/index.php'],
    '/wisata/{slug}'        => ['function' => ['profil.php', 'auth.php', 'wisata.php'], 'view' => 'wisata/detail.php'],
    '/berita'               => ['function' => ['profil.php', 'berita.php'], 'view' => 'berita/index.php'],
    '/berita/{slug}'        => ['function' => ['profil.php', 'auth.php', 'berita.php'], 'view' => 'berita/detail.php'],
    '/sitemap.xml'          => ['function' => ['wisata.php', 'berita.php'], 'view' => 'sitemap.php'],
    '/testdiagram'          => ['function' => [], 'view' => 'testdiagram.php'],

    '/auth/login'           => ['function' => ['auth.php', 'csrf.php', 'log.php'], 'view' => 'auth/login.php'],
    '/auth/logout'          => ['function' => ['auth.php', 'csrf.php'], 'view' => 'auth/logout.php'],
    '/dashboard'            => ['function' => ['csrf.php', 'profil.php', 'struktur.php', 'kependudukan.php', 'potensi.php', 'wisata.php', 'berita.php', 'log.php'], 'view' => 'dashboard/home.php'],

    '/dashboard/profil'     => ['function' => ['profil.php', 'upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/profil/index.php'],
    '/dashboard/profil/form'=> ['function' => ['profil.php', 'upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/profil/form.php'],

    '/dashboard/struktur'   => ['function' => ['struktur.php', 'upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/struktur/index.php'],
    '/dashboard/struktur/form' => ['function' => ['struktur.php', 'upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/struktur/form.php'],

    '/dashboard/kependudukan' => ['function' => ['kependudukan.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/kependudukan/index.php'],
    '/dashboard/kependudukan/form' => ['function' => ['kependudukan.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/kependudukan/form.php'],

    '/dashboard/potensi'    => ['function' => ['potensi.php', 'upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/potensi/index.php'],
    '/dashboard/potensi/form' => ['function' => ['potensi.php', 'upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/potensi/form.php'],

    '/dashboard/wisata'     => ['function' => ['wisata.php', 'upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/wisata/index.php'],
    '/dashboard/wisata/form' => ['function' => ['wisata.php', 'upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/wisata/form.php'],

    '/dashboard/berita'     => ['function' => ['berita.php', 'upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/berita/index.php'],
    '/dashboard/berita/form' => ['function' => ['berita.php', 'upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/berita/form.php'],

    '/dashboard/admin/profil' => ['function' => ['upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/admin/profil.php'],

    '/dashboard/ajax/{modul}/{aksi}' => ['function' => ['ajax.php', 'profil.php', 'struktur.php', 'kependudukan.php', 'potensi.php', 'wisata.php', 'berita.php', 'upload.php', 'csrf.php', 'log.php'], 'view' => 'dashboard/ajax.php'],
];
