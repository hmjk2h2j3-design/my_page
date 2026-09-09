<?php
/**
 * 관리자 인증.
 *
 * - 비밀번호는 password_hash() 로만 저장하고 password_verify() 로 확인합니다.
 * - 로그인 성공 시 세션 ID를 재발급해 세션 고정 공격을 막습니다.
 * - 관리자 페이지는 맨 위에서 require_login() 을 호출합니다.
 */

require_once __DIR__ . '/repository.php';

function admin_user(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function is_logged_in(): bool
{
    return admin_user() !== null;
}

/** 계정이 하나도 없으면 로그인 화면에서 안내를 띄웁니다. */
function admin_account_exists(): bool
{
    if (!db_ready()) {
        return false;
    }
    try {
        return (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * @return string|null 실패 사유(사용자에게 보여줄 문구), 성공이면 null
 */
function attempt_login(string $userid, string $password): ?string
{
    if (!db_ready()) {
        return 'DB에 연결할 수 없습니다. config/database.php 설정을 확인해 주세요.';
    }

    // 짧은 시간에 반복 시도하는 것을 막습니다.
    $fails = $_SESSION['login_fails'] ?? 0;
    $until = $_SESSION['login_lock_until'] ?? 0;
    if ($fails >= 5 && time() < $until) {
        return '로그인 시도가 많습니다. ' . max(1, (int) ceil(($until - time()) / 60)) . '분 뒤에 다시 시도해 주세요.';
    }

    $stmt = db()->prepare('SELECT id, userid, password FROM users WHERE userid = :u LIMIT 1');
    $stmt->execute(['u' => $userid]);
    $row = $stmt->fetch();

    // 계정이 없어도 같은 시간이 걸리도록 더미 해시를 검증합니다.
    $hash = $row['password'] ?? '$2y$10$usesomesillystringforsalt.invalidhashvaluexxxxxxxxxxxxx';

    if (!password_verify($password, $hash) || !$row) {
        $_SESSION['login_fails'] = $fails + 1;
        $_SESSION['login_lock_until'] = time() + 600;
        return '아이디 또는 비밀번호가 올바르지 않습니다.';
    }

    // 해시 알고리즘이 바뀌었으면 조용히 갱신합니다.
    if (password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
        $up = db()->prepare('UPDATE users SET password = :p WHERE id = :id');
        $up->execute(['p' => password_hash($password, PASSWORD_DEFAULT), 'id' => $row['id']]);
    }

    session_regenerate_id(true);
    unset($_SESSION['login_fails'], $_SESSION['login_lock_until']);
    $_SESSION['admin'] = ['id' => (int) $row['id'], 'userid' => $row['userid']];

    return null;
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

/** 로그인하지 않았으면 로그인 화면으로 보냅니다. */
function require_login(): void
{
    if (is_logged_in()) {
        return;
    }
    $next = $_SERVER['REQUEST_URI'] ?? '';
    header('Location: ' . url('admin/login.php') . ($next ? '?next=' . urlencode($next) : ''));
    exit;
}
