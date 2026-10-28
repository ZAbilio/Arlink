<?php
// Configuracao do banco local do XAMPP.
// Se o MySQL do seu computador usa porta diferente, ajuste somente DB_PORT.
$host = '127.0.0.1';
$usuario = 'root';
$senha = '';
$banco = 'arlink_db';
$porta = 3306;

// No PHP 8.0, habilitar excecoes permite tratar falhas e fazer rollback.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conexao = mysqli_connect($host, $usuario, $senha, $banco, $porta);
    mysqli_set_charset($conexao, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(503);
    // Mensagem amigavel: nao expoe detalhes de conexao no navegador.
    exit('ARLINK: nao foi possivel conectar ao banco. Inicie o MySQL no XAMPP, '
        . 'importe o arquivo arlink_db.sql e confira includes/conexao.php.');
}
