<?php
require_once 'includes/config.php'; require_once 'includes/sessao.php'; require_once 'includes/conexao.php';
$id=(int)($_GET['id']??0); $editar=$id>0; $titulo=$editar?'Editar Técnico':'Novo Técnico'; $erro='';
$d=['nome'=>'','email'=>'','telefone'=>''];
if ($editar){
  $st=mysqli_prepare($conexao,"SELECT id,nome,email,telefone FROM tecnicos WHERE id=? LIMIT 1");
  mysqli_stmt_bind_param($st,'i',$id); mysqli_stmt_execute($st);
  $d=mysqli_fetch_assoc(mysqli_stmt_get_result($st));
  if (!$d){header('Location: tecnicos.php');exit;}
}
if ($_SERVER['REQUEST_METHOD']==='POST' && !validar_formulario()) {
  http_response_code(403); exit('Formulario expirado. Atualize a pagina e tente novamente.');
}
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $d['nome']=trim($_POST['nome']??''); $d['email']=trim($_POST['email']??''); $d['telefone']=trim($_POST['telefone']??'');
  $ns=trim($_POST['senha']??'');
  if (!$d['nome']) $erro='Nome obrigatório.';
  elseif (!$d['email']) $erro='E-mail obrigatório.';
  elseif (!filter_var($d['email'],FILTER_VALIDATE_EMAIL)) $erro='E-mail inválido.';
  elseif (!$editar&&!$ns) $erro='Senha obrigatória.';
  elseif ($ns!==''&&strlen($ns)<6) $erro='Senha mínimo 6 caracteres.';
  if (!$erro) $erro = validar_dados($d, ['nome'=>100,'telefone'=>20,'email'=>100]);
  if (!$erro && strlen($ns) > 72) $erro = 'Senha deve ter no maximo 72 bytes.';
  if (!$erro){
    $chk=mysqli_prepare($conexao,"SELECT id FROM tecnicos WHERE email=? AND id!=?");
    mysqli_stmt_bind_param($chk,'si',$d['email'],$id); mysqli_stmt_execute($chk);
    if (mysqli_num_rows(mysqli_stmt_get_result($chk))>0) $erro='E-mail já cadastrado.';
  }
  if (!$erro){
    if ($editar){
      if ($ns){$hash=password_hash($ns,PASSWORD_DEFAULT);$st=mysqli_prepare($conexao,"UPDATE tecnicos SET nome=?,email=?,telefone=?,senha=? WHERE id=?");mysqli_stmt_bind_param($st,'ssssi',$d['nome'],$d['email'],$d['telefone'],$hash,$id);}
      else{$st=mysqli_prepare($conexao,"UPDATE tecnicos SET nome=?,email=?,telefone=? WHERE id=?");mysqli_stmt_bind_param($st,'sssi',$d['nome'],$d['email'],$d['telefone'],$id);}
    } else {
      $hash=password_hash($ns,PASSWORD_DEFAULT);
      $st=mysqli_prepare($conexao,"INSERT INTO tecnicos(nome,email,telefone,senha) VALUES(?,?,?,?)");
      mysqli_stmt_bind_param($st,'ssss',$d['nome'],$d['email'],$d['telefone'],$hash);
    }
    mysqli_stmt_execute($st);
    if ($editar && $id === (int)$_SESSION['tecnico_id']) $_SESSION['tecnico_nome'] = $d['nome'];
    header('Location: tecnicos.php?sucesso='.($editar?'editado':'criado')); exit;
  }
}
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo"><div><h1><?php echo $titulo; ?></h1></div><a href="tecnicos.php" class="btn btn-cinza">← Voltar</a></div>
  <?php if ($erro): ?><div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div><?php endif; ?>
  <div class="card"><div class="card-header"><?php echo $titulo; ?></div><div class="card-body">
    <form method="POST"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(token_formulario()); ?>"><div class="form-grid">
      <div class="form-group"><label>Nome *</label><input type="text" name="nome" value="<?php echo htmlspecialchars($d['nome']); ?>" required autofocus></div>
      <div class="form-group"><label>E-mail * (login)</label><input type="email" name="email" value="<?php echo htmlspecialchars($d['email']); ?>" required></div>
      <div class="form-group"><label>Telefone</label><input type="tel" name="telefone" value="<?php echo htmlspecialchars($d['telefone']??''); ?>" placeholder="(14) 99999-9999"></div>
      <div class="form-group"><label><?php echo $editar?'Nova senha (em branco = não altera)':'Senha *'; ?></label>
        <input type="password" name="senha" placeholder="Mínimo 6 caracteres" <?php echo $editar?'':'required'; ?>>
      </div>
    </div>
    <div class="form-acoes"><button type="submit" class="btn btn-verde">💾 Salvar</button><a href="tecnicos.php" class="btn btn-cinza">Cancelar</a></div>
    </form>
  </div></div>
</div>
<?php require_once 'includes/footer.php'; ?>
