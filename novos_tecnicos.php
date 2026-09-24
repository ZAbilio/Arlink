<?php
require_once 'includes/conexao.php';

$erro = '';

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome     = trim($_POST['nome']);
    $email    = trim($_POST['email']);
    $senha    = $_POST['senha'];
    $telefone = trim($_POST['telefone']);

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "Preencha Nome, E-mail e Senha.";
    } else {

        // Verifica se o e-mail já está cadastrado (a coluna email é UNIQUE)
        $sqlChecagem = "SELECT id FROM tecnicos WHERE email = ?";
        $stmtChecagem = mysqli_prepare($conexao, $sqlChecagem);
        mysqli_stmt_bind_param($stmtChecagem, "s", $email);
        mysqli_stmt_execute($stmtChecagem);
        mysqli_stmt_store_result($stmtChecagem);

        if (mysqli_stmt_num_rows($stmtChecagem) > 0) {
            $erro = "Já existe um técnico cadastrado com esse e-mail.";
        } else {

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO tecnicos (nome, email, senha, telefone)
                    VALUES (?, ?, ?, ?)";

            $stmt = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $nome, $email, $senhaHash, $telefone);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: tecnicos.php");
                exit;
            } else {
                $erro = "Erro ao salvar: " . mysqli_error($conexao);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Novo Técnico - AR Link</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #333;
        }

        .container {
            width: 95%;
            max-width: 700px;
            margin: 40px auto;
        }

        .topo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .topo h1 {
            color: #1e3a5f;
            font-size: 28px;
        }

        .btn-voltar {
            background: #6c757d;
            color: white;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-voltar:hover {
            background: #5c636a;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 30px;
        }

        .campo {
            margin-bottom: 18px;
        }

        .linha {
            display: flex;
            gap: 15px;
        }

        .linha .campo {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: bold;
            color: #1e3a5f;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: #1e3a5f;
        }

        .obrigatorio {
            color: #dc3545;
        }

        .dica {
            font-size: 12px;
            color: #777;
            margin-top: 4px;
        }

        .btn-salvar {
            background: #198754;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-salvar:hover {
            background: #157347;
        }

        .alerta {
            background: #f8d7da;
            color: #842029;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .linha {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="topo">
        <h1>Novo Técnico</h1>
        <a href="tecnicos.php" class="btn-voltar">← Voltar</a>
    </div>

    <div class="card">

        <?php if (!empty($erro)): ?>
            <div class="alerta"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="novos_tecnicos.php">

            <div class="campo">
                <label>Nome <span class="obrigatorio">*</span></label>
                <input type="text" name="nome" placeholder="Ex: João Silva" required>
            </div>

            <div class="linha">

                <div class="campo">
                    <label>E-mail <span class="obrigatorio">*</span></label>
                    <input type="email" name="email" placeholder="Ex: joao@arlink.com" required>
                </div>

                <div class="campo">
                    <label>Telefone</label>
                    <input type="text" name="telefone" placeholder="Ex: (14) 99999-9999">
                </div>

            </div>

            <div class="campo">
                <label>Senha <span class="obrigatorio">*</span></label>
                <input type="password" name="senha" required>
                <div class="dica">Será armazenada de forma criptografada.</div>
            </div>

            <button type="submit" class="btn-salvar">Salvar Técnico</button>

        </form>

    </div>

</div>

</body>
</html>