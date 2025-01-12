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

if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['id'];

    // 檢查用戶是否為活動發起者
    $sql = "SELECT user_id FROM invited WHERE event_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $stmt->bind_result($inviter_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id == $inviter_id) {
        echo "<script>alert('您不能參加自己發起的活動。'); window.location.href='activity.php';</script>";
        exit();
    }

    // 檢查用戶是否已參加該活動
    $sql = "SELECT COUNT(*) FROM participant WHERE event_id = ? AND userid = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ii', $event_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "<script>alert('您已經參加了這個活動。'); window.location.href='activity.php';</script>";
        exit();
    }

    // 獲取用戶資訊
    $sql = "SELECT username, cellphone FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $cellphone);
    $stmt->fetch();
    $stmt->close();

    // 獲取當前參與人數和人數限制
    $sql = "SELECT current_participants, participant_limit FROM invited WHERE event_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $stmt->bind_result($current_participants, $participant_limit);
    $stmt->fetch();
    $stmt->close();

    // 檢查是否可以加入活動
    if ($current_participants < $participant_limit) {
        // 更新參與人數
        $sql = "UPDATE invited SET current_participants = current_participants + 1 WHERE event_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $event_id);
        if ($stmt->execute()) {
            // 將用戶資訊加入 participant 資料表
            $sql = "INSERT INTO participant (userid, username, cellphone, event_id) VALUES (?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('issi', $user_id, $username, $cellphone, $event_id);
            if ($stmt->execute()) {
                echo "<script>alert('已成功加入活動。'); window.location.href='activity.php';</script>";
            } else {
                echo "<script>alert('加入活動失敗。'); window.location.href='activity.php';</script>";
            }
        } else {
            echo "<script>alert('更新參與人數失敗。'); window.location.href='activity.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('活動已滿。'); window.location.href='activity.php';</script>";
    }
}

$mysqli->close();
?>