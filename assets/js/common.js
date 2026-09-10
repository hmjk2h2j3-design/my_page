/* common.js — 헤더, 모바일 메뉴, 등장 효과 */

(function () {
  'use strict';

  /* ---------------------------------------------------------------- */
  /* 헤더: 스크롤 시 하단 경계선                                       */
  /* ---------------------------------------------------------------- */

  var header = document.querySelector('.site-header');

  if (header) {
    var setStuck = function () {
      header.classList.toggle('is-stuck', window.scrollY > 4);
    };
    setStuck();
    window.addEventListener('scroll', setStuck, { passive: true });
  }

  /* ---------------------------------------------------------------- */
  /* 모바일 메뉴                                                       */
  /* ---------------------------------------------------------------- */

  var toggle = document.querySelector('.nav-toggle');
  var nav = document.getElementById('site-nav');

  if (toggle && nav) {
    var closeNav = function () {
      nav.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
    };

    toggle.addEventListener('click', function () {
      var open = toggle.getAttribute('aria-expanded') === 'true';
      nav.classList.toggle('is-open', !open);
      toggle.setAttribute('aria-expanded', String(!open));
    });

    nav.addEventListener('click', function (event) {
      if (event.target.closest('a')) closeNav();
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') closeNav();
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 767) closeNav();
    });
  }

  /* 등장·커서 효과는 assets/js/motion.js 가 담당합니다. */
})();
