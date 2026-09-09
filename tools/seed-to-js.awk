#!/usr/bin/awk -f
# seed.php -> preview/content.js (정적 미리보기용 스냅샷)

function esc(s) {
  gsub(/\\/, "\\\\", s)
  gsub(/"/, "\\\"", s)
  gsub(/\t/, " ", s)
  gsub(/\n/, "\\n", s)
  return s
}

function flushdoc(   out) {
  out = doc
  doc = ""
  return out
}

BEGIN {
  print "/* 자동 생성 — data/seed.php 의 스냅샷. 원본을 고쳤으면 tools/build-preview.sh 를 다시 실행하세요. */"
  state = "none"
  incat = 0
  inimg = 0
  indoc = 0
  first_proj = 1
}

# ---------- 카테고리 ----------
/^\$categories = \[/ { print "window.CATEGORIES = ["; incat = 1; next }
incat && /^\];/ { print "];"; incat = 0; next }
incat && /'id' =>/ {
  line = $0
  match(line, /'id' => ([0-9]+)/, a)
  id = a[1]
  match(line, /'name' => '([^']*)'/, b)
  name = b[1]
  match(line, /'slug' => '([^']*)'/, c)
  slug = c[1]
  printf "  { id: %s, name: \"%s\", slug: \"%s\" },\n", id, name, slug
  next
}

# ---------- 프로젝트 ----------
/^\$projects = \[/ { print "window.PROJECTS = ["; state = "proj"; next }
state == "proj" && /^\];/ { print "];"; state = "done"; next }

state != "proj" { next }

# nowdoc 본문 수집
indoc {
  if ($0 == "TXT,") {
    indoc = 0
    printf "    %s: \"%s\",\n", dockey, esc(flushdoc())
  } else {
    doc = doc (doc == "" ? "" : "\n") $0
  }
  next
}

# 이미지 배열
inimg {
  if ($0 ~ /^        \],/) {
    inimg = 0
    print "    ],"
    next
  }
  if (match($0, /'([^']+)'/, m)) {
    printf "      \"%s\",\n", m[1]
  }
  next
}

/^    \[$/ { print (first_proj ? "  {" : "  {"); first_proj = 0; next }
/^    \],$/ { print "  },"; next }

/'images' => \[/ { print "    images: ["; inimg = 1; next }

match($0, /'([a-z_]+)' => <<<'TXT'/, m) { dockey = m[1]; indoc = 1; doc = ""; next }

match($0, /'([a-z_]+)' => ([0-9]+),/, m) { printf "    %s: %s,\n", m[1], m[2]; next }

match($0, /'([a-z_]+)' => '(.*)',$/, m) { printf "    %s: \"%s\",\n", m[1], esc(m[2]); next }
