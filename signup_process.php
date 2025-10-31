<?php
require_once '../db.php';

$user_id  = trim($_POST['user_id']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$nickname = trim($_POST['nickname']);
$phone    = trim($_POST['phone']);

if (!$user_id || !$nickname || !$phone) {
    echo "<script>alert('모든 항목을 입력해주세요.'); history.back();</script>";
    exit;
}

$stmt = $pdo->prepare("INSERT INTO members (user_id, password, nickname, phone, created_at)
                       VALUES (?, ?, ?, ?, NOW())");
$stmt->execute([$user_id, $password, $nickname, $phone]);

header("Location: signup_success.php");
exit;
