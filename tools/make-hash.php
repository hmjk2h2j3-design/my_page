<?php
/**
 * 관리자 비밀번호 해시 생성기 (명령줄 전용).
 *
 *   php tools/make-hash.php 원하는비밀번호
 *
 * 출력된 문자열을 users 테이블의 password 컬럼에 넣으세요.
 * 평문 비밀번호는 어디에도 저장하지 않습니다.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('이 스크립트는 명령줄에서만 실행할 수 있습니다.');
}

$password = $argv[1] ?? null;

if ($password === null || $password === '') {
    fwrite(STDERR, "사용법: php tools/make-hash.php <비밀번호>\n");
    exit(1);
}

if (strlen($password) < 10) {
    fwrite(STDERR, "비밀번호는 10자 이상을 권장합니다.\n");
}

echo password_hash($password, PASSWORD_DEFAULT), PHP_EOL;
