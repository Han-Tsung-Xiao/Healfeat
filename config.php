<?php
$servername = "localhost";
$username = "root";
$password = "Armvc26...73a"; // 資料庫密碼
$dbname = "healfeatdb"; // 資料庫名稱

try {
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
    echo "";
} catch (PDOException $e) {
    // 顯示更詳細的錯誤訊息，包含錯誤代碼和訊息
    die("資料庫連接失敗: " . $e->getMessage() . " (錯誤代碼: " . $e->getCode() . ")");
}
?>
