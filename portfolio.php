<?php
/**
 * PORTFOLIO — 비율이 제각각인 작업물을 자르지 않고 늘어놓는 아카이브.
 *
 * 카테고리와 정렬은 JS가 있으면 새로고침 없이, 없으면 링크로 동작합니다.
 */

require_once __DIR__ . '/includes/repository.php';
require_once __DIR__ . '/includes/partials.php';

$categories = repo_categories();
$counts     = repo_category_counts();

$cat  = isset($_GET['cat']) ? preg_replace('/[^a-z0-9\-]/i', '', (string) $_GET['cat']) : 'all';
$sort = isset($_GET['sort']) && in_array($_GET['sort'], ['new', 'old', 'curated'], true) ? $_GET['sort'] : 'curated';

if ($cat !== 'all' && !repo_category_by_slug($cat)) {
    $cat = 'all';
}

// 목록은 항상 전부 그리고, 화면에 보일 항목만 표시합니다.
// (JS가 없어도 카테고리 링크가 그대로 동작하도록)
$projects = repo_projects(['sort' => $sort]);
$visible  = $cat === 'all'
    ? count($projects)
    : count(array_filter($projects, static fn($p) => $p['category_slug'] === $cat));

$activeName = $cat === 'all' ? '전체' : (repo_category_by_slug($cat)['name'] ?? '전체');

$pageTitle       = 'Portfolio';
$pageDescription = site('name_ko') . '의 UI/UX · 웹 · 그래픽 · 브랜딩 작업물 아카이브입니다.';
$pageCss   = ['portfolio.css'];
$pageJs    = ['portfolio.js'];
$bodyClass = 'page-portfolio';
require __DIR__ . '/includes/header.php';
?>

<section class="page-head">
  <div class="container">
    <div class="page-head__top">
      <span class="idx">Portfolio</span>
      <span class="label"><?= count($projects) ?> Projects · 2023–<?= date('Y') ?></span>
    </div>

    <div class="page-head__grid">
      <h1 class="h1">
        자르지 않고<br>
        원래 비율로 모아 둔 아카이브.
      </h1>
      <p class="lede page-head__lede">
        포스터, 모바일 화면, 긴 상세페이지는 애초에 비율이 다릅니다.
        같은 크기의 카드에 맞춰 잘라내면 원래 의도한 프레임이 사라지기 때문에,
        여기서는 모든 작업물을 원본 비율 그대로 둡니다.
      </p>
    </div>
  </div>
</section>

<div class="container">

  <form class="toolbar" method="get" action="<?= e(url('portfolio.php')) ?>">
    <nav class="filters" aria-label="카테고리 필터">
      <a class="filters__btn<?= $cat === 'all' ? ' is-active' : '' ?>"
         data-filter="all"
         href="<?= e(url('portfolio.php')) ?><?= $sort !== 'curated' ? '?sort=' . e($sort) : '' ?>"
         <?= $cat === 'all' ? 'aria-current="true"' : '' ?>>
        ALL <span class="filters__count"><?= count($projects) ?></span>
      </a>

      <?php foreach ($categories as $c): ?>
        <a class="filters__btn<?= $cat === $c['slug'] ? ' is-active' : '' ?>"
           data-filter="<?= e($c['slug']) ?>"
           href="<?= e(url('portfolio.php?cat=' . $c['slug'] . ($sort !== 'curated' ? '&sort=' . $sort : ''))) ?>"
           <?= $cat === $c['slug'] ? 'aria-current="true"' : '' ?>>
          <?= e($c['name']) ?> <span class="filters__count"><?= (int) ($counts[$c['slug']] ?? 0) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="toolbar__right">
      <label for="sort">정렬</label>
      <select class="select toolbar__select" id="sort" name="sort" data-sort>
        <option value="curated" <?= $sort === 'curated' ? 'selected' : '' ?>>지정 순서</option>
        <option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>최신순</option>
        <option value="old" <?= $sort === 'old' ? 'selected' : '' ?>>오래된순</option>
      </select>
      <?php if ($cat !== 'all'): ?>
        <input type="hidden" name="cat" value="<?= e($cat) ?>">
      <?php endif; ?>
      <noscript><button class="link-more" type="submit">적용</button></noscript>
      <span class="toolbar__total" data-total><?= $visible ?>개</span>
    </div>
  </form>

  <div class="masonry" data-masonry data-category="<?= e($cat) ?>">
    <?php foreach ($projects as $i => $p): ?>
      <?php
      $hidden = $cat !== 'all' && $p['category_slug'] !== $cat;
      ob_start();
      work_card($p, $i + 1);
      $card = ob_get_clean();
      // 서버에서 걸러진 항목은 hidden 으로 표시해 두고, JS가 이어받습니다.
      echo $hidden ? str_replace('<a class="work ', '<a hidden class="work ', $card) : $card;
      ?>
    <?php endforeach; ?>
  </div>

  <div class="empty" data-empty <?= $visible ? 'hidden' : '' ?>>
    <p class="empty__title"><?= e($activeName) ?> 작업이 아직 없습니다.</p>
    <p>다른 카테고리를 선택하거나 전체 목록을 확인해 주세요.</p>
  </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
