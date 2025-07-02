<?php
// 모든 에러를 화면에 표시하도록 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$resource_id = $_GET['id'] ?? null;
if (!$resource_id) {
    echo "잘못된 접근입니다.";
    exit();
}

// 자료 조회 준비
$stmt = $conn->prepare("
    SELECT resources.*, users.username
    FROM resources
    JOIN users ON resources.user_id = users.id
    WHERE resources.id = ?
");
if (!$stmt) {
    die("Prepare 오류: " . $conn->error);
}
$stmt->bind_param("i", $resource_id);
if (!$stmt->execute()) {
    die("Execute 오류: " . $stmt->error);
}
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    echo "자료를 찾을 수 없습니다.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="../index.css">
    <style>
        /* 로그인용 중앙 고정 레이아웃 해제 */
        html, body {
            height: auto !important;
            display: block !important;
            overflow-y: auto !important;
        }
        .view-container {
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-family: 'Segoe UI', sans-serif;
        }
        .view-container h2 {
            margin-top: 0;
            font-size: 1.8em;
            color: #333;
        }
        .meta {
            color: #888;
            font-size: 0.9em;
            margin-bottom: 20px;
        }
        .post-content {
            white-space: pre-wrap;
            line-height: 1.6;
            font-size: 1.1em;
            margin-bottom: 20px;
        }
        .file-download {
            margin-bottom: 20px;
        }
        .file-download a {
            color: #007bff;
            text-decoration: none;
        }
        .file-download a:hover {
            text-decoration: underline;
        }
        .actions {
            margin-bottom: 30px;
        }
        .actions a {
            margin-right: 10px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
        }
        .actions .btn-edit { background-color: #0d6efd; }
        .actions .btn-delete { background-color: #dc3545; }
        .actions .btn-edit:hover { background-color: #0056b3; }
        .actions .btn-delete:hover { background-color: #b52a37; }
        .btn-back {
            display: inline-block;
            margin-bottom: 40px;
            color: #007bff;
            text-decoration: none;
        }
        .btn-back:hover { text-decoration: underline; }
        .comment-box { margin-top: 40px; }
        .comment-box h3 { margin-bottom: 15px; color: #333; }
        .comment-box textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
            font-size: 1em;
        }
        .comment-box button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #198754;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        .comment-box button:hover { background-color: #157347; }
        .comments-list { margin-top: 30px; }
        .comment-item {
            background-color: #f1f3f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .comment-meta {
            font-size: 0.85em;
            color: #555;
            margin-bottom: 8px;
        }
        .comment-controls a {
            margin-left: 10px;
            font-size: 0.85em;
            text-decoration: none;
            color: #0d6efd;
        }
        .comment-controls a.delete { color: #dc3545; }
        .comment-controls a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="view-container">
    <h2><?= htmlspecialchars($post['title']) ?></h2>
    <div class="meta">
        작성자: <?= htmlspecialchars($post['username']) ?> |
        작성일: <?= $post['created_at'] ?>
    </div>

    <div class="post-content">
        <?= nl2br(htmlspecialchars($post['content'])) ?>
    </div>

    <?php if ($post['original_filename']): ?>
        <div class="file-download">
            📎 
            <a href="download.php?file=<?= urlencode($post['filename']) ?>&name=<?= urlencode($post['original_filename']) ?>">
                <?= htmlspecialchars($post['original_filename']) ?> 다운로드
            </a>
        </div>
    <?php endif; ?>

    <div class="actions">
        <?php if ($_SESSION['user_id'] === $post['user_id']): ?>
            <a class="btn-edit" href="edit.php?id=<?= $resource_id ?>">✏ 수정</a>
            <a class="btn-delete" href="delete.php?id=<?= $resource_id ?>" onclick="return confirm('정말 삭제하시겠습니까?')">❌ 삭제</a>
        <?php endif; ?>
    </div>

    <a class="btn-back" href="list.php">← 목록으로 돌아가기</a>

    <div class="comment-box">
        <h3>💬 댓글 작성</h3>
        <form action="comments/store.php" method="post">
            <input type="hidden" name="post_id" value="<?= htmlspecialchars($resource_id) ?>">
            <textarea name="comment" rows="3" required></textarea><br>
            <button type="submit">댓글 작성</button>
        </form>
    </div>

    <div class="comments-list">
        <h3>📃 댓글 목록</h3>
        <?php
        $comment_stmt = $conn->prepare("
            SELECT rc.*, u.username
            FROM resource_comments AS rc
            JOIN users AS u ON rc.user_id = u.id
            WHERE rc.resource_id = ?
            ORDER BY rc.created_at ASC
        ");
        if (!$comment_stmt) {
            die("댓글 조회 Prepare 오류: " . $conn->error);
        }
        $comment_stmt->bind_param("i", $resource_id);
        if (!$comment_stmt->execute()) {
            die("댓글 조회 Execute 오류: " . $comment_stmt->error);
        }
        $comment_result = $comment_stmt->get_result();

        while ($c = $comment_result->fetch_assoc()):
        ?>
            <div class="comment-item">
                <div class="comment-meta">
                    <strong><?= htmlspecialchars($c['username']) ?></strong>
                    (<?= $c['created_at'] ?>)
                    <?php if ($_SESSION['user_id'] === $c['user_id']): ?>
                        <span class="comment-controls">
                            <a href="comments/edit.php?id=<?= $c['id'] ?>">✏ 수정</a>
                            <a class="delete" href="comments/delete.php?id=<?= $c['id'] ?>" onclick="return confirm('정말 삭제하시겠습니까?')">❌ 삭제</a>
                        </span>
                    <?php endif; ?>
                </div>
                <div><?= nl2br(htmlspecialchars($c['comment'])) ?></div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
