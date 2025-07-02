<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
    <link rel="stylesheet" type="text/css" href="index.css">
</head>
<body class="login-page">
    <div class="login-box">
        <h2>회원가입</h2>

        <form action="register.php" method="post">
            <label for="uname">아이디</label>
            <input type="text" name="uname" placeholder="아이디 입력" required>

            <label for="password">비밀번호</label>
            <input type="password" name="password" placeholder="비밀번호 입력" required>

            <button type="submit">가입하기</button>

            <p class="signup-link">
                이미 계정이 있으신가요? <a href="index.php">로그인</a>
            </p>
        </form>
    </div>
</body>
</html>
