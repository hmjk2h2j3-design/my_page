<?php
/**
 * 공용 헬퍼.
 * 모든 페이지가 이 파일 하나만 require 하면 됩니다.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(__DIR__));

/** 사이트 설정 (config/site.php) */
function site(?string $key = null)
{
    static $conf = null;
    if ($conf === null) {
        $conf = require ROOT_PATH . '/config/site.php';
    }
    return $key === null ? $conf : ($conf[$key] ?? null);
}

/** HTML 이스케이프 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** 사이트 내부 링크 */
function url(string $path = ''): string
{
    $base = rtrim((string) site('base_url'), '/');
    $path = ltrim($path, '/');
    return $path === '' ? ($base ?: '/') : $base . '/' . $path;
}

/** 정적 파일 경로 (수정 시 캐시가 갱신되도록 버전 쿼리를 붙임) */
function asset(string $path): string
{
    $full = ROOT_PATH . '/' . ltrim($path, '/');
    $ver  = is_file($full) ? filemtime($full) : null;
    return url($path) . ($ver ? '?v=' . $ver : '');
}

/** 현재 페이지 파일명 (nav 활성 표시에 사용) */
function current_page(): string
{
    return basename($_SERVER['SCRIPT_NAME'] ?? '', '.php');
}

/* ------------------------------------------------------------------ */
/* CSRF                                                                */
/* ------------------------------------------------------------------ */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(?string $token): bool
{
    return !empty($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

/* ------------------------------------------------------------------ */
/* 이미지                                                              */
/* ------------------------------------------------------------------ */

/**
 * 이미지의 원본 가로·세로를 반환합니다. 실패하면 null.
 *
 * width/height 속성을 미리 넣어 두면 브라우저가 이미지를 내려받기 전에
 * 자리를 잡아 두기 때문에 목록이 밀리지 않습니다(레이아웃 시프트 방지).
 * SVG는 getimagesize()가 읽지 못하므로 루트 태그를 직접 파싱합니다.
 */
function image_size(string $relPath): ?array
{
    static $cache = [];
    if (array_key_exists($relPath, $cache)) {
        return $cache[$relPath];
    }

    $file = ROOT_PATH . '/' . ltrim($relPath, '/');
    $size = null;

    if (is_file($file)) {
        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'svg') {
            $head = (string) file_get_contents($file, false, null, 0, 1024);
            if (preg_match('/viewBox\s*=\s*"[\d.\-]+\s+[\d.\-]+\s+([\d.]+)\s+([\d.]+)"/i', $head, $m)) {
                $size = [(int) round((float) $m[1]), (int) round((float) $m[2])];
            } elseif (preg_match('/width\s*=\s*"(\d+)"[^>]*height\s*=\s*"(\d+)"/i', $head, $m)) {
                $size = [(int) $m[1], (int) $m[2]];
            }
        } else {
            $info = @getimagesize($file);
            if ($info) {
                $size = [(int) $info[0], (int) $info[1]];
            }
        }
    }

    return $cache[$relPath] = $size;
}

/** <img> 에 붙일 width/height/aspect-ratio 속성 문자열 */
function image_attrs(string $relPath): string
{
    $size = image_size($relPath);
    if (!$size) {
        return '';
    }
    return sprintf(
        ' width="%d" height="%d" style="aspect-ratio:%d/%d"',
        $size[0],
        $size[1],
        $size[0],
        $size[1]
    );
}

/* ------------------------------------------------------------------ */
/* 텍스트                                                              */
/* ------------------------------------------------------------------ */

/** 줄바꿈으로 구분된 텍스트를 배열로 (빈 줄 제거) */
function text_lines(?string $text): array
{
    if ($text === null || trim($text) === '') {
        return [];
    }
    $lines = preg_split('/\R/u', trim($text)) ?: [];
    return array_values(array_filter(array_map('trim', $lines), static fn($l) => $l !== ''));
}

/** 목록 카드용 짧은 요약 */
function excerpt(?string $text, int $length = 90): string
{
    $text = trim(preg_replace('/\s+/u', ' ', (string) $text));
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length, 'UTF-8') . '…';
}

/** 두 자리 인덱스 (01, 02 …) */
function idx(int $n): string
{
    return str_pad((string) $n, 2, '0', STR_PAD_LEFT);
}
