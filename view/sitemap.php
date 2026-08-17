<?php
declare(strict_types=1);

$wisataList = getWisataList(true);
$beritaList = getBeritaList(true);

/* lastmod indeks = tanggal konten terbaru (YYYY-MM-DD) */
$tsWisata = 0;
foreach ($wisataList as $w) {
    $tsWisata = max($tsWisata, strtotime((string) $w['updated_at']));
}
$tsBerita = 0;
foreach ($beritaList as $b) {
    $tsBerita = max($tsBerita, strtotime((string) $b['updated_at']));
}
$tsHome   = max($tsWisata, $tsBerita);
$lastHome = $tsHome > 0 ? date('Y-m-d', $tsHome) : date('Y-m-d');
$lastWisata = $tsWisata > 0 ? date('Y-m-d', $tsWisata) : $lastHome;
$lastBerita = $tsBerita > 0 ? date('Y-m-d', $tsBerita) : $lastHome;

$urls = [];

foreach ($wisataList as $w) {
    $urls[] = [
        'loc'        => APP_URL . '/wisata/' . $w['slug'],
        'lastmod'    => date('Y-m-d', strtotime((string) $w['updated_at'])),
        'changefreq' => 'weekly',
        'priority'   => '0.7',
    ];
}
foreach ($beritaList as $b) {
    $urls[] = [
        'loc'        => APP_URL . '/berita/' . $b['slug'],
        'lastmod'    => date('Y-m-d', strtotime((string) $b['updated_at'])),
        'changefreq' => 'weekly',
        'priority'   => '0.8',
    ];
}
?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc><?= e(APP_URL) ?>/</loc><lastmod><?= e($lastHome) ?></lastmod><changefreq>weekly</changefreq><priority>1.0</priority></url>
    <url><loc><?= e(APP_URL) ?>/wisata</loc><lastmod><?= e($lastWisata) ?></lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>
    <url><loc><?= e(APP_URL) ?>/berita</loc><lastmod><?= e($lastBerita) ?></lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>
    <?php foreach ($urls as $u): ?>
    <url>
        <loc><?= e($u['loc']) ?></loc>
        <lastmod><?= e($u['lastmod']) ?></lastmod>
        <changefreq><?= e($u['changefreq']) ?></changefreq>
        <priority><?= e($u['priority']) ?></priority>
    </url>
    <?php endforeach; ?>
</urlset>
