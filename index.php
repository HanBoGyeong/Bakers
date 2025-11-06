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
?>
<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BAKERS - 빵집 리뷰 커뮤니티</title>
        <!-- 헤더 -->
        <header class="header">
     <?php include "header.php" ?>
    </header>
    <style>
    <?php include "main.css" ?>

    
    /* 갤러리 이미지 클릭 가능 스타일 강화 */
    .gallery-item {
      position: relative;
      overflow: hidden;
      border-radius: 12px;
      cursor: pointer !important;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .gallery-item:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 20px 40px rgba(0,0,0,0.25);
      z-index: 10;
    }
    
    .gallery-item img {
      transition: transform 0.4s ease;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .gallery-item:hover img {
      transform: scale(1.15);
      filter: brightness(1.1);
    }
    
    .item-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%);
      color: white;
      padding: 25px 20px 18px;
      font-weight: 700;
      font-size: 17px;
      transition: all 0.3s ease;
      letter-spacing: -0.5px;
    }
    
    .gallery-item:hover .item-overlay {
      background: linear-gradient(to top, rgba(30, 58, 138, 0.95) 0%, rgba(30, 58, 138, 0.6) 100%);
      padding-bottom: 25px;
    }
    
    /
    .gallery-item:hover::after {
      opacity: 1;
      transform: translateY(0);
    }
    
    
    .gallery-item:hover::before {
      opacity: 1;
      transform: translate(-50%, -50%) scale(1);
    }
    
    /* 클릭 애니메이션 */
    .gallery-item:active {
      transform: translateY(-8px) scale(0.98);
    }
    
    /* 더보기 버튼 스타일 개선 */
    .btn-more {
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    
    .btn-more::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255,255,255,0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }
    
    .btn-more:hover::before {
      width: 300px;
      height: 300px;
    }
    
    .btn-more:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(30, 58, 138, 0.3);
    }
    
    
    .page-transition.active {
      display: flex;
      opacity: 1;
    }
    
    .transition-content {
      text-align: center;
      color: white;
    }
    
    .transition-icon {
      font-size: 64px;
      animation: bounce 0.6s infinite;
      margin-bottom: 20px;
    }
    
    .transition-text {
      font-size: 24px;
      font-weight: 700;
      animation: fadeIn 0.5s ease;
    }
    
    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-15px); }
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    /* 모바일 반응형 */
    @media (max-width: 768px) {
      .gallery-item::before {
        font-size: 13px;
        padding: 10px 18px;
      }
      
      .gallery-item::after {
        font-size: 24px;
      }
    }
    </style>
  </head>
  <body>


    <!-- 홈 페이지 (메인 랜딩 페이지) -->
    <div id="home" class="page active">
      <section class="hero">
        <h1>
          Join to access our<br />growing library of premium<br />design
          resources
        </h1>
        <p>당신이 방문한 빵집의 소중한 순간을</p>
        <p>모든 빵덕과 함께 공유할 준비가 되셨나요?</p>
      </section>

      <section class="gallery-section">
        <div class="gallery-container">
          <div class="gallery-header">
            <h2>당신의 베이커리 경험을 공유하세요</h2>
            <div class="highlight">Share your bakery moments</div>
          </div>

          <div class="gallery-grid">
            <!-- 모든 이미지에 클릭 이벤트 추가 -->
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80"
                alt="빵집"
              />
              <div class="item-overlay">빵굼 베이커리</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&q=80"
                alt="빵"
              />
              <div class="item-overlay">성심당</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1568471173238-64ed8e1fc0fe?w=400&q=80"
                alt="크루아상"
              />
              <div class="item-overlay">크루아상 전문점</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=400&q=80"
                alt="바게트"
              />
              <div class="item-overlay">프랑스 베이커리</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1586985289688-ca3cf47d3e6e?w=400&q=80"
                alt="빵집"
              />
              <div class="item-overlay">파리바게뜨</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1612198188060-c7c2a3b66eae?w=400&q=80"
                alt="베이커리"
              />
              <div class="item-overlay">뚜레쥬르</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1608198093002-ad4e005484ec?w=400&q=80"
                alt="도넛"
              />
              <div class="item-overlay">던킨 도넛</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1517433670267-08bbd4be890f?w=400&q=80"
                alt="케이크"
              />
              <div class="item-overlay">카페 베이커리</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1557925923-cd4648e211a0?w=400&q=80"
                alt="빵"
              />
              <div class="item-overlay">동네 빵집</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1495147466023-ac5c588e2e94?w=400&q=80"
                alt="빵집"
              />
              <div class="item-overlay">브레드 스토리</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1590301157890-4810ed352733?w=400&q=80"
                alt="베이글"
              />
              <div class="item-overlay">베이글 카페</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=400&q=80"
                alt="빵"
              />
              <div class="item-overlay">홈메이드 베이커리</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1550617931-e17a7b70dce2?w=400&q=80"
                alt="제과점"
              />
              <div class="item-overlay">아티장 베이커리</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1560180477-7e7a0b6e6896?w=400&q=80"
                alt="빵"
              />
              <div class="item-overlay">소금빵 전문점</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1549903072-7e6e0bedb7fb?w=400&q=80"
                alt="베이커리"
              />
              <div class="item-overlay">스위트 베이커리</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&q=80"
                alt="빵집"
              />
              <div class="item-overlay">빵 공방</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1584278302340-3b486e679aeb?w=400&q=80"
                alt="빵"
              />
              <div class="item-overlay">골목 베이커리</div>
            </div>
            
            <div class="gallery-item" onclick="navigateToRecommend()">
              <img
                src="https://images.unsplash.com/photo-1562007908-17c67e878c88?w=400&q=80"
                alt="제빵"
              />
              <div class="item-overlay">제빵왕 김탁구</div>
            </div>
          </div>

          <button class="btn-more" onclick="navigateToRecommend()">
            더 많은 빵집보기
          </button>
        </div>
      </section>

      <section class="cta-section">
        <h2>
          지금 바로 시작하세요<br />
          당신의 <span class="highlight-text">빵집 경험</span>을 공유하고 다른
          사람들과 <span class="sub-text">소통</span>하세요
        </h2>
      </section>
    </div>

    <!-- 페이지 전환 오버레이 -->
    <div class="page-transition" id="pageTransition">
      <div class="transition-content">
        <div class="transition-icon">🍞</div>
        <div class="transition-text">추천 빵집으로 이동중...</div>
      </div>
    </div>

    <!-- 푸터 -->
    <footer>
    <?php include "footer.php"?>
    </footer>

    <script>
      // ⭐ 추천빵집 페이지로 이동하는 함수
      function navigateToRecommend() {
        // 전환 오버레이 표시
        const transition = document.getElementById('pageTransition');
        transition.classList.add('active');
        
        // 부드러운 페이지 전환
        setTimeout(function() {
          window.location.href = 'recommend.php';
        }, 600);
      }

      // DOMContentLoaded 이벤트
      document.addEventListener("DOMContentLoaded", function () {
        // 페이지 로드 애니메이션
        const galleryItems = document.querySelectorAll(".gallery-item");
        galleryItems.forEach((item, index) => {
          item.style.opacity = '0';
          item.style.transform = 'translateY(30px)';
          
          setTimeout(() => {
            item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
          }, index * 80);
        });
        
        // 키보드 접근성 추가
        galleryItems.forEach((item) => {
          // 탭 포커스 가능하도록 설정
          item.setAttribute("tabindex", "0");
          
          // 키보드로 Enter/Space 키 입력 시 클릭 이벤트 발생
          item.addEventListener("keypress", function(e) {
            if (e.key === "Enter" || e.key === " ") {
              e.preventDefault();
              navigateToRecommend();
            }
          });
          
          // 포커스 시 시각적 효과
          item.addEventListener("focus", function() {
            this.style.outline = "3px solid #1e3a8a";
            this.style.outlineOffset = "4px";
          });
          
          item.addEventListener("blur", function() {
            this.style.outline = "none";
          });
        });

        // 검색 기능
        const searchIcon = document.getElementById("searchIcon");
        const searchInput = document.getElementById("searchInput");

        if (searchIcon) {
          searchIcon.addEventListener("click", function () {
            searchInput.focus();
          });
        }

        if (searchInput) {
          searchInput.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
              const query = searchInput.value.trim();
              if (query) {
                window.location.href = 'search.php?q=' + encodeURIComponent(query);
              }
            }
          });
        }

        // "더 많은 빵집보기" 버튼 효과
        const btnMore = document.querySelector(".btn-more");
        if (btnMore) {
          btnMore.addEventListener("mouseenter", function() {
            this.style.transform = "translateY(-3px)";
          });
          
          btnMore.addEventListener("mouseleave", function() {
            this.style.transform = "translateY(0)";
          });
        }
      });

      // 페이지 떠나기 전 이벤트
      window.addEventListener('beforeunload', function() {
        document.body.style.opacity = '0';
      });
    </script>
  </body>
</html>