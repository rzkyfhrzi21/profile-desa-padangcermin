<?php
declare(strict_types=1);
$strukturTree = $strukturTree ?? [];

/**
 * Render satu node + subtree-nya sebagai diagram pohon hierarki (pure CSS).
 */
function renderStrukturNode(array $node, int $depth = 0): string
{
    $isRoot = $depth === 0;
    $children = $node['children'] ?? [];
    $hasChildren = $children !== [];

    $fotoSize = $isRoot ? 'w-24 h-24' : ($depth === 1 ? 'w-16 h-16' : 'w-14 h-14');
    $fotoIc   = $isRoot ? 'text-[40px]' : ($depth === 1 ? 'text-[24px]' : 'text-[20px]');
    $initIc   = $isRoot ? 'text-[28px]' : ($depth === 1 ? 'text-[18px]' : 'text-[16px]');
    $nameSize = $isRoot ? 'text-[15px]' : ($depth === 1 ? 'text-[13px]' : 'text-[12px]');
    $jabSize  = $isRoot ? 'text-[11px]' : 'text-[10px]';
    $cardPad  = $isRoot ? 'px-5 py-4' : 'px-3 py-2.5';

    $foto = strukturFoto($node, $fotoSize, $fotoIc, $initIc);

    $html = '<li>'
        . '<div class="org-node flex flex-col items-center gap-2 ' . $cardPad
        . ' bg-glass-fill backdrop-blur-md rounded-2xl border border-glass-border text-center min-w-0">'
        . $foto
        . '<div class="flex flex-col items-center gap-1 w-full min-w-0">'
        . '<span class="' . $nameSize . ' font-semibold text-on-surface leading-tight max-w-[140px]">' . e($node['nama']) . '</span>'
        . '<span class="' . $jabSize . ' text-primary/80 leading-tight max-w-[140px]">' . e($node['jabatan']) . '</span>'
        . '</div>'
        . '</div>';

    if ($hasChildren) {
        $html .= '<ul class="org-children">';
        foreach ($children as $child) {
            $html .= renderStrukturNode($child, $depth + 1);
        }
        $html .= '</ul>';
    }

    return $html . '</li>';
}

/**
 * Foto kartu struktur: tampil jika file ada di penyimpanan,
 * fallback inisial nama jika file tidak ditemukan,
 * ikon person jika kolom foto kosong.
 */
function strukturFoto(array $node, string $fotoSize, string $fotoIc, string $initIc): string
{
    $foto = $node['foto'] ?? '';
    if (fotoAda($foto)) {
        $url = uploadUrl($foto);
        return '<img class="' . $fotoSize . ' rounded-full object-cover border-2 border-primary/40 shadow-md cursor-pointer" data-skeleton
              alt="Foto ' . e($node['nama']) . '" loading="lazy" src="' . e($url) . '" data-lightbox="' . e($url) . '"/>';
    }
    if ($foto !== '') {
        return avatarInisial($node['nama'], $fotoSize, $initIc);
    }
    return '<div class="' . $fotoSize . ' rounded-full bg-surface-container-highest border border-glass-border flex items-center justify-center">
          <span class="material-symbols-outlined ' . $fotoIc . ' text-primary/60">person</span>
       </div>';
}

function orgCard(array $node, string $level): string
{
    $sizes = [
        'root'  => ['w-24 h-24', 'text-[40px]', 'text-[28px]', 'text-[15px]', 'text-[11px]', 'px-5 py-4'],
        'sekre' => ['w-16 h-16', 'text-[24px]', 'text-[18px]', 'text-[13px]', 'text-[10px]', 'px-3 py-2.5'],
        'staff' => ['w-14 h-14', 'text-[20px]', 'text-[16px]', 'text-[12px]', 'text-[10px]', 'px-3 py-2.5'],
        'kadus' => ['w-14 h-14', 'text-[20px]', 'text-[16px]', 'text-[12px]', 'text-[10px]', 'px-3 py-2.5'],
    ];
    [$fotoSize, $fotoIc, $initIc, $nameSize, $jabSize, $cardPad] = $sizes[$level];

    $foto = strukturFoto($node, $fotoSize, $fotoIc, $initIc);

    return '<div class="org-node flex flex-col items-center gap-2 ' . $cardPad
        . ' bg-glass-fill backdrop-blur-md rounded-2xl border border-glass-border text-center min-w-0">'
        . $foto
        . '<div class="flex flex-col items-center gap-1 w-full min-w-0">'
        . '<span class="' . $nameSize . ' font-semibold text-on-surface leading-tight max-w-[140px]">' . e($node['nama']) . '</span>'
        . '<span class="' . $jabSize . ' text-primary/80 leading-tight max-w-[140px]">' . e($node['jabatan']) . '</span>'
        . '</div>'
        . '</div>';
}

function orgCardStatic(string $nama, string $jabatan, string $icon): string
{
    return '<div class="org-node flex flex-col items-center gap-2 px-4 py-3 bg-glass-fill backdrop-blur-md rounded-2xl border border-glass-border text-center min-w-0">'
        . '<div class="w-14 h-14 rounded-full bg-surface-container-highest border border-glass-border flex items-center justify-center">'
        . '<span class="material-symbols-outlined text-[20px] text-primary/60">' . $icon . '</span>'
        . '</div>'
        . '<div class="flex flex-col items-center gap-1 w-full min-w-0">'
        . '<span class="text-[12px] font-semibold text-on-surface leading-tight max-w-[140px]">' . e($nama) . '</span>'
        . '<span class="text-[10px] text-primary/80 leading-tight max-w-[140px]">' . e($jabatan) . '</span>'
        . '</div>'
        . '</div>';
}
?>
<style>
.org-tree,
.org-tree ul {
    display: flex;
    padding: 0;
    margin: 0;
    list-style: none;
    align-items: flex-start;
}
.org-tree {
    flex-direction: column;
    align-items: center;
    width: max-content;
    margin: 0 auto;
}
.org-tree ul {
    position: relative;
    justify-content: center;
    flex-wrap: wrap;
    padding-top: 28px;
    gap: 0 10px;
}
.org-tree ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 0;
    height: 28px;
    border-left: 2px solid rgba(158, 230, 56, 0.35);
}
.org-tree li {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 28px 8px 0;
}
.org-tree li::before,
.org-tree li::after {
    content: '';
    position: absolute;
    top: 0;
    right: 50%;
    width: 50%;
    height: 28px;
    border-top: 2px solid rgba(158, 230, 56, 0.35);
}
.org-tree li::after {
    right: auto;
    left: 50%;
    border-left: 2px solid rgba(158, 230, 56, 0.35);
}
.org-tree li:only-child::after,
.org-tree li:only-child::before {
    display: none;
}
.org-tree li:only-child {
    padding-top: 0;
}
.org-tree li:only-child > ul::before {
    height: 28px;
}
.org-tree li:first-child::before,
.org-tree li:last-child::after {
    border-top: 0 none;
}
.org-tree li:last-child::before {
    border-top: 2px solid rgba(158, 230, 56, 0.35);
    border-right: 2px solid rgba(158, 230, 56, 0.35);
    border-radius: 0 8px 0 0;
}
.org-tree li:first-child::after {
    border-radius: 8px 0 0 0;
}
.org-scroll {
    overflow-x: auto;
    overflow-y: hidden;
    padding: 6px 8px 12px;
    margin: 0 -8px;
}
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
    min-height: 640px;
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
.org-custom .org-sekre .org-node {
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
@media (prefers-reduced-motion: reduce) {
    .org-tree li,
    .org-tree ul {
        padding-top: 12px;
    }
}
</style>
<section id="struktur" class="w-full py-14 px-margin-mobile lg:px-margin-desktop bg-surface-dim relative reveal">
<div class="max-w-container-max mx-auto">
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-stack-lg gap-4">
<div>
<h2 class="font-label-mono text-label-mono text-primary mb-2">STRUKTUR</h2>
<h3 class="font-headline-lg text-[24px] md:text-[26px] text-on-surface max-w-lg">Perangkat Desa Padang Cermin.</h3>
</div>
</div>
<?php if ($strukturTree === []): ?>
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] p-6 border border-glass-border text-center text-on-surface-variant font-body-md">Data struktur organisasi belum tersedia.</div>
<?php else: ?>
<?php
$kades = $strukturTree[0] ?? null;
$sekre = null;
$kasi = [];
$kaur = [];
$kadusList = [];
if ($kades !== null) {
    $kadesId = (int) ($kades['id'] ?? 0);
    $stack = [$kades];
    while ($stack !== []) {
        $node = array_pop($stack);
        if ((int) ($node['id'] ?? 0) !== $kadesId) {
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
        $children = $node['children'] ?? [];
        for ($i = count($children) - 1; $i >= 0; $i--) {
            $stack[] = $children[$i];
        }
    }
}
$customOk = $kades !== null && $sekre !== null && ($kasi !== [] || $kaur !== [] || $kadusList !== []);
?>
<?php if ($customOk): ?>
<div class="hidden md:block">
<div class="org-custom">
<div class="flex items-center justify-center gap-2 md:gap-4">
<?= orgCardStatic('BPD', 'Badan Permusyawaratan Desa', 'account_balance') ?>
<div class="w-8 md:w-12 self-stretch flex items-center">
<div class="w-full border-t-2 border-primary/30"></div>
</div>
<?= orgCard($kades, 'root') ?>
<div class="w-8 md:w-12 self-stretch flex items-center">
<div class="w-full border-t-2 border-primary/30"></div>
</div>
<?= orgCardStatic('LPM', 'Lembaga Pemberdayaan Masyarakat', 'groups') ?>
</div>
<div class="org-body">
<div class="org-trunk" aria-hidden="true"></div>
<div class="org-sekre">
<?= orgCard($sekre, 'sekre') ?>
</div>
<?php if ($kasi !== [] || $kaur !== []): ?>
<div class="org-staffrow">
<div class="org-childrow">
<?php foreach (array_merge($kasi, $kaur) as $s): ?>
<div class="org-childrow-item"><?= orgCard($s, 'staff') ?></div>
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
<div class="org-childrow-item"><?= orgCard($kd, 'kadus') ?></div>
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
<?= orgCardStatic('BPD', 'Badan Permusyawaratan Desa', 'account_balance') ?>
<?= orgCard($kades, 'root') ?>
<?= orgCardStatic('LPM', 'Lembaga Pemberdayaan Masyarakat', 'groups') ?>
<div class="org-mline"></div>
<?= orgCard($sekre, 'sekre') ?>
<?php if ($kasi !== [] || $kaur !== []): ?>
<div class="org-mline"></div>
<?php foreach (array_merge($kasi, $kaur) as $s): ?>
<?= orgCard($s, 'staff') ?>
<?php endforeach; ?>
<?php endif; ?>
<?php if ($kadusList !== []): ?>
<div class="org-mline"></div>
<?php foreach ($kadusList as $kd): ?>
<?= orgCard($kd, 'kadus') ?>
<?php endforeach; ?>
<?php endif; ?>
</div>
</div>
<?php else: ?>
<div class="org-scroll">
<div class="org-tree">
<?php foreach ($strukturTree as $root): ?>
<?= renderStrukturNode($root, 0) ?>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>
<?php endif; ?>
</div>
</section>