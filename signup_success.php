<?php
// 에러 표시 (디버깅용)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// POST 데이터 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<script>alert('잘못된 접근입니다.'); history.back();</script>");
}

// 데이터베이스 연결 설정
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bakers');

try {
    // MySQLi 연결
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("데이터베이스 연결 실패: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("<script>alert('데이터베이스 연결 오류: " . addslashes($e->getMessage()) . "'); history.back();</script>");
}

// POST 데이터 받기 및 검증
$user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$nickname = isset($_POST['nickname']) ? trim($_POST['nickname']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

// 빈 값 체크
if (empty($user_id) || empty($password) || empty($nickname) || empty($phone)) {
    $conn->close();
    die("<script>alert('모든 항목을 입력해주세요.'); history.back();</script>");
}

// 아이디 형식 검증
if (!preg_match('/^[a-zA-Z0-9]{4,20}$/', $user_id)) {
    $conn->close();
    die("<script>alert('아이디는 4-20자의 영문과 숫자만 사용 가능합니다.'); history.back();</script>");
}

// 비밀번호 길이 검증
if (strlen($password) < 8) {
    $conn->close();
    die("<script>alert('비밀번호는 8자 이상이어야 합니다.'); history.back();</script>");
}

// 휴대폰번호 형식 검증
if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
    $conn->close();
    die("<script>alert('휴대폰번호는 10-11자리 숫자만 입력해주세요.'); history.back();</script>");
}

// 비밀번호 해시화
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // 중복 체크 (추가 안전장치)
    $check_stmt = $conn->prepare("SELECT id FROM members WHERE user_id = ?");
    $check_stmt->bind_param("s", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $check_stmt->close();
        $conn->close();
        die("<script>alert('이미 사용중인 아이디입니다.'); history.back();</script>");
    }
    $check_stmt->close();
    
    // 회원 정보 삽입 (새 테이블 구조에 맞춤)
    // members 테이블: user_id, password, nickname, phone, email(선택)
    $stmt = $conn->prepare("INSERT INTO members (user_id, password, nickname, phone, created_at) VALUES (?, ?, ?, ?, NOW())");
    
    if (!$stmt) {
        throw new Exception("쿼리 준비 실패: " . $conn->error);
    }
    
    $stmt->bind_param("ssss", $user_id, $password_hash, $nickname, $phone);
    
    if (!$stmt->execute()) {
        throw new Exception("회원가입 실패: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
    // 성공 시 성공 페이지로 이동
    header("Location: signup_success.php");
    exit;
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->close();
    }
    error_log("Signup Error: " . $e->getMessage());
    die("<script>alert('회원가입 중 오류가 발생했습니다: " . addslashes($e->getMessage()) . "'); history.back();</script>");
}
?>