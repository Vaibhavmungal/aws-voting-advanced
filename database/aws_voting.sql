-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2026 at 01:50 PM
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
-- Database: `aws_voting`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'Vaibhav', '1234', '2026-02-17 09:23:39');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `election_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `name`, `position`, `election_id`, `image`) VALUES
(6, 'DIY', 'CR', 13, 'IMG_3957.JPG'),
(7, 'adii', 'President ', 13, 'FullSizeRender.jpg'),
(8, 'Akshay Thorat', 'V565+', 13, 'IMG_3987.JPG'),
(9, 'Rohit Patil', 'President', 13, NULL),
(10, 'Sakshi Deshmukh', 'Vice President', 13, NULL),
(11, 'wjdaszkxnaskjl', 'djlcndlzkx', 14, 'Screenshot 2025-02-10 174734.png'),
(12, 'mncskx,m ', 'dknlzv.x,', 14, 'Screenshot 2025-02-10 182242.png');

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

CREATE TABLE `elections` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(20) DEFAULT 'Inactive',
  `winner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `elections`
--

INSERT INTO `elections` (`id`, `title`, `type`, `start_date`, `end_date`, `status`, `winner_id`) VALUES
(4, 'admin', 'College', '2026-02-17', '2026-02-18', 'Inactive', NULL),
(13, 'College Election', 'College', '2026-02-19', '2026-02-22', 'Inactive', 0),
(14, 'dbszcjk', 'College', '2026-03-04', '2026-03-05', 'Active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `message`, `created_at`) VALUES
(1, 1, 'Very good voting system!', '2026-02-19 12:22:17'),
(2, 71, 'hasbzxcj kjzx', '2026-02-19 12:35:45'),
(3, 47, 'xyz\r\n', '2026-02-19 12:37:29'),
(4, 52, 'zzxzccvx ', '2026-02-19 13:45:38');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `action` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `election_card` varchar(20) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `has_voted` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(50) NOT NULL DEFAULT 'College'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `has_voted`, `created_at`, `type`) VALUES
(3, 'Rohit Patil', 'rohit.patil@college.edu', '123456', 1, '2026-02-17 10:46:08', 'College'),
(4, 'Sakshi Deshmukh', 'sakshi.deshmukh@college.edu', '123456', 0, '2026-02-17 10:46:08', 'College'),
(47, 'Rohit Patil', 'rohit01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(48, 'Sakshi Deshmukh', 'sakshi01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(49, 'Amit Shinde', 'amit01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(50, 'Priya Jadhav', 'priya01@college.edu', '123456', 1, '2026-02-17 10:55:09', 'College'),
(51, 'Omkar Kulkarni', 'omkar01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(52, 'Neha Kendre', 'neha01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(53, 'Rahul Sawant', 'rahul01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(54, 'Vaishnavi Patankar', 'vaishnavi01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(55, 'Akshay Thorat', 'akshay01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(56, 'Komal Raut', 'komal01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(57, 'Nikhil Chavan', 'nikhil01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(58, 'Pooja Ghadge', 'pooja01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(59, 'Sagar Wagh', 'sagar01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(60, 'Shubham Pawar', 'shubham01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(61, 'Sneha More', 'sneha01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(62, 'Tejas Mane', 'tejas01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(63, 'Anjali Gaikwad', 'anjali01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(64, 'Kunal Bhosale', 'kunal01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(65, 'Shruti Kadam', 'shruti01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(66, 'Pratik Mahajan', 'pratik01@college.edu', '123456', 0, '2026-02-17 10:55:09', 'College'),
(70, 'vaibhav mungal', 'vaihav@college.edu', '1234', 0, '2026-02-19 12:23:26', 'College'),
(71, 'vaibhav mungal', 'vaibhav@college.edu', '1234', 0, '2026-02-19 12:24:36', 'College');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `election_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `user_id`, `candidate_id`, `voted_at`, `election_id`) VALUES
(1, 1, 2, '2026-02-17 09:24:55', 0),
(2, 3, 2, '2026-02-17 10:52:57', 0),
(4, 69, 17, '2026-02-17 11:16:28', 0),
(6, 50, 17, '2026-02-17 12:13:11', 0),
(10, 71, 0, '2026-02-19 12:26:08', 13),
(11, 47, 0, '2026-02-19 13:33:26', 13),
(12, 48, 0, '2026-02-19 13:35:06', 13),
(13, 51, 7, '2026-02-19 13:44:21', 13),
(14, 52, 7, '2026-02-19 13:45:27', 13),
(15, 53, 10, '2026-02-19 13:56:44', 13),
(16, 58, 8, '2026-02-19 13:57:20', 13);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `election_id` (`election_id`);

--
-- Indexes for table `elections`
--
ALTER TABLE `elections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote` (`user_id`,`election_id`),
  ADD KEY `idx_votes_election_id` (`election_id`),
  ADD KEY `idx_votes_candidate_id` (`candidate_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD KEY `idx_users_has_voted` (`has_voted`);

--
-- Indexes for table `elections`
--
ALTER TABLE `elections`
  ADD KEY `idx_elections_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `elections`
--
ALTER TABLE `elections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
