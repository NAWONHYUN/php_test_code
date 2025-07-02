<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$user_id = $_SESSION['user_id'];

$original_filename = $_FILES['upload']['name'] ?? null;
$temp_file = $_FILES['upload']['tmp_name'] ?? null;
$stored_filename = null;

if ($original_filename && $temp_file) {
    $original_filename = basename($original_filename);
    $stored_filename = uniqid() . '_' . $original_filename;

    $upload_dir = __DIR__ . "/uploads/"; // 절대경로 사용 권장

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    move_uploaded_file($temp_file, $upload_dir . $stored_filename);
}

$stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, filename, original_filename) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $title, $content, $stored_filename, $original_filename);

if ($stmt->execute()) {
    header("Location: list.php");
    exit();
} else {
    echo "게시글 저장 실패: " . $stmt->error;
}
?>
