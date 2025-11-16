-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2023 at 06:51 PM
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
-- Database: `imobilar`
--

-- --------------------------------------------------------

--
-- Table structure for table `codigos`
--

CREATE TABLE `codigos` (
  `id_codigo` int(11) UNSIGNED NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `tipo` enum('2FA','ForgotPwd') NOT NULL,
  `link` varchar(255) NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favoritos`
--

CREATE TABLE `favoritos` (
  `id_moradia` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `data_favorito` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `moradia`
--

CREATE TABLE `moradia` (
  `id_moradia` int(11) UNSIGNED NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `tipo` enum('Casa','Apartamento','Kitnet','República','Quarto','Quarto Compartilhado','Dormitório','Pensão') NOT NULL,
  `largura` int(5) NOT NULL,
  `comprimento` int(5) NOT NULL,
  `preco` decimal(8,2) UNSIGNED NOT NULL,
  `numero_comodos` int(3) UNSIGNED DEFAULT NULL,
  `cep` varchar(9) NOT NULL,
  `cidade` varchar(32) NOT NULL,
  `uf` enum('Acre','Alagoas','Amapá','Amazonas','Bahia','Ceará','Distrito Federal','Espírito Santo','Goiás','Maranhão','Mato Grosso','Mato Grosso do Sul','Minas Gerais','Pará','Paraíba','Paraná','Pernambuco','Piauí','Rio de Janeiro','Rio Grande do Norte','Rio Grande do Sul','Rondônia','Roraima','Santa Catarina','São Paulo','Sergipe','Tocantins') NOT NULL,
  `logradouro` varchar(255) NOT NULL,
  `bairro` varchar(255) NOT NULL,
  `numero` int(3) UNSIGNED NOT NULL,
  `descricao` text NOT NULL,
  `wifi` tinyint(1) NOT NULL,
  `refeicao` tinyint(1) NOT NULL,
  `lazer` tinyint(1) NOT NULL,
  `estacionamento` tinyint(1) NOT NULL,
  `animais` tinyint(1) NOT NULL,
  `imagem1` varchar(255) NOT NULL,
  `imagem2` varchar(255) DEFAULT NULL,
  `imagem3` varchar(255) DEFAULT NULL,
  `imagem4` varchar(255) DEFAULT NULL,
  `imagem5` varchar(255) DEFAULT NULL,
  `situacao` enum('Aprovado','Rejeitado','Em Análise') NOT NULL DEFAULT 'Em Análise',
  `data_criacao` date NOT NULL DEFAULT current_timestamp(),
  `data_rejeicao` date NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id_notificacao` int(11) UNSIGNED NOT NULL,
  `assunto` varchar(255) NOT NULL,
  `conteudo` text NOT NULL,
  `data_notificacao` date NOT NULL DEFAULT current_timestamp(),
  `lido` tinyint(1) NOT NULL DEFAULT 0,
  `link` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id_token` int(11) UNSIGNED NOT NULL,
  `seletor` varchar(255) NOT NULL,
  `validador` varchar(255) NOT NULL,
  `data_expiracao` date NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `nome_usuario` varchar(255) NOT NULL,
  `nome_completo` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(11) DEFAULT NULL,
  `email_contato` varchar(255) DEFAULT NULL,
  `pfp` varchar(255) DEFAULT NULL,
  `criacao` datetime NOT NULL DEFAULT curtime(),
  `ativo` tinyint(1) NOT NULL DEFAULT 0,
  `cargo` enum('usuario','administrador') NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome_usuario`, `nome_completo`, `email`, `senha`, `telefone`, `email_contato`, `pfp`, `criacao`, `ativo`, `cargo`) VALUES
(1, 'admin', 'Administrador', 'admin@email.com', '$2y$12$eYI1NjkVw5cryYZ.svwzCuV2.V4j7Jo5QAkFul3XNJeY2S0SvswfO', '99123456789', 'admin-email@email.com', '', '2023-11-21 00:00:00', 1, 'administrador'),
(2, 'user', 'Usuário', 'user@email.com', '$2y$12$YrRUajzbj6Z99LUZC427K.KycRvxAyMeylNuCwoqrHjv3gj4s67Vy', '99123456789', 'user-email@email.com', '', '2023-11-21 00:00:00', 1, 'usuario');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `codigos`
--
ALTER TABLE `codigos`
  ADD PRIMARY KEY (`id_codigo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `favoritos`
--
ALTER TABLE `favoritos`
  ADD KEY `id_moradia` (`id_moradia`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `moradia`
--
ALTER TABLE `moradia`
  ADD PRIMARY KEY (`id_moradia`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id_notificacao`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id_token`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `codigos`
--
ALTER TABLE `codigos`
  MODIFY `id_codigo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moradia`
--
ALTER TABLE `moradia`
  MODIFY `id_moradia` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id_notificacao` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id_token` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `codigos`
--
ALTER TABLE `codigos`
  ADD CONSTRAINT `codigos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`id_moradia`) REFERENCES `moradia` (`id_moradia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `moradia`
--
ALTER TABLE `moradia`
  ADD CONSTRAINT `moradia_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
