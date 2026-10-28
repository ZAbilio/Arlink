<?php
require_once 'includes/config.php';
require_once 'includes/sessao.php';
require_once 'includes/conexao.php';
$titulo = 'Dashboard';
$total_clientes   = mysqli_fetch_row(mysqli_query($conexao,"SELECT COUNT(*) FROM clientes"))[0];
$total_aparelhos  = mysqli_fetch_row(mysqli_query($conexao,"SELECT COUNT(*) FROM aparelhos"))[0];
$total_manut      = mysqli_fetch_row(mysqli_query($conexao,"SELECT COUNT(*) FROM manutencoes"))[0];
$total_urgentes   = mysqli_fetch_row(mysqli_query($conexao,"SELECT COUNT(*) FROM aparelhos WHERE status='urgente'"))[0];
$total_vencidas = mysqli_fetch_row(mysqli_query($conexao, "SELECT COUNT(*) FROM aparelhos WHERE proxima_manutencao < CURDATE()"))[0];
$total_proximas = mysqli_fetch_row(mysqli_query($conexao, "SELECT COUNT(*) FROM aparelhos WHERE proxima_manutencao BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))[0];
$proximas = mysqli_query($conexao,
  "SELECT a.id,a.marca,a.modelo,a.proxima_manutencao,a.status,c.nome AS cn,c.telefone
   FROM aparelhos a JOIN clientes c ON c.id=a.cliente_id
   WHERE a.proxima_manutencao <= DATE_ADD(CURDATE(),INTERVAL 30 DAY)
   ORDER BY a.proxima_manutencao ASC LIMIT 8");
$ultimas = mysqli_query($conexao,
  "SELECT m.data_atendimento,m.tipo_servico,a.marca,a.modelo,c.nome AS cn
   FROM manutencoes m
   JOIN aparelhos a ON a.id=m.aparelho_id
   JOIN clientes c ON c.id=a.cliente_id
   ORDER BY m.criado_em DESC LIMIT 6");
function bs($s){
  $m=['em_dia'=>['Em dia','badge-verde'],'atencao'=>['Atenção','badge-amarelo'],'urgente'=>['Urgente','badge-vermelho']];
  $d=$m[$s]??[$s,'badge-cinza'];
  return '<span class="badge '.$d[1].'">'.htmlspecialchars($d[0]).'</span>';
}
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo">
    <div><h1>Dashboard</h1><p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['tecnico_nome']); ?>! Hoje é <?php echo date('d/m/Y'); ?>.</p></div>
  </div>
  <div class="cards-resumo">
    <div class="card-resumo"><span class="numero"><?php echo $total_clientes; ?></span><span class="rotulo">👥 Clientes</span></div>
    <div class="card-resumo"><span class="numero"><?php echo $total_aparelhos; ?></span><span class="rotulo">❄️ Aparelhos</span></div>
    <div class="card-resumo verde"><span class="numero"><?php echo $total_manut; ?></span><span class="rotulo">🔧 Manutenções</span></div>
    <div class="card-resumo vermelho"><span class="numero"><?php echo $total_urgentes; ?></span><span class="rotulo">🚨 Urgentes</span></div>
  </div>
  <p>Vencidas: <?php echo $total_vencidas; ?> | Próximas em 30 dias: <?php echo $total_proximas; ?>. A lista mostra até 8 aparelhos, começando pelos mais antigos.</p>
  <div class="dash-grid">
    <div class="card">
      <div class="card-header">📅 Manutenções vencidas e próximas (30 dias)</div>
      <div class="tabela-container">
        <?php if (mysqli_num_rows($proximas)>0): ?>
        <table><thead><tr><th>Aparelho</th><th>Cliente</th><th>Data</th><th>Status</th></tr></thead><tbody>
        <?php while ($p=mysqli_fetch_assoc($proximas)): ?>
          <tr>
            <td><?php echo htmlspecialchars($p['marca'].' '.$p['modelo']); ?></td>
            <td><?php echo htmlspecialchars($p['cn']); ?><br><small style="color:#888"><?php echo htmlspecialchars($p['telefone']); ?></small></td>
            <td><?php $d=strtotime($p['proxima_manutencao']);$diff=($d-strtotime('today'))/86400;$cor=$diff<0?'color:red':($diff<=7?'color:orange':'');echo '<span style="'.$cor.'">'.date('d/m/Y',$d).'</span>'; ?></td>
            <td><?php echo bs($p['status']); ?></td>
          </tr>
        <?php endwhile; ?>
        </tbody></table>
        <?php else: ?><div class="vazio"><p>Nenhuma manutenção vencida ou nos próximos 30 dias.</p></div><?php endif; ?>
      </div>
    </div>
    <div class="card">
      <div class="card-header">🕐 Últimas manutenções</div>
      <div class="tabela-container">
        <?php if (mysqli_num_rows($ultimas)>0): ?>
        <table><thead><tr><th>Data</th><th>Aparelho</th><th>Cliente</th><th>Tipo</th></tr></thead><tbody>
        <?php while ($u=mysqli_fetch_assoc($ultimas)): ?>
          <tr>
            <td><?php echo date('d/m/Y',strtotime($u['data_atendimento'])); ?></td>
            <td><?php echo htmlspecialchars($u['marca'].' '.$u['modelo']); ?></td>
            <td><?php echo htmlspecialchars($u['cn']); ?></td>
            <td><span class="badge badge-azul"><?php echo htmlspecialchars($u['tipo_servico']); ?></span></td>
          </tr>
        <?php endwhile; ?>
        </tbody></table>
        <?php else: ?><div class="vazio"><p>Nenhuma manutenção registrada ainda.</p></div><?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
