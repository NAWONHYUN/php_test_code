<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$post_id = $_GET['id'] ?? null;

$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post || $post['user_id'] != $_SESSION['user_id']) {
    echo "삭제 권한이 없거나 게시글이 존재하지 않습니다.";
    exit();
}

// ✅ 먼저 연결된 댓글 삭제
$del_comments = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
$del_comments->bind_param("i", $post_id);
$del_comments->execute();

// 첨부파일 삭제
if ($post['filename']) {
    $filepath = __DIR__ . "/uploads/" . $post['filename'];
    if (file_exists($filepath)) {
        unlink($filepath);
    }
}

// 게시글 삭제
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();

header("Location: list.php");
exit();
?>
