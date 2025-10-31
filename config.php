<?php
// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 에러 표시 (개발 환경)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 타임존 설정
date_default_timezone_set('Asia/Seoul');

// 경로 설정 - 이 부분이 중요!
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/bakers');
define('ROOT_URL', 'http://localhost/bakers');

// 데이터베이스 설정
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bakers_db');

// 데이터베이스 연결
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("데이터베이스 연결 실패: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("데이터베이스 오류: " . $e->getMessage());
}

// 유틸리티 함수
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . ROOT_URL . '/auth/login.php');
        exit;
    }
}

function get_user_info() {
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'user_id' => $_SESSION['user_login_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'level' => $_SESSION['user_level']
    ];
}

function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function url($path = '') {
    return ROOT_URL . '/' . ltrim($path, '/');
}
?>