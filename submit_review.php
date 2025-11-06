<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
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

// POST 데이터 받기
$bakery_id = isset($_POST['bakery_id']) ? intval($_POST['bakery_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$member_id = $_SESSION['user_id'];

// 유효성 검사
if ($bakery_id <= 0) {
    echo json_encode(['success' => false, 'message' => '빵집을 선택해주세요.']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => '별점을 선택해주세요. (1-5)']);
    exit;
}

if (empty($content)) {
    echo json_encode(['success' => false, 'message' => '리뷰 내용을 입력해주세요.']);
    exit;
}

if (strlen($content) < 10) {
    echo json_encode(['success' => false, 'message' => '리뷰는 최소 10자 이상 작성해주세요.']);
    exit;
}

// 이미지 업로드 처리
$uploaded_images = [];
if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    $upload_dir = 'uploads/reviews/';
    
    // 업로드 디렉토리가 없으면 생성
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_file_size = 5 * 1024 * 1024; // 5MB
    
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        if (empty($tmp_name)) continue;
        
        $file_name = $_FILES['images']['name'][$key];
        $file_size = $_FILES['images']['size'][$key];
        $file_type = $_FILES['images']['type'][$key];
        
        // 파일 타입 확인
        if (!in_array($file_type, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => '이미지 파일만 업로드 가능합니다. (JPG, PNG, GIF)']);
            exit;
        }
        
        // 파일 크기 확인
        if ($file_size > $max_file_size) {
            echo json_encode(['success' => false, 'message' => '이미지 파일은 5MB 이하만 가능합니다.']);
            exit;
        }
        
        // 파일명 생성 (중복 방지)
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $ext;
        $target_path = $upload_dir . $new_filename;
        
        // 파일 이동
        if (move_uploaded_file($tmp_name, $target_path)) {
            $uploaded_images[] = $new_filename;
        }
        
        // 최대 3장 제한
        if (count($uploaded_images) >= 3) break;
    }
}

// 이미지 배열을 JSON으로 변환
$images_json = !empty($uploaded_images) ? json_encode($uploaded_images) : null;

try {
    // 트랜잭션 시작
    $conn->begin_transaction();
    
    // 리뷰 삽입
    $stmt = $conn->prepare("INSERT INTO reviews (bakery_id, member_id, rating, title, content, images, visit_date, created_at) 
                            VALUES (?, ?, ?, '', ?, ?, NOW(), NOW())");
    
    if (!$stmt) {
        throw new Exception("쿼리 준비 실패: " . $conn->error);
    }
    
    $visit_date = date('Y-m-d');
    $stmt->bind_param("iiiss", $bakery_id, $member_id, $rating, $content, $images_json, $visit_date);
    
    if (!$stmt->execute()) {
        throw new Exception("리뷰 등록 실패: " . $stmt->error);
    }
    
    $stmt->close();
    
    // 빵집의 평균 평점과 리뷰 수 업데이트
    $update_stmt = $conn->prepare("UPDATE bakeries 
                                    SET rating = (SELECT ROUND(AVG(rating), 1) FROM reviews WHERE bakery_id = ? AND status = 'active'),
                                        review_count = (SELECT COUNT(*) FROM reviews WHERE bakery_id = ? AND status = 'active'),
                                        updated_at = NOW()
                                    WHERE id = ?");
    
    if (!$update_stmt) {
        throw new Exception("업데이트 쿼리 준비 실패: " . $conn->error);
    }
    
    $update_stmt->bind_param("iii", $bakery_id, $bakery_id, $bakery_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception("빵집 정보 업데이트 실패: " . $update_stmt->error);
    }
    
    $update_stmt->close();
    
    // 트랜잭션 커밋
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => '리뷰가 성공적으로 등록되었습니다!',
        'images_uploaded' => count($uploaded_images)
    ]);
    
} catch (Exception $e) {
    // 트랜잭션 롤백
    $conn->rollback();
    
    // 업로드된 이미지 삭제
    foreach ($uploaded_images as $img) {
        $file_path = 'uploads/reviews/' . $img;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    error_log("Review submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => '리뷰 등록 중 오류가 발생했습니다.']);
}

$conn->close();
?>