<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$comment_id = $_POST['id'] ?? null;
$post_id = $_POST['post_id'] ?? null;
$content = $_POST['comment'] ?? '';

$stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row || $row['user_id'] != $_SESSION['user_id']) {
    echo "수정 권한이 없습니다.";
    exit();
}

$stmt = $conn->prepare("UPDATE comments SET comment = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $content, $comment_id);
$stmt->execute();

header("Location: ../posts/view.php?id=" . $post_id);
exit();
?>
