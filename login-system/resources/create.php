<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>자료 올리기</title>
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
            margin-top: 8px;
        }

        button {
            margin-top: 25px;
            padding: 12px 20px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1em;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-link {
            margin-left: 15px;
            text-decoration: none;
            color: #6c757d;
            font-size: 0.95em;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>📄 자료 올리기</h2>

    <form action="store.php" method="post" enctype="multipart/form-data">
        <label for="title">제목</label>
        <input type="text" name="title" id="title" required>

        <label for="content">내용</label>
        <textarea name="content" id="content" rows="10" required></textarea>

        <label for="upload">파일 첨부</label>
        <input type="file" name="upload" id="upload">

        <button type="submit">업로드</button>
        <a class="back-link" href="list.php">← 목록으로</a>
    </form>
</div>

</body>
</html>
