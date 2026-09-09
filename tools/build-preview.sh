#!/usr/bin/env bash
#
# 정적 미리보기(preview/)에 쓰는 데이터를 다시 만듭니다.
#
#   bash tools/build-preview.sh
#
# 하는 일
#   1) data/seed.php  -> preview/content.js      (프로젝트 글과 이미지 경로)
#   2) assets/images/works/*.svg -> preview/image-sizes.js  (원본 가로세로)
#
# preview/index.html 과 preview/app.js 는 손대지 않습니다.

set -e

cd "$(dirname "$0")/.."

command -v awk >/dev/null || { echo "awk 가 필요합니다."; exit 1; }

awk -f tools/seed-to-js.awk data/seed.php > preview/content.js
echo "preview/content.js 생성"

{
  echo "/* 자동 생성 — assets/images/works 의 원본 크기 (레이아웃 밀림 방지용) */"
  echo "window.IMAGE_SIZES = {"
  for f in assets/images/works/*.svg assets/images/works/*.png assets/images/works/*.jpg; do
    [ -e "$f" ] || continue
    name=$(basename "$f")
    case "$f" in
      *.svg)
        wh=$(head -c 400 "$f" | sed -n 's/.*viewBox="0 0 \([0-9]*\) \([0-9]*\)".*/\1,\2/p')
        ;;
      *)
        # 래스터 이미지는 SVG처럼 파싱할 수 없으므로 건너뜁니다.
        wh=""
        ;;
    esac
    [ -n "$wh" ] && echo "  '$name': [$wh],"
  done
  echo "};"
} > preview/image-sizes.js
echo "preview/image-sizes.js 생성"

# ------------------------------------------------------------------
# GitHub Pages 용 진입 파일
#
# GitHub Pages 는 PHP 를 실행하지 못하므로, 저장소 루트에 정적 index.html 을
# 만들어 둡니다. preview/index.html 과 내용은 같고 경로만 루트 기준입니다.
# (로컬 XAMPP 에서는 .htaccess 의 DirectoryIndex 순서 때문에 index.php 가 먼저 뜹니다.)
# ------------------------------------------------------------------
sed -e 's|"\.\./assets/|"assets/|g' \
    -e 's|src="content\.js"|src="preview/content.js"|' \
    -e 's|src="image-sizes\.js"|src="preview/image-sizes.js"|' \
    -e 's|src="app\.js"|src="preview/app.js"|' \
    -e 's|name="asset-base" content="\.\./"|name="asset-base" content=""|' \
    preview/index.html > index.html
echo "index.html 생성 (GitHub Pages 진입 파일)"

# Jekyll 처리를 건너뛰게 해서 배포를 빠르고 예측 가능하게 만듭니다.
: > .nojekyll

echo "완료. preview/index.html 을 열어 확인하세요."
