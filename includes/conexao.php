<?php
$host = 'localhost';
$db = 'arlink_db';
$user = 'root';
$pass = '';

$conexao = mysqli_connect($host,$user, $pass);

mysqli_query($conexao,"create database if not exists arlink_db");

mysqli_select_db($conexao, $db);

$sql = "CREATE TABLE IF NOT EXISTS clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    endereco VARCHAR(200),
    cidade VARCHAR(80),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conexao, $sql) or die("Erro na tabela clientes: " . mysqli_error($conexao));






$sql = "CREATE TABLE IF NOT EXISTS tecnicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conexao, $sql) or die("Erro na tabela tecnicos: " . mysqli_error($conexao));






$sql = "CREATE TABLE IF NOT EXISTS aparelhos (
    id INT PRIMARY KEY AUTO_INCREMENT,
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
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
)";

mysqli_query($conexao, $sql) or die("Erro na tabela aparelhos: " . mysqli_error($conexao));



$sql = "CREATE TABLE IF NOT EXISTS manutencoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
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
    FOREIGN KEY (aparelho_id) REFERENCES aparelhos(id),
    FOREIGN KEY (tecnico_id) REFERENCES tecnicos(id)
)";

mysqli_query($conexao, $sql) or die("Erro na tabela manutencoes: " . mysqli_error($conexao));




$sql = "INSERT IGNORE INTO tecnicos
    (nome, email, senha, telefone)
VALUES
    (
        'Admin',
        'admin@arlink.com',
        '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLtChG',
        '(14) 99999-9999'
    )";

mysqli_query($conexao, $sql) or die("Erro ao cadastrar administrador: " . mysqli_error($conexao));

$sql = "INSERT INTO clientes
(nome, telefone, email, endereco, cidade)
VALUES
('João da Silva', '(14) 99123-4567', 'joao.silva@email.com', 'Rua das Flores, 120', 'Bauru'),
('Maria Oliveira', '(14) 99234-5678', 'maria.oliveira@email.com', 'Avenida Brasil, 450', 'Bauru'),
('Carlos Santos', '(14) 99345-6789', 'carlos.santos@email.com', 'Rua São Paulo, 85', 'Bauru'),
('Ana Paula Souza', '(14) 99456-7890', 'ana.souza@email.com', 'Rua das Palmeiras, 230', 'Bauru'),
('Roberto Almeida', '(14) 99567-8901', 'roberto.almeida@email.com', 'Avenida Nações Unidas, 1020', 'Bauru'),
('Fernanda Costa', '(14) 99678-9012', 'fernanda.costa@email.com', 'Rua Bahia, 315', 'Bauru'),
('Marcos Pereira', '(14) 99789-0123', 'marcos.pereira@email.com', 'Rua Minas Gerais, 670', 'Bauru'),
('Juliana Martins', '(14) 99890-1234', 'juliana.martins@email.com', 'Avenida Getúlio Vargas, 890', 'Bauru'),
('Ricardo Ferreira', '(14) 99901-2345', 'ricardo.ferreira@email.com', 'Rua Pernambuco, 145', 'Bauru'),
('Patrícia Rodrigues', '(14) 99012-3456', 'patricia.rodrigues@email.com', 'Rua Goiás, 520', 'Bauru')";

mysqli_query($conexao, $sql) or die(mysqli_error($conexao));


$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES => false,
];



try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>