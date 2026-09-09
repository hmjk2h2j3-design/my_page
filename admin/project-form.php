<?php
/**
 * 프로젝트 등록 / 수정.
 *
 * 한 프로젝트에 대표 이미지 1장과 상세 이미지 여러 장을 붙일 수 있습니다.
 * 상세 이미지는 순서 값으로 정렬해 상세 페이지에 그대로 출력됩니다.
 */

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/upload.php';

require_login();

$stats      = repo_stats();
$categories = repo_categories();

$id      = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$editing = $id > 0;
$project = $editing ? repo_project($id, true) : null;

if ($editing && !$project) {
    http_response_code(404);
    exit('프로젝트를 찾을 수 없습니다.');
}

$errors = [];

// 화면에 채워 넣을 값
$form = [
    'title'       => $project['title']       ?? '',
    'subtitle'    => $project['subtitle']    ?? '',
    'category_id' => $project['category_id'] ?? ($categories[0]['id'] ?? 0),
    'year'        => $project['year']        ?? (int) date('Y'),
    'description' => $project['description'] ?? '',
    'goal'        => $project['goal']        ?? '',
    'role'        => $project['role']        ?? '',
    'tools'       => $project['tools']       ?? '',
    'process'     => $project['process']     ?? '',
    'result'      => $project['result']      ?? '',
    'thumbnail'   => $project['thumbnail']   ?? '',
    'is_public'   => (int) ($project['is_public'] ?? 1),
    'sort_order'  => $project['sort_order']  ?? ($stats['total'] + 1),
];

/* ------------------------------------------------------------------ */
/* 저장                                                                */
/* ------------------------------------------------------------------ */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($form) as $key) {
        if ($key === 'thumbnail') {
            continue;
        }
        $form[$key] = is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : ($form[$key]);
    }
    $form['is_public']   = isset($_POST['is_public']) ? 1 : 0;
    $form['category_id'] = (int) ($_POST['category_id'] ?? 0);
    $form['year']        = (int) ($_POST['year'] ?? date('Y'));
    $form['sort_order']  = (int) ($_POST['sort_order'] ?? 0);
    $form['thumbnail']   = (string) ($_POST['thumbnail_current'] ?? '');

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors['form'] = '보안 토큰이 만료되었습니다. 페이지를 새로고침한 뒤 다시 저장해 주세요.';
    }
    if (!db_ready()) {
        $errors['form'] = 'DB에 연결되어 있지 않아 저장할 수 없습니다.';
    }
    if ($form['title'] === '') {
        $errors['title'] = '프로젝트명을 입력해 주세요.';
    }
    if ($form['year'] < 1990 || $form['year'] > (int) date('Y') + 1) {
        $errors['year'] = '제작연도를 확인해 주세요.';
    }

    // 대표 이미지
    if (!empty($_FILES['thumbnail_file']['name'])) {
        $up = store_uploaded_image($_FILES['thumbnail_file']);
        if (!$up['ok']) {
            $errors['thumbnail'] = $up['error'];
        } else {
            $old = $form['thumbnail'];
            $form['thumbnail'] = $up['thumb'];
            if ($old && $old !== $form['thumbnail']) {
                delete_upload($old);
            }
        }
    }

    if (!$errors) {
        $fields = [
            'title'       => $form['title'],
            'subtitle'    => $form['subtitle'],
            'category_id' => $form['category_id'] ?: null,
            'year'        => $form['year'],
            'description' => $form['description'],
            'goal'        => $form['goal'],
            'role'        => $form['role'],
            'tools'       => $form['tools'],
            'process'     => $form['process'],
            'result'      => $form['result'],
            'thumbnail'   => $form['thumbnail'],
            'is_public'   => $form['is_public'],
            'sort_order'  => $form['sort_order'],
        ];

        if ($editing) {
            $set = implode(', ', array_map(static fn($k) => "`$k` = :$k", array_keys($fields)));
            $stmt = db()->prepare("UPDATE projects SET $set WHERE id = :id");
            $stmt->execute($fields + ['id' => $id]);
        } else {
            $cols = '`' . implode('`, `', array_keys($fields)) . '`';
            $vals = ':' . implode(', :', array_keys($fields));
            $stmt = db()->prepare("INSERT INTO projects ($cols) VALUES ($vals)");
            $stmt->execute($fields);
            $id = (int) db()->lastInsertId();
        }

        // 기존 상세 이미지: 순서 변경 / 삭제
        foreach ((array) ($_POST['image_sort'] ?? []) as $imageId => $order) {
            $stmt = db()->prepare('UPDATE project_images SET sort_order = :o WHERE id = :i AND project_id = :p');
            $stmt->execute(['o' => (int) $order, 'i' => (int) $imageId, 'p' => $id]);
        }

        foreach ((array) ($_POST['image_delete'] ?? []) as $imageId) {
            $stmt = db()->prepare('SELECT image_path FROM project_images WHERE id = :i AND project_id = :p');
            $stmt->execute(['i' => (int) $imageId, 'p' => $id]);
            if ($path = $stmt->fetchColumn()) {
                delete_upload($path);
            }
            $stmt = db()->prepare('DELETE FROM project_images WHERE id = :i AND project_id = :p');
            $stmt->execute(['i' => (int) $imageId, 'p' => $id]);
        }

        // 새 상세 이미지 (여러 장)
        $files = $_FILES['images'] ?? null;
        if ($files && is_array($files['name'])) {
            $next = (int) db()->query("SELECT COALESCE(MAX(sort_order), 0) FROM project_images WHERE project_id = $id")->fetchColumn();

            foreach ($files['name'] as $i => $name) {
                if ($name === '' || $files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                $one = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
                $up = store_uploaded_image($one);
                if (!$up['ok']) {
                    $errors['images'] = $up['error'];
                    continue;
                }
                $next++;
                $stmt = db()->prepare('INSERT INTO project_images (project_id, image_path, sort_order) VALUES (:p, :i, :o)');
                $stmt->execute(['p' => $id, 'i' => $up['path'], 'o' => $next]);
            }
        }

        if (!isset($errors['images'])) {
            $_SESSION['flash'] = '‘' . $form['title'] . '’ 저장했습니다.';
            header('Location: ' . url('admin/projects.php'));
            exit;
        }

        $project = repo_project($id, true);
        $editing = true;
    }
}

// 상세 이미지 목록 (id 포함) — 편집 화면에서만 필요
$imageRows = [];
if ($editing && db_ready()) {
    $stmt = db()->prepare('SELECT id, image_path, sort_order FROM project_images WHERE project_id = :p ORDER BY sort_order, id');
    $stmt->execute(['p' => $id]);
    $imageRows = $stmt->fetchAll();
}

$adminTitle = $editing ? '작업물 수정' : '새 작업물 등록';
$adminNote  = '작업 과정과 결과는 한 줄에 한 항목씩 적으면 상세 페이지에서 목록으로 나옵니다.';
$adminHead  = '<a class="btn btn--ghost btn--sm" href="' . e(url('admin/projects.php')) . '">목록으로</a>';

require __DIR__ . '/includes/head.php';
?>

<?php if (!empty($errors['form'])): ?>
  <p class="notice"><?= e($errors['form']) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data"
      action="<?= e(url('admin/project-form.php' . ($editing ? '?id=' . $id : ''))) ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="thumbnail_current" value="<?= e($form['thumbnail']) ?>">

  <div class="form-grid">

    <!-- ------------------------------ 본문 ------------------------------ -->
    <div>
      <section class="form-panel">
        <h2 class="form-panel__title">기본 정보</h2>

        <div class="field">
          <label for="title">프로젝트명 <span class="req">*</span></label>
          <input class="input" type="text" id="title" name="title" required maxlength="120"
                 value="<?= e($form['title']) ?>">
          <?php if (isset($errors['title'])): ?>
            <span class="field__error"><?= e($errors['title']) ?></span>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="subtitle">한 줄 설명</label>
          <input class="input" type="text" id="subtitle" name="subtitle" maxlength="200"
                 value="<?= e($form['subtitle']) ?>">
          <span class="field__help">목록 카드와 상세 페이지 제목 아래에 나옵니다.</span>
        </div>

        <div class="field-row">
          <div class="field">
            <label for="category_id">카테고리</label>
            <select class="select" id="category_id" name="category_id">
              <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= (int) $form['category_id'] === (int) $c['id'] ? 'selected' : '' ?>>
                  <?= e($c['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="year">제작연도</label>
            <input class="input" type="number" id="year" name="year" min="1990" max="<?= (int) date('Y') + 1 ?>"
                   value="<?= (int) $form['year'] ?>">
            <?php if (isset($errors['year'])): ?>
              <span class="field__error"><?= e($errors['year']) ?></span>
            <?php endif; ?>
          </div>

          <div class="field">
            <label for="sort_order">표시 순서</label>
            <input class="input" type="number" id="sort_order" name="sort_order" min="0"
                   value="<?= (int) $form['sort_order'] ?>">
            <span class="field__help">작을수록 앞</span>
          </div>
        </div>
      </section>

      <section class="form-panel">
        <h2 class="form-panel__title">내용</h2>

        <div class="field">
          <label for="description">개요</label>
          <textarea class="textarea" id="description" name="description" rows="5"><?= e($form['description']) ?></textarea>
          <span class="field__help">어떤 문제에서 출발했는지 씁니다. 빈 줄 없이 줄만 바꾸면 문단으로 나뉩니다.</span>
        </div>

        <div class="field">
          <label for="goal">프로젝트 목표</label>
          <textarea class="textarea" id="goal" name="goal" rows="2" style="min-height:80px"><?= e($form['goal']) ?></textarea>
          <span class="field__help">상세 페이지에서 큰 글씨로 강조됩니다. 한 문장을 권합니다.</span>
        </div>

        <div class="field-row">
          <div class="field">
            <label for="role">담당 역할</label>
            <input class="input" type="text" id="role" name="role" value="<?= e($form['role']) ?>">
          </div>
          <div class="field">
            <label for="tools">사용 툴</label>
            <input class="input" type="text" id="tools" name="tools" value="<?= e($form['tools']) ?>">
          </div>
        </div>

        <div class="field">
          <label for="process">작업 과정</label>
          <textarea class="textarea" id="process" name="process" rows="6"><?= e($form['process']) ?></textarea>
          <span class="field__help">한 줄에 한 단계씩. 번호가 자동으로 붙습니다.</span>
        </div>

        <div class="field">
          <label for="result">결과</label>
          <textarea class="textarea" id="result" name="result" rows="4"><?= e($form['result']) ?></textarea>
          <span class="field__help">한 줄에 한 항목씩.</span>
        </div>
      </section>

      <section class="form-panel">
        <h2 class="form-panel__title">상세 이미지</h2>

        <?php if (!$editing): ?>
          <p class="field__help" style="margin-bottom:18px">
            먼저 저장하면 상세 이미지를 여러 장 올릴 수 있습니다. 지금 올려도 함께 저장됩니다.
          </p>
        <?php endif; ?>

        <?php if ($imageRows): ?>
          <div class="img-list">
            <?php foreach ($imageRows as $row): ?>
              <div class="img-item">
                <img src="<?= e(url($row['image_path'])) ?>" alt="" loading="lazy">
                <div class="img-item__row">
                  <label>
                    순서
                    <input type="number" name="image_sort[<?= (int) $row['id'] ?>]"
                           value="<?= (int) $row['sort_order'] ?>" min="0">
                  </label>
                  <label class="check" style="font-size:0.75rem">
                    <input type="checkbox" name="image_delete[]" value="<?= (int) $row['id'] ?>">
                    삭제
                  </label>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="field">
          <label for="images">이미지 추가 (여러 장 선택 가능)</label>
          <input type="file" id="images" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
          <span class="field__help">JPG · PNG · WEBP, 한 장당 10MB 이하. 올린 순서대로 뒤에 붙습니다.</span>
          <?php if (isset($errors['images'])): ?>
            <span class="field__error"><?= e($errors['images']) ?></span>
          <?php endif; ?>
        </div>
      </section>
    </div>

    <!-- ------------------------------ 사이드 ------------------------------ -->
    <div class="form-sticky">
      <section class="form-panel">
        <h2 class="form-panel__title">대표 이미지</h2>

        <?php if ($form['thumbnail']): ?>
          <img class="thumb-preview" src="<?= e(url($form['thumbnail'])) ?>" alt="현재 대표 이미지">
        <?php endif; ?>

        <div class="field">
          <label for="thumbnail_file"><?= $form['thumbnail'] ? '다른 이미지로 교체' : '이미지 선택' ?></label>
          <input type="file" id="thumbnail_file" name="thumbnail_file" accept="image/jpeg,image/png,image/webp">
          <span class="field__help">목록 카드와 홈에 쓰입니다. 가로형 이미지를 권합니다.</span>
          <?php if (isset($errors['thumbnail'])): ?>
            <span class="field__error"><?= e($errors['thumbnail']) ?></span>
          <?php endif; ?>
        </div>
      </section>

      <section class="form-panel">
        <h2 class="form-panel__title">공개 설정</h2>
        <div class="field">
          <label class="check">
            <input type="checkbox" name="is_public" value="1" <?= $form['is_public'] ? 'checked' : '' ?>>
            사이트에 공개
          </label>
          <span class="field__help">해제하면 관리자에게만 보입니다.</span>
        </div>
      </section>

      <div class="admin-actions">
        <button class="btn" type="submit">저장</button>
        <a class="btn btn--ghost" href="<?= e(url('admin/projects.php')) ?>">취소</a>
      </div>
    </div>

  </div>
</form>

<?php require __DIR__ . '/includes/foot.php'; ?>
