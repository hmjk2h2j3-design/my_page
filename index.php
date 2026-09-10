<?php
/**
 * HOME — 대표 작업물과 작업 방식을 한 화면 흐름으로 보여줍니다.
 */

require_once __DIR__ . '/includes/repository.php';
require_once __DIR__ . '/includes/partials.php';

// 첫 번째 작업물은 히어로의 대표 이미지로 크게 쓰고,
// 선택 작업 목록은 그다음 순서부터 이어집니다.
$selected = repo_projects(['sort' => 'curated', 'limit' => 6]);
$feature  = array_shift($selected);
$lead     = array_shift($selected);
$rest     = $selected;

$categoryCounts = repo_category_counts();

$fields = [
    ['slug' => 'uiux',     'name' => 'UI/UX',    'body' => '앱과 서비스의 화면 흐름을 설계합니다. 정보구조를 먼저 확정하고, 반복 요소는 컴포넌트로 정의해 개발에 넘깁니다.'],
    ['slug' => 'web',      'name' => 'WEB',      'body' => '반응형 웹사이트를 설계하고 퍼블리싱 단계까지 직접 확인합니다. 대비와 키보드 접근은 코드에서 검수합니다.'],
    ['slug' => 'graphic',  'name' => 'GRAPHIC',  'body' => '포스터와 편집물처럼 인쇄를 전제로 한 조판을 다룹니다. 판형별 규격과 인쇄 도수까지 결정에 포함합니다.'],
    ['slug' => 'branding', 'name' => 'BRANDING', 'body' => '로고 한 장이 아니라 최소 사용 규격과 금지 예까지 담은 가이드로 마무리합니다. 남이 이어 쓸 수 있어야 합니다.'],
];

$approach = [
    [
        'title' => '문제를 문장 하나로 줄인다',
        'body'  => '무엇을 예쁘게 만들지보다 무엇이 불편한지를 먼저 한 문장으로 적습니다. 그 문장이 나오지 않으면 화면을 그리지 않습니다.',
    ],
    [
        'title' => '쓰는 사람을 직접 본다',
        'body'  => '인터뷰와 관찰로 실제 사용 순서를 확인합니다. 짐작으로 만든 흐름은 대부분 두 번째 화면에서 무너집니다.',
    ],
    [
        'title' => '구조를 먼저, 색을 나중에',
        'body'  => '정보구조와 저해상도 화면으로 흐름을 검증한 뒤에 타이포와 색을 올립니다. 순서를 바꾸면 되돌리는 비용이 커집니다.',
    ],
    [
        'title' => '넘길 수 있는 형태로 끝낸다',
        'body'  => '반복 요소를 컴포넌트로 정의하고 규격과 예외를 문서로 남깁니다. 개발자와 다음 담당자가 되묻지 않아도 되는 상태가 완료입니다.',
    ],
];

$pageDescription = site('name_ko') . '의 UI·UX / 그래픽 디자인 포트폴리오. 모바일 앱, 웹사이트, 브랜드 아이덴티티 작업물과 그 과정을 기록합니다.';
$pageCss  = ['home.css'];
$bodyClass = 'page-home';
require __DIR__ . '/includes/header.php';
?>

<!-- ============================= HERO ============================= -->
<section class="hero">

  <!-- 배경 그래픽. 순수 장식이라 스크린리더에서는 감춥니다.
       세기 조절은 assets/css/home.css 의 --atmos-strength 하나로 합니다. -->
  <div class="atmos" aria-hidden="true">
    <span class="atmos__ink atmos__ink--1"></span>
    <span class="atmos__ink atmos__ink--2"></span>
    <span class="atmos__ink atmos__ink--3"></span>
    <span class="atmos__ring atmos__ring--1"></span>
    <span class="atmos__ring atmos__ring--2"></span>
    <span class="atmos__ring atmos__ring--3"></span>
    <span class="atmos__dots atmos__dots--fine"></span>
    <span class="atmos__dots atmos__dots--coarse"></span>
    <span class="atmos__grain"></span>
  </div>

  <div class="container">

    <div class="hero__top">
      <span class="label">Portfolio — <?= date('Y') ?></span>
      <span class="label"><?= e(site('location')) ?></span>
    </div>

    <h1 class="hero__statement display" data-split>
      더 쉽게 보고,<br>
      더 자연스럽게 쓰도록<br>
      <em>디자인</em>합니다.
    </h1>

    <div class="hero__grid">
      <div class="hero__intro">
        <p class="lede">
          <?= e(site('name_ko')) ?>입니다. 모바일 앱과 웹 서비스의 화면을 설계하고,
          브랜드가 인쇄물까지 같은 얼굴로 확장되도록 규칙을 만듭니다.
          아래는 최근 3년간의 작업과, 각 작업에서 실제로 무엇을 바꿨는지에 대한 기록입니다.
        </p>
        <div class="hero__actions">
          <a class="btn magnet" href="<?= e(url('portfolio.php')) ?>">
            작업 전체 보기 <span class="btn__arrow" aria-hidden="true">&rarr;</span>
          </a>
          <a class="btn btn--ghost magnet" href="<?= e(url('about.php')) ?>">소개 및 역량</a>
        </div>
      </div>

      <dl class="facts">
        <div class="facts__row">
          <dt class="facts__key">Focus</dt>
          <dd class="facts__val">UI·UX 설계 / 웹 디자인 / 브랜드 아이덴티티</dd>
        </div>
        <div class="facts__row">
          <dt class="facts__key">Tools</dt>
          <dd class="facts__val">Figma, Illustrator, Photoshop, InDesign, HTML·CSS</dd>
        </div>
        <div class="facts__row">
          <dt class="facts__key">Works</dt>
          <dd class="facts__val"><?= count(repo_projects()) ?>개 프로젝트 — 2023–<?= date('Y') ?></dd>
        </div>
        <div class="facts__row">
          <dt class="facts__key">Status</dt>
          <dd class="facts__val"><?= e(site('available')) ?></dd>
        </div>
      </dl>
    </div>
  </div>

  <?php if ($feature): ?>
    <div class="container hero__feature reveal">
      <a href="<?= e(url('project.php?id=' . (int) $feature['id'])) ?>" aria-label="<?= e($feature['title']) ?> 프로젝트 보기">
        <div class="hero__feature-media">
          <img src="<?= e(url($feature['thumbnail'])) ?>"
               alt="<?= e($feature['title'] . ' — ' . $feature['subtitle']) ?>"
               fetchpriority="high" decoding="async"<?= image_attrs($feature['thumbnail']) ?>>
        </div>
      </a>
      <div class="hero__feature-caption">
        <p>
          <strong><?= e($feature['title']) ?></strong>
          <span class="muted"> — <?= e($feature['subtitle']) ?></span>
        </p>
        <span class="label"><?= e($feature['category_name']) ?> / <?= (int) $feature['year'] ?></span>
      </div>
    </div>
  <?php endif; ?>
</section>

<!-- ======================= SELECTED WORKS ========================= -->
<section class="section">
  <div class="container">
    <?php section_head('01', '선택 작업', '', ['href' => url('portfolio.php'), 'label' => '아카이브 전체']); ?>

    <div class="works">
      <?php if ($lead): ?>
        <?php work_card($lead, 1, 'works__lead reveal', excerpt($lead['description'], 110)); ?>
      <?php endif; ?>

      <?php foreach ($rest as $i => $p): ?>
        <?php
        $class = 'works__item reveal';
        if ($i % 2 === 1) {
            $class .= ' works__item--offset';
        }
        work_card($p, $i + 2, $class);
        ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ========================= 전문 분야 ============================ -->
<section class="section">
  <div class="container">
    <?php section_head('02', '전문 분야', '네 영역을 나눠서 보지 않습니다. 화면의 정보 위계를 잡는 일과 지면을 조판하는 일은 같은 판단에서 나옵니다.'); ?>

    <div class="fields">
      <?php foreach ($fields as $f): ?>
        <div class="field-card">
          <h3 class="field-card__name"><?= e($f['name']) ?></h3>
          <p class="field-card__body"><?= e($f['body']) ?></p>
          <a class="field-card__link" href="<?= e(url('portfolio.php?cat=' . $f['slug'])) ?>">
            작업 보기
            <span class="field-card__count"><?= (int) ($categoryCounts[$f['slug']] ?? 0) ?></span>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =========================== APPROACH =========================== -->
<section class="section">
  <div class="container">
    <?php section_head('03', '작업 방식', '결과물보다 결정 과정을 남기는 편입니다. 아래 네 가지는 프로젝트마다 반복해 온 순서입니다.'); ?>

    <div class="approach">
      <?php foreach ($approach as $i => $item): ?>
        <div class="approach__item">
          <span class="approach__num" aria-hidden="true"><?= e(idx($i + 1)) ?></span>
          <h3 class="h3"><?= e($item['title']) ?></h3>
          <p class="approach__body"><?= e($item['body']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============================ CONTACT =========================== -->
<section class="band" aria-labelledby="contact-title">
  <div class="container">
    <span class="label">04 — Contact</span>
    <div class="band__grid">
      <div>
        <h2 class="h1 band__title" id="contact-title">함께 만들 팀을 찾고 있습니다.</h2>
        <p class="band__note">
          포트폴리오 원본(PDF)이나 이력서가 필요하시면 메일로 요청해 주세요.
          채용 절차와 무관한 피드백도 환영합니다.
        </p>
      </div>
      <div>
        <a class="band__mail" href="mailto:<?= e(site('email')) ?>"><?= e(site('email')) ?></a>
        <p class="band__note">
          문의 폼은 <a href="<?= e(url('contact.php')) ?>" style="color:inherit;border-bottom:1px solid currentColor">Contact 페이지</a>에 있습니다.
        </p>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
