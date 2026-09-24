<?php
require_once 'includes/conexao.php';

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $sql = "DELETE FROM aparelhos WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

}

header("Location: aparelhos.php");
exit;