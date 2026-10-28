<?php ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($titulo) ? htmlspecialchars($titulo).' | ARLINK' : 'ARLINK'; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <div class="navbar-brand">❄️ ARLINK</div>
  <div class="navbar-menu">
    <?php
    $pg = basename($_SERVER['PHP_SELF']);
    $links = [
      'dashboard.php'  => 'Dashboard',
      'clientes.php'   => 'Clientes',
      'aparelhos.php'  => 'Aparelhos',
      'manutencoes.php'=> 'Manutenções',
      'tecnicos.php'   => 'Técnicos',
      'busca.php'      => '🔍 Busca',
    ];
    foreach ($links as $url => $label):
      $ativo = $pg === $url ? 'ativo' : '';
    ?>
    <a href="<?php echo BASE_URL.$url; ?>" class="<?php echo $ativo; ?>"><?php echo $label; ?></a>
    <?php endforeach; ?>
  </div>
  <div class="navbar-user">
    <span>👤 <?php echo htmlspecialchars($_SESSION['tecnico_nome'] ?? ''); ?></span>
    <form action="<?php echo BASE_URL; ?>logout.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(token_formulario()); ?>">
      <button type="submit" class="btn-logout" style="background:transparent;cursor:pointer;font:inherit">Sair</button>
    </form>
  </div>
</nav>
