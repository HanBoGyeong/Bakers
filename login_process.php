<?php
session_start();

// 오류 표시 (개발 중만 켜두세요)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB 연결
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "bakers";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("<script>alert('데이터베이스 연결 실패: " . addslashes($conn->connect_error) . "'); history.back();</script>");
}
$conn->set_charset("utf8mb4");

// POST 방식만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<script>alert('잘못된 접근입니다.'); history.back();</script>");
}

$user_id = trim($_POST['user_id'] ?? '');
$password = $_POST['password'] ?? '';

if ($user_id === '' || $password === '') {
    die("<script>alert('아이디와 비밀번호를 모두 입력해주세요.'); history.back();</script>");
}

// 회원 조회
$stmt = $conn->prepare("SELECT id, user_id, password, nickname, level, status FROM members WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<script>alert('존재하지 않는 아이디입니다.'); history.back();</script>");
}

$user = $result->fetch_assoc();
$stmt->close();

// 계정 상태 확인
if ($user['status'] !== 'active') {
    die("<script>alert('비활성화된 계정입니다. 관리자에게 문의해주세요.'); history.back();</script>");
}

// 비밀번호 검증
if (!password_verify($password, $user['password'])) {
    die("<script>alert('비밀번호가 올바르지 않습니다.'); history.back();</script>");
}

// 로그인 성공 시 세션 생성
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['nickname'] = $user['nickname'];
$_SESSION['level'] = $user['level'];

// 마지막 로그인 시간 갱신
$update = $conn->prepare("UPDATE members SET last_login = NOW() WHERE id = ?");
$update->bind_param("i", $user['id']);
$update->execute();
$update->close();

$conn->close();

// 로그인 성공 후 메인 페이지 이동
echo "<script>alert('로그인 성공! 환영합니다, " . addslashes($user['nickname']) . "님 😊'); location.href='index.php';</script>";
exit;
?>
