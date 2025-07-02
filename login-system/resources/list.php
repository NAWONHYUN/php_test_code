<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
include '../db.php';


$sort       = (isset($_GET['sort']) && $_GET['sort'] === 'asc') ? 'ASC' : 'DESC';
$search     = trim($_GET['search'] ?? '');
$userFilter = trim($_GET['user']   ?? '');


$whereClauses = [];
$types        = '';
$params       = [];

if ($search !== '') {
    $whereClauses[] = "(resources.title LIKE ? OR resources.content LIKE ?)";
    $types         .= 'ss';
    $params[]       = "%{$search}%";
    $params[]       = "%{$search}%";
}

if ($userFilter !== '') {
    $whereClauses[] = "users.username = ?";
    $types         .= 's';
    $params[]       = $userFilter;
}

// 3) SQL 빌드
$sql = "
    SELECT
        resources.id,
        resources.title,
        resources.created_at,
        users.username
    FROM resources
    JOIN users ON resources.user_id = users.id
";
if (!empty($whereClauses)) {
    $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
}
$sql .= " ORDER BY resources.created_at {$sort}";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare error: " . $conn->error);
}
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>자료공유 게시판</title>
  <link rel="stylesheet" href="../index.css">
  <style>
    html, body { height:auto!important; display:block!important; overflow-y:auto!important; }
    .board-container {
      width: 90%;
      max-width: 1200px;
      margin: 50px auto;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      font-family: 'Segoe UI', sans-serif;
    }
    .board-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }
    .board-header h2 { margin: 0; color: #333; }
    .user-info { font-size: 0.95em; color: #666; }
    .controls {
      margin-bottom: 20px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    .controls select, .controls input {
      padding: 6px 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 0.95em;
    }
    .controls button {
      padding: 6px 12px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .controls button:hover { background: #0056b3; }
    .post {
      padding: 15px 0;
      border-bottom: 1px solid #ddd;
    }
    .post:last-child { border-bottom: none; }
    .post-title a {
      font-size: 1.2em;
      font-weight: bold;
      color: #007bff;
      text-decoration: none;
    }
    .post-title a:hover { text-decoration: underline; }
    .post-meta {
      font-size: 0.9em;
      color: #666;
      margin-top: 4px;
    }
    .actions {
      margin-top: 30px;
      display: flex;
      justify-content: space-between;
    }
    .actions a {
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: bold;
      color: #fff;
      text-decoration: none;
    }
    .btn-create { background: #007bff; }
    .btn-create:hover { background: #0056b3; }
    .btn-logout { background: #dc3545; }
    .btn-logout:hover { background: #b52a37; }
  </style>
</head>
<body>

<div class="board-container">
  <div class="board-header">
    <h2>📋 자료공유 게시판 목록</h2>
    <div class="user-info">(id: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>)</div>
  </div>

  <!-- 정렬·검색 폼 -->
  <form class="controls" method="get">
    <select name="sort">
      <option value="desc" <?= $sort==='DESC'?'selected':'' ?>>최신순</option>
      <option value="asc"  <?= $sort==='ASC'?'selected':'' ?>>오래된순</option>
    </select>
    <input type="text" name="search" placeholder="제목·내용 검색" value="<?= htmlspecialchars($search) ?>">
    <input type="text" name="user" placeholder="작성자 검색" value="<?= htmlspecialchars($userFilter) ?>">
    <button type="submit">적용</button>
  </form>

  <!-- 게시글 목록 -->
  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="post">
        <div class="post-title">
          <a href="view.php?id=<?= $row['id'] ?>">
            <?= htmlspecialchars($row['title']) ?>
          </a>
        </div>
        <div class="post-meta">
          작성자: <?= htmlspecialchars($row['username']) ?> |
          작성일: <?= $row['created_at'] ?>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>조건에 맞는 게시글이 없습니다.</p>
  <?php endif; ?>

  <!-- 새 글 / 로그아웃 -->
  <div class="actions">
    <a href="create.php" class="btn-create">➕ 새 글 작성</a>
    <a href="../logout.php" class="btn-logout">🚪 로그아웃</a>
  </div>
</div>

</body>
</html>
