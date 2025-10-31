<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BAKERS - 간편 회원가입</title>
    <link rel="stylesheet" href="main.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Noto Sans KR", sans-serif;
            background:rgb(255, 255, 255);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

       
        /* 로그인 컨테이너 */
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
        }

       

        .login-title {
            font-size: 26px;
            font-weight: 600;
            color: #1e3a8a;
            text-align: center;
            margin-bottom: 50px;
        }

        /* 폼 */
        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 500px;
            height: 48px;
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

        .form-links {
            text-align: right;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .form-links a {
            color: #868e96;
            text-decoration: none;
            margin-left: 10px;
        }

        .btn-login-submit {
            width: 100%;
            height: 48px;
            background: #e9ecef;
            color: #868e96;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 30px;
        }

        .social-divider {
            text-align: center;
            margin: 30px 0;
            color: #868e96;
            font-size: 13px;
        }

        .social-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .social-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        .social-btn:hover {
            transform: translateY(-2px);
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
            .header {
                flex-direction: column;
                gap: 15px;
            }
            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }
    </style>
  </head>

  <body>
    <!-- 헤더 -->
    <header class="header login">
      <?php include "header.php"; ?>
    </header>


    <!-- 회원가입 -->
    <div class="signup-container">
      <div class="signup-box">
        <h1 class="signup-title">간편 회원가입</h1>
        <p class="signup-sub">BAKERS에서 제공하는 빵의 다양한 경험을 함께 해보세요</p>

        <form>
          <div class="input-row">
            <input type="text" placeholder="아이디 영문, 숫자 조합">
            <button type="button" class="btn-check">중복확인</button>
          </div>

          <div class="input-row">
            <input type="password" placeholder="비밀번호 영문, 숫자, 특수문자">
            <input type="password" placeholder="비밀번호 확인">
          </div>

          <div class="input-row">
            <input type="text" placeholder="닉네임 입력">
            <input type="text" placeholder="휴대폰번호 -없이 입력">
          </div>

          <div class="agree-box">
            <label><input type="checkbox"> 전체 동의</label>
          </div>

          <button type="submit" class="btn-submit">회원가입</button>

          <div class="login-link">
            이미 계정이 있으신가요? <a href="login.php">로그인</a>
          </div>
        </form>
      </div>
    </div>

    <!-- 푸터 -->
    <footer>
      <?php include "footer.php"; ?>
    </footer>
  </body>
</html>
