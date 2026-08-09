<?php
declare(strict_types=1);

const AJAX_PAGE_LIMIT = 10;

function ajaxResponse(array $data): never
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function ajaxBadge(string $status, string $onLabel, string $offLabel): string
{
    if ($status === $onLabel || $status === 'publish' || $status === 'aktif') {
        return '<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-status-success/20 text-[#a3f0b6] border border-[#a3f0b6]/30"><span class="w-1.5 h-1.5 rounded-full bg-[#a3f0b6] animate-pulse"></span><span class="text-caption font-caption">' . ($status === 'publish' ? 'Published' : ($status === 'aktif' ? 'Aktif' : $onLabel)) . '</span></div>';
    }
    return '<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-status-warning/20 text-[#ffdb80] border border-[#ffdb80]/30"><span class="w-1.5 h-1.5 rounded-full bg-[#ffdb80]"></span><span class="text-caption font-caption">' . ($status === 'draft' ? 'Draft' : ($status === 'nonaktif' ? 'Nonaktif' : $offLabel)) . '</span></div>';
}

function ajaxAksiButtons(string $editUrl, int $id, string $nama, string $pesanHapus): string
{
    return '<div class="inline-flex items-center justify-end gap-2">'
        . '<a class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors" title="Edit" href="' . $editUrl . '"><span class="material-symbols-outlined text-[18px]">edit</span></a>'
        . '<button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-status-error hover:bg-surface-container transition-colors" title="Hapus" data-aksi="delete" data-id="' . $id . '" data-nama="' . e($nama) . '" data-pesan="' . e($pesanHapus) . '"><span class="material-symbols-outlined text-[18px]">delete</span></button>'
        . '</div>';
}

function ajaxPagination(int $total, int $page, int $limit): array
{
    $totalPages = max(1, (int) ceil($total / $limit));
    $page = min(max(1, $page), $totalPages);
    if ($totalPages <= 1) {
        return ['pagination' => '', 'total_html' => 'Total ' . $total . ' data', 'page' => $page, 'total_pages' => $totalPages];
    }
    $b = fn(int $p, string $label, bool $disabled = false): string =>
        '<button type="button" data-page="' . $p . '" class="px-3 h-8 flex items-center justify-center rounded-lg hover:bg-surface-container text-on-surface-variant transition-colors text-caption font-caption gap-1 ' . ($disabled ? 'pointer-events-none opacity-40' : '') . '">' . $label . '</button>';
    $pageBtn = fn(int $p, bool $aktif) =>
        $aktif
            ? '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary text-on-primary font-label-mono text-label-mono shadow-lime-glow">' . $p . '</span>'
            : '<button type="button" data-page="' . $p . '" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-surface-container text-on-surface-variant transition-colors font-label-mono text-label-mono">' . $p . '</button>';

    $html = '<div class="flex items-center gap-1 bg-surface-container-highest p-1 rounded-xl border border-glass-border/50">'
        . $b(max(1, $page - 1), '<span class="material-symbols-outlined text-[18px]">chevron_left</span> Prev', $page <= 1)
        . '<div class="h-4 w-px bg-glass-border mx-1"></div>';
    $mulai = max(1, $page - 2);
    $akhir = min($totalPages, $page + 2);
    for ($i = $mulai; $i <= $akhir; $i++) {
        $html .= $pageBtn($i, $i === $page);
    }
    if ($akhir < $totalPages) {
        $html .= '<span class="w-8 h-8 flex items-center justify-center text-on-surface-variant">...</span>';
        $html .= $pageBtn($totalPages, false);
    }
    $html .= '<div class="h-4 w-px bg-glass-border mx-1"></div>'
        . $b(min($totalPages, $page + 1), 'Next <span class="material-symbols-outlined text-[18px]">chevron_right</span>', $page >= $totalPages)
        . '</div>';
    $dari = $total === 0 ? 0 : (($page - 1) * $limit) + 1;
    $sampai = min($page * $limit, $total);
    return ['pagination' => $html, 'total_html' => 'Menampilkan ' . $dari . '–' . $sampai . ' dari ' . $total, 'page' => $page, 'total_pages' => $totalPages];
}

function ajaxEmptyState(int $colspan, string $icon, string $pesan): string
{
    return '<tr><td colspan="' . $colspan . '" class="py-16 px-4 text-center">'
        . '<div class="flex flex-col items-center justify-center text-center gap-3">'
        . '<span class="material-symbols-outlined text-[48px] text-on-surface-variant/40">' . $icon . '</span>'
        . '<p class="text-body-md font-body-md text-on-surface-variant">' . e($pesan) . '</p>'
        . '</div></td></tr>';
}

function ajaxParams(): array
{
    $p = [
        'page' => max(1, (int) ($_POST['page'] ?? 1)),
        'q' => trim((string) ($_POST['q'] ?? '')),
        'status' => trim((string) ($_POST['status'] ?? '')),
        'kategori' => (int) ($_POST['kategori'] ?? 0),
    ];
    if ($p['status'] === '0') {
        $p['status'] = '';
    }
    return $p;
}

/* ================= WISATA ================= */

function ajaxListWisata(array $p): array
{
    $db = getDb();
    $where = [];
    $params = [];
    if ($p['q'] !== '') {
        $where[] = '(nama LIKE ? OR alamat LIKE ?)';
        $params[] = '%' . $p['q'] . '%';
        $params[] = '%' . $p['q'] . '%';
    }
    if ($p['status'] !== '') {
        $where[] = 'status = ?';
        $params[] = $p['status'];
    }
    $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);
    $stmtCount = $db->prepare('SELECT COUNT(*) FROM wisata_desa' . $whereSql);
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $limit = AJAX_PAGE_LIMIT;
    $offset = max(0, $p['page'] - 1) * $limit;
    $stmt = $db->prepare('SELECT * FROM wisata_desa' . $whereSql . ' ORDER BY created_at DESC, id DESC LIMIT ' . $limit . ' OFFSET ' . $offset);
    $stmt->execute($params);
    $rows_data = getWisataWithGambar($stmt->fetchAll());

    $rows = [];
    if ($rows_data === []) {
        $rows[] = '<div class="col-span-full py-16 flex flex-col items-center gap-3 text-center"><span class="material-symbols-outlined text-[48px] text-on-surface-variant/40">landscape</span><p class="text-body-md font-body-md text-on-surface-variant">' . ($p['q'] !== '' ? 'Tidak ada wisata yang cocok dengan pencarian.' : 'Belum ada data wisata.') . '</p></div>';
    }
    foreach ($rows_data as $w) {
        $gambarUtama = $w['gambar'][0]['path_gambar'] ?? ($w['gambar_utama'] ?? '');
        $imgHtml = $gambarUtama !== ''
            ? '<img src="' . uploadUrl($gambarUtama) . '" alt="' . e($w['nama']) . '" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-lightbox="' . uploadUrl($gambarUtama) . '" data-skeleton loading="lazy">'
            : '<div class="w-full h-full flex items-center justify-center bg-surface-container-high"><span class="material-symbols-outlined text-on-surface-variant text-[48px]">landscape</span></div>';
        $statusBadge = $w['status'] === 'publish'
            ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-primary/10 text-primary text-caption font-caption border border-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span>Published</span>'
            : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant text-caption font-caption border border-glass-border">Draft</span>';
        $card = '<div class="group relative bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border overflow-hidden hover:border-primary/40 hover:-translate-y-1 transition-all duration-300">';
        $card .= '<div class="relative aspect-[4/3] overflow-hidden bg-surface-container-high">';
        $card .= $imgHtml;
        $card .= '<div class="absolute top-2 right-2">' . $statusBadge . '</div>';
        $card .= '</div>';
        $card .= '<div class="p-4">';
        $card .= '<h3 class="font-medium text-body-lg text-on-surface mb-1 truncate">' . e($w['nama']) . '</h3>';
        $card .= '<p class="text-caption font-caption text-on-surface-variant truncate mb-3">' . e($w['alamat'] ?? '') . '</p>';
        $card .= '<div class="flex gap-2">'
            . '<a href="' . APP_BASE . '/dashboard/wisata/form?id=' . (int) $w['id'] . '" class="flex-1 text-center py-2 px-3 rounded-xl bg-surface-container-high text-on-surface-variant hover:text-primary hover:bg-primary/10 border border-glass-border transition-all text-caption font-caption flex items-center justify-center gap-1"><span class="material-symbols-outlined text-[16px]">edit</span>Edit</a>'
            . '<button type="button" data-aksi="delete" data-id="' . (int) $w['id'] . '" data-pesan="Hapus wisata &quot;' . e($w['nama']) . '&quot; beserta galerinya?" class="py-2 px-3 rounded-xl bg-surface-container-high text-on-surface-variant hover:text-error hover:bg-error/10 border border-glass-border transition-all text-caption font-caption flex items-center justify-center gap-1" title="Hapus"><span class="material-symbols-outlined text-[16px]">delete</span></button>'
            . '</div>';
        $card .= '</div>';
        $card .= '</div>';
        $rows[] = $card;
    }
    $pg = ajaxPagination($total, $p['page'], $limit);
    return ['rows' => $rows, 'pagination' => $pg['pagination'], 'total_html' => $pg['total_html'], 'total' => $total, 'page' => $pg['page'], 'total_pages' => $pg['total_pages']];
}

function ajaxDeleteWisata(int $id): array
{
    $target = getWisataById($id);
    if ($target === null) {
        return ['ok' => false, 'message' => 'Wisata tidak ditemukan.'];
    }
    if (!deleteWisata($id)) {
        return ['ok' => false, 'message' => 'Gagal menghapus wisata.'];
    }
    catatLog('hapus wisata: ' . $target['nama'], 'wisata_desa', $id);
    return ['ok' => true, 'message' => 'Wisata "' . $target['nama'] . '" berhasil dihapus.'];
}

function ajaxDetailWisata(int $id): array
{
    $w = getWisataById($id);
    if ($w === null) {
        return ['ok' => false, 'message' => 'Wisata tidak ditemukan.'];
    }
    $gambar = getWisataImages($id);
    $galeri = '';
    foreach ($gambar as $i => $g) {
        $galeri .= '<img data-skeleton data-lightbox="' . uploadUrl($g['path_gambar']) . '" alt="' . e($w['nama']) . ' ' . ((int) $i + 1) . '" class="h-28 w-full object-cover rounded-xl cursor-pointer border border-glass-border/30" src="' . uploadUrl($g['path_gambar']) . '"/>';
    }
    $html = '<div class="flex flex-col gap-4">'
        . '<div class="flex items-center gap-2 text-caption font-caption text-primary"><span class="material-symbols-outlined text-[16px]">landscape</span>WISATA</div>'
        . '<h3 class="font-headline-lg text-headline-lg text-on-surface m-0">' . e($w['nama']) . '</h3>'
        . '<div class="flex flex-wrap gap-2">' . ajaxBadge($w['status'], 'Published', 'Draft')
        . ($w['harga_tiket'] !== null && $w['harga_tiket'] !== '' ? '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high border border-glass-border text-caption font-caption text-on-surface-variant"><span class="material-symbols-outlined text-[14px]">sell</span>' . e($w['harga_tiket']) . '</span>' : '')
        . ($w['jam_buka'] !== null && $w['jam_buka'] !== '' ? '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high border border-glass-border text-caption font-caption text-on-surface-variant"><span class="material-symbols-outlined text-[14px]">schedule</span>' . e($w['jam_buka']) . '</span>' : '')
        . '</div>'
        . ($galeri !== '' ? '<div class="grid grid-cols-3 gap-2">' . $galeri . '</div>' : '')
        . '<div class="flex flex-col gap-1"><span class="text-label-mono font-label-mono text-caption text-on-surface-variant">ALAMAT</span><span class="text-body-md font-body-md text-on-surface">' . e($w['alamat']) . '</span></div>'
        . '<div class="flex flex-col gap-1"><span class="text-label-mono font-label-mono text-caption text-on-surface-variant">DESKRIPSI</span><p class="text-body-md font-body-md text-on-surface-variant m-0 whitespace-pre-line">' . e($w['deskripsi'] ?? '-') . '</p></div>'
        . '</div>';
    return ['ok' => true, 'html' => $html];
}

/* ================= BERITA ================= */

function ajaxListBerita(array $p): array
{
    $limit = AJAX_PAGE_LIMIT;
    $berita = getBeritaListAdmin($limit, $p['page'], $p['q'], $p['kategori'] > 0 ? $p['kategori'] : null, $p['status']);
    $total = countBeritaAdmin($p['q'], $p['kategori'] > 0 ? $p['kategori'] : null, $p['status']);

    $html = '';
    if ($berita === []) {
        $html = ajaxEmptyState(5, 'newspaper', $p['q'] !== '' ? 'Tidak ada berita yang cocok dengan pencarian.' : 'Belum ada berita.');
    }
    foreach ($berita as $b) {
        $tgl = $b['published_at'] !== null ? date('d M Y', strtotime($b['published_at'])) : '-';
        $thumb = !empty($b['gambar_utama'])
            ? '<div class="w-12 h-12 rounded-lg bg-surface-container overflow-hidden shrink-0 hidden sm:block"><img class="w-full h-full object-cover cursor-pointer" data-lightbox="' . uploadUrl($b['gambar_utama']) . '" data-skeleton alt="Thumbnail ' . e($b['judul']) . '" src="' . uploadUrl($b['gambar_utama']) . '"/></div>'
            : '<div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center shrink-0 border border-glass-border/50 hidden sm:flex"><span class="material-symbols-outlined text-on-surface-variant/50">image</span></div>';
        $html .= '<tr class="border-b border-glass-border/30 hover:bg-surface-container-highest/50 transition-colors group/row">'
            . '<td class="py-3 px-4 w-16"><div class="w-12 h-12 rounded-lg overflow-hidden bg-surface-container-high flex-shrink-0">'
            . (!empty($b['gambar_utama']) 
                ? '<img src="' . uploadUrl($b['gambar_utama']) . '" alt="" class="w-full h-full object-cover" data-lightbox="' . uploadUrl($b['gambar_utama']) . '">'
                : '<span class="material-symbols-outlined text-on-surface-variant m-auto h-full flex items-center justify-center">image</span>')
            . '</div></td>'
            . '<td class="py-4 px-4"><span class="font-medium group-hover/row:text-primary transition-colors line-clamp-2 sm:line-clamp-1">' . e($b['judul']) . '</span></td>'
            . '<td class="py-4 px-4 text-on-surface-variant">' . e($b['kategori_nama'] ?? '-') . '</td>'
            . '<td class="py-4 px-4 text-label-mono font-label-mono text-on-surface-variant text-[13px] whitespace-nowrap">' . $tgl . '</td>'
            . '<td class="py-4 px-4 text-right">' . ajaxBadge($b['status'], 'Published', 'Draft') . '</td>'
            . '<td class="py-4 px-4 text-right">' . ajaxAksiButtons(APP_BASE . '/dashboard/berita/form?id=' . (int) $b['id'], (int) $b['id'], $b['judul'], 'Hapus berita &quot;' . $b['judul'] . '&quot;?') . '</td>'
            . '</tr>';
    }
    $pg = ajaxPagination($total, $p['page'], $limit);
    return ['rows' => $html, 'pagination' => $pg['pagination'], 'total_html' => $pg['total_html'], 'total' => $total, 'page' => $pg['page'], 'total_pages' => $pg['total_pages']];
}

function ajaxDeleteBerita(int $id): array
{
    $target = getBeritaById($id);
    if ($target === null) {
        return ['ok' => false, 'message' => 'Berita tidak ditemukan.'];
    }
    if (!deleteBerita($id)) {
        return ['ok' => false, 'message' => 'Gagal menghapus berita.'];
    }
    catatLog('hapus berita: ' . $target['judul'], 'berita_desa', $id);
    return ['ok' => true, 'message' => 'Berita "' . $target['judul'] . '" berhasil dihapus.'];
}

function ajaxDetailBerita(int $id): array
{
    $b = getBeritaById($id);
    if ($b === null) {
        return ['ok' => false, 'message' => 'Berita tidak ditemukan.'];
    }
    $html = '<div class="flex flex-col gap-4">'
        . '<div class="flex items-center gap-2 text-caption font-caption text-primary"><span class="material-symbols-outlined text-[16px]">newspaper</span>BERITA</div>'
        . '<h3 class="font-headline-lg text-headline-lg text-on-surface m-0">' . e($b['judul']) . '</h3>'
        . '<div class="flex flex-wrap gap-2">' . ajaxBadge($b['status'], 'Published', 'Draft')
        . ($b['published_at'] !== null ? '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high border border-glass-border text-caption font-caption text-on-surface-variant"><span class="material-symbols-outlined text-[14px]">calendar_today</span>' . formatTanggal($b['published_at']) . '</span>' : '')
        . '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high border border-glass-border text-caption font-caption text-on-surface-variant"><span class="material-symbols-outlined text-[14px]">visibility</span>' . formatAngka($b['views']) . ' views</span>'
        . '</div>'
        . (!empty($b['gambar_utama']) ? '<img data-skeleton data-lightbox="' . uploadUrl($b['gambar_utama']) . '" alt="' . e($b['judul']) . '" class="w-full max-h-64 object-cover rounded-xl cursor-pointer border border-glass-border/30" src="' . uploadUrl($b['gambar_utama']) . '"/>' : '')
        . '<div class="flex flex-col gap-1"><span class="text-label-mono font-label-mono text-caption text-on-surface-variant">KONTEN</span><p class="text-body-md font-body-md text-on-surface-variant m-0 whitespace-pre-line">' . e(truncate((string) $b['konten'], 500)) . '</p></div>'
        . '</div>';
    return ['ok' => true, 'html' => $html];
}

/* ================= POTENSI ================= */

function ajaxListPotensi(array $p): array
{
    $db = getDb();
    $where = [];
    $params = [];
    if ($p['q'] !== '') {
        $where[] = 'judul LIKE ?';
        $params[] = '%' . $p['q'] . '%';
    }
    if ($p['status'] !== '') {
        $where[] = 'status = ?';
        $params[] = $p['status'];
    }
    $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);
    $stmtCount = $db->prepare('SELECT COUNT(*) FROM potensi_desa' . $whereSql);
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $limit = AJAX_PAGE_LIMIT;
    $offset = max(0, $p['page'] - 1) * $limit;
    $stmt = $db->prepare('SELECT * FROM potensi_desa' . $whereSql . ' ORDER BY urutan ASC, id ASC LIMIT ' . $limit . ' OFFSET ' . $offset);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $kategoriLabel = [
        'pertanian' => 'Pertanian', 'perkebunan' => 'Perkebunan', 'peternakan' => 'Peternakan',
        'perikanan' => 'Perikanan', 'umkm' => 'UMKM', 'kerajinan' => 'Kerajinan',
    ];
    $html = '';
    if ($rows === []) {
        $html = ajaxEmptyState(5, 'psychiatry', $p['q'] !== '' ? 'Tidak ada potensi yang cocok dengan pencarian.' : 'Belum ada data potensi desa.');
    }
    foreach ($rows as $po) {
        $kat = trim((string) ($po['kategori'] ?? ''));
        $katLabel = $kat !== '' ? ($kategoriLabel[$kat] ?? ucwords(str_replace(['_', '-'], ' ', $kat))) : '-';
        $thumb = !empty($po['gambar'])
            ? '<div class="w-12 h-12 rounded-lg bg-surface-container overflow-hidden shrink-0 hidden sm:block"><img class="w-full h-full object-cover cursor-pointer" data-lightbox="' . uploadUrl($po['gambar']) . '" data-skeleton alt="Thumbnail ' . e($po['judul']) . '" src="' . uploadUrl($po['gambar']) . '"/></div>'
            : '<div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center shrink-0 border border-glass-border/50 hidden sm:flex"><span class="material-symbols-outlined text-on-surface-variant/50">psychiatry</span></div>';
        $html .= '<tr class="border-b border-glass-border/30 hover:bg-surface-container-highest/50 transition-colors group/row">'
            . '<td class="py-4 px-4"><div class="flex items-center gap-3">' . $thumb . '<span class="font-medium group-hover/row:text-primary transition-colors line-clamp-2 sm:line-clamp-1">' . e($po['judul']) . '</span></div></td>'
            . '<td class="py-4 px-4 text-on-surface-variant">' . e($katLabel) . '</td>'
            . '<td class="py-4 px-4 text-center text-label-mono font-label-mono text-on-surface-variant text-[13px] whitespace-nowrap">' . (int) $po['urutan'] . '</td>'
            . '<td class="py-4 px-4 text-right">' . ajaxBadge($po['status'], 'Aktif', 'Nonaktif') . '</td>'
            . '<td class="py-4 px-4 text-right">' . ajaxAksiButtons(APP_BASE . '/dashboard/potensi/form?id=' . (int) $po['id'], (int) $po['id'], $po['judul'], 'Hapus potensi &quot;' . $po['judul'] . '&quot;?') . '</td>'
            . '</tr>';
    }
    $pg = ajaxPagination($total, $p['page'], $limit);
    return ['rows' => $html, 'pagination' => $pg['pagination'], 'total_html' => $pg['total_html'], 'total' => $total, 'page' => $pg['page'], 'total_pages' => $pg['total_pages']];
}

function ajaxDeletePotensi(int $id): array
{
    $target = getPotensiById($id);
    if ($target === null) {
        return ['ok' => false, 'message' => 'Potensi tidak ditemukan.'];
    }
    if (!deletePotensi($id)) {
        return ['ok' => false, 'message' => 'Gagal menghapus potensi.'];
    }
    if (!empty($target['gambar'])) {
        $file = UPLOAD_PATH . '/' . $target['gambar'];
        if (is_file($file)) {
            @unlink($file);
        }
    }
    catatLog('hapus potensi: ' . $target['judul'], 'potensi_desa', $id);
    return ['ok' => true, 'message' => 'Potensi "' . $target['judul'] . '" berhasil dihapus.'];
}

function ajaxDetailPotensi(int $id): array
{
    $po = getPotensiById($id);
    if ($po === null) {
        return ['ok' => false, 'message' => 'Potensi tidak ditemukan.'];
    }
    $kat = trim((string) ($po['kategori'] ?? ''));
    $html = '<div class="flex flex-col gap-4">'
        . '<div class="flex items-center gap-2 text-caption font-caption text-primary"><span class="material-symbols-outlined text-[16px]">psychiatry</span>POTENSI DESA</div>'
        . '<h3 class="font-headline-lg text-headline-lg text-on-surface m-0">' . e($po['judul']) . '</h3>'
        . '<div class="flex flex-wrap gap-2">' . ajaxBadge($po['status'], 'Aktif', 'Nonaktif')
        . ($kat !== '' ? '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high border border-glass-border text-caption font-caption text-on-surface-variant">' . e(ucwords(str_replace(['_', '-'], ' ', $kat))) . '</span>' : '')
        . '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high border border-glass-border text-caption font-caption text-on-surface-variant"><span class="material-symbols-outlined text-[14px]">sort</span>Urutan ' . (int) $po['urutan'] . '</span>'
        . '</div>'
        . (!empty($po['gambar']) ? '<img data-skeleton data-lightbox="' . uploadUrl($po['gambar']) . '" alt="' . e($po['judul']) . '" class="w-full max-h-64 object-cover rounded-xl cursor-pointer border border-glass-border/30" src="' . uploadUrl($po['gambar']) . '"/>' : '')
        . '<div class="flex flex-col gap-1"><span class="text-label-mono font-label-mono text-caption text-on-surface-variant">DESKRIPSI</span><p class="text-body-md font-body-md text-on-surface-variant m-0 whitespace-pre-line">' . e($po['deskripsi'] ?? '-') . '</p></div>'
        . '</div>';
    return ['ok' => true, 'html' => $html];
}

/* ================= STRUKTUR ================= */

function ajaxListStruktur(array $p): array
{
    $db = getDb();
    $where = [];
    $params = [];
    if ($p['q'] !== '') {
        $where[] = '(nama LIKE ? OR jabatan LIKE ?)';
        $params[] = '%' . $p['q'] . '%';
        $params[] = '%' . $p['q'] . '%';
    }
    $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);
    $stmtCount = $db->prepare('SELECT COUNT(*) FROM struktur_organisasi' . $whereSql);
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $limit = AJAX_PAGE_LIMIT;
    $offset = max(0, $p['page'] - 1) * $limit;
    $stmt = $db->prepare('SELECT * FROM struktur_organisasi' . $whereSql . ' ORDER BY urutan ASC, id ASC LIMIT ' . $limit . ' OFFSET ' . $offset);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $html = '';
    if ($rows === []) {
        $html = ajaxEmptyState(6, 'account_tree', $p['q'] !== '' ? 'Tidak ada pegawai yang cocok dengan pencarian.' : 'Belum ada data struktur organisasi.');
    }
    foreach ($rows as $n) {
        $foto = !empty($n['foto'])
            ? '<div class="w-10 h-10 rounded-full overflow-hidden shrink-0 border border-glass-border/50"><img class="w-full h-full object-cover cursor-pointer" data-lightbox="' . uploadUrl($n['foto']) . '" data-skeleton alt="Foto ' . e($n['nama']) . '" src="' . uploadUrl($n['foto']) . '"/></div>'
            : '<div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center shrink-0 border border-glass-border/50"><span class="material-symbols-outlined text-[18px] text-on-surface-variant/50">person</span></div>';
        $kontak = (int) ($n['tampil_di_kontak'] ?? 0) === 1
            ? '<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-muted-forest text-primary border border-primary/20"><span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span><span class="text-caption font-caption">Tampil di Kontak</span></div>'
            : '<span class="text-caption font-caption text-on-surface-variant/60">-</span>';
        $html .= '<tr class="border-b border-glass-border/30 hover:bg-surface-container-highest/50 transition-colors group/row">'
            . '<td class="py-4 px-4"><div class="flex items-center gap-3">' . $foto . '<span class="font-medium group-hover/row:text-primary transition-colors">' . e($n['nama']) . '</span></div></td>'
            . '<td class="py-4 px-4 text-on-surface-variant">' . e($n['jabatan']) . '</td>'
            . '<td class="py-4 px-4 text-on-surface-variant">' . e($n['pendidikan_terakhir'] ?? '-') . '</td>'
            . '<td class="py-4 px-4 text-label-mono font-label-mono text-on-surface-variant text-[13px] whitespace-nowrap">' . (int) $n['urutan'] . '</td>'
            . '<td class="py-4 px-4">' . $kontak . '</td>'
            . '<td class="py-4 px-4 text-right">' . ajaxAksiButtons(APP_BASE . '/dashboard/struktur/form?id=' . (int) $n['id'], (int) $n['id'], $n['nama'], 'Hapus jabatan &quot;' . $n['nama'] . '&quot;?') . '</td>'
            . '</tr>';
    }
    $pg = ajaxPagination($total, $p['page'], $limit);
    return ['rows' => $html, 'pagination' => $pg['pagination'], 'total_html' => $pg['total_html'], 'total' => $total, 'page' => $pg['page'], 'total_pages' => $pg['total_pages']];
}

function ajaxDeleteStruktur(int $id): array
{
    $target = getStrukturById($id);
    if ($target === null) {
        return ['ok' => false, 'message' => 'Data tidak ditemukan.'];
    }
    if (!deleteStruktur($id)) {
        return ['ok' => false, 'message' => 'Tidak dapat menghapus "' . $target['nama'] . '" karena memiliki bawahan.'];
    }
    if (!empty($target['foto'])) {
        $file = UPLOAD_PATH . '/' . $target['foto'];
        if (is_file($file)) {
            @unlink($file);
        }
    }
    catatLog('hapus struktur: ' . $target['nama'], 'struktur_organisasi', $id);
    return ['ok' => true, 'message' => 'Struktur "' . $target['nama'] . '" berhasil dihapus.'];
}

function ajaxDetailStruktur(int $id): array
{
    $n = getStrukturById($id);
    if ($n === null) {
        return ['ok' => false, 'message' => 'Data tidak ditemukan.'];
    }
    $foto = !empty($n['foto'])
        ? '<img data-skeleton data-lightbox="' . uploadUrl($n['foto']) . '" alt="Foto ' . e($n['nama']) . '" class="w-28 h-28 object-cover rounded-2xl cursor-pointer border border-glass-border/30" src="' . uploadUrl($n['foto']) . '"/>'
        : '<div class="w-28 h-28 rounded-2xl bg-surface-container flex items-center justify-center border border-glass-border/50"><span class="material-symbols-outlined text-[40px] text-on-surface-variant/50">person</span></div>';
    $html = '<div class="flex flex-col gap-4">'
        . '<div class="flex items-center gap-2 text-caption font-caption text-primary"><span class="material-symbols-outlined text-[16px]">account_tree</span>APARATUR DESA</div>'
        . '<div class="flex items-center gap-4">' . $foto
        . '<div class="flex flex-col gap-1"><h3 class="font-headline-lg text-headline-lg text-on-surface m-0">' . e($n['nama']) . '</h3><span class="text-body-md font-body-md text-primary">' . e($n['jabatan']) . '</span></div></div>'
        . '<div class="flex flex-wrap gap-2">'
        . '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high border border-glass-border text-caption font-caption text-on-surface-variant"><span class="material-symbols-outlined text-[14px]">school</span>' . e($n['pendidikan_terakhir'] ?? '-') . '</span>'
        . '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high border border-glass-border text-caption font-caption text-on-surface-variant"><span class="material-symbols-outlined text-[14px]">sort</span>Urutan ' . (int) $n['urutan'] . '</span>'
        . ((int) ($n['tampil_di_kontak'] ?? 0) === 1 ? '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-primary/10 border border-primary/30 text-caption font-caption text-primary"><span class="material-symbols-outlined text-[14px]">call</span>Tampil di Kontak</span>' : '')
        . '</div></div>';
    return ['ok' => true, 'html' => $html];
}

/* ================= KEPENDUDUKAN ================= */

function ajaxListKependudukan(array $p): array
{
    $db = getDb();
    $where = [];
    $params = [];
    if ($p['q'] !== '') {
        $where[] = '(periode LIKE ? OR keterangan LIKE ?)';
        $params[] = '%' . $p['q'] . '%';
        $params[] = '%' . $p['q'] . '%';
    }
    $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);
    $stmtCount = $db->prepare('SELECT COUNT(*) FROM data_kependudukan' . $whereSql);
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $limit = AJAX_PAGE_LIMIT;
    $offset = max(0, $p['page'] - 1) * $limit;
    $stmt = $db->prepare('SELECT * FROM data_kependudukan' . $whereSql . ' ORDER BY periode DESC LIMIT ' . $limit . ' OFFSET ' . $offset);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $html = '';
    if ($rows === []) {
        $html = ajaxEmptyState(8, 'group', $p['q'] !== '' ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data kependudukan.');
    }
    foreach ($rows as $d) {
        $html .= '<tr class="border-b border-glass-border/30 hover:bg-surface-container-highest/50 transition-colors group/row">'
            . '<td class="py-4 px-4"><span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container-highest border border-glass-border/50 text-label-mono font-label-mono text-primary text-[13px]">' . e($d['periode']) . '</span></td>'
            . '<td class="py-4 px-4 text-right text-label-mono font-label-mono whitespace-nowrap group-hover/row:text-primary transition-colors">' . formatAngka($d['jumlah_kk']) . '</td>'
            . '<td class="py-4 px-4 text-right text-label-mono font-label-mono whitespace-nowrap">' . formatAngka($d['jumlah_jiwa']) . '</td>'
            . '<td class="py-4 px-4 text-right text-label-mono font-label-mono whitespace-nowrap">' . formatAngka($d['jumlah_laki']) . '</td>'
            . '<td class="py-4 px-4 text-right text-label-mono font-label-mono whitespace-nowrap">' . formatAngka($d['jumlah_perempuan']) . '</td>'
            . '<td class="py-4 px-4 text-on-surface-variant">' . e($d['keterangan'] ?? '-') . '</td>'
            . '<td class="py-4 px-4 text-label-mono font-label-mono text-on-surface-variant text-[13px] whitespace-nowrap">' . formatTanggal($d['updated_at']) . '</td>'
            . '<td class="py-4 px-4 text-right">' . ajaxAksiButtons(APP_BASE . '/dashboard/kependudukan/form?id=' . (int) $d['id'], (int) $d['id'], $d['periode'], 'Hapus data periode &quot;' . $d['periode'] . '&quot;?') . '</td>'
            . '</tr>';
    }
    $pg = ajaxPagination($total, $p['page'], $limit);
    return ['rows' => $html, 'pagination' => $pg['pagination'], 'total_html' => $pg['total_html'], 'total' => $total, 'page' => $pg['page'], 'total_pages' => $pg['total_pages']];
}

function ajaxDeleteKependudukan(int $id): array
{
    $target = getDataKependudukanById($id);
    if ($target === null) {
        return ['ok' => false, 'message' => 'Data tidak ditemukan.'];
    }
    if (!deleteKependudukan($id)) {
        return ['ok' => false, 'message' => 'Gagal menghapus data.'];
    }
    catatLog('hapus data kependudukan: ' . $target['periode'], 'data_kependudukan', $id);
    return ['ok' => true, 'message' => 'Data periode ' . $target['periode'] . ' berhasil dihapus.'];
}

function ajaxDetailKependudukan(int $id): array
{
    $d = getDataKependudukanById($id);
    if ($d === null) {
        return ['ok' => false, 'message' => 'Data tidak ditemukan.'];
    }
    $angka = static function (string $label, $nilai) {
        return '<div class="flex flex-col gap-1"><span class="text-label-mono font-label-mono text-caption text-on-surface-variant">' . $label . '</span><span class="font-label-mono text-[28px] leading-none text-on-surface">' . formatAngka($nilai) . '</span></div>';
    };
    $html = '<div class="flex flex-col gap-5">'
        . '<div class="flex items-center gap-2 text-caption font-caption text-primary"><span class="material-symbols-outlined text-[16px]">group</span>DATA DEMOGRAFI</div>'
        . '<div class="flex items-center justify-between gap-3"><h3 class="font-headline-lg text-headline-lg text-on-surface m-0">Periode ' . e($d['periode']) . '</h3>'
        . '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high border border-glass-border text-caption font-caption text-on-surface-variant"><span class="material-symbols-outlined text-[14px]">update</span>' . formatTanggal($d['updated_at']) . '</span></div>'
        . '<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">' . $angka('Jumlah KK', $d['jumlah_kk']) . $angka('Jumlah Jiwa', $d['jumlah_jiwa']) . $angka('Laki-laki', $d['jumlah_laki']) . $angka('Perempuan', $d['jumlah_perempuan']) . '</div>'
        . ($d['keterangan'] !== null ? '<div class="flex flex-col gap-1"><span class="text-label-mono font-label-mono text-caption text-on-surface-variant">KETERANGAN</span><p class="text-body-md font-body-md text-on-surface-variant m-0">' . e($d['keterangan']) . '</p></div>' : '')
        . '</div>';
    return ['ok' => true, 'html' => $html];
}

/* ================= DISPATCHER ================= */

function ajaxDispatch(string $modul, string $aksi): never
{
    $p = ajaxParams();
    $id = (int) ($_POST['id'] ?? 0);

    $modulFns = [
        'wisata' => ['list' => 'ajaxListWisata', 'delete' => 'ajaxDeleteWisata', 'detail' => 'ajaxDetailWisata'],
        'berita' => ['list' => 'ajaxListBerita', 'delete' => 'ajaxDeleteBerita', 'detail' => 'ajaxDetailBerita'],
        'potensi' => ['list' => 'ajaxListPotensi', 'delete' => 'ajaxDeletePotensi', 'detail' => 'ajaxDetailPotensi'],
        'struktur' => ['list' => 'ajaxListStruktur', 'delete' => 'ajaxDeleteStruktur', 'detail' => 'ajaxDetailStruktur'],
        'kependudukan' => ['list' => 'ajaxListKependudukan', 'delete' => 'ajaxDeleteKependudukan', 'detail' => 'ajaxDetailKependudukan', 'dusun-list' => 'ajaxDusunList', 'dusun-save' => 'ajaxDusunSave', 'dusun-delete' => 'ajaxDusunDelete'],
    ];

    if (!isset($modulFns[$modul])) {
        ajaxResponse(['ok' => false, 'message' => 'Modul tidak dikenal.']);
    }

    switch ($aksi) {
        case 'list':
            ajaxResponse(['ok' => true] + $modulFns[$modul]['list']($p));
        case 'delete':
            if ($id <= 0) {
                ajaxResponse(['ok' => false, 'message' => 'ID tidak valid.']);
            }
            ajaxResponse($modulFns[$modul]['delete']($id));
        case 'detail':
            if ($id <= 0) {
                ajaxResponse(['ok' => false, 'message' => 'ID tidak valid.']);
            }
            ajaxResponse($modulFns[$modul]['detail']($id));
        case 'dusun-list':
            if ($modul === 'kependudukan') {
                $periode = trim((string) ($_POST['periode'] ?? ''));
                if ($periode === '') { ajaxResponse(['ok' => false, 'message' => 'Periode kosong']); }
                $rows = getDusunByPeriode($periode);
                $h = '';
                foreach ($rows as $d) {
                    $h .= '<tr data-id="' . (int)$d['id'] . '">';
                    $h .= '<td class="py-2 px-3 text-on-surface">' . e($d['nama_dusun']) . '</td>';
                    $h .= '<td class="py-2 px-3 text-on-surface text-right font-label-mono">' . number_format((int)$d['jumlah_laki']) . '</td>';
                    $h .= '<td class="py-2 px-3 text-on-surface text-right font-label-mono">' . number_format((int)$d['jumlah_perempuan']) . '</td>';
                    $h .= '<td class="py-2 px-3 text-on-surface text-right font-label-mono">' . number_format((int)$d['jumlah_kk']) . '</td>';
                    $h .= '<td class="py-2 px-3 text-on-surface text-right font-label-mono">' . number_format((int)$d['jumlah_jiwa']) . '</td>';
                    $h .= '<td class="py-2 px-3 text-right"><button type="button" data-dusun-delete data-id="' . (int)$d['id'] . '" class="text-error hover:text-error/80 transition-colors"><span class="material-symbols-outlined text-[18px]">delete</span></button></td>';
                    $h .= '</tr>';
                }
                ajaxResponse(['ok' => true, 'html' => $h, 'rows' => $rows]);
            }
            break;
        case 'dusun-save':
            if ($modul === 'kependudukan') {
                csrfValidate();
                $periode = trim((string) ($_POST['periode'] ?? ''));
                $namaDusun = trim((string) ($_POST['nama_dusun'] ?? ''));
                if ($periode === '' || $namaDusun === '') { ajaxResponse(['ok' => false, 'message' => 'Periode dan nama dusun wajib diisi']); }
                $data = ['periode' => $periode, 'nama_dusun' => $namaDusun, 'jumlah_laki' => (int)($_POST['jumlah_laki'] ?? 0), 'jumlah_perempuan' => (int)($_POST['jumlah_perempuan'] ?? 0), 'jumlah_kk' => (int)($_POST['jumlah_kk'] ?? 0), 'jumlah_jiwa' => (int)($_POST['jumlah_jiwa'] ?? 0)];
                $did = saveDusunKependudukan($data);
                ajaxResponse(['ok' => $did > 0, 'message' => $did > 0 ? 'Dusun berhasil disimpan.' : 'Gagal menyimpan dusun.']);
            }
            break;
        case 'dusun-delete':
            if ($modul === 'kependudukan') {
                csrfValidate();
                $did = (int)($_POST['id'] ?? 0);
                $ok = $did > 0 && deleteDusunKependudukan($did);
                ajaxResponse(['ok' => $ok, 'message' => $ok ? 'Dusun dihapus.' : 'Gagal menghapus.']);
            }
            break;
        default:
            ajaxResponse(['ok' => false, 'message' => 'Aksi tidak dikenal.']);
    }
}
