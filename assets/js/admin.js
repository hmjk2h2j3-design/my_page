/* admin.js — 삭제 확인, 이미지 선택 미리보기 */

(function () {
  'use strict';

  /* ---------------------------------------------------------------- */
  /* 되돌릴 수 없는 동작은 한 번 확인                                  */
  /* ---------------------------------------------------------------- */

  document.addEventListener('submit', function (event) {
    var form = event.target.closest('form[data-confirm]');
    if (!form) return;
    if (!window.confirm(form.getAttribute('data-confirm'))) {
      event.preventDefault();
    }
  });

  /* ---------------------------------------------------------------- */
  /* 대표 이미지: 고르는 즉시 미리보기 교체                            */
  /* ---------------------------------------------------------------- */

  var thumbInput = document.getElementById('thumbnail_file');

  if (thumbInput) {
    thumbInput.addEventListener('change', function () {
      var file = thumbInput.files && thumbInput.files[0];
      if (!file) return;

      var preview = document.querySelector('.thumb-preview');

      if (!preview) {
        preview = document.createElement('img');
        preview.className = 'thumb-preview';
        preview.alt = '선택한 대표 이미지';
        thumbInput.closest('.form-panel').insertBefore(preview, thumbInput.closest('.field'));
      }

      var url = URL.createObjectURL(file);
      preview.src = url;
      preview.addEventListener('load', function () { URL.revokeObjectURL(url); }, { once: true });
    });
  }

  /* ---------------------------------------------------------------- */
  /* 상세 이미지: 몇 장 골랐는지 알려 주기                             */
  /* ---------------------------------------------------------------- */

  var imagesInput = document.getElementById('images');

  if (imagesInput) {
    var note = document.createElement('span');
    note.className = 'field__help';
    imagesInput.closest('.field').appendChild(note);

    imagesInput.addEventListener('change', function () {
      var count = imagesInput.files ? imagesInput.files.length : 0;
      note.textContent = count ? count + '장 선택됨. 저장하면 목록 뒤에 순서대로 붙습니다.' : '';
    });
  }

  /* ---------------------------------------------------------------- */
  /* 삭제 체크한 이미지는 흐리게                                       */
  /* ---------------------------------------------------------------- */

  document.querySelectorAll('.img-item input[name="image_delete[]"]').forEach(function (box) {
    box.addEventListener('change', function () {
      var item = box.closest('.img-item');
      item.style.opacity = box.checked ? '0.4' : '';
    });
  });
})();
