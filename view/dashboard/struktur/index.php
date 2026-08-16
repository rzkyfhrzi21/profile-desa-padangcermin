<?php
declare(strict_types=1);

$judulHalaman = 'Manajemen Struktur';

$allRows      = getStrukturAll();
$totalAnggota = count($allRows);

/* Stats */
$totalRoot = count(array_filter($allRows, fn($r) => $r['parent_id'] === null));
$totalFoto   = count(array_filter($allRows, fn($r) => !empty($r['foto'])));
foreach ($allRows as $r) {
    $nodeMap[(int) $r['id']] = $r;
    $pid = $r['parent_id'] !== null ? (int) $r['parent_id'] : 0;
    $children[$pid][] = (int) $r['id'];
}
/* Sort children by urutan ASC */
foreach ($children as &$_ch) {
    usort($_ch, static function (int $a, int $b) use ($nodeMap): int {
        return (int) ($nodeMap[$a]['urutan'] ?? 0) <=> (int) ($nodeMap[$b]['urutan'] ?? 0);
    });
}
unset($_ch);
$roots = $children[0] ?? []; // root = nodes dengan parent_id = NULL

/* ── Helper render card ─────────────────────────────────────────────── */
function renderOrgCard(array $n, string $size = 'md'): string
{
    $isLg = $size === 'lg';
    $isSm = $size === 'sm';
    $avc  = $isLg ? 'w-20 h-20' : ($isSm ? 'w-12 h-12' : 'w-14 h-14');
    $ic   = $isLg ? 'text-[32px]' : ($isSm ? 'text-[16px]' : 'text-[22px]');
    $nc   = $isLg ? 'text-body-md font-semibold' : ($isSm ? 'text-[11px] font-semibold leading-tight' : 'text-caption font-semibold leading-tight');
    $jc   = $isLg ? 'text-caption font-medium' : 'text-[10px]';
    $pad  = $isLg ? 'px-5 py-4' : ($isSm ? 'px-3 py-2' : 'px-4 py-3');

    $fotoPath = $n['foto'] ?? '';
    $img = $fotoPath !== '' && fotoAda($fotoPath)
        ? '<img src="' . e(uploadUrl($fotoPath)) . '" alt="Foto ' . e($n['nama']) . '" '
          . 'class="' . $avc . ' rounded-full object-cover border-2 border-primary/40 shadow-md cursor-pointer" '
          . 'data-lightbox="' . e(uploadUrl($fotoPath)) . '" data-skeleton loading="lazy">'
        : ($fotoPath !== ''
            ? avatarInisial($n['nama'], $avc, $ic)
            : '<div class="' . $avc . ' rounded-full bg-surface-container-high flex items-center justify-center border-2 border-glass-border/60 shadow-sm">'
              . '<span class="material-symbols-outlined ' . $ic . ' text-on-surface-variant/40">person</span>'
              . '</div>');

    return '<div class="org-card group flex flex-col items-center gap-2 ' . $pad . ' '
        . 'bg-glass-fill backdrop-blur-md rounded-2xl border border-glass-border '
        . 'hover:border-primary/50 hover:-translate-y-1 transition-all duration-300 min-w-0 text-center">'
        . $img
        . '<div class="w-full min-w-0">'
        . '<p class="' . $nc . ' text-on-surface truncate group-hover:text-primary transition-colors">' . e($n['nama']) . '</p>'
        . '<p class="' . $jc . ' text-primary/70 mt-0.5 leading-tight line-clamp-2">' . e($n['jabatan']) . '</p>'
        . '</div>'
        . '</div>';
}

/* ── Recursive tree renderer ──────────────────────────────────────────── */
function renderOrgTree(array $children, array $nodeMap, int $parentId, int $depth = 0): string
{
    $ids = $children[$parentId] ?? [];
    if (empty($ids)) {
        return '';
    }

    $size       = $depth === 0 ? 'lg' : ($depth === 1 ? 'md' : 'sm');
    $fromOpac   = max(10, 60 - $depth * 15);
    $toOpac     = max(5, 30 - $depth * 8);
    $lineH      = $depth === 0 ? 'h-10' : 'h-6';

    $html  = '<div class="flex flex-col items-center w-full">';
    $html .= '<div class="w-px ' . $lineH . ' bg-gradient-to-b from-primary/' . $fromOpac . ' to-primary/' . $toOpac . '"></div>';
    $html .= '<div class="flex items-start justify-center gap-4 md:gap-6 w-full flex-wrap">';

    foreach ($ids as $id) {
        $node        = $nodeMap[$id];
        $hasChildren = !empty($children[$id]);

        $html .= '<div class="flex flex-col items-center">';
        $html .= renderOrgCard($node, $size);

        if ($hasChildren) {
            $html .= renderOrgTree($children, $nodeMap, $id, $depth + 1);
        }

        $html .= '</div>';
    }

    $html .= '</div>'; // /row
    $html .= '</div>'; // /wrapper

    return $html;
}

require __DIR__ . '/../layout.php';
?>

<!-- ════════════════════════════════════════════════════════════
     SECTION: HEADER + CRUD TABLE
════════════════════════════════════════════════════════════ -->
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-8 gap-4">
  <div class="flex flex-col gap-2">
    <span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Aparatur Desa</span>
    <h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0">Manajemen Struktur &amp; SDM</h1>
    <p class="text-body-md font-body-md text-on-surface-variant m-0">Susunan organisasi pemerintahan pekon, <?= $totalAnggota ?> anggota tercatat.</p>
  </div>
  <a class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300 group whitespace-nowrap"
     href="<?= APP_BASE ?>/dashboard/struktur/form">
    <span class="material-symbols-outlined text-[20px] transition-transform group-hover:rotate-90">add</span>
    Tambah Jabatan
  </a>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-surface-container rounded-2xl p-4 flex items-center gap-3 border border-glass-border/40">
    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
      <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings:'FILL' 1">groups</span>
    </div>
    <div><p class="text-[11px] text-on-surface-variant uppercase tracking-wide">Total Aparatur</p><p class="text-[22px] font-bold font-mono text-on-surface leading-none"><?= $totalAnggota ?></p></div>
  </div>
  <div class="bg-surface-container rounded-2xl p-4 flex items-center gap-3 border border-glass-border/40">
    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
      <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings:'FILL' 1">account_tree</span>
    </div>
    <div><p class="text-[11px] text-on-surface-variant uppercase tracking-wide">Jabatan Puncak</p><p class="text-[22px] font-bold font-mono text-on-surface leading-none"><?= $totalRoot ?></p></div>
  </div>
  <div class="bg-surface-container rounded-2xl p-4 flex items-center gap-3 border border-glass-border/40">
    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
      <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings:'FILL' 1">photo_camera</span>
    </div>
    <div><p class="text-[11px] text-on-surface-variant uppercase tracking-wide">Punya Foto</p><p class="text-[22px] font-bold font-mono text-on-surface leading-none"><?= $totalFoto ?></p></div>
  </div>
</div>

<!-- Card CRUD Table -->
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg relative overflow-hidden hover:border-primary/40 transition-colors duration-500">
  <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>

  <div id="struktur-table" class="relative z-10" data-endpoint="<?= APP_BASE ?>/dashboard/ajax/struktur/list">
    <form id="struktur-filter" class="flex flex-col md:flex-row items-stretch md:items-center gap-3 mb-4" onsubmit="return false;">
      <input class="flex-1 min-w-0 bg-surface-container-highest border border-glass-border rounded-xl py-3 pl-4 pr-4 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50"
             name="q" data-live-search placeholder="Cari nama atau jabatan..." type="text"/>
      <button type="button" data-reset-filter
              class="shrink-0 px-4 py-3 rounded-xl border border-glass-border text-on-surface-variant hover:text-primary hover:border-primary/40 transition-colors text-caption font-caption flex items-center gap-1.5">
        <span class="material-symbols-outlined text-[18px]">restart_alt</span> Reset
      </button>
    </form>

    <div class="table-box">
      <div class="overflow-x-auto">
        <div class="flex items-center justify-between py-4 px-4 text-caption font-caption text-on-surface-variant border-b border-glass-border/50">
          <span data-table-info>Memuat data...</span>
        </div>
        <table class="w-full text-left border-collapse min-w-[640px]">
          <thead>
            <tr class="text-label-mono font-label-mono text-on-surface-variant border-b border-glass-border/50">
              <th class="py-4 px-4 font-medium w-10 text-center">No</th>
              <th class="py-4 px-4 font-medium w-1/3">Pegawai</th>
              <th class="py-4 px-4 font-medium">Jabatan</th>
              <th class="py-4 px-4 font-medium">Pendidikan</th>
              <th class="py-4 px-4 font-medium">Urutan</th>
              <th class="py-4 px-4 font-medium text-right">Aksi</th>
            </tr>
          </thead>
          <tbody data-table-body class="text-body-md font-body-md text-on-surface"></tbody>
        </table>
      </div>
      <div data-table-foot class="flex flex-col sm:flex-row items-center justify-between mt-6 pt-4 border-t border-glass-border/30 gap-4 sm:gap-0"></div>
    </div>
  </div>
</div>
</section>

<!-- ════════════════════════════════════════════════════════════
     SECTION: PREVIEW BAGAN STRUKTUR ORGANISASI (Recursive Tree)
════════════════════════════════════════════════════════════ -->
<section class="mt-10">
  <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-3">
    <div class="flex flex-col gap-1">
      <span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Visualisasi</span>
      <h2 class="text-headline-lg font-headline-lg text-on-background m-0">Preview Bagan Struktur Organisasi</h2>
      <p class="text-body-md font-body-md text-on-surface-variant m-0">Diagram hierarki pemerintahan Desa Padang Cermin</p>
    </div>
    <a href="<?= APP_BASE ?>/dashboard/struktur/form"
       class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary/10 border border-primary/30 text-caption font-caption text-primary hover:bg-primary/20 transition-all duration-200">
      <span class="material-symbols-outlined text-[16px]">add</span> Tambah Jabatan
    </a>
  </div>

  <div class="bg-glass-fill backdrop-blur-md rounded-[24px] border border-glass-border p-6 md:p-10 relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0">
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[500px] h-[300px] bg-primary/3 rounded-full blur-[100px]"></div>
      <div class="absolute bottom-0 right-0 w-64 h-64 bg-primary/3 rounded-full blur-[60px]"></div>
    </div>

    <div class="relative z-10 text-center mb-8">
      <h3 class="text-label-mono font-label-mono tracking-[0.2em] text-on-surface-variant/60 uppercase text-[11px] m-0">
        STRUKTUR ORGANISASI PEMERINTAH DESA PADANG CERMIN
      </h3>
    </div>

    <!-- Org Chart — scrollable horizontal di mobile -->
    <div class="overflow-x-auto -mx-2 px-2 pb-4">
      <div class="relative z-10">

        <?php if ($totalAnggota === 0): ?>
        <div class="flex flex-col items-center justify-center py-20 gap-4 text-center">
          <span class="material-symbols-outlined text-[64px] text-on-surface-variant/20">account_tree</span>
          <p class="text-body-md font-body-md text-on-surface-variant/60 m-0">Belum ada data struktur organisasi.</p>
          <a href="<?= APP_BASE ?>/dashboard/struktur/form"
             class="mt-2 bg-primary text-on-primary text-caption font-caption px-5 py-2.5 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all">
            <span class="material-symbols-outlined text-[16px]">add</span> Tambah Jabatan Pertama
          </a>
        </div>

        <?php else: ?>

        <?php
        $root = !empty($roots) ? $nodeMap[$roots[0]] : null;
        $sekre = null;
        $kasi = [];
        $kaur = [];
        $kadusList = [];
        if ($root !== null) {
            $rootId = (int) $root['id'];
            $stack = [$rootId];
            while ($stack !== []) {
                $id = (int) array_pop($stack);
                if ($id !== $rootId) {
                    $node = $nodeMap[$id];
                    $jab = $node['jabatan'] ?? '';
                    if (stripos($jab, 'Sekretaris') !== false || stripos($jab, 'Sekdes') !== false) {
                        if ($sekre === null) {
                            $sekre = $node;
                        }
                    } elseif (stripos($jab, 'Kasi') !== false) {
                        $kasi[] = $node;
                    } elseif (stripos($jab, 'Kaur') !== false) {
                        $kaur[] = $node;
                    } else {
                        $kadusList[] = $node;
                    }
                }
                $kids = $children[$id] ?? [];
                for ($i = count($kids) - 1; $i >= 0; $i--) {
                    $stack[] = (int) $kids[$i];
                }
            }
        }
        $customOk = $root !== null && $sekre !== null && ($kasi !== [] || $kaur !== [] || $kadusList !== []);
        $bpdCard = '<div class="flex flex-col items-center gap-2 px-4 py-3 min-w-[110px] '
            . 'bg-glass-fill backdrop-blur-md rounded-2xl border border-glass-border '
            . 'hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 text-center">'
            . '<div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center border-2 border-primary/20">'
            . '<span class="material-symbols-outlined text-[22px] text-primary/70" style="font-variation-settings:\'FILL\' 1">policy</span>'
            . '</div>'
            . '<div>'
            . '<p class="text-caption font-semibold text-on-surface m-0">BPD</p>'
            . '<p class="text-[10px] text-on-surface-variant/60 m-0 leading-tight">Badan Permusyawaratan Desa</p>'
            . '</div>'
            . '</div>';
        $lpmCard = '<div class="flex flex-col items-center gap-2 px-4 py-3 min-w-[110px] '
            . 'bg-glass-fill backdrop-blur-md rounded-2xl border border-glass-border '
            . 'hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 text-center">'
            . '<div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center border-2 border-primary/20">'
            . '<span class="material-symbols-outlined text-[22px] text-primary/70" style="font-variation-settings:\'FILL\' 1">groups</span>'
            . '</div>'
            . '<div>'
            . '<p class="text-caption font-semibold text-on-surface m-0">LPM</p>'
            . '<p class="text-[10px] text-on-surface-variant/60 m-0 leading-tight">Lembaga Pemberdayaan Masyarakat</p>'
            . '</div>'
            . '</div>';
        ?>

        <style>
        .org-custom {
            overflow-x: auto;
            padding: 6px 8px 12px;
            margin: 0 -8px;
        }
        .org-custom .org-body {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            min-width: 840px;
            min-height: 620px;
            margin: 0 auto;
        }
        .org-custom .org-trunk {
            width: 2px;
            flex: 1 1 auto;
            min-height: 24px;
            background: rgba(158, 230, 56, 0.35);
        }
        .org-custom .org-sekre {
            position: absolute;
            top: 24px;
            left: calc(50% + 46px);
        }
        .org-custom .org-sekre::before {
            content: '';
            position: absolute;
            top: 0;
            left: -46px;
            width: 92px;
            height: 22px;
            border-top: 2px solid rgba(158, 230, 56, 0.35);
            border-right: 2px solid rgba(158, 230, 56, 0.35);
            border-radius: 0 10px 0 0;
        }
        .org-custom .org-sekre .org-card {
            margin-top: 22px;
        }
        .org-custom .org-staffrow {
            position: absolute;
            top: 220px;
            left: 50%;
            transform: translateX(-50%);
        }
        .org-custom .org-childrow {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .org-custom .org-childrow-item {
            position: relative;
            padding: 34px 7px 0;
        }
        .org-custom .org-childrow-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(158, 230, 56, 0.35);
        }
        .org-custom .org-childrow-item:first-child::before {
            left: 50%;
        }
        .org-custom .org-childrow-item:last-child::before {
            right: 50%;
        }
        .org-custom .org-childrow-item::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 50%;
            width: 2px;
            height: 32px;
            background: rgba(158, 230, 56, 0.35);
        }
        .org-custom .org-kadus-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .org-custom .org-kadus-vline {
            width: 2px;
            height: 34px;
            background: rgba(158, 230, 56, 0.35);
        }
        .org-mline {
            width: 2px;
            height: 24px;
            background: rgba(158, 230, 56, 0.35);
        }
        </style>

        <?php if ($customOk): ?>
        <div class="hidden md:block">
        <div class="org-custom">
        <div class="flex items-center justify-center gap-2 md:gap-4">
          <?= $bpdCard ?>
          <div class="w-8 md:w-12 self-stretch flex items-center">
          <div class="w-full border-t-2 border-primary/30"></div>
          </div>
          <?= renderOrgCard($root, 'lg') ?>
          <div class="w-8 md:w-12 self-stretch flex items-center">
          <div class="w-full border-t-2 border-primary/30"></div>
          </div>
          <?= $lpmCard ?>
        </div>
        <div class="org-body">
        <div class="org-trunk" aria-hidden="true"></div>
        <div class="org-sekre"><?= renderOrgCard($sekre, 'md') ?></div>
        <?php if ($kasi !== [] || $kaur !== []): ?>
        <div class="org-staffrow">
        <div class="org-childrow">
        <?php foreach (array_merge($kasi, $kaur) as $s): ?>
        <div class="org-childrow-item"><?= renderOrgCard($s, 'sm') ?></div>
        <?php endforeach; ?>
        </div>
        </div>
        <?php endif; ?>
        <?php if ($kadusList !== []): ?>
        <div class="org-kadus-wrap">
        <?php foreach (array_chunk($kadusList, 8) as $rIdx => $row): ?>
        <?php if ($rIdx > 0): ?>
        <div class="org-kadus-vline" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="org-childrow">
        <?php foreach ($row as $kd): ?>
        <div class="org-childrow-item"><?= renderOrgCard($kd, 'sm') ?></div>
        <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
        </div>
        </div>
        </div>

        <div class="md:hidden">
        <div class="flex flex-col items-center gap-3">
          <?= $bpdCard ?>
          <?= renderOrgCard($root, 'lg') ?>
          <?= $lpmCard ?>
          <div class="org-mline"></div>
          <?= renderOrgCard($sekre, 'md') ?>
          <?php if ($kasi !== [] || $kaur !== []): ?>
          <div class="org-mline"></div>
          <?php foreach (array_merge($kasi, $kaur) as $s): ?>
          <?= renderOrgCard($s, 'sm') ?>
          <?php endforeach; ?>
          <?php endif; ?>
          <?php if ($kadusList !== []): ?>
          <div class="org-mline"></div>
          <?php foreach ($kadusList as $kd): ?>
          <?= renderOrgCard($kd, 'sm') ?>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
        </div>
        <?php else: ?>
        <div class="min-w-[480px] flex flex-col items-center">

        <!-- Garis BPD — Root — LPM (decorative, hanya jika ada root) -->
        <?php if (!empty($roots)): ?>
        <div class="flex items-center justify-center gap-0 w-full mb-2">
          <!-- BPD (dekoratif, bukan dari DB) -->
          <div class="flex items-center">
            <?= $bpdCard ?>
            <div class="w-14 flex items-center justify-center">
              <div class="w-full border-t-2 border-dashed border-primary/30"></div>
            </div>
          </div>

          <!-- Render semua root node (biasanya 1 = Kepala Desa) -->
          <?php foreach ($roots as $rootId): ?>
          <div class="flex flex-col items-center">
            <div class="relative">
              <?= renderOrgCard($nodeMap[$rootId], 'lg') ?>
              <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 z-10">
                <span class="bg-primary text-on-primary text-[9px] font-label-mono tracking-widest px-3 py-1 rounded-full whitespace-nowrap shadow-md">
                  <?= e($nodeMap[$rootId]['jabatan']) ?>
                </span>
              </div>
            </div>
          </div>
          <?php endforeach; ?>

          <div class="flex items-center">
            <div class="w-14 flex items-center justify-center">
              <div class="w-full border-t-2 border-dashed border-primary/30"></div>
            </div>
            <!-- LPM (dekoratif) -->
            <?= $lpmCard ?>
          </div>
        </div>

        <!-- Render sub-tree dari setiap root secara rekursif -->
        <?php foreach ($roots as $rootId): ?>
        <?= renderOrgTree($children, $nodeMap, $rootId, 0) ?>
        <?php endforeach; ?>

        <?php endif; // end $roots check ?>

        </div>
        <?php endif; ?>

        <?php endif; // end $totalAnggota === 0 check ?>

      </div><!-- /chart -->
    </div><!-- /overflow-x-auto -->

    <!-- Legend -->
    <div class="relative z-10 mt-8 pt-5 border-t border-glass-border/30 flex flex-wrap items-center gap-x-5 gap-y-2 text-[11px] text-on-surface-variant/50">
      <div class="flex items-center gap-1.5">
        <span class="material-symbols-outlined text-[13px]">account_tree</span>
        Kepala Desa &rarr; Sekretaris &rarr; Kasi / Kaur &rarr; Kepala Dusun
      </div>
      <div class="ml-auto flex items-center gap-1">
        <span class="material-symbols-outlined text-[13px]">touch_app</span>
        Klik foto untuk pratinjau besar
      </div>
    </div>

  </div><!-- /card -->
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    AdminUI.initAjaxTable({
        name: 'struktur',
        container: '#struktur-table',
        onRender: function (container) {
            if (window.MediaHelpers) MediaHelpers.initSkeleton(container);
        },
        actions: {
            delete: function (btn) {
                AdminUI.confirmModal(
                    'Hapus Jabatan',
                    btn.dataset.pesan || 'Yakin ingin menghapus data ini?',
                    'Hapus',
                    function () {
                        var endpoint = btn.closest('[data-endpoint]').dataset.endpoint.replace('/list', '/delete');
                        AdminUI.ajax(endpoint, { id: btn.dataset.id })
                            .then(function (res) {
                                if (res.ok) {
                                    AdminUI.showToast('success', res.message);
                                    AdminUI.loadTable('struktur');
                                } else {
                                    AdminUI.showToast('error', res.message || 'Gagal menghapus data.');
                                }
                            })
                            .catch(function () {
                                AdminUI.showToast('error', 'Gagal terhubung ke server.');
                            });
                    }
                );
            }
        }
    });

    /* Init lightbox untuk org chart */
    var chartWrap = document.querySelector('.overflow-x-auto');
    if (chartWrap && window.MediaHelpers) {
        MediaHelpers.initSkeleton(chartWrap);
    }
});
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>
