<?php
require_once 'includes/config.php';
require_once 'includes/sessao.php';
require_once 'includes/conexao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validar_formulario()) {
    http_response_code(403);
    exit('Acao nao autorizada. Volte ao sistema.');
}
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0 || $id === (int)$_SESSION['tecnico_id']) {
    header('Location: tecnicos.php?erro=proprio');
    exit;
}
// Se existem atendimentos vinculados, nao e possivel excluir o tecnico.
// Preservar o historico e melhor do que remover registros antigos.
$st = mysqli_prepare($conexao, 'SELECT COUNT(*) FROM manutencoes WHERE tecnico_id=?');
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
$total = (int)mysqli_fetch_row(mysqli_stmt_get_result($st))[0];
if ($total > 0) {
    header('Location: tecnicos.php?erro=historico');
    exit;
}
$st = mysqli_prepare($conexao, 'DELETE FROM tecnicos WHERE id=?');
mysqli_stmt_bind_param($st, 'i', $id);
mysqli_stmt_execute($st);
header('Location: tecnicos.php?sucesso=excluido');
exit;
