/*
  app.js — 정적 미리보기 전용 렌더러.

  XAMPP 없이 index.html 을 더블클릭해도 사이트를 그대로 볼 수 있게,
  PHP 템플릿과 같은 마크업을 자바스크립트로 그려 냅니다.
  스타일과 메이슨리 로직은 실제 사이트(assets/)의 것을 그대로 씁니다.
*/

(function () {
  'use strict';

  var SITE = {
    nameKo: '김민겸',
    nameEn: 'Mingyeom Kim',
    role: 'UI·UX / 그래픽 디자이너',
    email: 'gmingyeom52@gmail.com',
    location: 'Seoul, KR',
    available: '2026 신입·주니어 채용 지원 중'
  };

  var YEAR = new Date().getFullYear();

  /* 이미지 경로 기준.
     preview/index.html 에서 열면 '../', 저장소 루트의 index.html 에서 열면 ''. */
  var baseMeta = document.querySelector('meta[name="asset-base"]');
  var BASE = baseMeta ? baseMeta.getAttribute('content') : '../';
  var main = document.getElementById('main');
  var catById = {};
  window.CATEGORIES.forEach(function (c) { catById[c.id] = c; });

  var projects = window.PROJECTS.map(function (p) {
    var c = catById[p.category_id] || {};
    return Object.assign({}, p, { category_name: c.name || '', category_slug: c.slug || '' });
  });

  /* ---------------------------------------------------------------- */
  /* 도우미                                                            */
  /* ---------------------------------------------------------------- */

  function esc(value) {
    return String(value === undefined || value === null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function idx(n) { return n < 10 ? '0' + n : String(n); }

  function lines(text) {
    return String(text || '').split('\n').map(function (l) { return l.trim(); })
      .filter(function (l) { return l !== ''; });
  }

  function excerpt(text, length) {
    var t = String(text || '').replace(/\s+/g, ' ').trim();
    return t.length <= length ? t : t.slice(0, length) + '…';
  }

  /** 원본 크기를 width/height 로 넣어 이미지 로딩 중 밀림을 막습니다. */
  function imgAttrs(path) {
    var name = path.split('/').pop();
    var size = window.IMAGE_SIZES[name];
    if (!size) return '';
    return ' width="' + size[0] + '" height="' + size[1] +
      '" style="aspect-ratio:' + size[0] + '/' + size[1] + '"';
  }

  function sorted(list, mode) {
    var out = list.slice();
    out.sort(function (a, b) {
      if (mode === 'new') return b.year - a.year || b.created_at.localeCompare(a.created_at);
      if (mode === 'old') return a.year - b.year || a.created_at.localeCompare(b.created_at);
      return a.sort_order - b.sort_order;
    });
    return out;
  }

  /* ---------------------------------------------------------------- */
  /* 마크업 조각                                                       */
  /* ---------------------------------------------------------------- */

  function sectionHead(index, title, note, link) {
    return '' +
      '<div class="section-head">' +
        '<div class="section-head__title">' +
          '<span class="idx">' + esc(index) + '</span>' +
          '<h2 class="h2">' + esc(title) + '</h2>' +
        '</div>' +
        (link
          ? '<a class="link-more" href="' + link.href + '">' + esc(link.label) + ' <span aria-hidden="true">&rarr;</span></a>'
          : (note ? '<p class="section-head__note">' + esc(note) + '</p>' : '')) +
      '</div>';
  }

  function workCard(p, n, cls, note) {
    return '' +
      '<a class="work ' + (cls || '') + '" href="#/project/' + p.id + '"' +
        ' data-category="' + esc(p.category_slug) + '"' +
        ' data-year="' + p.year + '"' +
        ' data-created="' + esc(p.created_at) + '"' +
        ' data-order="' + p.sort_order + '">' +
        '<figure class="work__media">' +
          '<img src="' + BASE + esc(p.thumbnail) + '" alt="' + esc(p.title + ' — ' + p.subtitle) +
          '" loading="lazy" decoding="async"' + imgAttrs(p.thumbnail) + '>' +
        '</figure>' +
        '<div class="work__meta">' +
          (n ? '<span class="work__idx" aria-hidden="true">' + idx(n) + '</span>' : '') +
          '<h3 class="work__title">' + esc(p.title) + '</h3>' +
          '<p class="work__sub">' + esc(p.subtitle) + '</p>' +
          '<span class="work__tag">' + esc(p.category_name) + ' — ' + p.year + '</span>' +
          (note ? '<p class="work__note">' + esc(note) + '</p>' : '') +
        '</div>' +
      '</a>';
  }

  /* ---------------------------------------------------------------- */
  /* HOME                                                              */
  /* ---------------------------------------------------------------- */

  var FIELDS = [
    ['uiux', 'UI/UX', '앱과 서비스의 화면 흐름을 설계합니다. 정보구조를 먼저 확정하고, 반복 요소는 컴포넌트로 정의해 개발에 넘깁니다.'],
    ['web', 'WEB', '반응형 웹사이트를 설계하고 퍼블리싱 단계까지 직접 확인합니다. 대비와 키보드 접근은 코드에서 검수합니다.'],
    ['graphic', 'GRAPHIC', '포스터와 편집물처럼 인쇄를 전제로 한 조판을 다룹니다. 판형별 규격과 인쇄 도수까지 결정에 포함합니다.'],
    ['branding', 'BRANDING', '로고 한 장이 아니라 최소 사용 규격과 금지 예까지 담은 가이드로 마무리합니다. 남이 이어 쓸 수 있어야 합니다.']
  ];

  var APPROACH = [
    ['문제를 문장 하나로 줄인다', '무엇을 예쁘게 만들지보다 무엇이 불편한지를 먼저 한 문장으로 적습니다. 그 문장이 나오지 않으면 화면을 그리지 않습니다.'],
    ['쓰는 사람을 직접 본다', '인터뷰와 관찰로 실제 사용 순서를 확인합니다. 짐작으로 만든 흐름은 대부분 두 번째 화면에서 무너집니다.'],
    ['구조를 먼저, 색을 나중에', '정보구조와 저해상도 화면으로 흐름을 검증한 뒤에 타이포와 색을 올립니다. 순서를 바꾸면 되돌리는 비용이 커집니다.'],
    ['넘길 수 있는 형태로 끝낸다', '반복 요소를 컴포넌트로 정의하고 규격과 예외를 문서로 남깁니다. 개발자와 다음 담당자가 되묻지 않아도 되는 상태가 완료입니다.']
  ];

  function renderHome() {
    var selected = sorted(projects, 'curated').slice(0, 6);
    var feature = selected[0];
    var lead = selected[1];
    var rest = selected.slice(2);

    var html = '' +
      '<section class="hero">' +
      '<div class="atmos" aria-hidden="true">' +
        '<span class="atmos__ink atmos__ink--1"></span>' +
        '<span class="atmos__ink atmos__ink--2"></span>' +
        '<span class="atmos__ink atmos__ink--3"></span>' +
        '<span class="atmos__ring atmos__ring--1"></span>' +
        '<span class="atmos__ring atmos__ring--2"></span>' +
        '<span class="atmos__ring atmos__ring--3"></span>' +
        '<span class="atmos__dots atmos__dots--fine"></span>' +
        '<span class="atmos__dots atmos__dots--coarse"></span>' +
        '<span class="atmos__grain"></span>' +
      '</div>' +
      '<div class="container">' +
        '<div class="hero__top">' +
          '<span class="label">Portfolio — ' + YEAR + '</span>' +
          '<span class="label">' + esc(SITE.location) + '</span>' +
        '</div>' +
        '<h1 class="hero__statement display" data-split>더 쉽게 보고,<br>더 자연스럽게 쓰도록<br><em>디자인</em>합니다.</h1>' +
        '<div class="hero__grid">' +
          '<div class="hero__intro">' +
            '<p class="lede">' + esc(SITE.nameKo) + '입니다. 모바일 앱과 웹 서비스의 화면을 설계하고, ' +
              '브랜드가 인쇄물까지 같은 얼굴로 확장되도록 규칙을 만듭니다. ' +
              '아래는 최근 3년간의 작업과, 각 작업에서 실제로 무엇을 바꿨는지에 대한 기록입니다.</p>' +
            '<div class="hero__actions">' +
              '<a class="btn magnet" href="#/portfolio">작업 전체 보기 <span class="btn__arrow" aria-hidden="true">&rarr;</span></a>' +
              '<a class="btn btn--ghost magnet" href="#/about">소개 및 역량</a>' +
            '</div>' +
          '</div>' +
          '<dl class="facts">' +
            factRow('Focus', 'UI·UX 설계 / 웹 디자인 / 브랜드 아이덴티티') +
            factRow('Tools', 'Figma, Illustrator, Photoshop, InDesign, HTML·CSS') +
            factRow('Works', projects.length + '개 프로젝트 — 2023–' + YEAR) +
            factRow('Status', SITE.available) +
          '</dl>' +
        '</div>' +
      '</div>' +

      '<div class="container hero__feature reveal">' +
        '<a href="#/project/' + feature.id + '" aria-label="' + esc(feature.title) + ' 프로젝트 보기">' +
          '<div class="hero__feature-media">' +
            '<img src="' + BASE + esc(feature.thumbnail) + '" alt="' + esc(feature.title + ' — ' + feature.subtitle) +
            '" decoding="async"' + imgAttrs(feature.thumbnail) + '>' +
          '</div>' +
        '</a>' +
        '<div class="hero__feature-caption">' +
          '<p><strong>' + esc(feature.title) + '</strong><span class="muted"> — ' + esc(feature.subtitle) + '</span></p>' +
          '<span class="label">' + esc(feature.category_name) + ' / ' + feature.year + '</span>' +
        '</div>' +
      '</div>' +
      '</section>' +

      '<section class="section"><div class="container">' +
        sectionHead('01', '선택 작업', '', { href: '#/portfolio', label: '아카이브 전체' }) +
        '<div class="works">' +
          workCard(lead, 1, 'works__lead reveal', excerpt(lead.description, 110)) +
          rest.map(function (p, i) {
            return workCard(p, i + 2, 'works__item reveal' + (i % 2 === 1 ? ' works__item--offset' : ''));
          }).join('') +
        '</div>' +
      '</div></section>' +

      '<section class="section"><div class="container">' +
        sectionHead('02', '전문 분야', '네 영역을 나눠서 보지 않습니다. 화면의 정보 위계를 잡는 일과 지면을 조판하는 일은 같은 판단에서 나옵니다.') +
        '<div class="fields">' +
          FIELDS.map(function (f) {
            var n = projects.filter(function (p) { return p.category_slug === f[0]; }).length;
            return '<div class="field-card">' +
              '<h3 class="field-card__name">' + esc(f[1]) + '</h3>' +
              '<p class="field-card__body">' + esc(f[2]) + '</p>' +
              '<a class="field-card__link" href="#/portfolio?cat=' + f[0] + '">작업 보기 ' +
                '<span class="field-card__count">' + n + '</span></a>' +
            '</div>';
          }).join('') +
        '</div>' +
      '</div></section>' +

      '<section class="section"><div class="container">' +
        sectionHead('03', '작업 방식', '결과물보다 결정 과정을 남기는 편입니다. 아래 네 가지는 프로젝트마다 반복해 온 순서입니다.') +
        '<div class="approach">' +
          APPROACH.map(function (a, i) {
            return '<div class="approach__item">' +
              '<span class="approach__num" aria-hidden="true">' + idx(i + 1) + '</span>' +
              '<h3 class="h3">' + esc(a[0]) + '</h3>' +
              '<p class="approach__body">' + esc(a[1]) + '</p>' +
            '</div>';
          }).join('') +
        '</div>' +
      '</div></section>' +

      '<section class="band"><div class="container">' +
        '<span class="label">04 — Contact</span>' +
        '<div class="band__grid">' +
          '<div>' +
            '<h2 class="h1 band__title">함께 만들 팀을 찾고 있습니다.</h2>' +
            '<p class="band__note">포트폴리오 원본(PDF)이나 이력서가 필요하시면 메일로 요청해 주세요. 채용 절차와 무관한 피드백도 환영합니다.</p>' +
          '</div>' +
          '<div>' +
            '<a class="band__mail" href="mailto:' + SITE.email + '">' + SITE.email + '</a>' +
            '<p class="band__note">문의 폼은 <a href="#/contact" style="color:inherit;border-bottom:1px solid currentColor">Contact 페이지</a>에 있습니다.</p>' +
          '</div>' +
        '</div>' +
      '</div></section>';

    return { title: SITE.nameKo + ' | ' + SITE.role + ' 포트폴리오', html: html, nav: '' };
  }

  function factRow(key, value) {
    return '<div class="facts__row"><dt class="facts__key">' + esc(key) +
      '</dt><dd class="facts__val">' + esc(value) + '</dd></div>';
  }

  /* ---------------------------------------------------------------- */
  /* ABOUT                                                             */
  /* ---------------------------------------------------------------- */

  var SKILLS = [
    ['UI 설계 · 정보구조', 92, '메뉴 47개를 대분류 5개로 줄이고, 주문 처리 클릭 수를 7회에서 3회로 줄인 작업이 여기에 속합니다.'],
    ['타이포그래피 · 편집 디자인', 86, '본문 가독성 기준을 먼저 정하고 제목을 맞춥니다. 인쇄물은 판형별 자간·굵기 규격을 따로 만듭니다.'],
    ['프로토타이핑 · 사용성 테스트', 78, 'Figma 프로토타입으로 5명 단위 테스트를 돌리고, 막히는 지점을 화면 수정으로 되돌립니다.'],
    ['브랜드 아이덴티티', 74, '로고 한 장이 아니라 최소 사용 규격, 금지 예, 인쇄 도수까지 포함한 가이드로 마무리합니다.'],
    ['디자인 시스템 · 문서화', 80, '반복 요소를 컴포넌트로 정의해 개발 전달용 문서로 넘깁니다. 되묻는 횟수를 줄이는 것이 목적입니다.'],
    ['퍼블리싱 (HTML · CSS · JS)', 62, '반응형과 접근성 기준을 코드 수준에서 확인할 수 있습니다. 이 사이트도 직접 만들었습니다.']
  ];

  var TOOLS = [
    ['Figma', '화면 설계 · 프로토타입'],
    ['Illustrator', '로고 · 그래픽'],
    ['Photoshop', '이미지 보정 · 목업'],
    ['InDesign', '편집 · 인쇄물'],
    ['VS Code', '퍼블리싱'],
    ['Notion', '리서치 정리']
  ];

  var STEPS = [
    ['문제 정의', '무엇이 불편한지를 한 문장으로 적습니다. 이 문장이 프로젝트 내내 판단 기준이 됩니다.'],
    ['리서치', '실제 사용자나 운영자를 만납니다. 인터뷰가 어려우면 최소한 경쟁 서비스 3개를 같은 과업으로 직접 써 봅니다.'],
    ['구조 설계', '정보구조와 화면 흐름을 저해상도로 먼저 확정합니다. 이 단계에서 화면 수가 대체로 절반으로 줄어듭니다.'],
    ['시각화', '타이포 · 색 · 간격 규칙을 정하고 화면에 올립니다. 대비는 WCAG AA를 기준으로 검수합니다.'],
    ['전달', '컴포넌트와 예외 상황을 문서로 정리해 넘깁니다. 문서가 없으면 작업이 끝난 것이 아닙니다.']
  ];

  function renderAbout() {
    var byYear = {};
    sorted(projects, 'new').forEach(function (p) {
      (byYear[p.year] = byYear[p.year] || []).push(p);
    });

    var years = Object.keys(byYear).sort(function (a, b) { return b - a; });

    var html = '' +
      '<section class="page-head"><div class="container">' +
        '<div class="page-head__top"><span class="idx">About</span><span class="label">' + esc(SITE.role) + '</span></div>' +
        '<div class="page-head__grid">' +
          '<h1 class="h1" data-split>보기 좋은 화면보다<br>설명할 수 있는 화면을 만듭니다.</h1>' +
          '<p class="lede page-head__lede">디자인을 넘길 때 “왜 이렇게 했나요”라는 질문에 근거로 답할 수 있는 상태를 목표로 합니다. 아래는 제가 실제로 할 수 있는 일과, 그 일을 하는 순서입니다.</p>' +
        '</div>' +
      '</div></section>' +

      '<div class="page-body">' +

      '<section class="section" style="padding-top:0"><div class="container">' +
        sectionHead('01', '프로필') +
        '<div class="about-statement">' +
          '<div class="about-statement__text prose">' +
            '<p>' + esc(SITE.nameKo) + '입니다. 모바일 앱과 웹 서비스의 화면을 설계하고, 브랜드가 인쇄물까지 같은 얼굴로 확장되도록 규칙을 만듭니다.</p>' +
            '<p>혼자 그리는 것보다 팀에 넘기는 단계를 더 신경 씁니다. 화면이 예쁜지보다 개발자가 되묻지 않아도 되는지, 운영자가 한 달 뒤에도 같은 규칙으로 쓸 수 있는지를 봅니다.</p>' +
            '<p>주로 다루는 영역은 UI·UX 설계와 편집 · 브랜드 그래픽입니다. 둘을 나누지 않는 편인데, 화면의 정보 위계를 잡는 감각과 지면을 조판하는 감각이 크게 다르지 않기 때문입니다.</p>' +
          '</div>' +
          '<dl class="facts">' +
            factRow('Name', SITE.nameKo + ' · ' + SITE.nameEn) +
            factRow('Role', SITE.role) +
            factRow('Based', SITE.location) +
            '<div class="facts__row"><dt class="facts__key">Email</dt><dd class="facts__val">' +
              '<a href="mailto:' + SITE.email + '" style="border-bottom:1px solid var(--line)">' + SITE.email + '</a></dd></div>' +
            factRow('Status', SITE.available) +
          '</dl>' +
        '</div>' +
      '</div></section>' +

      '<section class="section"><div class="container">' +
        sectionHead('02', '핵심 역량', '선 길이는 지금까지 작업에서 그 역량을 쓴 비중을 나타냅니다. 자격이 아니라 경험의 분포입니다.') +
        '<div class="skills">' +
          SKILLS.map(function (s) {
            return '<div class="skill">' +
              '<h3 class="skill__name">' + esc(s[0]) + '</h3>' +
              '<span class="skill__meter" aria-hidden="true"><i style="--level:' + s[1] + '%"></i></span>' +
              '<p class="skill__note">' + esc(s[2]) + '</p>' +
            '</div>';
          }).join('') +
        '</div>' +
      '</div></section>' +

      '<section class="section"><div class="container">' +
        sectionHead('03', '사용 도구') +
        '<div class="tools">' +
          TOOLS.map(function (t) {
            return '<div class="tool"><span class="tool__name">' + esc(t[0]) +
              '</span><span class="tool__use">' + esc(t[1]) + '</span></div>';
          }).join('') +
        '</div>' +
      '</div></section>' +

      '<section class="section"><div class="container">' +
        sectionHead('04', '작업 순서', '프로젝트 규모와 상관없이 이 순서를 지킵니다. 순서를 건너뛰면 대부분 시각화 단계에서 되돌아옵니다.') +
        '<div class="steps">' +
          STEPS.map(function (s, i) {
            return '<div class="step">' +
              '<span class="step__num" aria-hidden="true">' + idx(i + 1) + '</span>' +
              '<h3 class="h3">' + esc(s[0]) + '</h3>' +
              '<p class="step__body">' + esc(s[1]) + '</p>' +
            '</div>';
          }).join('') +
        '</div>' +
      '</div></section>' +

      '<section class="section"><div class="container">' +
        sectionHead('05', '연도별 작업', '', { href: '#/portfolio', label: '아카이브 전체' }) +
        '<div class="timeline">' +
          '<div class="timeline__row"><span class="timeline__year">' + YEAR + '</span><div>' +
            '<p class="timeline__title">개인 포트폴리오 사이트 설계 및 제작</p>' +
            '<p class="timeline__note">기획 · 디자인 · 퍼블리싱 · 관리자 CMS 직접 구현</p>' +
          '</div></div>' +
          years.map(function (y) {
            return '<div class="timeline__row"><span class="timeline__year">' + y + '</span><div>' +
              byYear[y].map(function (p) {
                return '<p class="timeline__title"><a href="#/project/' + p.id +
                  '" style="border-bottom:1px solid var(--line-soft)">' + esc(p.title) +
                  '</a><span class="timeline__note"> — ' + esc(p.subtitle) + '</span></p>';
              }).join('') +
            '</div></div>';
          }).join('') +
        '</div>' +
      '</div></section>' +

      '</div>';

    return { title: 'About | ' + SITE.nameKo + ' Portfolio', html: html, nav: 'about' };
  }

  /* ---------------------------------------------------------------- */
  /* PORTFOLIO                                                         */
  /* ---------------------------------------------------------------- */

  function renderPortfolio(query) {
    var cat = query.cat || 'all';
    var sort = query.sort || 'curated';
    var list = sorted(projects, sort);

    var counts = {};
    projects.forEach(function (p) { counts[p.category_slug] = (counts[p.category_slug] || 0) + 1; });

    var visible = cat === 'all' ? list.length : list.filter(function (p) { return p.category_slug === cat; }).length;
    var activeName = cat === 'all' ? '전체' : ((catById[Object.keys(catById).filter(function (k) {
      return catById[k].slug === cat;
    })[0]] || {}).name || '전체');

    var html = '' +
      '<section class="page-head"><div class="container">' +
        '<div class="page-head__top"><span class="idx">Portfolio</span>' +
        '<span class="label">' + projects.length + ' Projects · 2023–' + YEAR + '</span></div>' +
        '<div class="page-head__grid">' +
          '<h1 class="h1" data-split>자르지 않고<br>원래 비율로 모아 둔 아카이브.</h1>' +
          '<p class="lede page-head__lede">포스터, 모바일 화면, 긴 상세페이지는 애초에 비율이 다릅니다. 같은 크기의 카드에 맞춰 잘라내면 원래 의도한 프레임이 사라지기 때문에, 여기서는 모든 작업물을 원본 비율 그대로 둡니다.</p>' +
        '</div>' +
      '</div></section>' +

      '<div class="container">' +
        '<div class="toolbar">' +
          '<nav class="filters" aria-label="카테고리 필터">' +
            '<a class="filters__btn' + (cat === 'all' ? ' is-active' : '') + '" data-filter="all" href="#/portfolio">' +
              'ALL <span class="filters__count">' + projects.length + '</span></a>' +
            window.CATEGORIES.map(function (c) {
              return '<a class="filters__btn' + (cat === c.slug ? ' is-active' : '') +
                '" data-filter="' + c.slug + '" href="#/portfolio?cat=' + c.slug + '">' +
                esc(c.name) + ' <span class="filters__count">' + (counts[c.slug] || 0) + '</span></a>';
            }).join('') +
          '</nav>' +
          '<div class="toolbar__right">' +
            '<label for="sort">정렬</label>' +
            '<select class="select toolbar__select" id="sort" data-sort>' +
              '<option value="curated"' + (sort === 'curated' ? ' selected' : '') + '>지정 순서</option>' +
              '<option value="new"' + (sort === 'new' ? ' selected' : '') + '>최신순</option>' +
              '<option value="old"' + (sort === 'old' ? ' selected' : '') + '>오래된순</option>' +
            '</select>' +
            '<span class="toolbar__total" data-total>' + visible + '개</span>' +
          '</div>' +
        '</div>' +

        '<div class="masonry" data-masonry data-no-url="1" data-category="' + esc(cat) + '">' +
          list.map(function (p, i) {
            var hidden = cat !== 'all' && p.category_slug !== cat;
            return workCard(p, i + 1, '').replace('<a class="work', hidden ? '<a hidden class="work' : '<a class="work');
          }).join('') +
        '</div>' +

        '<div class="empty" data-empty' + (visible ? ' hidden' : '') + '>' +
          '<p class="empty__title">' + esc(activeName) + ' 작업이 아직 없습니다.</p>' +
          '<p>다른 카테고리를 선택하거나 전체 목록을 확인해 주세요.</p>' +
        '</div>' +
      '</div>';

    return { title: 'Portfolio | ' + SITE.nameKo + ' Portfolio', html: html, nav: 'portfolio', grid: true };
  }

  /* ---------------------------------------------------------------- */
  /* PROJECT DETAIL                                                    */
  /* ---------------------------------------------------------------- */

  function renderProject(id) {
    var project = projects.filter(function (p) { return p.id === id; })[0];
    if (!project) return renderNotFound();

    var related = projects.filter(function (p) {
      return p.id !== project.id && p.category_slug === project.category_slug;
    });
    sorted(projects, 'new').forEach(function (p) {
      if (related.length >= 3) return;
      if (p.id !== project.id && related.indexOf(p) === -1) related.push(p);
    });
    related = related.slice(0, 3);

    var html = '' +
      '<article>' +
      '<header class="project-head"><div class="container">' +
        '<nav class="project-head__breadcrumb" aria-label="위치">' +
          '<a href="#/portfolio">Portfolio</a><span aria-hidden="true">/</span>' +
          '<a href="#/portfolio?cat=' + esc(project.category_slug) + '">' + esc(project.category_name) + '</a>' +
        '</nav>' +
        '<h1 class="h1 project-head__title">' + esc(project.title) + '</h1>' +
        '<p class="lede project-head__sub">' + esc(project.subtitle) + '</p>' +
        '<dl class="project-facts">' +
          projectFact('Category', project.category_name) +
          projectFact('Year', project.year) +
          projectFact('Role', project.role) +
          projectFact('Tools', project.tools) +
        '</dl>' +
      '</div></header>' +

      '<section class="container project-brief">' +
        '<div class="prose"><span class="label">Overview</span><div style="margin-top:14px">' +
          lines(project.description).map(function (l) { return '<p>' + esc(l) + '</p>'; }).join('') +
        '</div></div>' +
        '<div class="project-goal"><span class="label">Goal</span>' +
          '<p class="project-goal__text" style="margin-top:12px">' + esc(project.goal) + '</p></div>' +
      '</section>' +

      '<section class="container">' +
        '<h2 class="visually-hidden">디자인 이미지</h2>' +
        '<div class="project-gallery">' +
          project.images.map(function (img, i) {
            return '<figure class="reveal">' +
              '<img src="' + BASE + esc(img) + '" alt="' + esc(project.title) + ' 디자인 이미지 ' + (i + 1) +
              '" loading="' + (i === 0 ? 'eager' : 'lazy') + '" decoding="async"' + imgAttrs(img) + '>' +
              '<figcaption><b>' + idx(i + 1) + '</b><span>' + esc(project.title) + ' — ' + esc(project.category_name) + '</span></figcaption>' +
            '</figure>';
          }).join('') +
        '</div>' +
      '</section>' +

      '<section class="container project-notes">' +
        '<div><span class="label">Process</span><ol class="numbered" style="margin-top:16px">' +
          lines(project.process).map(function (l) { return '<li>' + esc(l) + '</li>'; }).join('') +
        '</ol></div>' +
        '<div><span class="label">Result</span><ul class="dashed" style="margin-top:16px">' +
          lines(project.result).map(function (l) { return '<li>' + esc(l) + '</li>'; }).join('') +
        '</ul></div>' +
      '</section>' +
      '</article>' +

      '<section class="section"><div class="container">' +
        sectionHead('—', '다른 작업', '', { href: '#/portfolio', label: '아카이브 전체' }) +
        '<div class="related">' +
          related.map(function (p, i) { return workCard(p, i + 1, 'reveal'); }).join('') +
        '</div>' +
      '</div></section>';

    return { title: project.title + ' | ' + SITE.nameKo + ' Portfolio', html: html, nav: 'portfolio' };
  }

  function projectFact(key, value) {
    return '<div class="project-facts__item"><dt class="project-facts__key">' + esc(key) +
      '</dt><dd class="project-facts__val">' + esc(value) + '</dd></div>';
  }

  /* ---------------------------------------------------------------- */
  /* CONTACT                                                           */
  /* ---------------------------------------------------------------- */

  function renderContact() {
    var html = '' +
      '<section class="page-head"><div class="container">' +
        '<div class="page-head__top"><span class="idx">Contact</span><span class="label">' + esc(SITE.available) + '</span></div>' +
        '<div class="page-head__grid">' +
          '<h1 class="h1" data-split>채용 · 협업 문의를<br>기다리고 있습니다.</h1>' +
          '<p class="lede page-head__lede">포트폴리오 원본(PDF)이나 이력서가 필요하시면 아래로 요청해 주세요. 평일 기준 하루 안에 회신합니다.</p>' +
        '</div>' +
      '</div></section>' +

      '<div class="page-body"><div class="container contact-grid">' +
        '<div>' +
          '<div data-form-msg></div>' +
          '<form class="contact-form" novalidate>' +
            '<div class="field"><label for="name">이름 <span class="req" aria-hidden="true">*</span></label>' +
              '<input class="input" type="text" id="name" name="name" required maxlength="60" autocomplete="name" placeholder="홍길동"></div>' +
            '<div class="field"><label for="email">이메일 <span class="req" aria-hidden="true">*</span></label>' +
              '<input class="input" type="email" id="email" name="email" required maxlength="180" autocomplete="email" placeholder="name@company.com"></div>' +
            '<div class="field"><label for="message">문의 내용 <span class="req" aria-hidden="true">*</span></label>' +
              '<textarea class="textarea" id="message" name="message" required maxlength="3000" placeholder="채용 포지션이나 프로젝트 내용을 간단히 적어 주세요."></textarea></div>' +
            '<div class="contact-form__actions">' +
              '<button class="btn" type="submit">보내기 <span class="btn__arrow" aria-hidden="true">&rarr;</span></button>' +
              '<span class="contact-form__hint">보내주신 내용은 채용 문의 회신 목적으로만 사용합니다.</span>' +
            '</div>' +
          '</form>' +
        '</div>' +

        '<aside class="contact-aside">' +
          '<div class="contact-aside__block"><span class="label">Email</span>' +
            '<p style="margin-top:10px"><a class="contact-aside__mail" href="mailto:' + SITE.email + '">' + SITE.email + '</a></p></div>' +
          '<div class="contact-aside__block"><span class="label">보내주시면 좋은 내용</span>' +
            '<ul class="dashed" style="margin-top:10px">' +
              '<li>채용 포지션과 담당 업무 범위</li>' +
              '<li>필요한 산출물 형태 (PDF · 원본 파일 · 링크)</li>' +
              '<li>회신받을 연락처와 희망 일정</li>' +
            '</ul></div>' +
          '<div class="contact-aside__block"><span class="label">Based in</span>' +
            '<p style="margin-top:10px" class="muted">' + esc(SITE.location) + ' · 원격 협업 가능</p></div>' +
        '</aside>' +
      '</div></div>';

    return { title: 'Contact | ' + SITE.nameKo + ' Portfolio', html: html, nav: 'contact', contact: true };
  }

  function renderNotFound() {
    return {
      title: '페이지를 찾을 수 없습니다',
      nav: '',
      html: '<div class="container error-page">' +
        '<span class="idx">404</span>' +
        '<h1 class="h1" style="margin-top:16px">이 주소에는 아무것도 없습니다.</h1>' +
        '<p class="lede" style="margin-top:18px">주소가 바뀌었거나, 아직 공개하지 않은 작업물일 수 있습니다. 아카이브에서 다시 찾아 주세요.</p>' +
        '<p style="margin-top:30px"><a class="btn" href="#/portfolio">아카이브로 이동 <span class="btn__arrow" aria-hidden="true">&rarr;</span></a></p>' +
        '</div>'
    };
  }

  /* ---------------------------------------------------------------- */
  /* 라우팅                                                            */
  /* ---------------------------------------------------------------- */

  function parseHash() {
    var raw = window.location.hash.replace(/^#/, '') || '/';
    var parts = raw.split('?');
    var path = parts[0].replace(/\/+$/, '') || '/';
    var query = {};
    (parts[1] || '').split('&').forEach(function (pair) {
      if (!pair) return;
      var kv = pair.split('=');
      query[decodeURIComponent(kv[0])] = decodeURIComponent(kv[1] || '');
    });
    return { path: path, query: query };
  }

  function route() {
    var r = parseHash();
    var page;

    if (r.path === '/' || r.path === '') page = renderHome();
    else if (r.path === '/about') page = renderAbout();
    else if (r.path === '/portfolio') page = renderPortfolio(r.query);
    else if (/^\/project\/\d+$/.test(r.path)) page = renderProject(Number(r.path.split('/')[2]));
    else if (r.path === '/contact') page = renderContact();
    else page = renderNotFound();

    main.innerHTML = page.html;
    document.title = page.title;

    document.querySelectorAll('[data-nav]').forEach(function (link) {
      var active = link.getAttribute('data-nav') === page.nav;
      link.classList.toggle('is-active', active);
      if (active) link.setAttribute('aria-current', 'page');
      else link.removeAttribute('aria-current');
    });

    if (page.grid && window.PortfolioGrid) window.PortfolioGrid.init(document);
    if (window.Reveal) window.Reveal.scan(main);
    if (page.contact) bindContactForm();

    window.scrollTo(0, 0);
  }

  function bindContactForm() {
    var form = main.querySelector('.contact-form');
    var slot = main.querySelector('[data-form-msg]');
    if (!form) return;

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      slot.innerHTML = '<p class="notice">이 화면은 서버 없이 여는 미리보기라 폼 전송이 동작하지 않습니다. ' +
        'XAMPP에서 contact.php 로 열면 실제로 접수됩니다. 지금은 ' +
        '<a href="mailto:' + SITE.email + '" style="border-bottom:1px solid currentColor">' + SITE.email + '</a> 로 보내 주세요.</p>';
      slot.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
  }

  var yearSlot = document.querySelector('[data-year]');
  if (yearSlot) yearSlot.textContent = YEAR;

  window.addEventListener('hashchange', route);
  route();
})();
