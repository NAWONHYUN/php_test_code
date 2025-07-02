<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$title             = $_POST['title']   ?? '';
$content           = $_POST['content'] ?? '';
$user_id           = $_SESSION['user_id'];

$original_filename = $_FILES['upload']['name']     ?? null;
$temp_file         = $_FILES['upload']['tmp_name'] ?? null;
$stored_filename   = null;

if ($original_filename && $temp_file) {
    $original_filename = basename($original_filename);
    $stored_filename   = uniqid() . '_' . $original_filename;

    $upload_dir = __DIR__ . '/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    move_uploaded_file($temp_file, $upload_dir . $stored_filename);
}

$stmt = $conn->prepare("
    INSERT INTO resources
        (user_id, title, content, filename, original_filename)
    VALUES
        (?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "issss",
    $user_id,
    $title,
    $content,
    $stored_filename,
    $original_filename
);

if ($stmt->execute()) {
    header("Location: list.php");
    exit();
} else {
    echo "자료 저장 실패: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
}
?>
