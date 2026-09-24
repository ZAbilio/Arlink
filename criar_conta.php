<?php
session_start();

if (isset($_SESSION['tecnico_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'includes/conexao.php';

$erro = '';
$nome = '';
$email = '';
$telefone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha    = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preencha nome, e-mail e senha.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas não coincidem.';
    } else {

        // Verifica se o e-mail já está cadastrado
        $sqlChecagem = "SELECT id FROM tecnicos WHERE email = ?";
        $stmtChecagem = mysqli_prepare($conexao, $sqlChecagem);
        mysqli_stmt_bind_param($stmtChecagem, "s", $email);
        mysqli_stmt_execute($stmtChecagem);
        mysqli_stmt_store_result($stmtChecagem);

        if (mysqli_stmt_num_rows($stmtChecagem) > 0) {
            $erro = 'Já existe uma conta cadastrada com esse e-mail.';
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO tecnicos (nome, email, senha, telefone)
                    VALUES (?, ?, ?, ?)";

            $stmt = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $nome, $email, $senhaHash, $telefone);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: login.php?conta_criada=1');
                exit;
            } else {
                $erro = 'Erro ao criar conta: ' . mysqli_error($conexao);
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

    <title>Criar Conta | AR Link</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: white;
            width: 100%;
            max-width: 400px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            padding: 40px 35px;
        }

        .logo {
            width: 56px;
            height: 56px;
            background: #198754;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 18px;
        }

        h1 {
            text-align: center;
            color: #1e3a5f;
            font-size: 22px;
        }

        p.subtitulo {
            text-align: center;
            color: #777;
            font-size: 13px;
            margin-top: 4px;
            margin-bottom: 24px;
        }

        .campo {
            margin-bottom: 16px;
        }

        .linha {
            display: flex;
            gap: 12px;
        }

        .linha .campo {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: bold;
            color: #1e3a5f;
        }

        input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: #1e3a5f;
        }

        .campo-senha-wrapper {
            position: relative;
        }

        .campo-senha-wrapper input {
            padding-right: 42px;
        }

        .btn-olho {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            color: #777;
        }

        .btn-criar {
            width: 100%;
            background: #198754;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 6px;
        }

        .btn-criar:hover {
            background: #157347;
        }

        .alerta {
            background: #f8d7da;
            color: #842029;
            padding: 11px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
            text-align: center;
        }

        .voltar {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
        }

        .voltar a {
            color: #198754;
            font-weight: bold;
            text-decoration: none;
        }

        .voltar a:hover {
            text-decoration: underline;
        }

        @media (max-width: 420px) {
            .linha {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>

<div class="card">

    <div class="logo">❄️</div>

    <h1>Criar Conta</h1>
    <p class="subtitulo">Cadastre-se para acessar o AR Link</p>

    <?php if ($erro !== ''): ?>
        <div class="alerta"><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form action="criar_conta.php" method="POST">

        <div class="campo">
            <label for="nome">Nome completo</label>
            <input
                type="text"
                id="nome"
                name="nome"
                value="<?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="campo">
            <label for="email">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <div class="campo">
            <label for="telefone">Telefone</label>
            <input
                type="text"
                id="telefone"
                name="telefone"
                placeholder="Ex: (14) 99999-9999"
                value="<?php echo htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8'); ?>"
            >
        </div>

        <div class="linha">

            <div class="campo">
                <label for="senha">Senha</label>
                <div class="campo-senha-wrapper">
                    <input type="password" id="senha" name="senha" minlength="6" required>
                    <button type="button" class="btn-olho" onclick="alternarSenha('senha', 'olho1')" id="olho1">👁️</button>
                </div>
            </div>

            <div class="campo">
                <label for="confirmar_senha">Confirmar senha</label>
                <div class="campo-senha-wrapper">
                    <input type="password" id="confirmar_senha" name="confirmar_senha" minlength="6" required>
                    <button type="button" class="btn-olho" onclick="alternarSenha('confirmar_senha', 'olho2')" id="olho2">👁️</button>
                </div>
            </div>

        </div>

        <button type="submit" class="btn-criar">CRIAR CONTA</button>

    </form>

    <div class="voltar">
        Já tem conta? <a href="login.php">Fazer login</a>
    </div>

</div>

<script>
    function alternarSenha(idCampo, idBotao) {
        const campo = document.getElementById(idCampo);
        const botao = document.getElementById(idBotao);

        if (campo.type === 'password') {
            campo.type = 'text';
            botao.textContent = '🙈';
        } else {
            campo.type = 'password';
            botao.textContent = '👁️';
        }
    }
</script>

</body>
</html>