<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    echo "잘못된 접근입니다.";
    exit();
}

// 게시글 불러오기
$stmt = $conn->prepare("
    SELECT posts.*, users.username 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    WHERE posts.id = ?
");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    echo "게시글을 찾을 수 없습니다.";
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
            color: #333;
            font-size: 1.8em;
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
            margin-top: 10px;
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
            text-decoration: none;
            color: #fff;
        }
        .actions .btn-edit {
            background-color: #0d6efd;
        }
        .actions .btn-delete {
            background-color: #dc3545;
        }
        .actions .btn-edit:hover {
            background-color: #0056b3;
        }
        .actions .btn-delete:hover {
            background-color: #b52a37;
        }
        .btn-back {
            display: inline-block;
            margin-bottom: 40px;
            color: #007bff;
            text-decoration: none;
        }
        .btn-back:hover {
            text-decoration: underline;
        }
        .comment-box {
            margin-top: 40px;
        }
        .comment-box h3 {
            margin-bottom: 15px;
            color: #333;
        }
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
            cursor: pointer;
            font-weight: bold;
        }
        .comment-box button:hover {
            background-color: #157347;
        }
        .comments-list {
            margin-top: 30px;
        }
        .comment-item {
            padding: 15px;
            background-color: #f1f3f5;
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
        .comment-controls a.delete {
            color: #dc3545;
        }
        .comment-controls a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="view-container">
    <h2><?= htmlspecialchars($post['title']) ?></h2>
    <div class="meta">
        작성자: <?= htmlspecialchars($post['username']) ?> | 작성일: <?= $post['created_at'] ?>
    </div>

    <div class="post-content">
        <?= nl2br(htmlspecialchars($post['content'])) ?>
    </div>

    <?php if ($post['original_filename']): ?>
        <div class="file-download">
            📎 <a href="../download.php?file=<?= urlencode($post['filename']) ?>&name=<?= urlencode($post['original_filename']) ?>">
                <?= htmlspecialchars($post['original_filename']) ?> 다운로드
            </a>
        </div>
    <?php endif; ?>

    <div class="actions">
        <?php if ($_SESSION['user_id'] == $post['user_id']): ?>
            <a class="btn-edit" href="edit.php?id=<?= $post_id ?>">✏ 수정</a>
            <a class="btn-delete" href="delete.php?id=<?= $post_id ?>" onclick="return confirm('정말 삭제하시겠습니까?')">❌ 삭제</a>
        <?php endif; ?>
    </div>

    <a class="btn-back" href="list.php">← 목록으로 돌아가기</a>

    <div class="comment-box">
        <h3>💬 댓글 작성</h3>
        <form action="../comments/stores.php" method="post">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <textarea name="comment" rows="3" placeholder="댓글을 입력하세요" required></textarea><br>
            <button type="submit">댓글 작성</button>
        </form>
    </div>

    <div class="comments-list">
        <h3>📃 댓글 목록</h3>
        <?php
        $comment_stmt = $conn->prepare("
            SELECT comments.*, users.username 
            FROM comments 
            JOIN users ON comments.user_id = users.id 
            WHERE post_id = ? 
            ORDER BY created_at ASC
        ");
        $comment_stmt->bind_param("i", $post['id']);
        $comment_stmt->execute();
        $comment_result = $comment_stmt->get_result();

        while ($comment = $comment_result->fetch_assoc()):
        ?>
            <div class="comment-item">
                <div class="comment-meta">
                    <strong><?= htmlspecialchars($comment['username']) ?></strong> 
                    (<?= $comment['created_at'] ?>)
                    <?php if ($_SESSION['user_id'] == $comment['user_id']): ?>
                        <span class="comment-controls">
                            <a href="../comments/edit.php?id=<?= $comment['id'] ?>">✏ 수정</a>
                            <a class="delete" href="../comments/delete.php?id=<?= $comment['id'] ?>" onclick="return confirm('정말 삭제하시겠습니까?')">❌ 삭제</a>
                        </span>
                    <?php endif; ?>
                </div>
                <div><?= nl2br(htmlspecialchars($comment['comment'])) ?></div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
