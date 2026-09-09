<?php
/**
 * 관리자 로그인.
 */

require_once dirname(__DIR__) . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . url('admin/index.php'));
    exit;
}

$error  = null;
$userid = '';
$hasAccount = admin_account_exists();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid   = trim((string) ($_POST['userid'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $error = '보안 토큰이 만료되었습니다. 다시 시도해 주세요.';
    } elseif ($userid === '' || $password === '') {
        $error = '아이디와 비밀번호를 입력해 주세요.';
    } else {
        $error = attempt_login($userid, $password);
    }

    if ($error === null) {
        $next = (string) ($_POST['next'] ?? '');
        // 외부 주소로 튕기지 않도록 내부 경로만 허용합니다.
        $safe = (str_starts_with($next, '/') && !str_starts_with($next, '//')) ? $next : url('admin/index.php');
        header('Location: ' . $safe);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>관리자 로그인</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Instrument+Serif&amp;display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable-dynamic-subset.min.css">

<link rel="stylesheet" href="<?= e(asset('assets/css/reset.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/common.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/admin.css')) ?>">
</head>
<body class="admin">

<div class="login-wrap">
  <div class="login-card">

    <div class="login-card__brand">
      <span><?= e(site('name_ko')) ?></span>
      <em>Portfolio Admin</em>
    </div>

    <h1>관리자 로그인</h1>
    <p class="login-card__note">작업물을 등록하고 문의를 확인합니다.</p>

    <?php if ($error): ?>
      <p class="notice"><?= e($error) ?></p>
    <?php endif; ?>

    <?php if (!db_ready()): ?>
      <p class="notice">
        DB에 연결되어 있지 않습니다. <code>sql/schema.sql</code>을 import 하고
        <code>config/database.php</code>의 접속 정보를 확인해 주세요.
      </p>
    <?php elseif (!$hasAccount): ?>
      <p class="notice">
        아직 관리자 계정이 없습니다. 명령줄에서
        <code>php tools/make-hash.php 비밀번호</code>를 실행해 나온 해시를
        <code>users</code> 테이블에 넣어 주세요. 자세한 방법은 <code>sql/schema.sql</code> 맨 아래에 있습니다.
      </p>
    <?php endif; ?>

    <form method="post" action="<?= e(url('admin/login.php')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="next" value="<?= e((string) ($_GET['next'] ?? '')) ?>">

      <div class="field">
        <label for="userid">아이디</label>
        <input class="input" type="text" id="userid" name="userid" required
               autocomplete="username" autofocus value="<?= e($userid) ?>">
      </div>

      <div class="field">
        <label for="password">비밀번호</label>
        <input class="input" type="password" id="password" name="password" required
               autocomplete="current-password">
      </div>

      <button class="btn" type="submit" style="margin-top:8px">로그인</button>
    </form>

    <a class="login-back" href="<?= e(url('index.php')) ?>">&larr; 사이트로 돌아가기</a>
  </div>
</div>

</body>
</html>
