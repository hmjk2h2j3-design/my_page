<?php
/**
 * 데이터 접근 계층.
 *
 * MySQL이 연결되면 DB에서, 아니면 data/seed.php 에서 같은 형태의 배열을 돌려줍니다.
 * 덕분에 페이지 쪽 코드는 DB 유무를 신경 쓰지 않아도 됩니다.
 */

require_once __DIR__ . '/functions.php';

/* ------------------------------------------------------------------ */
/* 연결                                                                */
/* ------------------------------------------------------------------ */

function db(): ?PDO
{
    static $pdo = false;
    if ($pdo !== false) {
        return $pdo;
    }

    $c = require ROOT_PATH . '/config/database.php';
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $c['host'], $c['port'], $c['dbname'], $c['charset']);

    try {
        $pdo = new PDO($dsn, $c['user'], $c['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        $pdo = null;
    }

    return $pdo;
}

function db_ready(): bool
{
    return db() instanceof PDO;
}

function seed(): array
{
    static $data = null;
    if ($data === null) {
        $data = require ROOT_PATH . '/data/seed.php';
    }
    return $data;
}

/* ------------------------------------------------------------------ */
/* 카테고리                                                            */
/* ------------------------------------------------------------------ */

function repo_categories(): array
{
    if (db_ready()) {
        return db()->query('SELECT id, name, slug, sort_order FROM categories ORDER BY sort_order, id')->fetchAll();
    }
    return seed()['categories'];
}

function repo_category_by_slug(string $slug): ?array
{
    foreach (repo_categories() as $cat) {
        if ($cat['slug'] === $slug) {
            return $cat;
        }
    }
    return null;
}

/** 카테고리별 공개 프로젝트 수 */
function repo_category_counts(): array
{
    $counts = [];
    foreach (repo_projects(['sort' => 'curated']) as $p) {
        $slug = $p['category_slug'];
        $counts[$slug] = ($counts[$slug] ?? 0) + 1;
    }
    return $counts;
}

/* ------------------------------------------------------------------ */
/* 프로젝트                                                            */
/* ------------------------------------------------------------------ */

/**
 * @param array{category?:?string, sort?:string, limit?:?int, include_private?:bool, exclude?:?int} $opts
 */
function repo_projects(array $opts = []): array
{
    $category = $opts['category'] ?? null;
    $sort     = $opts['sort'] ?? 'curated';
    $limit    = $opts['limit'] ?? null;
    $private  = $opts['include_private'] ?? false;
    $exclude  = $opts['exclude'] ?? null;

    $order = match ($sort) {
        'new' => 'p.year DESC, p.created_at DESC',
        'old' => 'p.year ASC, p.created_at ASC',
        default => 'p.sort_order ASC, p.id ASC',
    };

    if (db_ready()) {
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM projects p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE 1 = 1';
        $args = [];
        if (!$private) {
            $sql .= ' AND p.is_public = 1';
        }
        if ($category) {
            $sql .= ' AND c.slug = :slug';
            $args['slug'] = $category;
        }
        if ($exclude) {
            $sql .= ' AND p.id <> :ex';
            $args['ex'] = $exclude;
        }
        $sql .= ' ORDER BY ' . $order;
        if ($limit) {
            $sql .= ' LIMIT ' . (int) $limit;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($args);
        return $stmt->fetchAll();
    }

    // --- seed fallback ---
    $cats = [];
    foreach (seed()['categories'] as $c) {
        $cats[$c['id']] = $c;
    }

    $rows = [];
    foreach (seed()['projects'] as $p) {
        if (!$private && empty($p['is_public'])) {
            continue;
        }
        $cat = $cats[$p['category_id']] ?? null;
        $p['category_name'] = $cat['name'] ?? '';
        $p['category_slug'] = $cat['slug'] ?? '';
        if ($category && $p['category_slug'] !== $category) {
            continue;
        }
        if ($exclude && (int) $p['id'] === (int) $exclude) {
            continue;
        }
        $rows[] = $p;
    }

    usort($rows, static function (array $a, array $b) use ($sort) {
        return match ($sort) {
            'new' => [$b['year'], $b['created_at']] <=> [$a['year'], $a['created_at']],
            'old' => [$a['year'], $a['created_at']] <=> [$b['year'], $b['created_at']],
            default => [$a['sort_order'], $a['id']] <=> [$b['sort_order'], $b['id']],
        };
    });

    return $limit ? array_slice($rows, 0, $limit) : $rows;
}

/** 상세 1건 (images 포함) */
function repo_project(int $id, bool $includePrivate = false): ?array
{
    if (db_ready()) {
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM projects p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.id = :id' . ($includePrivate ? '' : ' AND p.is_public = 1');
        $stmt = db()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $project = $stmt->fetch();
        if (!$project) {
            return null;
        }
        $stmt = db()->prepare('SELECT image_path FROM project_images WHERE project_id = :id ORDER BY sort_order, id');
        $stmt->execute(['id' => $id]);
        $project['images'] = array_column($stmt->fetchAll(), 'image_path');
        return $project;
    }

    foreach (repo_projects(['include_private' => $includePrivate]) as $p) {
        if ((int) $p['id'] === $id) {
            return $p;
        }
    }
    return null;
}

/** 같은 카테고리 → 부족하면 최신순으로 채움 */
function repo_related(array $project, int $limit = 3): array
{
    $same = repo_projects([
        'category' => $project['category_slug'],
        'sort'     => 'curated',
        'exclude'  => (int) $project['id'],
        'limit'    => $limit,
    ]);
    if (count($same) >= $limit) {
        return $same;
    }

    $seen = array_column($same, 'id');
    foreach (repo_projects(['sort' => 'new', 'exclude' => (int) $project['id']]) as $p) {
        if (count($same) >= $limit) {
            break;
        }
        if (!in_array($p['id'], $seen, false)) {
            $same[] = $p;
            $seen[] = $p['id'];
        }
    }
    return $same;
}

/* ------------------------------------------------------------------ */
/* 문의                                                                */
/* ------------------------------------------------------------------ */

function repo_save_contact(string $name, string $email, string $message): bool
{
    if (db_ready()) {
        $stmt = db()->prepare('INSERT INTO contacts (name, email, message, created_at) VALUES (:n, :e, :m, NOW())');
        return $stmt->execute(['n' => $name, 'e' => $email, 'm' => $message]);
    }

    // DB가 없을 때는 파일로 남깁니다. (웹에서 직접 열리지 않도록 data/.htaccess 로 차단)
    $line = json_encode([
        'name'       => $name,
        'email'      => $email,
        'message'    => $message,
        'created_at' => date('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE);

    return (bool) @file_put_contents(ROOT_PATH . '/data/contacts.jsonl', $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function repo_contacts(int $limit = 200): array
{
    if (db_ready()) {
        $stmt = db()->prepare('SELECT * FROM contacts ORDER BY created_at DESC, id DESC LIMIT ' . (int) $limit);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    $file = ROOT_PATH . '/data/contacts.jsonl';
    if (!is_file($file)) {
        return [];
    }
    $rows = [];
    foreach (array_filter(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []) as $i => $line) {
        $row = json_decode($line, true);
        if (is_array($row)) {
            $row['id'] = $i + 1;
            $rows[] = $row;
        }
    }
    return array_slice(array_reverse($rows), 0, $limit);
}

/* ------------------------------------------------------------------ */
/* 통계 (관리자 대시보드)                                              */
/* ------------------------------------------------------------------ */

function repo_stats(): array
{
    $all     = repo_projects(['include_private' => true, 'sort' => 'curated']);
    $public  = array_filter($all, static fn($p) => (int) $p['is_public'] === 1);

    return [
        'total'      => count($all),
        'public'     => count($public),
        'private'    => count($all) - count($public),
        'contacts'   => count(repo_contacts()),
        'categories' => count(repo_categories()),
        'recent'     => array_slice(repo_projects(['include_private' => true, 'sort' => 'new']), 0, 5),
    ];
}
