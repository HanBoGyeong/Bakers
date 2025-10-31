-- ========================================
-- BAKERS 데이터베이스 스키마 (수정완료)
-- ========================================

-- 기존 데이터베이스 삭제 후 재생성 (주의: 모든 데이터가 삭제됩니다)
DROP DATABASE IF EXISTS bakers_db;

CREATE DATABASE bakers_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE bakers_db;

-- ========================================
-- 1. 회원 테이블 (FIRST - 외래키 참조되므로 먼저 생성)
-- ========================================
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    level TINYINT DEFAULT 1 COMMENT '1:일반, 9:관리자',
    point INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 2. 빵집 테이블 (SECOND - members를 참조)
-- ========================================
CREATE TABLE bakeries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    latitude DECIMAL(10, 8) DEFAULT NULL,
    longitude DECIMAL(11, 8) DEFAULT NULL,
    opening_hours VARCHAR(100) DEFAULT NULL,
    main_image VARCHAR(255) DEFAULT NULL,
    rating DECIMAL(2,1) DEFAULT 0.0,
    review_count INT DEFAULT 0,
    view_count INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_rating (rating),
    INDEX idx_status (status),
    INDEX idx_created_by (created_by),
    CONSTRAINT fk_bakeries_created_by 
        FOREIGN KEY (created_by) 
        REFERENCES members(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 3. 리뷰 테이블 (THIRD - members와 bakeries 둘 다 참조)
-- ========================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bakery_id INT NOT NULL,
    member_id INT NOT NULL,
    rating TINYINT NOT NULL,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    images TEXT DEFAULT NULL,
    visit_date DATE DEFAULT NULL,
    likes INT DEFAULT 0,
    status ENUM('active', 'hidden') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_bakery_id (bakery_id),
    INDEX idx_member_id (member_id),
    INDEX idx_rating (rating),
    INDEX idx_created_at (created_at),
    CONSTRAINT fk_reviews_bakery 
        FOREIGN KEY (bakery_id) 
        REFERENCES bakeries(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_reviews_member 
        FOREIGN KEY (member_id) 
        REFERENCES members(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 4. 북마크 테이블
-- ========================================
CREATE TABLE bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    bakery_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bookmark (member_id, bakery_id),
    INDEX idx_member_id (member_id),
    INDEX idx_bakery_id (bakery_id),
    CONSTRAINT fk_bookmarks_member 
        FOREIGN KEY (member_id) 
        REFERENCES members(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_bookmarks_bakery 
        FOREIGN KEY (bakery_id) 
        REFERENCES bakeries(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 샘플 데이터 삽입
-- ========================================

-- 1. 관리자 계정 (ID: admin, PW: admin123)
INSERT INTO members (user_id, password, name, email, level, status) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '관리자', 'admin@bakers.com', 9, 'active');

-- 2. 테스트 계정 (ID: test, PW: test123)
INSERT INTO members (user_id, password, name, email, phone, status) VALUES
('test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '김빵덕', 'test@bakers.com', '010-1234-5678', 'active');

-- 3. 추가 테스트 회원
INSERT INTO members (user_id, password, name, email, phone) VALUES
('user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '이크루아상', 'user1@bakers.com', '010-2222-3333'),
('user2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '박바게트', 'user2@bakers.com', '010-3333-4444');

-- 4. 샘플 빵집 데이터
INSERT INTO bakeries (name, address, phone, description, opening_hours, rating, review_count, main_image, created_by, status) VALUES
('빵굼 베이커리', '서울특별시 강남구 테헤란로 123', '02-1234-5678', '신선한 빵을 매일 아침 구워내는 동네 빵집입니다. 특히 크루아상과 바게트가 인기메뉴입니다.', '07:00-21:00', 4.5, 127, 'bakery1.jpg', 1, 'active'),
('성심당', '대전광역시 중구 대종로 480', '042-252-4821', '대전의 명물 빵집, 튀김소보로가 유명합니다. 1956년부터 3대째 이어온 전통있는 베이커리입니다.', '08:00-22:00', 4.8, 2543, 'bakery2.jpg', 1, 'active'),
('파리바게뜨 강남점', '서울특별시 서초구 강남대로 123', '02-2222-3333', '전국 체인 베이커리. 다양한 빵과 케이크를 합리적인 가격에 만나보세요.', '07:00-23:00', 4.2, 89, 'bakery3.jpg', 2, 'active'),
('뚜레쥬르 송파점', '서울특별시 송파구 올림픽로 456', '02-3333-4444', '프리미엄 베이커리 카페. 편안한 분위기에서 갓 구운 빵을 즐기세요.', '08:00-22:00', 4.3, 156, 'bakery4.jpg', 2, 'active'),
('크루아상 전문점', '서울특별시 마포구 홍대입구로 789', '02-4444-5555', '프랑스 정통 크루아상을 선보이는 베이커리. 매일 아침 직접 만드는 버터 크루아상이 일품입니다.', '09:00-20:00', 4.6, 234, 'bakery5.jpg', 1, 'active'),
('동네빵집', '서울특별시 용산구 이태원로 321', '02-5555-6666', '따뜻한 동네 빵집. 어렸을 때 먹던 그 맛을 그대로 재현합니다.', '06:00-22:00', 4.4, 178, 'bakery6.jpg', 2, 'active'),
('맘모스 베이커리', '서울특별시 종로구 인사동길 100', '02-6666-7777', '인사동의 숨은 명소. 전통 찐빵과 모던한 디저트의 조화.', '10:00-21:00', 4.7, 312, 'bakery7.jpg', 1, 'active'),
('브레드 가든', '경기도 성남시 분당구 정자동 200', '031-1111-2222', '분당의 프리미엄 베이커리. 유기농 재료만을 사용합니다.', '08:00-22:00', 4.5, 145, 'bakery8.jpg', 1, 'active'),
('제과왕 김탁구', '서울특별시 강서구 화곡동 300', '02-7777-8888', '드라마처럼 정성을 다해 만드는 빵집. 팥빵이 시그니처 메뉴입니다.', '07:30-20:30', 4.3, 98, 'bakery9.jpg', 2, 'active'),
('르뱅 베이커리', '서울특별시 영등포구 여의도동 400', '02-8888-9999', '천연발효종으로 만드는 건강한 빵. 직장인들에게 인기 만점.', '07:00-21:00', 4.6, 267, 'bakery10.jpg', 1, 'active');

-- 5. 샘플 리뷰
INSERT INTO reviews (bakery_id, member_id, rating, title, content, visit_date, likes, status) VALUES
(1, 2, 5, '너무 맛있어요!', '크루아상이 정말 바삭하고 맛있습니다. 버터향이 진하고 겉은 바삭 속은 촉촉해요. 강추합니다!', '2024-01-15', 15, 'active'),
(2, 2, 5, '성심당은 역시 튀김소보로죠', '대전 갈 때마다 꼭 들르는 곳입니다. 튀김소보로 정말 맛있어요. 줄 서서 사 먹을 가치가 있습니다!', '2024-01-10', 87, 'active'),
(3, 2, 4, '무난한 체인점', '가까워서 자주 가는데 무난합니다. 빵 종류도 다양하고 가격도 합리적이에요.', '2024-01-20', 5, 'active'),
(1, 3, 5, '재방문 의사 100%', '친절하고 빵도 신선해요. 다음에 또 올게요!', '2024-01-18', 8, 'active'),
(4, 3, 4, '깔끔한 인테리어', '매장이 깨끗하고 분위기가 좋아요. 빵맛도 괜찮습니다.', '2024-01-12', 12, 'active'),
(5, 4, 5, '크루아상 맛집 인정!', '여기 크루아상 진짜 맛있어요. 프랑스에서 먹던 그 맛이에요.', '2024-01-08', 23, 'active'),
(6, 4, 4, '옛날 빵 좋아하시는 분께 추천', '어렸을 때 생각나는 빵 맛이에요. 향수를 느낄 수 있습니다.', '2024-01-05', 7, 'active'),
(2, 3, 5, '대전 여행 필수코스', '성심당 없이는 대전 여행이 완성되지 않죠. 최고입니다!', '2024-01-22', 45, 'active');

-- 6. 샘플 북마크
INSERT INTO bookmarks (member_id, bakery_id) VALUES
(2, 1),
(2, 2),
(2, 5),
(3, 1),
(3, 4),
(4, 5),
(4, 6);

-- ========================================
-- 데이터 확인
-- ========================================
SELECT '✅ 데이터베이스 생성 완료!' AS '상태';

SELECT '📊 데이터 통계' AS '';
SELECT CONCAT('회원: ', COUNT(*), '명') AS '정보' FROM members
UNION ALL
SELECT CONCAT('빵집: ', COUNT(*), '개') FROM bakeries
UNION ALL  
SELECT CONCAT('리뷰: ', COUNT(*), '개') FROM reviews
UNION ALL
SELECT CONCAT('북마크: ', COUNT(*), '개') FROM bookmarks;

-- 테이블 구조 확인
SELECT '📋 생성된 테이블 목록' AS '';
SHOW TABLES;

-- 완료 메시지
SELECT '🎉 설치 완료! http://localhost/bakers 에서 확인하세요.' AS '안내';