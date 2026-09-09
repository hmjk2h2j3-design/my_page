<?php
/**
 * DB가 없을 때 공개 페이지가 사용하는 기본 데이터.
 *
 * MySQL을 연결하면 이 파일은 더 이상 사용되지 않습니다.
 * sql/schema.sql 에 같은 내용이 INSERT 문으로 들어 있습니다.
 *
 * 실제 작업물로 교체할 때는
 *   1) assets/images/works/ 안의 이미지를 바꾸고
 *   2) 아래 'thumbnail' / 'images' 경로와 글을 수정하면 됩니다.
 */

$categories = [
    ['id' => 1, 'name' => 'UI/UX',    'slug' => 'uiux',     'sort_order' => 1],
    ['id' => 2, 'name' => 'WEB',      'slug' => 'web',      'sort_order' => 2],
    ['id' => 3, 'name' => 'GRAPHIC',  'slug' => 'graphic',  'sort_order' => 3],
    ['id' => 4, 'name' => 'BRANDING', 'slug' => 'branding', 'sort_order' => 4],
];

$projects = [
    [
        'id' => 1,
        'title' => 'NOTED',
        'subtitle' => '읽다 만 책까지 기록하는 독서 앱',
        'category_id' => 1,
        'year' => 2025,
        'sort_order' => 3,
        'is_public' => 1,
        'created_at' => '2025-08-14 10:00:00',
        'thumbnail' => 'assets/images/works/noted-01.svg',
        'description' => <<<'TXT'
시중의 독서 기록 앱은 대부분 "다 읽은 책"을 등록하는 구조입니다. 그래서 읽다 만 책, 마음에 든 문장 한 줄, 다시 펼쳐볼 페이지 번호는 갈 곳이 없습니다.
기록의 최소 단위를 '책'에서 '문장'으로 내리면 어떻게 되는지 확인하려고 시작한 개인 프로젝트입니다.
TXT,
        'goal' => <<<'TXT'
한 번 기록하는 데 걸리는 시간을 15초 아래로 줄인다. 책을 덮기 전에, 앱을 켠 자리에서 끝나야 한다.
TXT,
        'role' => '사용자 리서치 / 정보구조 설계 / 화면 설계 / UI 디자인 / 프로토타입',
        'tools' => 'Figma, Photoshop, Notion',
        'process' => <<<'TXT'
독서 앱을 3개월 이상 써 본 20–30대 8명 인터뷰. 기록이 끊기는 지점을 3가지로 정리했다.
등록 절차가 "책 검색 → 상태 선택 → 별점 → 메모"로 길다는 점이 공통 불만이었다.
정보구조를 뒤집어 문장을 먼저 입력하고 책은 나중에 연결하도록 재편했다.
로우파이 화면 12장으로 흐름을 먼저 검증하고, 그다음 UI를 올렸다.
프로토타입 사용성 테스트 2회(각 5명), 탭 구조와 입력 순서를 한 번씩 수정했다.
TXT,
        'result' => <<<'TXT'
기록 완료까지 평균 41초에서 13초로 줄었다.
2차 테스트 참가자 5명 중 4명이 "문장을 먼저 적는 순서가 더 자연스럽다"고 답했다.
서체는 본문 가독성을 우선해 16px / 행간 1.7로 고정하고, 강조는 색이 아니라 굵기로만 처리했다.
TXT,
        'images' => [
            'assets/images/works/noted-01.svg',
            'assets/images/works/noted-02.svg',
            'assets/images/works/noted-03.svg',
            'assets/images/works/noted-04.svg',
        ],
    ],
    [
        'id' => 2,
        'title' => '온기 ONGI',
        'subtitle' => '동네 로스터리 카페 브랜드 아이덴티티',
        'category_id' => 4,
        'year' => 2025,
        'sort_order' => 4,
        'is_public' => 1,
        'created_at' => '2025-06-02 10:00:00',
        'thumbnail' => 'assets/images/works/ongi-01.svg',
        'description' => <<<'TXT'
5년 된 동네 로스터리의 리브랜딩 작업입니다. 로고는 있었지만 원두 봉투, 메뉴판, 스티커가 각각 다른 서체를 쓰고 있었습니다.
"따뜻하게"라는 형용사 대신, 매장에서 실제로 반복되는 장면 하나를 상징으로 잡았습니다. 잔에 커피를 절반 정도 따라 건네는 순간입니다.
TXT,
        'goal' => <<<'TXT'
사장님이 새 인쇄물을 만들 때 디자이너 없이도 규칙을 지킬 수 있는 최소한의 가이드를 만든다.
TXT,
        'role' => '브랜드 리서치 / 로고타입 / 심볼 / 컬러·타입 시스템 / 패키지 적용',
        'tools' => 'Illustrator, Photoshop, Figma',
        'process' => <<<'TXT'
2주간 매장 관찰과 사장님 인터뷰. 손님의 70%가 테이크아웃이라는 점을 확인했다.
심볼은 반쯤 채운 원 하나로 정리했다. 컵, 원두, 해가 뜨는 모양으로 동시에 읽힌다.
로고타입은 세리프로 잡되 자간을 넓혀 작은 크기에서도 뭉치지 않게 했다.
컬러는 4색으로 제한했다. 인쇄 단가를 고려해 별색 1도 + 검정으로도 성립하도록 검증했다.
원두 봉투, 컵 슬리브, 명함, 메뉴판에 실제로 얹어 보고 크기별 최소 사용 규격을 정했다.
TXT,
        'result' => <<<'TXT'
A4 4장짜리 가이드로 정리했다. 로고 최소 크기, 여백, 금지 사용 예, 컬러 코드까지 포함했다.
적용 3개월 뒤 사장님이 직접 만든 시즌 포스터가 가이드 안에서 나왔다. 그게 이 작업의 실제 결과라고 생각한다.
TXT,
        'images' => [
            'assets/images/works/ongi-01.svg',
            'assets/images/works/ongi-02.svg',
            'assets/images/works/ongi-03.svg',
            'assets/images/works/ongi-04.svg',
        ],
    ],
    [
        'id' => 3,
        'title' => '시립미술관 웹 리뉴얼',
        'subtitle' => '전시 정보를 먼저 보여주는 구조로',
        'category_id' => 2,
        'year' => 2024,
        'sort_order' => 1,
        'is_public' => 1,
        'created_at' => '2024-11-20 10:00:00',
        'thumbnail' => 'assets/images/works/sema-01.svg',
        'description' => <<<'TXT'
공공 미술관 웹사이트를 개인 과제로 다시 설계했습니다. 원본 사이트는 첫 화면의 60% 이상을 공지사항과 배너가 차지하고 있었고, 정작 "지금 무슨 전시를 하는지"는 스크롤을 두 번 내려야 나왔습니다.
TXT,
        'goal' => <<<'TXT'
첫 화면에서 현재 전시 · 기간 · 관람료 · 휴관일 네 가지를 스크롤 없이 확인할 수 있게 한다.
TXT,
        'role' => '현황 분석 / 정보구조 재설계 / 반응형 화면 설계 / UI 디자인',
        'tools' => 'Figma, Photoshop',
        'process' => <<<'TXT'
기존 사이트의 메뉴 47개를 카드소팅으로 다시 묶어 대분류 5개로 줄였다.
방문 목적을 "전시 확인 / 예약 / 오시는 길" 세 가지로 좁히고 나머지는 하위로 내렸다.
12칼럼 그리드를 기준으로 데스크톱·태블릿·모바일 세 벌을 동시에 그렸다.
전시 목록은 포스터 비율이 제각각이라 카드 높이를 고정하지 않고 원본 비율을 유지했다.
본문 대비는 WCAG AA(4.5:1)를 기준으로 전 화면을 다시 검수했다.
TXT,
        'result' => <<<'TXT'
첫 화면 진입 후 현재 전시 확인까지 필요한 스크롤이 2회에서 0회로 줄었다.
메뉴 깊이는 최대 4단계에서 2단계로 줄었다.
모바일에서도 데스크톱과 같은 우선순위를 유지하도록 콘텐츠 순서를 고정했다.
TXT,
        'images' => [
            'assets/images/works/sema-01.svg',
            'assets/images/works/sema-02.svg',
            'assets/images/works/sema-03.svg',
        ],
    ],
    [
        'id' => 4,
        'title' => '활자의 무게',
        'subtitle' => '타이포그래피 포스터 4종',
        'category_id' => 3,
        'year' => 2024,
        'sort_order' => 5,
        'is_public' => 1,
        'created_at' => '2024-09-05 10:00:00',
        'thumbnail' => 'assets/images/works/type-01.svg',
        'description' => <<<'TXT'
"같은 문장을 서체와 크기만 바꿔서 얼마나 다르게 읽히게 할 수 있는가"를 주제로 만든 포스터 연작입니다.
네 장 모두 문구는 같고, 조판만 다릅니다.
TXT,
        'goal' => <<<'TXT'
장식 없이 활자와 여백만으로 네 가지 다른 온도를 만든다.
TXT,
        'role' => '컨셉 / 조판 / 인쇄 감리',
        'tools' => 'InDesign, Illustrator',
        'process' => <<<'TXT'
같은 문장을 세리프·산세리프·모노 세 계열로 각각 조판해 비교했다.
포스터 판형은 B2로 고정하고, 여백 비율만 1:1.2 / 1:1.6 / 1:2 로 바꿔 실험했다.
가장 큰 글자와 가장 작은 글자의 크기 차이를 8배 이상 벌렸을 때 시선 이동이 가장 명확했다.
리소 인쇄 2도로 출력해 실제 종이 위 대비를 확인하고 잉크 농도를 두 번 조정했다.
TXT,
        'result' => <<<'TXT'
학과 전시에 4종 세트로 출품했다.
인쇄 결과 어두운 배경의 세리프 조판이 가장 멀리서도 읽혔다. 화면에서 판단한 것과 반대였다.
TXT,
        'images' => [
            'assets/images/works/type-01.svg',
            'assets/images/works/type-02.svg',
            'assets/images/works/type-03.svg',
        ],
    ],
    [
        'id' => 5,
        'title' => 'FRAME',
        'subtitle' => '비율이 제각각인 사진을 위한 아카이브',
        'category_id' => 2,
        'year' => 2025,
        'sort_order' => 2,
        'is_public' => 1,
        'created_at' => '2025-03-11 10:00:00',
        'thumbnail' => 'assets/images/works/frame-01.svg',
        'description' => <<<'TXT'
필름 사진을 올리는 아카이브 서비스의 화면 설계입니다. 6×6 정사각, 3:2, 파노라마가 한 화면에 섞이는 것이 전제 조건이었습니다.
모든 사진을 같은 비율로 자르는 순간 사진가가 정한 프레임이 사라진다는 점에서 출발했습니다.
TXT,
        'goal' => <<<'TXT'
어떤 비율의 사진도 자르지 않고, 그러면서도 목록이 지저분해 보이지 않게 한다.
TXT,
        'role' => '레이아웃 설계 / UI 디자인 / 프론트엔드 프로토타입',
        'tools' => 'Figma, HTML/CSS, JavaScript',
        'process' => <<<'TXT'
그리드 후보 3안(고정 카드 / 가로 스트립 / 메이슨리)을 실제 사진 120장으로 각각 조판해 비교했다.
메이슨리가 비율 유지에는 유리했지만 열이 늘어날수록 시선 흐름이 끊겼다.
열 개수를 최대 4열로 제한하고 열 사이 간격을 넓혀 세로 흐름을 살렸다.
사진마다 가로세로 값을 미리 읽어 자리를 먼저 잡도록 해서 로딩 중 밀림을 없앴다.
TXT,
        'result' => <<<'TXT'
사진 200장 기준 초기 로딩 후 레이아웃이 흔들리는 현상(CLS)을 0에 가깝게 유지했다.
이 프로젝트에서 만든 메이슨리 방식을 지금 이 포트폴리오 사이트에도 그대로 쓰고 있다.
TXT,
        'images' => [
            'assets/images/works/frame-01.svg',
            'assets/images/works/frame-02.svg',
            'assets/images/works/frame-03.svg',
        ],
    ],
    [
        'id' => 6,
        'title' => '하루의 온도',
        'subtitle' => '문장 대신 색으로 남기는 감정 기록',
        'category_id' => 1,
        'year' => 2024,
        'sort_order' => 7,
        'is_public' => 1,
        'created_at' => '2024-07-19 10:00:00',
        'thumbnail' => 'assets/images/works/haru-01.svg',
        'description' => <<<'TXT'
감정 기록 앱은 대부분 "오늘 기분을 골라주세요"로 시작합니다. 그런데 감정에 이름을 붙이는 일 자체가 부담이라는 이야기를 인터뷰에서 반복해서 들었습니다.
그래서 이름 대신 색 온도로 고르게 했습니다.
TXT,
        'goal' => <<<'TXT'
하루 기록을 3초 안에 끝내고, 한 달치를 한 화면에서 색으로 되돌아볼 수 있게 한다.
TXT,
        'role' => '리서치 / 컨셉 / 화면 설계 / UI 디자인 / 컬러 시스템',
        'tools' => 'Figma, Photoshop',
        'process' => <<<'TXT'
감정 기록 앱 경험자 6명 인터뷰. "기분 이름 고르기가 제일 어렵다"는 응답이 4명이었다.
차가운 색–따뜻한 색 12단계 스케일을 만들고, 좌우 슬라이드 한 번으로 선택이 끝나게 했다.
색만으로는 나중에 왜 그랬는지 기억나지 않는 문제가 있어, 선택 후 한 줄 메모를 선택 입력으로 붙였다.
월간 화면은 색 격자 하나로만 구성했다. 숫자와 그래프를 모두 뺐다.
색약 사용자를 고려해 명도 차이만으로도 12단계가 구분되는지 흑백 변환으로 검증했다.
TXT,
        'result' => <<<'TXT'
프로토타입 테스트에서 하루 기록 평균 소요 시간 4.2초.
"한 달을 한눈에 보는 화면이 가장 좋다"는 응답이 6명 중 5명이었다.
TXT,
        'images' => [
            'assets/images/works/haru-01.svg',
            'assets/images/works/haru-02.svg',
            'assets/images/works/haru-03.svg',
        ],
    ],
    [
        'id' => 7,
        'title' => '무해상점',
        'subtitle' => '제로웨이스트 편집숍 패키지',
        'category_id' => 4,
        'year' => 2023,
        'sort_order' => 8,
        'is_public' => 1,
        'created_at' => '2023-10-08 10:00:00',
        'thumbnail' => 'assets/images/works/muhae-01.svg',
        'description' => <<<'TXT'
포장을 줄이는 가게의 포장을 디자인하는 일이었습니다. 인쇄 도수, 코팅, 접착제까지 디자인 결정에 포함되는 프로젝트였습니다.
TXT,
        'goal' => <<<'TXT'
후가공 없이 1도 인쇄만으로 매대에서 구분되는 패키지를 만든다.
TXT,
        'role' => '패키지 구조 / 그래픽 / 라벨 시스템',
        'tools' => 'Illustrator, Photoshop',
        'process' => <<<'TXT'
코팅 없는 크라프트지 위에서 색이 어떻게 죽는지 먼저 인쇄 테스트를 했다.
녹색 계열 3종을 실제 종이에 뽑아 보고, 화면 값보다 명도를 15% 올려 보정했다.
품목이 40종이 넘어 라벨을 하나씩 그리지 않고 규격 3종 + 색 4종 조합으로 시스템화했다.
접착 라벨 대신 종이 띠지를 써서 분리배출 시 뜯어낼 필요가 없게 했다.
TXT,
        'result' => <<<'TXT'
라벨 12종 조합으로 품목 40여 종을 모두 커버했다.
1도 인쇄 기준으로 기존 대비 인쇄 단가를 약 30% 줄였다.
TXT,
        'images' => [
            'assets/images/works/muhae-01.svg',
            'assets/images/works/muhae-02.svg',
            'assets/images/works/muhae-03.svg',
        ],
    ],
    [
        'id' => 8,
        'title' => 'Seoul Type Week',
        'subtitle' => '타이포그래피 주간 행사 그래픽',
        'category_id' => 3,
        'year' => 2023,
        'sort_order' => 9,
        'is_public' => 1,
        'created_at' => '2023-05-16 10:00:00',
        'thumbnail' => 'assets/images/works/week-01.svg',
        'description' => <<<'TXT'
가상의 타이포그래피 행사를 위한 아이덴티티와 홍보물 세트입니다. 포스터 한 장이 아니라, 배너·프로그램북·현수막까지 같은 규칙으로 확장되는지를 확인하는 것이 과제였습니다.
TXT,
        'goal' => <<<'TXT'
서로 다른 판형 6종에서 같은 인상을 유지하는 그래픽 규칙을 만든다.
TXT,
        'role' => '아이덴티티 / 포스터 / 프로그램북 / 적용물',
        'tools' => 'InDesign, Illustrator',
        'process' => <<<'TXT'
행사명 두 단어를 위아래로 겹쳐 쌓는 것을 유일한 규칙으로 정했다.
판형이 바뀌어도 이 겹침 각도와 여백 비율만 지키면 같은 인상이 유지되는지 6종에 적용해 검증했다.
프로그램북은 2단 그리드로 잡고 강연 시간표를 왼쪽 고정, 설명을 오른쪽에 배치했다.
현수막처럼 멀리서 보는 매체는 자간을 좁히고 굵기를 한 단계 올려 별도 규격을 만들었다.
TXT,
        'result' => <<<'TXT'
포스터 2종, 배너 2종, 프로그램북, 현수막까지 총 6종을 하나의 규칙으로 완성했다.
멀리서 보는 매체용 별도 규격을 만든 것이 이 작업에서 가장 실용적인 결정이었다.
TXT,
        'images' => [
            'assets/images/works/week-01.svg',
            'assets/images/works/week-02.svg',
            'assets/images/works/week-03.svg',
        ],
    ],
    [
        'id' => 9,
        'title' => '커머스 관리자 콘솔',
        'subtitle' => '하루 300건을 처리하는 사람을 위한 화면',
        'category_id' => 1,
        'year' => 2025,
        'sort_order' => 6,
        'is_public' => 1,
        'created_at' => '2025-01-22 10:00:00',
        'thumbnail' => 'assets/images/works/admin-01.svg',
        'description' => <<<'TXT'
쇼핑몰 운영자가 하루 종일 보는 관리자 화면을 다시 설계했습니다. 예쁘게 만드는 것보다, 같은 동작을 300번 반복해도 지치지 않는 것이 목표였습니다.
TXT,
        'goal' => <<<'TXT'
주문 1건 처리에 필요한 클릭 수를 줄이고, 목록에서 페이지를 떠나지 않고 처리를 끝낸다.
TXT,
        'role' => '운영자 인터뷰 / 화면 설계 / 컴포넌트 정의 / UI 디자인',
        'tools' => 'Figma',
        'process' => <<<'TXT'
쇼핑몰 운영자 3명의 실제 작업을 각 2시간씩 옆에서 관찰했다.
가장 많이 반복되는 동작은 "주문 확인 → 송장 입력 → 발송 처리" 세 단계였다.
목록 행을 펼쳐서 그 자리에서 처리하도록 바꿔 상세 페이지 진입을 없앴다.
표는 밀도를 우선해 행 높이를 44px로 낮추고, 대신 행 구분선을 흐리게 해 눈의 피로를 줄였다.
버튼·배지·표 셀 등 반복 요소 24개를 컴포넌트로 정의해 개발 전달용 문서로 정리했다.
TXT,
        'result' => <<<'TXT'
주문 1건 처리 클릭 수가 7회에서 3회로 줄었다.
운영자 3명 모두 "목록에서 바로 끝나는 게 제일 낫다"고 답했다.
컴포넌트 24종 정의서를 함께 넘겨 개발 단계에서 되묻는 일을 줄였다.
TXT,
        'images' => [
            'assets/images/works/admin-01.svg',
            'assets/images/works/admin-02.svg',
            'assets/images/works/admin-03.svg',
        ],
    ],
];

return ['categories' => $categories, 'projects' => $projects];
