<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../../db.php';

$comment_id = $_GET['id'] ?? null;
if (!$comment_id) {
    echo "잘못된 접근입니다.";
    exit();
}

// 댓글 조회 및 권한 확인
$stmt = $conn->prepare("SELECT * FROM resource_comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$comment = $stmt->get_result()->fetch_assoc();

if (!$comment || $comment['user_id'] != $_SESSION['user_id']) {
    echo "댓글을 삭제할 권한이 없습니다.";
    exit();
}

$resource_id = $comment['resource_id'];

// 댓글 삭제
$stmt = $conn->prepare("DELETE FROM resource_comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
if ($stmt->execute()) {
    header("Location: ../view.php?id=" . $resource_id);
    exit();
} else {
    echo "댓글 삭제 실패: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
    exit();
}
