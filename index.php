<?php
require_once 'includes/conexao.php';

// Contadores rápidos para o dashboard
function contar($conexao, $tabela) {
    $sql = "SELECT COUNT(*) AS total FROM $tabela";
    $resultado = mysqli_query($conexao, $sql);
    if ($resultado) {
        $linha = mysqli_fetch_assoc($resultado);
        return $linha['total'];
    }
    return 0;
}

$totalClientes    = contar($conexao, 'clientes');
$totalAparelhos   = contar($conexao, 'aparelhos');
$totalTecnicos    = contar($conexao, 'tecnicos');
$totalManutencoes = contar($conexao, 'manutencoes');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AR Link - Página Inicial</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f9;
            color: #333;
        }

        /* Cabeçalho / Navbar */
        .navbar {
            background: #1e3a5f;
            color: white;
            padding: 18px 0;
        }

        .navbar-conteudo {
            width: 95%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar-icone {
            width: 42px;
            height: 42px;
            background: #198754;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .navbar h1 {
            font-size: 22px;
            letter-spacing: 0.5px;
        }

        .navbar span {
            display: block;
            font-size: 12px;
            color: #b8c4d4;
            font-weight: normal;
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
            color: white;
            padding: 50px 0 70px;
            text-align: center;
        }

        .hero h2 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 16px;
            color: #cfe0f0;
            max-width: 500px;
            margin: 0 auto;
        }

        /* Container geral */
        .container {
            width: 95%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Cards de estatística (sobrepõem o hero) */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: -45px;
            margin-bottom: 45px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
            padding: 22px;
            text-align: center;
        }

        .stat-numero {
            font-size: 30px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .stat-label {
            font-size: 13px;
            color: #777;
            margin-top: 4px;
        }

        /* Seção de módulos */
        .secao-titulo {
            font-size: 20px;
            color: #1e3a5f;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .modulos {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 60px;
        }

        .modulo-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            padding: 28px 22px;
            text-decoration: none;
            color: #333;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            border-top: 4px solid transparent;
        }

        .modulo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .modulo-icone {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }

        .modulo-clientes .modulo-icone { background: #e7f1ff; }
        .modulo-clientes { border-top-color: #0d6efd; }

        .modulo-aparelhos .modulo-icone { background: #e6f7f0; }
        .modulo-aparelhos { border-top-color: #198754; }

        .modulo-tecnicos .modulo-icone { background: #fff3e0; }
        .modulo-tecnicos { border-top-color: #fd7e14; }

        .modulo-manutencoes .modulo-icone { background: #f3e8fd; }
        .modulo-manutencoes { border-top-color: #6f42c1; }

        .modulo-card h3 {
            font-size: 17px;
            color: #1e3a5f;
            margin-bottom: 6px;
        }

        .modulo-card p {
            font-size: 13px;
            color: #777;
            line-height: 1.4;
        }

        .modulo-seta {
            display: inline-block;
            margin-top: 14px;
            font-size: 13px;
            font-weight: bold;
            color: #1e3a5f;
        }

        /* Rodapé */
        footer {
            text-align: center;
            padding: 25px 0;
            font-size: 13px;
            color: #999;
            border-top: 1px solid #e5e5e5;
        }

        @media (max-width: 900px) {
            .stats,
            .modulos {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 500px) {
            .stats,
            .modulos {
                grid-template-columns: 1fr;
            }

            .hero h2 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div class="navbar-conteudo">
            <div class="navbar-icone">❄️</div>
            <div>
                <h1>AR Link</h1>
                <span>Sistema de Gestão de Manutenção</span>
            </div>
        </div>
    </div>

    <div class="hero">
        <h2>Bem-vindo ao AR Link</h2>
        <p>Gerencie clientes, aparelhos, técnicos e manutenções de ar-condicionado em um só lugar.</p>
    </div>

    <div class="container">

        <div class="stats">

            <div class="stat-card">
                <div class="stat-numero"><?php echo $totalClientes; ?></div>
                <div class="stat-label">Clientes cadastrados</div>
            </div>

            <div class="stat-card">
                <div class="stat-numero"><?php echo $totalAparelhos; ?></div>
                <div class="stat-label">Aparelhos cadastrados</div>
            </div>

            <div class="stat-card">
                <div class="stat-numero"><?php echo $totalTecnicos; ?></div>
                <div class="stat-label">Técnicos cadastrados</div>
            </div>

            <div class="stat-card">
                <div class="stat-numero"><?php echo $totalManutencoes; ?></div>
                <div class="stat-label">Manutenções registradas</div>
            </div>

        </div>

        <div class="secao-titulo">Acesso rápido</div>

        <div class="modulos">

            <a href="clientes.php" class="modulo-card modulo-clientes">
                <div class="modulo-icone">👤</div>
                <h3>Clientes</h3>
                <p>Cadastre e gerencie os dados dos seus clientes.</p>
                <span class="modulo-seta">Acessar →</span>
            </a>

            <a href="aparelhos.php" class="modulo-card modulo-aparelhos">
                <div class="modulo-icone">🌀</div>
                <h3>Aparelhos</h3>
                <p>Controle os aparelhos de ar-condicionado instalados.</p>
                <span class="modulo-seta">Acessar →</span>
            </a>

            <a href="tecnicos.php" class="modulo-card modulo-tecnicos">
                <div class="modulo-icone">🔧</div>
                <h3>Técnicos</h3>
                <p>Gerencie a equipe técnica responsável pelos atendimentos.</p>
                <span class="modulo-seta">Acessar →</span>
            </a>

            <a href="manutencoes.php" class="modulo-card modulo-manutencoes">
                <div class="modulo-icone">📋</div>
                <h3>Manutenções</h3>
                <p>Registre e acompanhe o histórico de manutenções.</p>
                <span class="modulo-seta">Acessar →</span>
            </a>

        </div>

    </div>

    <footer>
        AR Link &copy; <?php echo date('Y'); ?> — Sistema de Gestão de Manutenção de Ar-Condicionado
    </footer>

</body>
</html>