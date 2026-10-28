<?php
require_once 'includes/config.php'; require_once 'includes/sessao.php'; require_once 'includes/conexao.php';
$titulo='Registrar Manutenção'; $aid=(int)($_GET['aparelho_id']??0); $erro='';
$d=['aparelho_id'=>$aid,'data_atendimento'=>date('Y-m-d'),'tipo_servico'=>'','diagnostico'=>'','servico_realizado'=>'','pecas_utilizadas'=>'','valor_cobrado'=>'','proxima_visita'=>'','observacoes_internas'=>''];
$aparelhos=mysqli_query($conexao,"SELECT a.id,a.marca,a.modelo,a.numero_serie,c.nome AS cn FROM aparelhos a JOIN clientes c ON c.id=a.cliente_id ORDER BY c.nome,a.marca");
if ($_SERVER['REQUEST_METHOD']==='POST' && !validar_formulario()) {
  http_response_code(403); exit('Formulario expirado. Atualize a pagina e tente novamente.');
}
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $d['aparelho_id']=(int)($_POST['aparelho_id']??0);
  $d['data_atendimento']=trim($_POST['data_atendimento']??'');
  $d['tipo_servico']=trim($_POST['tipo_servico']??'');
  $d['diagnostico']=trim($_POST['diagnostico']??'');
  $d['servico_realizado']=trim($_POST['servico_realizado']??'');
  $d['pecas_utilizadas']=trim($_POST['pecas_utilizadas']??'');
  $d['valor_cobrado']=trim($_POST['valor_cobrado']??'');
  $d['proxima_visita']=trim($_POST['proxima_visita']??'');
  $d['observacoes_internas']=trim($_POST['observacoes_internas']??'');
  if (!$d['aparelho_id']) $erro='Selecione um aparelho.';
  elseif (!$d['data_atendimento']) $erro='Informe a data.';
  elseif (!$d['tipo_servico']) $erro='Informe o tipo de serviço.';
  elseif (!$d['diagnostico']) $erro='Informe o diagnóstico.';
  elseif (!$d['servico_realizado']) $erro='Informe o serviço realizado.';
  if (!$erro) $erro = validar_dados($d, ['tipo_servico'=>50,'diagnostico'=>16000,'servico_realizado'=>16000,'pecas_utilizadas'=>16000,'observacoes_internas'=>16000]);
  if (!$erro && !registro_existe($conexao, 'aparelhos', $d['aparelho_id'])) $erro = 'Aparelho inexistente.';
  if (!$erro && !registro_existe($conexao, 'tecnicos', (int)$_SESSION['tecnico_id'])) $erro = 'Tecnico inexistente. Entre novamente.';
  if (!$erro){
    $valor=$d['valor_cobrado']!==''?(float)str_replace(',','.',$d['valor_cobrado']):null;
    $prox=$d['proxima_visita']?:null;
    // As duas gravacoes devem ser confirmadas juntas.
    try {
        mysqli_begin_transaction($conexao);
        $st=mysqli_prepare($conexao,"INSERT INTO manutencoes(aparelho_id,tecnico_id,data_atendimento,tipo_servico,diagnostico,servico_realizado,pecas_utilizadas,valor_cobrado,proxima_visita,observacoes_internas) VALUES(?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($st,'iisssssdss',$d['aparelho_id'],$_SESSION['tecnico_id'],$d['data_atendimento'],$d['tipo_servico'],$d['diagnostico'],$d['servico_realizado'],$d['pecas_utilizadas'],$valor,$prox,$d['observacoes_internas']);
        mysqli_stmt_execute($st);
        if ($prox){$u=mysqli_prepare($conexao,"UPDATE aparelhos SET proxima_manutencao=? WHERE id=?");mysqli_stmt_bind_param($u,'si',$prox,$d['aparelho_id']);mysqli_stmt_execute($u);}
        mysqli_commit($conexao);
        header('Location: historico.php?aparelho_id='.$d['aparelho_id'].'&sucesso=criado'); exit;
    } catch (mysqli_sql_exception $falha) {
        mysqli_rollback($conexao);
        $erro = 'Nao foi possivel salvar. Nenhuma alteracao foi confirmada. Confira os dados e tente novamente.';
    }
  }
}
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo"><div><h1>Registrar Manutenção</h1></div><a href="manutencoes.php" class="btn btn-cinza">← Voltar</a></div>
  <?php if ($erro): ?><div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div><?php endif; ?>
  <div class="card"><div class="card-header">Dados do atendimento</div><div class="card-body">
    <form method="POST"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(token_formulario()); ?>"><div class="form-grid">
      <div class="form-group full"><label>Aparelho *</label>
        <select name="aparelho_id" required>
          <option value="">— Selecione —</option>
          <?php while ($a=mysqli_fetch_assoc($aparelhos)): $sel=$a['id']==$d['aparelho_id']?'selected':''; ?>
          <option value="<?php echo $a['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($a['cn'].' — '.$a['marca'].' '.$a['modelo'].($a['numero_serie']?' ('.$a['numero_serie'].')':'')); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group"><label>Data do atendimento *</label><input type="date" name="data_atendimento" value="<?php echo htmlspecialchars($d['data_atendimento']); ?>" required></div>
      <div class="form-group"><label>Tipo de serviço *</label>
        <select name="tipo_servico" required>
          <option value="">— Selecione —</option>
          <?php foreach(['Preventiva','Corretiva','Higienização','Instalação','Desinstalação','Revisão Geral'] as $t): $sel=$d['tipo_servico']===$t?'selected':''; ?>
          <option value="<?php echo $t; ?>" <?php echo $sel; ?>><?php echo $t; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group full"><label>Diagnóstico * <small style="color:#888">(o que foi encontrado)</small></label>
        <textarea name="diagnostico" required placeholder="Descreva o que foi identificado..."><?php echo htmlspecialchars($d['diagnostico']); ?></textarea>
      </div>
      <div class="form-group full"><label>Serviço realizado * <small style="color:#888">(o que foi feito)</small></label>
        <textarea name="servico_realizado" required placeholder="Descreva o serviço executado..."><?php echo htmlspecialchars($d['servico_realizado']); ?></textarea>
      </div>
      <div class="form-group full"><label>Peças utilizadas</label>
        <textarea name="pecas_utilizadas" placeholder="Ex: Capacitor 40+5 MFD, Gás R-410A..."><?php echo htmlspecialchars($d['pecas_utilizadas']); ?></textarea>
      </div>
      <div class="form-group"><label>Valor cobrado (R$)</label><input type="number" name="valor_cobrado" step="0.01" min="0" value="<?php echo htmlspecialchars($d['valor_cobrado']); ?>" placeholder="0.00"></div>
      <div class="form-group"><label>Próxima visita</label><input type="date" name="proxima_visita" value="<?php echo htmlspecialchars($d['proxima_visita']); ?>"></div>
      <div class="form-group full"><label>Observações internas <small style="color:#888">(só técnicos veem)</small></label>
        <textarea name="observacoes_internas" placeholder="Anotações particulares..."><?php echo htmlspecialchars($d['observacoes_internas']); ?></textarea>
      </div>
    </div>
    <div class="form-acoes"><button type="submit" class="btn btn-verde">💾 Salvar manutenção</button><a href="manutencoes.php" class="btn btn-cinza">Cancelar</a></div>
    </form>
  </div></div>
</div>
<?php require_once 'includes/footer.php'; ?>
