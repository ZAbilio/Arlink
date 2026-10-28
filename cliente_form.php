<?php
require_once 'includes/config.php';
require_once 'includes/sessao.php';
require_once 'includes/conexao.php';
$id=$_GET['id']??0; $id=(int)$id; $editar=$id>0;
$titulo=$editar?'Editar Cliente':'Novo Cliente';
$d=['nome'=>'','telefone'=>'','email'=>'','endereco'=>'','cidade'=>'']; $erro='';
if ($editar) {
  $st=mysqli_prepare($conexao,"SELECT * FROM clientes WHERE id=? LIMIT 1");
  mysqli_stmt_bind_param($st,'i',$id); mysqli_stmt_execute($st);
  $d=mysqli_fetch_assoc(mysqli_stmt_get_result($st));
  if (!$d) { header('Location: clientes.php'); exit; }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && !validar_formulario()) {
  http_response_code(403); exit('Formulario expirado. Atualize a pagina e tente novamente.');
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $d['nome']=trim($_POST['nome']??''); $d['telefone']=trim($_POST['telefone']??'');
  $d['email']=trim($_POST['email']??''); $d['endereco']=trim($_POST['endereco']??'');
  $d['cidade']=trim($_POST['cidade']??'');
  if (!$d['nome']) $erro='Nome obrigatório.';
  elseif (!$d['telefone']) $erro='Telefone obrigatório.';
  if (!$erro) $erro = validar_dados($d, ['nome'=>100,'telefone'=>20,'email'=>100,'endereco'=>200,'cidade'=>80]);
  if (!$erro) {
    if ($editar) {
      $st=mysqli_prepare($conexao,"UPDATE clientes SET nome=?,telefone=?,email=?,endereco=?,cidade=? WHERE id=?");
      mysqli_stmt_bind_param($st,'sssssi',$d['nome'],$d['telefone'],$d['email'],$d['endereco'],$d['cidade'],$id);
    } else {
      $st=mysqli_prepare($conexao,"INSERT INTO clientes(nome,telefone,email,endereco,cidade) VALUES(?,?,?,?,?)");
      mysqli_stmt_bind_param($st,'sssss',$d['nome'],$d['telefone'],$d['email'],$d['endereco'],$d['cidade']);
    }
    mysqli_stmt_execute($st);
    header('Location: clientes.php?sucesso='.($editar?'editado':'criado')); exit;
  }
}
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo"><div><h1><?php echo $titulo; ?></h1></div><a href="clientes.php" class="btn btn-cinza">← Voltar</a></div>
  <?php if ($erro): ?><div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div><?php endif; ?>
  <div class="card"><div class="card-header"><?php echo $titulo; ?></div><div class="card-body">
    <form method="POST"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(token_formulario()); ?>"><div class="form-grid">
      <div class="form-group"><label>Nome *</label><input type="text" name="nome" value="<?php echo htmlspecialchars($d['nome']); ?>" required autofocus></div>
      <div class="form-group"><label>Telefone *</label><input type="tel" name="telefone" value="<?php echo htmlspecialchars($d['telefone']); ?>" placeholder="(14) 99999-9999" required></div>
      <div class="form-group"><label>E-mail</label><input type="email" name="email" value="<?php echo htmlspecialchars($d['email']); ?>"></div>
      <div class="form-group"><label>Cidade</label><input type="text" name="cidade" value="<?php echo htmlspecialchars($d['cidade']); ?>"></div>
      <div class="form-group full"><label>Endereço</label><input type="text" name="endereco" value="<?php echo htmlspecialchars($d['endereco']); ?>" placeholder="Rua, número, bairro"></div>
    </div>
    <div class="form-acoes"><button type="submit" class="btn btn-verde">💾 Salvar</button><a href="clientes.php" class="btn btn-cinza">Cancelar</a></div>
    </form>
  </div></div>
</div>
<?php require_once 'includes/footer.php'; ?>
