<?php
require_once 'includes/config.php';
require_once 'includes/sessao.php';
require_once 'includes/conexao.php';
$titulo  = 'Clientes';
$sucesso = $_GET['sucesso'] ?? '';
$res = mysqli_query($conexao,
  "SELECT c.id,c.nome,c.telefone,c.email,c.cidade,c.criado_em,COUNT(a.id) AS total
   FROM clientes c LEFT JOIN aparelhos a ON a.cliente_id=c.id
   GROUP BY c.id ORDER BY c.nome ASC");
require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-topo">
    <div><h1>Clientes</h1><p>Gerencie os clientes cadastrados.</p></div>
    <a href="cliente_form.php" class="btn btn-verde">+ Novo Cliente</a>
  </div>
  <?php if ($sucesso==='criado'): ?><div class="alerta alerta-sucesso">✅ Cliente cadastrado!</div>
  <?php elseif ($sucesso==='editado'): ?><div class="alerta alerta-sucesso">✅ Cliente atualizado!</div>
  <?php elseif ($sucesso==='excluido'): ?><div class="alerta alerta-sucesso">✅ Cliente excluído!</div><?php endif; ?>
  <div class="card">
    <div class="contador"><?php echo mysqli_num_rows($res); ?> cliente(s)</div>
    <div class="tabela-container">
      <?php if (mysqli_num_rows($res)>0): ?>
      <table><thead><tr><th>#</th><th>Nome</th><th>Telefone</th><th>E-mail</th><th>Cidade</th><th>Aparelhos</th><th>Cadastro</th><th>Ações</th></tr></thead>
      <tbody>
      <?php while ($c=mysqli_fetch_assoc($res)): ?>
        <tr>
          <td><?php echo $c['id']; ?></td>
          <td><strong><?php echo htmlspecialchars($c['nome']); ?></strong></td>
          <td><?php echo htmlspecialchars($c['telefone']); ?></td>
          <td><?php echo $c['email'] ? htmlspecialchars($c['email']) : '-'; ?></td>
          <td><?php echo $c['cidade'] ? htmlspecialchars($c['cidade']) : '-'; ?></td>
          <td><span class="badge badge-azul"><?php echo $c['total']; ?></span></td>
          <td><?php echo date('d/m/Y',strtotime($c['criado_em'])); ?></td>
          <td class="acoes">
            <a href="aparelhos.php?cliente_id=<?php echo $c['id']; ?>" class="btn btn-cinza">Aparelhos</a>
            <a href="cliente_form.php?id=<?php echo $c['id']; ?>" class="btn btn-primario">Editar</a>
            <form action="cliente_excluir.php" method="POST" onsubmit="return confirm('Excluir este cliente e TODOS os seus aparelhos e manutencoes?')" class="form-excluir"><input type="hidden" name="id" value="<?php echo $c['id']; ?>"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(token_formulario()); ?>"><button type="submit" class="btn btn-vermelho">Excluir</button></form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody></table>
      <?php else: ?><div class="vazio"><h3>Nenhum cliente</h3><p>Clique em "+ Novo Cliente".</p></div><?php endif; ?>
    </div>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
