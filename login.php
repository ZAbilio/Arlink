<?php
require_once 'includes/config.php';
session_start();
if (isset($_SESSION['tecnico_id'])) { header('Location: dashboard.php'); exit; }
require_once 'includes/conexao.php';
require_once 'includes/csrf.php';
$erro = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !validar_formulario()) {
    http_response_code(403); exit('Formulario expirado. Atualize a pagina e tente novamente.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    if (strlen($email) > 100 || strlen($senha) > 72 || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $erro = 'E-mail ou senha invalidos.'; }
    elseif (!$email || !$senha) { $erro = 'Preencha e-mail e senha.'; }
    else {
        $stmt = mysqli_prepare($conexao, "SELECT id,nome,email,senha FROM tecnicos WHERE email=? LIMIT 1");
        mysqli_stmt_bind_param($stmt,'s',$email);
        mysqli_stmt_execute($stmt);
        $t = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if ($t && password_verify($senha, $t['senha'])) {
            session_regenerate_id(true);
            $_SESSION['tecnico_id']   = $t['id'];
            $_SESSION['tecnico_nome'] = $t['nome'];
            header('Location: dashboard.php'); exit;
        }
        $erro = 'E-mail ou senha incorretos.';
    }
}
?>
<!DOCTYPE html><html lang="pt-BR"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login | ARLINK</title>
<link rel="stylesheet" href="assets/css/style.css">
</head><body>
<div class="login-page"><div class="login-box">
  <div class="login-logo"><h1>❄️ ARLINK</h1><p>Gestão de manutenções de ar-condicionado</p></div>
  <?php if ($erro): ?><div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div><?php endif; ?>
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(token_formulario()); ?>">
    <div class="form-group" style="margin-bottom:16px">
      <label>E-mail</label>
      <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="seu@email.com" required autofocus>
    </div>
    <div class="form-group" style="margin-bottom:24px">
      <label>Senha</label>
      <input type="password" name="senha" placeholder="••••••••" required>
    </div>
    <button type="submit" class="btn btn-primario">Entrar no sistema</button>
  </form>
  <p style="text-align:center;margin-top:20px;font-size:12px;color:#aaa">ETEC Rodrigues de Abreu &mdash; TCC 2026</p>
</div></div>
</body></html>
