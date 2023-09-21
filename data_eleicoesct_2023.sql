-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: database:3306
-- Generation Time: Sep 21, 2023 at 07:47 AM
-- Server version: 8.1.0
-- PHP Version: 8.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `data_eleicoesct_2023`
--

-- --------------------------------------------------------

--
-- Table structure for table `elct_apuracoes`
--

CREATE TABLE `elct_apuracoes` (
  `id` int NOT NULL,
  `candidato` int NOT NULL,
  `sessao` int NOT NULL,
  `votos` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `elct_candidatos`
--

CREATE TABLE `elct_candidatos` (
  `id` int NOT NULL,
  `nome` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `numero` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `foto` text COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `elct_candidatos`
--

INSERT INTO `elct_candidatos` (`id`, `nome`, `numero`, `foto`) VALUES
(2, 'ALEXANDRA ROSSUEL', '111', 'a1caed054a901f82b59459d4c238b563.png');

-- --------------------------------------------------------

--
-- Table structure for table `elct_sessoes`
--

CREATE TABLE `elct_sessoes` (
  `id` int NOT NULL,
  `local` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `numero` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `elct_apuracoes`
--
ALTER TABLE `elct_apuracoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `apuracoes_fk_candidato` (`candidato`),
  ADD KEY `apuracoes_fk_sessao` (`sessao`);

--
-- Indexes for table `elct_candidatos`
--
ALTER TABLE `elct_candidatos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `elct_sessoes`
--
ALTER TABLE `elct_sessoes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `elct_apuracoes`
--
ALTER TABLE `elct_apuracoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `elct_candidatos`
--
ALTER TABLE `elct_candidatos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `elct_sessoes`
--
ALTER TABLE `elct_sessoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `elct_apuracoes`
--
ALTER TABLE `elct_apuracoes`
  ADD CONSTRAINT `apuracoes_fk_candidato` FOREIGN KEY (`candidato`) REFERENCES `elct_candidatos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `apuracoes_fk_sessao` FOREIGN KEY (`sessao`) REFERENCES `elct_sessoes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
