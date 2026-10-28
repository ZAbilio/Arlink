<?php
require_once 'includes/config.php'; require_once 'includes/sessao.php'; require_once 'includes/conexao.php';
$titulo='Manutenções'; $sucesso=$_GET['sucesso']??'';
$res=mysqli_query($conexao,
  "SELECT m.id,m.data_atendimento,m.tipo_servico,m.valor_cobrado,a.marca,a.modelo,c.nome AS cn,t.nome AS tn
   FROM manutencoes m JOIN aparelhos a ON a.id=m.aparelho_id JOIN clientes c ON c.id=a.cliente_id JOIN tecnicos t ON t.id=m.tecnico_id
   ORDER BY m.data_atendimento DESC,m.criado_em DESC");
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo">
    <div><h1>Manutenções</h1><p>Histórico de todos os atendimentos.</p></div>
    <a href="manutencao_form.php" class="btn btn-verde">+ Registrar Manutenção</a>
  </div>
  <?php if ($sucesso==='criado'): ?><div class="alerta alerta-sucesso">✅ Manutenção registrada!</div>
  <?php elseif ($sucesso==='excluido'): ?><div class="alerta alerta-sucesso">✅ Manutenção excluída!</div><?php endif; ?>
  <div class="card">
    <div class="contador"><?php echo mysqli_num_rows($res); ?> manutenção(ões)</div>
    <div class="tabela-container">
      <?php if (mysqli_num_rows($res)>0): ?>
      <table><thead><tr><th>Data</th><th>Aparelho</th><th>Cliente</th><th>Tipo</th><th>Técnico</th><th>Valor</th><th>Ações</th></tr></thead>
      <tbody>
      <?php while ($m=mysqli_fetch_assoc($res)): ?>
        <tr>
          <td><?php echo date('d/m/Y',strtotime($m['data_atendimento'])); ?></td>
          <td><?php echo htmlspecialchars($m['marca'].' '.$m['modelo']); ?></td>
          <td><?php echo htmlspecialchars($m['cn']); ?></td>
          <td><span class="badge badge-azul"><?php echo htmlspecialchars($m['tipo_servico']); ?></span></td>
          <td><?php echo htmlspecialchars($m['tn']); ?></td>
          <td><?php echo $m['valor_cobrado']!==null?'R$ '.number_format($m['valor_cobrado'],2,',','.'):'-'; ?></td>
          <td class="acoes">
            <a href="manutencao_detalhe.php?id=<?php echo $m['id']; ?>" class="btn btn-cinza">Ver</a>
            <form action="manutencao_excluir.php" method="POST" onsubmit="return confirm('Excluir esta manutencao?')" class="form-excluir"><input type="hidden" name="id" value="<?php echo $m['id']; ?>"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(token_formulario()); ?>"><button type="submit" class="btn btn-vermelho">Excluir</button></form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody></table>
      <?php else: ?><div class="vazio"><h3>Nenhuma manutenção</h3><p>Clique em "+ Registrar".</p></div><?php endif; ?>
    </div>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
