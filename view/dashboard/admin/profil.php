<?php
declare(strict_types=1);

$judulHalaman = 'Profil Admin';

// Load full admin data
$adminSession = currentAdmin();
if (!$adminSession) {
    redirect('/auth/login');
}
$db = getDb();
$stmt = $db->prepare('SELECT * FROM admins WHERE id = ?');
$stmt->execute([$adminSession['id']]);
$adminData = $stmt->fetch();
if (!$adminData) {
    redirect('/auth/login');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    $action = trim((string) ($_POST['action'] ?? ''));

    if ($action === 'update_profil') {
        $nama = trim((string) ($_POST['nama'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));

        $errors = [];
        if ($nama === '') {
            $errors[] = 'Nama wajib diisi.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        $fotoPath = $adminData['foto'] ?? null;
        $adaFile = ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        if ($adaFile) {
            $up = handleUpload($_FILES['foto'], 'admin', $nama . ' photo');
            if (!$up['ok']) {
                $errors[] = $up['error'];
            } else {
                $fotoPath = $up['path'];
                // Remove old photo
                if (!empty($adminData['foto']) && $adminData['foto'] !== $fotoPath) {
                    $oldFile = UPLOAD_PATH . '/' . $adminData['foto'];
                    if (is_file($oldFile)) {
                        @unlink($oldFile);
                    }
                }
            }
        }

        if ($errors === []) {
            $stmt2 = $db->prepare('UPDATE admins SET nama = ?, email = ?, foto = ?, updated_at = NOW() WHERE id = ?');
            $stmt2->execute([$nama, $email !== '' ? $email : null, $fotoPath, $adminData['id']]);
            catatLog('update profil admin', 'admins', (int) $adminData['id']);
            flash('success', 'Profil berhasil diperbarui.');
        } else {
            foreach ($errors as $err) {
                flash('error', $err);
            }
        }
    }

    if ($action === 'change_password') {
        $currentPwd = (string) ($_POST['current_password'] ?? '');
        $newPwd = (string) ($_POST['new_password'] ?? '');
        $confirmPwd = (string) ($_POST['confirm_password'] ?? '');

        $errors = [];
        if (!password_verify($currentPwd, $adminData['password_hash'])) {
            $errors[] = 'Password lama salah.';
        }
        if (strlen($newPwd) < 8) {
            $errors[] = 'Password baru minimal 8 karakter.';
        }
        if ($newPwd !== $confirmPwd) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        if ($errors === []) {
            $newHash = password_hash($newPwd, PASSWORD_BCRYPT);
            $stmt3 = $db->prepare('UPDATE admins SET password_hash = ?, updated_at = NOW() WHERE id = ?');
            $stmt3->execute([$newHash, $adminData['id']]);
            catatLog('ubah password admin', 'admins', (int) $adminData['id']);
            flash('success', 'Password berhasil diubah.');
        } else {
            foreach ($errors as $err) {
                flash('error', $err);
            }
        }
    }

    redirect('/dashboard/admin/profil');
}

require __DIR__ . '/../layout.php';
?>
<section>
<div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-6 gap-4">
<div class="flex flex-col gap-2">
<span class="text-label-mono font-label-mono text-primary uppercase tracking-widest">Pengaturan Akun</span>
<h1 class="text-headline-xl-mobile md:text-headline-xl font-headline-xl text-on-background m-0">Profil Admin</h1>
</div>
<a href="<?= APP_BASE ?>/dashboard" class="text-caption font-caption text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
<span class="material-symbols-outlined text-[18px]">arrow_back</span> Dashboard
</a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-gutter">
<!-- Left Column: Avatar + Info -->
<div class="xl:col-span-4 flex flex-col gap-gutter">
<!-- Avatar Card -->
<div class="bg-glass-fill backdrop-blur-md rounded-[20px] p-stack-lg border border-glass-border relative overflow-hidden group">
<div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none group-hover:bg-primary/20 transition-all duration-500"></div>
<div class="flex flex-col items-center text-center relative z-10">
<div class="relative mb-6">
<div class="w-32 h-32 rounded-full overflow-hidden border-2 border-primary/30 p-1 group-hover:border-primary/60 transition-colors duration-300 shadow-lime-glow">
<?php if (!empty($adminData['foto'])): ?>
<img class="w-full h-full object-cover rounded-full" id="avatar-preview" src="<?= uploadUrl($adminData['foto']) ?>" alt="Foto profil <?= e($adminData['nama']) ?>"/>
<?php else: ?>
<div id="avatar-placeholder" class="w-full h-full rounded-full bg-surface-container-high flex items-center justify-center">
<span class="material-symbols-outlined text-primary text-[48px]">admin_panel_settings</span>
</div>
<img class="w-full h-full object-cover rounded-full hidden" id="avatar-preview" src="" alt=""/>
<?php endif; ?>
</div>
<label for="foto-upload" class="absolute bottom-0 right-0 bg-primary text-on-primary w-10 h-10 rounded-full flex items-center justify-center hover:bg-primary-fixed transition-colors shadow-lg cursor-pointer" title="Ganti foto profil">
<span class="material-symbols-outlined text-[20px]">photo_camera</span>
</label>
</div>
<h2 class="text-headline-md font-headline-md text-on-surface mb-1"><?= e($adminData['nama']) ?></h2>
<span class="bg-muted-forest text-primary px-3 py-1 rounded-full text-caption font-caption inline-flex items-center gap-2 mb-4">
<span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span> Super Admin
</span>
<div class="w-full border-t border-glass-border pt-4 mt-2 grid grid-cols-2 gap-4">
<div class="text-left">
<p class="text-caption text-on-surface-variant mb-0.5">Username</p>
<p class="text-body-md font-medium text-on-surface"><?= e($adminData['username']) ?></p>
</div>
<div class="text-right">
<p class="text-caption text-on-surface-variant mb-0.5">Status</p>
<p class="text-body-md font-medium text-primary">Aktif</p>
</div>
</div>
</div>
</div>
</div>

<!-- Right Column: Forms -->
<div class="xl:col-span-8 flex flex-col gap-gutter">
<!-- Tab Buttons -->
<div class="flex gap-2">
<button type="button" id="tab-btn-info" class="profil-tab-btn px-5 py-2.5 rounded-full text-caption font-caption border transition-all bg-primary/10 text-primary border-primary/40" data-tab="info">
<span class="material-symbols-outlined text-[16px] align-middle">person</span> Informasi Akun
</button>
<button type="button" id="tab-btn-keamanan" class="profil-tab-btn px-5 py-2.5 rounded-full text-caption font-caption border transition-all border-glass-border text-on-surface-variant hover:border-primary/40 hover:text-primary" data-tab="keamanan">
<span class="material-symbols-outlined text-[16px] align-middle">lock</span> Keamanan
</button>
</div>

<!-- Tab: Informasi Akun -->
<div id="tab-info" class="bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border overflow-hidden relative">
<div class="h-1 bg-gradient-to-r from-primary to-transparent absolute top-0 left-0 w-full"></div>
<div class="p-stack-lg">
<h3 class="text-headline-md font-headline-md text-on-surface mb-6 flex items-center gap-3">
<span class="material-symbols-outlined text-primary">person</span> Informasi Pribadi
</h3>
<form method="post" action="<?= APP_BASE ?>/dashboard/admin/profil" enctype="multipart/form-data">
<?= csrfField() ?>
<input type="hidden" name="action" value="update_profil">
<input type="file" id="foto-upload" name="foto" accept="image/jpeg,image/jpg,image/png,image/webp,image/heic,image/heif" class="sr-only">
<div class="flex flex-col gap-stack-md">
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="nama-input">Nama Lengkap</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="nama-input" name="nama" required type="text" value="<?= e($adminData['nama']) ?>"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="email-input">Email</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all placeholder:text-on-surface-variant/50" id="email-input" name="email" type="email" placeholder="admin@example.com" value="<?= e($adminData['email'] ?? '') ?>"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]">Username</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface-variant cursor-not-allowed opacity-60" type="text" value="<?= e($adminData['username']) ?>" readonly/>
<p class="text-caption font-caption text-on-surface-variant m-0">Username tidak dapat diubah.</p>
</div>
<div class="flex justify-end">
<button class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300" type="submit">
<span class="material-symbols-outlined text-[20px]">save</span> Simpan Perubahan
</button>
</div>
</div>
</form>
</div>
</div>

<!-- Tab: Keamanan (hidden by default) -->
<div id="tab-keamanan" class="hidden bg-glass-fill backdrop-blur-md rounded-[20px] border border-glass-border overflow-hidden relative">
<div class="h-1 bg-gradient-to-r from-primary to-transparent absolute top-0 left-0 w-full"></div>
<div class="p-stack-lg">
<h3 class="text-headline-md font-headline-md text-on-surface mb-6 flex items-center gap-3">
<span class="material-symbols-outlined text-primary">lock</span> Ubah Password
</h3>
<form method="post" action="<?= APP_BASE ?>/dashboard/admin/profil">
<?= csrfField() ?>
<input type="hidden" name="action" value="change_password">
<div class="flex flex-col gap-stack-md">
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="current_password">Password Lama</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="current_password" name="current_password" required type="password"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="new_password">Password Baru <span class="text-on-surface-variant normal-case font-normal">(min. 8 karakter)</span></label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="new_password" name="new_password" required type="password" minlength="8"/>
</div>
<div class="flex flex-col gap-2">
<label class="text-label-mono font-label-mono text-on-surface-variant uppercase tracking-widest text-[12px]" for="confirm_password">Konfirmasi Password Baru</label>
<input class="w-full bg-surface-container-highest border border-glass-border rounded-xl px-4 py-3 text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:shadow-lime-glow transition-all" id="confirm_password" name="confirm_password" required type="password"/>
</div>
<div class="flex justify-end">
<button class="bg-primary text-on-primary font-caption text-caption px-6 py-3 rounded-full flex items-center gap-2 hover:shadow-lime-glow transition-all duration-300" type="submit">
<span class="material-symbols-outlined text-[20px]">lock_reset</span> Ubah Password
</button>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
</section>
<script>
// Tab switching
document.querySelectorAll('.profil-tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.profil-tab-btn').forEach(function(b) {
            b.classList.remove('bg-primary/10', 'text-primary', 'border-primary/40');
            b.classList.add('border-glass-border', 'text-on-surface-variant');
        });
        this.classList.add('bg-primary/10', 'text-primary', 'border-primary/40');
        this.classList.remove('border-glass-border', 'text-on-surface-variant');
        document.querySelectorAll('#tab-info, #tab-keamanan').forEach(function(t) {
            t.classList.add('hidden');
        });
        var target = document.getElementById('tab-' + this.dataset.tab);
        if (target) target.classList.remove('hidden');
    });
});
// Photo preview on file select
var fotoInput = document.getElementById('foto-upload');
if (fotoInput) {
    fotoInput.addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function(ev) {
            var img = document.getElementById('avatar-preview');
            var placeholder = document.getElementById('avatar-placeholder');
            if (img) { img.src = ev.target.result; img.classList.remove('hidden'); }
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
}
</script>
<?php require __DIR__ . '/../layout_close.php'; ?>
