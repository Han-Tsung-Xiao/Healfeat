<?php
// 啟用錯誤報告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 資料庫連接設定
$host = 'localhost';
$dbname = 'healfeatdb';
$username = 'root';
$password = 'Armvc26...73a'; // 使用環境變量

// 建立 MySQL 連接
$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("連接資料庫失敗: " . $mysqli->connect_error);
}

// 確認用戶已登入
session_start();
if (!isset($_SESSION['id'])) {
    echo "<script>alert('請先登入。');</script>";
    exit();
}

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    $user_id = $_SESSION['id'];

    // 日誌輸出
    error_log("GET event_id: " . $event_id);
    error_log("SESSION user_id: " . $user_id);

    // 獲取邀請的詳細信息
    $sql = "SELECT * FROM invited WHERE event_id = ? AND user_id = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die("預備語句失敗: " . $mysqli->error);
    }
    $stmt->bind_param('ii', $event_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $invite = $result->fetch_assoc();
    $stmt->close();

    if (!$invite) {
        echo "<script>alert('未找到邀請或您無權編輯此邀請。'); window.location.href='activity.php';</script>";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $sport_type = $_POST['sport_type'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $participant_limit = $_POST['participant_limit'];
    $current_participants = $_POST['current_participants'];
    $status = $_POST['status'];
    $user_id = $_SESSION['id'];

    // 日誌輸出
    error_log("POST event_id: " . $event_id);
    error_log("POST sport_type: " . $sport_type);
    error_log("POST location: " . $location);
    error_log("POST description: " . $description);
    error_log("POST participant_limit: " . $participant_limit);
    error_log("POST current_participants: " . $current_participants);
    error_log("POST status: " . $status);
    error_log("SESSION user_id: " . $user_id);

    // 更新邀請的詳細信息
    $sql = "UPDATE invited SET sport_type = ?, location = ?, description = ?, participant_limit = ?, current_participants = ?, status = ? WHERE event_id = ? AND user_id = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die("預備語句失敗: " . $mysqli->error);
    }
    $stmt->bind_param('sssiiisi', $sport_type, $location, $description, $participant_limit, $current_participants, $status, $event_id, $user_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('邀請已更新。'); window.location.href='activity.php';</script>";
        } else {
            echo "<script>alert('更新失敗，沒有找到相應的記錄。'); window.location.href='activity.php';</script>";
        }
    } else {
        echo "<script>alert('更新失敗: " . $stmt->error . "'); window.location.href='activity.php';</script>";
    }
    $stmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯邀請</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
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
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea, select {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            font-size: 1em;
            color: white;
            background-color: #2c3e50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>編輯邀請</h1>
        <form action="edit_invite.php" method="post">
            <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($invite['event_id']); ?>">
            <label for="sport_type">運動類型</label>
            <input type="text" id="sport_type" name="sport_type" value="<?php echo htmlspecialchars($invite['sport_type']); ?>" required>
            
            <label for="location">活動地點</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($invite['location']); ?>" required>
            
            <label for="description">活動描述</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($invite['description']); ?></textarea>
            
            <label for="participant_limit">人數限制</label>
            <input type="number" id="participant_limit" name="participant_limit" value="<?php echo htmlspecialchars($invite['participant_limit']); ?>" required>
            
            <label for="current_participants">目前參與人數</label>
            <input type="number" id="current_participants" name="current_participants" value="<?php echo htmlspecialchars($invite['current_participants']); ?>" required>
            
            <label for="status">狀態</label>
            <select id="status" name="status" required>
                <option value="open" <?php echo ($invite['status'] == 'open') ? 'selected' : ''; ?>>開放</option>
                <option value="closed" <?php echo ($invite['status'] == 'closed') ? 'selected' : ''; ?>>關閉</option>
            </select>
            
            <button type="submit">更新邀請</button>
        </form>
    </div>
</body>
</html>