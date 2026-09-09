/* portfolio.js — 메이슨리 배치 + 카테고리 필터 + 정렬 */

(function () {
  'use strict';

  function init(scope) {
    var root = scope || document;
    var grid = root.querySelector('[data-masonry]');
    if (!grid || grid.dataset.ready === '1') return null;
    grid.dataset.ready = '1';

    var items = Array.prototype.slice.call(grid.querySelectorAll('.work'));
    var buttons = Array.prototype.slice.call(root.querySelectorAll('.filters__btn'));
    var sortSelect = root.querySelector('[data-sort]');
    var totalEl = root.querySelector('[data-total]');
    var empty = root.querySelector('[data-empty]');

    /* -------------------------------------------------------------- */
    /* 배치                                                            */
    /* -------------------------------------------------------------- */

    /*
      각 항목의 실제 높이를 읽어 grid-row 를 몇 칸 차지할지 계산합니다.
      이미지에 width/height 가 들어 있어 내려받기 전에도 높이를 알 수 있으므로
      목록이 뒤늦게 밀리지 않습니다.
    */
    function layout() {
      var styles = window.getComputedStyle(grid);
      var row = parseFloat(styles.getPropertyValue('--row')) || 8;
      var gap = parseFloat(styles.rowGap) || 0;

      items.forEach(function (item) {
        if (item.hidden) return;
        var height = item.getBoundingClientRect().height;
        var span = Math.max(1, Math.ceil((height + gap) / (row + gap)));
        item.style.setProperty('--span', span);
      });

      grid.classList.add('is-masonry');
    }

    function relayout() {
      if (!grid.isConnected) return;
      // 높이를 다시 재려면 먼저 span 을 비워 자연 높이로 되돌립니다.
      grid.classList.remove('is-masonry');
      items.forEach(function (item) { item.style.removeProperty('--span'); });
      void grid.offsetHeight; // 강제 리플로우
      layout();
    }

    var resizeTimer;
    window.addEventListener('resize', function () {
      window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(relayout, 140);
    });

    // 이미지가 예상과 다른 크기로 로드될 경우를 대비
    grid.querySelectorAll('img').forEach(function (img) {
      if (img.complete) return;
      img.addEventListener('load', relayout, { once: true });
      img.addEventListener('error', relayout, { once: true });
    });

    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(relayout);
    }

    /* -------------------------------------------------------------- */
    /* 필터 · 정렬                                                     */
    /* -------------------------------------------------------------- */

    var state = {
      category: grid.getAttribute('data-category') || 'all',
      sort: (sortSelect && sortSelect.value) || 'curated'
    };

    function compare(a, b) {
      var ya = Number(a.getAttribute('data-year'));
      var yb = Number(b.getAttribute('data-year'));
      var ca = a.getAttribute('data-created') || '';
      var cb = b.getAttribute('data-created') || '';

      if (state.sort === 'new') return yb - ya || cb.localeCompare(ca);
      if (state.sort === 'old') return ya - yb || ca.localeCompare(cb);
      return Number(a.getAttribute('data-order')) - Number(b.getAttribute('data-order'));
    }

    function apply(updateUrl) {
      var visible = 0;

      items.forEach(function (item) {
        var match = state.category === 'all' || item.getAttribute('data-category') === state.category;
        item.hidden = !match;
        if (match) visible += 1;
      });

      items.slice().sort(compare).forEach(function (item) { grid.appendChild(item); });

      buttons.forEach(function (btn) {
        var active = (btn.getAttribute('data-filter') || 'all') === state.category;
        btn.classList.toggle('is-active', active);
        btn.setAttribute('aria-current', active ? 'true' : 'false');
      });

      if (totalEl) totalEl.textContent = visible + '개';
      if (empty) empty.hidden = visible !== 0;

      relayout();
      if (updateUrl !== false) syncUrl();
    }

    function syncUrl() {
      // 정적 미리보기(#/ 해시 라우팅)에서는 주소를 건드리지 않습니다.
      if (grid.dataset.noUrl === '1') return;
      if (!window.history || !window.history.replaceState) return;

      var params = new URLSearchParams();
      if (state.category !== 'all') params.set('cat', state.category);
      if (state.sort !== 'curated') params.set('sort', state.sort);
      var query = params.toString();
      window.history.replaceState(null, '', query ? '?' + query : window.location.pathname);
    }

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function (event) {
        event.preventDefault();
        state.category = btn.getAttribute('data-filter') || 'all';
        apply();
      });
    });

    if (sortSelect) {
      sortSelect.addEventListener('change', function () {
        state.sort = sortSelect.value;
        apply();
      });
    }

    apply(false);
    return { relayout: relayout };
  }

  window.PortfolioGrid = { init: init };
  init(document);
})();
