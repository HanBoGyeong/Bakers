<?php
require_once 'config.php';
$page_title = 'BAKERS - 빵집 리뷰 커뮤니티';

// 최근 빵집 6개 가져오기
$query = "SELECT * FROM bakeries WHERE status = 'active' ORDER BY created_at DESC LIMIT 18";
$result = $conn->query($query);
$bakeries = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bakeries[] = $row;
    }
}

require_once 'header.php';
?>

<!-- 히어로 섹션 -->
<section class="hero">
    <div class="hero-content">
        <h1>
            Join to access our<br>
            growing library of premium<br>
            design resources
        </h1>
        <p>당신이 방문한 빵집의 소중한 순간을</p>
        <p>모든 빵덕과 함께 공유할 준비가 되셨나요?</p>
    </div>
</section>

<!-- 갤러리 섹션 -->
<section class="gallery-section">
    <div class="gallery-container">
        <div class="gallery-header">
            <h2>당신의 베이커리 경험을 공유하세요</h2>
            <div class="highlight">Share your bakery moments</div>
        </div>

        <div class="gallery-grid">
            <?php if (!empty($bakeries)): ?>
                <?php foreach ($bakeries as $bakery): ?>
                    <div class="gallery-item" onclick="location.href='<?= url('pages/bakery_detail.php?id=' . $bakery['id']) ?>'">
                        <?php if ($bakery['main_image']): ?>
                            <img src="<?= url('uploads/bakeries/' . $bakery['main_image']) ?>" 
                                 alt="<?= escape($bakery['name']) ?>"
                                 onerror="this.src='https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80'">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80" 
                                 alt="<?= escape($bakery['name']) ?>">
                        <?php endif; ?>
                        <div class="item-overlay">
                            <div class="bakery-name"><?= escape($bakery['name']) ?></div>
                            <div class="bakery-rating">⭐ <?= $bakery['rating'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- 샘플 이미지들 -->
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80" alt="빵집">
                    <div class="item-overlay">빵굼 베이커리</div>
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&q=80" alt="빵">
                    <div class="item-overlay">성심당</div>
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1568471173238-64ed8e1fc0fe?w=400&q=80" alt="크루아상">
                    <div class="item-overlay">크루아상 전문점</div>
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=400&q=80" alt="바게트">
                    <div class="item-overlay">프랑스 베이커리</div>
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1586985289688-ca3cf47d3e6e?w=400&q=80" alt="빵집">
                    <div class="item-overlay">파리바게뜨</div>
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1612198188060-c7c2a3b66eae?w=400&q=80" alt="베이커리">
                    <div class="item-overlay">뚜레쥬르</div>
                </div>
            <?php endif; ?>
        </div>

        <a href="<?= url('pages/reviews.php') ?>" class="btn-more">더 많은 빵집보기</a>
    </div>
</section>

<!-- CTA 섹션 -->
<section class="cta-section">
    <h2>
        지금 바로 시작하세요<br>
        당신의 <span class="highlight-text">빵집 경험</span>을 공유하고 다른 사람들과 <span class="sub-text">소통</span>하세요
    </h2>
    <?php if (!is_logged_in()): ?>
        <a href="<?= url('auth/signup.php') ?>" class="btn-cta">회원가입하고 시작하기</a>
    <?php endif; ?>
</section>

<?php require_once 'footer.php'; ?>