create database if not exists arlink_db;
use arlink_db;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `aparelhos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `numero_serie` varchar(100) DEFAULT NULL,
  `capacidade_btu` varchar(20) DEFAULT NULL,
  `tipo` varchar(30) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'em_dia',
  `proxima_manutencao` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `cidade` varchar(80) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `manutencoes` (
  `id` int(11) NOT NULL,
  `aparelho_id` int(11) NOT NULL,
  `tecnico_id` int(11) NOT NULL,
  `data_atendimento` date NOT NULL,
  `tipo_servico` varchar(50) NOT NULL,
  `diagnostico` text NOT NULL,
  `servico_realizado` text NOT NULL,
  `pecas_utilizadas` text DEFAULT NULL,
  `valor_cobrado` decimal(10,2) DEFAULT NULL,
  `proxima_visita` date DEFAULT NULL,
  `observacoes_internas` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tecnicos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tecnicos` (`id`, `nome`, `email`, `senha`, `telefone`, `criado_em`) VALUES
(1, 'Admin Teste', 'admin@arlink.com', '$2y$10$TZjyDHiIurPwP5qp3uiWFexW5OB0qSBfjzNfErzCjjCjhezSmMAla', '(14) 99999-9999', '2026-08-14 02:11:30');

ALTER TABLE `aparelhos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `manutencoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aparelho_id` (`aparelho_id`),
  ADD KEY `tecnico_id` (`tecnico_id`);

ALTER TABLE `tecnicos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `aparelhos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `manutencoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tecnicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `aparelhos`
  ADD CONSTRAINT `aparelhos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

ALTER TABLE `manutencoes`
  ADD CONSTRAINT `manutencoes_ibfk_1` FOREIGN KEY (`aparelho_id`) REFERENCES `aparelhos` (`id`),
  ADD CONSTRAINT `manutencoes_ibfk_2` FOREIGN KEY (`tecnico_id`) REFERENCES `tecnicos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
