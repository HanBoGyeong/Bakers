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

// 빵집 목록 가져오기 (이미지와 한줄평 포함)
$bakeries_query = "SELECT * FROM bakeries WHERE status = 'active' ORDER BY rating DESC, created_at DESC";
$bakeries_result = $conn->query($bakeries_query);
$bakeries = [];
if ($bakeries_result) {
    while ($row = $bakeries_result->fetch_assoc()) {
        $bakeries[] = $row;
    }
}

// 최근 리뷰 가져오기
$reviews_query = "SELECT r.*, m.nickname, b.name as bakery_name 
                  FROM reviews r 
                  JOIN members m ON r.member_id = m.id 
                  JOIN bakeries b ON r.bakery_id = b.id 
                  WHERE r.status = 'active' 
                  ORDER BY r.created_at DESC 
                  LIMIT 10";
$reviews_result = $conn->query($reviews_query);
$recent_reviews = [];
if ($reviews_result) {
    while ($row = $reviews_result->fetch_assoc()) {
        $recent_reviews[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>추천빵집 - BAKERS</title>
    <link rel="stylesheet" href="main.css">
    <style>
        body {
            font-family: "Noto Sans KR", sans-serif;
            margin: 0;
            background: #f8f8f0;
        }
        .recommend-container {
            max-width: 1200px;
            margin: 80px auto 40px;
            padding: 0 20px;
        }
        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .page-header h1 {
            font-size: 36px;
            color: #1e3a8a;
            margin-bottom: 10px;
        }
        .page-header p {
            color: #666;
            font-size: 16px;
        }
        
        /* 빵집 카드 그리드 */
        .bakery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        .bakery-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
        }
        .bakery-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        
        /* 이미지 컨테이너 */
        .bakery-image-container {
            width: 100%;
            height: 240px;
            overflow: hidden;
            position: relative;
            background: #f0f0f0;
        }
        .bakery-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .bakery-card:hover .bakery-image {
            transform: scale(1.05);
        }
        
        /* 빵집 정보 영역 */
        .bakery-info {
            padding: 24px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .bakery-name {
            font-size: 20px;
            font-weight: 700;
            color: #222;
            margin-bottom: 12px;
            line-height: 1.3;
        }
        
        /* 한줄평 영역 */
        .bakery-oneliner {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 16px;
            font-style: italic;
            min-height: 44px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            padding: 10px 12px;
            background: #f8f9fa;
            border-left: 3px solid #1e3a8a;
            border-radius: 4px;
        }
        .bakery-oneliner::before {
            content: '"';
            color: #1e3a8a;
            font-weight: bold;
        }
        .bakery-oneliner::after {
            content: '"';
            color: #1e3a8a;
            font-weight: bold;
        }
        .bakery-oneliner.empty {
            color: #aaa;
            font-style: normal;
            background: #fafafa;
            border-left-color: #ddd;
        }
        .bakery-oneliner.empty::before,
        .bakery-oneliner.empty::after {
            content: '';
        }
        
        .bakery-address {
            font-size: 13px;
            color: #666;
            margin-bottom: 14px;
            display: flex;
            align-items: flex-start;
            gap: 6px;
        }
        .bakery-address::before {
            content: '📍';
            flex-shrink: 0;
        }
        
        .bakery-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            margin-bottom: 18px;
            padding-top: 12px;
            border-top: 1px solid #f0f0f0;
        }
        .rating-stars {
            color: #ffa500;
            font-weight: 700;
            font-size: 16px;
        }
        .review-count {
            color: #999;
            font-size: 13px;
        }
        
        .btn-review {
            width: 100%;
            padding: 13px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s ease;
            margin-top: auto;
        }
        .btn-review:hover {
            background: #16347a;
            transform: translateY(-2px);
        }
        
        /* 최근 리뷰 섹션 */
        .recent-reviews-section {
            margin-top: 80px;
            padding-top: 50px;
            border-top: 3px solid #e0e0e0;
        }
        .section-title {
            font-size: 28px;
            color: #222;
            margin-bottom: 35px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .review-list {
            display: grid;
            gap: 24px;
        }
        .review-item {
            background: white;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            transition: box-shadow 0.2s ease;
        }
        .review-item:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }
        .review-author {
            font-weight: 700;
            color: #222;
            font-size: 15px;
        }
        .review-date {
            font-size: 13px;
            color: #999;
        }
        .review-bakery {
            color: #1e3a8a;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .review-rating {
            color: #ffa500;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .review-content {
            color: #444;
            line-height: 1.7;
            font-size: 14px;
        }
        
        /* 빈 상태 */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }
        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #666;
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
            background: rgba(0,0,0,0.6);
            overflow-y: auto;
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            margin: 20px;
        }
        .modal-header {
            padding: 28px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-title {
            font-size: 22px;
            font-weight: 700;
            color: #222;
        }
        .modal-close {
            font-size: 28px;
            color: #999;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }
        .modal-close:hover {
            color: #333;
            background: #f0f0f0;
        }
        .modal-body {
            padding: 28px;
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1e3a8a;
        }
        .form-group textarea {
            min-height: 130px;
            resize: vertical;
            font-family: inherit;
        }
        .star-rating {
            display: flex;
            gap: 10px;
            font-size: 36px;
        }
        .star {
            cursor: pointer;
            color: #ddd;
            transition: color 0.2s, transform 0.2s;
        }
        .star:hover,
        .star.active {
            color: #ffa500;
            transform: scale(1.1);
        }
        .image-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 45px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .image-upload:hover {
            border-color: #1e3a8a;
            background: #f8f9ff;
        }
        .image-upload input[type="file"] {
            display: none;
        }
        .upload-text {
            color: #666;
            font-size: 14px;
        }
        .image-preview {
            margin-top: 18px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }
        .preview-item {
            position: relative;
            width: 110px;
            height: 110px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .preview-remove {
            position: absolute;
            top: 6px;
            right: 6px;
            background: rgba(0,0,0,0.75);
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .preview-remove:hover {
            background: rgba(0,0,0,0.9);
        }
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-submit:hover {
            background: #16347a;
            transform: translateY(-2px);
        }
        .login-required {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        .login-required p {
            margin-bottom: 20px;
            font-size: 15px;
        }
        .login-required a {
            color: #1e3a8a;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
        }
        
        /* 반응형 */
        @media (max-width: 768px) {
            .bakery-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            .page-header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- 헤더 -->
    <header class="header">
        <?php include 'header.php'; ?>
    </header>

    <!-- 메인 컨텐츠 -->
    <div class="recommend-container">
        <div class="page-header">
            <h1>⭐ 추천 빵집</h1>
            <p>BAKERS가 엄선한 최고의 빵집들을 만나보세요</p>
        </div>

        <!-- 빵집 목록 -->
        <div class="bakery-grid">
            <?php if (empty($bakeries)): ?>
                <div class="empty-state" style="grid-column: 1/-1;">
                    <h3>🍞 등록된 빵집이 없습니다</h3>
                    <p>곧 멋진 빵집들이 추가될 예정입니다!</p>
                </div>
            <?php else: ?>
                <?php foreach ($bakeries as $bakery): ?>
                <div class="bakery-card">
                    <!-- 각 빵집의 고유 이미지 -->
                    <div class="bakery-image-container">
                        <?php
                        // image_url 필드에서 이미지 경로 가져오기
                        $image_url = !empty($bakery['image_url']) 
                            ? htmlspecialchars($bakery['image_url']) 
                            : '1.성심당.png';  // 기본 이미지
                        ?>
                        <img src="./img/<?= $image_url ?>" 
                             alt="<?= htmlspecialchars($bakery['name']) ?>" 
                             class="bakery-image"
                             >
                    </div>
                    
                    <div class="bakery-info">
                        <div class="bakery-name"><?= htmlspecialchars($bakery['name']) ?></div>
                        
                        <!-- 각 빵집의 고유 한줄평 -->
                        <?php if (!empty($bakery['description'])): ?>
                            <div class="bakery-oneliner">
                                <?= htmlspecialchars($bakery['description']) ?>
                            </div>
                        <?php else: ?>
                            <div class="bakery-oneliner empty">
                                아직 한줄 소개가 없습니다
                            </div>
                        <?php endif; ?>
                        
                        <div class="bakery-address"><?= htmlspecialchars($bakery['address']) ?></div>
                        
                        <div class="bakery-rating">
                            <span class="rating-stars">⭐ <?= number_format($bakery['rating'], 1) ?></span>
                            <span class="review-count">(<?= $bakery['review_count'] ?>개 리뷰)</span>
                        </div>
                        
                        <button class="btn-review" onclick="openReviewModal(<?= $bakery['id'] ?>, '<?= addslashes(htmlspecialchars($bakery['name'])) ?>')">
                            리뷰 작성하기
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        
      
    </div>

    <!-- 리뷰 작성 모달 -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">리뷰 작성</h3>
                <button class="modal-close" onclick="closeReviewModal()">&times;</button>
            </div>
            <div class="modal-body">
                <?php if ($is_logged_in): ?>
                <form id="reviewForm" enctype="multipart/form-data">
                    <input type="hidden" id="bakery_id" name="bakery_id">
                    
                    <div class="form-group">
                        <label>빵집 이름</label>
                        <input type="text" id="bakery_name_display" readonly style="background: #f5f5f5;">
                    </div>

                    <div class="form-group">
                        <label>별점 <span style="color: #ff4444;">*</span></label>
                        <div class="star-rating" id="starRating">
                            <span class="star" data-rating="1">★</span>
                            <span class="star" data-rating="2">★</span>
                            <span class="star" data-rating="3">★</span>
                            <span class="star" data-rating="4">★</span>
                            <span class="star" data-rating="5">★</span>
                        </div>
                        <input type="hidden" id="rating" name="rating" required>
                    </div>

                    <div class="form-group">
                        <label>한줄평 <span style="color: #ff4444;">*</span></label>
                        <textarea id="content" name="content" placeholder="이 빵집에 대한 솔직한 리뷰를 남겨주세요" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>사진 업로드 (선택)</label>
                        <div class="image-upload" onclick="document.getElementById('imageInput').click()">
                            <div class="upload-text">📷 클릭하여 사진 업로드<br><small>최대 3장까지 가능</small></div>
                            <input type="file" id="imageInput" name="images[]" accept="image/*" multiple onchange="previewImages(event)">
                        </div>
                        <div id="imagePreview" class="image-preview"></div>
                    </div>

                    <button type="submit" class="btn-submit">리뷰 등록하기</button>
                </form>
                <?php else: ?>
                <div class="login-required">
                    <p>리뷰를 작성하려면 로그인이 필요합니다.</p>
                    <a href="login.php">로그인하러 가기 →</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 푸터 -->
    <footer>
        <?php include 'footer.php'; ?>
    </footer>

    <script>
        let selectedRating = 0;
        let selectedFiles = [];

        // 별점 선택
        document.querySelectorAll('.star').forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating);
                document.getElementById('rating').value = selectedRating;
                
                document.querySelectorAll('.star').forEach((s, index) => {
                    if (index < selectedRating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });

        // 리뷰 모달 열기
        function openReviewModal(bakeryId, bakeryName) {
            <?php if (!$is_logged_in): ?>
                alert('로그인이 필요한 서비스입니다.');
                window.location.href = 'login.php';
                return;
            <?php endif; ?>

            document.getElementById('bakery_id').value = bakeryId;
            document.getElementById('bakery_name_display').value = bakeryName;
            document.getElementById('reviewModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // 리뷰 모달 닫기
        function closeReviewModal() {
            document.getElementById('reviewModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('reviewForm').reset();
            selectedRating = 0;
            selectedFiles = [];
            document.querySelectorAll('.star').forEach(s => s.classList.remove('active'));
            document.getElementById('imagePreview').innerHTML = '';
        }

        // 모달 외부 클릭시 닫기
        document.getElementById('reviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReviewModal();
            }
        });

        // 이미지 미리보기
        function previewImages(event) {
            const files = Array.from(event.target.files);
            const preview = document.getElementById('imagePreview');
            
            if (files.length > 3) {
                alert('최대 3장까지만 업로드 가능합니다.');
                return;
            }

            selectedFiles = files;
            preview.innerHTML = '';

            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'preview-item';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="preview">
                        <button type="button" class="preview-remove" onclick="removeImage(${index})">×</button>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        // 이미지 제거
        function removeImage(index) {
            selectedFiles.splice(index, 1);
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            document.getElementById('imageInput').files = dataTransfer.files;
            
            const event = new Event('change');
            document.getElementById('imageInput').dispatchEvent(event);
        }

        // 리뷰 제출
        document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            if (selectedRating === 0) {
                alert('별점을 선택해주세요.');
                return;
            }

            const formData = new FormData(this);

            fetch('submit_review.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('리뷰가 등록되었습니다!');
                    closeReviewModal();
                    location.reload();
                } else {
                    alert(data.message || '리뷰 등록에 실패했습니다.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('오류가 발생했습니다.');
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>