-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2024 at 03:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `missing_persons_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `timestamp`) VALUES
(1, 83, 'Approve Report', '{\"report_id\":\"39\",\"status\":\"approved\"}', '2024-08-04 12:09:57'),
(2, 83, 'Approve Report', '{\"report_id\":\"40\",\"status\":\"approved\"}', '2024-08-04 13:19:41'),
(3, 83, 'Reject Report', '{\"report_id\":\"44\",\"status\":\"rejected\"}', '2024-08-04 18:12:01'),
(4, 83, 'Approve Report', '{\"report_id\":\"45\",\"status\":\"approved\"}', '2024-08-04 18:12:24'),
(5, 83, 'Approve Report', '{\"report_id\":\"47\",\"status\":\"approved\"}', '2024-08-11 23:05:05'),
(6, 83, 'Approve Report', '{\"report_id\":\"49\",\"status\":\"approved\"}', '2024-08-11 23:16:29'),
(7, 83, 'Reject Report', '{\"report_id\":\"41\",\"status\":\"rejected\"}', '2024-08-12 11:46:05'),
(8, 83, 'Submit Report', '{\"report_id\":\"53\",\"status\":null}', '2024-08-13 08:08:34'),
(9, 93, 'Submit Report', '{\"report_id\":\"54\",\"status\":null}', '2024-08-13 15:43:39'),
(10, 83, 'Reject Report', '{\"report_id\":\"42\",\"status\":\"rejected\"}', '2024-08-21 06:45:54'),
(11, 83, 'Approve Report', '{\"report_id\":\"43\",\"status\":\"approved\"}', '2024-08-21 06:59:52'),
(12, 83, 'Submit Report', '{\"report_id\":\"55\",\"status\":null}', '2024-08-23 09:23:49'),
(13, 83, 'Submit Report', '{\"report_id\":\"56\",\"status\":null}', '2024-08-23 10:16:25'),
(14, 83, 'Submit Report', '{\"report_id\":\"57\",\"status\":null}', '2024-08-24 14:08:21'),
(15, 83, 'Submit Report', '{\"report_id\":\"58\",\"status\":null}', '2024-08-24 14:35:07'),
(16, 83, 'Submit Report', '{\"report_id\":\"59\",\"status\":null}', '2024-08-24 14:36:59'),
(17, 83, 'Approve Report', '{\"report_id\":\"58\",\"status\":\"approved\"}', '2024-08-24 14:56:54'),
(18, 83, 'Approve Report', '{\"report_id\":\"54\",\"status\":\"approved\"}', '2024-08-26 12:32:41'),
(19, 83, 'Approve Report', '{\"report_id\":\"50\",\"status\":\"approved\"}', '2024-08-27 17:28:48'),
(20, 31, 'Submit Report', '{\"report_id\":\"60\",\"status\":null}', '2024-08-29 06:55:47'),
(21, 106, 'Submit Report', '{\"report_id\":\"61\",\"status\":null}', '2024-09-24 09:41:03'),
(22, 83, 'Approve Report', '{\"report_id\":\"61\",\"status\":\"approved\"}', '2024-09-24 09:42:43'),
(23, 83, 'Submit Report', '{\"report_id\":\"62\",\"status\":null}', '2024-09-25 13:42:46'),
(24, 83, 'Approve Report', '{\"report_id\":\"62\",\"status\":\"approved\"}', '2024-09-25 13:43:20'),
(25, 31, 'Submit Report', '{\"report_id\":\"63\",\"status\":null}', '2024-09-26 10:15:52'),
(26, 93, 'Submit Report', '{\"report_id\":\"64\",\"status\":null}', '2024-09-27 18:57:10');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `type` enum('missing','found') NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `last_seen` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `contact_info` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_path` varchar(255) DEFAULT NULL,
  `location` varchar(255) NOT NULL DEFAULT 'unknown',
  `latitude` decimal(18,16) DEFAULT NULL,
  `longitude` decimal(19,16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `type`, `name`, `age`, `gender`, `last_seen`, `description`, `contact_info`, `user_id`, `status`, `created_at`, `image_path`, `location`, `latitude`, `longitude`) VALUES
(35, 'missing', 'alison Njobvu', 30, 'male', '2024-07-28', 'tall,light skin', '0970651693', 83, 'approved', '2024-08-01 13:39:37', '../uploads/996e5ca5-2278-485f-b792-1933622295bc_83_1722519577.0229.jpg', '', -13.1339000000000000, 27.8493000000000000),
(36, 'missing', 'mulenga mulenga', 20, 'male', '2024-08-04', 'yellow', '45453', 83, 'approved', '2024-08-04 09:12:16', '../uploads/74828cab-f962-409b-84fb-c65278ed432f_83_1722762736.1595.jpg', '', -13.1339000000000000, 27.8493000000000000),
(37, 'missing', 'daniele', 20, 'male', '2024-07-31', 'dark', '0970651693', 83, 'approved', '2024-08-04 10:06:40', '../uploads/ad358fd3-aa36-4443-94d6-c7b87cb5e4e4_83_1722766000.2587.jpg', '', -13.1339000000000000, 27.8493000000000000),
(38, 'missing', 'daniele', 20, 'male', '2024-08-01', 'tall', '0970651693', 83, 'approved', '2024-08-04 10:07:59', '../uploads/e655e809-fc49-4f97-9cad-ad117e44b474_83_1722766079.8605.jpg', '', -13.1339000000000000, 27.8493000000000000),
(39, 'missing', 'mary', 29, 'male', '2024-07-28', 'highlander', '0770343370', 83, 'approved', '2024-08-04 12:09:35', '../uploads/7ef823c3-e875-47d8-a931-d478bcff71af_83_1722773375.7215.jpg', '', -13.1339000000000000, 27.8493000000000000),
(40, 'found', 'alison Njobvu', 30, 'male', '2024-08-04', 'tall', '0970651693', 83, 'approved', '2024-08-04 13:18:32', '../uploads/ec482986-8e96-4334-822b-dfaa99c81757_83_1722777512.741.jpg', '', -13.1339000000000000, 27.8493000000000000),
(41, 'found', 'alison Njobvu', 30, 'male', '2024-08-04', 'tall', '0977777777', 83, 'rejected', '2024-08-04 13:24:17', NULL, '', NULL, NULL),
(42, 'found', 'alison Njobvu', 20, 'male', '2024-07-29', 'black', '0977777777', 83, 'rejected', '2024-08-04 13:28:06', '../uploads/81c751f5-32df-4762-b26e-418081cde0a6_83_1722778086.2846.jpg', '', -13.1339000000000000, NULL),
(43, 'found', 'alison Njobvu', 50, 'female', '2024-08-04', 'dark blue', '0970651693', 83, 'approved', '2024-08-04 13:29:04', '../uploads/2cf278cd-8f34-4be3-a0fa-aa0d32a7c7b9_83_1722778144.8417.jpg', '', -13.1339000000000000, 27.8493000000000000),
(44, 'missing', 'alison Njobvu', 16, 'male', '2024-08-04', 'tall', '0770343370', 83, 'rejected', '2024-08-04 13:45:27', '../uploads/70b2e67c-3b9f-42e9-abfc-ce82981f3b24_83_1722779127.5209.jpg', '', NULL, NULL),
(45, 'missing', 'alison Njobvu', 50, 'female', '2024-07-30', 'tall', '0770343370', 83, 'approved', '2024-08-04 13:47:34', '../uploads/29c75462-2df5-4f1c-9545-c0e787224481_83_1722779254.5482.jpg', '', -13.1339000000000000, 27.8493000000000000),
(46, 'found', 'alison Njobvu', 30, 'male', '2024-07-29', 'tall', '0770343370', 83, 'pending', '2024-08-04 14:01:57', '../uploads/c40b5bc5-2c0b-430b-9b02-78a5c4a18329_83_1722780117.111.jpg', '', NULL, NULL),
(47, 'missing', 'astrida mbalushi', 20, 'female', '2024-07-31', 'short,smiley face', '0770343370', 83, 'approved', '2024-08-11 23:04:22', '../uploads/35a14a76-b17a-4655-b85f-b6a2193fec8f_27_1721961792.9174.jpg', 'kamwala', -13.1339000000000000, 27.8493000000000000),
(48, 'found', 'astrida mbalushi', 20, 'female', '2024-08-07', 'short and happy', '0770343370', 83, 'pending', '2024-08-11 23:10:07', NULL, 'kamwala', NULL, NULL),
(49, 'missing', 'astrida mbalushi', 25, 'female', '2024-07-31', 'happy face', '0770343370', 83, 'approved', '2024-08-11 23:13:57', '../uploads/profilePictures/73044f1e-cb69-4a09-bf88-48df8e77b0c9__1723418037.0369.png', 'garden', -13.1339000000000000, 27.8493000000000000),
(50, 'missing', 'mary phiri', 35, 'male', '2024-07-01', 'black na white', '09744463525', 83, 'approved', '2024-08-13 07:43:08', '../uploads/reports/656468fa-e773-4ed0-8a19-6f75fc2db497__1723534988.2659.jpeg', 'matero', -13.1339000000000000, 27.8493000000000000),
(51, 'missing', 'mary phiri', 35, 'male', '2024-07-01', 'black na white', '09744463525', 83, 'pending', '2024-08-13 08:05:03', '../uploads/reports/af0ce28a-daba-4eec-981a-f2aaea2c944a_dannychanda05@gmail.com_1723536303.3924.jpeg', 'matero', NULL, NULL),
(52, 'missing', 'mary phiri', 35, 'male', '2024-07-01', 'black na white', '09744463525', 83, 'pending', '2024-08-13 08:05:45', '../uploads/reports/5932c127-8fef-423e-b13f-ccca9ba3aacd_dannychanda05@gmail.com_1723536345.5267.jpeg', 'matero', NULL, NULL),
(53, 'missing', 'mary phiri', 35, 'male', '2024-07-01', 'black na white', '09744463525', 83, 'pending', '2024-08-13 08:08:34', '../uploads/reports/2b238b68-f2dc-4735-ba43-b714e7b41ab8_dannychanda05@gmail.com_1723536514.2149.jpeg', 'matero', NULL, NULL),
(54, 'missing', 'Given', 23, 'male', '2024-06-20', 'Very stupid boy who does not give the correct results to his students', '0974886363', 93, 'approved', '2024-08-13 15:43:39', '../uploads/reports/89decf18-5999-4456-858d-29dcec06720f_Samuelmwango100@gmail.com_1723563819.5355.jpg', 'Evelyn Hone', -13.1339000000000000, 27.8493000000000000),
(55, 'missing', 'mulenga', 9, 'male', '2024-08-28', 'tall and dark', '0977777777', 83, 'pending', '2024-08-23 09:23:49', '../uploads/reports/1f3e9858-a90c-4f4b-a9b8-4ebc2a416b18_dannychanda05@gmail.com_1724405029.1212.jpeg', 'ndola', -12.9705198198087180, 28.6497570007324140),
(56, 'missing', 'frank dux', 40, 'male', '2024-07-30', 'kkkkkk', '45453', 83, 'pending', '2024-08-23 10:16:25', '../uploads/reports/5f40c4fd-c0fb-4e72-9b39-fe9a3227709f_dannychanda05@gmail.com_1724408185.862.jpg', 'samfya', -11.3517239407191100, 29.5576740234374920),
(57, 'missing', 'Hallison', 25, 'male', '1999-01-25', 'tall', '09744463525', 83, 'pending', '2024-08-24 14:08:21', '../uploads/reports/52b2a2e0-7b5c-49b2-a454-3b6d51e22da0_dannychanda05@gmail.com_1724508500.9994.jpg', 'lusaka', 0.0000000000000000, 0.0000000000000000),
(58, 'missing', 'Chris brown', 30, 'male', '1994-02-26', 'dancer', '09744463525', 83, 'approved', '2024-08-24 14:35:07', '../uploads/reports/9f0dd356-dd64-4433-8725-1158aee9afe8_dannychanda05@gmail.com_1724510107.1919.jpg', 'lusaka', -15.4222459000000000, 28.2905366000000000),
(59, 'missing', 'Chris brown', 9, 'female', '2024-08-16', 'dancer', '09744463525', 83, 'pending', '2024-08-24 14:36:59', '../uploads/reports/957ba96b-f782-4010-9ec9-c097c16931fd_dannychanda05@gmail.com_1724510219.4545.jpg', 'lusaka', -15.4177275000000000, 28.2901397000000000),
(60, 'missing', 'Danny', 25, 'male', '2023-12-23', 'dark ,tall,programmer', '09744463525', 31, 'pending', '2024-08-29 06:55:47', '../uploads/reports/e1eebe1e-c82b-42bc-8bc9-b74c6249bbe7_ruth@gmail.com_1724914547.2647.jpg', 'Evelyn Hone', -15.4206208000000000, 28.3115520000000000),
(61, 'missing', 'mulenga', 9, 'male', '2024-09-24', 'wfsfsdfz', '0977777777', 106, 'approved', '2024-09-24 09:41:03', '../uploads/reports/df5bfb8c-03dc-46ff-aa66-a853f37317fa_ruthtembo2580@gmail.com_1727170863.7423.jpg', 'lusaka', -15.4206208000000000, 28.3115520000000000),
(62, 'missing', 'ackim nkhowani', 20, 'male', '2024-09-03', 'hhhhh', '09744463525', 83, 'approved', '2024-09-25 13:42:46', '../uploads/reports/2a9cb15e-44b9-4e39-8ec1-05b68841d80c_dannychanda05@gmail.com_1727271766.3069.jpg', 'kamwala', -15.4206208000000000, 28.3115520000000000),
(63, 'missing', 'kalaba', 9, 'male', '2024-09-03', 'elA', '0974886363', 31, 'pending', '2024-09-26 10:15:52', '../uploads/reports/f7eb444e-f9be-4361-a7d4-1b9c646ba2b6_ruth@gmail.com_1727345752.5841.jpg', 'Evelyn Hone', 0.0000000000000000, 0.0000000000000000),
(64, 'missing', 'emma', 40, 'male', '2024-09-04', 'kya', '09744463525', 93, 'pending', '2024-09-27 18:57:10', '../uploads/reports/e1de6ac1-b5cb-45d0-9641-238760f28756_Samuelmwango100@gmail.com_1727463430.655.jpg', 'Evelyn Hone', 0.0000000000000000, 0.0000000000000000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','allowed_user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(500) NOT NULL,
  `code` int(5) DEFAULT NULL,
  `status` enum('not verified','verified','suspended') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `image`, `code`, `status`) VALUES
(31, 'Ruth tembo', 'ruth@gmail.com', '$2y$10$8zsm.k9ZZRmahO9vJ.B5ju2c4WuwomOOOgxJ8ufvz4q9zX7l3.KSO', 'allowed_user', '2024-07-25 23:31:11', '../uploads/profilePictures/default.jpg', 33042, 'verified'),
(83, 'daniel chanda', 'dannychanda05@gmail.com', '$2y$10$cblBgh5BK3azUUk5aN9WWe4D3btvsWvLU7x/B3uCxLr.YoTLEyr.a', 'admin', '2024-07-31 23:50:07', '../uploads/profilePictures/69d9c2aa-df0a-4f26-88cf-5d32027ce6d5_dannychanda05@gmail.com_1722469807.1866.jpg', 39775, 'verified'),
(88, 'Frank', 'franksichone81@gmail.com', '$2y$10$/EbhldT3XKsfX0BCYlgbSeg/GIaH1lR58hyYGS899xQ9L8BAupZaG', 'allowed_user', '2024-08-08 14:29:50', '../uploads/profilePictures/default.jpg', 20637, 'verified'),
(90, 'margaret', 'magaret@gmail.com', '$2y$10$KTDVclKPxEnUgcynJvaKvOZfAHHKyHMXoRBY8LyGgQ/lvubO9R4sG', 'allowed_user', '2024-08-11 21:54:42', '../uploads/profilePictures/default.jpg', 72337, 'not verified'),
(91, 'tanise zulu', 'tanisezulu@gmail.com', '$2y$10$d8xpL5S21qtWWgXApIlPlOh5QvN7A7c0FSxOmBIAwZJ2kJLdpxHJy', 'allowed_user', '2024-08-11 21:57:54', '../uploads/profilePictures/default.jpg', 30726, 'verified'),
(92, 'chisha kaunda', 'chishakaunda@gmail.com', '$2y$10$sPjQY91g6jldCfnkGVFzeePryBuBkkJJ3d9RJjp7FMxnx.QT11.IK', 'allowed_user', '2024-08-11 22:43:17', '../uploads/profilePictures/default.jpg', 52864, 'not verified'),
(93, 'Samuelmwango100', 'Samuelmwango100@gmail.com', '$2y$10$4pz7we4L1AHJ1di6HQCyLO6sHKeWDObowCe.NYG/vr.GaG0./CVd6', 'allowed_user', '2024-08-13 15:38:26', '../uploads/profilePictures/bed872d0-2382-4d08-a7aa-602879441b74_Samuelmwango100@gmail.com_1723563505.911.jpg', 79216, 'verified'),
(94, 'nicholas lungu', 'nicholasjaylungu13@gmail.com', '$2y$10$HUbB3u00COxlIKYUGUGAReVfCEFw9zPtkoR9dquXgwJVbmO0VOo0y', 'allowed_user', '2024-09-04 06:36:19', '../uploads/profilePictures/default.jpg', 45148, 'not verified'),
(95, 'nicholas lungu', 'nicholasjaylungu@gmail.com', '$2y$10$o9ReDfCkpb7QsWGh86hS7uUqiywB0PaOsDCXZT1DXN8q2oZ6JHX2S', 'allowed_user', '2024-09-04 06:37:45', '../uploads/profilePictures/default.jpg', 15665, 'verified'),
(100, 'deborah', 'astridahmbalushi4@gmail.com', '$2y$10$YbFyv5G7Le89IBOMp69a7OC8u2QX4Rl6KDF6nBrfhPq8WVEwU.3V2', 'allowed_user', '2024-09-13 10:13:19', '../uploads/profilePictures/default.jpg', 89022, 'verified'),
(101, 'John', 'johnkalaba0052@gmail.com', '$2y$10$qtmqn5ichsu8K6bQPLFbb.kdkpT.aFo/0OO4gNMXOk3syPV30iPpa', 'allowed_user', '2024-09-15 16:45:18', '../uploads/profilePictures/33880d10-7431-46d9-ab6d-c22950b6b353_johnkalaba0052@gmail.com_1726418718.1264.jpeg', 32700, 'verified'),
(104, 'clinton', 'clintonchinz97@gmail.com', '$2y$10$21zDraZ.saTc9ZlJ/VYMtOyL8qTkZi/3Nbv4ayL4dDZZzMmtZA.W.', 'allowed_user', '2024-09-22 13:46:51', '../uploads/profilePictures/default.jpg', 81570, 'verified'),
(106, 'ruth', 'ruthtembo2580@gmail.com', '$2y$10$HzroananBrIb6FBuB5TQkOJeAIHHXrhXFx.JrJAG44v2zVR8a/F0W', 'allowed_user', '2024-09-24 09:32:57', '../uploads/profilePictures/default.jpg', 73780, 'verified'),
(107, 'kalinda', 'janethamungu@gmail.com', '$2y$10$4daxhI4C4ZWwkcdyW/1mT.FKehRQn/EoDLTRbUKMtzBQLceuoQogu', 'allowed_user', '2024-09-27 20:36:23', '../uploads/profilePictures/default.jpg', 31778, 'verified');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
