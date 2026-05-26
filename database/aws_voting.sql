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
  `image` varchar(255) DEFAULT NULL,
  `manifesto` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `name`, `position`, `election_id`, `image`, `manifesto`) VALUES
(1, 'Rahul Sharma', 'President', 1, NULL, 'I stand for upgrading campus Wi-Fi infrastructure, extending library reading room hours to 24/7, and initiating a weekly student-grievance roundtable with the principal. Let\'s make campus student-first!'),
(2, 'Priya Deshmukh', 'President', 1, NULL, 'My vision is a green, clean, and digitized campus. I promise to replace paper noticeboards with modern interactive LED panels, setup solar charging hubs in college lawns, and host monthly inter-collegiate hackathons.'),
(3, 'Aditya Kulkarni', 'Vice President', 1, NULL, 'I pledge to work towards improving placement preparation bootcamps, securing sponsorships for our annual cultural fest, and upgrading equipment in the engineering laboratories.'),
(4, 'Sneha Patil', 'Vice President', 1, NULL, 'My focus is on student mental well-being and sports. I will set up a dedicated counseling lounge, introduce inter-departmental sports leagues, and ensure sanitizers & safety equipment are standard across all campuses.'),
(5, 'Amit Shinde', 'CSE Representative', 2, NULL, 'I stand for computer lab modernization, student-led study groups, and securing extra software licenses for home use. Choose innovation!'),
(6, 'Neha Kendre', 'CSE Representative', 2, NULL, 'I want to bridge the gap between syllabus and industry. I propose inviting tech startup founders for regular talks, and forming coding clubs for lower semester students.'),
(7, 'Vikram Malhotra', 'Executive Director', 4, NULL, 'I propose allocating 40% of our budget directly to localized community reforestation projects, introducing quarterly transparent audit reports for all donors, and launching a mobile app to connect our global volunteers.'),
(8, 'Dr. Anjali Mehta', 'Executive Director', 4, NULL, 'My focus is on global advocacy and partnership expansion. I aim to secure three major international conservation grants, establish green partnerships with Fortune 500 companies, and setup our first urban biodiversity research lab.');

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
(1, 'Student Council Elections 2026', 'College', '2026-05-20', '2026-06-05', 'Active', NULL),
(2, 'Department Representative Polls', 'College', '2026-05-22', '2026-06-02', 'Active', NULL),
(3, 'College Cultural Committee Selection', 'College', '2026-05-24', '2026-06-10', 'Active', NULL),
(4, 'GreenEarth NGO Board Selection 2026', 'NGO', '2026-05-24', '2026-06-15', 'Active', NULL);

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
(1, 1, 'The secure Aadhar and mobile validation makes this portal feel highly authentic and secure!', '2026-05-25 12:22:17'),
(2, 2, 'Very sleek and fast voting interface! Great work on the dark-mode dashboard.', '2026-05-25 12:35:45');

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

INSERT INTO `users` (`id`, `name`, `email`, `password`, `election_card`, `mobile`, `has_voted`, `created_at`, `type`) VALUES
(1, 'Abhishek Joshi', 'abhishek@college.ac.in', '$2y$10$f3WnF3qf.QJ3PzFfe0082OxzO16Lcr/tG5Q0P0iG9CexxXhDkW7e6', '111122223333', '9876543210', 1, '2026-05-25 12:00:00', 'College'),
(2, 'Riya Sen', 'riya@college.ac.in', '$2y$10$f3WnF3qf.QJ3PzFfe0082OxzO16Lcr/tG5Q0P0iG9CexxXhDkW7e6', '444455556666', '9812345678', 0, '2026-05-25 12:00:00', 'College'),
(3, 'Yash Vardhan', 'yash@college.ac.in', '$2y$10$f3WnF3qf.QJ3PzFfe0082OxzO16Lcr/tG5Q0P0iG9CexxXhDkW7e6', '777788889999', '9765432109', 0, '2026-05-25 12:00:00', 'College');

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
(1, 1, 1, '2026-05-25 12:44:21', 1);

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
