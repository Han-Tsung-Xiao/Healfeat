-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost
-- 產生時間： 2025-01-12 16:19:44
-- 伺服器版本： 8.0.40
-- PHP 版本： 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `healfeatdb`
--

-- --------------------------------------------------------

--
-- 資料表結構 `invited`
--

CREATE TABLE `invited` (
  `event_id` int NOT NULL,
  `user_id` int NOT NULL,
  `sport_type` varchar(50) NOT NULL,
  `participant_limit` int NOT NULL,
  `location` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `description` text,
  `username` varchar(50) NOT NULL,
  `current_participants` int DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 傾印資料表的資料 `invited`
--

INSERT INTO `invited` (`event_id`, `user_id`, `sport_type`, `participant_limit`, `location`, `created_at`, `description`, `username`, `current_participants`, `status`) VALUES
(14, 2, '籃球', 10, '元智大學籃球場', '2024-12-26 04:25:47', '五對五', 'user1', 3, '0');

-- --------------------------------------------------------

--
-- 資料表結構 `participant`
--

CREATE TABLE `participant` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `cellphone` varchar(20) DEFAULT NULL,
  `event_id` int DEFAULT NULL,
  `userid` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 傾印資料表的資料 `participant`
--

INSERT INTO `participant` (`id`, `username`, `cellphone`, `event_id`, `userid`) VALUES
(2, 'user2', '0922222222', 14, 3);

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `cellphone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`id`, `username`, `cellphone`, `email`, `password`, `created_at`) VALUES
(2, 'user1', '0911111111', 'user1@gmail.com', '0ffe1abd1a08215353c233d6e009613e95eec4253832a761af28ff37ac5a150c', '2024-12-26 03:55:46'),
(3, 'user2', '0922222222', 'user2@gmail.com', 'edee29f882543b956620b26d0ee0e7e950399b1c4222f5de05e06425b4c995e9', '2024-12-26 04:32:19');

-- --------------------------------------------------------

--
-- 資料表結構 `user_health`
--

CREATE TABLE `user_health` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `height` decimal(5,2) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `age` int NOT NULL,
  `heart_rate` int NOT NULL,
  `blood_pressure` varchar(20) NOT NULL,
  `blood_oxygen` decimal(5,2) NOT NULL,
  `steps` int NOT NULL,
  `favorite_sport` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 傾印資料表的資料 `user_health`
--

INSERT INTO `user_health` (`id`, `user_id`, `height`, `weight`, `age`, `heart_rate`, `blood_pressure`, `blood_oxygen`, `steps`, `favorite_sport`) VALUES
(3, 2, 178.00, 55.00, 16, 89, '100', 98.00, 5345, '籃球'),
(4, 3, 178.00, 55.00, 16, 89, '100', 98.00, 5345, '籃球');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `invited`
--
ALTER TABLE `invited`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `participant`
--
ALTER TABLE `participant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `userid` (`userid`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 資料表索引 `user_health`
--
ALTER TABLE `user_health`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `invited`
--
ALTER TABLE `invited`
  MODIFY `event_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `participant`
--
ALTER TABLE `participant`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_health`
--
ALTER TABLE `user_health`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `invited`
--
ALTER TABLE `invited`
  ADD CONSTRAINT `invited_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- 資料表的限制式 `participant`
--
ALTER TABLE `participant`
  ADD CONSTRAINT `participant_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `invited` (`event_id`),
  ADD CONSTRAINT `participant_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`id`);

--
-- 資料表的限制式 `user_health`
--
ALTER TABLE `user_health`
  ADD CONSTRAINT `user_health_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
