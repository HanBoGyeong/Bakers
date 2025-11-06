

<div class="header-container">
    <!-- 로고 -->
    <a href="index.php">
        <img src="BAKERS.png" class="logo" alt="BAKERS" />
    </a>

    <div class="nav-with-search">
        <nav>
            <ul>
                <li><a href="recommend.php">추천빵집</a></li>
                <li><a href="nearby.php">내 주변</a></li>
                <li><a href="community.php">커뮤니티</a></li>
            </ul>
        </nav>

        <div class="search-container">
            <div class="search-icon" id="searchIcon">
                <!-- 돋보기 SVG -->
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </div>
            <div class="search-input-wrapper" id="searchInputWrapper">
                <input type="text" class="search-input" id="searchInput" placeholder="빵집을 검색하세요..." />
                <div class="search-close" id="searchClose">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="header-buttons">
        <?php if (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <!-- 로그인 상태일 때 -->
            <a href="mypage.php">
                <button class="btn btn-login">
                    <?php echo htmlspecialchars($_SESSION['nickname']); ?>님
                </button>
            </a>
            <a href="logout.php">
                <button class="btn btn-signup">로그아웃</button>
            </a>
        <?php else: ?>
            <!-- 로그인 안 했을 때 -->
            <a href="login.php">
                <button class="btn btn-login">로그인</button>
            </a>
            <a href="signup.php">
                <button class="btn btn-signup">회원가입</button>
            </a>
        <?php endif; ?>
    </div>
</div>

<script>
// 검색 기능
document.addEventListener('DOMContentLoaded', function() {
    const searchIcon = document.getElementById('searchIcon');
    const searchInput = document.getElementById('searchInput');

    if (searchIcon && searchInput) {
        searchIcon.addEventListener('click', function() {
            searchInput.focus();
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = searchInput.value.trim();
                if (query) {
                    window.location.href = 'search.php?q=' + encodeURIComponent(query);
                }
            }
        });
    }
});
</script>