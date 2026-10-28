<?php
require_once 'includes/config.php'; require_once 'includes/sessao.php'; require_once 'includes/conexao.php';
$aid=(int)($_GET['aparelho_id']??0); $sucesso=$_GET['sucesso']??'';
if (!$aid){header('Location: aparelhos.php');exit;}
$st=mysqli_prepare($conexao,"SELECT a.*,c.nome AS cn,c.telefone,c.endereco,c.cidade FROM aparelhos a JOIN clientes c ON c.id=a.cliente_id WHERE a.id=?");
mysqli_stmt_bind_param($st,'i',$aid); mysqli_stmt_execute($st);
$ap=mysqli_fetch_assoc(mysqli_stmt_get_result($st));
if (!$ap){header('Location: aparelhos.php');exit;}
$st2=mysqli_prepare($conexao,"SELECT m.*,t.nome AS tn FROM manutencoes m JOIN tecnicos t ON t.id=m.tecnico_id WHERE m.aparelho_id=? ORDER BY m.data_atendimento DESC");
mysqli_stmt_bind_param($st2,'i',$aid); mysqli_stmt_execute($st2);
$mans=mysqli_stmt_get_result($st2);
function bs($s){$m=['em_dia'=>['Em dia','badge-verde'],'atencao'=>['Atenção','badge-amarelo'],'urgente'=>['Urgente','badge-vermelho']];$d=$m[$s]??[$s,'badge-cinza'];return '<span class="badge '.$d[1].'">'.htmlspecialchars($d[0]).'</span>';}
$titulo='Histórico — '.$ap['marca'].' '.$ap['modelo'];
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo">
    <div><h1>Histórico de Manutenções</h1><p><?php echo htmlspecialchars($ap['marca'].' '.$ap['modelo'].' — '.$ap['cn']); ?></p></div>
    <div class="acoes">
      <a href="aparelhos.php" class="btn btn-cinza">← Aparelhos</a>
      <a href="manutencao_form.php?aparelho_id=<?php echo $aid; ?>" class="btn btn-verde">+ Nova Manutenção</a>
      <button onclick="window.print()" class="btn btn-primario">🖨️ Imprimir</button>
    </div>
  </div>
  <?php if ($sucesso==='criado'): ?><div class="alerta alerta-sucesso">✅ Manutenção registrada!</div><?php endif; ?>

  <!-- Ficha do aparelho -->
  <div class="historico-header">
    <div class="info-item"><label>Aparelho</label><span><?php echo htmlspecialchars($ap['marca'].' '.$ap['modelo']); ?></span></div>
    <div class="info-item"><label>Tipo</label><span><?php echo htmlspecialchars($ap['tipo'] ?: '-'); ?></span></div>
    <div class="info-item"><label>Capacidade</label><span><?php echo htmlspecialchars($ap['capacidade_btu'] ? $ap['capacidade_btu'].' BTU' : '-'); ?></span></div>
    <div class="info-item"><label>Nº Série</label><span><?php echo htmlspecialchars($ap['numero_serie'] ?: '-'); ?></span></div>
    <div class="info-item"><label>Status</label><span><?php echo bs($ap['status']); ?></span></div>
    <div class="info-item"><label>Próx. Manutenção</label><span><?php echo $ap['proxima_manutencao']?date('d/m/Y',strtotime($ap['proxima_manutencao'])):'-'; ?></span></div>
    <div class="info-item"><label>Cliente</label><span><?php echo htmlspecialchars($ap['cn']); ?></span></div>
    <div class="info-item"><label>Telefone</label><span><?php echo htmlspecialchars($ap['telefone']); ?></span></div>
    <?php if ($ap['endereco']): ?><div class="info-item"><label>Endereço</label><span><?php echo htmlspecialchars($ap['endereco'].', '.$ap['cidade']); ?></span></div><?php endif; ?>
  </div>

  <!-- Lista de manutenções -->
  <?php if (mysqli_num_rows($mans)>0):
    $total=mysqli_num_rows($mans); mysqli_data_seek($mans,0); ?>
  <p style="color:#666;margin-bottom:14px;font-size:14px"><?php echo $total; ?> registro(s) — mais recente primeiro</p>
  <?php while ($m=mysqli_fetch_assoc($mans)): ?>
  <div class="linha-historico">
    <div class="linha-historico-header">
      <div>📅 <?php echo date('d/m/Y',strtotime($m['data_atendimento'])); ?> &nbsp;|&nbsp;
        <span class="badge badge-azul"><?php echo htmlspecialchars($m['tipo_servico']); ?></span>
        &nbsp;|&nbsp; 🔧 <?php echo htmlspecialchars($m['tn']); ?>
      </div>
      <div style="display:flex;gap:8px;align-items:center">
        <?php if ($m['valor_cobrado']): ?><span style="font-weight:600">R$ <?php echo number_format($m['valor_cobrado'],2,',','.'); ?></span><?php endif; ?>
        <a href="manutencao_detalhe.php?id=<?php echo $m['id']; ?>" class="btn btn-cinza" style="padding:4px 12px;font-size:12px">Ver detalhes</a>
        <form action="manutencao_excluir.php" method="POST" onsubmit="return confirm('Excluir esta manutencao?')" class="form-excluir"><input type="hidden" name="id" value="<?php echo $m['id']; ?>"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(token_formulario()); ?>"><button type="submit" class="btn btn-vermelho">Excluir</button></form>
      </div>
    </div>
    <div class="linha-historico-body">
      <div class="info-campo"><label>Diagnóstico</label><p><?php echo nl2br(htmlspecialchars($m['diagnostico'])); ?></p></div>
      <div class="info-campo"><label>Serviço realizado</label><p><?php echo nl2br(htmlspecialchars($m['servico_realizado'])); ?></p></div>
      <div class="info-campo"><label>Peças utilizadas</label><p><?php echo $m['pecas_utilizadas']?nl2br(htmlspecialchars($m['pecas_utilizadas'])):'—'; ?></p></div>
      <?php if ($m['proxima_visita']): ?><div class="info-campo"><label>Próxima visita</label><p><?php echo date('d/m/Y',strtotime($m['proxima_visita'])); ?></p></div><?php endif; ?>
      <?php if ($m['observacoes_internas']): ?>
      <div class="info-campo"><label>🔒 Obs. internas</label>
        <p style="background:#fff3cd;padding:8px;border-radius:6px;font-size:13px"><?php echo nl2br(htmlspecialchars($m['observacoes_internas'])); ?></p>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endwhile; ?>
  <?php else: ?>
  <div class="card"><div class="vazio"><h3>Nenhuma manutenção registrada</h3>
    <p style="margin:12px 0"><a href="manutencao_form.php?aparelho_id=<?php echo $aid; ?>" class="btn btn-verde">+ Registrar primeira manutenção</a></p>
  </div></div>
  <?php endif; ?>
</div>
<?php require_once 'includes/footer.php'; ?>
