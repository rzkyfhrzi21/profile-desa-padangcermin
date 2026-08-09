<?php
declare(strict_types=1);

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function formatTanggal(?string $tanggal): string
{
    if ($tanggal === null || $tanggal === '') {
        return '-';
    }
    $ts = strtotime($tanggal);
    if ($ts === false) {
        return htmlspecialchars($tanggal);
    }
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

function formatAngka($nilai): string
{
    return number_format((int) $nilai, 0, ',', '.');
}

function redirect(string $path): never
{
    header('Location: ' . APP_BASE . $path);
    exit;
}

function flash(string $tipe, string $pesan): void
{
    $_SESSION['flash'][] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function getFlash(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function old(string $key, string $default = ''): string
{
    return htmlspecialchars((string) ($_POST[$key] ?? $default), ENT_QUOTES, 'UTF-8');
}

function isPost(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function truncate(string $text, int $max = 155): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)) ?? '');
    if (mb_strlen($text) <= $max) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $max - 1), " \t\n\r\0\x0B.,;:") . '…';
}

function assetUrl(string $path): string
{
    $base = APP_BASE !== '' ? APP_BASE . '/public' : '';
    $url = $base . '/assets/' . ltrim($path, '/');
    $file = dirname(__DIR__) . '/public/assets/' . ltrim($path, '/');
    if (is_file($file)) {
        $url .= '?v=' . filemtime($file);
    }
    return $url;
}

function uploadUrl(string $path): string
{
    if ($path === '' || $path === null) {
        return '';
    }
    return APP_BASE . '/uploads/' . ltrim($path, '/');
}
