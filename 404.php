<?php
/**
 * 404 — 없는 주소 / 없는 프로젝트.
 */

require_once __DIR__ . '/includes/functions.php';

if (http_response_code() !== 404) {
    http_response_code(404);
}

$pageTitle       = '페이지를 찾을 수 없습니다';
$pageDescription = '요청하신 페이지를 찾을 수 없습니다.';
$bodyClass = 'page-404';
require __DIR__ . '/includes/header.php';
?>

<div class="container error-page">
  <span class="idx">404</span>
  <h1 class="h1" style="margin-top:16px">이 주소에는 아무것도 없습니다.</h1>
  <p class="lede" style="margin-top:18px">
    주소가 바뀌었거나, 아직 공개하지 않은 작업물일 수 있습니다.
    아카이브에서 다시 찾아 주세요.
  </p>
  <p style="margin-top:30px">
    <a class="btn" href="<?= e(url('portfolio.php')) ?>">
      아카이브로 이동 <span class="btn__arrow" aria-hidden="true">&rarr;</span>
    </a>
  </p>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
