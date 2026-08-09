<?php
declare(strict_types=1);

function isLoggedIn(): bool
{
    return isset($_SESSION['admin_id']);
}

function requireAdmin(): void
{
    if (!isLoggedIn()) {
        redirect('/auth/login');
    }
}

function currentAdmin(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    $db = getDb();
    $stmt = $db->prepare('SELECT id, username, nama, foto, email, role FROM admins WHERE id = ?');
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch() ?: null;
}

function getAdminName(int $id): string
{
    $db = getDb();
    $stmt = $db->prepare('SELECT nama FROM admins WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row['nama'] ?? 'Admin Pekon';
}

function login(string $username, string $password): bool
{
    if (loginIsLocked($username)) {
        return false;
    }

    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        recordLoginAttempt($username, true);
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        return true;
    }

    recordLoginAttempt($username, false);
    return false;
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function loginIsLocked(string $username): bool
{
    $key = 'login_lock_' . md5($username);
    $data = $_SESSION[$key] ?? null;
    if ($data === null) {
        return false;
    }
    if ($data['failures'] >= LOGIN_ATTEMPT_LIMIT && (time() - $data['last']) < LOGIN_ATTEMPT_WINDOW) {
        return true;
    }
    if (time() - $data['last'] >= LOGIN_ATTEMPT_WINDOW) {
        unset($_SESSION[$key]);
        return false;
    }
    return false;
}

function recordLoginAttempt(string $username, bool $success): void
{
    $key = 'login_lock_' . md5($username);
    $data = $_SESSION[$key] ?? ['failures' => 0, 'last' => 0];
    if ($success) {
        unset($_SESSION[$key]);
        return;
    }
    $data['failures']++;
    $data['last'] = time();
    $_SESSION[$key] = $data;
}

function loginLockRemaining(string $username): int
{
    $key = 'login_lock_' . md5($username);
    $data = $_SESSION[$key] ?? null;
    if ($data === null || $data['failures'] < LOGIN_ATTEMPT_LIMIT) {
        return 0;
    }
    return max(0, LOGIN_ATTEMPT_WINDOW - (time() - $data['last']));
}
