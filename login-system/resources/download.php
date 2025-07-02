<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';

// GET 파라미터로 넘어온 파일명/원본명
$stored   = $_GET['file'] ?? '';
$original = $_GET['name'] ?? 'downloaded_file';

// 저장된 파일 경로
$uploadDir = __DIR__ . '/uploads/';
$filepath  = $uploadDir . $stored;

// 파일 존재 여부 체크
if (!$stored || !file_exists($filepath)) {
    echo "❌ 파일을 찾을 수 없습니다.";
    exit();
}

// 다운로드 헤더 전송
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($original) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));
flush();

// 파일 출력
readfile($filepath);
exit();
?>
