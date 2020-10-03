-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2020 at 08:02 AM
-- Server version: 10.3.22-MariaDB-0+deb10u1
-- PHP Version: 7.3.17-1+0~20200419.57+debian10~1.gbp0fda17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iconjudge`
--

-- --------------------------------------------------------

--
-- Table structure for table `iconScores`
--

CREATE TABLE `iconScores` (
  `iconID` int(11) NOT NULL,
  `iconNumber` smallint(6) NOT NULL,
  `gamemode` varchar(8) NOT NULL,
  `percentageWon` float NOT NULL,
  `timer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `matchups`
--

CREATE TABLE `matchups` (
  `matchupID` int(11) NOT NULL,
  `matchupToken` varchar(128) NOT NULL,
  `userToken` varchar(128) NOT NULL,
  `firstChoice` smallint(6) NOT NULL,
  `secondChoice` smallint(6) NOT NULL,
  `gamemode` varchar(8) NOT NULL,
  `whoWon` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'who''s next',
  `isDecided` tinyint(1) NOT NULL DEFAULT 0,
  `timer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `triedAndFailed`
--

CREATE TABLE `triedAndFailed` (
  `failureID` int(11) NOT NULL,
  `matchupToken` varchar(128) NOT NULL,
  `userToken` varchar(128) NOT NULL,
  `firstChoice` smallint(6) NOT NULL,
  `secondChoice` smallint(6) NOT NULL,
  `gamemode` varchar(8) NOT NULL,
  `whoWon` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `iconScores`
--
ALTER TABLE `iconScores`
  ADD PRIMARY KEY (`iconID`);
  
--
-- Indexes for table `matchups`
--
ALTER TABLE `matchups`
  ADD PRIMARY KEY (`matchupID`);

--
-- Indexes for table `triedAndFailed`
--
ALTER TABLE `triedAndFailed`
  ADD PRIMARY KEY (`failureID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `iconScores`
--
ALTER TABLE `iconScores`
  MODIFY `iconID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matchups`
--
ALTER TABLE `matchups`
  MODIFY `matchupID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `triedAndFailed`
--
ALTER TABLE `triedAndFailed`
  MODIFY `failureID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
