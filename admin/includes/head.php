<?php
/**
 * 관리자 공통 헤더 (사이드바 포함).
 *   $adminTitle — 화면 제목
 *   $adminNote  — 제목 아래 한 줄 설명
 *   $adminHead  — 우측 버튼 영역 HTML
 */

require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_login();

$adminTitle = $adminTitle ?? '대시보드';
$adminNote  = $adminNote ?? '';
$adminHead  = $adminHead ?? '';
$stats      = $stats ?? repo_stats();
$here       = basename($_SERVER['SCRIPT_NAME'], '.php');

$menu = [
    '작업물' => [
        ['file' => 'index',      'label' => '대시보드', 'href' => url('admin/index.php'),      'count' => null],
        ['file' => 'projects',   'label' => '프로젝트', 'href' => url('admin/projects.php'),   'count' => $stats['total']],
        ['file' => 'categories', 'label' => '카테고리', 'href' => url('admin/categories.php'), 'count' => $stats['categories']],
    ],
    '문의' => [
        ['file' => 'contacts', 'label' => '받은 문의', 'href' => url('admin/contacts.php'), 'count' => $stats['contacts']],
    ],
];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($adminTitle) ?> | 관리자</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Instrument+Serif&amp;display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable-dynamic-subset.min.css">

<link rel="stylesheet" href="<?= e(asset('assets/css/reset.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/common.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/admin.css')) ?>">
</head>
<body class="admin">

<div class="admin-shell">

  <aside class="admin-side">
    <div class="admin-side__brand">
      <span><?= e(site('name_ko')) ?></span>
      <span class="admin-side__tag">Admin</span>
    </div>

    <nav class="admin-nav" aria-label="관리자 메뉴">
      <?php foreach ($menu as $group => $items): ?>
        <div class="admin-nav__group">
          <span class="admin-nav__label"><?= e($group) ?></span>
          <?php foreach ($items as $item): ?>
            <a class="admin-nav__link<?= $here === $item['file'] ? ' is-active' : '' ?>"
               href="<?= e($item['href']) ?>">
              <span><?= e($item['label']) ?></span>
              <?php if ($item['count'] !== null): ?>
                <span class="admin-nav__count"><?= (int) $item['count'] ?></span>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </nav>

    <div class="admin-side__foot">
      <?= e(admin_user()['userid'] ?? '') ?>(으)로 로그인<br>
      <a href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener">사이트 보기</a> ·
      <a href="<?= e(url('admin/logout.php')) ?>">로그아웃</a>
      <?php if (!db_ready()): ?>
        <p style="margin-top:10px;color:var(--accent-deep)">DB 미연결 상태입니다.</p>
      <?php endif; ?>
    </div>
  </aside>

  <main class="admin-main">
    <div class="admin-head">
      <div>
        <h1><?= e($adminTitle) ?></h1>
        <?php if ($adminNote !== ''): ?>
          <p class="admin-head__note"><?= e($adminNote) ?></p>
        <?php endif; ?>
      </div>
      <?php if ($adminHead !== ''): ?>
        <div class="admin-actions"><?= $adminHead ?></div>
      <?php endif; ?>
    </div>
