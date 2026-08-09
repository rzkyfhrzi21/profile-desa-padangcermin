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
    $nameSize = $isRoot ? 'text-[15px]' : ($depth === 1 ? 'text-[13px]' : 'text-[12px]');
    $jabSize  = $isRoot ? 'text-[11px]' : 'text-[10px]';
    $cardPad  = $isRoot ? 'px-5 py-4' : 'px-3 py-2.5';

    $foto = !empty($node['foto'])
        ? '<img class="' . $fotoSize . ' rounded-full object-cover border-2 border-primary/40 shadow-md" data-skeleton
              alt="Foto ' . e($node['nama']) . '" loading="lazy" src="' . e(uploadUrl($node['foto'])) . '"/>'
        : '<div class="' . $fotoSize . ' rounded-full bg-surface-container-highest border border-glass-border flex items-center justify-center">
              <span class="material-symbols-outlined ' . $fotoIc . ' text-primary/60">person</span>
           </div>';

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
    max-width: 640px;
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
    min-width: 0;
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
<div class="org-scroll">
<div class="org-tree">
<?php foreach ($strukturTree as $root): ?>
<?= renderStrukturNode($root, 0) ?>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>
</div>
</section>
