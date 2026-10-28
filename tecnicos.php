<?php
require_once 'includes/config.php'; require_once 'includes/sessao.php'; require_once 'includes/conexao.php';
$titulo='Técnicos'; $sucesso=$_GET['sucesso']??'';
$res=mysqli_query($conexao,"SELECT t.id,t.nome,t.email,t.telefone,t.criado_em,COUNT(m.id) AS total FROM tecnicos t LEFT JOIN manutencoes m ON m.tecnico_id=t.id GROUP BY t.id ORDER BY t.nome");
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo">
    <div><h1>Técnicos</h1><p>Usuários com acesso ao sistema.</p></div>
    <a href="tecnico_form.php" class="btn btn-verde">+ Novo Técnico</a>
  </div>
  <?php if ($sucesso==='criado'): ?><div class="alerta alerta-sucesso">✅ Técnico cadastrado!</div>
  <?php elseif ($sucesso==='editado'): ?><div class="alerta alerta-sucesso">✅ Técnico atualizado!</div>
  <?php elseif ($sucesso==='excluido'): ?><div class="alerta alerta-sucesso">✅ Técnico excluído!</div><?php endif; ?>
  <?php if (($_GET['erro'] ?? '') === 'historico'): ?><div class="alerta alerta-erro">Este tecnico possui manutencoes registradas. Mantenha o cadastro para preservar o historico.</div><?php endif; ?>
  <?php if (($_GET['erro'] ?? '') === 'proprio'): ?><div class="alerta alerta-erro">Voce nao pode excluir sua propria conta.</div><?php endif; ?>
  <div class="card">
    <div class="contador"><?php echo mysqli_num_rows($res); ?> técnico(s)</div>
    <div class="tabela-container">
      <?php if (mysqli_num_rows($res)>0): ?>
      <table><thead><tr><th>#</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Manutenções</th><th>Cadastro</th><th>Ações</th></tr></thead>
      <tbody>
      <?php while ($t=mysqli_fetch_assoc($res)): ?>
        <tr>
          <td><?php echo $t['id']; ?></td>
          <td><strong><?php echo htmlspecialchars($t['nome']); ?></strong><?php if ($t['id']==$_SESSION['tecnico_id']): ?> <span class="badge badge-verde">Você</span><?php endif; ?></td>
          <td><?php echo htmlspecialchars($t['email']); ?></td>
          <td><?php echo $t['telefone']?htmlspecialchars($t['telefone']):'-'; ?></td>
          <td><span class="badge badge-azul"><?php echo $t['total']; ?></span></td>
          <td><?php echo date('d/m/Y',strtotime($t['criado_em'])); ?></td>
          <td class="acoes">
            <a href="tecnico_form.php?id=<?php echo $t['id']; ?>" class="btn btn-primario">Editar</a>
            <?php if ($t['id']!=$_SESSION['tecnico_id']): ?>
            <form action="tecnico_excluir.php" method="POST" onsubmit="return confirm('Excluir este tecnico?')" class="form-excluir"><input type="hidden" name="id" value="<?php echo $t['id']; ?>"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(token_formulario()); ?>"><button type="submit" class="btn btn-vermelho">Excluir</button></form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody></table>
      <?php else: ?><div class="vazio"><h3>Nenhum técnico</h3></div><?php endif; ?>
    </div>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
