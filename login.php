<?php
session_start();
require_once 'config.php';

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = '帳號和密碼不能為空。';
    } else {
        try {
            // 從資料庫中查詢用戶
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && hash('sha256', $password) === $user['password']) {
                // 登入成功，將用戶資料存入 session
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['cellphone'] = $user['cellphone'];
                $_SESSION['email'] = $user['email'];

                // 顯示彈出式視窗
                echo "<script>
                    if (confirm('是否同意讀取您的健康數據？')) {
                        window.location.href = 'main.php';
                    } else {
                        alert('您必須同意讀取健康數據才能繼續。');
                    }
                </script>";
                exit();
            } else {
                $error = '帳號或密碼錯誤，請再試一次。';
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('images/login.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .login-container {
            width: 400px;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .login-container h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
        }
        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background-color: #007aff;
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-container button:hover {
            background-color: #005ecb;
        }
        .register-link {
            margin-top: 20px;
            font-size: 14px;
        }
        .register-link a {
            color: #007aff;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .error {
            color: #d93025;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>登入</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="帳號" required>
            <input type="password" name="password" placeholder="密碼" required>
            <button type="submit">登入</button>
        </form>
        <div class="register-link">
            <p>還沒有帳號？<a href="register.php">立即註冊</a></p>
        </div>
    </div>
</body>
</html>
