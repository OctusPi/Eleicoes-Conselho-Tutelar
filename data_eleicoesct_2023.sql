-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: database:3306
-- Tempo de geração: 20/09/2023 às 22:08
-- Versão do servidor: 8.1.0
-- Versão do PHP: 8.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `data_eleicoesct_2023`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `elct_apuracoes`
--

CREATE TABLE `elct_apuracoes` (
  `id` int NOT NULL,
  `candidato` int NOT NULL,
  `sessao` int NOT NULL,
  `votos` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `elct_candidatos`
--

CREATE TABLE `elct_candidatos` (
  `id` int NOT NULL,
  `nome` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `numero` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `foto` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `elct_sessoes`
--

CREATE TABLE `elct_sessoes` (
  `id` int NOT NULL,
  `local` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `numero` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `elct_apuracoes`
--
ALTER TABLE `elct_apuracoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `apuracoes_fk_candidato` (`candidato`),
  ADD KEY `apuracoes_fk_sessao` (`sessao`);

--
-- Índices de tabela `elct_candidatos`
--
ALTER TABLE `elct_candidatos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `elct_sessoes`
--
ALTER TABLE `elct_sessoes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `elct_apuracoes`
--
ALTER TABLE `elct_apuracoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `elct_candidatos`
--
ALTER TABLE `elct_candidatos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `elct_sessoes`
--
ALTER TABLE `elct_sessoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `elct_apuracoes`
--
ALTER TABLE `elct_apuracoes`
  ADD CONSTRAINT `apuracoes_fk_candidato` FOREIGN KEY (`candidato`) REFERENCES `elct_candidatos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `apuracoes_fk_sessao` FOREIGN KEY (`sessao`) REFERENCES `elct_sessoes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
