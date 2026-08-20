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
        $sql = 'SELECT id, nome, email, senha
                FROM tecnicos
                WHERE email = :email
                LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'email' => $email
        ]);

        $tecnico = $stmt->fetch();

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

    <title>Login | ARLINK</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main>
        <section>
            <h1>ARLINK</h1>
            <p>Gestão de manutenções de ar-condicionado</p>

            <?php if ($erro !== ''): ?>
                <p role="alert">
                    <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div>
                    <label for="email">E-mail</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                        autocomplete="email"
                        required
                    >
                </div>

                <div>
                    <label for="senha">Senha</label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit">Entrar</button>
            </form>
        </section>
    </main>
</body>
</html>