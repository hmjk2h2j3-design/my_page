<?php
/**
 * PROJECT DETAIL — 작업물 한 건을 과정까지 포함해 보여줍니다.
 */

require_once __DIR__ . '/includes/repository.php';
require_once __DIR__ . '/includes/partials.php';

$id      = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$project = $id > 0 ? repo_project($id) : null;

if (!$project) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$images  = $project['images'] ?? [];
$process = text_lines($project['process'] ?? '');
$result  = text_lines($project['result'] ?? '');
$related = repo_related($project, 3);

$pageTitle       = $project['title'];
$pageDescription = excerpt($project['description'], 150);
$pageCss   = ['pages.css'];
$bodyClass = 'page-project';
require __DIR__ . '/includes/header.php';
?>

<article>

  <!-- ------------------------------ 머리말 ------------------------------ -->
  <header class="project-head">
    <div class="container">
      <nav class="project-head__breadcrumb" aria-label="위치">
        <a href="<?= e(url('portfolio.php')) ?>">Portfolio</a>
        <span aria-hidden="true">/</span>
        <a href="<?= e(url('portfolio.php?cat=' . $project['category_slug'])) ?>"><?= e($project['category_name']) ?></a>
      </nav>

      <h1 class="h1 project-head__title"><?= e($project['title']) ?></h1>
      <p class="lede project-head__sub"><?= e($project['subtitle'] ?? '') ?></p>

      <dl class="project-facts">
        <div class="project-facts__item">
          <dt class="project-facts__key">Category</dt>
          <dd class="project-facts__val"><?= e($project['category_name']) ?></dd>
        </div>
        <div class="project-facts__item">
          <dt class="project-facts__key">Year</dt>
          <dd class="project-facts__val"><?= (int) $project['year'] ?></dd>
        </div>
        <div class="project-facts__item">
          <dt class="project-facts__key">Role</dt>
          <dd class="project-facts__val"><?= e($project['role']) ?></dd>
        </div>
        <div class="project-facts__item">
          <dt class="project-facts__key">Tools</dt>
          <dd class="project-facts__val"><?= e($project['tools']) ?></dd>
        </div>
      </dl>
    </div>
  </header>

  <!-- ------------------------------ 개요 ------------------------------ -->
  <section class="container project-brief">
    <div class="prose">
      <span class="label">Overview</span>
      <div style="margin-top:14px">
        <?php foreach (text_lines($project['description']) as $line): ?>
          <p><?= e($line) ?></p>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="project-goal">
      <span class="label">Goal</span>
      <p class="project-goal__text" style="margin-top:12px"><?= e($project['goal']) ?></p>
    </div>
  </section>

  <!-- ------------------------------ 이미지 ------------------------------ -->
  <?php if ($images): ?>
    <section class="container">
      <h2 class="visually-hidden">디자인 이미지</h2>
      <div class="project-gallery">
        <?php foreach ($images as $i => $img): ?>
          <figure class="reveal">
            <img src="<?= e(url($img)) ?>"
                 alt="<?= e($project['title']) ?> 디자인 이미지 <?= $i + 1 ?>"
                 loading="<?= $i === 0 ? 'eager' : 'lazy' ?>"
                 decoding="async"<?= image_attrs($img) ?>>
            <figcaption>
              <b><?= e(idx($i + 1)) ?></b>
              <span><?= e($project['title']) ?> — <?= e($project['category_name']) ?></span>
            </figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- --------------------------- 과정 / 결과 --------------------------- -->
  <section class="container project-notes">
    <?php if ($process): ?>
      <div>
        <span class="label">Process</span>
        <ol class="numbered" style="margin-top:16px">
          <?php foreach ($process as $line): ?>
            <li><?= e($line) ?></li>
          <?php endforeach; ?>
        </ol>
      </div>
    <?php endif; ?>

    <?php if ($result): ?>
      <div>
        <span class="label">Result</span>
        <ul class="dashed" style="margin-top:16px">
          <?php foreach ($result as $line): ?>
            <li><?= e($line) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </section>

</article>

<!-- --------------------------- 다음 작업 --------------------------- -->
<?php if ($related): ?>
  <section class="section">
    <div class="container">
      <?php section_head('—', '다른 작업', '', ['href' => url('portfolio.php'), 'label' => '아카이브 전체']); ?>
      <div class="related">
        <?php foreach ($related as $i => $p): ?>
          <?php work_card($p, $i + 1, 'reveal'); ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
