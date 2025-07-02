<?php
// 기본값 (로컬 개발 환경용)
$host = '127.0.0.1';
$port = 3306;
$db   = 'user_system';
$user = 'root';
$pass = 'a01084763363@';

// 환경 변수 기반 설정 (Elastic Beanstalk 등)
if (getenv("RDS_HOSTNAME")) {
    $host = getenv("RDS_HOSTNAME");
    $port = getenv("RDS_PORT") ?: 3306;
    $db   = getenv("RDS_DB_NAME");
    $user = getenv("RDS_USERNAME");
    $pass = getenv("RDS_PASSWORD");
}

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}
?>
