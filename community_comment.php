<?php
session_start();
header('Content-Type: application/json');

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
    exit;
}

// POST 방식만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
    exit;
}

// 데이터베이스 연결
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bakers');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '데이터베이스 연결 실패']);
    exit;
}

// 데이터 받기
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$content = trim($_POST['content'] ?? '');
$user_id = $_SESSION['user_id'];

// 유효성 검사
if ($post_id === 0 || empty($content)) {
    echo json_encode(['success' => false, 'message' => '댓글 내용을 입력해주세요.']);
    exit;
}

// 회원 ID 조회
$member_query = "SELECT id FROM members WHERE user_id = ? AND status = 'active'";
$stmt = $conn->prepare($member_query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => '회원 정보를 찾을 수 없습니다.']);
    exit;
}

$member = $result->fetch_assoc();
$member_id = $member['id'];
$stmt->close();

// 게시글 존재 확인
$post_check = "SELECT id FROM community_posts WHERE id = ? AND status = 'active'";
$stmt = $conn->prepare($post_check);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => '존재하지 않는 게시글입니다.']);
    exit;
}
$stmt->close();

// 댓글 등록
$insert_query = "INSERT INTO community_comments (post_id, member_id, content, created_at, status) 
                 VALUES (?, ?, ?, NOW(), 'active')";
$stmt = $conn->prepare($insert_query);
$stmt->bind_param("iis", $post_id, $member_id, $content);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '댓글이 작성되었습니다.']);
} else {
    echo json_encode(['success' => false, 'message' => '댓글 작성에 실패했습니다.']);
}

$stmt->close();
$conn->close();
?>