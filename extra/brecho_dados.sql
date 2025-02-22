-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/02/2025 às 02:19
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `brecho_dados`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`) VALUES
(1, 'aeinsfelt@gmail.com', 'e34b48add8a1f3f2e35ff1c800eddfb2185ef444cb01e3cdcac2c3b3eab594eb', '2025-02-06 04:33:31'),
(2, 'aeinsfelt@gmail.com', '12727dc66162cd1eb0edd642d6262e31a59e19d89a2b28c35269a5c27303c2c5', '2025-02-06 04:39:01'),
(3, 'aeinsfelt@gmail.com', 'c5861bc6c9e56b4e4238eb27404e46a5f674fdda7e4dc5ca14c1fb68a6d2b508', '2025-02-07 02:39:43'),
(8, 'adrianojreinsfelt@gmail.com', '4ba9f869b4770cc9f6d577037102a64e199c3779a77e1a009bb889be8ce6b365', '2025-02-07 05:11:57'),
(9, 'aeinsfelt@gmail.com', '7e40b29b79cf341359b16eb2a66280fa36a280963654d95da2286484b4e8d119', '2025-02-07 05:12:41'),
(10, 'adrianojreinsfelt@gmail.com', '2a7e6a4f977a98985c36696afbd860ac78fa6874f7241ecbd75924d8b378c7fe', '2025-02-07 05:12:51'),
(11, 'adrianojreinsfelt@outlook.com', 'c6ff29f4dc79e4a1aa0f068c40aeaaffe9d36faed1ed9049c55094b989129557', '2025-02-07 05:13:36'),
(12, 'adrianojreinsfelt@gmail.com', 'f78f7ba56a90a64286bb14563366244411ee915c73ed79ffc9f99b805f0c828d', '2025-02-07 05:15:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `data_postagem` timestamp NOT NULL DEFAULT current_timestamp(),
  `categoria` enum('CAMISA','CALÇA','SAPATO','ACESSÓRIO','OUTROS') NOT NULL,
  `genero` enum('Masculino','Feminino') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `imagem`, `id_usuario`, `data_postagem`, `categoria`, `genero`) VALUES
(13, 'TESTE 1', 'TESTE 1', 10.00, '13_2025-02-09-06-00-01.png', 3, '2025-02-09 09:00:01', 'CAMISA', 'Masculino'),
(14, 'TESTE 2', 'TOMATOMA', 123.00, '14_2025-02-09-06-01-21.png', 3, '2025-02-09 09:01:21', 'OUTROS', 'Masculino');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `data_cadastro`) VALUES
(1, 'Adriano', 'aeinsfelt@gmail.com', '$2y$10$aW5AWd5NV3jL7CE/HfsQzOBrndUWINmMny34CBazdR4ZI1WklXV6W', '2025-02-06 01:11:33'),
(2, 'Adriano', 'adriano@gmail.com', '$2y$10$420Ibdy1rO2ckHZ5fT3PH.RzIlBPeWsxsqQ1POw68H/1YYpQJgGw6', '2025-02-06 02:39:59'),
(3, 'AJ DEV', 'adrianojreinsfelt@gmail.com', '$2y$10$No4bRMV82FTSmvUaVyLteuLNpO1MbhqAWL6nSU4KzNNBWaqilcwWm', '2025-02-07 01:21:22'),
(4, 'Adriano 1', 'adrianojreinsfelt@outlook.com', '$2y$10$MsrJMypxcZ.JS8Y9FyOAgOxBIw4FfyP1UXN5VPAYA8HpiotfgFJfq', '2025-02-07 03:43:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_produto` int(11) DEFAULT NULL,
  `data_venda` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pendente','aprovado','enviado','entregue') DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_produto` (`id_produto`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendas_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
