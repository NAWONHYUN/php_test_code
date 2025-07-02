<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$comment = $_POST['comment'] ?? '';
$post_id = $_POST['post_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$comment || !$post_id) {
    echo "내용이 없거나 게시글 ID가 누락되었습니다.";
    exit();
}

$stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $post_id, $user_id, $comment);
$stmt->execute();

header("Location: ../posts/view.php?id=" . $post_id);
exit();
?>
