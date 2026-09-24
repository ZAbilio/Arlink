<?php
session_start();

if (isset($_SESSION['tecnico_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'includes/conexao.php';

$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha o e-mail e a senha.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } else {
        $sql = "SELECT id, nome, email, senha
                FROM tecnicos
                WHERE email = ?
                LIMIT 1";

        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $tecnico = mysqli_fetch_assoc($resultado);

        if ($tecnico && password_verify($senha, $tecnico['senha'])) {
            session_regenerate_id(true);

            $_SESSION['tecnico_id'] = $tecnico['id'];
            $_SESSION['tecnico_nome'] = $tecnico['nome'];
            $_SESSION['tecnico_email'] = $tecnico['email'];

            header('Location: dashboard.php');
            exit;
        }

        $erro = 'E-mail ou senha incorretos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | AR Link</title>

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

        .login-card {
            background: white;
            width: 100%;
            max-width: 380px;
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

        .login-card h1 {
            text-align: center;
            color: #1e3a5f;
            font-size: 24px;
            letter-spacing: 0.5px;
        }

        .login-card p.subtitulo {
            text-align: center;
            color: #777;
            font-size: 13px;
            margin-top: 4px;
            margin-bottom: 24px;
        }

        .campo {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .campo-senha-wrapper {
            position: relative;
        }

        input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .campo-senha-wrapper input {
            padding-right: 42px;
        }

        input:focus {
            outline: none;
            border-color: #1e3a5f;
        }

        .btn-olho {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 17px;
            padding: 4px;
            color: #777;
        }

        .esqueci {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 20px;
        }

        .esqueci a {
            font-size: 12px;
            color: #1e3a5f;
            text-decoration: none;
        }

        .esqueci a:hover {
            text-decoration: underline;
        }

        .btn-entrar {
            width: 100%;
            background: #198754;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 4px;
        }

        .btn-entrar:hover {
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

        .criar-conta {
            text-align: center;
            margin-top: 22px;
            font-size: 13px;
            color: #666;
        }

        .criar-conta a {
            color: #198754;
            font-weight: bold;
            text-decoration: none;
        }

        .criar-conta a:hover {
            text-decoration: underline;
        }

        .credenciais-teste {
            margin-top: 22px;
            background: #fff3cd;
            border: 1px solid #ffe69c;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 12px;
            color: #664d03;
            text-align: center;
        }

        .credenciais-teste strong {
            display: block;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>

<div class="login-card">

    <div class="logo">❄️</div>

    <h1>AR Link</h1>
    <p class="subtitulo">Gestão de manutenções de ar-condicionado</p>

    <?php if ($erro !== ''): ?>
        <div class="alerta">
            <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">

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
            <label for="senha">Senha</label>
            <div class="campo-senha-wrapper">
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    required
                >
                <button type="button" class="btn-olho" onclick="alternarSenha()" id="btnOlho">👁️</button>
            </div>
        </div>

        <div class="esqueci">
            <a href="recuperar_senha.php">Esqueci minha senha</a>
        </div>

        <button type="submit" class="btn-entrar">ENTRAR</button>

    </form>

    <div class="criar-conta">
        Não tem conta? <a href="criar_conta.php">Criar conta</a>
    </div>

    <div class="credenciais-teste">
        <strong>Acesso de teste</strong>
        E-mail: admin@arlink.com<br>
        Senha: admin123
    </div>

</div>

<script>
    function alternarSenha() {
        const campo = document.getElementById('senha');
        const botao = document.getElementById('btnOlho');

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