<?php
// 1) 에러 출력 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// 2) 올바른 경로로 수정: resources/comments/ -> ../../db.php
include '../../db.php';

// 3) POST & SESSION 데이터 디버깅 출력
echo "<pre>POST 데이터:\n";
var_dump($_POST);
echo "\nSESSION 데이터:\n";
var_dump($_SESSION);
echo "</pre>";

// 4) 폼에서 넘어온 값 가져오기
$comment     = $_POST['comment']     ?? '';
$resource_id = $_POST['post_id']     ?? null;
$user_id     = $_SESSION['user_id'];

// 5) 유효성 검사
if (!$comment || !$resource_id) {
    die("❌ 내용이 없거나 자료 ID가 누락되었습니다.");
}

// 6) Prepare 단계 디버깅
$sql = "
    INSERT INTO resource_comments
        (resource_id, user_id, comment)
    VALUES
        (?, ?, ?)
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare 오류: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'));
}

// 7) Bind & Execute 단계 디버깅
$stmt->bind_param("iis", $resource_id, $user_id, $comment);
if (!$stmt->execute()) {
    die("Execute 오류: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8'));
}

// 8) 성공 시 리다이렉트
header("Location: ../view.php?id=" . $resource_id);
exit();
?>
