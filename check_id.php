<?php
require_once '../db.php';
header('Content-Type: application/json; charset=utf-8');

$user_id = trim($_POST['user_id'] ?? '');

if ($user_id === '') {
    echo json_encode(['success' => false, 'message' => '아이디를 입력해주세요.']);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM members WHERE user_id = ?");
$stmt->execute([$user_id]);
$count = $stmt->fetchColumn();

if ($count > 0) {
    echo json_encode(['success' => false, 'message' => '이미 사용 중인 아이디입니다.']);
} else {
    echo json_encode(['success' => true, 'message' => '사용 가능한 아이디입니다.']);
}
