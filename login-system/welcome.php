<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>환영합니다</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <style>
        .welcome-box {
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            margin: 100px auto;
            text-align: center;
        }

        .welcome-box h2 {
            margin-bottom: 25px;
            color: #333;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-group form button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-board {
            background-color: #007bff;
            color: white;
        }

        .btn-board:hover {
            background-color: #0056b3;
        }

        .btn-logout {
            background-color: #dc3545;
            color: white;
        }

        .btn-logout:hover {
            background-color: #b52a37;
        }
    </style>
</head>
<body>

    <div class="welcome-box">
        <h2>환영합니다, <?= htmlspecialchars($_SESSION['username']) ?>님!</h2>
        <div class="btn-group">
            <!-- 게시판 이동 -->
            <form action="posts/list.php" method="get">
                <button class="btn-board" type="submit">📋 자유 게시판으로 이동</button>
            </form>
            <form action="resources/list.php" method="get">
                <button class="btn-board" type="submit">📋 자료공유 게시판으로 이동</button>
            </form>

            <!-- 로그아웃 -->
            <form action="logout.php" method="post">
                <button class="btn-logout" type="submit">🚪 로그아웃</button>
            </form>
        </div>
    </div>

</body>
</html>
