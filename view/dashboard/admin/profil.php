<?php
declare(strict_types=1);

$judulHalaman = 'Profil Admin';
$admin        = currentAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    $aksi = trim((string) ($_POST['aksi'] ?? ''));

    /* ---- UPDATE PROFIL ---- */
    if ($aksi === 'update_profil') {
        $nama   = trim((string) ($_POST['nama'] ?? ''));
        $errors = [];

        if ($nama === '') {
            $errors[] = 'Nama wajib diisi.';
        }
        if (mb_strlen($nama) > 100) {
            $errors[] = 'Nama maksimal 100 karakter.';
        }

        /* Upload foto — kosongkan input = foto lama tetap */
        $fotoBaru = null;
        $adaFile  = ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        if ($adaFile) {
            $up = handleUpload($_FILES['foto'], 'admin', $nama !== '' ? $nama : 'Admin');
            if (!$up['ok']) {
                $errors[] = $up['error'];
            } else {
                $fotoBaru = $up['path'];
            }
        }

        if ($errors !== []) {
            foreach ($errors as $err) {
                flash('error', $err);
            }
            redirect('/dashboard/admin/profil');
        }

        $db   = getDb();
        $stmt = $db->prepare('UPDATE admins SET nama = ? WHERE id = ?');
        $stmt->execute([$nama, (int) $admin['id']]);

        if ($fotoBaru !== null) {
            if (!empty($admin['foto'])) {
                $fileLama = UPLOAD_PATH . '/' . $admin['foto'];
                if (is_file($fileLama)) {
                    @unlink($fileLama);
                }
            }
            $db->prepare('UPDATE admins SET foto = ? WHERE id = ?')->execute([$fotoBaru, (int) $admin['id']]);
        }

        catatLog('update profil admin', 'admins', (int) $admin['id']);
        flash('success', 'Profil berhasil diperbarui.');
        redirect('/dashboard/admin/profil');
    }

    /* ---- GANTI PASSWORD ---- */
    if ($aksi === 'update_password') {
        $pasLama    = (string) ($_POST['password_lama'] ?? '');
        $passBaru   = (string) ($_POST['password_baru'] ?? '');
        $passKonfirm = (string) ($_POST['password_konfirmasi'] ?? '');
        $errors     = [];

        $db   = getDb();
        $stmt = $db->prepare('SELECT password_hash FROM admins WHERE id = ?');
        $stmt->execute([(int) $admin['id']]);
        $row  = $stmt->fetch();

        if (!$row || !password_verify($pasLama, $row['password_hash'])) {
            $errors[] = 'Password lama tidak sesuai.';
        }
        if (strlen($passBaru) < 8) {
            $errors[] = 'Password baru minimal 8 karakter.';
        }
        if ($passBaru !== $passKonfirm) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        if ($errors !== []) {
            foreach ($errors as $err) {
                flash('error', $err);
            }
            redirect('/dashboard/admin/profil');
        }

        $db->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($passBaru, PASSWORD_BCRYPT), (int) $admin['id']]);
        catatLog('ubah password admin', 'admins', (int) $admin['id']);
        flash('success', 'Password berhasil diubah. Silakan login ulang jika diperlukan.');
        redirect('/dashboard/admin/profil');
    }
}

/* Re-fetch setelah kemungkinan update */
$admin = currentAdmin();

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col gap-2 mb-8">
    <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-primary shadow-lime-glow animate-pulse"></span>
        <span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Akun Admin</span>
    </div>
    <h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0">Profil Admin</h1>
    <p class="text-body-md font-body-md text-on-surface-variant m-0">Kelola informasi akun dan keamanan akses dashboard.</p>
</div>

<div class="grid grid-cols-12 gap-gutter">

    <!-- Kolom Kiri: Avatar + Info -->
    <div class="col-span-12 xl:col-span-4 flex flex-col gap-stack-lg">

        <!-- Avatar Card -->
        <div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-6 flex flex-col items-center gap-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="relative z-10 flex flex-col items-center gap-4 w-full">
                <!-- Avatar -->
                <div id="avatar-wrap" class="relative w-28 h-28 rounded-full border-2 border-primary/40 overflow-hidden bg-surface-container-high flex items-center justify-center shadow-lime-glow">
                    <?php if (!empty($admin['foto'])): ?>
                    <img id="avatar-preview" src="<?= e(uploadUrl($admin['foto'])) ?>" alt="Foto <?= e($admin['nama'] ?? 'Admin') ?>" class="w-full h-full object-cover"/>
                    <?php else: ?>
                    <span id="avatar-icon" class="material-symbols-outlined text-primary text-[52px]" style="font-variation-settings:'FILL' 1">admin_panel_settings</span>
                    <img id="avatar-preview" src="" alt="Preview" class="w-full h-full object-cover absolute inset-0 hidden"/>
                    <?php endif; ?>
                </div>
                <div class="text-center">
                    <p class="font-semibold text-on-surface text-lg leading-tight"><?= e($admin['nama'] ?? 'Admin') ?></p>
                    <span class="text-caption font-caption text-primary">Super Admin</span>
                </div>
                <!-- Upload Avatar -->
                <form method="post" action="<?= APP_BASE ?>/dashboard/admin/profil" enctype="multipart/form-data" class="w-full">
                    <?= csrfField() ?>
                    <input type="hidden" name="aksi" value="update_profil"/>
                    <input type="hidden" name="nama" value="<?= e($admin['nama'] ?? 'Admin') ?>"/>
                    <label for="foto-avatar" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container border border-glass-border text-caption font-caption text-on-surface-variant hover:text-primary hover:border-primary/40 cursor-pointer transition-colors">
                        <span class="material-symbols-outlined text-[18px]">photo_camera</span> Ganti Foto
                    </label>
                    <input id="foto-avatar" name="foto" type="file" accept="image/jpeg,image/jpg,image/png,image/webp,image/heic,image/heif" class="hidden"/>
                    <button type="submit" id="btn-simpan-foto" class="hidden w-full mt-2 bg-primary text-on-primary font-caption text-caption px-4 py-2.5 rounded-xl flex items-center justify-center gap-2 hover:shadow-lime-glow transition-all">
                        <span class="material-symbols-outlined text-[18px]">check</span> Simpan Foto
                    </button>
                </form>
            </div>
        </div>

        <!-- Informasi Sesi -->
        <div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 flex flex-col gap-3 relative overflow-hidden">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-primary text-[20px]">security</span>
                <h2 class="text-headline-sm font-headline-sm text-on-surface m-0">Keamanan Akun</h2>
            </div>
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between py-2 border-b border-glass-border/40">
                    <span class="text-caption font-caption text-on-surface-variant">Status</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 border border-primary/30 text-caption font-caption text-primary">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>Aktif
                    </span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-glass-border/40">
                    <span class="text-caption font-caption text-on-surface-variant">Level Akses</span>
                    <span class="text-caption font-caption text-on-surface font-medium">Super Admin</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-caption font-caption text-on-surface-variant">Username</span>
                    <span class="text-caption font-label-mono text-on-surface"><?= e($admin['username'] ?? '-') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Form Update -->
    <div class="col-span-12 xl:col-span-8 flex flex-col gap-stack-lg">

        <!-- Form Update Profil -->
        <form method="post" action="<?= APP_BASE ?>/dashboard/admin/profil" enctype="multipart/form-data">
            <?= csrfField() ?>
            <input type="hidden" name="aksi" value="update_profil"/>
            <div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
                <div class="absolute top-0 right-0 w-56 h-56 bg-primary/5 rounded-full blur-[60px]"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-surface-container border border-glass-border flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">manage_accounts</span>
                    </div>
                    <h2 class="text-headline-md font-headline-md text-on-surface m-0">Informasi Profil</h2>
                </div>
                <div class="flex flex-col gap-2 relative z-10">
                    <label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="nama-profil">Nama Lengkap</label>
                    <input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="nama-profil" name="nama" placeholder="Masukkan nama lengkap" required type="text" value="<?= e($admin['nama'] ?? '') ?>"/>
                </div>
                <div class="flex flex-col gap-2 relative z-10">
                    <label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="foto-profil">Foto Profil <span class="text-on-surface-variant/50 normal-case">(opsional, maks 2MB)</span></label>
                    <input class="w-full text-caption font-caption text-on-surface-variant file:mr-4 file:rounded-xl file:border-0 file:bg-surface-container-highest file:px-4 file:py-2.5 file:text-on-surface file:cursor-pointer hover:file:bg-surface-container transition-colors" id="foto-profil" name="foto" type="file" accept="image/jpeg,image/jpg,image/png,image/webp,image/heic,image/heif"/>
                    <p class="text-[11px] text-on-surface-variant m-0">Max 2 MB · Kosongkan jika tidak ganti foto</p>
                    <div id="foto-admin-preview-wrap" class="hidden mt-2">
                        <img id="foto-admin-preview" src="" alt="Preview" class="w-24 h-24 rounded-full object-cover border-2 border-primary/40"/>
                    </div>
                </div>
                <div class="flex justify-end relative z-10">
                    <button class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300" type="submit">
                        <span class="material-symbols-outlined text-[20px]">check</span> Simpan Profil
                    </button>
                </div>
            </div>
        </form>

        <!-- Form Ganti Password -->
        <form method="post" action="<?= APP_BASE ?>/dashboard/admin/profil">
            <?= csrfField() ?>
            <input type="hidden" name="aksi" value="update_password"/>
            <div class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border p-4 md:p-stack-lg flex flex-col gap-stack-md relative overflow-hidden">
                <div class="absolute top-0 left-0 w-48 h-48 bg-primary/5 rounded-full blur-[60px]"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-surface-container border border-glass-border flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">lock_reset</span>
                    </div>
                    <h2 class="text-headline-md font-headline-md text-on-surface m-0">Ganti Password</h2>
                </div>
                <div class="flex flex-col gap-2 relative z-10">
                    <label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="password-lama">Password Lama</label>
                    <input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="password-lama" name="password_lama" placeholder="Masukkan password saat ini" required type="password"/>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md relative z-10">
                    <div class="flex flex-col gap-2">
                        <label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="password-baru">Password Baru</label>
                        <input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="password-baru" name="password_baru" placeholder="Min. 8 karakter" required type="password" minlength="8"/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="password-konfirmasi">Konfirmasi Password</label>
                        <input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="password-konfirmasi" name="password_konfirmasi" placeholder="Ulangi password baru" required type="password" minlength="8"/>
                    </div>
                </div>
                <p class="text-caption font-caption text-on-surface-variant m-0 relative z-10">Password baru minimal 8 karakter. Setelah berhasil diubah, Anda mungkin perlu login ulang.</p>
                <div class="flex justify-end relative z-10">
                    <button class="bg-surface-container border border-glass-border text-on-surface-variant hover:text-primary hover:border-primary/40 font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 transition-all duration-300" type="submit">
                        <span class="material-symbols-outlined text-[20px]">lock</span> Ubah Password
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
</section>
<script>
(function () {
    /* Avatar preview + auto-show save button */
    var fotoAvatar = document.getElementById('foto-avatar');
    var avatarPreview = document.getElementById('avatar-preview');
    var avatarIcon = document.getElementById('avatar-icon');
    var btnSimpanFoto = document.getElementById('btn-simpan-foto');

    if (fotoAvatar) {
        fotoAvatar.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function (ev) {
                if (avatarPreview) {
                    avatarPreview.src = ev.target.result;
                    avatarPreview.classList.remove('hidden');
                }
                if (avatarIcon) avatarIcon.classList.add('hidden');
                if (btnSimpanFoto) btnSimpanFoto.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    }

    /* Foto profil (form bawah) preview */
    var fotoProfil = document.getElementById('foto-profil');
    var fotoWrap = document.getElementById('foto-admin-preview-wrap');
    var fotoImg = document.getElementById('foto-admin-preview');
    if (fotoProfil) {
        fotoProfil.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (!file) {
                if (fotoWrap) fotoWrap.classList.add('hidden');
                return;
            }
            var reader = new FileReader();
            reader.onload = function (ev) {
                if (fotoImg && fotoWrap) {
                    fotoImg.src = ev.target.result;
                    fotoWrap.classList.remove('hidden');
                }
                if (avatarPreview) {
                    avatarPreview.src = ev.target.result;
                    avatarPreview.classList.remove('hidden');
                    if (avatarIcon) avatarIcon.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        });
    }

    /* Konfirmasi password match */
    var passBaru = document.getElementById('password-baru');
    var passKonfirm = document.getElementById('password-konfirmasi');
    if (passKonfirm) {
        passKonfirm.addEventListener('input', function () {
            if (passBaru && passKonfirm.value !== passBaru.value) {
                passKonfirm.setCustomValidity('Password konfirmasi tidak cocok.');
            } else {
                passKonfirm.setCustomValidity('');
            }
        });
    }
})();
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>
