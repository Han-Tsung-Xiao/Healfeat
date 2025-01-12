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

// 從資料庫中獲取所有用戶的邀請信息
$sql = "SELECT * FROM invited";
$result = $mysqli->query($sql);
$invites = $result->fetch_all(MYSQLI_ASSOC);

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>所有邀請</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-image: url("images/main.jpg");
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
        header .btn-back {
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            text-decoration: none;
            cursor: pointer;
        }
        header .btn-back:hover {
            background-color: #c0392b;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .no-invites {
            text-align: center;
            color: #888;
        }
        .btn-join {
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            cursor: pointer;
        }
        .btn-join:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <header>
        <h1>所有邀請</h1>
        <a href="sport.php" class="btn-back">← 返回運動頁面</a>
    </header>
    <div class="container">
        <?php if (count($invites) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>發起人</th>
                        <th>運動類型</th>
                        <th>活動地點</th>
                        <th>活動描述</th>
                        <th>人數限制</th>
                        <th>當前報名人數</th>
                        <th>狀態</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invites as $invite): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($invite['username']); ?></td>
                            <td><?php echo htmlspecialchars($invite['sport_type']); ?></td>
                            <td><?php echo htmlspecialchars($invite['location']); ?></td>
                            <td><?php echo htmlspecialchars($invite['description']); ?></td>
                            <td><?php echo htmlspecialchars($invite['participant_limit']); ?></td>
                            <td><?php echo htmlspecialchars($invite['current_participants']); ?></td>
                            <td><?php echo htmlspecialchars($invite['status']); ?></td>
                            <td>
                                <form action="join_activity.php" method="post" style="display:inline;">
                                    <input type="hidden" name="event_id" value="<?php echo $invite['event_id']; ?>">
                                    <button type="submit" class="btn-join" <?php echo ($invite['current_participants'] >= $invite['participant_limit']) ? 'disabled' : ''; ?>>
                                        加入活動
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-invites">目前沒有任何邀請。</p>
        <?php endif; ?>
    </div>
</body>
</html>