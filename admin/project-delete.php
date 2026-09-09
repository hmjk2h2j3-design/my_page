<?php
/**
 * 프로젝트 삭제.
 *
 * DB 행과 함께 uploads/ 아래에 있는 이미지 파일도 지웁니다.
 * (project_images 는 외래키 ON DELETE CASCADE 로 함께 지워집니다.)
 */

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/upload.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('잘못된 요청입니다.');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0 || !db_ready()) {
    $_SESSION['flash'] = '삭제하지 못했습니다.';
    header('Location: ' . url('admin/projects.php'));
    exit;
}

$project = repo_project($id, true);

if ($project) {
    $stmt = db()->prepare('SELECT image_path FROM project_images WHERE project_id = :p');
    $stmt->execute(['p' => $id]);

    foreach ($stmt->fetchAll() as $row) {
        delete_upload($row['image_path']);
    }
    delete_upload($project['thumbnail'] ?? null);

    $stmt = db()->prepare('DELETE FROM projects WHERE id = :id');
    $stmt->execute(['id' => $id]);

    $_SESSION['flash'] = '‘' . $project['title'] . '’ 삭제했습니다.';
} else {
    $_SESSION['flash'] = '이미 삭제된 프로젝트입니다.';
}

header('Location: ' . url('admin/projects.php'));
exit;
