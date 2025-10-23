-- ========================================
-- BAKERS 데이터베이스 스키마
-- ========================================

-- 데이터베이스 생성
CREATE DATABASE IF NOT EXISTS bakers_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE bakers_db;

-- ========================================
-- 회원 테이블
-- ========================================
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    level TINYINT DEFAULT 1 COMMENT '1:일반, 9:관리자',
    point INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 빵집 테이블
-- ========================================
CREATE TABLE bakeries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    description TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    opening_hours VARCHAR(100),
    main_image VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 0.0,
    review_count INT DEFAULT 0,
    view_count INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES members(id) ON DELETE SET NULL,
    INDEX idx_name (name),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 리뷰 테이블
-- ========================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bakery_id INT NOT NULL,
    member_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    images TEXT COMMENT 'JSON 배열로 저장',
    visit_date DATE,
    likes INT DEFAULT 0,
    status ENUM('active', 'hidden') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (bakery_id) REFERENCES bakeries(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    INDEX idx_bakery_id (bakery_id),
    INDEX idx_member_id (member_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 북마크 테이블
-- ========================================
CREATE TABLE bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    bakery_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bookmark (member_id, bakery_id),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (bakery_id) REFERENCES bakeries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 샘플 데이터
-- ========================================

-- 관리자 계정 (ID: admin, PW: admin123)
INSERT INTO members (user_id, password, name, email, level) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '관리자', 'admin@bakers.com', 9);

-- 테스트 계정 (ID: test, PW: test123)
INSERT INTO members (user_id, password, name, email, phone) VALUES
('test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '김빵덕', 'test@bakers.com', '010-1234-5678');

-- 샘플 빵집 데이터
INSERT INTO bakeries (name, address, phone, description, opening_hours, rating, review_count, main_image, created_by) VALUES
('빵굼 베이커리', '서울특별시 강남구 테헤란로 123', '02-1234-5678', '신선한 빵을 매일 아침 구워내는 동네 빵집입니다.', '07:00-21:00', 4.5, 127, 'bakery1.jpg', 1),
('성심당', '대전광역시 중구 대종로 480', '042-252-4821', '대전의 명물 빵집, 튀김소보로가 유명합니다.', '08:00-22:00', 4.8, 2543, 'bakery2.jpg', 1),
('파리바게뜨 강남점', '서울특별시 서초구 강남대로 123', '02-2222-3333', '전국 체인 베이커리', '07:00-23:00', 4.2, 89, 'bakery3.jpg', 1),
('뚜레쥬르 송파점', '서울특별시 송파구 올림픽로 456', '02-3333-4444', '프리미엄 베이커리 카페', '08:00-22:00', 4.3, 156, 'bakery4.jpg', 1),
('크루아상 전문점', '서울특별시 마포구 홍대입구로 789', '02-4444-5555', '프랑스 정통 크루아상', '09:00-20:00', 4.6, 234, 'bakery5.jpg', 1),
('동네빵집', '서울특별시 용산구 이태원로 321', '02-5555-6666', '따뜻한 동네 빵집', '06:00-22:00', 4.4, 178, 'bakery6.jpg', 1);

-- 샘플 리뷰
INSERT INTO reviews (bakery_id, member_id, rating, title, content, visit_date, likes) VALUES
(1, 2, 5, '너무 맛있어요!', '크루아상이 정말 바삭하고 맛있습니다. 강추!', '2024-01-15', 15),
(2, 2, 5, '성심당은 역시 튀김소보로죠', '대전 갈 때마다 꼭 들르는 곳입니다.', '2024-01-10', 87),
(3, 2, 4, '무난한 체인점', '가까워서 자주 가는데 무난합니다.', '2024-01-20', 5);

-- 확인
SELECT '✅ 데이터베이스 생성 완료' AS status;
SELECT CONCAT('회원: ', COUNT(*), '명') AS info FROM members
UNION ALL
SELECT CONCAT('빵집: ', COUNT(*), '개') FROM bakeries
UNION ALL  
SELECT CONCAT('리뷰: ', COUNT(*), '개') FROM reviews;