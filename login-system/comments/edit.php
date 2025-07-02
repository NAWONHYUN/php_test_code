<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$comment_id = $_GET['id'] ?? null;

$stmt = $conn->prepare("SELECT * FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();

if (!$comment || $comment['user_id'] != $_SESSION['user_id']) {
    echo "댓글을 수정할 수 없습니다.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>댓글 수정</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .comment-edit-container {
            max-width: 600px;
            margin: 80px auto;
            background-color: white;
            padding: 30px 35px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h3 {
            margin-bottom: 20px;
            color: #333;
        }

        textarea {
            width: 100%;
            padding: 12px;
            font-size: 1em;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        .btn-group {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        button[type="submit"] {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .cancel-link {
            align-self: center;
            text-decoration: none;
            background-color: #6c757d;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.95em;
        }

        .cancel-link:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="comment-edit-container">
    <h3>✏ 댓글 수정</h3>
    <form action="update.php" method="post">
        <input type="hidden" name="id" value="<?= $comment['id'] ?>">
        <input type="hidden" name="post_id" value="<?= $comment['post_id'] ?>">

        <textarea name="comment" rows="5" required><?= htmlspecialchars($comment['comment']) ?></textarea>

        <div class="btn-group">
            <button type="submit">수정 완료</button>
            <a class="cancel-link" href="../posts/view.php?id=<?= $comment['post_id'] ?>">취소</a>
        </div>
    </form>
</div>

</body>
</html>
