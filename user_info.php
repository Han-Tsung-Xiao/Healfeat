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

// 獲取用戶健康數據
$sql_health = "SELECT * FROM user_health WHERE user_id = $user_id";
$result_health = $mysqli->query($sql_health);
$user_health = $result_health->fetch_assoc() ?? [];
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用戶資訊</title>
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

        .user-info {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .user-info h2 {
            text-align: center;
        }

        .user-info label {
            display: block;
            margin-top: 10px;
        }

        .user-info p {
            margin: 5px 0;
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

    <section class="user-info">
        <h2>用戶資訊</h2>
        <div>
            <label>用戶名:</label>
            <p><?php echo htmlspecialchars($user['username']); ?></p>
        </div>
        <div>
            <label>電子郵件:</label>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        <div>
            <label>身高:</label>
            <p><?php echo htmlspecialchars($user_health['height'] ?? '尚未填寫'); ?></p>
        </div>
        <div>
            <label>體重:</label>
            <p><?php echo htmlspecialchars($user_health['weight'] ?? '尚未填寫'); ?></p>
        </div>
        <div>
            <label>年齡:</label>
            <p><?php echo htmlspecialchars($user_health['age'] ?? '尚未填寫'); ?></p>
        </div>
        <div>
            <label>心跳:</label>
            <p><?php echo htmlspecialchars($user_health['heart_rate'] ?? '尚未填寫'); ?></p>
        </div>
        <div>
            <label>血壓:</label>
            <p><?php echo htmlspecialchars($user_health['blood_pressure'] ?? '尚未填寫'); ?></p>
        </div>
        <div>
            <label>血氧:</label>
            <p><?php echo htmlspecialchars($user_health['blood_oxygen'] ?? '尚未填寫'); ?></p>
        </div>
        <div>
            <label>步數:</label>
            <p><?php echo htmlspecialchars($user_health['steps'] ?? '尚未填寫'); ?></p>
        </div>
        <div>
            <label>擅長運動:</label>
            <p><?php echo htmlspecialchars($user_health['favorite_sport'] ?? '尚未填寫'); ?></p>
        </div>

        <a href="main.php" class="back-button">返回主頁</a>
    </section>
</body>
</html>
