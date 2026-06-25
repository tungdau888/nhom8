-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2026 at 02:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nhaccuatui_clone`
--

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`id`, `title`, `category_id`, `image_url`) VALUES
(2, 'NHẠC CHILL TIKTOK', 6, 'uploads/albums/1781164800_1753758572742_300.webp'),
(3, 'NHẠC TRUNG HAY NHẤT', 6, 'uploads/albums/1781164894_1771507496573_300.webp'),
(4, 'TỪ TIK TOK QUA', 6, 'uploads/albums/1781164927_1759313741436_300.webp'),
(5, 'Nhạc HỒNG CÔNG', 6, 'uploads/albums/1781164974_1752822537253_300.webp'),
(6, 'NHẠC DOUYIN', 6, 'uploads/albums/1781165014_1739261289750_300.webp');

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `artists`
--

INSERT INTO `artists` (`id`, `name`, `image_url`) VALUES
(2, 'sơn tùng mtp', 'uploads/artists/1781017744_son-tung-mtp-17182382517241228747767.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `image_url`) VALUES
(1, 'banne1', 'uploads/banners/1781014234_1780050951021_1500.webp'),
(3, '3', 'uploads/banners/1781014405_1781002590099_1500.webp');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image_url`, `created_at`) VALUES
(5, 'Lofi', 'uploads/categories/1781164591_genre_14_600.png', '2026-06-11 06:17:07'),
(6, 'NHẠC TRUNG', 'uploads/categories/1781164566_CPop_600.png', '2026-06-11 06:34:36'),
(8, 'TIK TOK', 'uploads/categories/1781165220_scene_2_600.png', '2026-06-11 08:07:00'),
(9, 'BUỒN', 'uploads/categories/1781165246_mood_3_600.png', '2026-06-11 08:07:26'),
(10, 'BOLERO', 'uploads/categories/1781165266_genre_23_600.png', '2026-06-11 08:07:46'),
(11, 'POP', 'uploads/categories/1781165286_genre_101_600.png', '2026-06-11 08:08:06'),
(12, 'NHẠC HÀN', 'uploads/categories/1781165307_kpop_600.png', '2026-06-11 08:08:27'),
(13, 'REMIX', 'uploads/categories/1781165346_genre_39_600.png', '2026-06-11 08:09:06'),
(14, 'anime', 'uploads/categories/1781165419_genre_16_600.png', '2026-06-11 08:10:19'),
(15, 'RAP VIỆT', 'uploads/categories/1781165457_vrap_600.png', '2026-06-11 08:10:57');

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_songs`
--

CREATE TABLE `playlist_songs` (
  `playlist_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_config`
--

CREATE TABLE `site_config` (
  `cfg_key` varchar(100) NOT NULL,
  `cfg_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_config`
--

INSERT INTO `site_config` (`cfg_key`, `cfg_value`) VALUES
('bxh_col1_cat', ''),
('bxh_col1_songs', '14'),
('bxh_col1_title', 'Top 50 Bài Hát Thịnh Hành'),
('bxh_col2_cat', ''),
('bxh_col2_songs', '10'),
('bxh_col2_title', 'Top 50 Nhạc Việt'),
('bxh_col3_cat', ''),
('bxh_col3_songs', '11'),
('bxh_col3_title', 'Top 50 Nhạc Hot'),
('homepage_sections_json', '[{\"title\":\"NHẠC HAY \",\"type\":\"scroll\",\"album_ids\":\"6\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `artist` varchar(100) NOT NULL,
  `audio_url` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `duration` int(11) DEFAULT NULL COMMENT 'Thời lượng bài hát (tính bằng giây)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `album_id` int(11) DEFAULT NULL,
  `lyrics` text DEFAULT NULL,
  `composer` varchar(255) DEFAULT NULL,
  `mood` varchar(50) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'approved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`id`, `title`, `artist`, `audio_url`, `image_url`, `category_id`, `views`, `duration`, `created_at`, `album_id`, `lyrics`, `composer`, `mood`, `status`) VALUES
(10, '大风在刮大雪在下 (Bản Hợp Xướng)  Lục Tiểu Lạc', 'Lục Tiểu Lạc', 'uploads/audio/1781164441_1781164014_1781163994_1780990176_大风在刮大雪在下 (Bản Hợp Xướng).mp3', 'uploads/images/1781164406_1781160788_1768260873350_300.jpg', 6, 4, NULL, '2026-06-11 07:53:26', NULL, '别在这个冬把我给丢下\r\n熬过了风雪熬过了冬天\r\n偏偏我熬不过这场思念\r\n熬过了黑夜熬过了失眠\r\n可我熬不过没你在身边\r\n熬过了风雪熬过了冬天\r\n偏偏我熬不过这场思念\r\n熬过了黑夜熬过了失眠\r\n可我熬不过没你在身边\r\n大风在刮大雪在下\r\n我好想念他\r\n片片的回忆是我的伤疤\r\n等来雪花等来泪花\r\n我没等到他\r\n是谁替代我和他有了家\r\n大风在刮大雪在下\r\n吹散我和他\r\n偏偏承诺啊太快就融化\r\n想问酒杯想问雪花\r\n我的他在哪\r\n别在这个冬把我给丢下\r\n熬过了风雪熬过了冬天\r\n偏偏我熬不过这场思念\r\n熬过了黑夜熬过了失眠\r\n可我熬不过没你在身边\r\n是谁替代我和他有了家\r\n大风在刮大雪在下\r\n吹散我和他\r\n偏偏承诺啊太快就融化\r\n想问酒杯想问雪花\r\n我的他在哪\r\n别在这个冬把我给丢下\r\n熬过了风雪熬过了冬天\r\n偏偏我熬不过这场思念\r\n熬过了黑夜熬过了失眠\r\n可我熬不过没你在身边\r\n熬过了风雪熬过了冬天\r\n偏偏我熬不过这场思念\r\n熬过了黑夜熬过了失眠\r\n可我熬不过没你在身边\r\n熬过了风雪熬过了冬天\r\n偏偏我熬不过这场思念\r\n熬过了黑夜熬过了失眠\r\n可我熬不过没你在身边', 'Lục Tiểu Lạ', 'buon', 'approved'),
(11, 'Em (feat. SOOBIN)', 'SOOBIN', 'uploads/audio/1781164743_Em__feat._SOOBIN_.mp3', 'uploads/images/1781164743_1779370796566_300.jpg', 5, 0, NULL, '2026-06-11 07:59:03', NULL, 'Toàn lý do đâu đâu\r\nNhư con nít đôi co nhau\r\nTa cố thêm được bao lâu\r\nTa chỉ đang cố để mai này đau hơn\r\nAnh hiểu em đến mức phải dừng\r\nEm hiểu anh đến mức phải đi\r\nVậy là mất nhau rồi em ơi\r\nGió bay và hoa rơi\r\nMột lần cuốn nhau rồi xa thôi\r\nChẳng thể nào giữ nhau giữa trời\r\nAnh sẽ phiêu lãng nơi xa vời\r\nEm sẽ theo con nước lạ cuốn trôi\r\nVậy em đến để nói anh biết\r\nTình yêu là thứ đẹp nhất\r\nHay em chỉ đến để cho anh biết\r\nLàm sao để có thể sống sót\r\nTrước mất mát vậy thôi\r\nAnh có thể cho em hết đời mình\r\nNhưng em làm cho anh ghét niềm tin\r\nBên em bao lâu\r\nRồi một ngày bình thường\r\nEm chợt mất đi cảm giác\r\nTừng yêu nhau\r\nEm là tất cả trong anh\r\nVà ngày không em\r\nLà từ xưa nay anh không thích\r\nKhi nghĩ đến\r\nBên anh đêm thâu\r\nGiờ từng dòng kỷ niệm như làn khói\r\nTheo từng phút phai nhạt màu\r\nEm nhớ gì không em\r\nMình gật đầu cho nhau\r\nĐể khi ta xa\r\nTa chỉ có một lần xa nhau\r\nEm\r\nGiờ anh muốn biết trong em ta là gì\r\nTháng năm qua là gì\r\nMà chẳng thể hơn một thứ\r\nKhi nào vui thì giữ\r\nKhi nào không còn vui\r\nThì dễ dàng vứt\r\nChẳng thể tìm được một lí do gì\r\nMà chỉ vì một lần giông tố như này\r\nChẳng thể nào làm ta mất đi sạch\r\nMình đã từng vượt qua\r\nNhững điều khó hơn vậy\r\nAnh giờ ôm ngàn thương nhớ\r\nEm giờ vui em giờ vui\r\nBaby thôi đừng nói cho anh\r\nĐừng kể cho anh\r\nĐâu ai mong nghe những thứ đấy\r\nKhi anh đang phải cố gắng\r\nTìm điều gì để đối phó với thời gian\r\nAnh giờ sao\r\nThật sự em cần biết không em\r\nHay chỉ vờ quan tâm\r\nĐừng hỏi thăm anh với cách ấy\r\nĐừng bỏ đi nhưng cứ vẫn đây\r\nTội nghiệp gì anh cũng đã vụn vỡ\r\nBên em bao lâu\r\nRồi một ngày bình thường\r\nEm chợt mất đi cảm giác\r\nTừng yêu nhau\r\nTa nào có gặp thêm ai\r\nChẳng ai sai\r\nVậy em đi vì đâu\r\nSao anh không được biết\r\nQuên em quên sao\r\nTại nhà thờ bạn bè ai cũng muốn\r\nTa hạnh phúc đến bạc đầu\r\nNay đứng nhìn em đi\r\nĐừng bắt anh tìm\r\nCòn điều gì trong con tim anh\r\nCũng đã vỡ nát\r\nEm em em\r\nEm nhớ gì em nhớ gì\r\nBên em bao lâu\r\nRồi một ngày bình thường\r\nEm chợt mất đi cảm giác\r\nTừng yêu nhau\r\nTa nào có gặp thêm ai\r\nChẳng ai sai\r\nVậy em đi vì đâu\r\nSao anh không được biết\r\nQuên em quên sao\r\nTại nhà thờ bạn bè ai cũng muốn\r\nTa hạnh phúc đến bạc đầu\r\nNay đứng nhìn em đi\r\nĐừng bắt anh tìm\r\nTừng điều anh trao lâu nay\r\nĐâu chắc em cần đến', 'SOOBIN', 'thu_gian', 'approved'),
(12, '爱 (I Love You 3000 Chinese Version)', 'Jackson Wang (Vương Gia Nhĩ)', 'uploads/audio/1781165147______I_Love_You_3000_Chinese_Version_.mp3', 'uploads/images/1781165147_1572075947134_300.jpg', 6, 0, NULL, '2026-06-11 08:05:47', 2, 'Baby, take my hand\r\nI just want to be your husband\r\n\'Cause I\'m your Iron Man\r\nAnd I love you 3000\r\nBaby, take a chance\r\n\'Cause I want this to be something\r\nStraight out of a Hollywood movie\r\n看见你的双眼\r\n有一次的见面\r\n心里有话想说\r\n但又怕我说错\r\n想尽办法\r\n但是气氛尴尬\r\n如果今天不说就没机会\r\n从第一次见你就该说\r\n说哪天你会穿上婚纱\r\n走遍世界,牵着你手\r\n看见月亮带着你走\r\n对你说\r\nBaby, take my hand\r\nI just want to be your husband\r\n\'Cause I\'m your Iron Man\r\nAnd I love you 3000\r\nBaby, take a chance\r\n\'Cause I want this to be something\r\nStraight out of a Hollywood movie\r\n拿你的照片\r\n从没看多几遍\r\n窗外一片黑的天\r\n时间很有限 and\r\n希望天亮之前\r\n把我的心愿\r\n不埋在心里,全都告诉你\r\n从第一次见你就该说\r\n说哪天你会穿上婚纱\r\n走遍世界,牵着你手\r\n看见月亮带着你走\r\n对你说\r\nBaby, take my hand\r\nI just want to be your husband\r\n\'Cause I\'m your Iron Man\r\nAnd I love you 3000\r\nBaby, take a chance\r\n\'Cause I want this to be something\r\nStraight out of a Hollywood movie\r\nDa da, da da da dum\r\nNo spoilers please\r\nDa da, da da da dum\r\nNo spoilers please\r\nBaby, take my hand\r\nI just want to be your husband\r\n\'Cause I\'m your Iron Man\r\nAnd I love you 3000\r\nBaby, take a chance\r\n\'Cause I want this to be something\r\nStraight out of a Hollywood movie\r\nDa da, da da da dum\r\nNo spoilers please\r\nDa da, da da da dum\r\nNo spoilers please\r\nOh\r\nNo spoilers please\r\nDa da, da da da dum\r\nAnd I love you 3000', 'Jackson Wang (Vương Gia Nhĩ)', 'buon', 'approved'),
(13, 'Trò Chơi Tháp Rơi Tự Do / 跳楼机 (DJ A Bố Remix)', 'DBL', 'uploads/audio/1781165584_Tr___Ch__i_Th__p_R__i_T____Do______________DJ_A_B____Remix_.mp3', 'uploads/images/1781165584_1736936325485_300.jpg', 6, 1, NULL, '2026-06-11 08:13:04', 6, '作词 : 姜洄\r\n作曲 : 鹿柯的宁叔\r\n制作人 : LBI利比/赵楚峰\r\n编曲 : 卡其漠罗洋\r\n吉他 : 大牛\r\n混音&母带 : LBI利比\r\n和声 : LBI利比\r\n制作公司 : 天马行空文化\r\n策划/推广 : 大碗\r\n监制 : 杰森\r\n总监制 : 陈国威/许雯静\r\n出品 : 索尼音乐\r\n风走了 只留下一条街的叶落\r\n你走了 只留下我双眼的红\r\n逼着自己早点睡\r\n能不能再做一个有你的美梦\r\n我好像一束极光\r\n守在遥远的世界尽头\r\n看过了你的眼眸\r\n才知道孤独很难忍受\r\n可笑吗 我删访问记录的时候有多慌张\r\n他会看见吗 曾经只有我能看的模样\r\n从夜深人静 一直难过到天亮\r\n你反正不会再担心 我隐隐作疼的心脏\r\n好像遇到我 你才对自由向往\r\n怎么为他 失去一切也无妨\r\n可能是我贱吧\r\n不爱我的非要上\r\n那么硬的南墙非要撞\r\n是不是内心希望\r\n头破血流就会让你想起\r\n最爱我的时光\r\nbaby我们的感情好像跳楼机\r\n让我突然地升空又急速落地\r\n你带给我一场疯狂\r\n劫后余生好难呼吸\r\n那天的天气难得放晴\r\n你说的话却把我困在雨季\r\n其实你不是不爱了吧\r\n只是有些摩擦没处理\r\n怎么你闭口不语\r\n是不是我正好\r\n说中你的心\r\n就承认还是在意吧\r\n就骗骗我也可以\r\n可笑吗\r\n你的出现是我不能规避的伤\r\n怎么能接受这荒唐\r\n可能是我贱吧\r\n不爱我的非要上\r\n那么硬的南墙非要撞\r\n是不是内心希望\r\n头破血流就会让你想起\r\n最爱我的时光\r\nbaby我们的感情好像跳楼机\r\n让我突然地升空又急速落地\r\n你带给我一场疯狂\r\n劫后余生好难呼吸\r\n那天的天气难得放晴\r\n你说的话却把我困在雨季\r\n其实你不是不爱了吧\r\n只是有些摩擦没处理\r\n怎么你闭口不语\r\n是不是我正好\r\n说中你的心\r\n就承认还是在意吧\r\n哪怕骗骗我也可以', 'GAT', 'nang_dong', 'approved'),
(14, 'Jumping Machine跳楼机 (With Tyson Yoshi)', 'GATS', 'uploads/audio/1781165657_Jumping_Machine___________With_Tyson_Yoshi_.mp3', 'uploads/images/1781165657_1749649230256_300.jpg', 6, 0, NULL, '2026-06-11 08:14:17', 5, 'CHÁN', 'GÁYYS', 'buon', 'approved'),
(15, 'ABC1', 'lục tiểu lạc', 'uploads/audio/1781166489_Jumping_Machine___________With_Tyson_Yoshi_.mp3', 'uploads/images/1781166489_OIP.webp', 6, 0, NULL, '2026-06-11 08:28:09', 6, 'HAHAHA', 'GÁYYS', 'vui', 'approved'),
(16, 'BCBBC', 'lục tiểu lạc', 'uploads/audio/1781166560_Jumping_Machine___________With_Tyson_Yoshi_.mp3', 'uploads/images/1781166560_1749649230256_300.jpg', 6, 0, NULL, '2026-06-11 08:29:20', NULL, 'HEHE', 'ABS', 'vui', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'admin01', 'admin123', 'admin@example.com', 'admin', '2026-06-08 13:59:37'),
(4, 'DUCTUNG1', '$2y$10$W.zbBHKCgyQC9CdSYhWrPeyMyX4iEA5Aat1EzIHUkmhuc5FwDwtRu', 'tungdau888@gmaill.com', 'admin', '2026-06-08 14:23:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `playlist_songs`
--
ALTER TABLE `playlist_songs`
  ADD PRIMARY KEY (`playlist_id`,`song_id`),
  ADD KEY `song_id` (`song_id`);

--
-- Indexes for table `site_config`
--
ALTER TABLE `site_config`
  ADD PRIMARY KEY (`cfg_key`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `album_id` (`album_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `albums`
--
ALTER TABLE `albums`
  ADD CONSTRAINT `albums_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlists`
--
ALTER TABLE `playlists`
  ADD CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlist_songs`
--
ALTER TABLE `playlist_songs`
  ADD CONSTRAINT `playlist_songs_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `playlist_songs_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `songs`
--
ALTER TABLE `songs`
  ADD CONSTRAINT `songs_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `songs_ibfk_2` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
