<?php
// 資料庫連接設定
$host = 'localhost';
$dbname = 'healfeatdb';
$username = 'root';
$password = 'Armvc26...73a'; // 使用環境變量

// 建立 MySQL 連接
$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    echo "<script>alert('連接資料庫失敗: " . $mysqli->connect_error . "');</script>";
    exit();
}

// 假設用戶已登入，並取得 user_id
session_start();
if (!isset($_SESSION['id'])) {
    echo "<script>alert('請先登入。');</script>";
    exit();
}
$user_id = $_SESSION['id'];

// 從資料庫中獲取用戶名
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$username = $user['username'];

// 檢查是否已有未完成的邀請
$check_sql = "SELECT COUNT(*) as count FROM invited WHERE user_id = ? AND status = 'pending'";
$check_stmt = $mysqli->prepare($check_sql);
$check_stmt->bind_param('i', $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$check_row = $check_result->fetch_assoc();
$has_pending_invite = $check_row['count'] > 0;

$check_stmt->close();

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($has_pending_invite) {
        echo "<script>alert('您已有未完成的邀請，請先完成或取消該邀請。');</script>";
    } else {
        $sport_type = $_POST['sport_type'];
        $participant_limit = $_POST['participant_limit'];
        $location = $_POST['location'];
        $description = $_POST['description'];
        $current_participants = isset($_POST['current_participants']) ? $_POST['current_participants'] : NULL;

        $sql = "INSERT INTO invited (user_id, username, sport_type, participant_limit, location, description, current_participants, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ississi', $user_id, $username, $sport_type, $participant_limit, $location, $description, $current_participants);

        if ($stmt->execute()) {
            echo "<script>alert('活動已成功發起！');</script>";
        } else {
            echo "<script>alert('發起活動失敗: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>發起邀請</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            background-image: url(images/invite.jpg);
            background-size: cover;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
        }
        header h1 {
            margin: 0;
            font-size: 1.2em;
        }
        header .btn-container {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }
        header .btn-back, header .btn-my-invites {
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            text-decoration: none;
            cursor: pointer;
        }
        header .btn-back:hover, header .btn-my-invites:hover {
            background-color: #c0392b;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 10px;
            font-weight: bold;
        }
        input, select, textarea {
            margin-bottom: 20px;
            padding: 10px;
            font-size: 16px;
        }
        input[readonly] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background-color: #2ecc71;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #27ae60;
        }
        .invite-status {
            background-color: #f39c12;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>發起邀請</h1>
        <div class="btn-container">
            <a href="my_invites.php" class="btn-my-invites">我的邀請</a>
            <a href="sport.php" class="btn-back">← 返回運動頁面</a>
        </div>
        <?php if ($has_pending_invite): ?>
            <span class="invite-status">您有未完成的邀請</span>
        <?php endif; ?>
    </header>
    <div class="container">
        <form method="POST" action="invite.php">
            <label for="username">用戶名</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>

            <label for="sport_type">運動類型</label>
            <input type="text" id="sport_type" name="sport_type" required>

            <label for="participant_limit">人數限制</label>
            <input type="number" id="participant_limit" name="participant_limit" required>

            <label for="location">活動地點</label>
            <input type="text" id="location" name="location" required>

            <label for="description">活動描述</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="current_participants">當前報名人數</label>
            <input type="number" id="current_participants" name="current_participants">

            <button type="submit">發起活動</button>
        </form>
    </div>
</body>
</html>
