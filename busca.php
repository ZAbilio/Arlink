<?php
require_once 'includes/config.php'; require_once 'includes/sessao.php'; require_once 'includes/conexao.php';
$titulo='Busca'; $q=trim($_GET['q']??'');
$cr=$ar=$mr=null;
if ($q!==''){
  $like='%'.$q.'%';
  $st=mysqli_prepare($conexao,"SELECT id,nome,telefone,email,cidade FROM clientes WHERE nome LIKE ? OR telefone LIKE ? OR email LIKE ? ORDER BY nome LIMIT 20");
  mysqli_stmt_bind_param($st,'sss',$like,$like,$like); mysqli_stmt_execute($st); $cr=mysqli_stmt_get_result($st);
  $st2=mysqli_prepare($conexao,"SELECT a.id,a.marca,a.modelo,a.numero_serie,a.status,c.nome AS cn FROM aparelhos a JOIN clientes c ON c.id=a.cliente_id WHERE a.marca LIKE ? OR a.modelo LIKE ? OR a.numero_serie LIKE ? OR c.nome LIKE ? ORDER BY c.nome LIMIT 20");
  mysqli_stmt_bind_param($st2,'ssss',$like,$like,$like,$like); mysqli_stmt_execute($st2); $ar=mysqli_stmt_get_result($st2);
  $st3=mysqli_prepare($conexao,"SELECT m.id,m.data_atendimento,m.tipo_servico,m.diagnostico,a.marca,a.modelo,c.nome AS cn FROM manutencoes m JOIN aparelhos a ON a.id=m.aparelho_id JOIN clientes c ON c.id=a.cliente_id WHERE m.diagnostico LIKE ? OR m.servico_realizado LIKE ? OR m.pecas_utilizadas LIKE ? OR c.nome LIKE ? ORDER BY m.data_atendimento DESC LIMIT 20");
  mysqli_stmt_bind_param($st3,'ssss',$like,$like,$like,$like); mysqli_stmt_execute($st3); $mr=mysqli_stmt_get_result($st3);
}
function bs($s){$m=['em_dia'=>['Em dia','badge-verde'],'atencao'=>['Atenção','badge-amarelo'],'urgente'=>['Urgente','badge-vermelho']];$d=$m[$s]??[$s,'badge-cinza'];return '<span class="badge '.$d[1].'">'.htmlspecialchars($d[0]).'</span>';}
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo"><div><h1>🔍 Busca Geral</h1><p>Pesquise clientes, aparelhos ou manutenções.</p></div></div>
  <form action="busca.php" method="GET" class="busca-form">
    <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Nome do cliente, marca, modelo, nº série, diagnóstico..." autofocus>
    <button type="submit" class="btn btn-primario">Buscar</button>
    <?php if ($q): ?><a href="busca.php" class="btn btn-cinza">Limpar</a><?php endif; ?>
  </form>

  <?php if ($q===''): ?>
    <div class="alerta alerta-info">Digite um termo acima para pesquisar em todo o sistema.</div>
  <?php else: ?>

    <!-- Clientes -->
    <div class="card" style="margin-bottom:20px">
      <div class="card-header">👥 Clientes (<?php echo mysqli_num_rows($cr); ?>)</div>
      <div class="tabela-container">
        <?php if (mysqli_num_rows($cr)>0): ?>
        <table><thead><tr><th>Nome</th><th>Telefone</th><th>E-mail</th><th>Cidade</th><th>Ações</th></tr></thead><tbody>
        <?php while ($c=mysqli_fetch_assoc($cr)): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($c['nome']); ?></strong></td>
            <td><?php echo htmlspecialchars($c['telefone']); ?></td>
            <td><?php echo htmlspecialchars($c['email'] ?: '-'); ?></td>
            <td><?php echo htmlspecialchars($c['cidade'] ?: '-'); ?></td>
            <td class="acoes">
              <a href="aparelhos.php?cliente_id=<?php echo $c['id']; ?>" class="btn btn-cinza">Aparelhos</a>
              <a href="cliente_form.php?id=<?php echo $c['id']; ?>" class="btn btn-primario">Editar</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody></table>
        <?php else: ?><div class="vazio"><p>Nenhum cliente encontrado.</p></div><?php endif; ?>
      </div>
    </div>

    <!-- Aparelhos -->
    <div class="card" style="margin-bottom:20px">
      <div class="card-header">❄️ Aparelhos (<?php echo mysqli_num_rows($ar); ?>)</div>
      <div class="tabela-container">
        <?php if (mysqli_num_rows($ar)>0): ?>
        <table><thead><tr><th>Marca/Modelo</th><th>Nº Série</th><th>Cliente</th><th>Status</th><th>Ações</th></tr></thead><tbody>
        <?php while ($a=mysqli_fetch_assoc($ar)): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($a['marca']); ?></strong> <?php echo htmlspecialchars($a['modelo']); ?></td>
            <td><?php echo htmlspecialchars($a['numero_serie'] ?: '-'); ?></td>
            <td><?php echo htmlspecialchars($a['cn']); ?></td>
            <td><?php echo bs($a['status']); ?></td>
            <td class="acoes">
              <a href="historico.php?aparelho_id=<?php echo $a['id']; ?>" class="btn btn-cinza">Histórico</a>
              <a href="aparelho_form.php?id=<?php echo $a['id']; ?>" class="btn btn-primario">Editar</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody></table>
        <?php else: ?><div class="vazio"><p>Nenhum aparelho encontrado.</p></div><?php endif; ?>
      </div>
    </div>

    <!-- Manutenções -->
    <div class="card">
      <div class="card-header">🔧 Manutenções (<?php echo mysqli_num_rows($mr); ?>)</div>
      <div class="tabela-container">
        <?php if (mysqli_num_rows($mr)>0): ?>
        <table><thead><tr><th>Data</th><th>Aparelho</th><th>Cliente</th><th>Tipo</th><th>Diagnóstico</th><th>Ação</th></tr></thead><tbody>
        <?php while ($m=mysqli_fetch_assoc($mr)): ?>
          <tr>
            <td><?php echo date('d/m/Y',strtotime($m['data_atendimento'])); ?></td>
            <td><?php echo htmlspecialchars($m['marca'].' '.$m['modelo']); ?></td>
            <td><?php echo htmlspecialchars($m['cn']); ?></td>
            <td><span class="badge badge-azul"><?php echo htmlspecialchars($m['tipo_servico']); ?></span></td>
            <td style="max-width:220px;font-size:13px"><?php echo htmlspecialchars(mb_substr($m['diagnostico'],0,80)).'...'; ?></td>
            <td><a href="manutencao_detalhe.php?id=<?php echo $m['id']; ?>" class="btn btn-cinza">Ver</a></td>
          </tr>
        <?php endwhile; ?>
        </tbody></table>
        <?php else: ?><div class="vazio"><p>Nenhuma manutenção encontrada.</p></div><?php endif; ?>
      </div>
    </div>

  <?php endif; ?>
</div>
<?php require_once 'includes/footer.php'; ?>
