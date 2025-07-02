<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>로그인</title>
    <link rel="stylesheet" type="text/css" href="index.css">
</head>
<body class ="login-page">
    <div class="login-box">
        <h2>로그인</h2>

        <?php if (isset($_GET['error'])) { ?>
            <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php } ?>

        <form action="login.php" method="post">
            <label for="uname">아이디</label>
            <input type="text" name="uname" placeholder="아이디 입력" required>

            <label for="password">비밀번호</label>
            <input type="password" name="password" placeholder="비밀번호 입력" required>

            <button type="submit">로그인</button>

            <p class="signup-link">
                아직 회원이 아니신가요? <a href="signup.php">회원 가입</a>
            </p>
        </form>
    </div>
</body>
</html>
