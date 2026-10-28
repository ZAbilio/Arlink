<?php
require_once 'includes/config.php';
require_once 'includes/sessao.php';
require_once 'includes/conexao.php';
// Exclusao exige POST autenticado, token da sessao e confirmacao na interface.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validar_formulario()) {
    http_response_code(403);
    exit('Acao nao autorizada. Volte ao sistema.');
}
$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    $st = mysqli_prepare($conexao, "DELETE FROM manutencoes WHERE id=?");
    mysqli_stmt_bind_param($st, 'i', $id);
    mysqli_stmt_execute($st);
}
header('Location: manutencoes.php?sucesso=excluido');
exit;
