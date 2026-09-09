<?php
/**
 * 받은 문의 목록.
 *
 * 내용은 방문자가 입력한 값이므로 반드시 이스케이프해서 출력합니다.
 */

require_once dirname(__DIR__) . '/includes/auth.php';

require_login();

$stats    = repo_stats();
$messages = repo_contacts();

$adminTitle = '받은 문의';
$adminNote  = $messages
    ? count($messages) . '건의 문의가 있습니다. 회신은 메일 주소를 눌러 진행하세요.'
    : '아직 받은 문의가 없습니다.';

require __DIR__ . '/includes/head.php';
?>

<?php if (!db_ready()): ?>
  <p class="notice">
    DB 미연결 상태에서는 문의가 <code>data/contacts.jsonl</code> 파일에 쌓입니다.
    이 화면은 그 파일을 읽어 보여 줍니다.
  </p>
<?php endif; ?>

<?php if (!$messages): ?>
  <div class="table-wrap">
    <p class="empty-row">받은 문의가 없습니다.</p>
  </div>
<?php endif; ?>

<?php foreach ($messages as $m): ?>
  <article class="message">
    <header class="message__head">
      <span class="message__name"><?= e($m['name']) ?></span>
      <a class="message__mail" href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('[포트폴리오] 문의 회신') ?>">
        <?= e($m['email']) ?>
      </a>
      <time class="message__date" datetime="<?= e($m['created_at']) ?>">
        <?= e(date('Y.m.d H:i', strtotime($m['created_at']))) ?>
      </time>
    </header>
    <p class="message__body"><?= e($m['message']) ?></p>
  </article>
<?php endforeach; ?>

<?php require __DIR__ . '/includes/foot.php'; ?>
