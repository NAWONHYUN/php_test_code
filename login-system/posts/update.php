<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$post_id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$user_id = $_SESSION['user_id'];

// 기존 게시글 정보 가져오기
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    echo "게시글을 찾을 수 없습니다.";
    exit();
}

if ($post['user_id'] != $user_id) {
    echo "수정 권한이 없습니다.";
    exit();
}

// 파일 처리
$new_original_filename = $_FILES['upload']['name'] ?? null;
$new_temp_file = $_FILES['upload']['tmp_name'] ?? null;

if ($new_original_filename && $new_temp_file) {
    $new_stored_filename = uniqid() . "_" . basename($new_original_filename);
    $upload_dir = "uploads/";

    // uploads 폴더 없으면 생성
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    move_uploaded_file($new_temp_file, $upload_dir . $new_stored_filename);

    // 이전 파일은 삭제할 수도 있음 (옵션)

    // 새 파일 정보 포함하여 업데이트
    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, filename = ?, original_filename = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $content, $new_stored_filename, $new_original_filename, $post_id);
} else {
    // 파일 변경 없이 제목, 내용만 수정
    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $post_id);
}

if ($stmt->execute()) {
    header("Location: view.php?id=" . $post_id);
    exit();
} else {
    echo "게시글 수정 실패: " . $stmt->error;
}
?>
