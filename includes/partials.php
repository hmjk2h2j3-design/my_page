<?php
/**
 * 여러 페이지에서 반복되는 마크업 조각.
 */

require_once __DIR__ . '/functions.php';

/** 섹션 머리말 */
function section_head(string $index, string $title, string $note = '', array $link = []): void
{
    ?>
    <div class="section-head">
      <div class="section-head__title">
        <span class="idx"><?= e($index) ?></span>
        <h2 class="h2"><?= e($title) ?></h2>
      </div>
      <?php if ($link): ?>
        <a class="link-more" href="<?= e($link['href']) ?>"><?= e($link['label']) ?> <span aria-hidden="true">&rarr;</span></a>
      <?php elseif ($note !== ''): ?>
        <p class="section-head__note"><?= e($note) ?></p>
      <?php endif; ?>
    </div>
    <?php
}

/**
 * 작업물 카드.
 *
 * @param array    $p     프로젝트 행
 * @param int|null $n     카드 좌측에 표시할 번호 (null이면 숨김)
 * @param string   $class 추가 클래스
 * @param string   $note  대표 작업물에만 붙는 한 줄 설명
 */
function work_card(array $p, ?int $n = null, string $class = '', string $note = ''): void
{
    $thumb = $p['thumbnail'];
    $alt   = $p['title'] . ' — ' . ($p['subtitle'] ?? $p['category_name']);
    ?>
    <a class="work <?= e($class) ?>"
       href="<?= e(url('project.php?id=' . (int) $p['id'])) ?>"
       data-category="<?= e($p['category_slug']) ?>"
       data-year="<?= (int) $p['year'] ?>"
       data-created="<?= e($p['created_at']) ?>"
       data-order="<?= (int) $p['sort_order'] ?>">
      <figure class="work__media">
        <img src="<?= e(url($thumb)) ?>" alt="<?= e($alt) ?>" loading="lazy" decoding="async"<?= image_attrs($thumb) ?>>
      </figure>
      <div class="work__meta">
        <?php if ($n !== null): ?>
          <span class="work__idx" aria-hidden="true"><?= e(idx($n)) ?></span>
        <?php endif; ?>
        <h3 class="work__title"><?= e($p['title']) ?></h3>
        <p class="work__sub"><?= e($p['subtitle'] ?? '') ?></p>
        <span class="work__tag"><?= e($p['category_name']) ?> — <?= (int) $p['year'] ?></span>
        <?php if ($note !== ''): ?>
          <p class="work__note"><?= e($note) ?></p>
        <?php endif; ?>
      </div>
    </a>
    <?php
}
