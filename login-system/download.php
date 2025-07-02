<?php
$stored_filename = $_GET['file'] ?? null;
$original_filename = $_GET['name'] ?? 'downloaded_file.pdf';
$filepath = "posts/uploads/" . $stored_filename;

if (!$stored_filename || !file_exists($filepath)) {
    echo "❌ 파일을 찾을 수 없습니다.";
    exit();
}

// PDF 포함 모든 파일의 올바른 다운로드 처리를 위한 헤더 설정
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream'); // PDF를 바이너리로 처리
header('Content-Disposition: attachment; filename="' . basename($original_filename) . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));
flush(); // 출력 버퍼 비우기
readfile($filepath);
exit();
?>
