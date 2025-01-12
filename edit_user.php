<?php
// 資料庫連接設定
$host = 'localhost';
$dbname = 'healfeatdb';
$username = 'root';
$password = 'Armvc26...73a';

// 建立 MySQL 連接
$mysqli = new mysqli($host, $username, $password, $dbname);

// 檢查連接是否成功
if ($mysqli->connect_error) {
    die("連接資料庫失敗: " . $mysqli->connect_error);
}

// 假設用戶已經登入，並且存儲了用戶 ID（這裡用 1 作為範例）
session_start();
$user_id = $_SESSION['id'] ?? 1;

// 獲取用戶資料
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $mysqli->query($sql);
$user = $result->fetch_assoc();

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];
    $new_password_confirm = $_POST['password_confirm'];

    // 驗證密碼是否相符
    if ($new_password !== $new_password_confirm) {
        $error = "密碼不相符！";
    } else {
        // 更新用戶資料
        if (!empty($new_password)) {
            // 如果密碼有更改，先加密密碼
            $new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $mysqli->prepare($sql_update);
            $stmt->bind_param('sssi', $new_username, $new_email, $new_password, $user_id);
        } else {
            $sql_update = "UPDATE users SET username = ?, email = ? WHERE id = ?";
            $stmt = $mysqli->prepare($sql_update);
            $stmt->bind_param('ssi', $new_username, $new_email, $user_id);
        }
        
        if ($stmt->execute()) {
            // 更新成功，跳轉回主頁
            header("Location: main.php");
            exit;
        } else {
            $error = "更新資料時發生錯誤！";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更改用戶資訊</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header nav ul {
            list-style: none;
            display: flex;
        }

        header nav ul li {
            margin-right: 20px;
        }

        header nav ul li a {
            color: white;
            text-decoration: none;
        }

        .user-menu {
            position: relative;
        }

        .user-menu button {
            background-color: #3498db;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 40px;
            background-color: white;
            border: 1px solid #ccc;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .dropdown-menu a {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            color: #333;
        }

        .dropdown-menu a:hover {
            background-color: #f1f1f1;
        }

        .dropdown-menu.show {
            display: block;
        }

        .edit-form {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .edit-form h2 {
            text-align: center;
        }

        .edit-form label {
            display: block;
            margin-top: 10px;
        }

        .edit-form input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-form button {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .back-button {
            display: block;
            margin: 20px auto;
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .back-button:hover {
            background-color: #2980b9;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownButton = document.getElementById('user-dropdown');
            const dropdownMenu = document.getElementById('user-dropdown-menu');
            dropdownButton.addEventListener('click', () => dropdownMenu.classList.toggle('show'));
        });
    </script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="health_guide.php">健康指南</a></li>
                <li><a href="exercise_lifestyle.php">運動生活</a></li>
            </ul>
        </nav>
        <div class="user-menu">
            <button id="user-dropdown"><?php echo htmlspecialchars($user['username']); ?> ▼</button>
            <div id="user-dropdown-menu" class="dropdown-menu">
                <a href="user_info.php">用戶資訊</a>
                <a href="edit_user.php">更改用戶資訊</a>
                <a href="logout.php">登出</a>
            </div>
        </div>
    </header>

    <section class="edit-form">
        <h2>更改用戶資訊</h2>
        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <form method="POST">
            <label>用戶名:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label>電子郵件:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>新密碼 (若不修改則可空白):</label>
            <input type="password" name="password">

            <label>確認新密碼:</label>
            <input type="password" name="password_confirm">

            <button type="submit">提交更改</button>
        </form>

        <a href="main.php" class="back-button">返回主頁</a>
    </section>
</body>
</html>
