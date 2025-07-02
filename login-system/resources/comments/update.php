<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../../db.php';

$comment_id  = $_POST['id']      ?? null;
$resource_id = $_POST['post_id'] ?? null;
$content     = $_POST['comment'] ?? '';
$user_id     = $_SESSION['user_id'];

if (!$comment_id || !$content) {
    echo "잘못된 요청입니다.";
    exit();
}

// 권한 확인
$stmt = $conn->prepare("SELECT * FROM resource_comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$orig = $stmt->get_result()->fetch_assoc();
if (!$orig || $orig['user_id'] != $user_id) {
    echo "댓글을 수정할 권한이 없습니다.";
    exit();
}

$stmt = $conn->prepare("
    UPDATE resource_comments
    SET comment = ?
    WHERE id = ?
");
$stmt->bind_param("si", $content, $comment_id);

if ($stmt->execute()) {
    header("Location: ../view.php?id=" . $resource_id);
    exit();
} else {
    echo "댓글 수정 실패: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
    exit();
}
