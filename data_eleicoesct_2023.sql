-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: database:3306
-- Tempo de geração: 22/09/2023 às 01:47
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
-- Estrutura para tabela `elct_abstencoes`
--

CREATE TABLE `elct_abstencoes` (
  `id` int NOT NULL,
  `sessao` int NOT NULL,
  `nulos` int NOT NULL DEFAULT '0',
  `brancos` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `elct_abstencoes`
--

INSERT INTO `elct_abstencoes` (`id`, `sessao`, `nulos`, `brancos`) VALUES
(1, 2, 8, 10),
(2, 1, 8, 3);

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

--
-- Despejando dados para a tabela `elct_apuracoes`
--

INSERT INTO `elct_apuracoes` (`id`, `candidato`, `sessao`, `votos`) VALUES
(1, 6, 2, 250),
(2, 4, 2, 340),
(3, 5, 2, 320),
(4, 3, 2, 750),
(5, 6, 1, 250),
(6, 4, 1, 340),
(7, 5, 1, 3200),
(8, 3, 1, 170);

-- --------------------------------------------------------

--
-- Estrutura para tabela `elct_candidatos`
--

CREATE TABLE `elct_candidatos` (
  `id` int NOT NULL,
  `nome` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `numero` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `foto` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `elct_candidatos`
--

INSERT INTO `elct_candidatos` (`id`, `nome`, `numero`, `foto`) VALUES
(3, 'MARIA FLORES DA SILVA', '100', 'e13928d3f3e8b48bed029579a82cf979.png'),
(4, 'ANTONIA SAIKE DE ALENCAR', '990', '3f37bcae1514a95ccd5f02302c80c07c.png'),
(5, 'FRANCISCA PRINCE DE ANDRADE', '345', '7a7aabd0dea0a887b31ae2ac88aa2093.png'),
(6, 'ANGELO PEREIRA AMORIM', '675', '8a6d2ff08903cb95bd37875f1e5534d0.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `elct_sessoes`
--

CREATE TABLE `elct_sessoes` (
  `id` int NOT NULL,
  `local` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `numero` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `elct_sessoes`
--

INSERT INTO `elct_sessoes` (`id`, `local`, `numero`) VALUES
(1, 'CABARÉ DE DONA CHICA', '001'),
(2, 'BAR DE SEU VALENÇA', '002');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `elct_abstencoes`
--
ALTER TABLE `elct_abstencoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abstencoes_fk_sessao` (`sessao`);

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
-- AUTO_INCREMENT de tabela `elct_abstencoes`
--
ALTER TABLE `elct_abstencoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `elct_apuracoes`
--
ALTER TABLE `elct_apuracoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `elct_candidatos`
--
ALTER TABLE `elct_candidatos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `elct_sessoes`
--
ALTER TABLE `elct_sessoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `elct_abstencoes`
--
ALTER TABLE `elct_abstencoes`
  ADD CONSTRAINT `abstencoes_fk_sessao` FOREIGN KEY (`sessao`) REFERENCES `elct_sessoes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

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
