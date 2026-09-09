<?php
/**
 * 카테고리 관리 — 추가 / 이름·순서 수정 / 삭제.
 *
 * 삭제해도 프로젝트는 남습니다 (외래키 ON DELETE SET NULL).
 */

require_once dirname(__DIR__) . '/includes/auth.php';

require_login();

$errors = [];
$flash  = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

/** 이름에서 slug 후보를 만듭니다. */
function slugify(string $name): string
{
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/u', '-', $slug) ?? '';
    return trim($slug, '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors['form'] = '보안 토큰이 만료되었습니다. 다시 시도해 주세요.';
    } elseif (!db_ready()) {
        $errors['form'] = 'DB에 연결되어 있지 않아 저장할 수 없습니다.';
    } else {
        try {
            if ($action === 'create') {
                $name = trim((string) ($_POST['name'] ?? ''));
                $slug = slugify((string) ($_POST['slug'] ?? '')) ?: slugify($name);

                if ($name === '') {
                    $errors['name'] = '카테고리명을 입력해 주세요.';
                } elseif ($slug === '') {
                    $errors['slug'] = 'URL에 쓸 영문 키를 입력해 주세요. (예: motion)';
                } else {
                    $order = (int) db()->query('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM categories')->fetchColumn();
                    $stmt = db()->prepare('INSERT INTO categories (name, slug, sort_order) VALUES (:n, :s, :o)');
                    $stmt->execute(['n' => $name, 's' => $slug, 'o' => $order]);
                    $_SESSION['flash'] = '‘' . $name . '’ 추가했습니다.';
                    header('Location: ' . url('admin/categories.php'));
                    exit;
                }
            }

            if ($action === 'update') {
                foreach ((array) ($_POST['name'] ?? []) as $cid => $name) {
                    $name = trim((string) $name);
                    if ($name === '') {
                        continue;
                    }
                    $stmt = db()->prepare('UPDATE categories SET name = :n, sort_order = :o WHERE id = :id');
                    $stmt->execute([
                        'n'  => $name,
                        'o'  => (int) ($_POST['sort_order'][$cid] ?? 0),
                        'id' => (int) $cid,
                    ]);
                }
                $_SESSION['flash'] = '카테고리를 저장했습니다.';
                header('Location: ' . url('admin/categories.php'));
                exit;
            }

            if ($action === 'delete') {
                $stmt = db()->prepare('DELETE FROM categories WHERE id = :id');
                $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
                $_SESSION['flash'] = '카테고리를 삭제했습니다. 해당 작업물은 카테고리 없음 상태가 됩니다.';
                header('Location: ' . url('admin/categories.php'));
                exit;
            }
        } catch (PDOException $e) {
            $errors['form'] = '같은 영문 키(slug)를 쓰는 카테고리가 이미 있습니다.';
        }
    }
}

$stats      = repo_stats();
$categories = repo_categories();
$counts     = repo_category_counts();

$adminTitle = '카테고리';
$adminNote  = '포트폴리오 필터에 나오는 항목입니다. 영문 키는 주소(?cat=)에 쓰이므로 만든 뒤에는 바꾸지 않는 편이 좋습니다.';

require __DIR__ . '/includes/head.php';
?>

<?php if ($flash): ?>
  <p class="notice notice--ok"><?= e($flash) ?></p>
<?php endif; ?>
<?php if (!empty($errors['form'])): ?>
  <p class="notice"><?= e($errors['form']) ?></p>
<?php endif; ?>

<div class="form-grid">

  <form method="post" action="<?= e(url('admin/categories.php')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="update">

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th style="width:70px">순서</th>
            <th>이름</th>
            <th style="width:140px">영문 키</th>
            <th style="width:90px">작업물</th>
            <th style="width:90px"></th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$categories): ?>
            <tr><td colspan="5" class="empty-row">카테고리가 없습니다.</td></tr>
          <?php endif; ?>

          <?php foreach ($categories as $c): ?>
            <tr>
              <td>
                <input class="input" type="number" style="padding:4px 2px;max-width:56px"
                       name="sort_order[<?= (int) $c['id'] ?>]" value="<?= (int) $c['sort_order'] ?>" min="0">
              </td>
              <td>
                <input class="input" type="text" style="padding:4px 2px"
                       name="name[<?= (int) $c['id'] ?>]" value="<?= e($c['name']) ?>" maxlength="40">
              </td>
              <td><span class="chip"><?= e($c['slug']) ?></span></td>
              <td><?= (int) ($counts[$c['slug']] ?? 0) ?>개</td>
              <td>
                <div class="table__actions">
                  <button class="btn btn--danger btn--sm" type="submit"
                          form="delete-<?= (int) $c['id'] ?>">삭제</button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="admin-actions" style="margin-top:16px">
      <button class="btn btn--sm" type="submit">순서 · 이름 저장</button>
    </div>
  </form>

  <div class="form-sticky">
    <section class="form-panel">
      <h2 class="form-panel__title">카테고리 추가</h2>
      <form method="post" action="<?= e(url('admin/categories.php')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create">

        <div class="field">
          <label for="new-name">이름</label>
          <input class="input" type="text" id="new-name" name="name" maxlength="40" placeholder="MOTION" required>
          <?php if (isset($errors['name'])): ?>
            <span class="field__error"><?= e($errors['name']) ?></span>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="new-slug">영문 키 (선택)</label>
          <input class="input" type="text" id="new-slug" name="slug" maxlength="40" placeholder="motion">
          <span class="field__help">비워 두면 이름에서 자동으로 만듭니다. 소문자·숫자·하이픈만 쓸 수 있습니다.</span>
          <?php if (isset($errors['slug'])): ?>
            <span class="field__error"><?= e($errors['slug']) ?></span>
          <?php endif; ?>
        </div>

        <button class="btn btn--sm" type="submit" style="margin-top:4px">추가</button>
      </form>
    </section>
  </div>

</div>

<?php foreach ($categories as $c): ?>
  <form id="delete-<?= (int) $c['id'] ?>" method="post" action="<?= e(url('admin/categories.php')) ?>" hidden
        data-confirm="‘<?= e($c['name']) ?>’ 카테고리를 삭제합니다. 이 카테고리의 작업물은 삭제되지 않고 분류만 사라집니다. 계속할까요?">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
  </form>
<?php endforeach; ?>

<?php require __DIR__ . '/includes/foot.php'; ?>
