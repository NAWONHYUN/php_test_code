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

// 기존 자료 불러오기
$stmt = $conn->prepare("SELECT * FROM resources WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post   = $result->fetch_assoc();

if (!$post) {
    echo "자료를 찾을 수 없습니다.";
    exit();
}

// 작성자 본인 확인
if ($_SESSION['user_id'] != $post['user_id']) {
    echo "이 자료를 수정할 권한이 없습니다.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>자료 수정</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .form-container {
            max-width: 700px;
            margin: 50px auto;
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 30px;
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            margin-top: 20px;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 1em;
        }
        input[type="file"] {
            margin-top: 10px;
        }
        .current-file {
            margin-top: 10px;
            font-size: 0.9em;
            color: #555;
        }
        .btn-group {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }
        button[type="submit"] {
            padding: 12px 20px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1em;
            cursor: pointer;
        }
        .cancel-link {
            display: inline-block;
            padding: 11px 16px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1em;
        }
        .cancel-link:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>✏ 자료 수정</h2>

    <form action="update.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>">

        <label for="title">제목</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title']) ?>" required>

        <label for="content">내용</label>
        <textarea name="content" id="content" rows="10" required><?= htmlspecialchars($post['content']) ?></textarea>

        <?php if ($post['original_filename']) : ?>
            <div class="current-file">현재 첨부파일: <?= htmlspecialchars($post['original_filename']) ?></div>
        <?php endif; ?>

        <label for="upload">새 파일 첨부 (선택)</label>
        <input type="file" name="upload" id="upload">

        <div class="btn-group">
            <button type="submit">수정 완료</button>
            <a class="cancel-link" href="view.php?id=<?= htmlspecialchars($post['id']) ?>">취소</a>
        </div>
    </form>
</div>

</body>
</html>
