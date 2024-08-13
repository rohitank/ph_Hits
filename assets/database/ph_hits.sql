-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 15, 2023 at 09:41 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ph_hits`
--

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `contentID` int(11) NOT NULL,
  `contentTitle` varchar(60) NOT NULL,
  `contentDesc` varchar(155) NOT NULL,
  `schedDate` date NOT NULL,
  `userID` int(11) NOT NULL,
  `filePath` varchar(200) DEFAULT NULL,
  `duration` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`contentID`, `contentTitle`, `contentDesc`, `schedDate`, `userID`, `filePath`, `duration`) VALUES
(48, 'Lazy Song - Bruno Mars', 'jjj', '2023-12-07', 1, 'uploads/Bruno Mars - The Lazy Song (Official Music Video).mp4', '00:03:19'),
(49, 'Smokin Out The Window - Bruno Mars ft. Anderson Paak', 'Sang by Silk Sonic', '2023-12-07', 1, 'uploads/Bruno Mars, Anderson .Paak, Silk Sonic - Smokin Out The Window [Official Music Video].mp4', '00:03:20'),
(50, 'I\'m The One - DJ Khaled', 'Anotha One!', '2023-12-07', 1, 'uploads/DJ Khaled - I\'m The One ft. Justin Bieber, Quavo, Chance the Rapper, Lil Wayne.mp3', '00:05:21'),
(51, 'Marshmello - Alone', 'Do you like smores?', '2023-12-07', 1, 'uploads/Marshmello - Alone (Official Music Video).mp4', '00:03:19'),
(52, 'Happy - Pharell Williams', 'What\'s the opposite of happy? Ethan.', '2023-12-07', 1, 'uploads/Pharrell Williams - Happy (Video).mp3', '00:04:00'),
(53, 'Finesse - Bruno Mars', 'Sang by Bruno Mars the one and only!', '2023-12-07', 1, 'uploads/Bruno Mars - Finesse (Remix) (feat. Cardi B) (Official Music Video).mp3', '00:03:42'),
(54, 'Hymn for the Weeknd - Coldplay', 'Hotwork', '2023-12-07', 1, 'uploads/Coldplay - Hymn For The Weekend (Official Video).mp4', '00:04:20'),
(55, 'Hymn for the Weeknd - Coldplay', 'Hotwork', '2023-12-07', 1, 'uploads/Coldplay - Hymn For The Weekend (Official Video).mp4', '00:04:20'),
(57, 'Welcome to phHits!', 'Listen to the Philippines\' top hits today!', '2023-12-08', 2, 'uploads/Advertisement.mp4', '00:00:35'),
(58, 'as', 'delete me', '2023-12-12', 3, 'uploads/Boundary Analysis.mp4', '00:00:31');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `historyID` int(11) NOT NULL,
  `streamDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`historyID`, `streamDate`) VALUES
(18, '2023-12-13'),
(19, '2023-12-14');

-- --------------------------------------------------------

--
-- Table structure for table `logInStatus`
--

CREATE TABLE `logInStatus` (
  `userID` int(11) NOT NULL,
  `status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logInStatus`
--

INSERT INTO `logInStatus` (`userID`, `status`) VALUES
(2, 'OFFLINE'),
(1, 'OFFLINE'),
(3, 'OFFLINE'),
(4, 'OFFLINE'),
(5, 'OFFLINE'),
(6, 'OFFLINE'),
(9, 'OFFLINE');

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `queueID` int(11) NOT NULL,
  `contentID` int(11) NOT NULL,
  `dateOfAiring` date NOT NULL,
  `schedTime` time NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue`
--

INSERT INTO `queue` (`queueID`, `contentID`, `dateOfAiring`, `schedTime`, `status`) VALUES
(7750, 48, '2023-12-13', '15:57:21', 'queued'),
(7751, 49, '2023-12-13', '16:00:41', 'queued'),
(7752, 51, '2023-12-13', '16:04:00', 'queued'),
(7753, 54, '2023-12-13', '16:08:20', 'queued'),
(7754, 57, '2023-12-13', '16:08:55', 'queued'),
(7755, 48, '2023-12-14', '16:12:14', 'queued'),
(7756, 49, '2023-12-14', '16:15:34', 'queued'),
(7757, 51, '2023-12-14', '16:18:53', 'queued'),
(7758, 54, '2023-12-14', '16:23:13', 'queued'),
(7759, 48, '2023-12-15', '16:21:36', 'queued');

-- --------------------------------------------------------

--
-- Table structure for table `streams`
--

CREATE TABLE `streams` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(300) NOT NULL,
  `livestreamer` varchar(50) NOT NULL,
  `isStreaming` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `streams`
--

INSERT INTO `streams` (`id`, `title`, `description`, `livestreamer`, `isStreaming`) VALUES
(70, 'HHHHHH', 'HHHHHHH', 'meep', 0),
(71, 'Hi', 'hello', 'meep', 0),
(72, 'sup', 'mmm', 'meep', 0),
(73, 'big ting', 'bigggg', 'meep', 0),
(74, 'shouldn\'t work', 'hahaha', 'woshu', 0),
(75, 'big boi', 'bi g', 'meep', 0),
(76, 'memem', 'memem', 'woshu', 0),
(77, 'j', 'j', 'meep', 0),
(78, 'What\'s up!', 'Hello bro.', 'woshu', 0),
(79, 'anotha one', 'hello bro', 'woshu', 0),
(80, 'Big Boi', 'I need a', 'meep', 0),
(81, 'Hello ', 'Hi', 'meep', 0),
(82, 'j', 'j', 'meep', 0),
(83, 'My name is Rohit!', 'Hello!', 'meep', 0),
(84, 'Begin again', 'spin again', 'meep', 0),
(85, 'wassup', 'hi', 'meep', 0),
(86, 'Hello!', 'My name is Rohit!', 'meep', 0),
(87, 'Hello!', 'My name is Rohit Tank.', 'meep', 0),
(88, 'Stream', 'hello', 'meep', 0),
(89, 'Another one.', 'Hello.', 'meep', 0),
(90, 'Big things', 'big big', 'meep', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `fName` varchar(20) NOT NULL,
  `lName` varchar(20) NOT NULL,
  `userName` varchar(20) NOT NULL,
  `password` varchar(30) NOT NULL,
  `status` varchar(8) DEFAULT NULL,
  `role` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `fName`, `lName`, `userName`, `password`, `status`, `role`) VALUES
(1, 'Aden', 'Lim', 'sniper', '123', 'ACTIVE', 'Admin'),
(2, 'John', 'Doe', 'woshu', '123', 'ACTIVE', 'Content Manager'),
(3, 'Alice', 'Smith', 'meep', '123', 'ACTIVE', 'Content Manager'),
(4, 'Emily', 'Johnson', 'emilyj', '123', 'ACTIVE', 'Content Manager'),
(5, 'Michael', 'Williams', 'mwill', '123', 'ACTIVE', 'Content Manager'),
(6, 'Sophia', 'Brown', 'lab', '123', 'ACTIVE', 'Content Manager'),
(9, 'Shan', 'Man', 'smac', '123', 'ACTIVE', 'Content Manager');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`contentID`) USING BTREE,
  ADD KEY `userID` (`userID`) USING BTREE;

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`historyID`),
  ADD KEY `streamDate` (`streamDate`);

--
-- Indexes for table `logInStatus`
--
ALTER TABLE `logInStatus`
  ADD KEY `user_id` (`userID`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`queueID`),
  ADD KEY `contentID` (`contentID`),
  ADD KEY `dateOfAiring` (`dateOfAiring`);

--
-- Indexes for table `streams`
--
ALTER TABLE `streams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`,`userName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `contentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `historyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `queueID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7760;

--
-- AUTO_INCREMENT for table `streams`
--
ALTER TABLE `streams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `content_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`streamDate`) REFERENCES `queue` (`dateOfAiring`);

--
-- Constraints for table `logInStatus`
--
ALTER TABLE `logInStatus`
  ADD CONSTRAINT `loginstatus_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `queue`
--
ALTER TABLE `queue`
  ADD CONSTRAINT `queue_ibfk_1` FOREIGN KEY (`contentID`) REFERENCES `content` (`contentID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
