<?php
// 啟用錯誤報告
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// 確認用戶已登入
session_start();
if (!isset($_SESSION['id'])) {
    echo "<script>alert('請先登入。');</script>";
    exit();
}
$user_id = $_SESSION['id'];

// 獲取用戶資料
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// 獲取用戶健康數據
$sql_health = "SELECT * FROM user_health WHERE id = ?";
$stmt = $mysqli->prepare($sql_health);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result_health = $stmt->get_result();
$user_health = $result_health->fetch_assoc() ?? [];
$stmt->close();

// 處理健康數據表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_health'])) {
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $age = $_POST['age'];
    $heart_rate = $_POST['heart_rate'];
    $blood_pressure = $_POST['blood_pressure'];
    $blood_oxygen = $_POST['blood_oxygen'];
    $steps = $_POST['steps'];
    $favorite_sport = $_POST['favorite_sport'];

    // 驗證輸入
    if (!is_numeric($height) || !is_numeric($weight) || !is_numeric($age) || !is_numeric($heart_rate) || !is_numeric($blood_oxygen) || !is_numeric($steps)) {
        $error = "請填寫有效的數據！";
    } else {
        // 檢查是否已有健康數據
        if ($user_health) {
            $sql_update = "UPDATE user_health SET 
                height = ?, weight = ?, age = ?, heart_rate = ?, blood_pressure = ?, 
                blood_oxygen = ?, steps = ?, favorite_sport = ?
                WHERE user_id = ?";
            $stmt = $mysqli->prepare($sql_update);
            $stmt->bind_param('ddiidsssi', $height, $weight, $age, $heart_rate, $blood_pressure, $blood_oxygen, $steps, $favorite_sport, $user_id);
        } else {
            $sql_insert = "INSERT INTO user_health 
                (user_id, height, weight, age, heart_rate, blood_pressure, blood_oxygen, steps, favorite_sport)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql_insert);
            $stmt->bind_param('ididsssis', $user_id, $height, $weight, $age, $heart_rate, $blood_pressure, $blood_oxygen, $steps, $favorite_sport);
        }
        $stmt->execute();
        $stmt->close();

        // 重新加載健康數據
        $sql_health = "SELECT * FROM user_health WHERE user_id = ?";
        $stmt = $mysqli->prepare($sql_health);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result_health = $stmt->get_result();
        $user_health = $result_health->fetch_assoc();
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>健康數據填寫</title>
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
            z-index: 1000;
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

        .health-form {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .health-form h2 {
            text-align: center;
        }

        .health-form label {
            display: block;
            margin-top: 10px;
        }

        .health-form input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .health-form button {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
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
                <li><a href="sport.php">運動生活</a></li>
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

    <section class="health-form">
        <h2>填寫健康數據</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label>身高 (cm): <input type="text" name="height" value="<?php echo htmlspecialchars($user_health['height'] ?? ''); ?>"></label>
            <label>體重 (kg): <input type="text" name="weight" value="<?php echo htmlspecialchars($user_health['weight'] ?? ''); ?>"></label>
            <label>年齡: <input type="text" name="age" value="<?php echo htmlspecialchars($user_health['age'] ?? ''); ?>"></label>
            <label>心跳: <input type="text" name="heart_rate" value="<?php echo htmlspecialchars($user_health['heart_rate'] ?? ''); ?>"></label>
            <label>血壓: <input type="text" name="blood_pressure" value="<?php echo htmlspecialchars($user_health['blood_pressure'] ?? ''); ?>"></label>
            <label>血氧: <input type="text" name="blood_oxygen" value="<?php echo htmlspecialchars($user_health['blood_oxygen'] ?? ''); ?>"></label>
            <label>步數: <input type="text" name="steps" value="<?php echo htmlspecialchars($user_health['steps'] ?? ''); ?>"></label>
            <label>擅長運動: <input type="text" name="favorite_sport" value="<?php echo htmlspecialchars($user_health['favorite_sport'] ?? ''); ?>"></label>
            <button type="submit" name="submit_health">提交</button>
        </form>
    </section>
</body>
</html>

