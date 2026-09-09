-- ============================================================
--  개인 포트폴리오 사이트 — 데이터베이스 스키마
--
--  사용법 (XAMPP 기준)
--    1) phpMyAdmin 접속 → 좌측 상단 [SQL] 탭
--    2) 이 파일 전체를 붙여넣고 실행
--    3) config/database.php 의 접속 정보 확인
--    4) 관리자 계정 만들기 → 파일 맨 아래 안내 참고
--
--  문자셋은 이모지까지 안전한 utf8mb4 를 씁니다.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `portfolio`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `portfolio`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `project_images`;
DROP TABLE IF EXISTS `projects`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 관리자 계정
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid`     VARCHAR(60)  NOT NULL,
  `password`   VARCHAR(255) NOT NULL COMMENT 'password_hash() 결과. 평문 저장 금지',
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 카테고리
-- ------------------------------------------------------------
CREATE TABLE `categories` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(40)  NOT NULL,
  `slug`       VARCHAR(40)  NOT NULL COMMENT 'URL 및 필터에 쓰는 영문 키',
  `sort_order` INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 프로젝트
-- ------------------------------------------------------------
CREATE TABLE `projects` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(120) NOT NULL,
  `subtitle`    VARCHAR(200)     NULL COMMENT '목록 카드에 나오는 한 줄 설명',
  `category_id` INT UNSIGNED     NULL,
  `year`        SMALLINT     NOT NULL,
  `description` TEXT             NULL COMMENT '개요',
  `goal`        TEXT             NULL COMMENT '프로젝트 목표',
  `role`        TEXT             NULL COMMENT '담당 역할',
  `tools`       VARCHAR(200)     NULL COMMENT '사용 툴',
  `process`     TEXT             NULL COMMENT '작업 과정 — 한 줄에 한 단계',
  `result`      TEXT             NULL COMMENT '결과 — 한 줄에 한 항목',
  `thumbnail`   VARCHAR(255)     NULL COMMENT '대표 이미지 경로',
  `is_public`   TINYINT(1)   NOT NULL DEFAULT 1,
  `sort_order`  INT          NOT NULL DEFAULT 0 COMMENT '작을수록 앞',
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_projects_public_order` (`is_public`, `sort_order`),
  KEY `idx_projects_category` (`category_id`),
  CONSTRAINT `fk_projects_category` FOREIGN KEY (`category_id`)
    REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 프로젝트 상세 이미지 (한 프로젝트에 여러 장)
-- ------------------------------------------------------------
CREATE TABLE `project_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `sort_order` INT          NOT NULL DEFAULT 0,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_images_project` (`project_id`, `sort_order`),
  CONSTRAINT `fk_images_project` FOREIGN KEY (`project_id`)
    REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 문의
-- ------------------------------------------------------------
CREATE TABLE `contacts` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(60)  NOT NULL,
  `email`      VARCHAR(180) NOT NULL,
  `message`    TEXT         NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contacts_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  기본 데이터
-- ============================================================

INSERT INTO `categories` (`id`, `name`, `slug`, `sort_order`) VALUES
(1, 'UI/UX',    'uiux',     1),
(2, 'WEB',      'web',      2),
(3, 'GRAPHIC',  'graphic',  3),
(4, 'BRANDING', 'branding', 4);

INSERT INTO projects
  (id, title, subtitle, category_id, year, description, goal, `role`, tools, process, result, thumbnail, is_public, sort_order, created_at)
VALUES
(1, 'NOTED', '읽다 만 책까지 기록하는 독서 앱', 1, 2025, '시중의 독서 기록 앱은 대부분 "다 읽은 책"을 등록하는 구조입니다. 그래서 읽다 만 책, 마음에 든 문장 한 줄, 다시 펼쳐볼 페이지 번호는 갈 곳이 없습니다.\n기록의 최소 단위를 ''책''에서 ''문장''으로 내리면 어떻게 되는지 확인하려고 시작한 개인 프로젝트입니다.', '한 번 기록하는 데 걸리는 시간을 15초 아래로 줄인다. 책을 덮기 전에, 앱을 켠 자리에서 끝나야 한다.', '사용자 리서치 / 정보구조 설계 / 화면 설계 / UI 디자인 / 프로토타입', 'Figma, Photoshop, Notion', '독서 앱을 3개월 이상 써 본 20–30대 8명 인터뷰. 기록이 끊기는 지점을 3가지로 정리했다.\n등록 절차가 "책 검색 → 상태 선택 → 별점 → 메모"로 길다는 점이 공통 불만이었다.\n정보구조를 뒤집어 문장을 먼저 입력하고 책은 나중에 연결하도록 재편했다.\n로우파이 화면 12장으로 흐름을 먼저 검증하고, 그다음 UI를 올렸다.\n프로토타입 사용성 테스트 2회(각 5명), 탭 구조와 입력 순서를 한 번씩 수정했다.', '기록 완료까지 평균 41초에서 13초로 줄었다.\n2차 테스트 참가자 5명 중 4명이 "문장을 먼저 적는 순서가 더 자연스럽다"고 답했다.\n서체는 본문 가독성을 우선해 16px / 행간 1.7로 고정하고, 강조는 색이 아니라 굵기로만 처리했다.', 'assets/images/works/noted-01.svg', 1, 3, '2025-08-14 10:00:00'),
(2, '온기 ONGI', '동네 로스터리 카페 브랜드 아이덴티티', 4, 2025, '5년 된 동네 로스터리의 리브랜딩 작업입니다. 로고는 있었지만 원두 봉투, 메뉴판, 스티커가 각각 다른 서체를 쓰고 있었습니다.\n"따뜻하게"라는 형용사 대신, 매장에서 실제로 반복되는 장면 하나를 상징으로 잡았습니다. 잔에 커피를 절반 정도 따라 건네는 순간입니다.', '사장님이 새 인쇄물을 만들 때 디자이너 없이도 규칙을 지킬 수 있는 최소한의 가이드를 만든다.', '브랜드 리서치 / 로고타입 / 심볼 / 컬러·타입 시스템 / 패키지 적용', 'Illustrator, Photoshop, Figma', '2주간 매장 관찰과 사장님 인터뷰. 손님의 70%가 테이크아웃이라는 점을 확인했다.\n심볼은 반쯤 채운 원 하나로 정리했다. 컵, 원두, 해가 뜨는 모양으로 동시에 읽힌다.\n로고타입은 세리프로 잡되 자간을 넓혀 작은 크기에서도 뭉치지 않게 했다.\n컬러는 4색으로 제한했다. 인쇄 단가를 고려해 별색 1도 + 검정으로도 성립하도록 검증했다.\n원두 봉투, 컵 슬리브, 명함, 메뉴판에 실제로 얹어 보고 크기별 최소 사용 규격을 정했다.', 'A4 4장짜리 가이드로 정리했다. 로고 최소 크기, 여백, 금지 사용 예, 컬러 코드까지 포함했다.\n적용 3개월 뒤 사장님이 직접 만든 시즌 포스터가 가이드 안에서 나왔다. 그게 이 작업의 실제 결과라고 생각한다.', 'assets/images/works/ongi-01.svg', 1, 4, '2025-06-02 10:00:00'),
(3, '시립미술관 웹 리뉴얼', '전시 정보를 먼저 보여주는 구조로', 2, 2024, '공공 미술관 웹사이트를 개인 과제로 다시 설계했습니다. 원본 사이트는 첫 화면의 60% 이상을 공지사항과 배너가 차지하고 있었고, 정작 "지금 무슨 전시를 하는지"는 스크롤을 두 번 내려야 나왔습니다.', '첫 화면에서 현재 전시 · 기간 · 관람료 · 휴관일 네 가지를 스크롤 없이 확인할 수 있게 한다.', '현황 분석 / 정보구조 재설계 / 반응형 화면 설계 / UI 디자인', 'Figma, Photoshop', '기존 사이트의 메뉴 47개를 카드소팅으로 다시 묶어 대분류 5개로 줄였다.\n방문 목적을 "전시 확인 / 예약 / 오시는 길" 세 가지로 좁히고 나머지는 하위로 내렸다.\n12칼럼 그리드를 기준으로 데스크톱·태블릿·모바일 세 벌을 동시에 그렸다.\n전시 목록은 포스터 비율이 제각각이라 카드 높이를 고정하지 않고 원본 비율을 유지했다.\n본문 대비는 WCAG AA(4.5:1)를 기준으로 전 화면을 다시 검수했다.', '첫 화면 진입 후 현재 전시 확인까지 필요한 스크롤이 2회에서 0회로 줄었다.\n메뉴 깊이는 최대 4단계에서 2단계로 줄었다.\n모바일에서도 데스크톱과 같은 우선순위를 유지하도록 콘텐츠 순서를 고정했다.', 'assets/images/works/sema-01.svg', 1, 1, '2024-11-20 10:00:00'),
(4, '활자의 무게', '타이포그래피 포스터 4종', 3, 2024, '"같은 문장을 서체와 크기만 바꿔서 얼마나 다르게 읽히게 할 수 있는가"를 주제로 만든 포스터 연작입니다.\n네 장 모두 문구는 같고, 조판만 다릅니다.', '장식 없이 활자와 여백만으로 네 가지 다른 온도를 만든다.', '컨셉 / 조판 / 인쇄 감리', 'InDesign, Illustrator', '같은 문장을 세리프·산세리프·모노 세 계열로 각각 조판해 비교했다.\n포스터 판형은 B2로 고정하고, 여백 비율만 1:1.2 / 1:1.6 / 1:2 로 바꿔 실험했다.\n가장 큰 글자와 가장 작은 글자의 크기 차이를 8배 이상 벌렸을 때 시선 이동이 가장 명확했다.\n리소 인쇄 2도로 출력해 실제 종이 위 대비를 확인하고 잉크 농도를 두 번 조정했다.', '학과 전시에 4종 세트로 출품했다.\n인쇄 결과 어두운 배경의 세리프 조판이 가장 멀리서도 읽혔다. 화면에서 판단한 것과 반대였다.', 'assets/images/works/type-01.svg', 1, 5, '2024-09-05 10:00:00'),
(5, 'FRAME', '비율이 제각각인 사진을 위한 아카이브', 2, 2025, '필름 사진을 올리는 아카이브 서비스의 화면 설계입니다. 6×6 정사각, 3:2, 파노라마가 한 화면에 섞이는 것이 전제 조건이었습니다.\n모든 사진을 같은 비율로 자르는 순간 사진가가 정한 프레임이 사라진다는 점에서 출발했습니다.', '어떤 비율의 사진도 자르지 않고, 그러면서도 목록이 지저분해 보이지 않게 한다.', '레이아웃 설계 / UI 디자인 / 프론트엔드 프로토타입', 'Figma, HTML/CSS, JavaScript', '그리드 후보 3안(고정 카드 / 가로 스트립 / 메이슨리)을 실제 사진 120장으로 각각 조판해 비교했다.\n메이슨리가 비율 유지에는 유리했지만 열이 늘어날수록 시선 흐름이 끊겼다.\n열 개수를 최대 4열로 제한하고 열 사이 간격을 넓혀 세로 흐름을 살렸다.\n사진마다 가로세로 값을 미리 읽어 자리를 먼저 잡도록 해서 로딩 중 밀림을 없앴다.', '사진 200장 기준 초기 로딩 후 레이아웃이 흔들리는 현상(CLS)을 0에 가깝게 유지했다.\n이 프로젝트에서 만든 메이슨리 방식을 지금 이 포트폴리오 사이트에도 그대로 쓰고 있다.', 'assets/images/works/frame-01.svg', 1, 2, '2025-03-11 10:00:00'),
(6, '하루의 온도', '문장 대신 색으로 남기는 감정 기록', 1, 2024, '감정 기록 앱은 대부분 "오늘 기분을 골라주세요"로 시작합니다. 그런데 감정에 이름을 붙이는 일 자체가 부담이라는 이야기를 인터뷰에서 반복해서 들었습니다.\n그래서 이름 대신 색 온도로 고르게 했습니다.', '하루 기록을 3초 안에 끝내고, 한 달치를 한 화면에서 색으로 되돌아볼 수 있게 한다.', '리서치 / 컨셉 / 화면 설계 / UI 디자인 / 컬러 시스템', 'Figma, Photoshop', '감정 기록 앱 경험자 6명 인터뷰. "기분 이름 고르기가 제일 어렵다"는 응답이 4명이었다.\n차가운 색–따뜻한 색 12단계 스케일을 만들고, 좌우 슬라이드 한 번으로 선택이 끝나게 했다.\n색만으로는 나중에 왜 그랬는지 기억나지 않는 문제가 있어, 선택 후 한 줄 메모를 선택 입력으로 붙였다.\n월간 화면은 색 격자 하나로만 구성했다. 숫자와 그래프를 모두 뺐다.\n색약 사용자를 고려해 명도 차이만으로도 12단계가 구분되는지 흑백 변환으로 검증했다.', '프로토타입 테스트에서 하루 기록 평균 소요 시간 4.2초.\n"한 달을 한눈에 보는 화면이 가장 좋다"는 응답이 6명 중 5명이었다.', 'assets/images/works/haru-01.svg', 1, 7, '2024-07-19 10:00:00'),
(7, '무해상점', '제로웨이스트 편집숍 패키지', 4, 2023, '포장을 줄이는 가게의 포장을 디자인하는 일이었습니다. 인쇄 도수, 코팅, 접착제까지 디자인 결정에 포함되는 프로젝트였습니다.', '후가공 없이 1도 인쇄만으로 매대에서 구분되는 패키지를 만든다.', '패키지 구조 / 그래픽 / 라벨 시스템', 'Illustrator, Photoshop', '코팅 없는 크라프트지 위에서 색이 어떻게 죽는지 먼저 인쇄 테스트를 했다.\n녹색 계열 3종을 실제 종이에 뽑아 보고, 화면 값보다 명도를 15% 올려 보정했다.\n품목이 40종이 넘어 라벨을 하나씩 그리지 않고 규격 3종 + 색 4종 조합으로 시스템화했다.\n접착 라벨 대신 종이 띠지를 써서 분리배출 시 뜯어낼 필요가 없게 했다.', '라벨 12종 조합으로 품목 40여 종을 모두 커버했다.\n1도 인쇄 기준으로 기존 대비 인쇄 단가를 약 30% 줄였다.', 'assets/images/works/muhae-01.svg', 1, 8, '2023-10-08 10:00:00'),
(8, 'Seoul Type Week', '타이포그래피 주간 행사 그래픽', 3, 2023, '가상의 타이포그래피 행사를 위한 아이덴티티와 홍보물 세트입니다. 포스터 한 장이 아니라, 배너·프로그램북·현수막까지 같은 규칙으로 확장되는지를 확인하는 것이 과제였습니다.', '서로 다른 판형 6종에서 같은 인상을 유지하는 그래픽 규칙을 만든다.', '아이덴티티 / 포스터 / 프로그램북 / 적용물', 'InDesign, Illustrator', '행사명 두 단어를 위아래로 겹쳐 쌓는 것을 유일한 규칙으로 정했다.\n판형이 바뀌어도 이 겹침 각도와 여백 비율만 지키면 같은 인상이 유지되는지 6종에 적용해 검증했다.\n프로그램북은 2단 그리드로 잡고 강연 시간표를 왼쪽 고정, 설명을 오른쪽에 배치했다.\n현수막처럼 멀리서 보는 매체는 자간을 좁히고 굵기를 한 단계 올려 별도 규격을 만들었다.', '포스터 2종, 배너 2종, 프로그램북, 현수막까지 총 6종을 하나의 규칙으로 완성했다.\n멀리서 보는 매체용 별도 규격을 만든 것이 이 작업에서 가장 실용적인 결정이었다.', 'assets/images/works/week-01.svg', 1, 9, '2023-05-16 10:00:00'),
(9, '커머스 관리자 콘솔', '하루 300건을 처리하는 사람을 위한 화면', 1, 2025, '쇼핑몰 운영자가 하루 종일 보는 관리자 화면을 다시 설계했습니다. 예쁘게 만드는 것보다, 같은 동작을 300번 반복해도 지치지 않는 것이 목표였습니다.', '주문 1건 처리에 필요한 클릭 수를 줄이고, 목록에서 페이지를 떠나지 않고 처리를 끝낸다.', '운영자 인터뷰 / 화면 설계 / 컴포넌트 정의 / UI 디자인', 'Figma', '쇼핑몰 운영자 3명의 실제 작업을 각 2시간씩 옆에서 관찰했다.\n가장 많이 반복되는 동작은 "주문 확인 → 송장 입력 → 발송 처리" 세 단계였다.\n목록 행을 펼쳐서 그 자리에서 처리하도록 바꿔 상세 페이지 진입을 없앴다.\n표는 밀도를 우선해 행 높이를 44px로 낮추고, 대신 행 구분선을 흐리게 해 눈의 피로를 줄였다.\n버튼·배지·표 셀 등 반복 요소 24개를 컴포넌트로 정의해 개발 전달용 문서로 정리했다.', '주문 1건 처리 클릭 수가 7회에서 3회로 줄었다.\n운영자 3명 모두 "목록에서 바로 끝나는 게 제일 낫다"고 답했다.\n컴포넌트 24종 정의서를 함께 넘겨 개발 단계에서 되묻는 일을 줄였다.', 'assets/images/works/admin-01.svg', 1, 6, '2025-01-22 10:00:00');

INSERT INTO project_images (project_id, image_path, sort_order) VALUES
(1, 'assets/images/works/noted-01.svg', 1),
(1, 'assets/images/works/noted-02.svg', 2),
(1, 'assets/images/works/noted-03.svg', 3),
(1, 'assets/images/works/noted-04.svg', 4),
(2, 'assets/images/works/ongi-01.svg', 1),
(2, 'assets/images/works/ongi-02.svg', 2),
(2, 'assets/images/works/ongi-03.svg', 3),
(2, 'assets/images/works/ongi-04.svg', 4),
(3, 'assets/images/works/sema-01.svg', 1),
(3, 'assets/images/works/sema-02.svg', 2),
(3, 'assets/images/works/sema-03.svg', 3),
(4, 'assets/images/works/type-01.svg', 1),
(4, 'assets/images/works/type-02.svg', 2),
(4, 'assets/images/works/type-03.svg', 3),
(5, 'assets/images/works/frame-01.svg', 1),
(5, 'assets/images/works/frame-02.svg', 2),
(5, 'assets/images/works/frame-03.svg', 3),
(6, 'assets/images/works/haru-01.svg', 1),
(6, 'assets/images/works/haru-02.svg', 2),
(6, 'assets/images/works/haru-03.svg', 3),
(7, 'assets/images/works/muhae-01.svg', 1),
(7, 'assets/images/works/muhae-02.svg', 2),
(7, 'assets/images/works/muhae-03.svg', 3),
(8, 'assets/images/works/week-01.svg', 1),
(8, 'assets/images/works/week-02.svg', 2),
(8, 'assets/images/works/week-03.svg', 3),
(9, 'assets/images/works/admin-01.svg', 1),
(9, 'assets/images/works/admin-02.svg', 2),
(9, 'assets/images/works/admin-03.svg', 3);

-- ============================================================
--  관리자 계정 만들기
-- ============================================================
--
--  비밀번호는 절대 평문으로 넣지 않습니다.
--  아래 명령으로 해시를 만든 뒤, 그 값을 INSERT 문에 붙여넣으세요.
--
--    php tools/make-hash.php 원하는비밀번호
--
--  (XAMPP를 쓴다면 php.exe 는 보통 C:\xampp\php\php.exe 에 있습니다.)
--
--  출력된 $2y$... 문자열을 아래 자리에 넣고 실행하면 됩니다.
--
--  INSERT INTO `users` (`userid`, `password`) VALUES
--  ('admin', '여기에_해시_붙여넣기');
--
