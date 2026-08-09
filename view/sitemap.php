<?php
declare(strict_types=1);

$wisataList = getWisataList(true);
$beritaList = getBeritaList(true);
$urls = [];

foreach ($wisataList as $w) {
    $urls[] = [
        'loc'        => APP_URL . '/wisata/' . $w['slug'],
        'lastmod'    => date('c', strtotime($w['updated_at'])),
        'changefreq' => 'weekly',
        'priority'   => '0.7',
    ];
}
foreach ($beritaList as $b) {
    $urls[] = [
        'loc'        => APP_URL . '/berita/' . $b['slug'],
        'lastmod'    => date('c', strtotime($b['updated_at'])),
        'changefreq' => 'weekly',
        'priority'   => '0.8',
    ];
}
?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc><?= e(APP_URL) ?>/</loc><changefreq>weekly</changefreq><priority>1.0</priority></url>
    <url><loc><?= e(APP_URL) ?>/wisata</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>
    <url><loc><?= e(APP_URL) ?>/berita</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>
    <?php foreach ($urls as $u): ?>
    <url>
        <loc><?= e($u['loc']) ?></loc>
        <lastmod><?= e($u['lastmod']) ?></lastmod>
        <changefreq><?= e($u['changefreq']) ?></changefreq>
        <priority><?= e($u['priority']) ?></priority>
    </url>
    <?php endforeach; ?>
</urlset>
