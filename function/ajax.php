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
    $noBase = ($p['page'] - 1) * $limit;
    $noIdx  = 0;
    if ($rows_data === []) {
        $rows[] = '<div class="col-span-full py-16 flex flex-col items-center gap-3 text-center"><span class="material-symbols-outlined text-[48px] text-on-surface-variant/40">landscape</span><p class="text-body-md font-body-md text-on-surface-variant">' . ($p['q'] !== '' ? 'Tidak ada wisata yang cocok dengan pencarian.' : 'Belum ada data wisata.') . '</p></div>';
    }
    foreach ($rows_data as $w) {
        $noIdx++;
        $no = $noBase + $noIdx;
        $gambarUtama = $w['gambar'][0]['path_gambar'] ?? ($w['gambar_utama'] ?? '');
        $imgHtml = $gambarUtama !== ''
            ? '<img src="' . uploadUrl($gambarUtama) . '" alt="' . e($w['nama']) . '" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-lightbox="' . uploadUrl($gambarUtama) . '" data-skeleton loading="lazy">'
            : '<div class="w-full h-full flex items-center justify-center bg-surface-container-high"><span class="material-symbols-outlined text-on-surface-variant text-[48px]">landscape</span></div>';
        $statusBadge = $w['status'] === 'publish'
            ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-primary/10 text-primary text-caption font-caption border border-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span>Published</span>'
            : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant text-caption font-caption border border-glass-border">Draft</span>';
        $card = '<div class="group relative bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border overflow-hidden hover:border-primary/40 hover:-translate-y-1 transition-all duration-300">';
        $card .= '<div class="relative aspect-[8/3] overflow-hidden bg-surface-container-high">';
        $card .= $imgHtml;
        $card .= '<div class="absolute top-2 left-2"><span class="bg-black/60 text-white text-[11px] font-mono px-2 py-0.5 rounded-full">#' . $no . '</span></div>';
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
    $noBase = ($p['page'] - 1) * $limit;
    $noIdx  = 0;
    if ($berita === []) {
        $html = ajaxEmptyState(7, 'newspaper', $p['q'] !== '' ? 'Tidak ada berita yang cocok dengan pencarian.' : 'Belum ada berita.');
    }
    foreach ($berita as $b) {
        $noIdx++;
        $no = $noBase + $noIdx;
        $tgl = $b['published_at'] !== null ? date('d M Y', strtotime($b['published_at'])) : '-';
        $html .= '<tr class="border-b border-glass-border/30 hover:bg-surface-container-highest/50 transition-colors group/row">'
            . '<td class="py-4 px-4 text-center"><span class="text-label-mono font-label-mono text-[12px] text-on-surface-variant/60">' . $no . '</span></td>'
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
    $noBase = ($p['page'] - 1) * $limit;
    $noIdx  = 0;
    if ($rows === []) {
        $html = ajaxEmptyState(6, 'psychiatry', $p['q'] !== '' ? 'Tidak ada potensi yang cocok dengan pencarian.' : 'Belum ada data potensi desa.');
    }
    foreach ($rows as $po) {
        $noIdx++;
        $no = $noBase + $noIdx;
        $kat = trim((string) ($po['kategori'] ?? ''));
        $katLabel = $kat !== '' ? ($kategoriLabel[$kat] ?? ucwords(str_replace(['_', '-'], ' ', $kat))) : '-';
        $html .= '<tr class="border-b border-glass-border/30 hover:bg-surface-container-highest/50 transition-colors group/row">'
            . '<td class="py-4 px-4 text-center w-12"><span class="text-label-mono font-label-mono text-[12px] text-on-surface-variant/60">' . $no . '</span></td>'
            . '<td class="py-4 px-4"><span class="font-medium group-hover/row:text-primary transition-colors line-clamp-2 sm:line-clamp-1">' . e($po['judul']) . '</span></td>'
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
        // Sertakan pencarian nama parent juga
        $where[] = '(s.nama LIKE ? OR s.jabatan LIKE ? OR p.jabatan LIKE ?)';
        $params[] = '%' . $p['q'] . '%';
        $params[] = '%' . $p['q'] . '%';
        $params[] = '%' . $p['q'] . '%';
    }
    $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

    // LEFT JOIN ke diri sendiri untuk ambil jabatan & nama atasan langsung
    $baseSql = ' FROM struktur_organisasi s LEFT JOIN struktur_organisasi p ON s.parent_id = p.id';

    $stmtCount = $db->prepare('SELECT COUNT(*)' . $baseSql . $whereSql);
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $limit  = AJAX_PAGE_LIMIT;
    $offset = max(0, $p['page'] - 1) * $limit;
    $stmt   = $db->prepare(
        'SELECT s.*, p.nama AS parent_nama, p.jabatan AS parent_jabatan'
        . $baseSql . $whereSql
        . ' ORDER BY s.urutan ASC, s.id ASC'
        . ' LIMIT ' . $limit . ' OFFSET ' . $offset
    );
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $html = '';
    $noBase = ($p['page'] - 1) * $limit;
    $noIdx  = 0;
    if ($rows === []) {
        $html = ajaxEmptyState(7, 'account_tree', $p['q'] !== '' ? 'Tidak ada pegawai yang cocok dengan pencarian.' : 'Belum ada data struktur organisasi.');
    }
    foreach ($rows as $n) {
        $noIdx++;
        $no = $noBase + $noIdx;
        $foto = !empty($n['foto'])
            ? '<div class="w-10 h-10 rounded-full overflow-hidden shrink-0 border border-glass-border/50"><img class="w-full h-full object-cover cursor-pointer" data-lightbox="' . uploadUrl($n['foto']) . '" data-skeleton alt="Foto ' . e($n['nama']) . '" src="' . uploadUrl($n['foto']) . '"/></div>'
            : '<div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center shrink-0 border border-glass-border/50"><span class="material-symbols-outlined text-[18px] text-on-surface-variant/50">person</span></div>';

        $parentLabel = $n['parent_jabatan'] !== null
            ? e($n['parent_jabatan'])
            : '<span class="text-primary/70">Root</span>';
        $urtLabel = '<div class="flex flex-col gap-0.5">'
            . '<span class="text-label-mono font-label-mono text-[11px] text-on-surface-variant/50 leading-none">di bawah</span>'
            . '<span class="text-caption font-caption text-on-surface-variant leading-tight">' . $parentLabel . '</span>'
            . '<span class="text-label-mono font-label-mono text-primary text-[11px]">ke-' . (int) $n['urutan'] . '</span>'
            . '</div>';

        $html .= '<tr class="border-b border-glass-border/30 hover:bg-surface-container-highest/50 transition-colors group/row">'
            . '<td class="py-4 px-4 text-center w-12"><span class="text-label-mono font-label-mono text-[12px] text-on-surface-variant/60">' . $no . '</span></td>'
            . '<td class="py-4 px-4"><div class="flex items-center gap-3">' . $foto . '<span class="font-medium group-hover/row:text-primary transition-colors">' . e($n['nama']) . '</span></div></td>'
            . '<td class="py-4 px-4 text-on-surface-variant">' . e($n['jabatan']) . '</td>'
            . '<td class="py-4 px-4 text-on-surface-variant">' . e($n['pendidikan_terakhir'] ?? '-') . '</td>'
            . '<td class="py-4 px-4">' . $urtLabel . '</td>'
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

                $db2 = getDb();
                /* Semua dusun master (aktif), join ke data dusun di periode ini */
                $stmt2 = $db2->prepare("
                    SELECT dm.id AS master_id, dm.nama AS nama_dusun, dm.urutan,
                           kd.id AS dusun_id,
                           COALESCE(kd.jumlah_laki, 0) AS jumlah_laki,
                           COALESCE(kd.jumlah_perempuan, 0) AS jumlah_perempuan,
                           COALESCE(kd.jumlah_kk, 0) AS jumlah_kk,
                           COALESCE(kd.jumlah_jiwa, 0) AS jumlah_jiwa
                    FROM dusun_master dm
                    LEFT JOIN kependudukan_dusun kd
                        ON kd.nama_dusun = dm.nama AND kd.periode = ?
                    WHERE dm.aktif = 1
                    ORDER BY dm.urutan
                ");
                $stmt2->execute([$periode]);
                $dusunRows = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                $h = '';
                $rowsForJs = [];
                foreach ($dusunRows as $d) {
                    $did = $d['dusun_id'];
                    $h .= '<tr class="border-b border-black/6 text-coklat" data-id="' . (int)($did ?? 0) . '" data-nama="' . e($d['nama_dusun']) . '">';
                    $h .= '<td class="py-2 px-3 font-medium text-coklat">' . e($d['nama_dusun']) . '</td>';
                    $h .= '<td class="py-2 px-3 text-right"><input type="number" min="0" class="dusun-inp w-20 bg-admin-bg border border-black/10 rounded-lg px-2 py-1 text-label-mono text-coklat text-right focus:border-hijau focus:outline-none" data-field="laki" value="' . (int)$d['jumlah_laki'] . '"/></td>';
                    $h .= '<td class="py-2 px-3 text-right"><input type="number" min="0" class="dusun-inp w-20 bg-admin-bg border border-black/10 rounded-lg px-2 py-1 text-label-mono text-coklat text-right focus:border-hijau focus:outline-none" data-field="perempuan" value="' . (int)$d['jumlah_perempuan'] . '"/></td>';
                    $h .= '<td class="py-2 px-3 text-right"><input type="number" min="0" class="dusun-inp w-20 bg-admin-bg border border-black/10 rounded-lg px-2 py-1 text-label-mono text-coklat text-right focus:border-hijau focus:outline-none" data-field="kk" value="' . (int)$d['jumlah_kk'] . '"/></td>';
                    $h .= '<td class="py-2 px-3 text-right"><input type="number" min="0" class="dusun-inp w-20 bg-admin-bg border border-black/10 rounded-lg px-2 py-1 text-label-mono text-coklat text-right focus:border-hijau focus:outline-none" data-field="jiwa" value="' . (int)$d['jumlah_jiwa'] . '"/></td>';
                    $h .= '</tr>';
                    $rowsForJs[] = [
                        'jumlah_kk'        => (int)$d['jumlah_kk'],
                        'jumlah_jiwa'      => (int)$d['jumlah_jiwa'],
                        'jumlah_laki'      => (int)$d['jumlah_laki'],
                        'jumlah_perempuan' => (int)$d['jumlah_perempuan'],
                    ];
                }
                ajaxResponse(['ok' => true, 'html' => $h, 'rows' => $rowsForJs]);
            }
            break;
        case 'dusun-save':
            if ($modul === 'kependudukan') {
                csrfValidate();
                $periode   = trim((string) ($_POST['periode'] ?? ''));
                $namaDusun = trim((string) ($_POST['nama_dusun'] ?? ''));
                if ($periode === '' || $namaDusun === '') { ajaxResponse(['ok' => false, 'message' => 'Periode dan nama dusun wajib diisi']); }
                $db3 = getDb();
                /* Upsert: update jika sudah ada, insert jika belum */
                $existing = $db3->prepare('SELECT id FROM kependudukan_dusun WHERE periode = ? AND nama_dusun = ?');
                $existing->execute([$periode, $namaDusun]);
                $existId = $existing->fetchColumn();
                if ($existId) {
                    $upd = $db3->prepare('UPDATE kependudukan_dusun SET jumlah_laki=?, jumlah_perempuan=?, jumlah_kk=?, jumlah_jiwa=?, updated_at=NOW() WHERE id=?');
                    $ok3 = $upd->execute([(int)($_POST['jumlah_laki']??0),(int)($_POST['jumlah_perempuan']??0),(int)($_POST['jumlah_kk']??0),(int)($_POST['jumlah_jiwa']??0),(int)$existId]);
                } else {
                    $ins = $db3->prepare('INSERT INTO kependudukan_dusun (periode,nama_dusun,jumlah_laki,jumlah_perempuan,jumlah_kk,jumlah_jiwa) VALUES (?,?,?,?,?,?)');
                    $ok3 = $ins->execute([$periode,$namaDusun,(int)($_POST['jumlah_laki']??0),(int)($_POST['jumlah_perempuan']??0),(int)($_POST['jumlah_kk']??0),(int)($_POST['jumlah_jiwa']??0)]);
                }
                ajaxResponse(['ok' => (bool)$ok3, 'message' => $ok3 ? 'Data dusun ' . $namaDusun . ' disimpan.' : 'Gagal menyimpan.']);
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
        case 'dusun-master-list':
            if ($modul === 'kependudukan') {
                $dm = getDb()->query('SELECT id, nama, urutan, aktif FROM dusun_master ORDER BY urutan')->fetchAll(PDO::FETCH_ASSOC);
                $h = '';
                foreach ($dm as $i => $row) {
                    $h .= '<tr class="border-b border-black/6 text-coklat"><td class="py-2 px-3">' . ($i+1) . '</td><td class="py-2 px-3 font-medium">' . e($row['nama']) . '</td><td class="py-2 px-3 text-center text-abu">' . $row['urutan'] . '</td>';
                    $h .= '<td class="py-2 px-3 text-right"><button type="button" data-master-delete data-id="' . (int)$row['id'] . '" data-nama="' . e($row['nama']) . '" class="inline-flex w-9 h-9 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 transition-colors" aria-label="Hapus dusun ' . e($row['nama']) . '"><span class="material-symbols-outlined text-[18px]">delete</span></button></td></tr>';
                }
                ajaxResponse(['ok' => true, 'html' => $h, 'rows' => $dm]);
            }
            break;
        case 'dusun-master-save':
            if ($modul === 'kependudukan') {
                csrfValidate();
                $nama = trim((string)($_POST['nama'] ?? ''));
                $urt  = max(0, (int)($_POST['urutan'] ?? 0));
                if ($nama === '') { ajaxResponse(['ok' => false, 'message' => 'Nama dusun wajib diisi.']); }
                $db4 = getDb();
                if ($urt === 0) {
                    $urt = (int)$db4->query('SELECT COALESCE(MAX(urutan),0)+1 FROM dusun_master')->fetchColumn();
                }
                $ins = $db4->prepare('INSERT IGNORE INTO dusun_master (nama, urutan) VALUES (?, ?)');
                $ins->execute([$nama, $urt]);
                $newId = (int)$db4->lastInsertId();
                ajaxResponse(['ok' => $newId > 0, 'message' => $newId > 0 ? 'Dusun "' . $nama . '" ditambahkan.' : 'Nama dusun sudah ada.']);
            }
            break;
        case 'dusun-master-delete':
            if ($modul === 'kependudukan') {
                csrfValidate();
                $mid = (int)($_POST['id'] ?? 0);
                if ($mid <= 0) { ajaxResponse(['ok' => false, 'message' => 'ID tidak valid.']); }
                try {
                    $namaDihapus = deleteDusunMaster($mid);
                    if ($namaDihapus === null) {
                        ajaxResponse(['ok' => false, 'message' => 'Dusun tidak ditemukan.']);
                    }
                    catatLog('hapus dusun: ' . $namaDihapus, 'dusun_master', $mid);
                    ajaxResponse(['ok' => true, 'message' => 'Dusun "' . $namaDihapus . '" dan seluruh data periodenya berhasil dihapus.']);
                } catch (Throwable $t) {
                    ajaxResponse(['ok' => false, 'message' => 'Gagal menghapus dusun dan data periodenya.']);
                }
            }
            break;
        default:
            ajaxResponse(['ok' => false, 'message' => 'Aksi tidak dikenal.']);
    }
}
