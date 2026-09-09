<?php
/**
 * CONTACT — 문의 폼.
 *
 * 처리 순서: CSRF 확인 → 입력 검증 → 저장 → 새로고침 재전송 방지(PRG).
 */

require_once __DIR__ . '/includes/repository.php';
require_once __DIR__ . '/includes/partials.php';

$errors = [];
$old    = ['name' => '', 'email' => '', 'message' => ''];
$sent   = isset($_GET['sent']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name']    = trim((string) ($_POST['name'] ?? ''));
    $old['email']   = trim((string) ($_POST['email'] ?? ''));
    $old['message'] = trim((string) ($_POST['message'] ?? ''));
    $trap           = trim((string) ($_POST['website'] ?? ''));

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors['form'] = '보안 토큰이 만료되었습니다. 페이지를 새로고침한 뒤 다시 보내 주세요.';
    }

    // 사람에게는 보이지 않는 칸. 채워져 있으면 자동 전송으로 봅니다.
    if ($trap !== '') {
        $errors['form'] = '전송에 실패했습니다.';
    }

    $last = $_SESSION['contact_last'] ?? 0;
    if (!$errors && time() - $last < 20) {
        $errors['form'] = '잠시 후 다시 시도해 주세요.';
    }

    if (mb_strlen($old['name']) < 2 || mb_strlen($old['name']) > 60) {
        $errors['name'] = '이름을 2자 이상 60자 이하로 입력해 주세요.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = '회신받을 이메일 주소를 정확히 입력해 주세요.';
    }
    if (mb_strlen($old['message']) < 10) {
        $errors['message'] = '문의 내용을 10자 이상 입력해 주세요.';
    } elseif (mb_strlen($old['message']) > 3000) {
        $errors['message'] = '문의 내용은 3000자까지 입력할 수 있습니다.';
    }

    if (!$errors) {
        if (repo_save_contact($old['name'], $old['email'], $old['message'])) {
            $_SESSION['contact_last'] = time();
            header('Location: ' . url('contact.php?sent=1'));
            exit;
        }
        $errors['form'] = '저장 중 문제가 발생했습니다. 번거로우시겠지만 이메일로 보내 주세요.';
    }
}

$pageTitle       = 'Contact';
$pageDescription = site('name_ko') . '에게 채용 및 협업 문의를 보낼 수 있는 페이지입니다.';
$pageCss   = ['pages.css'];
$bodyClass = 'page-contact';
require __DIR__ . '/includes/header.php';
?>

<section class="page-head">
  <div class="container">
    <div class="page-head__top">
      <span class="idx">Contact</span>
      <span class="label"><?= e(site('available')) ?></span>
    </div>

    <div class="page-head__grid">
      <h1 class="h1">
        채용 · 협업 문의를<br>
        기다리고 있습니다.
      </h1>
      <p class="lede page-head__lede">
        포트폴리오 원본(PDF)이나 이력서가 필요하시면 아래로 요청해 주세요.
        평일 기준 하루 안에 회신합니다.
      </p>
    </div>
  </div>
</section>

<div class="page-body">
  <div class="container contact-grid">

    <div>
      <?php if ($sent): ?>
        <p class="notice notice--ok">
          문의가 접수되었습니다. 입력하신 주소로 회신드리겠습니다. 감사합니다.
        </p>
      <?php endif; ?>

      <?php if (!empty($errors['form'])): ?>
        <p class="notice"><?= e($errors['form']) ?></p>
      <?php endif; ?>

      <form class="contact-form" method="post" action="<?= e(url('contact.php')) ?>" novalidate>
        <?= csrf_field() ?>

        <div class="honeypot" aria-hidden="true">
          <label for="website">이 칸은 비워 두세요</label>
          <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="field">
          <label for="name">이름 <span class="req" aria-hidden="true">*</span></label>
          <input class="input" type="text" id="name" name="name" required maxlength="60"
                 autocomplete="name" placeholder="홍길동"
                 value="<?= e($old['name']) ?>"
                 <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="name-error"' : '' ?>>
          <?php if (isset($errors['name'])): ?>
            <span class="field__error" id="name-error"><?= e($errors['name']) ?></span>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="email">이메일 <span class="req" aria-hidden="true">*</span></label>
          <input class="input" type="email" id="email" name="email" required maxlength="180"
                 autocomplete="email" placeholder="name@company.com"
                 value="<?= e($old['email']) ?>"
                 <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?>>
          <?php if (isset($errors['email'])): ?>
            <span class="field__error" id="email-error"><?= e($errors['email']) ?></span>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="message">문의 내용 <span class="req" aria-hidden="true">*</span></label>
          <textarea class="textarea" id="message" name="message" required maxlength="3000"
                    placeholder="채용 포지션이나 프로젝트 내용을 간단히 적어 주세요."
                    <?= isset($errors['message']) ? 'aria-invalid="true" aria-describedby="message-error"' : '' ?>><?= e($old['message']) ?></textarea>
          <?php if (isset($errors['message'])): ?>
            <span class="field__error" id="message-error"><?= e($errors['message']) ?></span>
          <?php endif; ?>
        </div>

        <div class="contact-form__actions">
          <button class="btn" type="submit">
            보내기 <span class="btn__arrow" aria-hidden="true">&rarr;</span>
          </button>
          <span class="contact-form__hint">보내주신 내용은 채용 문의 회신 목적으로만 사용합니다.</span>
        </div>
      </form>
    </div>

    <aside class="contact-aside">
      <div class="contact-aside__block">
        <span class="label">Email</span>
        <p style="margin-top:10px">
          <a class="contact-aside__mail" href="mailto:<?= e(site('email')) ?>"><?= e(site('email')) ?></a>
        </p>
      </div>

      <div class="contact-aside__block">
        <span class="label">보내주시면 좋은 내용</span>
        <ul class="dashed" style="margin-top:10px">
          <li>채용 포지션과 담당 업무 범위</li>
          <li>필요한 산출물 형태 (PDF · 원본 파일 · 링크)</li>
          <li>회신받을 연락처와 희망 일정</li>
        </ul>
      </div>

      <div class="contact-aside__block">
        <span class="label">Based in</span>
        <p style="margin-top:10px" class="muted"><?= e(site('location')) ?> · 원격 협업 가능</p>
      </div>

      <?php if (array_filter(site('links') ?? [])): ?>
        <div class="contact-aside__block">
          <span class="label">Links</span>
          <ul class="dashed" style="margin-top:10px">
            <?php foreach (array_filter(site('links')) as $label => $href): ?>
              <li><a href="<?= e($href) ?>" target="_blank" rel="noopener noreferrer"
                     style="border-bottom:1px solid var(--line)"><?= e($label) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </aside>

  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
