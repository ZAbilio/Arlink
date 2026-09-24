<?php
require_once 'includes/conexao.php';

$erro = '';

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cliente_id         = $_POST['cliente_id'];
    $marca              = trim($_POST['marca']);
    $modelo             = trim($_POST['modelo']);
    $numero_serie       = trim($_POST['numero_serie']);
    $capacidade_btu     = trim($_POST['capacidade_btu']);
    $tipo               = trim($_POST['tipo']);
    $status             = $_POST['status'];
    $proxima_manutencao = $_POST['proxima_manutencao'] !== '' ? $_POST['proxima_manutencao'] : null;
    $observacoes        = trim($_POST['observacoes']);

    if (empty($cliente_id) || empty($marca) || empty($modelo)) {
        $erro = "Preencha ao menos Cliente, Marca e Modelo.";
    } else {

        $sql = "INSERT INTO aparelhos 
                    (cliente_id, marca, modelo, numero_serie, capacidade_btu, tipo, status, proxima_manutencao, observacoes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conexao, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "issssssss",
            $cliente_id,
            $marca,
            $modelo,
            $numero_serie,
            $capacidade_btu,
            $tipo,
            $status,
            $proxima_manutencao,
            $observacoes
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: aparelhos.php");
            exit;
        } else {
            $erro = "Erro ao salvar: " . mysqli_error($conexao);
        }
    }
}

// Busca clientes para o select
$sqlClientes = "SELECT id, nome FROM clientes ORDER BY nome";
$resClientes = mysqli_query($conexao, $sqlClientes);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Novo Aparelho - AR Link</title>

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
            max-width: 800px;
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
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
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

        .campo {
            margin-bottom: 18px;
        }

        .linha {
            display: flex;
            gap: 15px;
        }

        .linha .campo {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: bold;
            color: #1e3a5f;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        textarea {
            resize: vertical;
            min-height: 70px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #1e3a5f;
        }

        .obrigatorio {
            color: #dc3545;
        }

        .btn-salvar {
            background: #198754;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-salvar:hover {
            background: #157347;
        }

        .alerta {
            background: #f8d7da;
            color: #842029;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .linha {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="topo">
        <h1>Novo Aparelho</h1>
        <a href="aparelhos.php" class="btn-voltar">← Voltar</a>
    </div>

    <div class="card">

        <?php if (!empty($erro)): ?>
            <div class="alerta"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="novos_aparelhos.php">

            <div class="campo">
                <label>Cliente <span class="obrigatorio">*</span></label>
                <select name="cliente_id" required>
                    <option value="">Selecione...</option>
                    <?php while ($cli = mysqli_fetch_assoc($resClientes)): ?>
                        <option value="<?php echo $cli['id']; ?>">
                            <?php echo htmlspecialchars($cli['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="linha">

                <div class="campo">
                    <label>Marca <span class="obrigatorio">*</span></label>
                    <input type="text" name="marca" placeholder="Ex: Samsung" required>
                </div>

                <div class="campo">
                    <label>Modelo <span class="obrigatorio">*</span></label>
                    <input type="text" name="modelo" placeholder="Ex: Digital Inverter" required>
                </div>

            </div>

            <div class="linha">

                <div class="campo">
                    <label>Tipo</label>
                    <select name="tipo">
                        <option value="">Selecione...</option>
                        <option value="Split">Split</option>
                        <option value="Janela">Janela</option>
                        <option value="Portátil">Portátil</option>
                        <option value="Cassete">Cassete</option>
                        <option value="Multi Split">Multi Split</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Capacidade (BTUs)</label>
                    <input type="text" name="capacidade_btu" placeholder="Ex: 12000">
                </div>

            </div>

            <div class="linha">

                <div class="campo">
                    <label>Número de Série</label>
                    <input type="text" name="numero_serie" placeholder="Ex: SN123456">
                </div>

                <div class="campo">
                    <label>Status</label>
                    <select name="status">
                        <option value="em_dia">Em dia</option>
                        <option value="atrasado">Atrasado</option>
                        <option value="pendente">Pendente</option>
                    </select>
                </div>

            </div>

            <div class="campo">
                <label>Próxima Manutenção</label>
                <input type="date" name="proxima_manutencao">
            </div>

            <div class="campo">
                <label>Observações</label>
                <textarea name="observacoes"></textarea>
            </div>

            <button type="submit" class="btn-salvar">Salvar Aparelho</button>

        </form>

    </div>

</div>

</body>
</html>