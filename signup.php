<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BAKERS - 간편 회원가입</title>
    <link rel="stylesheet" href="main.css">
    <style>
    body {
      font-family: "Noto Sans KR", sans-serif;
      margin: 0;
      background: #fff;
      color: #111;
    }
    .container {
      max-width: 400px;
      margin: 100px auto;
      text-align: center;
    }
    h1 {
      color: #1e3a8a;
      font-size: 24px;
      margin-bottom: 10px;
    }
    p.sub {
      color: #666;
      font-size: 13px;
      margin-bottom: 40px;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }
    input {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    .input-group {
      display: flex;
      gap: 8px;
    }
    .btn-check {
      padding: 0 12px;
      border: 1px solid #1e3a8a;
      background: #fff;
      color: #1e3a8a;
      border-radius: 6px;
      cursor: pointer;
      font-size: 13px;
    }
    .btn-check:hover {
      background: #f3f6ff;
    }
    .agreement {
      display: flex;
      align-items: flex-start;
      gap: 8px;
      text-align: left;
      font-size: 13px;
      color: #444;
    }
    .agreement small {
      display: block;
      color: #888;
      font-size: 12px;
      margin-top: 4px;
    }
    .btn-submit {
      padding: 12px;
      background: #1e3a8a;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      cursor: pointer;
    }
    .btn-submit:hover {
      background: #16347a;
    }
    .login-link {
      margin-top: 30px;
      font-size: 13px;
      color: #555;
    }
    .login-link a {
      color: #1e3a8a;
      text-decoration: none;
    }
  </style>
  </head>

  <body>
    <!-- 헤더 -->
    <header class="header login">
      <?php include "header.php"; ?>
    </header>


    <!-- 회원가입 -->
    <div class="container">
    <h1>간편 회원가입</h1>
    <p class="sub">BAKERS에서 제공하는 빵의 다양한 경험을 해보세요</p>

    <form id="signupForm" method="POST" action="signup_process.php">
      <div class="input-group">
        <input type="text" id="user_id" name="user_id" placeholder="아이디 영문, 숫자 조합" required>
        <button type="button" class="btn-check" onclick="checkId()">중복확인</button>
      </div>
      <small id="idMessage" style="display:none;"></small>

      <input type="password" name="password" placeholder="비밀번호 영문, 숫자, 특수문자 조합" required>
      <input type="text" name="nickname" placeholder="닉네임 입력" required>
      <input type="tel" name="phone" placeholder="휴대폰번호 -없이 입력" required>

      <label class="agreement">
        <input type="checkbox" id="agree" required>
        <div>
          전체 동의
          <small>전체 동의시 필수 및 선택 항목 모두에 대해 동의 처리되며, 선택 항목 동의를 거부하셔도 서비스 이용이 가능합니다.</small>
        </div>
      </label>

      <button type="submit" class="btn-submit">회원가입</button>
      <p class="login-link">이미 계정이 있으신가요? <a href="login.php">로그인</a></p>
    </form>
  </div>

  <script>
    let idChecked = false;
    function checkId() {
      const userId = document.getElementById('user_id').value.trim();
      const msg = document.getElementById('idMessage');
      if (!userId) {
        msg.textContent = '아이디를 입력해주세요.';
        msg.style.color = '#d32f2f';
        msg.style.display = 'block';
        return;
      }
      fetch('check_id.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'user_id=' + encodeURIComponent(userId)
      })
      .then(res => res.json())
      .then(data => {
        msg.textContent = data.message;
        msg.style.color = data.success ? '#2b8a3e' : '#d32f2f';
        msg.style.display = 'block';
        idChecked = data.success;
      })
      .catch(() => {
        msg.textContent = '서버 오류가 발생했습니다.';
        msg.style.color = '#d32f2f';
        msg.style.display = 'block';
      });
    }

    document.getElementById('signupForm').addEventListener('submit', e => {
      if (!idChecked) {
        e.preventDefault();
        alert('아이디 중복확인을 해주세요.');
      }
    });
  </script>

    <!-- 푸터 -->
    <footer>
      <?php include "footer.php"; ?>
    </footer>
  </body>
</html>
