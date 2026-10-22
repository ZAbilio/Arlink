<?php
require_once 'includes/conexao.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: clientes.php');
    exit;
}

$erro = '';

try {
    $sql = "DELETE FROM clientes WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: clientes.php');
    exit;
} catch (mysqli_sql_exception $e) {
    // 1451 = cliente vinculado a outros registros (chave estrangeira)
    if ($e->getCode() == 1451) {
        $erro = 'Este cliente não pode ser excluído porque possui registros vinculados (aparelhos, manutenções, etc.). Exclua ou transfira esses registros primeiro.';
    } else {
        $erro = 'Erro ao excluir cliente: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Cliente - AR Link</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #333;
        }

        .container {
            width: 95%;
            max-width: 600px;
            margin: 60px auto;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 30px;
        }

        h1 {
            color: #1e3a5f;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .alerta {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 14px;
            line-height: 1.5;
        }

        .btn-voltar {
            display: inline-block;
            background: #6c757d;
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 6px;
            font-weight: bold;
        }

        .btn-voltar:hover {
            background: #5c636a;
        }
    </style>
</head>

<body>

<?php include 'includes/menu.php'; ?>

<div class="container">
    <div class="card">
        <h1>Não foi possível excluir</h1>

        <div class="alerta">
            <?php echo htmlspecialchars($erro); ?>
        </div>

        <a href="clientes.php" class="btn-voltar">← Voltar para clientes</a>
    </div>
</div>

</body>
</html>