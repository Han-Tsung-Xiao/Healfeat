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

// 從資料庫中獲取用戶的邀請信息
$sql = "SELECT * FROM invited WHERE user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$invites = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 函數：獲取活動參與者
function getParticipants($mysqli, $event_id) {
    $sql = "SELECT username, cellphone FROM participant WHERE event_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $participants = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $participants;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的邀請</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
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
        .participants {
            margin-top: 20px;
        }
        .participants h2 {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .participants ul {
            list-style-type: none;
            padding: 0;
        }
        .participants li {
            background-color: #f9f9f9;
            margin-bottom: 5px;
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <header>
        <h1>我的邀請</h1>
        <a href="invite.php" class="btn-back">← 返回發起邀請</a>
    </header>
    <div class="container">
        <?php if (count($invites) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>運動類型</th>
                        <th>人數限制</th>
                        <th>活動地點</th>
                        <th>活動描述</th>
                        <th>當前報名人數</th>
                        <th>狀態</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invites as $invite): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($invite['sport_type']); ?></td>
                            <td><?php echo htmlspecialchars($invite['participant_limit']); ?></td>
                            <td><?php echo htmlspecialchars($invite['location']); ?></td>
                            <td><?php echo htmlspecialchars($invite['description']); ?></td>
                            <td><?php echo htmlspecialchars($invite['current_participants']); ?></td>
                            <td><?php echo htmlspecialchars($invite['status']); ?></td>
                            <td>
                                <form action="edit_invite.php" method="get" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $invite['event_id']; ?>">
                                    <button type="submit" class="btn-edit">編輯</button>
                                </form>
                                <form action="delete_invite.php" method="get" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $invite['event_id']; ?>">
                                    <button type="submit" class="btn-delete" onclick="return confirm('確定要刪除這個邀請嗎？')">刪除</button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7">
                                <div class="participants">
                                    <h2>活動參與者</h2>
                                    <ul>
                                        <?php
                                        $participants = getParticipants($mysqli, $invite['event_id']);
                                        if (count($participants) > 0):
                                            foreach ($participants as $participant):
                                        ?>
                                                <li><?php echo htmlspecialchars($participant['username']) . " - " . htmlspecialchars($participant['cellphone']); ?></li>
                                        <?php
                                            endforeach;
                                        else:
                                        ?>
                                            <li>目前沒有參與者。</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-invites">您尚未發起任何邀請。</p>
        <?php endif; ?>
    </div>
    <?php $mysqli->close(); ?>
</body>
</html>