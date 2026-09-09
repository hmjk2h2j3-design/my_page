<?php
/**
 * ABOUT — 성격이나 취향이 아니라 어떤 작업을 어떻게 하는지를 씁니다.
 */

require_once __DIR__ . '/includes/repository.php';
require_once __DIR__ . '/includes/partials.php';

$skills = [
    [
        'name'  => 'UI 설계 · 정보구조',
        'level' => 92,
        'note'  => '메뉴 47개를 대분류 5개로 줄이고, 주문 처리 클릭 수를 7회에서 3회로 줄인 작업이 여기에 속합니다.',
    ],
    [
        'name'  => '타이포그래피 · 편집 디자인',
        'level' => 86,
        'note'  => '본문 가독성 기준을 먼저 정하고 제목을 맞춥니다. 인쇄물은 판형별 자간·굵기 규격을 따로 만듭니다.',
    ],
    [
        'name'  => '프로토타이핑 · 사용성 테스트',
        'level' => 78,
        'note'  => 'Figma 프로토타입으로 5명 단위 테스트를 돌리고, 막히는 지점을 화면 수정으로 되돌립니다.',
    ],
    [
        'name'  => '브랜드 아이덴티티',
        'level' => 74,
        'note'  => '로고 한 장이 아니라 최소 사용 규격, 금지 예, 인쇄 도수까지 포함한 가이드로 마무리합니다.',
    ],
    [
        'name'  => '디자인 시스템 · 문서화',
        'level' => 80,
        'note'  => '반복 요소를 컴포넌트로 정의해 개발 전달용 문서로 넘깁니다. 되묻는 횟수를 줄이는 것이 목적입니다.',
    ],
    [
        'name'  => '퍼블리싱 (HTML · CSS · JS)',
        'level' => 62,
        'note'  => '반응형과 접근성 기준을 코드 수준에서 확인할 수 있습니다. 이 사이트도 직접 만들었습니다.',
    ],
];

$tools = [
    ['name' => 'Figma',        'use' => '화면 설계 · 프로토타입'],
    ['name' => 'Illustrator',  'use' => '로고 · 그래픽'],
    ['name' => 'Photoshop',    'use' => '이미지 보정 · 목업'],
    ['name' => 'InDesign',     'use' => '편집 · 인쇄물'],
    ['name' => 'VS Code',      'use' => '퍼블리싱'],
    ['name' => 'Notion',       'use' => '리서치 정리'],
];

$steps = [
    ['title' => '문제 정의',   'body' => '무엇이 불편한지를 한 문장으로 적습니다. 이 문장이 프로젝트 내내 판단 기준이 됩니다.'],
    ['title' => '리서치',      'body' => '실제 사용자나 운영자를 만납니다. 인터뷰가 어려우면 최소한 경쟁 서비스 3개를 같은 과업으로 직접 써 봅니다.'],
    ['title' => '구조 설계',   'body' => '정보구조와 화면 흐름을 저해상도로 먼저 확정합니다. 이 단계에서 화면 수가 대체로 절반으로 줄어듭니다.'],
    ['title' => '시각화',      'body' => '타이포 · 색 · 간격 규칙을 정하고 화면에 올립니다. 대비는 WCAG AA를 기준으로 검수합니다.'],
    ['title' => '전달',        'body' => '컴포넌트와 예외 상황을 문서로 정리해 넘깁니다. 문서가 없으면 작업이 끝난 것이 아닙니다.'],
];

// 약력은 실제 등록된 작업물에서 자동으로 만듭니다.
$byYear = [];
foreach (repo_projects(['sort' => 'new']) as $p) {
    $byYear[(int) $p['year']][] = $p;
}

$pageTitle       = 'About';
$pageDescription = site('name_ko') . '의 디자인 역량, 사용 도구, 작업 순서를 정리한 소개 페이지입니다.';
$pageCss   = ['pages.css'];
$bodyClass = 'page-about';
require __DIR__ . '/includes/header.php';
?>

<section class="page-head">
  <div class="container">
    <div class="page-head__top">
      <span class="idx">About</span>
      <span class="label"><?= e(site('role')) ?></span>
    </div>

    <div class="page-head__grid">
      <h1 class="h1">
        보기 좋은 화면보다<br>
        설명할 수 있는 화면을 만듭니다.
      </h1>
      <p class="lede page-head__lede">
        디자인을 넘길 때 “왜 이렇게 했나요”라는 질문에 근거로 답할 수 있는 상태를 목표로 합니다.
        아래는 제가 실제로 할 수 있는 일과, 그 일을 하는 순서입니다.
      </p>
    </div>
  </div>
</section>

<div class="page-body">

  <!-- ---------------------------------------------------------- -->
  <section class="section" style="padding-top:0">
    <div class="container">
      <?php section_head('01', '프로필'); ?>

      <div class="about-statement">
        <div class="about-statement__text prose">
          <p>
            <?= e(site('name_ko')) ?>입니다. 모바일 앱과 웹 서비스의 화면을 설계하고, 브랜드가
            인쇄물까지 같은 얼굴로 확장되도록 규칙을 만듭니다.
          </p>
          <p>
            혼자 그리는 것보다 팀에 넘기는 단계를 더 신경 씁니다. 화면이 예쁜지보다
            개발자가 되묻지 않아도 되는지, 운영자가 한 달 뒤에도 같은 규칙으로 쓸 수 있는지를 봅니다.
          </p>
          <p>
            주로 다루는 영역은 UI·UX 설계와 편집 · 브랜드 그래픽입니다. 둘을 나누지 않는 편인데,
            화면의 정보 위계를 잡는 감각과 지면을 조판하는 감각이 크게 다르지 않기 때문입니다.
          </p>
        </div>

        <dl class="facts">
          <div class="facts__row">
            <dt class="facts__key">Name</dt>
            <dd class="facts__val"><?= e(site('name_ko')) ?> · <?= e(site('name_en')) ?></dd>
          </div>
          <div class="facts__row">
            <dt class="facts__key">Role</dt>
            <dd class="facts__val"><?= e(site('role')) ?></dd>
          </div>
          <div class="facts__row">
            <dt class="facts__key">Based</dt>
            <dd class="facts__val"><?= e(site('location')) ?></dd>
          </div>
          <div class="facts__row">
            <dt class="facts__key">Email</dt>
            <dd class="facts__val">
              <a href="mailto:<?= e(site('email')) ?>" style="border-bottom:1px solid var(--line)"><?= e(site('email')) ?></a>
            </dd>
          </div>
          <div class="facts__row">
            <dt class="facts__key">Status</dt>
            <dd class="facts__val"><?= e(site('available')) ?></dd>
          </div>
        </dl>
      </div>
    </div>
  </section>

  <!-- ---------------------------------------------------------- -->
  <section class="section">
    <div class="container">
      <?php section_head('02', '핵심 역량', '선 길이는 지금까지 작업에서 그 역량을 쓴 비중을 나타냅니다. 자격이 아니라 경험의 분포입니다.'); ?>

      <div class="skills">
        <?php foreach ($skills as $s): ?>
          <div class="skill">
            <h3 class="skill__name"><?= e($s['name']) ?></h3>
            <span class="skill__meter" aria-hidden="true"><i style="--level:<?= (int) $s['level'] ?>%"></i></span>
            <p class="skill__note"><?= e($s['note']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ---------------------------------------------------------- -->
  <section class="section">
    <div class="container">
      <?php section_head('03', '사용 도구'); ?>

      <div class="tools">
        <?php foreach ($tools as $t): ?>
          <div class="tool">
            <span class="tool__name"><?= e($t['name']) ?></span>
            <span class="tool__use"><?= e($t['use']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ---------------------------------------------------------- -->
  <section class="section">
    <div class="container">
      <?php section_head('04', '작업 순서', '프로젝트 규모와 상관없이 이 순서를 지킵니다. 순서를 건너뛰면 대부분 시각화 단계에서 되돌아옵니다.'); ?>

      <div class="steps">
        <?php foreach ($steps as $i => $s): ?>
          <div class="step">
            <span class="step__num" aria-hidden="true"><?= e(idx($i + 1)) ?></span>
            <h3 class="h3"><?= e($s['title']) ?></h3>
            <p class="step__body"><?= e($s['body']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ---------------------------------------------------------- -->
  <section class="section">
    <div class="container">
      <?php section_head('05', '연도별 작업', '', ['href' => url('portfolio.php'), 'label' => '아카이브 전체']); ?>

      <div class="timeline">
        <div class="timeline__row">
          <span class="timeline__year"><?= date('Y') ?></span>
          <div>
            <p class="timeline__title">개인 포트폴리오 사이트 설계 및 제작</p>
            <p class="timeline__note">기획 · 디자인 · 퍼블리싱 · 관리자 CMS 직접 구현</p>
          </div>
        </div>

        <?php foreach ($byYear as $year => $list): ?>
          <div class="timeline__row">
            <span class="timeline__year"><?= (int) $year ?></span>
            <div>
              <?php foreach ($list as $p): ?>
                <p class="timeline__title">
                  <a href="<?= e(url('project.php?id=' . (int) $p['id'])) ?>"
                     style="border-bottom:1px solid var(--line-soft)"><?= e($p['title']) ?></a>
                  <span class="timeline__note"> — <?= e($p['subtitle']) ?></span>
                </p>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
