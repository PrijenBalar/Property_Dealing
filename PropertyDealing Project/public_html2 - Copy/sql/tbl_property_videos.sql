-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 16, 2026 at 09:39 AM
-- Server version: 8.0.39
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kargil_property1`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_property_videos`
--

DROP TABLE IF EXISTS `tbl_property_videos`;
CREATE TABLE IF NOT EXISTS `tbl_property_videos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `property_id` int DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_property_videos`
--

INSERT INTO `tbl_property_videos` (`id`, `property_id`, `video`) VALUES
(1, 3, '1765785534_WhatsApp Video 2025-07-28 at 1.16.37 PM.mp4'),
(2, 6, '1765878473_video4.mp4'),
(3, 7, '1765880842_video4.mp4'),
(4, 9, '1765902410_video4.mp4'),
(5, 9, '1765902410_video2.mp4'),
(6, 8, '1765903625_video2.mp4'),
(7, 10, '1765903938_video4.mp4'),
(8, 10, '1765903938_video2.mp4'),
(9, 10, '1765959228_video2.mp4'),
(10, 10, '1765959228_video1.mp4'),
(13, 20, 'vid_6942a301407d4.mp4'),
(14, 33, 'vid_6942dfb9d5d9b.mp4'),
(16, 37, 'vid_6942e478b7337.mp4'),
(18, 43, 'vid_6942e99204820.mp4'),
(19, 48, '1766052095_WhatsApp Video 2025-12-18 at 3.23.48 PM.mp4'),
(33, 54, 'vid_6943f46f721e8.mp4'),
(35, 69, 'vid_69466c74a30bd.mp4'),
(36, 67, '1766223804_WhatsApp Video 2025-12-19 at 8.15.09 PM.mp4'),
(37, 75, 'vid_694672d020346.mp4'),
(39, 90, 'vid_6947e7951ca13.mp4'),
(42, 101, 'vid_69492a22d05ad.mp4'),
(44, 43, '1766491114_WhatsApp Video 2025-12-17 at 5.21.05 PM.mp4'),
(50, 65, '1766675069_WhatsApp Video 2025-12-24 at 1.18.36 PM.mp4'),
(51, 125, 'vid_694e3966e2e3f.mp4'),
(55, 140, 'vid_6953dc2383b74.mp4'),
(67, 163, 'vid_695cf9c5c4990.mp4'),
(71, 174, 'vid_6985b96bb280c.mp4'),
(72, 183, 'vid_6985c26eae0ce.mp4'),
(73, 201, 'vid_6985ddaa593ae.mp4'),
(74, 212, 'vid_6989bb2176102.mp4'),
(75, 218, 'vid_6989cc401e192.mp4'),
(76, 185, '1770819564_WhatsApp Video 2026-02-01 at 3.24.16 PM.mp4'),
(78, 229, 'vid_69931e26c83bf.mp4'),
(79, 234, 'vid_69932957b4c14.mp4'),
(80, 235, 'vid_69932c6f3888d.mp4'),
(81, 236, 'vid_69932e664a7a1.mp4'),
(82, 238, 'vid_699332a9759d0.mp4'),
(83, 239, 'vid_699333f2f000d.mp4'),
(84, 242, 'vid_69937d1a39526.mp4'),
(85, 244, 'vid_6997162498be3.mp4'),
(86, 251, 'vid_699958f906cb7.mp4'),
(87, 253, 'vid_69995f02d4663.mp4'),
(88, 254, 'vid_6999666f476e7.mp4'),
(90, 273, 'vid_699a254528056.mp4'),
(91, 274, 'vid_699a2795c7111.mp4'),
(92, 274, 'vid_699a2795c8608.mp4'),
(93, 306, 'vid_69a2bf1e07c0a.mp4'),
(94, 308, 'vid_69a2c0a189017.mp4'),
(95, 326, 'vid_69a68461d33db.mp4'),
(96, 327, 'vid_69a6866dc3635.mp4'),
(97, 337, 'vid_69a69208b3934.mp4'),
(98, 344, 'vid_69a69c9ce4ec5.mp4'),
(99, 319, '1772733108_WhatsApp Video 2026-02-27 at 2.33.15 PM.mp4'),
(100, 323, '1772733477_WhatsApp Video 2026-03-02 at 5.04.37 PM.mp4'),
(101, 349, '1773312724_WhatsApp Video 2026-03-12 at 12.08.36 PM.mp4'),
(102, 348, '1773313022_WhatsApp Video 2026-03-12 at 11.11.46 AM.mp4'),
(103, 353, 'vid_69ba5e6c6b1fa.mp4'),
(104, 356, 'vid_69ba6270d8a8c.mp4'),
(105, 363, 'vid_69ba6f7b3a573.mp4'),
(106, 367, 'vid_69ba7585db367.mp4'),
(107, 381, 'vid_69bd24e46a816.mp4'),
(108, 392, 'vid_69be324ba3cbf.mp4'),
(109, 404, 'vid_69c253bf590bb.mp4'),
(110, 405, 'vid_69c255d316d39.mp4'),
(111, 412, 'vid_69c264c3a9017.mp4'),
(112, 420, 'vid_69c389a9d1d73.mp4'),
(113, 422, 'vid_69c38bccedd37.mp4'),
(114, 422, 'vid_69c38bccf2095.mp4'),
(115, 422, 'vid_69c38bcd0683d.mp4'),
(122, 451, 'vid_6a0f35715859b.mp4'),
(123, 451, 'vid_6a0f35715e1b7.mp4'),
(124, 435, 'vid_6a0f36d26f7be.mp4'),
(125, 435, 'vid_6a0f36d27143b.mp4'),
(126, 451, 'vid_6a0f374271fad.mp4'),
(127, 451, 'vid_6a0f374273aef.mp4');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
