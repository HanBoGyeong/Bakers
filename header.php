<div class="header-container">
        <!-- ✅ 수정: 로고에 id 추가 -->
       <a href="index.php"> <img src="BAKERS.png" class="logo" id="bakersLogo" alt="BAKERS" />
       </a>
        <div class="nav-with-search">
          <nav>
            <ul>
              <!-- ✅ 수정: 추천빵집 메뉴에 id 추가 -->
              <li><a id="recommendLink">추천빵집</a></li>
              <li><a onclick="showPage('reviews')">내 주변</a></li>
              <li><a onclick="showPage('about')">커뮤니티</a></li>
            </ul>
          </nav>
          <div class="search-container">
            <div class="search-icon" id="searchIcon">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
              </svg>
            </div>
            <div class="search-input-wrapper" id="searchInputWrapper">
              <input
                type="text"
                class="search-input"
                id="searchInput"
                placeholder="빵집을 검색하세요..."
              />
              <div class="search-close" id="searchClose">
                <svg
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </div>
            </div>
          </div>
        </div>
        <div class="header-buttons">
          <a href="login.php"><button class="btn btn-login" >
            로그인
          </button></a>  
          <a href="signup.php"><button class="btn btn-signup" >
            회원가입
          </button></a>
          
        </div>
      </div>