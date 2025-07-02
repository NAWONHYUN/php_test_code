<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$comment_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

// 댓글 존재 및 소유자 확인
$stmt = $conn->prepare("SELECT * FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();

if (!$comment) {
    echo "❌ 댓글이 존재하지 않습니다.";
    exit();
}

if ($comment['user_id'] != $user_id) {
    echo "❌ 삭제 권한이 없습니다.";
    exit();
}

$post_id = $comment['post_id'];

// 삭제 처리
$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();

header("Location: ../posts/view.php?id=" . $post_id);
exit();
?>
