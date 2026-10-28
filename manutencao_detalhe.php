<?php
require_once 'includes/config.php'; require_once 'includes/sessao.php'; require_once 'includes/conexao.php';
$id=(int)($_GET['id']??0); if (!$id){header('Location: manutencoes.php');exit;}
$st=mysqli_prepare($conexao,"SELECT m.*,a.marca,a.modelo,a.numero_serie,a.capacidade_btu,c.nome AS cn,c.telefone,c.endereco,c.cidade,t.nome AS tn FROM manutencoes m JOIN aparelhos a ON a.id=m.aparelho_id JOIN clientes c ON c.id=a.cliente_id JOIN tecnicos t ON t.id=m.tecnico_id WHERE m.id=?");
mysqli_stmt_bind_param($st,'i',$id); mysqli_stmt_execute($st);
$m=mysqli_fetch_assoc(mysqli_stmt_get_result($st));
if (!$m){header('Location: manutencoes.php');exit;}
$titulo='Manutenção #'.$id;
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo">
    <div><h1>Manutenção #<?php echo $id; ?></h1><p><?php echo date('d/m/Y',strtotime($m['data_atendimento'])); ?> — <?php echo htmlspecialchars($m['tn']); ?></p></div>
    <div class="acoes">
      <a href="historico.php?aparelho_id=<?php echo $m['aparelho_id']; ?>" class="btn btn-cinza">← Histórico</a>
      <button onclick="window.print()" class="btn btn-primario">🖨️ Imprimir</button>
    </div>
  </div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
    <div class="card"><div class="card-header">❄️ Aparelho</div><div class="card-body" style="display:grid;gap:12px">
      <div class="info-campo"><label>Marca / Modelo</label><p><?php echo htmlspecialchars($m['marca'].' '.$m['modelo']); ?></p></div>
      <div class="info-campo"><label>Nº Série</label><p><?php echo htmlspecialchars($m['numero_serie'] ?: '-'); ?></p></div>
      <div class="info-campo"><label>Capacidade</label><p><?php echo htmlspecialchars($m['capacidade_btu'] ? $m['capacidade_btu'].' BTU' : '-'); ?></p></div>
    </div></div>
    <div class="card"><div class="card-header">👤 Cliente</div><div class="card-body" style="display:grid;gap:12px">
      <div class="info-campo"><label>Nome</label><p><?php echo htmlspecialchars($m['cn']); ?></p></div>
      <div class="info-campo"><label>Telefone</label><p><?php echo htmlspecialchars($m['telefone']); ?></p></div>
      <div class="info-campo"><label>Endereço</label><p><?php echo $m['endereco']?htmlspecialchars($m['endereco'].', '.$m['cidade']):'-'; ?></p></div>
    </div></div>
  </div>
  <div class="card"><div class="card-header">🔧 Atendimento</div><div class="card-body">
    <div class="linha-historico-body" style="grid-template-columns:repeat(3,1fr)">
      <div class="info-campo"><label>Tipo</label><p><?php echo htmlspecialchars($m['tipo_servico']); ?></p></div>
      <div class="info-campo"><label>Técnico</label><p><?php echo htmlspecialchars($m['tn']); ?></p></div>
      <div class="info-campo"><label>Valor</label><p><?php echo $m['valor_cobrado']!==null?'R$ '.number_format($m['valor_cobrado'],2,',','.'):'Não informado'; ?></p></div>
      <div class="info-campo"><label>Diagnóstico</label><p><?php echo nl2br(htmlspecialchars($m['diagnostico'])); ?></p></div>
      <div class="info-campo"><label>Serviço realizado</label><p><?php echo nl2br(htmlspecialchars($m['servico_realizado'])); ?></p></div>
      <div class="info-campo"><label>Peças utilizadas</label><p><?php echo $m['pecas_utilizadas']?nl2br(htmlspecialchars($m['pecas_utilizadas'])):'-'; ?></p></div>
      <?php if ($m['proxima_visita']): ?>
      <div class="info-campo"><label>Próxima visita</label><p><?php echo date('d/m/Y',strtotime($m['proxima_visita'])); ?></p></div>
      <?php endif; ?>
      <?php if ($m['observacoes_internas']): ?>
      <div class="info-campo" style="grid-column:1/-1"><label>🔒 Obs. internas</label>
        <p style="background:#fff3cd;padding:10px;border-radius:6px"><?php echo nl2br(htmlspecialchars($m['observacoes_internas'])); ?></p>
      </div>
      <?php endif; ?>
    </div>
  </div></div>
</div>
<?php require_once 'includes/footer.php'; ?>
