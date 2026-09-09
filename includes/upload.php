<?php
/**
 * 이미지 업로드.
 *
 * 확장자만 믿지 않고 실제 파일 내용을 확인합니다.
 *   1) 업로드 오류 코드 확인
 *   2) 용량 제한
 *   3) getimagesize() 로 진짜 이미지인지 확인 (MIME 스푸핑 방지)
 *   4) 파일명을 난수로 새로 만들어 저장 (원본 파일명은 쓰지 않음)
 *   5) 목록용 썸네일 생성
 */

require_once __DIR__ . '/functions.php';

const UPLOAD_MAX_BYTES  = 10 * 1024 * 1024; // 10MB
const THUMB_MAX_WIDTH   = 1000;
const UPLOAD_ORIGINAL_DIR = 'uploads/projects/original';
const UPLOAD_THUMB_DIR    = 'uploads/projects/thumbnail';

/** 허용 형식: [감지된 이미지 타입 => 확장자] */
function upload_allowed_types(): array
{
    return [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];
}

function upload_error_message(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => '파일이 너무 큽니다. 10MB 이하로 올려 주세요.',
        UPLOAD_ERR_PARTIAL   => '파일이 일부만 전송되었습니다. 다시 시도해 주세요.',
        UPLOAD_ERR_NO_FILE   => '파일이 선택되지 않았습니다.',
        UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE => '서버에 파일을 저장할 수 없습니다.',
        UPLOAD_ERR_EXTENSION => '서버 설정에 의해 업로드가 중단되었습니다.',
        default              => '업로드에 실패했습니다.',
    };
}

/**
 * 업로드된 파일 하나를 저장합니다.
 *
 * @return array{ok:bool, path?:string, thumb?:string, error?:string}
 */
function store_uploaded_image(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => upload_error_message((int) $file['error'])];
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return ['ok' => false, 'error' => '올바른 업로드가 아닙니다.'];
    }

    if ($file['size'] > UPLOAD_MAX_BYTES) {
        return ['ok' => false, 'error' => '파일이 너무 큽니다. 10MB 이하로 올려 주세요.'];
    }

    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        return ['ok' => false, 'error' => '이미지 파일이 아닙니다. JPG, PNG, WEBP만 올릴 수 있습니다.'];
    }

    $allowed = upload_allowed_types();
    $type    = $info[2];
    if (!isset($allowed[$type])) {
        return ['ok' => false, 'error' => 'JPG, PNG, WEBP 형식만 올릴 수 있습니다.'];
    }

    $ext  = $allowed[$type];
    $name = date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $ext;

    $originalDir = ROOT_PATH . '/' . UPLOAD_ORIGINAL_DIR;
    $thumbDir    = ROOT_PATH . '/' . UPLOAD_THUMB_DIR;

    foreach ([$originalDir, $thumbDir] as $dir) {
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            return ['ok' => false, 'error' => '업로드 폴더를 만들 수 없습니다: ' . basename($dir)];
        }
    }

    if (!@move_uploaded_file($file['tmp_name'], $originalDir . '/' . $name)) {
        return ['ok' => false, 'error' => '파일을 저장하지 못했습니다.'];
    }

    @chmod($originalDir . '/' . $name, 0644);

    $thumb = make_thumbnail($originalDir . '/' . $name, $thumbDir . '/' . $name, $type);

    return [
        'ok'    => true,
        'path'  => UPLOAD_ORIGINAL_DIR . '/' . $name,
        'thumb' => $thumb ? UPLOAD_THUMB_DIR . '/' . $name : UPLOAD_ORIGINAL_DIR . '/' . $name,
    ];
}

/**
 * 목록용 축소본을 만듭니다. GD가 없으면 원본을 그대로 씁니다.
 * 가로세로 비율은 그대로 유지합니다.
 */
function make_thumbnail(string $source, string $target, int $type): bool
{
    if (!function_exists('imagecreatetruecolor')) {
        return false;
    }

    [$width, $height] = getimagesize($source);
    if (!$width || !$height) {
        return false;
    }

    if ($width <= THUMB_MAX_WIDTH) {
        return (bool) @copy($source, $target);
    }

    $newWidth  = THUMB_MAX_WIDTH;
    $newHeight = (int) round($height * ($newWidth / $width));

    $src = match ($type) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($source),
        IMAGETYPE_PNG  => @imagecreatefrompng($source),
        IMAGETYPE_WEBP => @imagecreatefromwebp($source),
        default        => false,
    };

    if (!$src) {
        return false;
    }

    $dst = imagecreatetruecolor($newWidth, $newHeight);

    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $saved = match ($type) {
        IMAGETYPE_JPEG => imagejpeg($dst, $target, 82),
        IMAGETYPE_PNG  => imagepng($dst, $target, 6),
        IMAGETYPE_WEBP => imagewebp($dst, $target, 82),
        default        => false,
    };

    imagedestroy($src);
    imagedestroy($dst);

    return (bool) $saved;
}

/**
 * DB에서 지운 이미지의 실제 파일을 정리합니다.
 * uploads/ 아래 경로만 삭제하며, 그 밖의 경로는 무시합니다.
 */
function delete_upload(?string $relPath): void
{
    if (!$relPath || !str_starts_with($relPath, 'uploads/')) {
        return;
    }

    $file = realpath(ROOT_PATH . '/' . $relPath);
    $base = realpath(ROOT_PATH . '/uploads');

    if ($file && $base && str_starts_with($file, $base) && is_file($file)) {
        @unlink($file);
    }

    // 원본과 썸네일은 파일명이 같으므로 짝도 함께 지웁니다.
    foreach ([UPLOAD_ORIGINAL_DIR, UPLOAD_THUMB_DIR] as $dir) {
        $pair = realpath(ROOT_PATH . '/' . $dir . '/' . basename($relPath));
        if ($pair && $base && str_starts_with($pair, $base) && is_file($pair)) {
            @unlink($pair);
        }
    }
}
