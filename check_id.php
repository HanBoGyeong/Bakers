<?php
// 에러 표시 (디버깅용)
error_reporting(E_ALL);
ini_set('display_errors', 0); // JSON 응답이므로 화면에 에러 표시 안함

// 데이터베이스 연결 설정
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bakers');

// 데이터베이스 연결
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => '데이터베이스 연결 실패'
    ]);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => '잘못된 요청 방식입니다.'
    ]);
    exit;
}

// user_id 받기
$user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';

// 빈 값 체크
if (empty($user_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID를 입력해주세요.'
    ]);
    exit;
}

// 길이 체크
if (strlen($user_id) < 4 || strlen($user_id) > 20) {
    echo json_encode([
        'success' => false,
        'message' => 'ID는 4-20자 사이여야 합니다.'
    ]);
    exit;
}

// 형식 체크
if (!preg_match('/^[a-zA-Z0-9]+$/', $user_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID는 영문, 숫자만 사용 가능합니다.'
    ]);
    exit;
}

// 데이터베이스 처리
try {
    // Prepared Statement로 중복 확인
    $stmt = $conn->prepare("SELECT id FROM members WHERE user_id = ?");
    
    if (!$stmt) {
        throw new Exception("쿼리 준비 실패: " . $conn->error);
    }
    
    $stmt->bind_param("s", $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("쿼리 실행 실패: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => '이미 사용중인 ID입니다.'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => '사용 가능한 ID입니다.'
        ]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("check_id.php Error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다.'
    ]);
}

$conn->close();
?>