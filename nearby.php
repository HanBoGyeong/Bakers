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

// 빵집 목록 가져오기
$bakeries_query = "SELECT * FROM bakeries WHERE status = 'active' ORDER BY rating DESC";
$bakeries_result = $conn->query($bakeries_query);
$bakeries = [];
if ($bakeries_result) {
    while ($row = $bakeries_result->fetch_assoc()) {
        $bakeries[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>내 주변 빵집 - BAKERS</title>
    <link rel="stylesheet" href="main.css">
    <style>
        body {
            font-family: "Noto Sans KR", sans-serif;
            margin: 0;
            background: #f8f8f0
        }
        .nearby-container {
            max-width: 1400px;
            margin: 80px auto 40px;
            padding: 0 20px;
        }
        .page-header {
            text-align: center;
            margin-bottom: 40px;
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
        
        /* 지도와 리스트 레이아웃 */
        .content-wrapper {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 24px;
            height: calc(100vh - 200px);
            min-height: 600px;
        }
        
        /* 빵집 리스트 */
        .bakery-list {
            background: white;
            border-radius: 12px;
            padding: 20px;
            overflow-y: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e0e0e0;
        }
        .list-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .list-count {
            color: #1e3a8a;
            font-weight: 600;
        }
        .bakery-item {
            padding: 16px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s;
        }
        .bakery-item:hover {
            background: #f8f9fa;
        }
        .bakery-item.active {
            background: #e7f3ff;
            border-left: 3px solid #1e3a8a;
        }
        .item-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .item-address {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }
        .item-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .rating-stars {
            color: #ffa500;
        }
        .distance {
            color: #1e3a8a;
            font-size: 13px;
            font-weight: 600;
        }
        
        /* 지도 영역 */
        .map-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: relative;
        }
        #map {
            width: 100%;
            height: 100%;
            min-height: 600px;
        }
        .map-controls {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 10;
            display: flex;
            gap: 10px;
        }
        .btn-control {
            background: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-control:hover {
            background: #f8f9fa;
        }
        .btn-control.active {
            background: #1e3a8a;
            color: white;
        }
        
        /* 모바일 반응형 */
        @media (max-width: 768px) {
            .content-wrapper {
                grid-template-columns: 1fr;
                height: auto;
            }
            .bakery-list {
                height: 300px;
            }
            #map {
                height: 400px;
            }
        }
        
        /* 위치 권한 알림 */
        .location-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .location-notice-icon {
            font-size: 24px;
        }
        .location-notice-text {
            font-size: 14px;
            color: #856404;
        }
    </style>
</head>
<body>
    <!-- 헤더 -->
    <header class="header">
        <?php include 'header.php'; ?>
    </header>

    <!-- 메인 컨텐츠 -->
    <div class="nearby-container">
        <div class="page-header">
            <h1>📍 내 주변 빵집</h1>
            <p>지도에서 가까운 빵집을 찾아보세요</p>
        </div>

        <div id="locationNotice" class="location-notice" style="display: none;">
            <span class="location-notice-icon">📌</span>
            <div class="location-notice-text">
                현재 위치를 확인하려면 브라우저에서 위치 권한을 허용해주세요.
            </div>
        </div>

        <div class="content-wrapper">
            <!-- 빵집 리스트 -->
            <div class="bakery-list">
                <div class="list-header">
                    <span class="list-title">빵집 목록</span>
                    <span class="list-count"><?= count($bakeries) ?>개</span>
                </div>
                <div id="bakeryListContainer">
                    <?php foreach ($bakeries as $index => $bakery): ?>
                    <div class="bakery-item" data-id="<?= $bakery['id'] ?>" 
                         data-lat="<?= $bakery['latitude'] ?? '37.5665' ?>" 
                         data-lng="<?= $bakery['longitude'] ?? '126.9780' ?>"
                         onclick="selectBakery(<?= $bakery['id'] ?>, <?= $bakery['latitude'] ?? '37.5665' ?>, <?= $bakery['longitude'] ?? '126.9780' ?>)">
                        <div class="item-name"><?= htmlspecialchars($bakery['name']) ?></div>
                        <div class="item-address"><?= htmlspecialchars($bakery['address']) ?></div>
                        <div class="item-rating">
                            <span class="rating-stars">⭐ <?= $bakery['rating'] ?></span>
                            <span style="color: #999;">(<?= $bakery['review_count'] ?>)</span>
                            <span class="distance" id="distance-<?= $bakery['id'] ?>">-</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 지도 -->
            <div class="map-container">
                <div class="map-controls">
                    <button class="btn-control" onclick="getCurrentLocation()">
                        📍 내 위치
                    </button>
                    <button class="btn-control" onclick="showAllBakeries()">
                        🏪 전체 보기
                    </button>
                </div>
                <div id="map"></div>
            </div>
        </div>
    </div>

    <!-- 푸터 -->
    <footer>
        <?php include 'footer.php'; ?>
    </footer>

    <!-- 카카오맵 API -->
    <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=YOUR_KAKAO_MAP_API_KEY"></script>
    <script>
        let map;
        let markers = [];
        let userMarker = null;
        let userPosition = null;

        // 빵집 데이터
        const bakeries = <?= json_encode($bakeries) ?>;

        // 지도 초기화
        function initMap() {
            const container = document.getElementById('map');
            const options = {
                center: new kakao.maps.LatLng(37.5665, 126.9780), // 서울 시청 기본 위치
                level: 5
            };

            map = new kakao.maps.Map(container, options);

            // 빵집 마커 표시
            bakeries.forEach(bakery => {
                const lat = parseFloat(bakery.latitude) || 37.5665;
                const lng = parseFloat(bakery.longitude) || 126.9780;
                addMarker(lat, lng, bakery.name, bakery.id);
            });

            // 현재 위치 가져오기
            getCurrentLocation();
        }

        // 마커 추가
        function addMarker(lat, lng, name, id) {
            const position = new kakao.maps.LatLng(lat, lng);
            const marker = new kakao.maps.Marker({
                position: position,
                map: map
            });

            // 정보창
            const infowindow = new kakao.maps.InfoWindow({
                content: `<div style="padding:10px;font-size:14px;">${name}</div>`
            });

            kakao.maps.event.addListener(marker, 'mouseover', function() {
                infowindow.open(map, marker);
            });

            kakao.maps.event.addListener(marker, 'mouseout', function() {
                infowindow.close();
            });

            kakao.maps.event.addListener(marker, 'click', function() {
                selectBakery(id, lat, lng);
            });

            markers.push({ marker, id, position });
        }

        // 현재 위치 가져오기
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        userPosition = new kakao.maps.LatLng(lat, lng);

                        // 사용자 위치 마커
                        if (userMarker) {
                            userMarker.setMap(null);
                        }

                        userMarker = new kakao.maps.Marker({
                            position: userPosition,
                            map: map,
                            image: new kakao.maps.MarkerImage(
                                'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSI4IiBmaWxsPSIjMWU3M2JlIiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjMiLz48L3N2Zz4=',
                                new kakao.maps.Size(24, 24)
                            )
                        });

                        map.setCenter(userPosition);
                        calculateDistances(lat, lng);
                    },
                    function(error) {
                        console.error('위치 정보를 가져올 수 없습니다:', error);
                        document.getElementById('locationNotice').style.display = 'flex';
                    }
                );
            } else {
                alert('이 브라우저는 위치 정보를 지원하지 않습니다.');
            }
        }

        // 거리 계산
        function calculateDistances(userLat, userLng) {
            bakeries.forEach(bakery => {
                const bakeryLat = parseFloat(bakery.latitude) || 37.5665;
                const bakeryLng = parseFloat(bakery.longitude) || 126.9780;
                const distance = getDistance(userLat, userLng, bakeryLat, bakeryLng);
                
                const distanceEl = document.getElementById(`distance-${bakery.id}`);
                if (distanceEl) {
                    distanceEl.textContent = distance < 1 
                        ? `${Math.round(distance * 1000)}m` 
                        : `${distance.toFixed(1)}km`;
                }
            });
        }

        // 두 지점 간 거리 계산 (km)
        function getDistance(lat1, lng1, lat2, lng2) {
            const R = 6371; // 지구 반지름 (km)
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                     Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                     Math.sin(dLng/2) * Math.sin(dLng/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // 빵집 선택
        function selectBakery(id, lat, lng) {
            // 리스트 아이템 활성화
            document.querySelectorAll('.bakery-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`[data-id="${id}"]`).classList.add('active');

            // 지도 이동
            const position = new kakao.maps.LatLng(lat, lng);
            map.setCenter(position);
            map.setLevel(3);
        }

        // 전체 빵집 보기
        function showAllBakeries() {
            if (markers.length > 0) {
                const bounds = new kakao.maps.LatLngBounds();
                markers.forEach(({position}) => {
                    bounds.extend(position);
                });
                map.setBounds(bounds);
            }
        }

        // 페이지 로드 시 지도 초기화
        window.onload = function() {
            initMap();
        };
    </script>
</body>
</html>
<?php $conn->close(); ?>