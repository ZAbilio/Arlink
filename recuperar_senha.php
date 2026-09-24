<?php
require_once 'includes/conexao.php';

$erro = '';
$sucesso = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email     = trim($_POST['email'] ?? '');
    $novaSenha = $_POST['nova_senha'] ?? '';

    if ($email === '' || $novaSenha === '') {
        $erro = 'Preencha o e-mail e a nova senha.';
    } elseif (strlen($novaSenha) < 6) {
        $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
    } else {
        $sql = "SELECT id FROM tecnicos WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $tecnico = mysqli_fetch_assoc($resultado);

        if (!$tecnico) {
            $erro = 'Não encontramos nenhum técnico com esse e-mail.';
        } else {
            $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

            $sqlUpdate = "UPDATE tecnicos SET senha = ? WHERE id = ?";
            $stmtUpdate = mysqli_prepare($conexao, $sqlUpdate);
            mysqli_stmt_bind_param($stmtUpdate, "si", $novaSenhaHash, $tecnico['id']);
            mysqli_stmt_execute($stmtUpdate);

            $sucesso = 'Senha atualizada com sucesso! Você já pode fazer login com a nova senha.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Recuperar Senha | AR Link</title>

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
            margin-bottom: 18px;
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

        .btn-confirmar {
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

        .btn-confirmar:hover {
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

        .sucesso {
            background: #d1e7dd;
            color: #0f5132;
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
        }

        .voltar a {
            color: #1e3a5f;
            text-decoration: none;
            font-weight: bold;
        }

        .voltar a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="card">

    <div class="logo">🔑</div>

    <h1>Recuperar Senha</h1>
    <p class="subtitulo">Informe seu e-mail cadastrado e defina uma nova senha</p>

    <?php if ($erro !== ''): ?>
        <div class="alerta"><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if ($sucesso !== ''): ?>
        <div class="sucesso"><?php echo htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if ($sucesso === ''): ?>
        <form action="recuperar_senha.php" method="POST">

            <div class="campo">
                <label for="email">E-mail cadastrado</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                    required
                >
            </div>

            <div class="campo">
                <label for="nova_senha">Nova senha</label>
                <input
                    type="password"
                    id="nova_senha"
                    name="nova_senha"
                    minlength="6"
                    required
                >
            </div>

            <button type="submit" class="btn-confirmar">Redefinir Senha</button>

        </form>
    <?php endif; ?>

    <div class="voltar">
        <a href="login.php">← Voltar para o login</a>
    </div>

</div>

</body>
</html>