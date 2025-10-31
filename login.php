<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BAKERS - 로그인</title>
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
            width: 400px;
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

  <!-- 로그인 -->
  <div class="login-container">
        <div class="login-box">
            <h1 class="login-title">로그인</h1>

            <form>
                <div class="form-group">
                    <label>아이디</label>
                    <input type="text" placeholder="아이디를 입력해주세요.">
                </div>

                <div class="form-group">
                    <label>비밀번호</label>
                    <input type="password" placeholder="비밀번호를 입력해주세요.">
                </div>

                <div class="form-links">
                    <a>아이디·비밀번호 찾기</a>
                    <span style="color: #dee2e6;">|</span>
                    <a>회원가입</a>
                </div>

                <button type="button" class="btn-login-submit" onclick="alert('로그인 기능 데모')">로그인</button>
            </form>

            <div class="social-divider">
                이용하실려면 로그인해주세요.
            </div>

            <div class="social-buttons">
                <button type="button" class="social-btn" style="background: #FEE500;" onclick="alert('카카오 로그인 준비중')" title="카카오 로그인">
                    <svg width="22" height="22" viewBox="0 0 24 24">
                        <path d="M12 3C6.5 3 2 6.6 2 11c0 2.8 1.9 5.3 4.7 6.7l-1.5 5.5c-.1.4.3.7.6.5l6.4-4.3c.6.1 1.2.1 1.8.1 5.5 0 10-3.6 10-8S17.5 3 12 3z" fill="#000"/>
                    </svg>
                </button>
                <button type="button" class="social-btn" style="background: #03C75A;" onclick="alert('네이버 로그인 준비중')" title="네이버 로그인">
                    <svg width="15" height="15" viewBox="0 0 24 24">
                        <path d="M16.273 12.845L7.376 0H0v24h7.726V11.156L16.624 24H24V0h-7.727v12.845z" fill="white"/>
                    </svg>
                </button>
                <button type="button" class="social-btn" style="background: white; border: 1px solid #dee2e6;" onclick="alert('구글 로그인 준비중')" title="구글 로그인">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                </button>
                <button type="button" class="social-btn" style="background: #1877F2;" onclick="alert('페이스북 로그인 준비중')" title="페이스북 로그인">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="white"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- 푸터 -->
    <footer>
      <?php include "footer.php"; ?>
    </footer>
  </body>
</html>
