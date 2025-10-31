<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BAKERS - 빵집 리뷰 커뮤니티</title>
    <link rel="stylesheet" href="main.css">
  </head>
  <body>
    <!-- 헤더 -->
    <header>
     <?php include "header.php" ?>

    </header>

    <!-- 홈 페이지 (메인 랜딩 페이지) -->
   로그인 페이지

    <!-- 푸터 -->
    <footer>
    <?php include "footer.php" ?>
      
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
