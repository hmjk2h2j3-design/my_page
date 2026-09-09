<?php
/**
 * MySQL 연결 설정.
 *
 * DB가 준비되지 않은 상태에서도 공개 페이지는 그대로 동작합니다.
 * (data/seed.php 의 샘플 데이터로 자동 대체됩니다.)
 * sql/schema.sql 을 import 한 뒤 아래 값을 채우면 CMS 연동으로 전환됩니다.
 */

return [
    'host'    => '127.0.0.1',
    'port'    => 3306,
    'dbname'  => 'portfolio',
    'user'    => 'root',
    'pass'    => '',
    'charset' => 'utf8mb4',
];
