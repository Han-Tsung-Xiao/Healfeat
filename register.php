<?php
session_start();

// 檢查是否已連接到資料庫
require_once 'config.php';
if (!isset($pdo)) {
    die('資料庫連接失敗，請檢查 config.php 中的 PDO 連接設置。');
}

// 處理註冊表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);
    $cellphone = trim($_POST['cellphone']);
    
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email) || empty($cellphone)) {
        $error = '所有欄位都必須填寫。';
    } elseif ($password !== $confirm_password) {
        $error = '密碼與確認密碼不一致。';
    } else {
        try {
            // 檢查用戶名是否已存在
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $error = '該用戶名已被使用。';
            } else {
                // 插入新用戶
                $hashedPassword = hash('sha256', $password);
                $stmt = $pdo->prepare('INSERT INTO users (username, password, email, cellphone) VALUES (:username, :password, :email, :cellphone)');
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':cellphone', $cellphone);
                $stmt->execute();
                header('Location: login.php');
                exit();
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
    <title>註冊</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('images/register.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .register-container {
            width: 400px;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .register-container h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
        }
        .register-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }
        .register-container button {
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
        .register-container button:hover {
            background-color: #005ecb;
        }
        .login-link {
            margin-top: 20px;
            font-size: 14px;
        }
        .login-link a {
            color: #007aff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .error {
            color: #d93025;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>註冊</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <input type="text" name="username" placeholder="帳號" required>
            <input type="email" name="email" placeholder="電子郵件" required>
            <input type="text" name="cellphone" placeholder="手機號碼" required>
            <input type="password" name="password" placeholder="密碼" required>
            <input type="password" name="confirm_password" placeholder="確認密碼" required>
            <button type="submit">註冊</button>
        </form>
        <div class="login-link">
            <p>已經有帳號了？<a href="login.php">立即登入</a></p>
        </div>
    </div>
</body>
</html>
