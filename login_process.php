<?php
require_once '../config.php';

// 이미 로그인되어 있으면 메인으로 이동
if (is_logged_in()) {
    header('Location: ' . url());
    exit;
}

$page_title = '회원가입 - BAKERS';
?>
<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="main.css">
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Noto Sans KR", sans-serif;
        background: rgb(255, 255, 255);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
      }

      /* 중앙 회원가입 컨테이너 */
      .signup-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
      }

      .signup-box {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 50px 60px;
        width: 500px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
      }

      .signup-title {
        font-size: 26px;
        font-weight: 600;
        color: #1e3a8a;
        text-align: center;
        margin-bottom: 30px;
      }

      .signup-desc {
        text-align: center;
        color: #555;
        font-size: 14px;
        margin-bottom: 40px;
      }

      /* 입력 그룹 */
      .form-group {
        margin-bottom: 22px;
      }

      .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
      }

      .form-group input {
        width: 100%;
        height: 45px;
        padding: 0 16px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 14px;
      }

      .form-group input:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
      }

      /* ID 중복확인 버튼 */
      .input-with-button {
        display: flex;
        gap: 10px;
      }

      .btn-check {
        padding: 0 18px;
        height: 45px;
        background: white;
        color: #1e3a8a;
        border: 1px solid #1e3a8a;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s;
      }

      .btn-check:hover {
        background: #f8f9fa;
      }

      .btn-signup-submit {
        width: 100%;
        height: 48px;
        background: #e9ecef;
        color: #868e96;
        border: none;
        border-radius: 6px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
        transition: background 0.2s;
      }

      .btn-signup-submit:hover {
        background: #dee2e6;
      }

      .agreement {
        display: flex;
        align-items: center;
        font-size: 13px;
        color: #555;
        margin-bottom: 20px;
      }

      .agreement input {
        margin-right: 8px;
      }

      .login-link {
        text-align: center;
        font-size: 13px;
        margin-top: 20px;
      }

      .login-link a {
        color: #1e3a8a;
        text-decoration: none;
        font-weight: 500;
      }

      .login-link a:hover {
        text-decoration: underline;
      }

      /* 푸터 */
      .footer {
        background: #1e3a8a;
        color: white;
        padding: 40px 20px 20px;
      }

      .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 40px;
        margin-bottom: 30px;
      }

      .footer-brand h3 {
        font-size: 18px;
        letter-spacing: 2px;
      }

      .footer-brand p {
        font-size: 11px;
        opacity: 0.8;
      }

      .footer-column h4 {
        font-size: 13px;
        margin-bottom: 15px;
      }

      .footer-column ul {
        list-style: none;
      }

      .footer-column a {
        color: white;
        font-size: 12px;
        opacity: 0.7;
        line-height: 2;
        cursor: pointer;
      }

      .footer-bottom {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 11px;
        opacity: 0.6;
      }

      @media (max-width: 768px) {
        .footer-content {
          grid-template-columns: 1fr;
          text-align: center;
        }
        .signup-box {
          width: 100%;
          padding: 40px 24px;
        }
      }
    </style>
  </head>

  <body>
    <!-- 헤더 -->
    <header class="header login">
      <?php include "header.php"; ?>
    </header>

    <!-- 회원가입 영역 -->
    <div class="signup-container">
      <div class="signup-box">
        <h1 class="signup-title">간편 회원가입</h1>
        <p class="signup-desc">BAKERS에서 제공하는 빵의 다양한 경험을 함께 해보세요</p>

        <form action="<?= url('auth/signup_process.php') ?>" method="POST" id="signupForm">
          <div class="form-group">
            <div class="input-with-button">
              <input type="text" id="user_id" name="user_id" placeholder="아이디 영문, 숫자 조합" required>
              <button type="button" class="btn-check" onclick="checkId()">중복확인</button>
            </div>
            <div id="idCheckMessage" class="form-message" style="display:none;font-size:13px;margin-top:6px;"></div>
          </div>

          <div class="form-group">
            <input type="password" id="password" name="password" placeholder="비밀번호 영문, 숫자, 특수문자" required>
          </div>

          <div class="form-group">
            <input type="password" id="password_confirm" name="password_confirm" placeholder="비밀번호 확인" required>
          </div>

          <div class="form-group">
            <input type="text" id="nickname" name="nickname" placeholder="닉네임 입력" required>
          </div>

          <div class="form-group">
            <input type="tel" id="phone" name="phone" placeholder="휴대폰번호 -없이 입력" required>
          </div>

          <div class="agreement">
            <input type="checkbox" id="agree_all" required>
            <label for="agree_all">전체 동의</label>
          </div>

          <button type="submit" class="btn-signup-submit">회원가입</button>

          <div class="login-link">
            이미 계정이 있으신가요? <a href="<?= url('auth/login.php') ?>">로그인</a>
          </div>
        </form>
      </div>
    </div>

    <!-- 푸터 -->
    <footer>
      <?php include "footer.php"; ?>
    </footer>

    <script>
      let idChecked = false;

      // 아이디 중복확인
      function checkId() {
        const userId = document.getElementById('user_id').value.trim();
        const msg = document.getElementById('idCheckMessage');

        if (!userId) {
          msg.textContent = '아이디를 입력해주세요.';
          msg.style.color = '#c92a2a';
          msg.style.display = 'block';
          return;
        }

        fetch('<?= url('auth/check_id.php') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'user_id=' + encodeURIComponent(userId)
        })
        .then(res => res.json())
        .then(data => {
          msg.textContent = data.message;
          msg.style.color = data.success ? '#2b8a3e' : '#c92a2a';
          msg.style.display = 'block';
          idChecked = data.success;
        })
        .catch(() => {
          msg.textContent = '오류가 발생했습니다.';
          msg.style.color = '#c92a2a';
          msg.style.display = 'block';
        });
      }

      // 비밀번호 확인 및 ID 중복체크 검증
      document.getElementById('signupForm').addEventListener('submit', e => {
        const pw = document.getElementById('password').value;
        const pw2 = document.getElementById('password_confirm').value;
        if (!idChecked) {
          e.preventDefault();
          alert('아이디 중복확인을 해주세요.');
          return;
        }
        if (pw !== pw2) {
          e.preventDefault();
          alert('비밀번호가 일치하지 않습니다.');
        }
      });
    </script>
  </body>
</html>
