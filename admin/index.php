<?php
/**
 * 관리자 대시보드.
 */

require_once dirname(__DIR__) . '/includes/auth.php';

require_login();

$stats = repo_stats();

$adminTitle = '대시보드';
$adminNote  = '등록 현황과 최근 작업을 확인합니다.';
$adminHead  = '<a class="btn btn--sm" href="' . e(url('admin/project-form.php')) . '">새 작업물 등록</a>';

require __DIR__ . '/includes/head.php';
?>

<div class="stats">
  <div class="stat">
    <span class="stat__key">전체 프로젝트</span>
    <span class="stat__val"><?= $stats['total'] ?><span>개</span></span>
  </div>
  <div class="stat">
    <span class="stat__key">공개</span>
    <span class="stat__val"><?= $stats['public'] ?><span>개</span></span>
  </div>
  <div class="stat">
    <span class="stat__key">비공개</span>
    <span class="stat__val"><?= $stats['private'] ?><span>개</span></span>
  </div>
  <div class="stat">
    <span class="stat__key">카테고리</span>
    <span class="stat__val"><?= $stats['categories'] ?><span>개</span></span>
  </div>
  <div class="stat">
    <span class="stat__key">받은 문의</span>
    <span class="stat__val"><?= $stats['contacts'] ?><span>건</span></span>
  </div>
</div>

<h2 class="form-panel__title" style="border:0;margin-bottom:12px">최근 등록</h2>

<div class="table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th style="width:80px">이미지</th>
        <th>프로젝트</th>
        <th style="width:110px">카테고리</th>
        <th style="width:70px">연도</th>
        <th style="width:90px">공개</th>
        <th style="width:150px"></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$stats['recent']): ?>
        <tr><td colspan="6" class="empty-row">아직 등록된 작업물이 없습니다.</td></tr>
      <?php endif; ?>

      <?php foreach ($stats['recent'] as $p): ?>
        <tr>
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
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/foot.php'; ?>
