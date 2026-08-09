<?php
declare(strict_types=1);

if (isPost()) {
    csrfValidate();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $lock = loginLockRemaining($username);
    if ($lock > 0) {
        flash('error', 'Terlalu banyak percobaan gagal. Akun terkunci, coba lagi dalam ' . (int) ceil($lock / 60) . ' menit.');
    } elseif ($username !== '' && $password !== '' && login($username, $password)) {
        catatLog('LOGIN', 'auth');
        flash('success', 'Selamat datang kembali!');
        redirect('/dashboard');
    } else {
        flash('error', 'Username atau password salah.');
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="robots" content="noindex, nofollow"/>
<title>Login | Portal Admin Desa Padang Cermin</title>
<link rel="icon" type="image/x-icon" href="<?= APP_BASE ?>/favicon.ico"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500&family=Plus+Jakarta+Sans:wght@400;500;600&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet"/>
<link href="<?= assetUrl('css/tailwind.css') ?>" rel="stylesheet"/>
<style>
/* Static ambient — tidak ada animasi berlebihan (light theme) */
.login-bg {
    background: radial-gradient(ellipse 80% 50% at 60% -10%, rgba(60, 110, 71, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at -10% 110%, rgba(60, 110, 71, 0.05) 0%, transparent 50%);
}
</style>
</head>
<body class="bg-admin-bg font-body-md text-coklat login-bg min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-sm">

    <!-- Logo + Judul -->
    <div class="flex flex-col items-center mb-8">
        <img alt="Logo Desa Padang Cermin"
             class="w-12 h-12 object-contain mb-4 drop-shadow-sm"
             src="<?= assetUrl('img/logo.png') ?>"/>
        <h1 class="text-xl font-bold text-coklat tracking-tight mb-0.5">Portal Admin</h1>
        <p class="text-sm text-abu">Desa Padang Cermin</p>
    </div>

    <?php if ($flash !== []): ?>
    <div class="mb-5 flex flex-col gap-2">
        <?php foreach ($flash as $f): ?>
        <div class="px-4 py-3 rounded-xl border text-sm flex items-center gap-2
            <?= $f['tipe'] === 'success'
                ? 'bg-hijau/10 border-hijau/30 text-hijau'
                : 'bg-red-500/10 border-red-500/30 text-red-400' ?>">
            <span class="material-symbols-outlined text-[18px] shrink-0">
                <?= $f['tipe'] === 'success' ? 'check_circle' : 'error' ?>
            </span>
            <?= e($f['pesan']) ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-black/10 p-6 shadow-xl">
        <form class="flex flex-col gap-4" action="<?= APP_BASE ?>/auth/login" method="post" autocomplete="on">
            <?= csrfField() ?>

            <!-- Username -->
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-abu" for="admin-id">Username</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-abu/60 text-[18px] group-focus-within:text-hijau transition-colors">person</span>
                    <input id="admin-id" name="username" type="text" autocomplete="username" required
                           class="w-full bg-[#F4F7F3] border border-black/10 rounded-xl py-2.5 pl-10 pr-4 text-sm text-coklat placeholder:text-abu/50 focus:outline-none focus:border-hijau/60 focus:bg-white transition-all"
                           placeholder="Masukkan username"
                           value="<?= e(old('username')) ?>"/>
                </div>
            </div>

            <!-- Password -->
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-abu" for="admin-pass">Password</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-abu/60 text-[18px] group-focus-within:text-hijau transition-colors">lock</span>
                    <input id="admin-pass" name="password" type="password" autocomplete="current-password" required
                           class="w-full bg-[#F4F7F3] border border-black/10 rounded-xl py-2.5 pl-10 pr-11 text-sm text-coklat placeholder:text-abu/50 focus:outline-none focus:border-hijau/60 focus:bg-white transition-all"
                           placeholder="••••••••"/>
                    <button id="toggle-password" type="button" aria-label="Tampilkan/Sembunyikan password"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-abu/70 hover:text-hijau transition-colors p-0.5">
                        <span class="material-symbols-outlined text-[18px]">visibility_off</span>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="mt-2 w-full py-3 rounded-xl bg-primary text-on-primary text-sm font-semibold hover:brightness-105 hover:shadow-lime-glow active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                Masuk
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
        </form>
    </div>

    <!-- Kembali ke Beranda -->
    <div class="mt-5 text-center">
        <a href="<?= APP_BASE ?>/"
           class="inline-flex items-center gap-1.5 text-sm font-medium text-hijau hover:text-coklat hover:underline underline-offset-4 transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Beranda
        </a>
    </div>

    <!-- Footer -->
    <p class="mt-6 text-center text-xs text-abu/60">
        &copy; <?= date('Y') ?> Desa Padang Cermin
    </p>
</div>

<script src="<?= assetUrl('js/security-warning.js') ?>"></script>
<script>
(function () {
    var btn = document.getElementById('toggle-password');
    var inp = document.getElementById('admin-pass');
    if (!btn || !inp) return;
    btn.addEventListener('click', function () {
        var isHidden = inp.type === 'password';
        inp.type = isHidden ? 'text' : 'password';
        btn.querySelector('span').textContent = isHidden ? 'visibility' : 'visibility_off';
    });
})();
</script>
</body>
</html>
