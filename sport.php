<?php
// 資料庫連接設定
$host = 'localhost';
$dbname = 'healfeatdb';
$username = 'root';
$password = 'Armvc26...73a';

// 建立 MySQL 連接
$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("連接資料庫失敗: " . $mysqli->connect_error);
}

// 假設用戶已登入，並取得 user_id
$user_id = 1;

// 獲取用戶喜好運動
$sql = "SELECT favorite_sport FROM user_health WHERE user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_health = $result->fetch_assoc();
$favorite_sport = $user_health['favorite_sport'] ?? 'sport';
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>運動地圖</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-image: url("images/sport.jpg");
            background-size: cover;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            font-size: 1.2em;
        }
        header nav {
            display: flex;
            gap: 15px;
        }
        header nav a {
            color: white;
            text-decoration: none;
            background-color: #3498db;
            padding: 8px 12px;
            border-radius: 5px;
        }
        header nav a:hover {
            background-color: #2980b9;
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
        #map {
            height: 70vh;
            width: 100%;
        }
        .search-bar {
            text-align: center;
            margin: 20px 0;
        }
        .search-bar input {
            width: 60%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .search-bar button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            background-color: #2ecc71;
            color: white;
            cursor: pointer;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header>
        <a href="main.php" class="btn-back">← 返回主頁</a>
        <h1>搜尋附近的 <?php echo htmlspecialchars($favorite_sport); ?> 場地</h1>
        <nav>
            <a href="invite.php">發起邀請</a>
            <a href="activity.php">查詢目前邀請活動</a>
        </nav>
    </header>
    
    <div class="search-bar">
        <input type="text" id="location-input" placeholder="輸入地名或地址 (例如：台北101)">
        <button onclick="searchLocation()">搜尋</button>
    </div>
    
    <div id="map"></div>
    <div id="error" class="error-message"></div>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const favoriteSport = "<?php echo htmlspecialchars($favorite_sport); ?>";
        const map = L.map('map').setView([25.033964, 121.564468], 14);
        const errorDiv = document.getElementById('error');
        let marker;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    map.setView([lat, lon], 14);
                    marker = L.marker([lat, lon]).addTo(map)
                        .bindPopup("您目前的位置")
                        .openPopup();
                    searchNearbySports(lat, lon);
                },
                error => {
                    errorDiv.innerText = "定位失敗，請手動輸入地點。";
                }
            );
        }

        function searchLocation() {
            const location = document.getElementById('location-input').value;
            if (location) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            const lat = data[0].lat;
                            const lon = data[0].lon;
                            map.setView([lat, lon], 14);
                            if (marker) {
                                map.removeLayer(marker);
                            }
                            marker = L.marker([lat, lon]).addTo(map)
                                .bindPopup(location)
                                .openPopup();
                            searchNearbySports(lat, lon);
                        } else {
                            errorDiv.innerText = "找不到該地點，請重新輸入。";
                        }
                    })
                    .catch(() => {
                        errorDiv.innerText = "搜尋過程中發生錯誤，請稍後再試。";
                    });
            } else {
                errorDiv.innerText = "請輸入地名或地址。";
            }
        }

        function searchNearbySports(lat, lon) {
            const query = `
                [out:json];
                node
                  (around:2000,${lat},${lon})
                  [leisure=sports_centre];
                out body;
            `;
            
            fetch(`https://overpass-api.de/api/interpreter?data=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.elements && data.elements.length > 0) {
                        data.elements.forEach(element => {
                            L.marker([element.lat, element.lon]).addTo(map)
                                .bindPopup(element.tags.name || "運動場地")
                                .openPopup();
                        });
                    } else {
                        alert('在兩公里範圍內找不到相關運動場地。');
                    }
                })
                .catch(() => {
                    errorDiv.innerText = "查詢過程中發生錯誤，請稍後再試。";
                });
        }
    </script>
</body>
</html>