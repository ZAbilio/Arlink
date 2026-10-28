<?php
require_once 'includes/config.php'; require_once 'includes/sessao.php'; require_once 'includes/conexao.php';
$id=(int)($_GET['id']??0); $cid=(int)($_GET['cliente_id']??0); $editar=$id>0;
$titulo=$editar?'Editar Aparelho':'Novo Aparelho'; $erro='';
$d=['cliente_id'=>$cid,'marca'=>'','modelo'=>'','numero_serie'=>'','capacidade_btu'=>'','tipo'=>'','status'=>'em_dia','proxima_manutencao'=>'','observacoes'=>''];
$cls=mysqli_query($conexao,"SELECT id,nome FROM clientes ORDER BY nome");
if ($editar){$st=mysqli_prepare($conexao,"SELECT * FROM aparelhos WHERE id=? LIMIT 1");mysqli_stmt_bind_param($st,'i',$id);mysqli_stmt_execute($st);$d=mysqli_fetch_assoc(mysqli_stmt_get_result($st));if(!$d){header('Location: aparelhos.php');exit;}}
if ($_SERVER['REQUEST_METHOD']==='POST' && !validar_formulario()) {
  http_response_code(403); exit('Formulario expirado. Atualize a pagina e tente novamente.');
}
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $d['cliente_id']=(int)($_POST['cliente_id']??0); $d['marca']=trim($_POST['marca']??'');
  $d['modelo']=trim($_POST['modelo']??''); $d['numero_serie']=trim($_POST['numero_serie']??'');
  $d['capacidade_btu']=trim($_POST['capacidade_btu']??''); $d['tipo']=trim($_POST['tipo']??'');
  $d['status']=trim($_POST['status']??'em_dia'); $d['proxima_manutencao']=trim($_POST['proxima_manutencao']??'');
  $d['observacoes']=trim($_POST['observacoes']??'');
  if (!$d['cliente_id']) $erro='Selecione um cliente.';
  elseif (!in_array($d['status'], ['em_dia','atencao','urgente'], true)) $erro='Status invalido.';
  elseif (!$d['marca']) $erro='Marca obrigatória.';
  elseif (!$d['modelo']) $erro='Modelo obrigatório.';
  if (!$erro) $erro = validar_dados($d, ['marca'=>50,'modelo'=>100,'numero_serie'=>100,'capacidade_btu'=>20,'tipo'=>30,'status'=>20,'observacoes'=>16000]);
  if (!$erro && !registro_existe($conexao, 'clientes', $d['cliente_id'])) $erro = 'Cliente inexistente.';
  if (!$erro){
    $pm=$d['proxima_manutencao']?:null;
    // O numero de letras em bind_param deve ser igual ao numero de valores.
    // i = identificador inteiro; s = texto e data.
    if ($editar){
      $st=mysqli_prepare($conexao,"UPDATE aparelhos SET cliente_id=?,marca=?,modelo=?,numero_serie=?,capacidade_btu=?,tipo=?,status=?,proxima_manutencao=?,observacoes=? WHERE id=?");
      mysqli_stmt_bind_param($st,'issssssssi',$d['cliente_id'],$d['marca'],$d['modelo'],$d['numero_serie'],$d['capacidade_btu'],$d['tipo'],$d['status'],$pm,$d['observacoes'],$id);
    } else {
      $st=mysqli_prepare($conexao,"INSERT INTO aparelhos(cliente_id,marca,modelo,numero_serie,capacidade_btu,tipo,status,proxima_manutencao,observacoes) VALUES(?,?,?,?,?,?,?,?,?)");
      mysqli_stmt_bind_param($st,'issssssss',$d['cliente_id'],$d['marca'],$d['modelo'],$d['numero_serie'],$d['capacidade_btu'],$d['tipo'],$d['status'],$pm,$d['observacoes']);
    }
    mysqli_stmt_execute($st);
    header('Location: aparelhos.php?sucesso='.($editar?'editado':'criado')); exit;
  }
}
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo"><div><h1><?php echo $titulo; ?></h1></div><a href="aparelhos.php" class="btn btn-cinza">← Voltar</a></div>
  <?php if ($erro): ?><div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div><?php endif; ?>
  <div class="card"><div class="card-header"><?php echo $titulo; ?></div><div class="card-body">
    <form method="POST"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(token_formulario()); ?>"><div class="form-grid">
      <div class="form-group full"><label>Cliente *</label>
        <select name="cliente_id" required>
          <option value="">— Selecione —</option>
          <?php while ($c=mysqli_fetch_assoc($cls)): $sel=$c['id']==$d['cliente_id']?'selected':''; ?>
          <option value="<?php echo $c['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($c['nome']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group"><label>Marca *</label><input type="text" name="marca" value="<?php echo htmlspecialchars($d['marca']); ?>" placeholder="Elgin, Midea, LG..." required></div>
      <div class="form-group"><label>Modelo *</label><input type="text" name="modelo" value="<?php echo htmlspecialchars($d['modelo']); ?>" required></div>
      <div class="form-group"><label>Número de série</label><input type="text" name="numero_serie" value="<?php echo htmlspecialchars($d['numero_serie']); ?>"></div>
      <div class="form-group"><label>Capacidade (BTU)</label><input type="text" name="capacidade_btu" value="<?php echo htmlspecialchars($d['capacidade_btu']); ?>" placeholder="9000, 12000..."></div>
      <div class="form-group"><label>Tipo</label>
        <select name="tipo">
          <option value="">— Selecione —</option>
          <?php foreach(['Split Hi Wall','Split Cassete','Split Piso Teto','Janela','Portátil'] as $t): $sel=$d['tipo']===$t?'selected':''; ?>
          <option value="<?php echo $t; ?>" <?php echo $sel; ?>><?php echo $t; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Status</label>
        <select name="status">
          <?php foreach(['em_dia'=>'Em dia','atencao'=>'Atenção','urgente'=>'Urgente'] as $v=>$l): $sel=$d['status']===$v?'selected':''; ?>
          <option value="<?php echo $v; ?>" <?php echo $sel; ?>><?php echo $l; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Próxima manutenção</label><input type="date" name="proxima_manutencao" value="<?php echo htmlspecialchars($d['proxima_manutencao']??''); ?>"></div>
      <div class="form-group full"><label>Observações</label><textarea name="observacoes"><?php echo htmlspecialchars($d['observacoes']??''); ?></textarea></div>
    </div>
    <div class="form-acoes"><button type="submit" class="btn btn-verde">💾 Salvar</button><a href="aparelhos.php" class="btn btn-cinza">Cancelar</a></div>
    </form>
  </div></div>
</div>
<?php require_once 'includes/footer.php'; ?>
