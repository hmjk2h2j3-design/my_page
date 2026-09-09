<?php
/**
 * 프로젝트 목록 (관리자).
 */

require_once dirname(__DIR__) . '/includes/auth.php';

require_login();

$stats    = repo_stats();
$projects = repo_projects(['include_private' => true, 'sort' => 'curated']);
$flash    = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$adminTitle = '프로젝트';
$adminNote  = '표시 순서는 숫자가 작을수록 앞에 나옵니다. 홈의 대표 이미지는 1번 작업물을 씁니다.';
$adminHead  = '<a class="btn btn--sm" href="' . e(url('admin/project-form.php')) . '">새 작업물 등록</a>';

require __DIR__ . '/includes/head.php';
?>

<?php if ($flash): ?>
  <p class="notice notice--ok"><?= e($flash) ?></p>
<?php endif; ?>

<?php if (!db_ready()): ?>
  <p class="notice">
    DB에 연결되어 있지 않아 지금은 샘플 데이터를 읽기 전용으로 보여 주고 있습니다.
    등록·수정·삭제를 하려면 <code>sql/schema.sql</code>을 import 하고 <code>config/database.php</code>를 설정해 주세요.
  </p>
<?php endif; ?>

<div class="table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th style="width:60px">순서</th>
        <th style="width:80px">이미지</th>
        <th>프로젝트</th>
        <th style="width:110px">카테고리</th>
        <th style="width:70px">연도</th>
        <th style="width:70px">이미지</th>
        <th style="width:90px">공개</th>
        <th style="width:200px"></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$projects): ?>
        <tr><td colspan="8" class="empty-row">등록된 작업물이 없습니다.</td></tr>
      <?php endif; ?>

      <?php foreach ($projects as $p): ?>
        <?php $detail = repo_project((int) $p['id'], true); ?>
        <tr>
          <td><span class="chip"><?= (int) $p['sort_order'] ?></span></td>
          <td>
            <?php if (!empty($p['thumbnail'])): ?>
              <img class="table__thumb" src="<?= e(url($p['thumbnail'])) ?>" alt="" loading="lazy">
            <?php endif; ?>
          </td>
          <td>
            <div class="table__title"><?= e($p['title']) ?></div>
            <div class="table__sub"><?= e($p['subtitle'] ?? '') ?></div>
          </td>
          <td><span class="chip"><?= e($p['category_name'] ?? '—') ?></span></td>
          <td><?= (int) $p['year'] ?></td>
          <td><?= count($detail['images'] ?? []) ?>장</td>
          <td>
            <?php if ((int) $p['is_public'] === 1): ?>
              <span class="chip chip--on">공개</span>
            <?php else: ?>
              <span class="chip chip--off">비공개</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="table__actions">
              <a class="btn btn--ghost btn--sm" href="<?= e(url('project.php?id=' . (int) $p['id'])) ?>" target="_blank" rel="noopener">보기</a>
              <a class="btn btn--sm" href="<?= e(url('admin/project-form.php?id=' . (int) $p['id'])) ?>">수정</a>
              <form method="post" action="<?= e(url('admin/project-delete.php')) ?>"
                    data-confirm="&lsquo;<?= e($p['title']) ?>&rsquo; 프로젝트와 연결된 이미지를 모두 삭제합니다. 계속할까요?">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                <button class="btn btn--danger btn--sm" type="submit">삭제</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/foot.php'; ?>
