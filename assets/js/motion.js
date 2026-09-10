/* motion.js — 스크롤·포인터 상호작용
 *
 * 스크롤 자체는 브라우저 기본 동작을 그대로 씁니다.
 * (관성 스크롤을 직접 구현하면 position: sticky 로 붙여 둔
 *  헤더와 포트폴리오 필터 바가 깨지기 때문입니다.)
 *
 * 노출하는 것
 *   window.Motion.scan(root)  — 새로 그린 영역에 효과를 다시 겁니다
 *   window.Reveal.scan(root)  — 위와 같음 (이전 이름 유지)
 */

(function () {
  'use strict';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var finePointer = window.matchMedia('(pointer: fine)').matches;

  /* ---------------------------------------------------------------- */
  /* 1. 스크롤 진행 막대 + 헤더 축소                                   */
  /* ---------------------------------------------------------------- */

  var progress = document.querySelector('.progress');
  var root = document.documentElement;
  var ticking = false;

  function onScroll() {
    if (ticking) return;
    ticking = true;

    window.requestAnimationFrame(function () {
      var max = document.body.scrollHeight - window.innerHeight;
      var ratio = max > 0 ? Math.min(1, window.scrollY / max) : 0;

      if (progress) progress.style.setProperty('--p', ratio.toFixed(4));
      root.style.setProperty('--header-h', window.scrollY > 60 ? '56px' : '68px');

      ticking = false;
    });
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll, { passive: true });
  onScroll();

  /* ---------------------------------------------------------------- */
  /* 2. 제목을 줄 단위로 쪼개기                                        */
  /* ---------------------------------------------------------------- */

  /*
    <h1>더 쉽게 보고,<br>더 자연스럽게...</h1> 를
    <h1><span class="line"><i>더 쉽게 보고,</i></span>...</h1> 로 바꿉니다.
    바깥 span 이 넘치는 부분을 잘라 주고, 안쪽 i 가 아래에서 올라옵니다.
  */
  function splitLines(el) {
    if (!el || el.dataset.split === '1') return;
    el.dataset.split = '1';

    var chunks = el.innerHTML.split(/<br\s*\/?>/i);
    if (chunks.length < 2) return;

    el.innerHTML = chunks
      .map(function (chunk, i) {
        return '<span class="line" style="--i:' + i + '"><i>' + chunk.trim() + '</i></span>';
      })
      .join('');
  }

  /* ---------------------------------------------------------------- */
  /* 3. 화면에 들어오면 효과 실행                                      */
  /* ---------------------------------------------------------------- */

  var observer = 'IntersectionObserver' in window && !reduced
    ? new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          show(entry.target);
          observer.unobserve(entry.target);
        });
      }, { rootMargin: '0px 0px -10% 0px', threshold: 0.08 })
    : null;

  function show(el) {
    el.classList.add('is-in');
    el.classList.add('is-shown');

    // 같은 묶음 안의 이미지도 함께 닦아 냅니다.
    el.querySelectorAll('.work__media, .hero__feature-media, .project-gallery figure')
      .forEach(function (media) { media.classList.add('is-shown'); });
  }

  /** 형제 요소마다 순번을 매겨 조금씩 늦게 나타나게 합니다. */
  function stagger(list) {
    Array.prototype.forEach.call(list, function (el, i) {
      if (!el.style.getPropertyValue('--i')) {
        el.style.setProperty('--i', String(i % 6));
      }
    });
  }

  function scan(scope) {
    var area = scope || document;

    // 제목은 줄 단위로 쪼개고 바로 재생합니다 (첫 화면이라 기다릴 필요가 없습니다).
    area.querySelectorAll('[data-split]').forEach(function (el) {
      splitLines(el);
      if (reduced) {
        el.classList.add('is-in');
        return;
      }
      // 쪼갠 직후 배치를 확정시킨 뒤 다음 틱에 재생합니다.
      // (requestAnimationFrame 은 백그라운드 탭에서 멈추므로 타이머를 씁니다.)
      void el.offsetHeight;
      window.setTimeout(function () { el.classList.add('is-in'); }, 60);
    });

    // 그리드 안의 카드들에 순번 부여
    ['.works', '.fields', '.approach', '.related', '.masonry', '.project-gallery']
      .forEach(function (sel) {
        area.querySelectorAll(sel).forEach(function (grid) {
          stagger(grid.children);
        });
      });

    var targets = area.querySelectorAll('.reveal:not(.is-in)');

    if (!observer) {
      targets.forEach(show);
      area.querySelectorAll('.work__media, .hero__feature-media, .project-gallery figure')
        .forEach(function (m) { m.classList.add('is-shown'); });
      return;
    }

    targets.forEach(function (el) { observer.observe(el); });

    // .reveal 로 감싸지 않은 이미지도 개별로 관찰합니다.
    area.querySelectorAll('.work__media, .hero__feature-media, .project-gallery figure')
      .forEach(function (media) {
        if (media.closest('.reveal')) return;
        if (media.classList.contains('is-shown')) return;
        observer.observe(media);
      });

    failsafe(area);
  }

  /*
    안전장치.
    어떤 이유로든 효과가 재생되지 않아 내용이 안 보이는 상태로 남는 일은
    포트폴리오에서 가장 나쁜 실패입니다. 3초 뒤에는 무조건 다 보이게 합니다.
  */
  function failsafe(area) {
    window.setTimeout(function () {
      var limit = window.innerHeight;

      // 이미 화면에 들어와 있어야 할 것만 강제로 보여 줍니다.
      // (아래쪽 요소는 그대로 두어 스크롤 등장 효과를 살립니다.)
      area.querySelectorAll(
        '.reveal:not(.is-in), [data-split]:not(.is-in), ' +
        '.work__media:not(.is-shown), .hero__feature-media:not(.is-shown), ' +
        '.project-gallery figure:not(.is-shown)'
      ).forEach(function (el) {
        if (el.getBoundingClientRect().top < limit) show(el);
      });
    }, 3000);
  }

  /* ---------------------------------------------------------------- */
  /* 4. 커서 — 작업물 위에서만 원형 라벨                               */
  /* ---------------------------------------------------------------- */

  function initCursor() {
    if (!finePointer || reduced) return;

    var cursor = document.querySelector('.cursor');
    if (!cursor) return;

    var target = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
    var current = { x: target.x, y: target.y, scale: 0 };
    var wanted = 0;

    document.addEventListener('pointermove', function (event) {
      target.x = event.clientX;
      target.y = event.clientY;
    }, { passive: true });

    // 작업물 링크 위에 올라가면 커진다
    document.addEventListener('pointerover', function (event) {
      var hit = event.target.closest('a.work, [data-cursor]');
      if (!hit) return;
      wanted = 1;
      cursor.classList.add('is-on');
      cursor.textContent = hit.getAttribute('data-cursor') || 'View';
    });

    document.addEventListener('pointerout', function (event) {
      if (!event.target.closest('a.work, [data-cursor]')) return;
      if (event.relatedTarget && event.relatedTarget.closest('a.work, [data-cursor]')) return;
      wanted = 0;
    });

    // 페이지를 떠나거나 스크롤로 대상이 사라지는 경우
    document.addEventListener('pointerleave', function () { wanted = 0; });

    (function loop() {
      // 위치는 살짝 늦게 따라와야 부드럽게 보입니다.
      current.x += (target.x - current.x) * 0.16;
      current.y += (target.y - current.y) * 0.16;
      current.scale += (wanted - current.scale) * 0.18;

      if (current.scale < 0.01 && wanted === 0) {
        cursor.classList.remove('is-on');
      }

      cursor.style.transform =
        'translate3d(' + current.x + 'px,' + current.y + 'px,0)' +
        ' translate(-50%,-50%) scale(' + current.scale.toFixed(3) + ')';

      window.requestAnimationFrame(loop);
    })();
  }

  /* ---------------------------------------------------------------- */
  /* 5. 버튼 — 커서 쪽으로 아주 살짝                                   */
  /* ---------------------------------------------------------------- */

  function initMagnet() {
    if (!finePointer || reduced) return;

    document.addEventListener('pointermove', function (event) {
      var btn = event.target.closest('.magnet');
      if (!btn) return;

      var box = btn.getBoundingClientRect();
      var dx = event.clientX - (box.left + box.width / 2);
      var dy = event.clientY - (box.top + box.height / 2);

      btn.classList.add('is-pulled');
      btn.style.transform = 'translate(' + dx * 0.18 + 'px,' + dy * 0.24 + 'px)';
    }, { passive: true });

    document.addEventListener('pointerout', function (event) {
      var btn = event.target.closest('.magnet');
      if (!btn) return;
      if (event.relatedTarget && btn.contains(event.relatedTarget)) return;
      btn.classList.remove('is-pulled');
      btn.style.transform = '';
    });
  }

  /* ---------------------------------------------------------------- */

  window.Motion = { scan: scan };
  window.Reveal = { scan: scan }; // 이전 이름 유지

  initCursor();
  initMagnet();
  scan(document);
})();
