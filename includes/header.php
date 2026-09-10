<?php
/**
 * 공통 헤더.
 *
 * 페이지에서 require 하기 전에 아래 변수를 채울 수 있습니다.
 *   $pageTitle       — <title> 앞부분
 *   $pageDescription — meta description
 *   $pageCss         — 추가로 불러올 css 파일명 배열 (예: ['home.css'])
 *   $bodyClass       — <body> 클래스
 */

require_once __DIR__ . '/functions.php';

$site        = site();
$pageTitle   = $pageTitle ?? null;
$description = $pageDescription ?? $site['meta_description'];
$pageCss     = $pageCss ?? [];
$bodyClass   = $bodyClass ?? '';
$current     = current_page();

$title = $pageTitle
    ? $pageTitle . ' | ' . $site['name_ko'] . ' Portfolio'
    : $site['name_ko'] . ' | ' . $site['role'] . ' 포트폴리오';

$nav = [
    ['label' => 'About',     'file' => 'about',     'href' => url('about.php')],
    ['label' => 'Portfolio', 'file' => 'portfolio', 'href' => url('portfolio.php')],
    ['label' => 'Contact',   'file' => 'contact',   'href' => url('contact.php')],
];
?>
<!DOCTYPE html>
<html lang="ko" class="no-js">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<meta name="author" content="<?= e($site['name_ko']) ?>">
<meta name="theme-color" content="#f4f1ea">

<meta property="og:type" content="website">
<meta property="og:locale" content="ko_KR">
<meta property="og:site_name" content="<?= e($site['name_ko']) ?> Portfolio">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta name="twitter:card" content="summary_large_image">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&amp;display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable-dynamic-subset.min.css">

<link rel="stylesheet" href="<?= e(asset('assets/css/reset.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/common.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/layout.css')) ?>">
<?php foreach ($pageCss as $css): ?>
<link rel="stylesheet" href="<?= e(asset('assets/css/' . $css)) ?>">
<?php endforeach; ?>
<!-- 움직임 관련 규칙은 마지막에 둡니다 (헤더 높이·툴바 위치를 덮어써야 하므로) -->
<link rel="stylesheet" href="<?= e(asset('assets/css/motion.css')) ?>">

<script>document.documentElement.classList.remove('no-js');</script>
</head>
<body class="<?= e($bodyClass) ?>">

<a class="skip-link" href="#main">본문으로 건너뛰기</a>

<div class="progress" aria-hidden="true"><i></i></div>
<div class="cursor" aria-hidden="true"></div>

<header class="site-header">
  <div class="container site-header__inner">
    <a class="brand" href="<?= e(url('index.php')) ?>">
      <span class="brand__mark" aria-hidden="true"></span>
      <span><?= e($site['name_ko']) ?></span>
      <span class="brand__en"><?= e($site['name_en']) ?></span>
    </a>

    <button class="nav-toggle" type="button" aria-controls="site-nav" aria-expanded="false">
      <span aria-hidden="true"></span>
      <span class="visually-hidden">메뉴 열기</span>
    </button>

    <nav class="nav" id="site-nav" aria-label="주요 메뉴">
      <?php foreach ($nav as $item): ?>
        <a class="nav__link<?= $current === $item['file'] ? ' is-active' : '' ?>"
           href="<?= e($item['href']) ?>"
           <?= $current === $item['file'] ? 'aria-current="page"' : '' ?>><?= e($item['label']) ?></a>
      <?php endforeach; ?>
      <a class="nav__mail" href="mailto:<?= e($site['email']) ?>">이메일</a>
    </nav>
  </div>
</header>

<main id="main">
