<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['uname'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id']; //  추가: 게시판 접근용 세션 정보
            header("Location: welcome.php");
            exit();
        } else {
            header("Location: index.php?error=비밀번호가 틀렸습니다.");
            exit();
        }
    } else {
        header("Location: index.php?error=사용자를 찾을 수 없습니다.");
        exit();
    }
}
?>
