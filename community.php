<?php
session_start();

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
    die("데이터베이스 연결 실패: " . $e->getMessage());
}

// 로그인 확인
$is_logged_in = isset($_SESSION['user_id']);
$user_nickname = $is_logged_in ? $_SESSION['nickname'] : '';
$user_id = $is_logged_in ? $_SESSION['user_id'] : '';

// 페이지 번호
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

// 전체 게시글 수
$count_query = "SELECT COUNT(*) as total FROM community_posts WHERE status = 'active'";
$count_result = $conn->query($count_query);
$total_posts = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $per_page);

// 게시글 목록 가져오기
$posts_query = "SELECT p.*, m.nickname, 
                (SELECT COUNT(*) FROM community_comments WHERE post_id = p.id AND status = 'active') as comment_count
                FROM community_posts p 
                JOIN members m ON p.member_id = m.id 
                WHERE p.status = 'active' 
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
$stmt = $conn->prepare($posts_query);
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$posts_result = $stmt->get_result();
$posts = [];
while ($row = $posts_result->fetch_assoc()) {
    $posts[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>커뮤니티 - BAKERS</title>
    <link rel="stylesheet" href="main.css">
    <style>
        body {
            font-family: "Noto Sans KR", sans-serif;
            margin: 0;
            background: #f8f8f0;
        }
        .community-container {
            max-width: 1200px;
            margin: 80px auto 40px;
            padding: 0 20px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        .page-title {
            font-size: 36px;
            color: #1e3a8a;
        }
        .btn-write {
            padding: 12px 24px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-write:hover {
            background: #16347a;
        }
        
        /* 게시글 리스트 */
        .post-list {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .post-item {
            padding: 24px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s;
        }
        .post-item:hover {
            background: #f8f9fa;
        }
        .post-item:last-child {
            border-bottom: none;
        }
        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .post-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .post-content {
            color: #666;
            line-height: 1.6;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .post-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: #999;
        }
        .post-author {
            font-weight: 600;
            color: #1e3a8a;
        }
        .post-stats {
            display: flex;
            gap: 12px;
        }
        .stat-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        /* 페이지네이션 */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 40px;
        }
        .page-btn {
            padding: 8px 14px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            color: #333;
        }
        .page-btn:hover {
            background: #f8f9fa;
        }
        .page-btn.active {
            background: #1e3a8a;
            color: white;
            border-color: #1e3a8a;
        }
        
        /* 모달 */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            overflow-y: auto;
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            margin: 20px;
        }
        .modal-header {
            padding: 24px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }
        .modal-close {
            font-size: 28px;
            color: #999;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            width: 32px;
            height: 32px;
        }
        .modal-body {
            padding: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group textarea {
            min-height: 200px;
            resize: vertical;
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-submit:hover {
            background: #16347a;
        }
        
        /* 게시글 상세 보기 */
        .post-detail {
            padding: 24px;
        }
        .detail-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 16px;
        }
        .detail-meta {
            display: flex;
            gap: 16px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 24px;
            font-size: 14px;
            color: #666;
        }
        .detail-content {
            line-height: 1.8;
            color: #333;
            min-height: 200px;
            margin-bottom: 40px;
        }
        
        /* 댓글 섹션 */
        .comments-section {
            border-top: 2px solid #e0e0e0;
            padding-top: 24px;
        }
        .comments-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        .comment-form {
            margin-bottom: 30px;
        }
        .comment-input {
            width: 100%;
            min-height: 80px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            resize: vertical;
            margin-bottom: 12px;
        }
        .btn-comment-submit {
            padding: 10px 20px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .comment-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .comment-item {
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .comment-author {
            font-weight: 600;
            color: #1e3a8a;
        }
        .comment-date {
            color: #999;
            font-size: 13px;
        }
        .comment-content {
            color: #333;
            line-height: 1.6;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
    </style>
</head>
<body>
    <!-- 헤더 -->
    <header class="header">
        <?php include 'header.php'; ?>
    </header>

    <!-- 메인 컨텐츠 -->
    <div class="community-container">
        <div class="page-header">
            <h1 class="page-title">💬 커뮤니티</h1>
            <?php if ($is_logged_in): ?>
            <button class="btn-write" onclick="openWriteModal()">
                ✏️ 글쓰기
            </button>
            <?php endif; ?>
        </div>

        <!-- 게시글 리스트 -->
        <div class="post-list">
            <?php if (empty($posts)): ?>
            <div class="empty-state">
                <p>아직 작성된 게시글이 없습니다.</p>
                <p>첫 번째 게시글의 주인공이 되어보세요!</p>
            </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                <div class="post-item" onclick="viewPost(<?= $post['id'] ?>)">
                    <div class="post-header">
                        <div>
                            <div class="post-title"><?= htmlspecialchars($post['title']) ?></div>
                            <div class="post-content"><?= htmlspecialchars($post['content']) ?></div>
                        </div>
                    </div>
                    <div class="post-meta">
                        <span class="post-author"><?= htmlspecialchars($post['nickname']) ?></span>
                        <span><?= date('Y.m.d H:i', strtotime($post['created_at'])) ?></span>
                        <div class="post-stats">
                            <span class="stat-item">👁️ <?= $post['views'] ?></span>
                            <span class="stat-item">💬 <?= $post['comment_count'] ?></span>
                            <span class="stat-item">❤️ <?= $post['likes'] ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- 페이지네이션 -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <button class="page-btn" onclick="location.href='?page=<?= $page - 1 ?>'">‹</button>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <button class="page-btn <?= $i == $page ? 'active' : '' ?>" 
                    onclick="location.href='?page=<?= $i ?>'"><?= $i ?></button>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <button class="page-btn" onclick="location.href='?page=<?= $page + 1 ?>'">›</button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- 글쓰기 모달 -->
    <div id="writeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">새 글 작성</h3>
                <button class="modal-close" onclick="closeWriteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <?php if ($is_logged_in): ?>
                <form id="writeForm">
                    <div class="form-group">
                        <label>제목</label>
                        <input type="text" name="title" placeholder="제목을 입력하세요" required>
                    </div>
                    <div class="form-group">
                        <label>내용</label>
                        <textarea name="content" placeholder="내용을 입력하세요" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">작성하기</button>
                </form>
                <?php else: ?>
                <div style="text-align: center; padding: 40px;">
                    <p>로그인이 필요한 서비스입니다.</p>
                    <a href="login.php" style="color: #1e3a8a; font-weight: 600;">로그인하러 가기 →</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 게시글 상세보기 모달 -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">게시글 보기</h3>
                <button class="modal-close" onclick="closeViewModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="postDetail"></div>
            </div>
        </div>
    </div>

    <!-- 푸터 -->
    <footer>
        <?php include 'footer.php'; ?>
    </footer>

    <script>
        // 글쓰기 모달 열기
        function openWriteModal() {
            document.getElementById('writeModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // 글쓰기 모달 닫기
        function closeWriteModal() {
            document.getElementById('writeModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('writeForm').reset();
        }

        // 게시글 보기 모달 닫기
        function closeViewModal() {
            document.getElementById('viewModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // 글쓰기 폼 제출
        document.getElementById('writeForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('community_write.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('게시글이 작성되었습니다!');
                    closeWriteModal();
                    location.reload();
                } else {
                    alert(data.message || '게시글 작성에 실패했습니다.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('오류가 발생했습니다.');
            });
        });

        // 게시글 상세보기
        function viewPost(postId) {
            fetch(`community_view.php?id=${postId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('postDetail').innerHTML = data.html;
                        document.getElementById('viewModal').classList.add('active');
                        document.body.style.overflow = 'hidden';
                    } else {
                        alert('게시글을 불러올 수 없습니다.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('오류가 발생했습니다.');
                });
        }

        // 댓글 작성
        function submitComment(postId) {
            const content = document.getElementById('commentContent').value.trim();
            
            if (!content) {
                alert('댓글 내용을 입력해주세요.');
                return;
            }

            fetch('community_comment.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `post_id=${postId}&content=${encodeURIComponent(content)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('댓글이 작성되었습니다!');
                    viewPost(postId); // 새로고침
                } else {
                    alert(data.message || '댓글 작성에 실패했습니다.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('오류가 발생했습니다.');
            });
        }

        // 모달 외부 클릭 시 닫기
        document.getElementById('writeModal').addEventListener('click', function(e) {
            if (e.target === this) closeWriteModal();
        });

        document.getElementById('viewModal').addEventListener('click', function(e) {
            if (e.target === this) closeViewModal();
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>