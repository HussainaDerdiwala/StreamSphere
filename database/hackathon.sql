-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2025 at 10:49 AM
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
-- Database: `hackathon`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_user`
--

CREATE TABLE `auth_user` (
  `id` int(11) NOT NULL,
  `username` varchar(150) NOT NULL,
  `email` varchar(254) NOT NULL,
  `password` varchar(128) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `date_joined` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `user_type` enum('free','premium') NOT NULL DEFAULT 'free',
  `user` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_user`
--

INSERT INTO `auth_user` (`id`, `username`, `email`, `password`, `is_active`, `date_joined`, `last_login`, `profile_picture`, `user_type`, `user`) VALUES
(1, 'hinal', 'hinalsk2164@gmail.com', 'pbkdf2_sha256$260000$a893db7fa69a05961d733829$C7Zy2p9W3zqLVpjzp82VCRdiXWIf6FJL024IcKq+RF0=', 1, '2025-07-18 21:18:59', '2025-07-22 15:39:21', 'uploads/profile_pics/1752971973_12.jpg', 'premium', 'admin'),
(2, 'dushyant', 'carako8679@kissgy.com', 'pbkdf2_sha256$600000$1607f291d539e29385a1964f$ofoYnouDGRBK3+4kzO/2ejQ3LhW4YXrSikkcMiD/I/s=', 1, '2025-07-19 16:29:36', '2025-07-19 13:00:09', 'uploads/profile_pics/1752923093_Screenshot (183).png', 'free', 'user'),
(3, 'bhavin', 'divaca1768@luxpolar.com', 'pbkdf2_sha256$600000$aa41f622e15ef8fb12b35a2e$lxczaMXlcsedwPxepDBdWuWoSh1KWUXwRd/wCY8UlGc=', 1, '2025-07-19 16:38:12', NULL, NULL, 'free', 'user'),
(4, 'vaidik', 'posiroj694@kissgy.com', 'pbkdf2_sha256$600000$f2cc060186226d3fe6939e7b$f6YCPjERdSJio1ZDdXNqemEbeyuY9bV0qtWn8Pgi4Mg=', 1, '2025-07-19 16:40:40', NULL, 'uploads/profile_pics/1752926146_12.jpg', 'free', 'user'),
(5, 'sksandoa', 'dipeway230@kissgy.com', 'pbkdf2_sha256$600000$1d3ee7bf16b2199ed7e91392$m0+hxpTARjkU6poN3PoAadALYAdlLJ5lMS3GE+00878=', 1, '2025-07-20 00:10:23', NULL, NULL, 'free', 'user'),
(6, 'qwe', 'yobopi2987@luxpolar.com', 'pbkdf2_sha256$600000$0213137c0501c33950d58874$GXud0Zr8ZbTWV1GZhFmYzpFjyJNBM5NhiZcLhvjqpiM=', 1, '2025-07-20 00:36:35', NULL, NULL, 'free', 'user'),
(7, 'abc', 'yojegam580@luxpolar.com', 'pbkdf2_sha256$600000$2f475eae4ac3fc30e2673fd8$gmpKLHYENm8Cm1rtq1xDR5hpzGpouF2ws4KHmH29RwI=', 1, '2025-07-20 03:27:16', NULL, NULL, 'premium', 'user'),
(8, 'abc', 'wecana8681@luxpolar.com', 'pbkdf2_sha256$600000$f4a70b4ab21606b88c190a0b$rorQTv1PCUHvyr6nX+Ij04angtgxHKn4BOoJZBsymtE=', 1, '2025-07-20 03:31:24', NULL, NULL, 'premium', 'user'),
(9, 'abc', 'cedonow886@kissgy.com', 'pbkdf2_sha256$600000$20903d5e6a8adf19d4ef44a5$48vQ3k+IIP8yctjGipoetHcyYEKn2BU3cHdOAiuxaX4=', 1, '2025-07-20 03:57:24', '2025-07-20 05:40:22', 'uploads/profile_pics/1752965287_Screenshot (59).png', 'free', 'user'),
(10, 'hinal', 'kanac11709@luxpolar.com', 'pbkdf2_sha256$600000$52db5bed857b514e9e29a3d2$OwfqZvPu8Rw4Q1MKAZmc3TelUATtBicjzl7UhKXTIlc=', 1, '2025-07-20 06:25:50', NULL, NULL, 'free', ''),
(11, 'dsd', 'tasoca2551@luxpolar.com', 'pbkdf2_sha256$600000$90ce2eb67702cc5b1ca2510f$c3Nd6s40poiOgRtA0SysTDR73wO7AVQqiRPengK+V+0=', 1, '2025-07-20 10:00:07', '2025-07-20 06:59:30', 'uploads/profile_pics/1752985827_12.jpg', 'free', ''),
(12, 'ghgh', 'jaxaki3591@luxpolar.com', 'pbkdf2_sha256$600000$fcb61cfb27fcb5f088614569$Sq8/t+Ifb2VIlSg20sDUgQPNU59NV4KDTS9xfd8tcp8=', 1, '2025-07-20 11:03:54', NULL, 'uploads/profile_pics/1752989663_supawork-photo-20250705T112756007Z.png', 'free', ''),
(13, 'Hussaina', 'wisag32152@kissgy.com', 'pbkdf2_sha256$600000$6f2bc80bb909fef5867ab627$FfwLyxM84rwBkBVb8tflVQ1flHGc8OpGKNhjGVyUk3U=', 1, '2025-07-20 11:14:23', '2025-07-20 07:57:16', 'uploads/profile_pics/1752990309_headshotmaster_image_1751715365393.png', 'free', ''),
(14, 'hinal', 'pipipo3142@kissgy.com', 'pbkdf2_sha256$600000$26f674169ed7329faade9556$2ZL7l5aIfE9NPmstzXYLI8GEOjGjT6Abi8k2mJT7epU=', 1, '2025-07-22 19:12:06', NULL, NULL, 'free', '');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `video_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `video_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 24, 13, 'hello', '2025-07-20 05:49:19');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `video_id` int(11) DEFAULT NULL,
  `liked` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `video_id`, `liked`) VALUES
(7, 5, 17, 1),
(8, 6, 17, 1),
(14, 13, 24, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `video_id`, `user_id`, `reason`, `details`, `created_at`) VALUES
(2, 25, 13, 'Spam or misleading', 'hllo', '2025-07-20 11:27:38');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `subscriber_id` int(11) DEFAULT NULL,
  `channel_username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `subscriber_id`, `channel_username`) VALUES
(1, 1, 'hinal'),
(2, 5, 'hinal'),
(3, 9, 'abc'),
(4, 13, 'Hussaina');

-- --------------------------------------------------------

--
-- Table structure for table `video`
--

CREATE TABLE `video` (
  `id` int(11) NOT NULL,
  `video_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `uploaded_by` varchar(100) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(100) NOT NULL DEFAULT 'General',
  `privacy` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video`
--

INSERT INTO `video` (`id`, `video_name`, `file_path`, `thumbnail`, `uploaded_by`, `uploaded_at`, `category`, `privacy`) VALUES
(24, 'Animation', 'uploads/videos/687c82f1edbb9_2759477-uhd_3840_2160_30fps.mp4', 'uploads/thumbnails/687c82f1edbc0_12.jpg', 'Hussaina', '2025-07-20 05:47:29', 'Gaming', 0),
(25, 'animation2', 'uploads/videos/687c84533b483_6010502-uhd_2160_3840_25fps.mp4', 'uploads/thumbnails/687c84533b48c_12.jpg', 'Hussaina', '2025-07-20 05:53:23', 'Podcasts', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_user`
--
ALTER TABLE `auth_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`video_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_id` (`video_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_sub` (`subscriber_id`,`channel_username`);

--
-- Indexes for table `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_user`
--
ALTER TABLE `auth_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `video`
--
ALTER TABLE `video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `video` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
