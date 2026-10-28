CREATE DATABASE IF NOT EXISTS arlink_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE arlink_db;

CREATE TABLE IF NOT EXISTS tecnicos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  telefone VARCHAR(20),
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  telefone VARCHAR(20) NOT NULL,
  email VARCHAR(100),
  endereco VARCHAR(200),
  cidade VARCHAR(80),
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS aparelhos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  marca VARCHAR(50) NOT NULL,
  modelo VARCHAR(100) NOT NULL,
  numero_serie VARCHAR(100),
  capacidade_btu VARCHAR(20),
  tipo VARCHAR(30),
  status VARCHAR(20) DEFAULT 'em_dia',
  proxima_manutencao DATE,
  observacoes TEXT,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS manutencoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  aparelho_id INT NOT NULL,
  tecnico_id INT NOT NULL,
  data_atendimento DATE NOT NULL,
  tipo_servico VARCHAR(50) NOT NULL,
  diagnostico TEXT NOT NULL,
  servico_realizado TEXT NOT NULL,
  pecas_utilizadas TEXT,
  valor_cobrado DECIMAL(10,2),
  proxima_visita DATE,
  observacoes_internas TEXT,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (aparelho_id) REFERENCES aparelhos(id) ON DELETE CASCADE,
  FOREIGN KEY (tecnico_id) REFERENCES tecnicos(id)
);

-- Usuario inicial para DEMONSTRACAO LOCAL: admin@arlink.com / admin123
-- Em um computador publico, altere a senha depois do primeiro acesso.
-- Reimportar preserva a senha de um administrador ja existente.
INSERT INTO tecnicos (nome,email,senha,telefone) VALUES
('Administrador','admin@arlink.com','$2y$12$gvNoa2818DdIQaUgFjkoVut/hXcS5hKF68Y/8rul6N0CqCghy4GT.','(14) 99999-9999')
ON DUPLICATE KEY UPDATE email = email;

-- Dados de exemplo ficticios para apresentacao. Importacao repetida nao deve duplicar registros.
-- Clientes de exemplo
INSERT IGNORE INTO clientes (id,nome,telefone,email,endereco,cidade) VALUES
(1,'Joao da Silva','(14) 99123-4567','joao@email.com','Rua das Flores, 120','Bauru'),
(2,'Maria Oliveira','(14) 99234-5678','maria@email.com','Av. Brasil, 450','Bauru'),
(3,'Carlos Santos','(14) 99345-6789','carlos@email.com','Rua Sao Paulo, 85','Bauru');

-- Aparelhos de exemplo
INSERT IGNORE INTO aparelhos (id,cliente_id,marca,modelo,numero_serie,capacidade_btu,tipo,status,proxima_manutencao) VALUES
(1,1,'Elgin','Hi Wall Eco','ELG-2024-001','12000','Split Hi Wall','em_dia',DATE_ADD(CURDATE(), INTERVAL 15 DAY)),
(2,2,'Midea','Liva Inverter','MID-2023-042','9000','Split Hi Wall','atencao',DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
(3,3,'Daikin','FTF12','DAI-2022-118','12000','Split Hi Wall','urgente',DATE_ADD(CURDATE(), INTERVAL 2 DAY));

SET @admin_demo = (SELECT id FROM tecnicos WHERE email='admin@arlink.com');

-- Manutencoes de exemplo
INSERT IGNORE INTO manutencoes (id,aparelho_id,tecnico_id,data_atendimento,tipo_servico,diagnostico,servico_realizado,pecas_utilizadas,valor_cobrado,proxima_visita) VALUES
(1,1,@admin_demo,DATE_SUB(CURDATE(),INTERVAL 80 DAY),'Preventiva','Filtros com acumulo de poeira. Funcionamento normal.','Limpeza completa dos filtros e serpentina.','Nenhuma peca substituida',150.00,DATE_ADD(CURDATE(),INTERVAL 15 DAY)),
(2,2,@admin_demo,DATE_SUB(CURDATE(),INTERVAL 60 DAY),'Corretiva','Capacitor do compressor com falha.','Substituicao do capacitor.','Capacitor 40+5 MFD',280.00,DATE_ADD(CURDATE(),INTERVAL 7 DAY)),
(3,3,@admin_demo,DATE_SUB(CURDATE(),INTERVAL 35 DAY),'Preventiva','Pequeno acumulo de limo.','Higienizacao completa.','Produto higienizante',180.00,DATE_ADD(CURDATE(),INTERVAL 2 DAY));
