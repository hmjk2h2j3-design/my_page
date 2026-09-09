# 개인 디자인 포트폴리오 사이트 개발 기획서

## 0. 문서 정보

- 프로젝트명: 개인 디자인 포트폴리오 웹사이트
- 목적: 회사 이력서 제출을 위한 개인 브랜딩 및 디자인 역량 소개
- 핵심 방향: 사람의 성격이나 취향을 소개하기보다 실제 작업물과 디자인 역량을 중심으로 소개
- 개발 방식: 반응형 웹 + 관리자용 포트폴리오 CMS
- 작성 기준: 실제 구현 가능한 웹서비스 수준의 개발 명세

---

## 1. 프로젝트 개요

### 1.1 프로젝트 목적

지원자의 디자인 역량, 프로젝트 경험, 문제 해결 과정과 결과물을 회사 채용 담당자가 빠르게 확인할 수 있는 개인 포트폴리오 사이트를 제작한다.

단순한 이력서 페이지가 아니라 작업물을 지속적으로 추가하고 관리할 수 있는 개인 포트폴리오 아카이브를 목표로 한다.

### 1.2 핵심 가치

1. 작업물 중심
   - 자기소개 문장보다 실제 디자인 결과물을 우선적으로 보여준다.
2. 직관적인 탐색
   - 방문자가 짧은 시간 안에 핵심 작업물을 확인할 수 있도록 구성한다.
3. 전문적인 인상
   - 장식적인 요소를 최소화하고 디자인 결과물 자체가 주인공이 되도록 한다.
4. 지속 가능한 운영
   - 관리자 페이지에서 작업물을 직접 업로드, 수정, 삭제할 수 있게 한다.
5. 다양한 작업물 대응
   - 가로, 세로, 정사각형, 긴 상세페이지 등 서로 다른 이미지 비율을 원본 형태에 가깝게 유지한다.

---

## 2. 디자인 방향

### 2.1 전체 콘셉트

- Minimal
- Modern
- Editorial
- Professional
- Portfolio First
- AI 느낌이 나지 않는 자연스러운 개인 웹사이트

### 2.2 디자인 원칙

- 과도한 AI 스타일의 그래픽, 네온, 글로우, 3D 효과 사용 금지
- 과도한 인터랙션과 애니메이션 사용 금지
- 콘텐츠와 작업물이 가장 먼저 보이도록 구성
- 무채색 기반의 차분한 UI
- 충분한 여백 사용
- 정돈된 타이포그래피
- 이미지의 원본 비율을 최대한 유지
- 모바일에서도 동일한 콘텐츠 우선순위를 유지

### 2.3 컬러 시스템

기본적으로 무채색 계열을 사용한다.

- Primary: #111111
- Secondary: #666666
- Background: #F7F7F5
- Surface: #FFFFFF
- Border: #E5E5E5
- Muted: #A3A3A3

강한 포인트 컬러는 사용하지 않으며 필요할 경우 아주 제한적으로 사용한다.

### 2.4 폰트

- 기본 폰트: Pretendard 또는 Noto Sans KR
- 영문/숫자: Inter 계열 사용 가능
- 제목: 굵고 큰 타이포그래피
- 본문: 가독성 중심의 중간 굵기

### 2.5 아이콘

- Iconify 또는 Lucide Icons 사용 가능
- 선형 아이콘 중심
- 아이콘이 콘텐츠보다 눈에 띄지 않도록 최소한으로 사용

---

## 3. 사이트 구조

### 3.1 공개 사이트맵

```text
HOME
├── About
│   ├── Profile
│   ├── Design Skills
│   └── Tools
│
├── Portfolio
│   ├── All
│   ├── UI/UX
│   ├── Web
│   ├── Graphic
│   └── Branding
│
├── Project Detail
│   ├── Overview
│   ├── Role
│   ├── Process
│   ├── Design Images
│   └── Result
│
└── Contact
    ├── Contact Form
    └── Email / SNS
```

### 3.2 관리자 사이트맵

```text
ADMIN LOGIN
└── DASHBOARD
    ├── Portfolio Management
    │   ├── List
    │   ├── Create
    │   ├── Edit
    │   └── Delete
    │
    ├── Category Management
    │   ├── Create
    │   ├── Edit
    │   └── Delete
    │
    └── Contact Management
        ├── Message List
        └── Message Detail
```

---

## 4. 페이지별 개발 명세

## 4.1 HOME

### 목적
첫 화면에서 지원자의 디자인 전문성과 대표 작업물을 빠르게 인식시키는 페이지.

### 구성

1. Header
   - Logo / 이름
   - About
   - Portfolio
   - Contact
   - 모바일 Menu

2. Hero
   - 이름
   - Design / UIUX 관련 한 줄 소개
   - 대표 작업물 또는 대표 이미지
   - Portfolio 이동 버튼

3. Selected Works
   - 대표 포트폴리오 3~6개
   - 이미지 중심 카드
   - 카테고리 / 프로젝트명 표시

4. Skills Preview
   - UI/UX
   - Web Design
   - Graphic Design
   - Branding 등

5. Contact CTA
   - 채용 및 협업 문의 유도

6. Footer
   - 이름
   - Email
   - SNS
   - Copyright

---

## 4.2 ABOUT

### 목적
지원자의 디자인 역량과 작업 범위를 설명한다.

### 구성

- 간단한 Profile
- 핵심 디자인 역량
- 보유 기술
- 사용 프로그램
- 작업 방식

### 중요 원칙
개인적인 성격, 취향, 취미를 중심으로 작성하지 않는다.
디자인을 어떻게 접근하고 어떤 결과물을 만들 수 있는지를 중심으로 작성한다.

---

## 4.3 PORTFOLIO

### 핵심 기능
작업물을 Pinterest 또는 이미지 아카이브처럼 시각적으로 탐색할 수 있도록 구성한다.

### 카테고리 필터

- ALL
- UI/UX
- WEB
- GRAPHIC
- BRANDING
- 기타 사용자 지정 카테고리

### 갤러리 방식

Masonry Grid 레이아웃을 사용한다.

작업물마다 가로세로 크기가 다를 수 있으므로 동일한 높이 또는 동일 비율로 강제 크롭하지 않는다.

예시:

```text
┌────────┐ ┌──────────────┐ ┌───────┐
│        │ │              │ │       │
│ 세로형 │ │    가로형    │ │ 정사각형│
│        │ │              │ │       │
│        │ └──────────────┘ │       │
└────────┘                  │       │
                            └───────┘
```

### 이미지 처리 원칙

- 원본 비율 유지
- `object-fit: cover`로 강제 크롭하지 않음
- 기본적으로 `width: 100%; height: auto;` 사용
- 썸네일은 서버에서 별도로 생성할 수 있음
- 원본 이미지와 썸네일을 분리 저장

### 카드 정보

- 대표 이미지
- 프로젝트명
- 카테고리
- 제작연도

### 정렬

- 최신순
- 오래된순
- 관리자 지정 순서

---

## 4.4 PROJECT DETAIL

### 목적
작업물 하나를 상세하게 보여주는 페이지.

### 구성

1. Project Title
2. Category
3. Date
4. Overview
5. Project Goal
6. Role
7. Tools
8. Process
9. Design Images
10. Result / Outcome
11. Related Projects

### 이미지 구조

한 프로젝트에 여러 장의 이미지를 등록할 수 있어야 한다.

예:

```text
Project A
├── Thumbnail 1장
├── Detail Image 01
├── Detail Image 02
├── Detail Image 03
├── Detail Image 04
└── Detail Image 05
```

이미지는 등록 순서대로 상세 페이지에 출력한다.

---

## 4.5 CONTACT

### 구성

- 이름
- 이메일
- 문의 내용
- 제출 버튼

### 동작

사용자가 문의 내용을 입력하면 PHP를 통해 서버에서 처리한다.

선택적으로 관리자 DB에 저장하고 이메일 알림을 보낼 수 있다.

---

# 5. 관리자 CMS

## 5.1 관리자 로그인

관리자만 CMS에 접근할 수 있도록 세션 기반 인증을 사용한다.

### 기능

- Login
- Logout
- Session Check
- 로그인 실패 처리
- 비로그인 상태에서 관리자 페이지 접근 차단

비밀번호는 평문으로 저장하지 않고 PHP `password_hash()`로 저장한다.

---

## 5.2 Dashboard

### 표시 정보

- 전체 프로젝트 수
- 공개 프로젝트 수
- 비공개 프로젝트 수
- 전체 문의 수
- 최근 등록 프로젝트

---

## 5.3 Portfolio CRUD

### Create

작업물 등록 시 다음 정보를 입력한다.

- 프로젝트명
- 카테고리
- 제작연도
- 프로젝트 설명
- 프로젝트 목표
- 담당 역할
- 사용 툴
- 작업 과정
- 결과
- 대표 이미지
- 추가 이미지 여러 장
- 공개/비공개
- 정렬 순서

### Read

관리자 리스트에서 등록된 모든 프로젝트를 확인한다.

### Update

기존 프로젝트 내용을 수정할 수 있다.

### Delete

프로젝트 삭제 시 DB 데이터와 연결된 이미지 파일을 함께 삭제할 수 있도록 처리한다.

### Upload

- 이미지 확장자 검증
- MIME Type 검증
- 파일 용량 제한
- 파일명 난수화 또는 UUID 기반 저장
- 악성 파일 업로드 방지

권장 파일 형식:

- JPG / JPEG
- PNG
- WEBP

권장 최대 파일 크기:

- 이미지 1개당 10MB 이하

---

## 5.4 Category Management

관리자가 카테고리를 직접 추가할 수 있게 한다.

예:

```text
UI/UX
WEB
GRAPHIC
BRANDING
EDITORIAL
MOTION
```

---

# 6. 이미지 시스템

## 6.1 가장 중요한 요구사항

작업물마다 이미지 크기가 다를 수 있다는 전제를 시스템 전체에 반영한다.

예:

- 웹사이트 화면: 1920 × 1080
- 모바일 UI: 390 × 844
- 포스터: 1080 × 1530
- 정사각형 콘텐츠: 1080 × 1080
- 긴 상세페이지: 1080 × 5000 이상

모든 이미지를 하나의 고정 비율로 맞추지 않는다.

## 6.2 저장 구조

```text
/uploads/
├── projects/
│   ├── original/
│   └── thumbnail/
```

### DB에는 파일명 또는 경로를 저장한다.

원본 파일은 별도 보관하고 목록 화면에서는 썸네일을 사용해 페이지 로딩 속도를 개선한다.

## 6.3 Lazy Loading

포트폴리오 이미지에는 `loading="lazy"`를 적용한다.

필요한 경우 Intersection Observer를 이용하여 추가 이미지 로딩을 처리한다.

---

# 7. 반응형 웹

## Breakpoint

- Mobile: 0 ~ 767px
- Tablet: 768 ~ 1023px
- Desktop: 1024px 이상

### Mobile

- Masonry 1~2열
- Header 메뉴 축소
- 이미지 중심 UI
- 터치 영역 확대

### Tablet

- Masonry 2~3열

### Desktop

- Masonry 3~4열
- 넓은 여백 활용
- 작업물 집중형 레이아웃

---

# 8. 개발 환경

## 8.1 Frontend

- HTML5
- CSS3
- JavaScript Vanilla JS

프레임워크 없이 기본 HTML/CSS/JavaScript 중심으로 개발한다.

## 8.2 Backend

- PHP 8.x 권장

## 8.3 Database

- MySQL 8.x 권장

## 8.4 Server

개발 단계:

- Apache
- XAMPP
- localhost

배포 단계:

- Apache 기반 PHP Hosting 또는 VPS

## 8.5 개발 도구

- VS Code
- Git
- GitHub
- Chrome DevTools
- Figma
- Photoshop

---

# 9. 데이터베이스 설계

## 9.1 users

| 필드 | 타입 | 설명 |
|---|---|---|
| id | INT PK | 사용자 ID |
| userid | VARCHAR | 관리자 로그인 ID |
| password | VARCHAR | 암호화된 비밀번호 |
| created_at | DATETIME | 생성일 |

## 9.2 projects

| 필드 | 타입 | 설명 |
|---|---|---|
| id | INT PK | 프로젝트 ID |
| title | VARCHAR | 프로젝트명 |
| category_id | INT | 카테고리 ID |
| year | INT | 제작연도 |
| description | TEXT | 프로젝트 설명 |
| goal | TEXT | 프로젝트 목표 |
| role | TEXT | 담당 역할 |
| tools | VARCHAR | 사용 툴 |
| process | TEXT | 작업 과정 |
| result | TEXT | 결과 |
| thumbnail | VARCHAR | 대표 이미지 경로 |
| is_public | TINYINT | 공개 여부 |
| sort_order | INT | 노출 순서 |
| created_at | DATETIME | 생성일 |
| updated_at | DATETIME | 수정일 |

## 9.3 project_images

| 필드 | 타입 | 설명 |
|---|---|---|
| id | INT PK | 이미지 ID |
| project_id | INT | 프로젝트 ID |
| image_path | VARCHAR | 이미지 경로 |
| sort_order | INT | 이미지 순서 |
| created_at | DATETIME | 생성일 |

## 9.4 categories

| 필드 | 타입 | 설명 |
|---|---|---|
| id | INT PK | 카테고리 ID |
| name | VARCHAR | 카테고리명 |
| sort_order | INT | 표시 순서 |

## 9.5 contacts

| 필드 | 타입 | 설명 |
|---|---|---|
| id | INT PK | 문의 ID |
| name | VARCHAR | 문의자명 |
| email | VARCHAR | 이메일 |
| message | TEXT | 문의 내용 |
| created_at | DATETIME | 문의일 |

---

# 10. API 및 외부 라이브러리

이 사이트는 외부 API 의존도를 최소화한다.

## 필수/권장 사용

### Google Fonts

용도:
- 웹폰트 제공
- 한글/영문 타이포그래피 적용

### Iconify API 또는 CDN

용도:
- UI 아이콘
- 메뉴 아이콘
- 외부 링크 아이콘

### 선택 사항: EmailJS

Contact 문의를 별도 메일 서비스로 직접 발송해야 할 경우 사용한다.

단, PHP 메일 처리 또는 SMTP 서버를 사용할 수 있다면 EmailJS는 사용하지 않아도 된다.

### 선택 사항: AOS 또는 자체 JavaScript 애니메이션

과도한 애니메이션을 피하고 필요한 영역에서만 사용한다.

---

# 11. API 사용 원칙

외부 API를 많이 사용하지 않는다.

핵심 데이터는 PHP + MySQL로 관리한다.

```text
브라우저
   ↓
HTML / CSS / Vanilla JS
   ↓
PHP
   ↓
MySQL
```

외부 API는 폰트, 아이콘, 선택적인 이메일 발송 등 꼭 필요한 부분에만 제한한다.

---

# 12. 보안 요구사항

실제 배포를 고려하여 다음 보안 처리를 적용한다.

### SQL Injection 방지

- PDO 또는 MySQLi Prepared Statement 사용

### XSS 방지

- 출력 데이터 HTML Escape
- 입력값 검증

### CSRF 방지

관리자 CRUD 및 문의 폼에 CSRF Token 적용을 권장한다.

### 파일 업로드 보안

- 확장자 검증
- MIME 검사
- 파일 크기 제한
- 서버 실행 가능 확장자 차단
- 업로드 파일명 난수화

### 인증 보안

- `password_hash()`
- `password_verify()`
- Session Regeneration
- 로그아웃 처리

---

# 13. URL 구조 예시

```text
/
/index.php
/about.php
/portfolio.php
/project.php?id=1
/contact.php

/admin/
/admin/login.php
/admin/index.php
/admin/projects.php
/admin/project-create.php
/admin/project-edit.php
/admin/categories.php
/admin/contacts.php
```

Apache RewriteRule을 사용할 경우 다음처럼 구성할 수 있다.

```text
/portfolio
/project/12
/contact
/admin
/admin/projects
```

---

# 14. 권장 폴더 구조

```text
portfolio/
├── index.php
├── about.php
├── portfolio.php
├── project.php
├── contact.php
│
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── index.php
│   ├── projects.php
│   ├── project-create.php
│   ├── project-edit.php
│   ├── project-delete.php
│   ├── categories.php
│   └── contacts.php
│
├── assets/
│   ├── css/
│   │   ├── reset.css
│   │   ├── common.css
│   │   ├── layout.css
│   │   ├── home.css
│   │   ├── portfolio.css
│   │   └── admin.css
│   │
│   ├── js/
│   │   ├── common.js
│   │   ├── portfolio.js
│   │   └── admin.js
│   │
│   └── images/
│
├── uploads/
│   └── projects/
│       ├── original/
│       └── thumbnail/
│
├── config/
│   └── database.php
│
└── includes/
    ├── header.php
    ├── footer.php
    ├── auth.php
    └── functions.php
```

---

# 15. 핵심 UX 시나리오

## 방문자 시나리오

```text
HOME 방문
 ↓
대표 작업물 확인
 ↓
Portfolio 이동
 ↓
카테고리 필터
 ↓
작업물 선택
 ↓
Project Detail 확인
 ↓
Contact 문의
```

## 관리자 시나리오

```text
Admin Login
 ↓
Dashboard
 ↓
Portfolio 등록
 ↓
대표 이미지 업로드
 ↓
추가 이미지 여러 장 업로드
 ↓
카테고리 / 설명 / 역할 / 툴 입력
 ↓
공개 설정
 ↓
등록
 ↓
사이트 Portfolio에 즉시 반영
```

---

# 16. 성능 최적화

- 이미지 Lazy Loading
- WebP 우선 사용
- 썸네일 생성
- CSS/JS 파일 최소화
- 불필요한 외부 라이브러리 최소화
- 이미지 원본은 상세 페이지에서만 필요할 경우 로드
- 관리자 업로드 시 이미지 크기와 용량 검증

---

# 17. SEO 기본 적용

모든 공개 페이지에 다음 요소를 적용한다.

- `<title>`
- Meta Description
- Open Graph 기본 태그
- Semantic HTML
- 이미지 `alt` 속성
- 적절한 Heading 구조

예:

```html
<title>김민겸 | Portfolio</title>
<meta name="description" content="김민겸의 디자인 포트폴리오 사이트입니다.">
```

---

# 18. 접근성

- 충분한 텍스트 대비
- 키보드 접근 가능
- 버튼에 명확한 Label 제공
- 이미지에 적절한 Alt Text 제공
- 폼 요소에 Label 연결
- 지나치게 빠른 애니메이션 사용 금지

---

# 19. AI 코딩 개발 원칙

AI를 활용해 개발하더라도 결과물이 AI가 자동 생성한 템플릿처럼 보이지 않도록 한다.

### 반드시 지킬 것

- 과도한 카드 UI 사용 금지
- 의미 없는 그라데이션 금지
- 의미 없는 3D 그래픽 금지
- 과도한 글래스모피즘 금지
- 반복적인 AI 스타일 문구 금지
- 필요 이상의 애니메이션 금지
- 실제 디자인 작업물이 메인 콘텐츠가 되도록 구성
- 코드도 페이지별 역할에 맞게 단순하고 유지보수 가능하게 작성

### 디자인 목표

"AI가 만든 포트폴리오"가 아니라
"디자이너가 직접 설계한 개인 포트폴리오 사이트"처럼 보여야 한다.

---

# 20. 개발 우선순위

## 1단계 — 기본 UI

- 공통 Header / Footer
- Home
- About
- Portfolio
- Project Detail
- Contact
- 반응형 CSS

## 2단계 — 데이터 연결

- MySQL DB 구축
- PHP DB 연결
- 프로젝트 조회
- 카테고리 조회
- 상세 페이지 동적 출력

## 3단계 — 관리자 CMS

- 로그인
- Dashboard
- 프로젝트 등록
- 프로젝트 수정
- 프로젝트 삭제
- 이미지 업로드
- 다중 이미지 업로드
- 카테고리 관리

## 4단계 — 품질 개선

- 이미지 최적화
- Lazy Loading
- 보안 처리
- SEO
- 접근성
- 모바일 테스트
- 브라우저 호환성 테스트

## 5단계 — 배포

- 서버 업로드
- DB 이전
- 환경변수/설정 분리
- 도메인 연결
- HTTPS 적용
- 최종 테스트

---

# 21. 최종 구현 목표

이 프로젝트의 최종 결과물은 단순한 정적 이력서 페이지가 아니다.

다음 조건을 만족하는 개인 디자인 포트폴리오 플랫폼으로 구현한다.

```text
[ 회사 제출용 개인 소개 ]
          +
[ 디자인 포트폴리오 ]
          +
[ 개인 작업물 아카이브 ]
          +
[ 관리자 CMS ]
```

특히 포트폴리오 영역은 작업물의 크기와 비율이 제각각이라는 실제 디자인 작업 환경을 기준으로 설계한다.

작업물을 하나의 비율로 강제하지 않고, Masonry Grid와 원본 비율 유지 방식을 사용하여 Pinterest와 유사한 자연스러운 작업물 탐색 경험을 제공한다.

관리자는 언제든지 새 작업물을 업로드하고, 여러 이미지를 한 프로젝트에 연결하며, 카테고리와 공개 여부를 수정할 수 있어야 한다.

핵심은 기능을 많이 넣는 것이 아니라, **디자인 결과물을 가장 선명하게 보여주는 것**이다.
