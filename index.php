<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BAKERS - 빵집 리뷰 커뮤니티</title>
    <style>
    <?php include "main.css" ?>
    </style>
  </head>
  <body>
    <!-- 헤더 -->
    <header class="header">
     <?php include "header.php" ?>

    </header>

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
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80"
                alt="빵집"
              />
              <div class="item-overlay">빵굼 베이커리</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&q=80"
                alt="빵"
              />
              <div class="item-overlay">성심당</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1568471173238-64ed8e1fc0fe?w=400&q=80"
                alt="크루아상"
              />
              <div class="item-overlay">크루아상 전문점</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=400&q=80"
                alt="바게트"
              />
              <div class="item-overlay">프랑스 베이커리</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1586985289688-ca3cf47d3e6e?w=400&q=80"
                alt="빵집"
              />
              <div class="item-overlay">파리바게뜨</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1612198188060-c7c2a3b66eae?w=400&q=80"
                alt="베이커리"
              />
              <div class="item-overlay">뚜레쥬르</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1608198093002-ad4e005484ec?w=400&q=80"
                alt="도넛"
              />
              <div class="item-overlay">던킨 도넛</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1517433670267-08bbd4be890f?w=400&q=80"
                alt="케이크"
              />
              <div class="item-overlay">카페 베이커리</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1557925923-cd4648e211a0?w=400&q=80"
                alt="빵"
              />
              <div class="item-overlay">동네 빵집</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1495147466023-ac5c588e2e94?w=400&q=80"
                alt="빵집"
              />
              <div class="item-overlay">브레드 스토리</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1590301157890-4810ed352733?w=400&q=80"
                alt="베이글"
              />
              <div class="item-overlay">베이글 카페</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=400&q=80"
                alt="빵"
              />
              <div class="item-overlay">홈메이드 베이커리</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1550617931-e17a7b70dce2?w=400&q=80"
                alt="제과점"
              />
              <div class="item-overlay">아티장 베이커리</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1560180477-7e7a0b6e6896?w=400&q=80"
                alt="빵"
              />
              <div class="item-overlay">소금빵 전문점</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1549903072-7e6e0bedb7fb?w=400&q=80"
                alt="베이커리"
              />
              <div class="item-overlay">스위트 베이커리</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&q=80"
                alt="빵집"
              />
              <div class="item-overlay">빵 공방</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1584278302340-3b486e679aeb?w=400&q=80"
                alt="빵"
              />
              <div class="item-overlay">골목 베이커리</div>
            </div>
            <div class="gallery-item">
              <img
                src="https://images.unsplash.com/photo-1562007908-17c67e878c88?w=400&q=80"
                alt="제빵"
              />
              <div class="item-overlay">제빵왕 김탁구</div>
            </div>
          </div>

          <button class="btn-more" onclick="showPage('reviews')">
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

    <!-- ✅ 추가: 추천빵집 페이지 -->
    <div id="recommend" class="page">
      <div class="content-page">
        <h1>추천 빵집</h1>
        <p>BAKERS가 엄선한 최고의 빵집들을 소개합니다.</p>
        <p>지역별, 메뉴별로 특별한 빵집들을 만나보세요.</p>
        <p>빵덕들이 극찬한 맛집 리스트를 확인하세요!</p>
      </div>
    </div>

    <!-- 리뷰 페이지 -->
    <div id="reviews" class="page">
      <div class="content-page">
        <h1>리뷰 목록</h1>
        <p>사용자들이 작성한 빵집 리뷰를 확인하세요.</p>
        <p>별점, 사진, 후기를 통해 다양한 빵집 정보를 얻을 수 있습니다.</p>
        <p>마음에 드는 빵집을 발견하셨다면 직접 방문해보세요!</p>
      </div>
    </div>

    <!-- 지도보기 페이지 -->
    <div id="about" class="page">
      <div class="content-page">
        <h1>지도에서 빵집 찾기</h1>
        <p>지도를 통해 주변 빵집을 쉽게 찾아보세요.</p>
        <p>위치 기반으로 가까운 빵집을 추천해드립니다.</p>
        <p>별점과 리뷰를 확인하고 최고의 빵집을 발견하세요!</p>
      </div>
    </div>

    <!-- 로그인 페이지 -->
    <div id="login" class="page">
      <div class="content-page">
        <h1>로그인</h1>
        <p>BAKERS 회원으로 로그인하여 더 많은 기능을 이용하세요.</p>
        <p>리뷰 작성, 댓글, 좋아요 등 다양한 활동이 가능합니다.</p>
      </div>
    </div>

    <!-- 회원가입 페이지 -->
    <div id="signup" class="page">
      <div class="content-page">
        <h1>회원가입</h1>
        <p>BAKERS의 새로운 회원이 되어주세요.</p>
        <p>간단한 가입 절차로 빵집 리뷰 커뮤니티에 참여하실 수 있습니다.</p>
        <p>지금 바로 가입하고 당신의 빵집 이야기를 공유하세요!</p>
      </div>
    </div>

    <!-- 푸터 -->
    <footer>
    <?php include "footer.php"?>
    </footer>

    <script>
      function showPage(pageId) {
        // 모든 페이지 숨기기
        const pages = document.querySelectorAll(".page");
        pages.forEach((page) => {
          page.classList.remove("active");
        });

        // 선택된 페이지 보이기
        const selectedPage = document.getElementById(pageId);
        if (selectedPage) {
          selectedPage.classList.add("active");
        }

        // 페이지 상단으로 스크롤
        window.scrollTo({
          top: 0,
          behavior: "smooth",
        });
      }

      // ✅ 수정: DOMContentLoaded 이벤트에서 네비게이션 이벤트 등록
      document.addEventListener("DOMContentLoaded", function () {
        // 1️⃣ BAKERS 로고 클릭 이벤트 - home 페이지로 이동
        const bakersLogo = document.getElementById("bakersLogo");
        if (bakersLogo) {
          bakersLogo.addEventListener("click", function () {
            showPage("home");
          });
        }

        // 2️⃣ 추천빵집 메뉴 클릭 이벤트 - recommend 페이지로 이동
        const recommendLink = document.getElementById("recommendLink");
        if (recommendLink) {
          recommendLink.addEventListener("click", function () {
            showPage("recommend");
          });
        }

        // 3️⃣ 검색 기능
        const searchIcon = document.getElementById("searchIcon");

        const searchInput = document.getElementById("searchInput");

        // 돋보기 아이콘 클릭 - 검색 입력창에 포커스
        if (searchIcon) {
          searchIcon.addEventListener("click", function () {
            searchInput.focus();
          });
        }

        // 검색 입력 시 Enter 키 처리
        if (searchInput) {
          searchInput.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
              const query = searchInput.value.trim();
              if (query) {
                alert(`"${query}" 검색 결과를 찾고 있습니다...`);
                // 여기에 실제 검색 기능을 추가할 수 있습니다
              }
            }
          });
        }

        // 갤러리 아이템 클릭 시 리뷰 페이지로 이동
        const galleryItems = document.querySelectorAll(".gallery-item");
        galleryItems.forEach((item) => {
          item.addEventListener("click", function () {
            showPage("reviews");
          });
        });
      });
    </script>
  </body>
</html>
