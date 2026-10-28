<?php
require_once 'includes/config.php'; require_once 'includes/sessao.php'; require_once 'includes/conexao.php';
$titulo='Aparelhos'; $cid=(int)($_GET['cliente_id']??0); $sucesso=$_GET['sucesso']??'';
$where=$cid>0?"WHERE a.cliente_id=$cid":'';
$res=mysqli_query($conexao,"SELECT a.*,c.nome AS cn FROM aparelhos a JOIN clientes c ON c.id=a.cliente_id $where ORDER BY c.nome,a.marca");
$nc='';
if ($cid>0){$r=mysqli_query($conexao,"SELECT nome FROM clientes WHERE id=$cid LIMIT 1");$nc=mysqli_fetch_row($r)[0]??'';}
function bs($s){$m=['em_dia'=>['Em dia','badge-verde'],'atencao'=>['Atenção','badge-amarelo'],'urgente'=>['Urgente','badge-vermelho']];$d=$m[$s]??[$s,'badge-cinza'];return '<span class="badge '.$d[1].'">'.htmlspecialchars($d[0]).'</span>';}
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo">
    <div><h1>Aparelhos<?php echo $nc?' — '.htmlspecialchars($nc):''; ?></h1><p>Ar-condicionados cadastrados.</p></div>
    <div class="acoes">
      <?php if ($cid>0): ?><a href="aparelhos.php" class="btn btn-cinza">Ver todos</a><?php endif; ?>
      <a href="aparelho_form.php<?php echo $cid?"?cliente_id=$cid":''; ?>" class="btn btn-verde">+ Novo Aparelho</a>
    </div>
  </div>
  <?php if ($sucesso==='criado'): ?><div class="alerta alerta-sucesso">✅ Aparelho cadastrado!</div>
  <?php elseif ($sucesso==='editado'): ?><div class="alerta alerta-sucesso">✅ Aparelho atualizado!</div>
  <?php elseif ($sucesso==='excluido'): ?><div class="alerta alerta-sucesso">✅ Aparelho excluído!</div><?php endif; ?>
  <div class="card">
    <div class="contador"><?php echo mysqli_num_rows($res); ?> aparelho(s)</div>
    <div class="tabela-container">
      <?php if (mysqli_num_rows($res)>0): ?>
      <table><thead><tr><th>#</th><th>Marca/Modelo</th><th>Cliente</th><th>Tipo</th><th>BTU</th><th>Nº Série</th><th>Status</th><th>Próx. Manutenção</th><th>Ações</th></tr></thead>
      <tbody>
      <?php while ($a=mysqli_fetch_assoc($res)): ?>
        <tr>
          <td><?php echo $a['id']; ?></td>
          <td><strong><?php echo htmlspecialchars($a['marca']); ?></strong><br><small><?php echo htmlspecialchars($a['modelo']); ?></small></td>
          <td><?php echo htmlspecialchars($a['cn']); ?></td>
          <td><?php echo $a['tipo']?htmlspecialchars($a['tipo']):'-'; ?></td>
          <td><?php echo $a['capacidade_btu']?htmlspecialchars($a['capacidade_btu']):'-'; ?></td>
          <td><?php echo $a['numero_serie']?htmlspecialchars($a['numero_serie']):'-'; ?></td>
          <td><?php echo bs($a['status']); ?></td>
          <td><?php if ($a['proxima_manutencao']){$d=strtotime($a['proxima_manutencao']);$diff=($d-strtotime('today'))/86400;$cor=$diff<0?'color:red':($diff<=7?'color:orange':'');echo '<span style="'.$cor.'">'.date('d/m/Y',$d).'</span>';}else echo '-'; ?></td>
          <td class="acoes">
            <a href="historico.php?aparelho_id=<?php echo $a['id']; ?>" class="btn btn-cinza">Histórico</a>
            <a href="aparelho_form.php?id=<?php echo $a['id']; ?>" class="btn btn-primario">Editar</a>
            <form action="aparelho_excluir.php" method="POST" onsubmit="return confirm('Excluir este aparelho e TODO seu historico?')" class="form-excluir"><input type="hidden" name="id" value="<?php echo $a['id']; ?>"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(token_formulario()); ?>"><button type="submit" class="btn btn-vermelho">Excluir</button></form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody></table>
      <?php else: ?><div class="vazio"><h3>Nenhum aparelho</h3><p>Clique em "+ Novo Aparelho".</p></div><?php endif; ?>
    </div>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
