<?php
declare(strict_types=1);

const UPLOAD_FOTO_EXT = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
    'image/x-icon' => 'ico',
    'image/vnd.microsoft.icon' => 'ico',
    'image/heic' => 'heic',
    'image/heif' => 'heif',
];

const UPLOAD_VIDEO_EXT = [
    'video/mp4'  => 'mp4',
    'video/x-matroska' => 'mkv',
    'video/quicktime' => 'mov',
    'video/webm' => 'webm',
];

/**
 * Validasi & simpan upload foto (maks 2 MB).
 * Cek MIME asli via finfo (bukan ekstensi), rename ke nama random.
 * $alt opsional (label konteks saja, tidak disimpan ke DB).
 */
function handleUpload(array $file, string $subfolder, string $alt = '', int $maxBytes = 2 * 1024 * 1024): array
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Upload gagal. Coba lagi.'];
    }
    if ($file['size'] > $maxBytes) {
        return ['ok' => false, 'error' => 'Ukuran foto maksimal 2 MB.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset(UPLOAD_FOTO_EXT[$mime])) {
        return ['ok' => false, 'error' => 'Tipe file tidak diizinkan. Gunakan JPG, PNG, WebP, GIF, ICO, atau HEIC/HEIF.'];
    }

    $dir = UPLOAD_PATH . '/' . $subfolder;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $nama = bin2hex(random_bytes(8)) . '.' . UPLOAD_FOTO_EXT[$mime];
    $tujuan = $dir . '/' . $nama;
    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
        return ['ok' => false, 'error' => 'Gagal menyimpan file di server.'];
    }

    return ['ok' => true, 'path' => $subfolder . '/' . $nama];
}

/**
 * Validasi & simpan upload video (maks 15 MB).
 */
function handleUploadVideo(array $file, string $subfolder, int $maxBytes = 15 * 1024 * 1024): array
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Upload gagal. Coba lagi.'];
    }
    if ($file['size'] > $maxBytes) {
        return ['ok' => false, 'error' => 'Ukuran video maksimal 15 MB.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset(UPLOAD_VIDEO_EXT[$mime])) {
        return ['ok' => false, 'error' => 'Tipe file tidak diizinkan. Gunakan MP4, MKV, MOV, atau WebM.'];
    }

    $dir = UPLOAD_PATH . '/' . $subfolder;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $nama = bin2hex(random_bytes(8)) . '.' . UPLOAD_VIDEO_EXT[$mime];
    $tujuan = $dir . '/' . $nama;
    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
        return ['ok' => false, 'error' => 'Gagal menyimpan file di server.'];
    }

    return ['ok' => true, 'path' => $subfolder . '/' . $nama];
}
