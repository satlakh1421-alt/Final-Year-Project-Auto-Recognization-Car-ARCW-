-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 07, 2026 at 03:10 AM
-- Server version: 11.8.9-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u764947793_carwash_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `addons`
--

CREATE TABLE `addons` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addons`
--

INSERT INTO `addons` (`id`, `name`, `price`) VALUES
(1, 'Headlight Polish', 90.00),
(2, 'Full Mirror Polish', 200.00),
(3, 'Carpet Cleaning', 100.00),
(4, 'Roof Cleaning', 100.00),
(5, 'Engine Wash', 70.00),
(6, 'Insect Repellent', 35.00),
(7, 'Nano Mist', 40.00),
(8, 'Rain-X', 50.00),
(9, 'Scratch Removal', 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `email`, `password_hash`, `created_at`) VALUES
(1, 'admin', 'servproautospa.support@gmail.com', '$2y$10$yfN4LsfMj1SPQGqEHuVNA.9Es2ccpser7rLfVA/4gFfrVXxev2J1S', '2026-02-13 02:51:26');

-- --------------------------------------------------------

--
-- Table structure for table `anpr_captures`
--

CREATE TABLE `anpr_captures` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `plate_no` varchar(50) DEFAULT NULL,
  `qr_token` varchar(100) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'NEW',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anpr_captures`
--

INSERT INTO `anpr_captures` (`id`, `image_path`, `plate_no`, `qr_token`, `status`, `created_at`) VALUES
(1, 'uploads/1772946553_capture_20260308_130911.jpg', NULL, NULL, 'NEW', '2026-03-08 05:09:13'),
(2, 'uploads/1772946579_capture_20260308_130938.jpg', NULL, NULL, 'NEW', '2026-03-08 05:09:39'),
(3, 'uploads/1772946594_capture_20260308_130952.jpg', NULL, NULL, 'NEW', '2026-03-08 05:09:54'),
(4, 'uploads/1772946600_capture_20260308_130958.jpg', NULL, NULL, 'NEW', '2026-03-08 05:10:00'),
(5, 'uploads/1772946605_capture_20260308_131003.jpg', NULL, NULL, 'NEW', '2026-03-08 05:10:05'),
(6, 'uploads/1772947502_capture_20260308_132500.jpg', NULL, '5917703951413c697040a50b7adc535f', 'READY', '2026-03-08 05:25:02'),
(7, 'uploads/1772947509_capture_20260308_132507.jpg', NULL, '27aa615a759a74e3ce2b4cd23c83eaad', 'READY', '2026-03-08 05:25:09'),
(8, 'uploads/1772947526_capture_20260308_132524.jpg', NULL, '5f06c5e1701014ac8c904632003b6fca', 'READY', '2026-03-08 05:25:26'),
(9, 'uploads/1772947533_capture_20260308_132531.jpg', NULL, '4ee263a5cae51c58a0462c2ad001504f', 'READY', '2026-03-08 05:25:33'),
(10, 'uploads/1772947539_capture_20260308_132537.jpg', NULL, 'd8d2e58208715deeb8dc2268bc052316', 'READY', '2026-03-08 05:25:39'),
(11, 'uploads/1772947545_capture_20260308_132544.jpg', NULL, 'f55235435cb913f255ca63f70b469de3', 'READY', '2026-03-08 05:25:45'),
(12, 'uploads/1772947555_capture_20260308_132554.jpg', 'PRH 6676', '423ba8014b0daf9ebde09873cfaf63ab', 'READY', '2026-03-08 05:25:55'),
(13, 'uploads/1772947563_capture_20260308_132602.jpg', NULL, '0759b7dd691f46ba8f1d08c41c7f60d6', 'READY', '2026-03-08 05:26:03'),
(14, 'uploads/1772953530_capture_20260308_150528.jpg', 'RAJ 5534', '0c6c5006a15ba50ab214edc6c2009f90', 'READY', '2026-03-08 07:05:30'),
(15, 'uploads/1772959601_1772946553_capture_20260308_130911.jpg', 'PRH6676', NULL, 'READY', '2026-03-08 08:46:46'),
(16, 'uploads/1772959671_1772946594_capture_20260308_130952.jpg', '', NULL, 'READY', '2026-03-08 08:47:54'),
(17, 'uploads/1772964013_capture_20260308_180013.jpg', 'WMT313', NULL, 'READY', '2026-03-08 10:00:15'),
(18, 'uploads/1772964223_capture_20260308_180343.jpg', 'WXW1415', NULL, 'READY', '2026-03-08 10:03:45'),
(19, 'uploads/1772964293_capture_20260308_180453.jpg', 'MADANI8481', NULL, 'READY', '2026-03-08 10:04:55'),
(20, 'uploads/1772965274_capture_20260308_182113.jpg', 'BFL8331', 'fa85a8c7cf6475048e86e6fb3c8c6f63', 'READY', '2026-03-08 10:21:17'),
(21, 'uploads/1772966490_capture_20260308_184130.jpg', 'BJD168', 'be67543a35435552a4d7d09476ca51fd', 'READY', '2026-03-08 10:41:32');

-- --------------------------------------------------------

--
-- Table structure for table `captures`
--

CREATE TABLE `captures` (
  `id` int(11) NOT NULL,
  `plate_no` varchar(20) NOT NULL,
  `captured_at` datetime DEFAULT current_timestamp(),
  `token` varchar(50) NOT NULL,
  `status` enum('NEW','PENDING','PAID','WASHING','DONE') DEFAULT 'NEW',
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `captures`
--

INSERT INTO `captures` (`id`, `plate_no`, `captured_at`, `token`, `status`, `image_path`) VALUES
(1, 'ABC1234', '2026-02-10 21:18:47', 'TESTTOKEN123', 'PENDING', NULL),
(2, 'ESA 2114', '2026-02-11 06:49:55', '0e750b5d966e5d0e9201', 'PENDING', NULL),
(3, 'LLI 2354', '2026-02-11 07:08:52', 'd0fa4ca4737a5cbdd6df', 'NEW', NULL),
(4, 'RAN 7791', '2026-02-11 07:17:47', '08314914eb3f6287f87f', 'PENDING', NULL),
(5, 'SAT 1421', '2026-02-11 07:48:30', 'fd1e4e05f38b66ca1b58', 'PENDING', NULL),
(6, 'BAT 1122', '2026-02-11 07:57:56', '07feebbe17b12e0f548f', 'PENDING', NULL),
(7, 'WQJ 3453', '2026-02-11 08:10:04', '7a2deec23e67984882ba', 'PENDING', NULL),
(8, 'RAN 7791', '2026-02-11 08:30:26', '48c9e313eebc5213866a', 'PENDING', NULL),
(9, 'RAN 7791', '2026-02-11 11:24:24', 'c7ac2ce9d48d90d11263', 'PENDING', NULL),
(10, 'JAS 5544', '2026-02-11 11:30:00', '714a3dc36b2afe96c7c5', 'PENDING', NULL),
(11, 'BAT 1122', '2026-02-11 11:40:47', 'e7388f6d53580332fce6', 'PENDING', NULL),
(12, 'LIM 2114', '2026-02-11 11:50:25', '284163f281748f80b762', 'PENDING', NULL),
(13, 'BTS 4321', '2026-02-11 16:20:23', 'faa30f9af12085698fe0', 'PENDING', NULL),
(14, 'ESA 2114', '2026-02-11 16:39:32', '10dfb256e8eef6de2759', 'PENDING', NULL),
(15, 'LIM 2354', '2026-02-11 16:46:27', '3a9100d75d1eb0e7c2bb', 'PENDING', NULL),
(16, 'BST 1243', '2026-02-11 16:59:39', 'cfbf208585d7e950eeeb', 'PENDING', NULL),
(17, 'ASP 2143', '2026-02-11 17:06:05', '0f9a35c23e872bd374c8', 'PENDING', NULL),
(18, 'SAT 1421', '2026-02-12 03:20:29', '5c04bf012a6d865875e7', 'PENDING', NULL),
(19, 'LIM 6655', '2026-02-12 03:37:55', '280ae0d9aa349dd1468b', 'PENDING', NULL),
(20, 'LAP 1122', '2026-02-13 04:20:07', '00164143edba60d63d1a', 'PENDING', NULL),
(21, 'WQJ 3453', '2026-02-13 04:23:50', '80e8bf85dbf099587ac6', 'PENDING', NULL),
(22, 'SAT 1421', '2026-02-13 09:05:19', 'dd6a38db67f71e98b6f3', 'PENDING', NULL),
(23, 'XYZ 1029', '2026-02-13 09:51:02', '1a931f64dcb474972a17', 'PENDING', NULL),
(24, 'TAL 2938', '2026-02-13 10:40:38', 'dfe939ab0dc903dda52c', 'PENDING', NULL),
(25, 'QWS 2136', '2026-02-24 05:57:57', 'aab7015f91370aa1fae3', 'PENDING', NULL),
(26, 'ZAW 3322', '2026-02-25 08:22:36', '3a838e28f3d2e098cf90', 'PENDING', NULL),
(27, 'TTL 1243', '2026-02-25 08:37:05', '43db9a32f6569ed6d8fe', 'PENDING', NULL),
(28, 'WTR 6634', '2026-02-25 08:48:19', '7711cf9164a242f2c640', 'PENDING', NULL),
(29, 'TTF 1029', '2026-02-25 09:03:00', '2cd7ea01e41ac36cfde0', 'PENDING', NULL),
(30, 'TTD 1243', '2026-02-25 09:12:41', 'db7be8ae03d94f950204', 'PENDING', NULL),
(31, 'PPT 2343', '2026-02-25 09:22:45', '17fa285b4c2db3f18f5a', 'PENDING', NULL),
(32, 'MAT 2176', '2026-02-25 09:33:59', '992f9578cad18aea596e', 'PENDING', NULL),
(33, 'PPL 2233', '2026-02-25 09:43:46', 'b377a3f3b3da5bf5fecf', 'PENDING', NULL),
(34, 'WTF 2233', '2026-02-25 09:55:34', '7e43a4c924f3c98251f9', 'PENDING', NULL),
(35, 'TTR 4232', '2026-02-25 10:02:25', '942007cd1169bac9552d', 'PENDING', NULL),
(36, 'AAS 2267', '2026-02-25 10:06:19', '3d683db5198be5817df7', 'PENDING', NULL),
(37, 'DAE 7732', '2026-02-25 10:17:20', '1d1e52cfa85b434501ac', 'PENDING', NULL),
(38, 'QTR 6243', '2026-02-25 10:24:28', 'dd50f0b7b35303467e10', 'PENDING', NULL),
(39, 'DET 3251', '2026-02-25 15:53:58', 'b9abd9cf88bae0c89e87', 'PENDING', NULL),
(40, 'QWR 6421', '2026-02-26 03:21:03', '0ef2eb07766deb80dabc', 'PENDING', NULL),
(41, 'QAZ6655', '2026-02-26 03:36:10', '23b25441b7f7d53d0950', 'PENDING', NULL),
(42, 'WWQ 2211', '2026-02-26 03:57:45', '21b690951da5683ad50b', 'PENDING', NULL),
(43, 'TTP 9825', '2026-03-05 06:33:12', '809e21396905624995d8', 'PENDING', NULL),
(44, 'RAN 7791', '2026-03-05 06:43:50', 'a39c205fa9d961d824e9', 'NEW', NULL),
(45, 'SAT 1421', '2026-03-05 06:46:04', 'e3b400fc22a78660d93e', 'NEW', NULL),
(46, 'BAT 1122', '2026-03-05 06:46:31', '5bd807dee57bdc24050c', 'NEW', NULL),
(47, 'POT 2354', '2026-03-05 12:28:25', '6bdbcc2f09d52bde3ace', 'PENDING', NULL),
(48, 'TTB 2135', '2026-03-08 08:33:34', '5a972326a709676e69a0', 'PENDING', NULL),
(49, 'BFL8331', '2026-03-08 11:21:17', 'fa85a8c7cf6475048e86e6fb3c8c6f63', 'NEW', NULL),
(50, 'BJD168', '2026-03-08 11:41:32', 'be67543a35435552a4d7d09476ca51fd', 'NEW', 'uploads/1772966490_capture_20260308_184130.jpg'),
(51, 'QM9688E', '2026-03-08 13:26:50', '570e778e47074f4fae492308b2a9c643', 'PENDING', 'uploads/1772972806_capture_20260308_202645.jpg'),
(52, 'MAH41', '2026-03-08 14:18:58', 'd319a4ea93cc0968693fab10e07db3ac', 'PENDING', 'uploads/1772975935_capture_20260308_211854.jpg'),
(53, 'WMT313', '2026-03-10 04:47:26', 'b4076ed6a0694f680db097208b8897d9', 'NEW', 'uploads/1773114443_capture_20260310_114722.jpg'),
(54, 'WMT313', '2026-03-10 04:47:37', '3e66bafc6595918b3ad23ae3e49fa64b', 'NEW', 'uploads/1773114455_capture_20260310_114733.jpg'),
(55, 'QM9888E', '2026-03-10 04:48:01', '13921862194f53f2603de9ca186df46e', 'NEW', 'uploads/1773114475_capture_20260310_114754.jpg'),
(56, 'PRH6676', '2026-03-10 05:00:15', '1650b22fac72ae108603babf78fca08f', 'NEW', 'uploads/1773115212_capture_20260310_120010.jpg'),
(57, 'MAH41', '2026-03-10 07:36:21', '82d431a617ff61cc765fa8833a03e157', 'PENDING', 'uploads/1773124578_capture_20260310_143617.jpg'),
(58, 'QM9686E', '2026-03-10 08:18:04', 'b76a5d38a086e85998807e8b7ba4349a', 'NEW', 'uploads/1773127081_capture_20260310_151759.jpg'),
(59, 'QM8888E', '2026-03-10 08:18:38', 'b7a608b40ca77e2fb05b7a53d663f219', 'NEW', 'uploads/1773127115_capture_20260310_151833.jpg'),
(60, 'QM8088E', '2026-03-10 08:18:52', '7dc9b8de044f38b4e7b8615e078ac76d', 'NEW', 'uploads/1773127128_capture_20260310_151847.jpg'),
(61, 'VMT313', '2026-03-10 08:25:30', 'c0e2851bb89d9628ca55801eea31d7a4', 'NEW', 'uploads/1773127527_capture_20260310_152526.jpg'),
(62, 'MAH41', '2026-03-10 08:26:29', 'd6227827df1c92743ecf1e29f02a33e5', 'NEW', 'uploads/1773127585_capture_20260310_152624.jpg'),
(63, 'WA4414P', '2026-03-10 08:37:29', '01497a5f7e55f5293defa30acda317a2', 'NEW', 'uploads/1773128245_capture_20260310_153724.jpg'),
(64, 'WA4414P', '2026-03-10 08:38:42', '6fff0d8f33d10bdf7e5427f99dabd1e4', 'NEW', 'uploads/1773128319_capture_20260310_153838.jpg'),
(65, 'WA4414P', '2026-03-10 08:39:50', '41a67f1df69b6599f3ebb195c7ddd60c', 'NEW', 'uploads/1773128386_capture_20260310_153945.jpg'),
(66, 'PRH6676', '2026-03-10 08:43:49', '00e39b52ca2147e38258ff56f3ce6ca2', 'NEW', 'uploads/1773128626_capture_20260310_154345.jpg'),
(67, 'PRH6676', '2026-03-10 08:44:29', '9fb69416ff1f2634ab7f7d731564b8be', 'NEW', 'uploads/1773128666_capture_20260310_154425.jpg'),
(68, 'MAH41', '2026-03-10 08:56:20', 'f20f4a6163fd3ca51135be589be3ce43', 'NEW', 'uploads/1773129377_capture_20260310_155615.jpg'),
(69, 'MAH41', '2026-03-10 08:57:03', '28d412c3bcb70970363316cad84c3d81', 'NEW', 'uploads/1773129420_capture_20260310_155659.jpg'),
(70, 'MAH41', '2026-03-10 08:57:28', 'f4dd5dccd26cb13623045ad532e58153', 'NEW', 'uploads/1773129445_capture_20260310_155724.jpg'),
(71, '', '2026-03-10 08:57:42', 'd716d950a96a35e593af711dca6f41f1', 'NEW', 'uploads/1773129458_capture_20260310_155737.jpg'),
(72, '', '2026-03-10 08:58:31', '2ec2ea03d16b3b74a873d447db99d6ad', 'NEW', 'uploads/1773129508_capture_20260310_155826.jpg'),
(73, '', '2026-03-10 08:59:35', 'c404f91e89fc09be4cd496ba96032396', 'NEW', 'uploads/1773129572_capture_20260310_155930.jpg'),
(74, 'MAH41', '2026-03-10 08:59:56', '62f5b184df2a0920860ceb3c78141102', 'NEW', 'uploads/1773129594_capture_20260310_155952.jpg'),
(75, 'MAM41', '2026-03-10 09:00:18', '4a69ba567a8d17e19dd20ce7e809fe9a', 'NEW', 'uploads/1773129615_capture_20260310_160014.jpg'),
(76, 'MAH41', '2026-03-10 09:02:04', '4c0d7f5f7e023c34d9d73a98149613ea', 'NEW', 'uploads/1773129721_capture_20260310_160200.jpg'),
(77, '', '2026-03-10 10:05:48', '1f561d55fe9f66a4f7b2e0f2f75d3a8e', 'NEW', 'uploads/1773133544_capture_20260310_170543.jpg'),
(78, 'BMT313', '2026-03-10 10:06:02', 'db13164b1cf48d2c8d93e16e159d670a', 'NEW', 'uploads/1773133560_capture_20260310_170558.jpg'),
(79, '', '2026-03-10 10:06:15', '7b75146cc9aea69d30df966c407d9e28', 'NEW', 'uploads/1773133572_capture_20260310_170611.jpg'),
(80, 'BMT313', '2026-03-10 10:06:28', 'fb41718508c4cb19ebf7d88dc477b3c0', 'NEW', 'uploads/1773133585_capture_20260310_170624.jpg'),
(81, '', '2026-03-10 10:06:41', '16b3cc4521bbb9dfead0c08e8fc418cd', 'NEW', 'uploads/1773133598_capture_20260310_170637.jpg'),
(82, 'BNT313', '2026-03-10 10:06:54', 'aea6b664cfebf3ebc0203dff5c1554a3', 'NEW', 'uploads/1773133610_capture_20260310_170649.jpg'),
(83, '', '2026-03-10 10:07:07', 'c1ab8d3304f2ed991f33651085790792', 'NEW', 'uploads/1773133625_capture_20260310_170703.jpg'),
(84, 'QM8888E', '2026-03-10 10:07:21', '06e3af04db57808c484cc6fcb3b09f02', 'NEW', 'uploads/1773133639_capture_20260310_170717.jpg'),
(85, 'QM9888E', '2026-03-10 10:07:34', '1393c4dbc6432b0dfeb35a6fb4e67c2b', 'NEW', 'uploads/1773133651_capture_20260310_170730.jpg'),
(86, '', '2026-03-10 10:07:52', '2e71ee775a06c7b8b483ac9872544756', 'NEW', 'uploads/1773133669_capture_20260310_170747.jpg'),
(87, '', '2026-03-10 10:08:09', '4faf889f40566294474f6a0ceecc995d', 'NEW', 'uploads/1773133683_capture_20260310_170801.jpg'),
(88, '', '2026-03-10 10:09:45', '1f8ac6d4ff28261bbc7da50e90c55433', 'NEW', 'uploads/1773133782_capture_20260310_170941.jpg'),
(89, '', '2026-03-10 10:10:06', '4e89d859e85dfe675d29e66783d3ba38', 'NEW', 'uploads/1773133802_capture_20260310_171001.jpg'),
(90, '', '2026-03-10 10:10:43', '26685d4842cddbbda2545b343513f3ec', 'NEW', 'uploads/1773133840_capture_20260310_171038.jpg'),
(91, '', '2026-03-10 10:11:43', 'e9867cc067d567b45054cdc75b53a87c', 'NEW', 'uploads/1773133898_capture_20260310_171136.jpg'),
(92, 'MAH41', '2026-03-10 10:15:09', 'cc80dacc2cfdd186046afd6ee6647abf', 'NEW', 'uploads/1773134106_capture_20260310_171504.jpg'),
(93, 'MAH41', '2026-03-10 10:15:46', '4ab94e91bb6beaba9ee372d888f5ae13', 'NEW', 'uploads/1773134144_capture_20260310_171542.jpg'),
(94, 'WA4414P', '2026-03-10 10:20:37', '4819002043b19dded8c0e8f2a3b6a9d0', 'NEW', 'uploads/1773134433_capture_20260310_172032.jpg'),
(95, 'VB836', '2026-03-10 10:40:42', 'b2239db6a7295669762a309e0515cee0', 'NEW', 'uploads/1773135636_capture_20260310_174034.jpg'),
(96, 'VB8361', '2026-03-10 10:45:07', '833d451d5a020ba984496d9f110f1e6d', 'NEW', 'uploads/1773135901_capture_20260310_174459.jpg'),
(97, 'MAH41', '2026-03-11 16:08:15', '77affc3d667ecae846917cc02d9ce886', 'NEW', 'uploads/1773241692_capture_20260311_230810.jpg'),
(98, 'WA4414P', '2026-03-11 16:31:30', 'f76253cdb950a609cc33b131332ece8d', 'NEW', 'uploads/1773243087_capture_20260311_233125.jpg'),
(99, 'BLE5530', '2026-03-11 16:32:33', '11381855909ccf34bf6230ae36552d5e', 'NEW', 'uploads/1773243150_capture_20260311_233228.jpg'),
(100, 'WMT313', '2026-03-12 03:36:33', '4c9dffd34d65f53844f1cf4ca331d2db', 'PENDING', 'uploads/1773282990_capture_20260312_103629.jpg'),
(101, 'QM9686E', '2026-03-12 03:47:33', '46f33e5c361e0081b737a59d9604eff4', 'PENDING', 'uploads/1773283650_capture_20260312_104728.jpg'),
(102, 'MAH41', '2026-03-12 04:14:38', 'a28f8f6964e466e5b39258e2ddf0d5e0', 'PENDING', 'uploads/1773285275_capture_20260312_111434.jpg'),
(103, 'QM9686E', '2026-03-12 04:19:12', '0a1a6d6a9f42f117187f409146717a03', 'PENDING', 'uploads/1773285550_capture_20260312_111908.jpg'),
(104, 'QM9888E', '2026-03-12 15:01:23', '33b0082515db64ca2964b75600a312cd', 'NEW', 'uploads/1773298880_capture_20260312_150116.jpg'),
(105, 'WMT313', '2026-03-12 15:02:02', 'a0d0a481ba7bd059212f9e30269fc715', 'PENDING', 'uploads/1773298919_capture_20260312_150157.jpg'),
(106, 'JFC2218', '2026-03-12 16:00:32', '240d10a83a765cd60b6b6fec61c0a3b8', 'PENDING', 'uploads/1773302429_capture_20260312_160027.jpg'),
(107, 'PFQ5217', '2026-03-12 17:18:31', '6cf372b09e4abde06ce33437dc73e8d4', 'PENDING', 'uploads/1773307108_capture_20260312_171826.jpg'),
(108, 'WA4414P', '2026-03-12 17:22:57', '9bd25e73b9b09664ec54053c3796923e', 'PENDING', 'uploads/1773307374_capture_20260312_172251.jpg'),
(109, 'QM8888E', '2026-03-12 17:41:48', '16b4cd3bf0223a02ea0b5a2cdc4fcfb9', 'PENDING', 'uploads/1773308505_capture_20260312_174143.jpg'),
(110, 'MAH41', '2026-03-12 17:52:17', '5ce7acb2169fbf6a9681853cf3bc2106', 'PENDING', 'uploads/1773309134_capture_20260312_175212.jpg'),
(111, 'WQA 6132', '2026-03-13 07:10:57', 'abaa850ced8cd49be6aa', 'PENDING', NULL),
(112, 'MAH 41', '2026-03-13 07:15:10', '2c6b17437643bbd67766', 'NEW', NULL),
(113, 'MAH41', '2026-03-13 07:15:41', 'f7b84aa9acec2df75c3b', 'PENDING', NULL),
(114, 'TTK8866', '2026-03-13 15:24:38', 'e6ed29d7e1b312076ec2', 'PENDING', NULL),
(115, 'QQW3124', '2026-03-13 15:30:05', '57895e8f8065631c2649', 'PENDING', NULL),
(116, 'RAN 7791', '2026-03-13 15:33:45', '9e3a6d8a18b4e18904a3', 'PENDING', NULL),
(117, 'BAT 1122', '2026-03-13 15:52:15', 'e79a3a70583858ffe8d0', 'PENDING', NULL),
(118, 'EDR5386', '2026-03-13 16:50:08', '110c2d76bf1835c2488f', 'PENDING', NULL),
(119, 'RAN 7791', '2026-03-15 13:26:44', '5b5b69a34d4335214c17', 'PENDING', NULL),
(120, 'SAT 1421', '2026-03-15 13:57:16', '91632219fea414b7ca65', 'PENDING', NULL),
(121, 'BAT 1122', '2026-03-15 14:26:23', '02b337bade8f0a4dfd61', 'PENDING', NULL),
(122, 'ESA 2114', '2026-03-15 14:30:36', 'e9d582266d9956519b94', 'PENDING', NULL),
(123, 'BBD 5321', '2026-03-15 14:50:18', '1e4d55dcd6a5430ca0e8', 'PENDING', NULL),
(124, 'WMT313', '2026-03-15 15:58:24', 'd9ba1f15bf3670fd64a88f09b0182358', 'NEW', 'uploads/1773561502_capture.jpg'),
(125, 'MAH41', '2026-03-15 16:15:57', '6b22a48ff1d4e1730eb74a974fe2bfdd', 'NEW', 'uploads/1773562555_capture.jpg'),
(126, 'WA4414P', '2026-03-15 16:16:40', 'f565b6130e92cb727e9b6be94c935e73', 'NEW', 'uploads/1773562598_capture.jpg'),
(127, 'MAM41', '2026-03-15 16:25:58', '6c3040940c2e2cfef09ee65e071e80af', 'NEW', 'uploads/1773563157_capture.jpg'),
(128, 'VB836', '2026-03-15 16:36:09', '80ba86cc08f95533e2680a695265fe9d', 'NEW', 'uploads/1773563768_capture.jpg'),
(129, 'VB836', '2026-03-15 17:12:53', '7624397a77c1457f5cc9611fe3cb69f2', 'NEW', '1773565972_capture.jpg'),
(130, 'BLE6530', '2026-03-15 17:13:38', '2c4e41d6f554652cc49cc0e9d790ad6b', 'NEW', '1773566016_capture.jpg'),
(131, 'QW8888E', '2026-03-15 17:22:54', '928c047f880af4fae3e4ab709c76e689', 'NEW', '1773566572_capture.jpg'),
(132, 'MAH41', '2026-03-15 17:23:17', '912d7de98a252f6095d42db340639efe', 'NEW', '1773566596_capture.jpg'),
(133, 'PRH6676', '2026-03-15 17:23:40', '987333896827b088be8d1a5bb1f7b282', 'NEW', '1773566618_capture.jpg'),
(134, 'JFC221', '2026-03-15 17:27:52', 'a721f6000786ff10120a3628a78f431f', 'NEW', '1773566871_capture.jpg'),
(135, 'W1234N', '2026-03-15 17:29:55', '5d93a66b208952a55dba3ea64a679c37', 'NEW', '1773566993_capture.jpg'),
(136, 'QAB4838E', '2026-03-15 17:30:30', '264a55c0e09059dec8164d03ac958d7a', 'NEW', '1773567028_capture.jpg'),
(137, 'AAA4444', '2026-03-15 17:31:00', '25cb36d51f31efa102d5a51d13a660ad', 'NEW', '1773567058_capture.jpg'),
(138, 'SPE691P', '2026-03-15 17:39:31', 'ba4f57c6acb5fd193a53a3cd462c627a', 'NEW', '1773567570_capture.jpg'),
(139, 'SMA112', '2026-03-15 17:39:59', '1fe5bf67fed90d05e890d078e29c8318', 'NEW', '1773567598_capture.jpg'),
(140, 'SJQ4849D', '2026-03-15 17:41:07', '9e346c4239976c7caf2a686bbed50cb6', 'NEW', '1773567665_capture.jpg'),
(141, 'BHV33', '2026-03-15 18:11:51', '128b15b60c425dd0dfcf311a1231f6fe', 'NEW', 'uploads/1773569510_capture.jpg'),
(142, 'MAH41', '2026-03-16 14:00:56', '0e50d8e5bc478d8c97ed0bf32810a60e', 'NEW', 'uploads/1773640854_capture.jpg'),
(143, 'KN232', '2026-03-16 14:27:03', '8c85f4603914c2c63c797556f89fdc37', 'NEW', 'uploads/1773642422_capture.jpg'),
(144, 'WA4414P', '2026-03-16 15:38:59', 'feb308ab43cb1e5c07cfa5864a91c4a3', 'NEW', 'uploads/1773646738_capture.jpg'),
(145, 'VB836', '2026-03-16 15:39:25', '4af50c2cd0e21c37faccc67af5324417', 'NEW', 'uploads/1773646763_capture.jpg'),
(146, 'WMT313', '2026-03-16 15:39:51', '0de67bd34b0584d2de18367a53185774', 'NEW', 'uploads/1773646790_capture.jpg'),
(147, 'PRH6676', '2026-03-16 15:40:25', '8e55de1095ace8c92b7897d22115a20a', 'NEW', 'uploads/1773646823_capture.jpg'),
(148, 'MAX9799', '2026-03-16 15:42:51', 'b0df2598f00ddc75b829359ffd3ac96d', 'NEW', 'uploads/1773646969_capture.jpg'),
(149, 'WMT313', '2026-03-16 16:18:53', '305ec4db79280680633e87d2452f80a3', 'NEW', 'uploads/1773649132_capture.jpg'),
(150, 'MAH41', '2026-03-16 16:19:52', '1db505bd13aaa4506d632d47a46af8d3', 'NEW', 'uploads/1773649190_capture.jpg'),
(151, 'MA8481', '2026-03-16 16:24:48', '81700e56196b49d2d7fc2039f263c085', 'NEW', 'uploads/1773649486_capture.jpg'),
(152, 'JFC2218', '2026-03-16 16:28:11', '5e51230a84326c75f27a53703ee4fc9c', 'NEW', 'uploads/1773649689_capture.jpg'),
(153, 'W1234N', '2026-03-16 16:29:17', '64cfeb7f3c60f4e8c4b8e5fd94d99727', 'NEW', 'uploads/1773649756_capture.jpg'),
(154, 'QAA1989C', '2026-03-16 16:29:47', 'e99bee1d60da59454fa2e6ff46f1ab26', 'NEW', 'uploads/1773649786_capture.jpg'),
(155, 'PFQ5217', '2026-03-16 16:32:16', '1d748503ac9a1fe357ace29544781fc2', 'NEW', 'uploads/1773649935_capture.jpg'),
(156, 'WXW1415', '2026-03-16 16:32:50', 'ad4378d20fe79ac33ba8727bbe662e1b', 'NEW', 'uploads/1773649969_capture.jpg'),
(157, 'WPV8180', '2026-03-16 16:36:54', '35795617e876c96f84adb8c3667c0833', 'NEW', 'uploads/1773650212_capture.jpg'),
(158, 'WVK280', '2026-03-16 16:37:21', 'fecea4eab11ac33bf662aaa9b091caee', 'NEW', 'uploads/1773650239_capture.jpg'),
(159, 'W8888W', '2026-03-16 16:37:41', 'a7c34206fc08a0749c815303a817947a', 'NEW', 'uploads/1773650260_capture.jpg'),
(160, 'WWW3228', '2026-03-16 16:42:50', '672aeefbb68becd48f315fc8e2b90863', 'PENDING', 'uploads/1773650568_capture.jpg'),
(161, 'BLE553', '2026-03-16 17:18:10', '6367e86a2a5353a77d4f2ee230e11aea', 'NEW', 'uploads/1773652688_capture.jpg'),
(162, 'BLE6530', '2026-03-16 17:19:27', '823d3f9304cfe2c2dea93a3aa58dc2a2', 'NEW', 'uploads/1773652761_capture.jpg'),
(163, 'BLE5530', '2026-03-16 17:21:14', 'e10bffa6c98fe39844f625970c3033fa', 'NEW', 'uploads/1773652872_capture.jpg'),
(164, 'BLE6630', '2026-03-16 17:23:38', '5ac2c6097d53f32ee730330ce551b441', 'NEW', 'uploads/1773653017_capture.jpg'),
(165, 'VB836', '2026-03-16 17:24:24', '1e719f04849da2b6fca2f818b921e7b9', 'NEW', 'uploads/1773653062_capture.jpg'),
(166, 'WA4414P', '2026-03-16 17:25:21', '61bd9a7866f8b409e402cfd1e36ac5b8', 'PENDING', 'uploads/1773653120_capture.jpg'),
(167, 'WA4414P', '2026-03-16 17:57:56', '4fdcd6fa85b7d02329c15663773c3cad', 'NEW', 'uploads/1773655074_capture.jpg'),
(168, 'AAA4444', '2026-03-17 23:21:55', 'b9db0c7c2b062dec330b211be30ec7e3', 'NEW', 'uploads/1773760913_capture.jpg'),
(169, 'MAH41', '2026-03-17 23:23:19', '89722068ec935c69c89c6f3205d2af0f', 'NEW', 'uploads/1773760997_capture.jpg'),
(170, 'RAJ5534', '2026-03-17 23:24:42', '323bd00aff0dd878212e4b293b52c7b7', 'NEW', 'uploads/1773761081_capture.jpg'),
(171, 'WA4414P', '2026-03-17 23:56:25', 'c9d26f721f477306556cc2302e4d3e0b', 'NEW', 'uploads/1773762983_capture.jpg'),
(172, 'LAT6523', '2026-03-19 11:22:12', '51cfc58c37232b59866d', 'PENDING', NULL),
(173, 'RAN 7791', '2026-03-25 13:58:32', '3ff149cd373d984ce692', 'PENDING', NULL),
(174, 'SAT 1421', '2026-03-25 14:17:30', '81d2d2e7260e1f6224ff', 'PENDING', NULL),
(175, 'BAT 1122', '2026-03-25 14:21:06', 'b80df0a9308707a7a3f5', 'PENDING', NULL),
(176, 'BAT 1122', '2026-03-25 14:21:14', '625b969da4e2ef27b3ff', 'PENDING', NULL),
(177, 'ESA 2114', '2026-03-25 22:34:27', '11fa19d773f97fe85c79', 'PENDING', NULL),
(178, 'RAN 7791', '2026-03-27 17:12:40', 'a0dd2cbccdff55bdfc9a', 'NEW', NULL),
(179, 'WB4089Q', '2026-03-29 16:04:10', '3905184854cfd2bb0bad', 'PENDING', NULL),
(180, 'MAH41', '2026-03-31 14:49:59', '3ccac2394a374015509580a80efdb3f1', 'NEW', 'uploads/1774939798_capture.jpg'),
(181, 'RAJ7741', '2026-03-31 15:42:52', 'c09b97bbd54664b951736274aba81dc7', 'NEW', 'uploads/1774942971_capture.jpg'),
(182, 'RAB4421', '2026-03-31 15:44:22', '60883e79dab91f506c6fecae19a13a4c', 'NEW', 'uploads/1774943060_capture.jpg'),
(183, 'RAJ7791', '2026-03-31 15:49:42', '6616f3b991ec4104fcda0ce9942669fd', 'PENDING', 'uploads/1774943381_capture.jpg'),
(184, 'RAN 7791', '2026-04-02 10:34:38', '71a61168f101414e9475', 'PENDING', NULL),
(185, 'SAT1421', '2026-04-04 20:35:51', '2690642f6a7c13e0cd39e82a3a86c91a', 'NEW', 'uploads/1775306150_capture.jpg'),
(186, 'SAT1421', '2026-04-04 21:03:59', '7679edb511ef984a9384817cb3e48339', 'NEW', 'uploads/1775307837_capture.jpg'),
(187, 'BAT1421', '2026-04-04 21:15:38', '249fe6d9697d97f342f972e393425126', 'NEW', 'uploads/1775308537_capture.jpg'),
(188, 'SAT1421', '2026-04-04 21:25:45', 'e3dc473aa15ab7a751a0c0f211f19a74', 'PENDING', 'uploads/1775309144_capture.jpg'),
(189, 'T421', '2026-04-06 22:14:16', 'e57c45b43b6a4f5622d4b8d894b9746d', 'NEW', 'uploads/1775484855_capture.jpg'),
(190, 'T1421', '2026-04-06 22:14:48', '36614c56339a5c2325a9d3788fe9f1c8', 'NEW', 'uploads/1775484887_capture.jpg'),
(191, 'TY421', '2026-04-06 22:15:01', '24cbc4dad23db81e59021f7c839ee3c7', 'NEW', 'uploads/1775484899_capture.jpg'),
(192, 'SAT1421', '2026-04-06 22:17:56', 'e351ae3bbacc12c61be00633a2b29e0b', 'NEW', 'uploads/1775485075_capture.jpg'),
(193, 'AT1421', '2026-04-06 22:19:40', '21e43e3a574d957a4ba60a024fabe32f', 'NEW', 'uploads/1775485178_capture.jpg'),
(194, 'SAT1421', '2026-04-06 22:38:43', 'b9181f77c8247b91c194c3041929df7b', 'NEW', 'uploads/1775486322_capture.jpg'),
(195, 'AT1421', '2026-04-06 22:39:43', '677d3473df30bce0b344cb74da284842', 'NEW', 'uploads/1775486382_capture.jpg'),
(196, 'SAT1421', '2026-04-08 14:03:11', '57c5c52d89f35462a64728292adce410', 'NEW', 'uploads/1775628190_capture.jpg'),
(197, 'WQT6421', '2026-04-09 11:45:55', '8e188032ffa48cb7edeb', 'PENDING', NULL),
(198, 'TTW1029', '2026-04-09 20:34:52', 'f453e7153947232caf78', 'PENDING', NULL),
(199, 'TRW1029', '2026-04-10 19:24:39', '4e37243f1349566ed78b', 'PENDING', NULL),
(200, 'WQA 6132', '2026-04-10 19:53:48', '16fd13539dcba578773f', 'PENDING', NULL),
(201, 'RAN 7791', '2026-04-10 21:28:24', '2195865be6d431ab22d7', 'PENDING', NULL),
(202, 'ESA 2114', '2026-04-10 23:42:39', 'db4912d788d1521df919', 'PENDING', NULL),
(203, 'TRQ3215', '2026-04-10 23:45:18', 'a54ab30259e42b866770', 'PENDING', NULL),
(204, 'RAJ 5534', '2026-04-11 00:59:26', 'eed08333d61f5b4a6ec8', 'PENDING', NULL),
(205, 'RAN 7791', '2026-04-11 01:51:31', '382a5ad0f979daae04be', 'PENDING', NULL),
(206, 'SAT 1421', '2026-04-11 01:55:31', '18cf2d227d73e4c0d644', 'PENDING', NULL),
(207, 'BAT 1122', '2026-04-11 08:33:05', 'ff4824405650893ce354', 'PENDING', NULL),
(208, 'WQJ 3453', '2026-04-11 08:44:00', '4caacada978cc76b06b6', 'PENDING', NULL),
(209, 'DAE 7732', '2026-04-11 09:03:59', '9d23ee53a61cdd3287b3', 'PENDING', NULL),
(210, 'SAT 1421', '2026-04-11 09:06:12', '71cf74ae9f65a830abf6', 'NEW', NULL),
(211, 'SAT 1421', '2026-04-11 09:14:42', '5b54d2d101c75255744b', 'NEW', NULL),
(212, 'RWA5421', '2026-04-11 10:03:47', 'dd9329aae3ae5e45647a', 'NEW', NULL),
(213, 'RAN 7791', '2026-04-11 10:21:50', '4a90ed651af291f5caa0', 'PENDING', NULL),
(214, 'SAT 1421', '2026-04-11 10:22:45', '95347fa2ef2a0e6786dd', 'PENDING', NULL),
(215, 'SAT1421', '2026-04-11 13:27:12', '3f5596c4cee083ee1400aabed90fb3c4', 'PENDING', 'uploads/1775885231_capture.jpg'),
(216, 'SAT1421', '2026-04-12 16:46:35', '88a654d9a61827550d90d61ea76b3204', 'PENDING', 'uploads/1775983593_capture.jpg'),
(217, 'RAN 7791', '2026-04-12 17:48:39', '76efe12d83c485f2044f', 'PENDING', NULL),
(218, 'SAT1421', '2026-04-13 08:25:59', '6158cb80c3b4cd432d3d1e75bce081c1', 'NEW', 'uploads/1776039958_capture.jpg'),
(219, 'SAT142', '2026-04-13 08:26:36', '4d474276f891c43824060358c7a761ba', 'PENDING', 'uploads/1776039994_capture.jpg'),
(220, 'SAT1421', '2026-04-13 10:16:43', '6982ff6dcfd1decb6d80e5c72266c5ed', 'PENDING', 'uploads/1776046602_capture.jpg'),
(221, 'VBX5842', '2026-04-13 11:01:07', 'e726cd5adaa82a812b59', 'NEW', NULL),
(222, 'KDX2561', '2026-04-13 11:09:27', '2f22dabfd4cf8f1a115c', 'NEW', NULL),
(223, 'PDF 3477', '2026-04-13 11:18:52', '44dbb2c9ac0d02738d08', 'NEW', NULL),
(224, 'PDF 3477', '2026-04-13 11:19:04', '76ea13287f1cb5fdcad2', 'PENDING', NULL),
(225, 'DDY6669', '2026-04-13 11:30:59', 'ad20a6b227c890852575', 'PENDING', NULL),
(226, 'SAT1421', '2026-04-13 12:26:55', 'b668209530d67912222f7e51ffbd0705', 'NEW', 'uploads/1776054414_capture.jpg'),
(227, 'SAT1422', '2026-04-13 12:29:19', 'ee872eb255f4a035c8bf', 'PENDING', NULL),
(228, 'SAT1421', '2026-04-13 12:50:06', '05a7875470fa5144704514f6f0ecf26d', 'NEW', 'uploads/1776055804_capture.jpg'),
(229, 'VNA453', '2026-04-16 13:59:30', 'c9f3eaf0d33e4ce15f74f323f9b2fbbc', 'NEW', 'uploads/1776319169_capture.jpg'),
(230, 'RAN 7791', '2026-04-16 14:00:16', 'bd03ba2fddd86068c82c', 'NEW', NULL),
(231, 'BLE653D', '2026-04-16 14:49:44', '01a12d976f2eb922e00bde87f0daaf29', 'NEW', 'uploads/1776322183_capture.jpg'),
(232, 'BLE5530', '2026-04-16 14:50:36', 'cf2ec7444fe217c6a42cd5b3cebf923e', 'NEW', 'uploads/1776322235_capture.jpg'),
(233, 'BLE553D', '2026-04-16 14:51:28', '734c7a861302d343721244e576a8ee73', 'NEW', 'uploads/1776322286_capture.jpg'),
(234, 'BLE553D', '2026-04-16 14:53:43', 'e92c0118930613972f50', 'PENDING', NULL),
(235, 'TBS1243', '2026-04-17 10:45:12', '62091b693eaf8c3fd5a5', 'PENDING', NULL),
(236, 'SAT 1421', '2026-04-27 11:21:20', 'd44eb2286fb840650ab0', 'PENDING', NULL),
(237, 'WQR 5213', '2026-04-27 11:22:55', 'b7e66986a6772cbe022b', 'PENDING', NULL),
(238, 'MAH41', '2026-05-06 13:15:32', 'f7d87c56f7624e4a1be5d3e719bd4fba', 'NEW', 'uploads/1778044530_capture.jpg'),
(239, 'PFQ5217', '2026-05-07 11:19:07', '72e66e1b585d2989be6a59b8d9e0c8d9', 'PENDING', 'uploads/1778123946_capture.jpg'),
(240, 'PFQ5217', '2026-05-07 11:20:00', 'c902ccb7cd4ae5a6d1bb', 'PENDING', NULL),
(241, 'JAS4774', '2026-05-09 10:53:06', 'b688a85014813ab6f70a', 'PENDING', NULL),
(242, 'TTL4422', '2026-05-09 12:11:48', '6a9f488fb4fac08c48c4', 'PENDING', NULL),
(243, 'ZAQ2651', '2026-05-15 21:32:06', 'aafd7021316546db4333', 'PENDING', NULL),
(244, 'PMM9181', '2026-05-18 15:37:48', '01f2b4905655f449387e', 'PENDING', NULL),
(245, 'RAH5543', '2026-08-26 10:34:05', '0cc82c3f31dc9e0bc7b5', 'NEW', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `password_hash`, `created_at`, `last_login`, `is_active`) VALUES
(1, 'RANJIT KAUR A/P JIMIR SINGH', 'ranjitaor321@gmail.com', '0174787791', '$2y$10$ULpwpgRD5WpCH0h4R.0w2uEs3voTRt6T7LMNMJamycNKx2W.OHl0O', '2026-02-10 22:03:05', '2020-04-15 10:51:20', 0),
(2, 'Joginder Singh A/L  Balwan Singh', 'joginder@gmail.com', NULL, '$2y$10$TCAEOo9Z2BaxI7QXdmifWeMBhQyuN4zMQuL5yJkBccjncNmW633zq', '2026-02-10 22:03:36', '2026-04-11 10:50:39', 1),
(3, 'Satvindeep Singh A/L Joginder Singh', 'satlakh2222@gmail.com', NULL, '$2y$10$gp8S0khaV5ujvhyh.rhhEOE96iN4Jel7X3zeIKvJWr15.GapNjffa', '2026-02-10 22:03:45', '2026-04-10 23:14:46', 1),
(4, 'Lim Ven Cheng', 'limvencheng@gmail.com', NULL, '$2y$10$hsW/MOvHPWOnOOl3jnwmUeOIiUrl86A6rzHa4/B18dMeGPper4dzC', '2026-02-10 22:04:37', NULL, 1),
(5, 'Raj', 'policeevo@gmail.com', NULL, '$2y$10$OQbp9Kof94KgLUgJpSKwZeh.kvYcn3HiJ6MYcNhIZPH74yCKqfZaS', '2026-02-10 22:06:00', NULL, 1),
(6, 'Jaspreet Singh', 'preetsingh@gmail.com', NULL, '$2y$10$vlyP6MhKQabXds76jWdrWuwDqSLXqg2JrDKNuuSixCq.vCi1T3atG', '2026-02-10 22:25:36', NULL, 1),
(7, 'Satvindeep Singh A/L Joginder Singh', 'satvindeepsingh1421@gmail.com', NULL, '$2y$10$f6sX4BTRC/9ZquO9L4jV8.lwJrNWBWnfVMf/H5VvWAp6wustAXmN6', '2026-02-11 18:51:15', '2026-03-25 13:56:21', 1),
(8, 'Jusrorizal', 'jusrorizal@gmail.com', NULL, '$2y$10$Qz9NJ12kcq56LPWwrnre1uXNsZjX37evRMs.0Z5WueV5TNXFl/QN6', '2026-02-26 10:38:23', '2026-04-16 15:03:57', 1),
(9, 'Kalvinder Singh', 'kallu2141@gmail.com', NULL, '$2y$10$GWE95fH304GB7gcgnQtNf..7JnDCA39pqJjbMbNu14m0mD7lq9KbK', '2026-03-05 13:32:13', NULL, 1),
(10, 'Jimir Singh', 'jimir@gmail.com', NULL, '$2y$10$dal9brzb1j5gk76xWME9bORDIzjlUVrh7qytXT/KwtGGHXU2Mzv5u', '2026-03-05 19:27:16', '2026-03-05 12:33:27', 1),
(11, 'oscar', 'oscarhyxx@gmail.com', NULL, '$2y$10$93GvkT8KROPVkv0JFumlSuEAuo2ZyFul.Hkpti8hmb28mcKcJyzHa', '2026-03-12 10:51:46', '2026-03-12 04:20:22', 1),
(12, 'Thivesh', 'flash11@gmail.com', NULL, '$2y$10$LEt2m1YVX8kl4QCc7awyL.72mnP35AxtMizY8tQHh26T75y0yYVUS', '2026-03-12 15:18:53', '2026-03-12 10:23:35', 1),
(13, 'Samsung', 'samsung@gmail.com', NULL, '$2y$10$ecXsXJM18HdH6VkYAqegfeALt9e/MHAw9T2QCuK9cxpCWuo5MhIJ6', '2026-03-12 16:02:13', '2026-03-12 09:02:27', 1),
(14, 'Edor', 'vchey0514@gmail.com', NULL, '$2y$10$ETuqyRU7GA0CA5/KApwem.dBCYg5o8ZZsYK0MiaEoz78jcipwFifS', '2026-03-13 16:50:03', '2026-03-13 16:52:01', 1),
(15, 'Kala Singh A/L Kalder Singh', 'kalasingh@gmail.com', '0124177053', '$2y$10$SPV6gwlsxW7dT1GncOXvleJmGHTF3QLFo59ylZagG2dIFnJ9vCasG', '2026-03-15 14:47:04', '2026-03-15 14:53:54', 1),
(16, 'CIBAI', 'soonlai1129@gmail.com', '01112477991', '$2y$10$U7tgyf9qxtqCGTNBSeEWp.5VfLkCIs7twS1veij6r6yOq1iz.vIYe', '2026-03-15 22:07:57', '2026-03-15 22:08:07', 1),
(17, 'House', 'house@gmail.com', '012492222211', '$2y$10$y0RQ5dehEMJK0j4qYoiIT.gJIpnojKMbqa33yf0GFzoioQNSFafQW', '2026-03-16 16:45:14', '2026-03-16 16:49:53', 1),
(18, 'Lakhvinjit Kaur A/P Joginder Singh', 'lakhvinder@gmail.com', '012436543212', '$2y$10$n25mwlRttSZQuT9kTUQ5x.Lme1pOfZynvsaxp3OjYxrvMd7N8xx8y', '2026-03-16 17:29:45', '2026-03-16 17:42:02', 1),
(19, 'Jimir Singh', 'jimsingh@gmail.com', '0162833810', '$2y$10$Aq9JvutD5Iowyuu/H38WDeVhbOeRuklR1Np4iU8nQKSJERYLKdlFq', '2026-03-17 23:27:36', NULL, 1),
(20, 'Satu Singh', 'satu21@gmail.com', '01159774982', '$2y$10$V6lSFBz7g4dc8f0q8d1Rm.nsL.ITr7VaJpuSUSRqHsYMg16HTRMDq', '2026-03-17 23:31:40', '2026-03-17 23:32:01', 1),
(21, 'Satvinder Singh', 'satvindersingh@gmail.com', '0163732449', '$2y$10$tqb8E7lvMLgQjiCcNT/QWeTLs0jRSNBK8hO/CnU5MSAoehn7.s6hW', '2026-03-18 22:10:30', NULL, 1),
(22, 'Nana Singh', 'jimir2121@gmail.com', '01159776543', '$2y$10$gxMBOj5efQPty6hyBaKqXOtryGXib1vXWnWfNeQMSz3yOy6rTpD/G', '2026-03-25 14:20:27', '2026-03-25 14:21:33', 1),
(23, 'Rajdeep Singh', 'raj67@gmail.com', '013775564321', '$2y$10$07r44Fy1e1pvy.P0zw3HyeoIpN2YssSBmjoB5IcLQd5LCI66JoVPe', '2026-03-27 16:45:34', '2028-12-26 17:09:54', 1),
(24, 'Sara', 'sara@gmail.com', '012425441276', '$2y$10$Re2drJOFLWb83.93iwJmRO1BMtznccPtQmauRdOcxChcVApQCDPnW', '2026-03-29 16:00:33', '2026-03-29 16:36:00', 1),
(25, 'Shailez', 'shailnucr7@gmail.com', '0189482015', '$2y$10$N4V10NZhLfbYtr2B5ZPg3uSgrOTgF5xhFBXjIjrbRcuteRbRRFWQe', '2026-04-02 10:36:49', '2026-04-13 11:33:02', 1),
(26, 'Jun', 'staractive7744@gmail.com', '01157678602', '$2y$10$tbT27FXSHnFEItu7bSA3C.2xeY30soJfJ7s2v3Q/NWSyczye639iO', '2026-04-06 14:16:26', '2026-04-06 14:16:56', 1),
(27, 'aiman', 'aiman.hakim5618@gmail.com', '0196170445', '$2y$10$T.D.HRZZBW5Ju0rQjNo/s.vC5t0wF5gtiP4IzgwZ5wsAsHBhdhCAm', '2026-04-06 15:51:25', '2026-04-06 16:00:51', 1),
(28, 'June Lee', 'june22@gmail.com', '01159772231', '$2y$10$YrhyHKtQA.wufoVmQxoxauhWWS49TDdn21CeXixQxbh/syAdkG0Bi', '2026-04-09 11:30:09', '2026-04-11 10:52:34', 1),
(29, 'Deep Singh', 'deepsatvin@gmail.com', '01159776521', '$2y$10$DqK0qozu7KavfguwciUS8eBvyq7UyuR1KLTXaOrWLG2I1fGUUr4JO', '2026-04-10 10:41:16', NULL, 0),
(32, 'Deep Singh', 'sungai1414@gmail.com', '01159776521', '$2y$10$HXsRdzULpkLSePA8S5AaHeqsA17.ylQFOt96n3jO0NTKrLx2gff3e', '2026-04-10 10:48:40', NULL, 1),
(33, 'Ali', 'satvindeeps@gmail.com', '01256572134', '$2y$10$ooKj2JOJu6X3NIVVVH3Boeqmkei1BS2NOo73iobgbuitAB7L54XGa', '2026-04-10 11:54:23', NULL, 1),
(34, 'Akhiran Kumar', 'satvinven142114@gmail.com', '01254774412', '$2y$10$c60TgtQ.VmvnDezsHSCS6Opt3OEgHZ56TqsIfoSjc37oiPhDDdl/a', '2026-04-10 12:00:24', NULL, 1),
(35, 'Jaspreet Singh', 'jaspreetking29@gmail.com', '01352784632', '$2y$10$yB8BfJzn1qwaZQf4EfYlvu1nfhE/ivxo2k3qvTskwnmLZy4NmAP9W', '2026-04-10 23:18:03', '2026-05-09 10:36:17', 1),
(36, 'Amar', 'satlakh1421@gmail.com', '01123764281', '$2y$10$JNlrETqQBtc/v.46F21/ku0Cq89mnnTPyuz4W49vMD.5ylZWylwB2', '2026-04-10 23:33:15', '2026-04-10 23:33:15', 1),
(37, 'Kuala Lumpur', 'satvindeepsinghmand21@gmail.com', '01243212911', '$2y$10$qU9nfREzwblfUF5GTKABduLJiSJ1fyF0kHyWGtst5ynMabw2NYTde', '2026-04-10 23:41:24', '2026-04-10 23:41:55', 1),
(38, 'Gurdeep', 'satuvinsara142109@gmail.com', '012358162581', '$2y$10$gcM31BbhMOtYVfnQQNgDtuaA90bV0.Yrmj9vwXG6ryTp1CB4EGfA.', '2026-04-11 13:31:09', '2026-05-15 21:37:32', 1),
(39, 'Sidhu Kaur', 'ranjitkaursidhu19@gmail.com', '012-478 7572', '$2y$10$ayxkqHGWDedToDBHwiuRE.t5hRsocAAfJDmk6scmtTygtMVJ9PhaG', '2026-04-12 17:50:46', '2026-04-12 17:51:01', 1),
(40, 'HAZEEQ AFNAN BIN SHAHMI', 'afnanhazeeq@gmail.com', '017-5387480', '$2y$10$CJgQWVMVM351eCl7I40LSeCNEqM40glo0J4ua0OjyrGHmTaDe6vZG', '2026-04-13 10:38:29', '2026-04-13 10:38:44', 1),
(41, 'najwan', 'najwanfarhad17@gmail.com', '0194263968', '$2y$10$wvQezSdynbAp2crSUZCfO.l7qzgi3HYzFH/APkYiFnQ3Zzq8SQF16', '2026-04-13 10:55:08', '2026-04-13 10:56:18', 1),
(42, 'Baiti Adzlina', 'norbaiti.adzlina@gmail.com', '0195575161', '$2y$10$fy3KZfuGAtqgx0d4x/F0XO4bHkZiNJulCSOVunaOsJ2v3Rdhdg5d2', '2026-04-13 11:02:58', '2026-04-13 11:04:18', 1),
(43, 'Gokulan', 'gokulankupusamy@gmail.com', '0189482015', '$2y$10$CTAUNrE0UCsZsx1Z4l4YnutuzczgiXswT7OtK7FYe9OQOlvrxLYzC', '2026-04-13 11:20:49', '2026-04-13 11:20:49', 1),
(44, 'Ika', 'nurzulaikhazulkifly300@gmail.com', '60165790885', '$2y$10$5E/OVaWrJhfQyOO.rZPbVO89Q35F93nGqHNeLMV4fUXlRl2BJJIHe', '2026-04-13 11:44:15', '2026-04-13 11:45:04', 1),
(45, 'Lskhvinjit Kaur', 'projectfinalddt@gmail.com', '0124177853', '$2y$10$ndAbzhDKM0VISrgA/q1Yreejnr/.AxuTuisC0Hwlq0eB728H8TZKW', '2026-04-13 12:33:15', '2026-04-13 12:33:15', 1),
(46, 'Oscar', 'oscarhyx11@gmail.com', '0125110609', '$2y$10$l/2GRz.WsH12PPJP.Ob.dOIgc7A90nRWptn0wZnYCskn3dRb6nB3W', '2026-05-07 11:32:41', '2026-05-07 11:33:29', 1),
(47, 'Jaspreet kaur', 'jogindersinghmand74@gmail.com', '01159773344', '$2y$10$6VwjZntrlDCUJpCZ34fEgei2brNmy.LTeqYPqoo1.n/T//rEi2Wee', '2026-05-09 10:51:36', '2026-05-09 10:55:03', 1),
(48, 'Talvar Singh', 'satuuusingh21@gmail.com', '013411223312', '$2y$10$s574NiqK5M/Z7lix4k2NcuXfqppEwtUg8X5kgIo.aYMUvIl.Ds/lC', '2026-05-09 12:14:27', '2026-05-09 12:19:52', 1),
(49, 'Yf', 'yf.mybest@gmail.com', '0174922811', '$2y$10$ASn/smgfqUnJOVwPv7P3f.9.zgwireHnnT4l3KKa7vxrGLQ5G5blK', '2026-05-18 15:29:31', '2026-05-18 15:34:46', 1),
(50, 'Harpreet Kaur', 'gurdeep273737@gmail.com', '0169026475', '$2y$10$Bu979pu.m0px9ENK4rR88OTbrqfgeydqwHasOE54Zlr2ipIqvVwXm', '2026-06-22 17:16:48', '2026-06-22 17:20:26', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `capture_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `plate_no` varchar(20) DEFAULT NULL,
  `car_color` varchar(50) DEFAULT NULL,
  `car_type` varchar(50) DEFAULT NULL,
  `status` enum('PENDING','PAID','WASHING','DONE') DEFAULT 'PENDING',
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `capture_id`, `customer_id`, `service_id`, `plate_no`, `car_color`, `car_type`, `status`, `total_price`, `created_at`) VALUES
(7, 2, 3, 17, 'ESA 2114', 'Black', 'Bike', 'DONE', 250.00, '2026-02-11 13:55:08'),
(8, 4, 5, 10, 'RAN 7791', 'Silver', 'Car', 'DONE', 550.00, '2026-02-11 14:20:09'),
(9, 5, 3, 15, 'SAT 1421', 'Silver', 'Car', 'DONE', 1100.00, '2026-02-11 14:49:19'),
(10, 6, 1, 2, 'BAT 1122', 'Purple', 'SUV/MPV', 'DONE', 520.00, '2026-02-11 14:58:36'),
(11, 7, 5, 8, 'WQJ 3453', 'Blue', 'SUV/MPV', 'DONE', 800.00, '2026-02-11 15:11:48'),
(12, 8, 3, 4, 'RAN 7791', 'Black', 'Car', 'DONE', 500.00, '2026-02-11 15:31:41'),
(17, 1, 1, 18, 'ABC1234', 'Silver', 'SUV/MPV', 'DONE', 400.00, '2026-02-11 17:24:40'),
(18, 9, 1, 3, 'RAN 7791', 'Blue', 'Car', 'DONE', 240.00, '2026-02-11 18:24:54'),
(19, 10, 5, 10, 'JAS 5544', 'Purple', 'Car', 'DONE', 615.00, '2026-02-11 18:31:11'),
(20, 11, 3, 2, 'BAT 1122', 'Black', 'Car', 'DONE', 170.00, '2026-02-11 18:41:34'),
(21, 12, 7, 10, 'LIM 2114', 'Blue', 'Car', 'DONE', 590.00, '2026-02-11 18:51:37'),
(22, 13, 3, 11, 'BTS 4321', 'Blue', 'Car', 'DONE', 450.00, '2026-02-11 23:21:49'),
(23, 14, 1, 1, 'ESA 2114', 'Black', 'Car', 'PENDING', 595.00, '2026-02-11 23:41:00'),
(24, 15, 1, 16, 'LIM 2354', 'Blue', 'Bike', 'DONE', 125.00, '2026-02-11 23:47:17'),
(25, 16, 5, 4, 'BST 1243', 'Blue', 'SUV/MPV', 'DONE', 490.00, '2026-02-12 00:00:35'),
(26, 17, 2, 18, 'ASP 2143', 'Pink', 'Bike', 'DONE', 600.00, '2026-02-12 00:07:37'),
(27, 18, 3, 10, 'SAT 1421', 'Blue', 'Car', 'DONE', 350.00, '2026-02-12 10:22:39'),
(28, 19, 3, 12, 'LIM 6655', 'Green', 'Car', 'DONE', 1290.00, '2026-02-12 10:55:09'),
(29, 20, 1, 2, 'LAP 1122', 'Blue', 'Car', 'DONE', 350.00, '2026-02-13 11:20:46'),
(30, 21, 7, 10, 'WQJ 3453', 'Black', 'SUV/MPV', 'DONE', 720.00, '2026-02-13 11:24:34'),
(31, 22, 3, 9, 'SAT 1421', 'Black', 'Car', 'DONE', 520.00, '2026-02-13 16:09:46'),
(32, 23, 2, 9, 'XYZ 1029', 'Green', 'SUV/MPV', 'DONE', 250.00, '2026-02-13 16:51:54'),
(33, 24, 1, 16, 'TAL 2938', 'Black', 'Bike', 'DONE', 225.00, '2026-02-13 17:41:19'),
(34, 25, 3, 2, 'QWS 2136', 'Red', 'Car', 'DONE', 435.00, '2026-02-24 12:58:34'),
(35, 26, 1, 16, 'ZAW 3322', 'Yellow', 'Bike', 'PENDING', 35.00, '2026-02-25 15:23:58'),
(36, 27, 3, 1, 'TTL 1243', 'Blue', 'Car', 'PENDING', 140.00, '2026-02-25 15:37:54'),
(37, 28, 2, 2, 'WTR 6634', 'Silver', 'SUV/MPV', 'PENDING', 400.00, '2026-02-25 15:48:59'),
(38, 29, 3, 10, 'TTF 1029', 'Blue', 'Car', 'PENDING', 550.00, '2026-02-25 16:03:49'),
(39, 30, 1, 18, 'TTD 1243', 'Blue', 'Bike', 'PENDING', 490.00, '2026-02-25 16:13:16'),
(40, 31, 1, 2, 'PPT 2343', 'Black', 'SUV/MPV', 'PENDING', 200.00, '2026-02-25 16:23:11'),
(41, 32, 1, 18, 'MAT 2176', 'Black', 'SUV/MPV', 'PENDING', 400.00, '2026-02-25 16:34:38'),
(42, 33, 3, 2, 'PPL 2233', 'Silver', 'Car', 'PENDING', 370.00, '2026-02-25 16:44:18'),
(43, 34, 2, 10, 'WTF 2233', 'Pink', 'Car', 'PENDING', 440.00, '2026-02-25 16:56:06'),
(44, 35, 3, 16, 'TTR 4232', 'Red', 'Bike', 'DONE', 105.00, '2026-02-25 17:02:52'),
(45, 36, 1, 12, 'AAS 2267', 'Black', 'SUV/MPV', 'DONE', 1300.00, '2026-02-25 17:06:42'),
(46, 37, 1, 3, 'DAE 7732', 'Pink', 'Car', 'DONE', 240.00, '2026-02-25 17:17:54'),
(47, 38, 2, 12, 'QTR 6243', 'Purple', 'Car', 'DONE', 1570.00, '2026-02-25 17:25:00'),
(48, 39, 3, 2, 'DET 3251', 'Silver', 'Car', 'DONE', 360.00, '2026-02-25 22:55:36'),
(49, 40, 3, 1, 'QWR 6421', 'Purple', 'Car', 'DONE', 255.00, '2026-02-26 10:21:54'),
(50, 41, 8, 18, 'QAZ6655', 'Black', 'Car', 'DONE', 490.00, '2026-02-26 10:39:12'),
(51, 42, 3, 9, 'WWQ 2211', 'Blue', 'SUV/MPV', 'DONE', 250.00, '2026-02-26 10:59:29'),
(52, 43, 9, 2, 'TTP 9825', 'Brown', 'Car', 'DONE', 400.00, '2026-03-05 13:34:00'),
(53, 47, 10, 12, 'POT 2354', 'White', 'Car', 'DONE', 1770.00, '2026-03-05 19:29:48'),
(54, 48, 1, 3, 'TTB 2135', 'Black', 'Car', 'DONE', 240.00, '2026-03-08 15:34:38'),
(55, 51, 1, 2, 'QM9688E', 'Purple', 'Car', 'DONE', 270.00, '2026-03-08 21:15:30'),
(56, 52, 1, 10, 'MAH41', 'Blue', 'Car', 'DONE', 650.00, '2026-03-08 21:21:53'),
(57, 57, 1, 2, 'MAH41', 'Blue', 'Car', 'DONE', 250.00, '2026-03-10 14:40:34'),
(58, 100, 3, 2, 'WMT313', 'Silver', 'Car', 'DONE', 175.00, '2026-03-12 10:40:53'),
(59, 101, 11, 3, 'QM9686E', 'Pink', 'Car', 'DONE', 270.00, '2026-03-12 10:53:28'),
(60, 103, 11, 18, 'QM9686E', 'Blue', 'Bike', 'DONE', 400.00, '2026-03-12 11:28:56'),
(61, 102, 1, 10, 'MAH41', 'Blue', 'Car', 'DONE', 350.00, '2026-03-12 11:35:17'),
(62, 106, 13, 12, 'JFC2218', 'Silver', 'SUV/MPV', 'DONE', 1370.00, '2026-03-12 16:02:59'),
(63, 105, 3, 3, 'WMT313', 'Silver', 'Car', 'DONE', 180.00, '2026-03-12 17:06:21'),
(64, 107, 2, 18, 'PFQ5217', 'Purple', 'Bike', 'DONE', 560.00, '2026-03-12 17:21:00'),
(65, 108, 12, 3, 'WA4414P', 'Red', 'Car', 'DONE', 190.00, '2026-03-12 17:23:59'),
(66, 109, 3, 3, 'QM8888E', 'Blue', 'Car', 'DONE', 440.00, '2026-03-12 17:43:01'),
(67, 110, 3, 10, 'MAH41', 'Blue', 'Car', 'DONE', 440.00, '2026-03-12 17:53:16'),
(68, 111, 1, 3, 'WQA 6132', 'Silver', 'Car', 'DONE', 175.00, '2026-03-13 07:12:49'),
(69, 113, 3, 8, 'MAH41', 'Blue', 'Car', 'DONE', 775.00, '2026-03-13 07:16:37'),
(70, 114, 1, 5, 'TTK8866', 'Pink', 'SUV/MPV', 'DONE', 285.00, '2026-03-13 07:26:19'),
(71, 115, 3, 15, 'QQW3124', 'Black', 'Car', 'DONE', 905.00, '2026-03-13 15:32:14'),
(72, 116, 1, 10, 'RAN 7791', 'Blue', 'Car', 'DONE', 350.00, '2026-03-13 15:36:05'),
(73, 117, 3, 11, 'BAT 1122', 'Black', 'Car', 'DONE', 485.00, '2026-03-13 15:54:54'),
(74, 118, 14, 15, 'EDR5386', 'Chromatic Blue', 'Car', 'DONE', 1585.00, '2026-03-13 16:53:37'),
(75, 119, 1, 1, 'RAN 7791', 'Blue', 'Car', 'DONE', 55.00, '2026-03-15 13:29:04'),
(76, 120, 7, 1, 'SAT 1421', 'Black', 'Car', 'PENDING', 55.00, '2026-03-15 13:57:59'),
(77, 121, 7, 1, 'BAT 1122', 'Black', 'Car', 'PENDING', 55.00, '2026-03-15 14:27:35'),
(78, 122, 3, 1, 'ESA 2114', 'Black', 'Car', 'PENDING', 55.00, '2026-03-15 14:31:21'),
(79, 123, 15, 16, 'BBD 5321', 'Red', 'Bike', 'DONE', 35.00, '2026-03-15 14:51:33'),
(80, 160, 17, 16, 'WWW3228', 'White', 'Bike', 'PENDING', 105.00, '2026-03-16 16:46:11'),
(81, 166, 18, 1, 'WA4414P', 'Red', 'Car', 'DONE', 55.00, '2026-03-16 17:32:46'),
(82, 172, 1, 16, 'LAT6523', 'Silver', 'Bike', 'DONE', 35.00, '2026-03-19 11:22:43'),
(83, 173, 1, 10, 'RAN 7791', 'Blue', 'Car', 'DONE', 550.00, '2026-03-25 14:01:26'),
(84, 174, 1, 5, 'SAT 1421', 'Black', 'Car', 'DONE', 150.00, '2026-03-25 14:18:01'),
(85, 176, 22, 10, 'BAT 1122', 'Black', 'Car', 'DONE', 350.00, '2026-03-25 14:21:40'),
(86, 177, 1, 6, 'ESA 2114', 'Black', 'Car', 'DONE', 200.00, '2026-03-25 22:38:17'),
(87, 175, 23, 2, 'BAT 1122', 'Black', 'Car', 'DONE', 235.00, '2026-03-27 17:08:42'),
(88, 179, 24, 3, 'WB4089Q', 'Grey', 'Car', 'DONE', 230.00, '2026-03-29 16:31:43'),
(89, 184, 25, 2, 'RAN 7791', 'Blue', 'Car', 'DONE', 400.00, '2026-04-02 10:41:53'),
(90, 183, 25, 11, 'RAJ7791', 'Red', 'SUV/MPV', 'PAID', 350.00, '2026-04-02 10:54:19'),
(91, 188, 27, 16, 'SAT1421', 'Biru', 'Bike', 'DONE', 35.00, '2026-04-06 15:57:06'),
(92, 197, 28, 10, 'WQT6421', 'Purple', 'SUV/MPV', 'DONE', 635.00, '2026-04-09 11:52:27'),
(93, 198, 28, 3, 'TTW1029', 'Blue', 'Car', 'DONE', 140.00, '2026-04-09 20:36:41'),
(94, 199, 28, 3, 'TRW1029', 'Blue', 'Car', 'DONE', 240.00, '2026-04-10 19:33:21'),
(95, 201, 2, 3, 'RAN 7791', 'Blue', 'Car', 'PENDING', 240.00, '2026-04-10 21:31:23'),
(96, 200, 2, 2, 'WQA 6132', 'Silver', 'Car', 'PENDING', 190.00, '2026-04-10 22:05:17'),
(97, 202, 35, 9, 'ESA 2114', 'Black', 'Car', 'DONE', 350.00, '2026-04-10 23:43:22'),
(98, 203, 35, 3, 'TRQ3215', 'Blue', 'SUV/MPV', 'DONE', 280.00, '2026-04-10 23:46:32'),
(99, 204, 35, 3, 'RAJ 5534', 'Black', 'SUV/MPV', 'DONE', 180.00, '2026-04-11 01:00:56'),
(100, 205, 35, 3, 'RAN 7791', 'Black', 'SUV/MPV', 'DONE', 230.00, '2026-04-11 01:52:05'),
(101, 207, 35, 9, 'BAT 1122', 'Black', 'Car', 'DONE', 350.00, '2026-04-11 08:33:45'),
(102, 206, 35, 11, 'SAT 1421', 'Black', 'Car', 'DONE', 350.00, '2026-04-11 08:39:36'),
(103, 208, 28, 4, 'WQJ 3453', 'Black', 'SUV/MPV', 'DONE', 400.00, '2026-04-11 08:47:33'),
(104, 209, 35, 3, 'DAE 7732', 'Pink', 'Car', 'DONE', 390.00, '2026-04-11 09:04:29'),
(105, 214, 35, 4, 'SAT 1421', 'blue', 'SUV/MPV', 'DONE', 400.00, '2026-04-11 10:41:17'),
(106, 213, 35, 3, 'RAN 7791', 'Black', 'SUV/MPV', 'DONE', 190.00, '2026-04-11 12:33:24'),
(107, 215, 38, 1, 'SAT1421', 'Biru', 'Bike', 'DONE', 155.00, '2026-04-11 13:32:31'),
(108, 216, 35, 9, 'SAT1421', 'Biru', 'Bike', 'WASHING', 250.00, '2026-04-12 17:00:25'),
(109, 217, 39, 11, 'RAN 7791', 'Black', 'SUV/MPV', 'DONE', 450.00, '2026-04-12 17:51:10'),
(110, 220, 40, 2, 'SAT1421', 'Merah', 'Car', 'DONE', 170.00, '2026-04-13 10:39:28'),
(111, 219, 41, 17, 'SAT142', 'red', 'Bike', 'DONE', 220.00, '2026-04-13 10:56:56'),
(112, 225, 25, 11, 'DDY6669', 'Red', 'Car', 'DONE', 780.00, '2026-04-13 11:34:12'),
(113, 224, 44, 1, 'PDF 3477', 'Pink', 'Car', 'DONE', 125.00, '2026-04-13 11:45:44'),
(114, 227, 35, 7, 'SAT1422', 'Biru', 'SUV/MPV', 'DONE', 685.00, '2026-04-13 12:34:05'),
(115, 234, 8, 2, 'BLE553D', 'WHITE', 'Car', 'DONE', 140.00, '2026-04-16 15:00:23'),
(116, 235, 35, 2, 'TBS1243', 'Silver', 'SUV/MPV', 'DONE', 135.00, '2026-04-17 10:54:33'),
(117, 236, 35, 2, 'SAT 1421', 'blue', 'SUV/MPV', 'DONE', 200.00, '2026-04-27 11:23:13'),
(118, 237, 35, 3, 'WQR 5213', 'Silver', 'SUV/MPV', 'DONE', 430.00, '2026-04-27 11:27:34'),
(119, 240, 46, 2, 'PFQ5217', 'Purple', 'Bike', 'DONE', 170.00, '2026-05-07 11:34:08'),
(120, 239, 35, 16, 'PFQ5217', 'Purple', 'Bike', 'PENDING', 35.00, '2026-05-08 21:11:30'),
(121, 241, 47, 16, 'JAS4774', 'White', 'Bike', 'PENDING', 35.00, '2026-05-09 10:54:14'),
(122, 242, 48, 16, 'TTL4422', 'Brown', 'Bike', 'PENDING', 35.00, '2026-05-09 12:15:30'),
(123, 243, 38, 16, 'ZAQ2651', 'Orange', 'Bike', 'PAID', 35.00, '2026-05-15 21:33:29'),
(124, 244, 50, 1, 'PMM9181', 'Beige', 'Car', 'PENDING', 55.00, '2026-06-22 17:21:46');

-- --------------------------------------------------------

--
-- Table structure for table `order_addons`
--

CREATE TABLE `order_addons` (
  `order_id` int(11) DEFAULT NULL,
  `addon_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_addons`
--

INSERT INTO `order_addons` (`order_id`, `addon_id`) VALUES
(1, 3),
(1, 5),
(1, 4),
(1, 9),
(2, 2),
(3, 3),
(3, 5),
(3, 2),
(3, 8),
(3, 4),
(3, 9),
(4, 4),
(4, 9),
(6, 3),
(6, 5),
(6, 4),
(6, 9),
(7, 5),
(7, 1),
(7, 6),
(8, 4),
(8, 9),
(9, 3),
(9, 2),
(10, 3),
(10, 5),
(10, 8),
(10, 4),
(10, 9),
(11, 3),
(12, 3),
(12, 9),
(13, 1),
(14, 3),
(15, 5),
(16, 2),
(18, 3),
(19, 3),
(19, 1),
(19, 6),
(19, 7),
(20, 5),
(21, 3),
(21, 7),
(21, 9),
(22, 3),
(23, 3),
(23, 2),
(23, 7),
(23, 4),
(23, 9),
(24, 1),
(25, 3),
(25, 7),
(25, 8),
(26, 2),
(28, 7),
(28, 8),
(29, 3),
(29, 8),
(29, 9),
(30, 3),
(30, 5),
(30, 4),
(30, 9),
(31, 3),
(31, 5),
(31, 4),
(31, 9),
(32, 4),
(33, 3),
(33, 1),
(34, 3),
(34, 2),
(34, 6),
(36, 6),
(36, 8),
(37, 3),
(37, 4),
(37, 9),
(38, 3),
(38, 9),
(39, 1),
(40, 3),
(42, 3),
(42, 5),
(42, 9),
(43, 7),
(43, 8),
(44, 5),
(45, 3),
(46, 3),
(47, 3),
(47, 5),
(47, 4),
(47, 9),
(48, 5),
(48, 1),
(48, 9),
(49, 3),
(49, 9),
(50, 1),
(51, 3),
(52, 3),
(52, 4),
(52, 9),
(53, 3),
(53, 5),
(53, 2),
(53, 4),
(53, 9),
(54, 9),
(55, 3),
(55, 5),
(56, 3),
(56, 4),
(56, 9),
(57, 8),
(57, 4),
(58, 6),
(58, 7),
(59, 1),
(59, 7),
(62, 5),
(62, 9),
(63, 7),
(64, 5),
(64, 1),
(65, 8),
(66, 3),
(66, 4),
(66, 9),
(67, 7),
(67, 8),
(68, 6),
(69, 6),
(69, 7),
(70, 3),
(70, 6),
(71, 5),
(71, 6),
(73, 3),
(73, 6),
(74, 3),
(74, 5),
(74, 2),
(74, 1),
(74, 6),
(74, 7),
(74, 8),
(74, 4),
(74, 9),
(80, 5),
(83, 4),
(83, 9),
(87, 6),
(87, 4),
(88, 1),
(89, 5),
(89, 1),
(89, 7),
(89, 9),
(92, 2),
(92, 6),
(92, 8),
(94, 3),
(95, 3),
(96, 1),
(97, 4),
(97, 9),
(98, 1),
(98, 8),
(99, 7),
(100, 1),
(101, 3),
(101, 9),
(103, 3),
(104, 2),
(104, 8),
(105, 9),
(106, 8),
(107, 9),
(108, 3),
(109, 3),
(110, 5),
(111, 1),
(111, 6),
(111, 7),
(112, 2),
(112, 1),
(112, 7),
(112, 9),
(113, 5),
(114, 5),
(114, 6),
(114, 9),
(115, 7),
(116, 6),
(117, 4),
(118, 2),
(118, 1),
(119, 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_type`, `user_id`, `token_hash`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 'customer', 7, '50dfe6a05f31c881481838f084df54e3014cbff23e54a750d9e30b299f1fbceb', '2026-02-13 08:03:52', NULL, '2026-02-13 06:03:52'),
(2, 'customer', 7, 'efe19782ecb3d62a772b328a717e1d3fe91eaaed23f27dd1515e63eb5b52cb77', '2026-02-13 08:04:44', '2026-02-13 07:05:11', '2026-02-13 06:04:44'),
(3, 'admin', 1, '4de53f2903a15211b03e8ab3f4c97b57d5fb179759126c39a2d4fc0510628683', '2026-02-13 08:06:04', '2026-02-13 07:06:24', '2026-02-13 06:06:04'),
(4, 'admin', 1, 'edda4a068ce1611ee388cd5e5023af57cc2425f3da04e6edfd053188a100a959', '2026-02-13 08:06:41', '2026-02-13 07:06:57', '2026-02-13 06:06:41'),
(5, 'admin', 1, '5fbbef6691d405f106e2647435b5afb404c104b13ee0f128222cedf377584016', '2026-02-13 10:21:25', NULL, '2026-02-13 08:21:25'),
(6, 'customer', 8, '41e5bf830cb345256fff876226375bc3414cf42375990e1c878400a9864f9504', '2026-02-26 04:54:40', '2026-02-26 03:55:25', '2026-02-26 02:54:40'),
(7, 'customer', 7, '18b05cd07ee103506d6bc1082806f57fe0f9fca3a55b75441871ae480ac13b22', '2026-03-13 18:07:57', NULL, '2026-03-13 09:07:57'),
(8, 'customer', 7, 'a55e9ca4ec9c28a6df686fb83ea095dccf05dd6fca95f2111f15ad7085e30e21', '2026-03-13 18:09:18', NULL, '2026-03-13 09:09:18'),
(9, 'customer', 7, 'dc160225f56028dc7160ca814f22d69a9b9db9382cb2f97c62850cc119f6a568', '2026-03-13 19:06:52', '2026-03-13 18:07:24', '2026-03-13 10:06:52'),
(10, 'customer', 7, 'ef14ff427cfd628639e61d86ba8b3e9808d5d0d9439928fae984fd28c3a661b0', '2026-03-14 15:34:08', '2026-03-14 14:34:28', '2026-03-14 06:34:08'),
(11, 'admin', 1, '7cbbf2a2d79170bdd3c576e87a5ee6178c75ef27aecd41eb7f6cff6777fa428a', '2026-03-15 15:55:54', NULL, '2026-03-15 06:55:54'),
(12, 'admin', 1, '2efb451e21111753a08549577a2ecaf09c6571e5e40b0ccf6d305cd5c42d3187', '2026-03-15 16:04:20', NULL, '2026-03-15 07:04:20'),
(13, 'admin', 1, '7f69217f507194c8843b524a268e5072a2802026772998663bd303d5207692f2', '2026-03-15 16:09:20', '2026-03-15 15:10:27', '2026-03-15 07:09:20'),
(14, 'customer', 7, '72effb8feb3688637597607ab5b3a5b2beb0d9f19d8ebe055317034ab9338d08', '2026-04-10 13:27:56', NULL, '2026-04-10 04:27:56'),
(15, 'customer', 3, '7ae9962aa07341b5eb51b2ca5eacc3c5769496b1577e0789c6f3fccec333e6df', '2026-04-10 13:30:43', NULL, '2026-04-10 04:30:43'),
(16, 'customer', 38, 'ed53bf8910fcb7d6296471513ea551998d65add2d606e438fa9121bdae825e27', '2026-04-11 14:33:55', '2026-04-11 13:34:54', '2026-04-11 05:33:55'),
(17, 'admin', 1, 'f5e58ae042e0bd5cebc3f2cbe90bf3ff5e0e78720da7979ed44111db2fc2a076', '2026-04-11 18:13:25', NULL, '2026-04-11 09:13:25'),
(18, 'customer', 25, '22493bc2bc8dd6b2ba18c11fe2ef078924e96aeb6ad105987aa950bfd08052ee', '2026-04-13 13:38:10', NULL, '2026-04-13 04:38:10'),
(19, 'customer', 8, '85834642c250511cacd5690aa30491ec4ad35bd1acc5f77b9089bdc71656d482', '2026-04-16 15:58:03', '2026-04-16 14:58:31', '2026-04-16 06:58:03'),
(20, 'admin', 1, 'e208e27e0e4ccb1ecde37c6c2fac623421f1f96e1286ed916bab12e1372b2389', '2026-04-16 16:05:17', NULL, '2026-04-16 07:05:17'),
(21, 'admin', 1, 'bf06ffc0feb4b6a159a982927badf5529f980801e81bdaf417291919aa58cf4f', '2026-05-07 12:37:28', NULL, '2026-05-07 03:37:28'),
(22, 'admin', 1, '19f03daccda76c1fa3c970c934fa453fad1412ecf56c6133411eb937b2391e44', '2026-05-07 12:37:31', NULL, '2026-05-07 03:37:31'),
(23, 'customer', 49, '2cae89a73307a5e28e33b41c063838afd7052a06785a42ef9bad7ba1784f7aa4', '2026-05-18 16:31:12', '2026-05-18 15:31:38', '2026-05-18 07:31:12'),
(24, 'customer', 49, 'd3d202ee41ab0bced1d507634480900984192e780afa67becfe84fefdff76933', '2026-05-18 16:33:12', '2026-05-18 15:33:47', '2026-05-18 07:33:12');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `txn_ref` varchar(100) DEFAULT NULL,
  `billcode` varchar(50) DEFAULT NULL,
  `gateway` varchar(30) DEFAULT NULL,
  `raw_response` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `amount`, `payment_status`, `paid_at`, `txn_ref`, `billcode`, `gateway`, `raw_response`, `created_at`) VALUES
(1, 2, 600.00, 'SUCCESS', '2026-02-10 15:09:12', 'SIM-DDB50E5D', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(2, 3, 1320.00, 'SUCCESS', '2026-02-10 15:10:45', 'SIM-81321A40', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(3, 5, 255.00, 'SUCCESS', '2026-02-10 15:26:59', 'SIM-B8793A89', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(4, 6, 1070.00, 'SUCCESS', '2026-02-11 05:38:09', 'SIM-29708E0B', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(5, 7, 250.00, 'SUCCESS', '2026-02-11 06:55:09', 'SIM-1C01D677', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(6, 8, 550.00, 'SUCCESS', '2026-02-11 07:20:10', 'SIM-C11432C3', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(7, 9, 1100.00, 'SUCCESS', '2026-02-11 07:49:21', 'SIM-27D4D2DD', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(8, 10, 520.00, 'SUCCESS', '2026-02-11 07:58:38', 'SIM-038BE08E', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(9, 11, 800.00, 'SUCCESS', '2026-02-11 08:11:49', 'SIM-22D628FB', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(10, 12, 240.00, 'SUCCESS', '2026-02-11 08:31:45', 'SIM-67D65DCE', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(11, 13, 490.00, 'SUCCESS', '2026-02-11 08:50:26', 'SIM-6D3718AF', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(12, 14, 900.00, 'SUCCESS', '2026-02-11 08:52:10', 'SIM-67C15775', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(13, 15, 470.00, 'SUCCESS', '2026-02-11 08:58:19', 'SIM-E4200DB1', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(14, 16, 235.00, 'SUCCESS', '2026-02-11 09:17:14', 'SIM-7C36F350', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(15, 17, 400.00, 'SUCCESS', '2026-02-11 10:24:41', 'SIM-6AA0B68B', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(16, 18, 240.00, 'SUCCESS', '2026-02-11 11:24:55', 'SIM-20B18FAC', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(17, 19, 450.00, 'SUCCESS', '2026-02-11 11:33:33', 'SIM-B772C77C', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(18, 20, 170.00, 'SUCCESS', '2026-02-11 11:41:37', 'SIM-AC94DBD2', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(19, 21, 590.00, 'SUCCESS', '2026-02-11 11:52:44', 'SIM-0F077191', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(20, 22, 450.00, 'SUCCESS', '2026-02-11 16:22:40', 'SIM-9D59809D', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(21, 24, 125.00, 'SUCCESS', '2026-02-11 16:47:43', 'SIM-DE1DE2FE', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(22, 25, 490.00, 'SUCCESS', '2026-02-11 17:02:30', 'SIM-56EB90CE', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(23, 26, 600.00, 'SUCCESS', '2026-02-11 17:09:09', 'SIM-F056AD8C', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(24, 27, 290.00, 'SUCCESS', '2026-02-12 03:22:49', 'SIM-2D1D0756', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(25, 28, 1290.00, 'SUCCESS', '2026-02-12 04:14:07', 'SIM-B48012B5', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(26, 29, 350.00, 'SUCCESS', '2026-02-13 04:20:47', 'SIM-62AA2149', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(27, 30, 720.00, 'SUCCESS', '2026-02-13 04:24:35', 'SIM-AD922084', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(28, 31, 1620.00, 'SUCCESS', '2026-02-13 09:10:27', 'SIM-A3AD8F20', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(29, 32, 250.00, 'SUCCESS', '2026-02-13 09:51:56', 'SIM-A938C449', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(30, 33, 225.00, 'SUCCESS', '2026-02-13 10:41:20', 'SIM-C39E6470', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(31, 34, 435.00, 'SUCCESS', '2026-02-24 05:58:36', 'SIM-2DC20F52', NULL, NULL, NULL, '2026-05-09 02:45:53'),
(32, 35, 35.00, 'PENDING', NULL, NULL, 'dmzz7uu1', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(33, 36, 140.00, 'PENDING', NULL, NULL, 'p31mb6k3', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(34, 37, 400.00, 'PENDING', NULL, NULL, '2zg51bym', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(35, 38, 550.00, 'PENDING', NULL, NULL, 'lt0vjb7e', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(36, 39, 490.00, 'PENDING', NULL, NULL, 'lvxcreqd', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(37, 40, 200.00, 'PENDING', NULL, NULL, 'l6z60sp3', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(38, 41, 400.00, 'PENDING', NULL, NULL, 'v9m1nks2', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(39, 42, 370.00, 'PENDING', NULL, NULL, '2a6cpie5', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(40, 43, 440.00, 'PENDING', NULL, NULL, 'nkds0aun', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(41, 44, 105.00, 'SUCCESS', '2026-02-25 17:03:09', 'TP2602251292252183', 'cjamck4c', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(42, 45, 1300.00, 'SUCCESS', '2026-02-25 17:06:57', 'TP2602254290752126', 'j2pw0qzv', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(43, 46, 240.00, 'SUCCESS', '2026-02-25 17:18:33', 'TP2602252787352863', 'xyt81vbe', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(44, 47, 1570.00, 'SUCCESS', '2026-02-25 17:25:14', 'TP2602254824871608', 'v2eogoh1', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(45, 48, 360.00, 'SUCCESS', '2026-02-25 22:56:02', 'TP2602250247212434', 'vhfar0y8', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(46, 49, 255.00, 'SUCCESS', '2026-02-26 10:22:28', 'TP2602263425244592', 'n24bz1xx', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(47, 50, 490.00, 'SUCCESS', '2026-02-26 10:39:41', 'TP2602262837242145', '454ei6p9', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(48, 51, 250.00, 'SUCCESS', '2026-02-26 10:59:51', 'TP2602264662006717', 'got384e5', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(49, 52, 400.00, 'SUCCESS', '2026-03-05 13:34:16', 'TP2603052189559509', 'l39v8nj6', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(50, 53, 1770.00, 'SUCCESS', '2026-03-05 19:30:08', 'TP2603054284905594', '4jqfnciv', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(51, 54, 240.00, 'SUCCESS', '2026-03-08 15:34:52', 'TP2603081719836805', '5udfx590', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(52, 55, 270.00, 'SUCCESS', '2026-03-08 21:15:44', 'TP2603084094774147', 'tu58omyk', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(53, 56, 650.00, 'SUCCESS', '2026-03-08 21:22:06', 'TP2603081383677277', 'xzlgsztb', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(54, 57, 250.00, 'SUCCESS', '2026-03-10 14:40:49', 'TP2603104614240270', 'ryjn13h2', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(55, 58, 175.00, 'SUCCESS', '2026-03-12 10:41:12', 'TP2603122571642763', 'pfljix8j', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(56, 59, 320.00, 'SUCCESS', '2026-03-12 10:53:43', 'TP2603123200962283', 'sf1qjckk', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(57, 60, 200.00, 'SUCCESS', '2026-03-12 11:29:37', 'TP2603122664986884', 'ys6pg6lg', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(58, 61, 350.00, 'SUCCESS', '2026-03-12 16:50:26', 'TP2603122823248449', 'lxtl61ie', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(59, 62, 1370.00, 'SUCCESS', '2026-03-12 16:03:13', 'TP2603124288875483', 'n3de3nug', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(60, 63, 180.00, 'SUCCESS', '2026-03-12 17:06:52', 'TP2603121544728558', '9ayde0xz', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(61, 64, 560.00, 'SUCCESS', '2026-03-12 17:21:13', 'TP2603121174366978', 'cfh4mjdm', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(62, 65, 190.00, 'SUCCESS', '2026-03-12 17:24:13', 'TP2603120089248848', 'ma48hyzj', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(63, 66, 440.00, 'SUCCESS', '2026-03-12 17:43:41', 'TP2603123020726385', 'xx1j55pq', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(64, 67, 440.00, 'SUCCESS', '2026-03-12 17:53:27', 'TP2603120535266065', 'pi7gfbac', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(65, 68, 175.00, 'SUCCESS', '2026-03-13 07:13:01', 'TP2603130388830598', 'nja7d46m', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(66, 69, 775.00, 'SUCCESS', '2026-03-13 07:17:18', 'TP2603132600883419', '2b07h1dg', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(67, 70, 285.00, 'SUCCESS', '2026-03-13 07:26:30', 'TP2603134056326363', 'ttlyndeo', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(68, 71, 905.00, 'SUCCESS', '2026-03-13 15:32:30', 'TP2603131182379592', 'r8ogvz82', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(69, 72, 350.00, 'SUCCESS', '2026-03-13 15:36:52', 'TP2603130614478608', '3mskocnw', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(70, 73, 485.00, 'SUCCESS', '2026-03-13 15:55:16', 'TP2603134093656181', 'gkc1t553', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(71, 74, 1585.00, 'SUCCESS', '2026-03-13 16:54:20', 'TP2603131031153737', 'gp03m0pg', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(72, 75, 55.00, 'SUCCESS', '2026-03-15 13:38:57', 'TP2603153728356518', '1l2sn1dv', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(73, 76, 55.00, 'PENDING', NULL, NULL, '3gigs2bi', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(74, 77, 55.00, 'PENDING', NULL, NULL, 'qpp66vti', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(75, 78, 55.00, 'PENDING', NULL, NULL, 'tu5wffis', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(76, 79, 35.00, 'SUCCESS', '2026-03-15 14:53:04', 'TP2603154366424945', 'mdb7k4mw', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(77, 80, 105.00, 'PENDING', NULL, NULL, '9wks9xdz', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(78, 81, 55.00, 'SUCCESS', '2026-03-16 17:37:44', 'TP2603164186816883', 'dlstk4nz', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(79, 82, 35.00, 'SUCCESS', '2026-03-19 11:33:33', 'TP2603194145847079', 'gt2l1ivw', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(80, 83, 550.00, 'SUCCESS', '2026-03-25 14:07:26', 'TP2603253657615735', 'z1e3aigq', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(81, 84, 150.00, 'SUCCESS', '2026-03-25 14:18:11', 'TP2603253312670138', 'xhjx29vn', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(82, 85, 350.00, 'SUCCESS', '2026-03-25 14:21:51', 'TP2603252262230191', 'f7nl0fli', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(83, 86, 200.00, 'SUCCESS', '2026-03-25 22:38:28', 'TP2603254184481832', 'j4i6sw91', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(84, 87, 235.00, 'SUCCESS', '2026-03-27 17:08:56', 'TP2603271337633257', 'le478bly', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(85, 88, 230.00, 'SUCCESS', '2026-03-29 16:37:50', 'TP2603293475892082', 'tbtbm7m2', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(86, 89, 400.00, 'SUCCESS', '2026-04-02 10:47:27', 'TP2604024445505154', '09h9221n', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(87, 90, 350.00, 'SUCCESS', '2026-04-13 08:05:30', 'TP2604130580966497', 'qrpyewig', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(88, 91, 35.00, 'SUCCESS', '2026-04-06 15:57:42', 'TP2604064627915846', 'fql8k2t8', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(89, 92, 635.00, 'SUCCESS', '2026-04-09 11:54:37', 'TP2604091674316482', '592qoyg5', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(90, 93, 140.00, 'SUCCESS', '2026-04-09 20:36:52', 'TP2604090610520916', '3aa1hn77', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(91, 94, 240.00, 'SUCCESS', '2026-04-10 19:33:38', 'TP2604104611327078', 'ya7rinpb', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(92, 97, 350.00, 'SUCCESS', '2026-04-10 23:43:40', 'TP2604102426407210', 'yv2bry4j', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(93, 98, 280.00, 'SUCCESS', '2026-04-10 23:49:50', 'TP2604100428156493', 'dnu5uig7', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(94, 99, 180.00, 'SUCCESS', '2026-04-11 01:12:08', 'TP2604111675655489', 'rkpwywtr', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(95, 100, 230.00, 'SUCCESS', '2026-04-11 01:52:17', 'TP2604110671635571', 'gcqnl8n2', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(96, 101, 350.00, 'SUCCESS', '2026-04-11 08:33:58', 'TP2604110116746529', 'eou4mq9c', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(97, 102, 350.00, 'SUCCESS', '2026-04-11 08:39:49', 'TP2604114123859673', 'oxp4uny5', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(98, 103, 400.00, 'SUCCESS', '2026-04-11 08:50:44', 'TP2604114357813123', 'q96ai5tr', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(99, 104, 390.00, 'SUCCESS', '2026-04-11 09:04:41', 'TP2604113767190673', '5btww046', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(100, 105, 400.00, 'SUCCESS', '2026-04-11 10:41:29', 'TP2604112120719586', 'evgojkjs', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(101, 106, 190.00, 'SUCCESS', '2026-04-11 12:33:42', 'TP2604114897274752', 'uxi52dpy', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(102, 107, 155.00, 'SUCCESS', '2026-04-11 13:33:01', 'TP2604111831944620', 'dpct13e9', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(103, 108, 250.00, 'SUCCESS', '2026-04-12 17:00:50', 'TP2604124726473914', '8tur3t1a', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(104, 109, 450.00, 'SUCCESS', '2026-04-12 17:51:28', 'TP2604120695853287', 'sgkc3wjw', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(105, 110, 235.00, 'SUCCESS', '2026-04-13 10:40:22', 'TP2604130529131802', 'gfwgpt45', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(106, 111, 220.00, 'SUCCESS', '2026-04-13 10:57:25', 'TP2604131945099187', 'lgzxhzl5', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(107, 112, 780.00, 'SUCCESS', '2026-04-13 11:35:28', 'TP2604133287439028', 'wvp26aye', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(108, 113, 125.00, 'SUCCESS', '2026-04-13 11:46:13', 'TP2604133827418782', '4z1zfo8l', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(109, 114, 685.00, 'SUCCESS', '2026-04-13 12:35:02', 'TP2604134721132532', 'cy1ed3oz', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(110, 115, 140.00, 'SUCCESS', '2026-04-16 15:02:28', 'TP2604160703304930', 'tgufhhr1', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(111, 116, 135.00, 'SUCCESS', '2026-04-17 10:54:45', 'TP2604172436019357', '31l4mkuw', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(112, 117, 200.00, 'SUCCESS', '2026-04-27 11:23:25', 'TP2604272518910499', '3t7bpgzt', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(113, 118, 430.00, 'SUCCESS', '2026-04-27 11:30:04', 'TP2604273885695053', 'tawe01s8', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(114, 119, 170.00, 'SUCCESS', '2026-05-07 11:34:46', 'TP2605071218596765', 'v0n3u6id', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(115, 120, 35.00, 'PENDING', NULL, NULL, 'efwyvsu3', 'TOYYIBPAY', NULL, '2026-05-09 02:45:53'),
(116, 121, 35.00, 'PENDING', NULL, NULL, 'byv03djm', 'TOYYIBPAY', NULL, '2026-05-09 02:54:15'),
(117, 122, 35.00, 'ABANDONED', NULL, NULL, 'p5yyqyr8', 'TOYYIBPAY', NULL, '2026-05-09 04:15:31'),
(118, 122, 35.00, 'ABANDONED', NULL, NULL, 'nocxht2i', 'TOYYIBPAY', NULL, '2026-05-09 04:18:52'),
(119, 122, 35.00, 'PENDING', NULL, NULL, '3oakbakj', 'TOYYIBPAY', NULL, '2026-05-09 04:19:53'),
(120, 123, 35.00, 'ABANDONED', NULL, NULL, 'ew5peej4', 'TOYYIBPAY', NULL, '2026-05-15 13:33:30'),
(121, 123, 35.00, 'ABANDONED', NULL, NULL, '90qxdf9k', 'TOYYIBPAY', NULL, '2026-05-15 13:34:31'),
(122, 123, 35.00, 'SUCCESS', '2026-05-15 21:38:59', 'TP2605151413467724', '3zpeia5x', 'TOYYIBPAY', NULL, '2026-05-15 13:37:33'),
(123, 124, 55.00, 'PENDING', NULL, NULL, 'f0c4efa4', 'TOYYIBPAY', NULL, '2026-06-22 09:21:47');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'Car Wash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `price`, `category`) VALUES
(1, 'Premium Wash', 55.00, 'Car Wash Packages'),
(2, 'Ceramic Wash (Includes Ceramic Body Sealant)', 100.00, 'Car Wash Packages'),
(3, 'Exclusive Wash (UV Dressing + Nano Mist + Ion Coating)', 140.00, 'Car Wash Packages'),
(4, 'Cushion Wash', 300.00, 'Interior Services'),
(5, 'Leather Wash', 150.00, 'Interior Services'),
(6, 'Minor Interior Detailing', 200.00, 'Interior Services'),
(7, 'Major Interior Detailing (Remove Seat)', 480.00, 'Interior Services'),
(8, 'Complete Interior Detailing (Remove Seat)', 700.00, 'Interior Services'),
(9, 'Body Wax + RainX', 150.00, 'Exterior / Coating'),
(10, 'Body Sealant', 350.00, 'Exterior / Coating'),
(11, 'Glass Coating', 350.00, 'Exterior / Coating'),
(12, 'Body Coating', 1200.00, 'Exterior / Coating'),
(13, 'Polish Essential', 350.00, 'Polishing Packages'),
(14, 'Polish Advanced', 480.00, 'Polishing Packages'),
(15, 'Polish Elite', 800.00, 'Polishing Packages'),
(16, 'Bike: Ceramic Wash', 35.00, 'Bike Services'),
(17, 'Bike: Premium Wash', 55.00, 'Bike Services'),
(18, 'Bike: Ceramic Coating', 400.00, 'Bike Services');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('STAFF','ADMIN') DEFAULT 'STAFF'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `username`, `full_name`, `password_hash`, `role`) VALUES
(1, 'staff1', 'Test Staff', '$2y$10$uNBgMjzKEgyk1UEw1iqBGOgydALfMX/DOZtjiSFG49g6WS/fN7tp6', 'STAFF');

-- --------------------------------------------------------

--
-- Table structure for table `staff_logs`
--

CREATE TABLE `staff_logs` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `staff_username_snapshot` varchar(100) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `last_seen` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_logs`
--

INSERT INTO `staff_logs` (`id`, `staff_id`, `staff_username_snapshot`, `login_time`, `logout_time`, `last_seen`) VALUES
(1, 1, NULL, '2026-02-10 15:23:20', NULL, NULL),
(2, 1, NULL, '2026-02-10 15:23:45', NULL, NULL),
(3, 1, NULL, '2026-02-10 15:27:22', NULL, NULL),
(4, 1, NULL, '2026-02-11 05:38:32', NULL, NULL),
(5, 1, NULL, '2026-02-11 06:48:34', NULL, NULL),
(6, 1, NULL, '2026-02-11 06:51:08', NULL, NULL),
(7, 1, NULL, '2026-02-11 07:08:15', NULL, NULL),
(8, 1, NULL, '2026-02-11 07:15:29', NULL, NULL),
(9, 1, NULL, '2026-02-11 07:20:36', NULL, NULL),
(10, 1, NULL, '2026-02-11 07:48:13', NULL, NULL),
(11, 1, NULL, '2026-02-11 07:50:00', NULL, NULL),
(12, 1, NULL, '2026-02-11 07:57:41', NULL, NULL),
(13, 1, NULL, '2026-02-11 08:03:21', NULL, NULL),
(14, 1, NULL, '2026-02-11 08:09:46', NULL, NULL),
(15, 1, NULL, '2026-02-11 08:12:16', NULL, NULL),
(16, 1, NULL, '2026-02-11 08:30:13', NULL, NULL),
(17, 1, NULL, '2026-02-11 08:33:53', NULL, NULL),
(18, 1, NULL, '2026-02-11 09:01:00', NULL, NULL),
(19, 1, NULL, '2026-02-11 09:15:02', NULL, NULL),
(20, 1, NULL, '2026-02-11 10:29:11', NULL, NULL),
(21, 1, NULL, '2026-02-11 10:39:58', NULL, NULL),
(22, 1, NULL, '2026-02-11 10:41:51', NULL, NULL),
(23, 1, NULL, '2026-02-11 11:20:19', NULL, NULL),
(24, 1, NULL, '2026-02-11 11:24:05', NULL, NULL),
(25, 1, NULL, '2026-02-11 11:25:21', NULL, NULL),
(26, 1, NULL, '2026-02-11 11:26:31', NULL, NULL),
(27, 1, NULL, '2026-02-11 11:28:13', NULL, NULL),
(28, 1, NULL, '2026-02-11 11:34:20', NULL, NULL),
(29, 1, NULL, '2026-02-11 11:39:31', NULL, NULL),
(30, 1, NULL, '2026-02-11 11:40:27', NULL, NULL),
(31, 1, NULL, '2026-02-11 11:42:24', NULL, NULL),
(32, 1, NULL, '2026-02-11 11:48:28', NULL, NULL),
(33, 1, NULL, '2026-02-11 11:49:20', NULL, NULL),
(34, 1, NULL, '2026-02-11 11:50:11', NULL, NULL),
(35, 1, NULL, '2026-02-11 11:53:28', NULL, NULL),
(36, 1, NULL, '2026-02-11 11:54:32', NULL, NULL),
(37, 1, NULL, '2026-02-11 12:32:05', NULL, NULL),
(38, 1, NULL, '2026-02-11 15:22:30', NULL, NULL),
(39, 1, NULL, '2026-02-11 15:31:23', NULL, NULL),
(40, 1, NULL, '2026-02-11 15:32:06', '2026-02-11 16:04:39', NULL),
(41, 1, NULL, '2026-02-11 16:05:07', NULL, NULL),
(42, 1, NULL, '2026-02-11 16:25:23', NULL, NULL),
(43, 1, NULL, '2026-02-11 16:46:13', NULL, NULL),
(44, 1, NULL, '2026-02-11 16:48:04', NULL, NULL),
(45, 1, NULL, '2026-02-11 16:49:32', NULL, NULL),
(46, 1, NULL, '2026-02-11 16:50:31', NULL, NULL),
(47, 1, NULL, '2026-02-11 16:59:27', NULL, NULL),
(48, 1, NULL, '2026-02-11 17:03:02', NULL, NULL),
(49, 1, NULL, '2026-02-11 17:04:57', NULL, NULL),
(50, 1, NULL, '2026-02-11 17:05:41', NULL, NULL),
(51, 1, NULL, '2026-02-11 17:09:24', NULL, NULL),
(52, 1, NULL, '2026-02-11 17:10:50', NULL, NULL),
(53, 1, NULL, '2026-02-11 17:12:11', NULL, NULL),
(54, 1, NULL, '2026-02-12 03:20:12', NULL, NULL),
(55, 1, NULL, '2026-02-12 03:23:23', NULL, NULL),
(56, 1, NULL, '2026-02-12 03:37:39', NULL, NULL),
(57, 1, NULL, '2026-02-12 04:30:33', NULL, NULL),
(58, 1, NULL, '2026-02-13 04:14:43', NULL, NULL),
(59, 1, NULL, '2026-02-13 04:21:00', NULL, NULL),
(60, 1, NULL, '2026-02-13 04:24:47', '2026-02-13 04:57:42', NULL),
(61, 1, NULL, '2026-02-13 04:58:48', '2026-02-13 04:59:02', NULL),
(62, 1, NULL, '2026-02-13 05:46:03', '2026-02-13 05:46:12', '2026-02-12 21:46:11'),
(63, 1, NULL, '2026-02-13 05:46:44', '2026-02-13 05:46:48', '2026-02-12 21:46:44'),
(64, 1, NULL, '2026-02-13 05:48:54', NULL, '2026-02-12 21:49:04'),
(65, 1, NULL, '2026-02-13 05:49:09', '2026-02-13 05:49:14', '2026-02-12 21:49:09'),
(66, 1, NULL, '2026-02-13 06:05:21', '2026-02-13 06:05:27', '2026-02-12 22:05:27'),
(67, 1, NULL, '2026-02-13 06:05:33', NULL, '2026-02-12 22:05:33'),
(68, 1, NULL, '2026-02-13 06:06:51', NULL, '2026-02-12 22:06:51'),
(69, NULL, 'satvindeep singh', '2026-02-13 06:28:55', '2026-02-13 06:29:01', '2026-02-12 22:29:01'),
(70, NULL, 'sara', '2026-02-13 06:29:11', NULL, '2026-02-12 22:29:11'),
(71, NULL, 'satvindeep singh', '2026-02-13 06:35:22', NULL, '2026-02-12 22:35:27'),
(72, 1, NULL, '2026-02-13 09:04:05', NULL, '2026-02-13 01:05:19'),
(73, 1, NULL, '2026-02-13 09:12:41', NULL, '2026-02-13 01:15:00'),
(74, 1, NULL, '2026-02-13 09:50:42', NULL, '2026-02-13 01:51:02'),
(75, 1, NULL, '2026-02-13 09:52:09', NULL, '2026-02-13 01:53:14'),
(76, 1, NULL, '2026-02-13 09:56:20', NULL, '2026-02-13 02:05:12'),
(77, 1, NULL, '2026-02-13 10:05:35', NULL, '2026-02-13 02:11:52'),
(78, 1, NULL, '2026-02-13 10:18:38', NULL, '2026-02-13 02:40:38'),
(79, 1, NULL, '2026-02-13 10:41:33', NULL, '2026-02-13 02:42:30'),
(80, 1, NULL, '2026-02-13 10:44:14', '2026-02-13 10:44:24', '2026-02-13 02:44:24'),
(81, 1, NULL, '2026-02-24 05:57:44', NULL, '2026-02-23 21:58:54'),
(82, 1, NULL, '2026-02-24 05:59:14', NULL, '2026-02-23 21:59:36'),
(83, 1, NULL, '2026-02-24 05:59:47', '2026-02-24 06:00:17', '2026-02-23 22:00:17'),
(84, 1, NULL, '2026-02-25 08:22:25', NULL, '2026-02-25 00:37:33'),
(85, 1, NULL, '2026-02-25 08:48:08', NULL, '2026-02-25 00:50:48'),
(86, 1, NULL, '2026-02-25 08:52:21', NULL, '2026-02-25 01:02:27'),
(87, 1, NULL, '2026-02-25 09:02:50', NULL, '2026-02-25 01:03:00'),
(88, 1, NULL, '2026-02-25 09:04:56', NULL, '2026-02-25 01:12:41'),
(89, 1, NULL, '2026-02-25 09:13:47', NULL, '2026-02-25 01:22:51'),
(90, 1, NULL, '2026-02-25 09:23:39', NULL, '2026-02-25 01:33:31'),
(91, 1, NULL, '2026-02-25 09:33:49', NULL, '2026-02-25 01:33:59'),
(92, 1, NULL, '2026-02-25 09:43:30', NULL, '2026-02-25 01:55:13'),
(93, 1, NULL, '2026-02-25 09:55:24', NULL, '2026-02-25 02:01:45'),
(94, 1, NULL, '2026-02-25 10:01:56', '2026-02-25 10:01:59', '2026-02-25 02:01:59'),
(95, 1, NULL, '2026-02-25 10:02:15', NULL, '2026-02-25 02:05:53'),
(96, 1, NULL, '2026-02-25 10:06:08', NULL, '2026-02-25 02:17:20'),
(97, 1, NULL, '2026-02-25 10:18:45', NULL, '2026-02-25 02:18:55'),
(98, 1, NULL, '2026-02-25 10:24:16', NULL, '2026-02-25 02:25:42'),
(99, 1, NULL, '2026-02-25 10:26:58', '2026-02-25 10:29:20', '2026-02-25 02:29:20'),
(100, 1, NULL, '2026-02-25 15:53:20', NULL, '2026-02-25 07:57:11'),
(101, 1, NULL, '2026-02-26 03:20:06', NULL, '2026-02-25 19:24:24'),
(102, 1, NULL, '2026-02-26 03:31:32', '2026-02-26 03:32:15', '2026-02-25 19:32:15'),
(103, 1, NULL, '2026-02-26 03:35:47', NULL, '2026-02-25 19:42:47'),
(104, 1, NULL, '2026-02-26 03:43:17', '2026-02-26 03:44:51', '2026-02-25 19:44:51'),
(105, 1, NULL, '2026-02-26 03:44:56', '2026-02-26 03:54:02', '2026-02-25 19:54:02'),
(106, 1, NULL, '2026-02-26 03:57:10', NULL, '2026-02-25 19:57:45'),
(107, 1, NULL, '2026-02-26 04:12:56', '2026-02-26 04:13:16', '2026-02-25 20:13:16'),
(108, 1, NULL, '2026-02-26 04:13:41', NULL, '2026-02-25 20:13:46'),
(109, 1, NULL, '2026-02-26 04:18:08', '2026-02-26 04:18:37', '2026-02-25 20:18:37'),
(110, 1, NULL, '2026-03-05 03:39:03', '2026-03-05 03:40:41', '2026-03-04 19:40:41'),
(111, 1, NULL, '2026-03-05 03:41:37', '2026-03-05 03:44:51', '2026-03-04 19:44:51'),
(112, 1, NULL, '2026-03-05 03:44:59', NULL, '2026-03-04 19:48:00'),
(113, 1, NULL, '2026-03-05 03:48:22', NULL, '2026-03-04 20:00:06'),
(114, 1, NULL, '2026-03-05 05:52:12', NULL, '2026-03-04 22:32:21'),
(115, 1, NULL, '2026-03-05 06:32:56', '2026-03-05 06:36:18', '2026-03-04 22:36:18'),
(116, 1, NULL, '2026-03-05 06:36:23', NULL, '2026-03-04 22:42:41'),
(117, 1, NULL, '2026-03-05 06:43:43', NULL, '2026-03-05 00:03:44'),
(118, 1, NULL, '2026-03-05 12:25:00', NULL, '2026-03-05 04:25:01'),
(119, 1, NULL, '2026-03-05 12:27:50', NULL, '2026-03-05 04:31:55'),
(120, 1, NULL, '2026-03-05 12:33:46', '2026-03-05 12:34:40', '2026-03-05 04:34:40'),
(121, 1, NULL, '2026-03-08 07:40:36', NULL, '2026-03-07 23:40:36'),
(122, 1, NULL, '2026-03-08 07:41:21', NULL, '2026-03-07 23:45:54'),
(123, 1, NULL, '2026-03-08 07:59:59', NULL, '2026-03-08 00:03:16'),
(124, 1, NULL, '2026-03-08 08:06:16', NULL, '2026-03-08 00:06:44'),
(125, 1, NULL, '2026-03-08 08:07:45', NULL, '2026-03-08 00:19:03'),
(126, 1, NULL, '2026-03-08 08:33:09', NULL, '2026-03-08 00:33:45'),
(127, 1, NULL, '2026-03-08 08:35:03', '2026-03-08 08:35:07', '2026-03-08 00:35:07'),
(128, 1, NULL, '2026-03-08 08:36:39', NULL, '2026-03-08 00:36:39'),
(129, 1, NULL, '2026-03-08 11:44:13', '2026-03-08 11:52:00', '2026-03-08 03:52:00'),
(130, 1, NULL, '2026-03-08 11:52:09', NULL, '2026-03-08 04:18:35'),
(131, 1, NULL, '2026-03-08 12:18:59', NULL, '2026-03-08 04:55:42'),
(132, 1, NULL, '2026-03-08 12:56:01', NULL, '2026-03-08 06:07:16'),
(133, 1, NULL, '2026-03-08 14:09:31', NULL, '2026-03-08 06:09:51'),
(134, 1, NULL, '2026-03-08 14:10:38', NULL, '2026-03-08 06:10:48'),
(135, 1, NULL, '2026-03-08 14:14:53', NULL, '2026-03-08 06:21:22'),
(136, 1, NULL, '2026-03-08 14:22:31', NULL, '2026-03-08 06:30:59'),
(137, 1, NULL, '2026-03-08 14:35:36', NULL, '2026-03-08 06:45:17'),
(138, 1, NULL, '2026-03-08 14:46:32', NULL, '2026-03-08 06:46:39'),
(139, 1, NULL, '2026-03-08 14:47:43', NULL, '2026-03-08 07:00:44'),
(140, 1, NULL, '2026-03-10 07:35:27', NULL, '2026-03-09 23:39:39'),
(141, 1, NULL, '2026-03-10 07:41:38', NULL, '2026-03-09 23:41:48'),
(142, 1, NULL, '2026-03-10 08:16:44', NULL, '2026-03-10 02:46:32'),
(143, 1, NULL, '2026-03-11 15:44:10', NULL, '2026-03-11 08:34:33'),
(144, 1, NULL, '2026-03-12 03:16:15', NULL, '2026-03-11 19:42:16'),
(145, 1, NULL, '2026-03-12 03:42:39', NULL, '2026-03-11 21:07:22'),
(146, 1, NULL, '2026-03-12 07:42:06', NULL, '2026-03-12 08:03:30'),
(147, 1, NULL, '2026-03-12 09:04:12', NULL, '2026-03-12 08:39:22'),
(148, 1, NULL, '2026-03-12 09:41:46', NULL, '2026-03-12 08:51:21'),
(149, 1, NULL, '2026-03-12 09:51:57', NULL, '2026-03-12 08:52:11'),
(150, 1, NULL, '2026-03-12 10:05:32', NULL, '2026-03-12 03:20:18'),
(151, 1, NULL, '2026-03-13 06:51:30', '2026-03-13 06:51:57', '2026-03-13 06:51:57'),
(152, 1, NULL, '2026-03-13 07:10:41', NULL, '2026-03-13 10:38:10'),
(153, 1, NULL, '2026-03-15 13:26:30', '2026-03-15 14:44:04', '2026-03-15 06:44:04'),
(154, 1, NULL, '2026-03-15 14:50:06', NULL, '2026-03-15 06:55:37'),
(155, 1, NULL, '2026-03-15 15:13:34', NULL, '2026-03-15 08:26:37'),
(156, 1, NULL, '2026-03-15 16:27:02', NULL, '2026-03-15 08:54:18'),
(157, 1, NULL, '2026-03-15 16:54:46', NULL, '2026-03-15 10:13:47'),
(158, 1, NULL, '2026-03-16 11:51:50', NULL, '2026-03-16 04:58:26'),
(159, 1, NULL, '2026-03-16 12:58:43', NULL, '2026-03-16 06:38:21'),
(160, 1, NULL, '2026-03-16 15:37:19', '2026-03-16 16:21:07', '2026-03-16 08:21:07'),
(161, 1, NULL, '2026-03-16 16:22:35', '2026-03-16 16:38:16', '2026-03-16 08:38:16'),
(162, 1, NULL, '2026-03-16 16:39:20', '2026-03-16 16:57:45', '2026-03-16 08:57:45'),
(163, 1, NULL, '2026-03-16 17:11:05', NULL, '2026-03-16 09:46:59'),
(164, 1, NULL, '2026-03-16 17:48:46', '2026-03-16 17:49:26', '2026-03-16 09:49:26'),
(165, 1, NULL, '2026-03-16 17:56:22', NULL, '2026-03-16 10:09:55'),
(166, 1, NULL, '2026-03-17 23:18:08', NULL, '2026-03-17 15:33:16'),
(167, 1, NULL, '2026-03-17 23:53:30', NULL, '2026-03-17 16:00:38'),
(168, 1, NULL, '2026-03-19 11:05:30', NULL, '2026-03-19 03:37:02'),
(169, 1, NULL, '2026-03-25 13:58:25', '2026-03-25 14:40:54', '2026-03-25 06:40:54'),
(170, 1, NULL, '2026-03-25 21:15:39', NULL, '2026-03-25 13:15:54'),
(171, 1, NULL, '2026-03-25 22:28:16', '2026-03-25 22:58:48', '2026-03-25 14:58:48'),
(172, 1, NULL, '2026-03-27 16:46:02', NULL, '2026-03-27 09:10:12'),
(173, 1, NULL, '2026-03-27 17:12:01', NULL, '2026-03-27 09:12:44'),
(174, 1, NULL, '2026-03-29 16:02:28', '2026-03-29 16:02:49', '2026-03-29 08:02:49'),
(175, 1, NULL, '2026-03-29 16:03:00', NULL, '2026-03-29 08:04:36'),
(176, 1, NULL, '2026-03-29 16:09:40', '2026-03-29 16:25:18', '2026-03-29 08:25:18'),
(177, 1, NULL, '2026-03-29 16:25:57', '2026-03-29 16:44:24', '2026-03-29 08:44:24'),
(178, 1, NULL, '2026-03-31 14:44:55', NULL, '2026-03-31 07:52:24'),
(179, 1, NULL, '2026-04-02 10:34:02', NULL, '2026-04-02 02:53:09'),
(180, 1, NULL, '2026-04-04 20:22:46', NULL, '2026-04-04 13:44:09'),
(181, 1, NULL, '2026-04-06 14:17:28', NULL, '2026-04-06 06:19:24'),
(182, 1, NULL, '2026-04-06 15:53:15', '2026-04-06 16:03:34', '2026-04-06 08:03:34'),
(183, 1, NULL, '2026-04-06 22:17:21', NULL, '2026-04-06 14:24:29'),
(184, 1, NULL, '2026-04-06 22:31:42', NULL, '2026-04-06 14:32:44'),
(185, 1, NULL, '2026-04-06 22:35:59', NULL, '2026-04-06 14:43:05'),
(186, 1, NULL, '2026-04-08 14:02:52', '2026-04-08 14:18:53', '2026-04-08 06:18:53'),
(187, 1, NULL, '2026-04-09 11:37:41', '2026-04-09 12:13:04', '2026-04-09 04:13:04'),
(188, 1, NULL, '2026-04-09 13:00:08', NULL, '2026-04-09 05:00:08'),
(189, 1, NULL, '2026-04-09 20:30:42', '2026-04-09 20:31:37', '2026-04-09 12:31:37'),
(190, 1, NULL, '2026-04-09 20:32:05', '2026-04-09 20:34:28', '2026-04-09 12:34:28'),
(191, 1, NULL, '2026-04-09 20:34:42', '2026-04-09 20:34:59', '2026-04-09 12:34:59'),
(192, 1, NULL, '2026-04-09 20:35:29', NULL, '2026-04-09 12:50:43'),
(193, 1, NULL, '2026-04-10 17:07:37', NULL, '2026-04-10 09:09:52'),
(194, 1, NULL, '2026-04-10 17:22:26', NULL, '2026-04-10 09:35:44'),
(195, 1, NULL, '2026-04-10 17:35:18', NULL, '2026-04-10 09:35:34'),
(196, 1, NULL, '2026-04-10 17:36:18', NULL, '2026-04-10 09:37:20'),
(197, 1, NULL, '2026-04-10 17:38:34', NULL, '2026-04-10 09:41:36'),
(198, 1, NULL, '2026-04-10 17:39:41', NULL, '2026-04-10 09:41:22'),
(199, 1, NULL, '2026-04-10 17:50:47', '2026-04-10 17:54:14', '2026-04-10 09:54:14'),
(200, 1, NULL, '2026-04-10 17:53:41', '2026-04-10 17:54:03', '2026-04-10 09:54:03'),
(201, 1, NULL, '2026-04-10 17:57:10', NULL, '2026-04-10 09:57:28'),
(202, 1, NULL, '2026-04-10 18:01:59', NULL, '2026-04-10 10:16:31'),
(203, 1, NULL, '2026-04-10 18:16:56', NULL, '2026-04-10 10:26:36'),
(204, 1, NULL, '2026-04-10 18:29:45', '2026-04-10 18:53:18', '2026-04-10 10:53:18'),
(205, 1, NULL, '2026-04-10 18:55:47', NULL, '2026-04-10 17:55:31'),
(206, 1, NULL, '2026-04-11 08:32:46', '2026-04-11 13:41:06', '2026-04-11 05:41:06'),
(207, 1, NULL, '2026-04-11 13:52:48', '2026-04-11 14:12:44', '2026-04-11 06:12:44'),
(208, 1, NULL, '2026-04-11 16:34:28', NULL, '2026-04-11 08:54:39'),
(209, 1, NULL, '2026-04-11 17:02:26', NULL, '2026-04-11 09:10:43'),
(210, 1, NULL, '2026-04-12 16:39:54', NULL, '2026-04-12 08:47:21'),
(211, 1, NULL, '2026-04-12 17:05:12', NULL, '2026-04-12 09:05:13'),
(212, 1, NULL, '2026-04-12 17:36:00', NULL, '2026-04-12 09:36:06'),
(213, 1, NULL, '2026-04-12 17:48:22', NULL, '2026-04-12 09:56:41'),
(214, 1, NULL, '2026-04-13 08:26:44', '2026-04-13 08:32:31', '2026-04-13 00:32:31'),
(215, 1, NULL, '2026-04-13 08:37:29', '2026-04-13 08:39:10', '2026-04-13 00:39:10'),
(216, 1, NULL, '2026-04-13 09:46:33', '2026-04-13 09:46:37', '2026-04-13 01:46:37'),
(217, 1, NULL, '2026-04-13 09:56:16', '2026-04-13 09:57:44', '2026-04-13 01:57:44'),
(218, 1, NULL, '2026-04-13 09:58:05', NULL, '2026-04-13 02:01:25'),
(219, 1, NULL, '2026-04-13 10:02:42', NULL, '2026-04-13 02:13:36'),
(220, 1, NULL, '2026-04-13 10:14:16', '2026-04-13 10:34:30', '2026-04-13 02:34:30'),
(221, 1, NULL, '2026-04-13 10:36:25', '2026-04-13 10:46:55', '2026-04-13 02:46:55'),
(222, 1, NULL, '2026-04-13 10:49:20', '2026-04-13 10:56:36', '2026-04-13 02:56:36'),
(223, 1, NULL, '2026-04-13 10:57:02', '2026-04-13 11:06:56', '2026-04-13 03:06:56'),
(224, 1, NULL, '2026-04-13 11:08:11', '2026-04-13 11:28:45', '2026-04-13 03:28:45'),
(225, 1, NULL, '2026-04-13 11:29:14', NULL, '2026-04-13 03:39:11'),
(226, 1, NULL, '2026-04-13 11:40:49', '2026-04-13 11:47:38', '2026-04-13 03:47:38'),
(227, 1, NULL, '2026-04-13 11:50:50', '2026-04-13 12:02:49', '2026-04-13 04:02:49'),
(228, 1, NULL, '2026-04-13 12:26:23', '2026-04-13 14:24:54', '2026-04-13 06:24:54'),
(229, 1, NULL, '2026-04-13 14:35:14', '2026-04-13 14:41:46', '2026-04-13 06:41:46'),
(230, 1, NULL, '2026-04-16 13:59:18', '2026-04-16 14:01:33', '2026-04-16 06:01:33'),
(231, 1, NULL, '2026-04-16 14:47:07', NULL, '2026-04-16 07:25:17'),
(232, NULL, 'staff2', '2026-04-16 15:26:21', NULL, '2026-04-16 07:26:42'),
(233, 1, NULL, '2026-04-17 10:37:07', '2026-04-17 10:38:02', '2026-04-17 02:38:02'),
(234, 1, NULL, '2026-04-17 10:40:17', '2026-04-17 10:40:36', '2026-04-17 02:40:36'),
(235, 1, NULL, '2026-04-17 10:44:45', '2026-04-17 10:57:53', '2026-04-17 02:57:53'),
(236, 1, NULL, '2026-04-17 10:58:42', '2026-04-17 11:01:38', '2026-04-17 03:01:38'),
(237, 1, NULL, '2026-04-27 11:21:10', '2026-04-27 11:32:17', '2026-04-27 03:32:17'),
(238, 1, NULL, '2026-04-27 12:03:51', NULL, '2026-04-27 04:38:53'),
(239, 1, NULL, '2026-05-06 13:06:17', NULL, '2026-05-06 05:20:29'),
(240, 1, NULL, '2026-05-07 11:16:41', NULL, '2026-05-07 03:50:00'),
(241, NULL, 'admin2', '2026-05-07 11:50:09', '2026-05-07 11:50:18', '2026-05-07 03:50:18'),
(242, NULL, 'admin2', '2026-05-07 11:50:28', '2026-05-07 11:50:44', '2026-05-07 03:50:44'),
(243, NULL, 'admin2', '2026-05-07 11:51:01', NULL, '2026-05-07 03:54:44'),
(244, 1, NULL, '2026-05-08 21:03:30', NULL, '2026-05-08 13:14:57'),
(245, 1, NULL, '2026-05-08 21:21:03', NULL, '2026-05-08 13:53:36'),
(246, 1, NULL, '2026-05-08 22:12:34', NULL, '2026-05-08 14:13:35'),
(247, 1, NULL, '2026-05-09 10:52:40', NULL, '2026-05-09 02:54:37'),
(248, 1, NULL, '2026-05-09 12:11:33', NULL, '2026-05-09 04:11:48'),
(249, 1, NULL, '2026-05-15 21:30:41', NULL, '2026-05-15 13:32:06'),
(250, 1, NULL, '2026-05-15 21:41:20', NULL, '2026-05-15 13:41:34'),
(251, 1, NULL, '2026-05-18 15:36:36', NULL, '2026-05-18 07:37:48'),
(252, 1, NULL, '2026-06-22 17:18:42', NULL, '2026-06-22 09:26:20'),
(253, 1, NULL, '2026-08-26 10:31:30', NULL, '2026-08-26 02:38:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `anpr_captures`
--
ALTER TABLE `anpr_captures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `captures`
--
ALTER TABLE `captures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `capture_id` (`capture_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `staff_logs`
--
ALTER TABLE `staff_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addons`
--
ALTER TABLE `addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `anpr_captures`
--
ALTER TABLE `anpr_captures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `captures`
--
ALTER TABLE `captures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `staff_logs`
--
ALTER TABLE `staff_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
