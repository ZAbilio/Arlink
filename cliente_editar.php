<?php
require_once 'includes/conexao.php';

$erro = '';

// Valida o ID recebido
$id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: clientes.php');
    exit;
}

// Salva as alterações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $cidade   = trim($_POST['cidade'] ?? '');

    if ($nome === '' || $telefone === '') {
        $erro = 'Nome e telefone são obrigatórios.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } else {
        $sql = "UPDATE clientes
                SET nome = ?, telefone = ?, email = ?, endereco = ?, cidade = ?
                WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, 'sssssi', $nome, $telefone, $email, $endereco, $cidade, $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: clientes.php');
            exit;
        }

        $erro = 'Erro ao atualizar cliente: ' . mysqli_error($conexao);
        mysqli_stmt_close($stmt);
    }

    // Mantém o que o usuário digitou em caso de erro
    $cliente = [
        'id'       => $id,
        'nome'     => $nome,
        'telefone' => $telefone,
        'email'    => $email,
        'endereco' => $endereco,
        'cidade'   => $cidade,
    ];
} else {
    // Busca o cliente para preencher o formulário
    $sql = "SELECT id, nome, telefone, email, endereco, cidade FROM clientes WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $cliente = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    if (!$cliente) {
        header('Location: clientes.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Cliente - AR Link</title>

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
            max-width: 700px;
            margin: 40px auto;
        }

        .topo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .topo h1 {
            color: #1e3a5f;
            font-size: 28px;
        }

        .btn-voltar {
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

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 30px;
        }

        .alerta {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .campo {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
            color: #1e3a5f;
        }

        label span {
            color: #dc3545;
        }

        input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ccd2d9;
            border-radius: 6px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
        }

        .linha {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .botoes {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-salvar {
            background: #198754;
            color: white;
            border: none;
            padding: 12px 22px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-salvar:hover {
            background: #157347;
        }

        .btn-cancelar {
            background: #e9ecef;
            color: #333;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
        }

        .btn-cancelar:hover {
            background: #dde0e3;
        }

        @media (max-width: 700px) {
            .topo {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .topo h1 {
                font-size: 24px;
            }

            .linha {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .card {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<?php include 'includes/menu.php'; ?>

<div class="container">

    <div class="topo">
        <h1>Editar Cliente</h1>

        <a href="clientes.php" class="btn-voltar">← Voltar</a>
    </div>

    <div class="card">

        <?php if ($erro !== ''): ?>
            <div class="alerta"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="cliente_editar.php?id=<?php echo (int) $cliente['id']; ?>">

            <input type="hidden" name="id" value="<?php echo (int) $cliente['id']; ?>">

            <div class="campo">
                <label for="nome">Nome <span>*</span></label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?php echo htmlspecialchars($cliente['nome']); ?>"
                    required
                >
            </div>

            <div class="linha">
                <div class="campo">
                    <label for="telefone">Telefone <span>*</span></label>
                    <input
                        type="text"
                        id="telefone"
                        name="telefone"
                        value="<?php echo htmlspecialchars($cliente['telefone']); ?>"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($cliente['email'] ?? ''); ?>"
                    >
                </div>
            </div>

            <div class="campo">
                <label for="endereco">Endereço</label>
                <input  
                    type="text"
                    id="endereco"
                    name="endereco"
                    value="<?php echo htmlspecialchars($cliente['endereco'] ?? ''); ?>"
                >
            </div>

            <div class="campo">
                <label for="cidade">Cidade</label>
                <input
                    type="text"
                    id="cidade"
                    name="cidade"
                    value="<?php echo htmlspecialchars($cliente['cidade'] ?? ''); ?>"
                >
            </div>

            <div class="botoes">
                <button type="submit" class="btn-salvar">Salvar alterações</button>
                <a href="clientes.php" class="btn-cancelar">Cancelar</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>