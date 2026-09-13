CREATE DATABASE IF NOT EXISTS `financas_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `financas_db`;

CREATE TABLE IF NOT EXISTS `configuracoes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `chave` VARCHAR(100) NOT NULL UNIQUE,
  `valor` TEXT NULL,
  `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `configuracoes` (`chave`, `valor`) VALUES
('empresa_nome', 'Gestão Pro'),
('empresa_logo', ''),
('empresa_favicon', ''),
('moeda_simbolo', 'R$'),
('timezone', 'America/Sao_Paulo');

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `senha` VARCHAR(255) NOT NULL,
  `nivel_acesso` ENUM('admin', 'operador') DEFAULT 'operador',
  `status` ENUM('ativo', 'inativo') DEFAULT 'ativo',
  `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Senha padrão: admin123
INSERT IGNORE INTO `usuarios` (`nome`, `email`, `senha`, `nivel_acesso`) VALUES
('Administrador', 'admin@empresa.com', '$2y$10$wT3wYtT9kYQz6A2W/xS6Ue6xZz2X7J.9m7dK9X1u3W6Y8Z0V2X4qS', 'admin');