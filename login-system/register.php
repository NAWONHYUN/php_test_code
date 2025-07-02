<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['uname'] ?? '');
    $rawPwd   = $_POST['password'] ?? '';

    // 간단한 빈 값 체크 (서버 측)
    if ($username === '' || $rawPwd === '') {
        echo "<script>alert('아이디와 비밀번호를 모두 입력하세요!'); history.back();</script>";
        exit();
    }

    // 1) 아이디 중복 확인
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // 이미 같은 아이디가 존재할 때
        echo "<script>alert('이미 존재하는 아이디 입니다!!'); history.back();</script>";
        $checkStmt->close();
        $conn->close();
        exit();
    }
    $checkStmt->close();

    // 2) 중복이 아니면 회원가입 처리
    $hashedPwd = password_hash($rawPwd, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPwd);

    if ($stmt->execute()) {
        // 가입 성공 → 메인 페이지로 이동
        header("Location: index.php");
        exit();
    } else {
        // 예외 처리 (DB 오류 등)
        echo "<script>alert('회원가입 실패: {$stmt->error}'); history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

