<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 세션 초기화 및 종료
session_unset();
session_destroy();

// 로그인 페이지로 이동
header("Location: login.php");
exit;
