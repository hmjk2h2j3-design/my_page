# 김민겸 — 개인 디자인 포트폴리오 사이트

`personal_portfolio_development_plan.md` 기획서를 기준으로 만든 개인 포트폴리오 웹사이트입니다.
프레임워크 없이 HTML · CSS · Vanilla JS + PHP 8 + MySQL 8 로 구성했습니다.

---

## 1. 지금 바로 보기 (설치 없이)

`preview/index.html` 을 더블클릭하면 됩니다. XAMPP도, 인터넷 연결도 필요 없습니다.
(웹폰트만 인터넷에서 받아오므로, 오프라인이면 시스템 기본 서체로 보입니다.)

- 면접장에서 노트북으로 바로 열어 보여 줄 때
- GitHub Pages 같은 정적 호스팅에 올릴 때

이 미리보기는 **실제 사이트와 같은 CSS · JS를 그대로 쓰는 스냅샷**입니다.
글과 이미지는 `data/seed.php` 의 내용을 `preview/content.js` 로 복사해 둔 것이라,
`data/seed.php` 를 고쳤다면 아래 명령으로 다시 만들어야 반영됩니다.

```bash
bash tools/build-preview.sh
```

문의 폼은 서버가 필요하므로 미리보기에서는 안내 문구만 나옵니다.

---

## 1-1. GitHub Pages 로 링크 만들기

**GitHub Pages 는 PHP 를 실행하지 못합니다.** `index.php` 를 올려도 코드가 그대로 보이거나
파일이 내려받아집니다. 그래서 저장소 루트에 정적 진입 파일 `index.html` 을 함께 넣어 두었습니다.
Pages 는 `index.html` 을, 로컬 XAMPP 는 `.htaccess` 의 `DirectoryIndex` 순서에 따라
`index.php` 를 먼저 씁니다. 한 저장소로 둘 다 됩니다.

### 올릴 때 반드시 확인할 것

폴더를 통째로 올려야 합니다. 파일만 골라 올리면 `assets/` 가 빠져서 화면이 하얗게 나옵니다.

```text
반드시 포함해야 하는 것
├── index.html          ← Pages 진입 파일
├── .nojekyll           ← 없으면 배포가 느리고 예측하기 어려워집니다
├── assets/             ← css · js · 작업물 이미지 (이게 빠지면 아무것도 안 보입니다)
└── preview/            ← content.js · image-sizes.js · app.js
```

### 설정

저장소 → **Settings** → **Pages** → Source 를 `Deploy from a branch` 로,
Branch 를 `main` + `/ (root)` 로 지정하고 저장하면 1–2분 뒤 주소가 발급됩니다.

```text
https://<GitHub 아이디>.github.io/<저장소 이름>/
```

### 주의

`config/database.php` 는 공개 저장소에서 누구나 열어 볼 수 있습니다.
지금은 XAMPP 기본값(root / 빈 비밀번호)이라 문제가 없지만,
**실제 서버의 DB 비밀번호를 넣은 뒤에는 절대 커밋하지 마세요.**
그때는 `.gitignore` 에 `config/database.php` 를 추가하면 됩니다.

---

## 2. 전체 기능으로 실행하기 (XAMPP)

### 2.1 설치

1. [XAMPP](https://www.apachefriends.org/) 를 설치하고 **Apache**, **MySQL** 을 시작합니다.
2. 이 폴더를 통째로 `C:\xampp\htdocs\portfolio` 로 옮깁니다.
3. 브라우저에서 `http://localhost/phpmyadmin` 접속 → `SQL` 탭 →
   `sql/schema.sql` 내용을 붙여넣고 실행합니다.
4. `config/site.php` 의 `base_url` 을 설치 위치에 맞게 고칩니다.

   | 설치 위치 | base_url |
   |---|---|
   | `htdocs/` 바로 아래 | `''` (그대로 둠) |
   | `htdocs/portfolio/` | `'/portfolio'` |

5. `http://localhost/portfolio/` 접속.

### 2.2 관리자 계정 만들기

비밀번호는 평문으로 저장하지 않습니다. 해시를 만들어 넣습니다.

```bash
C:\xampp\php\php.exe tools/make-hash.php 원하는비밀번호
```

출력된 `$2y$...` 문자열을 복사해 phpMyAdmin 에서 실행합니다.

```sql
INSERT INTO users (userid, password) VALUES ('admin', '여기에_붙여넣기');
```

이제 `http://localhost/portfolio/admin/` 으로 로그인할 수 있습니다.

### 2.3 DB 없이도 동작합니다

MySQL 연결에 실패하면 공개 페이지는 `data/seed.php` 의 샘플 데이터로 자동 전환됩니다.
설정을 잘못해도 사이트가 죽지 않게 하기 위한 장치이며, 관리자 CMS는 DB가 있어야 씁니다.

---

## 3. 폴더 구조

```text
portfolio/
├── index.php          HOME
├── about.php          소개 · 역량 · 작업 순서
├── portfolio.php      아카이브 (메이슨리 + 카테고리 필터)
├── project.php        작업물 상세  (?id=1)
├── contact.php        문의 폼
├── 404.php
│
├── admin/             관리자 CMS
│   ├── login.php  logout.php  index.php
│   ├── projects.php  project-form.php  project-delete.php
│   ├── categories.php  contacts.php
│   └── includes/      관리자 공통 헤더 · 푸터
│
├── includes/          공통 PHP (웹에서 직접 접근 차단)
│   ├── functions.php   헬퍼 · CSRF · 이미지 크기
│   ├── repository.php  DB 접근 (+ DB 없을 때 대체)
│   ├── auth.php        로그인 · 세션
│   ├── upload.php      이미지 업로드 · 썸네일
│   ├── partials.php    반복 마크업
│   └── header.php  footer.php
│
├── config/            설정 (웹에서 직접 접근 차단)
│   ├── site.php        이름 · 이메일 · base_url
│   └── database.php    DB 접속 정보
│
├── data/              DB 없을 때 쓰는 데이터 (접근 차단)
├── sql/schema.sql     테이블 + 기본 데이터
├── tools/             해시 생성기, 미리보기 빌드 스크립트
├── preview/           무설치 미리보기
├── uploads/projects/  업로드 이미지 (original / thumbnail)
└── assets/
    ├── css/  reset · common · layout · home · portfolio · pages · admin
    ├── js/   common · portfolio · admin
    └── images/works/  작업물 이미지
```

---

## 4. 내 작업물로 바꾸기

### 방법 A — 관리자 CMS (권장)

`admin/` 로그인 → **프로젝트 → 새 작업물 등록**.
대표 이미지 1장과 상세 이미지 여러 장을 올리고, 공개 여부와 표시 순서를 지정합니다.

- 작업 과정 / 결과는 **한 줄에 한 항목**씩 적으면 상세 페이지에서 번호 목록으로 나옵니다.
- 표시 순서는 숫자가 작을수록 앞이며, **1번 작업물이 홈 상단 대표 이미지**가 됩니다.
  가로형 이미지를 쓰는 편이 보기 좋습니다.

### 방법 B — 파일 직접 수정 (DB 없이)

1. `assets/images/works/` 안의 이미지를 교체합니다.
2. `data/seed.php` 의 제목 · 설명 · 이미지 경로를 고칩니다.
3. `bash tools/build-preview.sh` 로 미리보기를 다시 만듭니다.

### 내 정보 바꾸기

`config/site.php` 한 곳만 고치면 헤더 · 푸터 · 메타 태그에 모두 반영됩니다.
(이름, 이메일, 지역, 구직 상태, SNS 링크 — 링크는 비워 두면 표시되지 않습니다.)

---

## 5. 지금 들어 있는 이미지에 대해

`assets/images/works/` 의 29개 SVG는 **실제 작업물이 아니라 자리표시용으로 만든 그래픽**입니다.
비율이 제각각인 실제 포트폴리오(포스터 2:3, 모바일 화면 3:4, 와이드 웹 16:10, 정사각 등)를
가정해 레이아웃이 잘 버티는지 확인하려고 넣었습니다.

**면접에 쓰기 전에 반드시 본인 작업물로 교체하세요.**
같은 이유로 9개 프로젝트의 글도 예시입니다.

---

## 6. 디자인 규칙

| 항목 | 값 |
|---|---|
| 배경 | `#f4f1ea` (따뜻한 종이색) |
| 본문 | `#17140f` / 보조 `#4b453c` / 흐림 `#726a5c` |
| 포인트 | `#b0492b` — 링크, 활성 필터, 번호에만 제한적으로 사용 |
| 본문 서체 | Pretendard |
| 표제 서체 | Instrument Serif (영문 · 숫자 전용) |
| 컨테이너 | 최대 1240px |
| 브레이크포인트 | 모바일 ~767 / 태블릿 768~1023 / 데스크톱 1024~ |

색 토큰은 전부 `assets/css/common.css` 의 `:root` 에 있습니다. 여기만 고치면 전체 톤이 바뀝니다.

### 홈 히어로 배경

홈 첫 화면에만 추상 배경이 깔립니다. 이미지 파일 없이 CSS로만 만든 네 겹입니다.

1. **원호 3개** — 큰 원의 테두리선. 서로 다른 주기(44·52·68초)로 아주 느리게 지나가며 겹치는 자리가 계속 바뀝니다. 형태 자체는 정지된 도형이라 텍스처가 아니라 '구성'으로 읽힙니다.
2. **하프톤 망점 2겹** — 인쇄에서 옅은 회색을 만드는 방식 그대로, 점 크기가 다른 두 겹을 겹쳐 농담을 만듭니다. 굵은 쪽은 포인트 컬러입니다.
3. **잉크 워시** — 팔레트 안의 색이 아주 옅게 번진 자리.
4. **종이 그레인** — 이 레이어가 있어야 화면 그래픽이 아니라 인쇄물 질감으로 읽힙니다.

전체가 글 없는 오른쪽 위 여백에만 몰려 있고, 제목과 본문이 앉는 왼쪽 아래로 갈수록 마스크로 사라지므로 가독성에는 영향이 없습니다.

**세기 조절** — `assets/css/home.css` 의 `.hero { --atmos-strength }` 값 하나만 바꾸면 전체가 함께 조절됩니다.

| 값 | 결과 |
|---|---|
| `0` | 배경 완전히 끔 |
| `0.6` | 기본값 |
| `1` 이상 | 더 뚜렷하게 |

배치를 바꾸고 싶으면 `.atmos__ring--1~3` 의 `top` / `right` / `width` 를 조절하면 됩니다.
`prefers-reduced-motion` 을 켠 사용자에게는 움직임이 자동으로 멈춥니다.

**작업물 이미지는 어떤 경우에도 강제로 자르지 않습니다.** (`object-fit: cover` 미사용)
목록은 자바스크립트 메이슨리로 배치하며, 각 이미지의 원본 가로세로를 미리 읽어
`width` / `height` 속성으로 넣기 때문에 이미지가 늦게 떠도 레이아웃이 밀리지 않습니다.

---

## 7. 보안 처리

- **SQL 인젝션** — 모든 쿼리는 PDO Prepared Statement
- **XSS** — 출력은 전부 `e()` (htmlspecialchars) 통과
- **CSRF** — 문의 폼과 관리자 CRUD 전체에 토큰 검증
- **인증** — `password_hash()` / `password_verify()`, 로그인 시 세션 ID 재발급,
  5회 실패 시 10분 잠금, 관리자 페이지는 로직 실행 전에 로그인 확인
- **업로드** — 확장자가 아니라 `getimagesize()` 로 실제 이미지인지 확인,
  10MB 제한, 파일명 난수화, `uploads/.htaccess` 로 스크립트 실행 차단
- **디렉터리** — `config/` `includes/` `data/` `sql/` 은 웹에서 직접 열 수 없음

배포 시 HTTPS를 적용하고, `php.ini` 에서 `session.cookie_secure = 1`,
`session.cookie_httponly = 1` 을 켜는 것을 권장합니다.

---

## 8. 남은 작업 (선택)

- [ ] 실제 작업물 이미지와 글로 교체
- [ ] `config/site.php` 의 SNS 링크 채우기
- [ ] Open Graph 공유 이미지(`og:image`) 추가 — SVG는 지원되지 않으므로 PNG/JPG 1200×630 권장
- [ ] `.htaccess` 아래쪽 주석을 풀어 `/project/12` 형태의 짧은 주소 사용
- [ ] 도메인 연결 및 HTTPS

---

## 9. 알려진 제약

- 이 코드는 PHP가 설치되지 않은 환경에서 작성되어 **`php -l` 구문 검사와 실제 실행 검증을 거치지 못했습니다.**
  정적 점검(태그 짝, 대체 문법 짝, 괄호 균형)만 통과한 상태이므로,
  XAMPP를 켠 뒤 각 페이지를 한 번씩 열어 확인해 주세요.
- 화면 · 반응형 · 메이슨리 동작은 `preview/` 로 실제 브라우저에서 확인했습니다.
