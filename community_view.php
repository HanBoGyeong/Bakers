<?php
session_start();
header('Content-Type: application/json');

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

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($post_id === 0) {
    echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
    exit;
}

// 조회수 증가
$update_views = "UPDATE community_posts SET views = views + 1 WHERE id = ?";
$stmt = $conn->prepare($update_views);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

// 게시글 조회
$post_query = "SELECT p.*, m.nickname 
               FROM community_posts p 
               JOIN members m ON p.member_id = m.id 
               WHERE p.id = ? AND p.status = 'active'";
$stmt = $conn->prepare($post_query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => '게시글을 찾을 수 없습니다.']);
    exit;
}

$post = $result->fetch_assoc();
$stmt->close();

// 댓글 조회
$comments_query = "SELECT c.*, m.nickname 
                   FROM community_comments c 
                   JOIN members m ON c.member_id = m.id 
                   WHERE c.post_id = ? AND c.status = 'active' 
                   ORDER BY c.created_at ASC";
$stmt = $conn->prepare($comments_query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$comments_result = $stmt->get_result();
$comments = [];
while ($row = $comments_result->fetch_assoc()) {
    $comments[] = $row;
}
$stmt->close();
$conn->close();

// HTML 생성
$is_logged_in = isset($_SESSION['user_id']);

$html = '<div class="post-detail">';
$html .= '<h2 class="detail-title">' . htmlspecialchars($post['title']) . '</h2>';
$html .= '<div class="detail-meta">';
$html .= '<span class="post-author">' . htmlspecialchars($post['nickname']) . '</span>';
$html .= '<span>' . date('Y.m.d H:i', strtotime($post['created_at'])) . '</span>';
$html .= '<span>👁️ ' . $post['views'] . '</span>';
$html .= '<span>❤️ ' . $post['likes'] . '</span>';
$html .= '</div>';
$html .= '<div class="detail-content">' . nl2br(htmlspecialchars($post['content'])) . '</div>';

// 댓글 섹션
$html .= '<div class="comments-section">';
$html .= '<h3 class="comments-title">댓글 ' . count($comments) . '개</h3>';

// 댓글 작성 폼
if ($is_logged_in) {
    $html .= '<div class="comment-form">';
    $html .= '<textarea id="commentContent" class="comment-input" placeholder="댓글을 입력하세요..."></textarea>';
    $html .= '<button class="btn-comment-submit" onclick="submitComment(' . $post_id . ')">댓글 작성</button>';
    $html .= '</div>';
} else {
    $html .= '<div style="padding: 20px; text-align: center; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px;">';
    $html .= '<p>댓글을 작성하려면 <a href="login.php" style="color: #1e3a8a; font-weight: 600;">로그인</a>이 필요합니다.</p>';
    $html .= '</div>';
}

// 댓글 목록
if (!empty($comments)) {
    $html .= '<div class="comment-list">';
    foreach ($comments as $comment) {
        $html .= '<div class="comment-item">';
        $html .= '<div class="comment-header">';
        $html .= '<span class="comment-author">' . htmlspecialchars($comment['nickname']) . '</span>';
        $html .= '<span class="comment-date">' . date('Y.m.d H:i', strtotime($comment['created_at'])) . '</span>';
        $html .= '</div>';
        $html .= '<div class="comment-content">' . nl2br(htmlspecialchars($comment['content'])) . '</div>';
        $html .= '</div>';
    }
    $html .= '</div>';
} else {
    $html .= '<div style="text-align: center; padding: 40px; color: #999;">';
    $html .= '<p>아직 댓글이 없습니다. 첫 댓글을 작성해보세요!</p>';
    $html .= '</div>';
}

$html .= '</div>'; // comments-section
$html .= '</div>'; // post-detail

echo json_encode(['success' => true, 'html' => $html]);
?>