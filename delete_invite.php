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
    error_log("event_id: " . $event_id);
    error_log("user_id: " . $user_id);

    // 刪除活動參與者
    $sql = "DELETE FROM participant WHERE event_id = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die("預備語句失敗: " . $mysqli->error);
    }
    $stmt->bind_param('i', $event_id);
    if (!$stmt->execute()) {
        echo "<script>alert('刪除活動參與者失敗: " . $stmt->error . "'); window.location.href='my_invites.php';</script>";
        $stmt->close();
        $mysqli->close();
        exit();
    }
    $stmt->close();

    // 刪除邀請
    $sql = "DELETE FROM invited WHERE event_id = ? AND user_id = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die("預備語句失敗: " . $mysqli->error);
    }
    $stmt->bind_param('ii', $event_id, $user_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('邀請已刪除。'); window.location.href='my_invites.php';</script>";
        } else {
            echo "<script>alert('刪除失敗，沒有找到相應的記錄。'); window.location.href='my_invites.php';</script>";
        }
    } else {
        echo "<script>alert('刪除失敗: " . $stmt->error . "'); window.location.href='my_invites.php';</script>";
    }
    $stmt->close();
}

$mysqli->close();
?>