<?php
require_once 'includes/conexao.php';

$erro = '';

$id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: aparelhos.php');
    exit;
}

$statusOpcoes = [
    'em_dia'   => 'Em dia',
    'pendente' => 'Pendente',
    'atrasado' => 'Atrasado',
];

// Lista de clientes para o select
$clientes = [];
$resClientes = mysqli_query($conexao, "SELECT id, nome FROM clientes ORDER BY nome ASC");
if ($resClientes) {
    while ($linha = mysqli_fetch_assoc($resClientes)) {
        $clientes[] = $linha;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id         = (int) ($_POST['cliente_id'] ?? 0);
    $marca              = trim($_POST['marca'] ?? '');
    $modelo             = trim($_POST['modelo'] ?? '');
    $tipo               = trim($_POST['tipo'] ?? '');
    $capacidade_btu     = trim($_POST['capacidade_btu'] ?? '');
    $numero_serie       = trim($_POST['numero_serie'] ?? '');
    $status             = $_POST['status'] ?? 'em_dia';
    $proxima_manutencao = trim($_POST['proxima_manutencao'] ?? '');

    if ($cliente_id <= 0 || $marca === '' || $modelo === '') {
        $erro = 'Cliente, marca e modelo são obrigatórios.';
    } elseif (!isset($statusOpcoes[$status])) {
        $erro = 'Status inválido.';
    } elseif ($capacidade_btu !== '' && !ctype_digit($capacidade_btu)) {
        $erro = 'A capacidade (BTUs) deve conter apenas números.';
    } else {
        // Campos opcionais vazios viram NULL no banco
        $tipo               = $tipo !== '' ? $tipo : null;
        $capacidade_btu     = $capacidade_btu !== '' ? (int) $capacidade_btu : null;
        $numero_serie       = $numero_serie !== '' ? $numero_serie : null;
        $proxima_manutencao = $proxima_manutencao !== '' ? $proxima_manutencao : null;

        try {
            $sql = "UPDATE aparelhos
                    SET cliente_id = ?, marca = ?, modelo = ?, tipo = ?,
                        capacidade_btu = ?, numero_serie = ?, status = ?,
                        proxima_manutencao = ?
                    WHERE id = ?";
            $stmt = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                'isssisssi',
                $cliente_id,
                $marca,
                $modelo,
                $tipo,
                $capacidade_btu,
                $numero_serie,
                $status,
                $proxima_manutencao,
                $id
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            header('Location: aparelhos.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $erro = 'Já existe um aparelho com este número de série.';
            } else {
                $erro = 'Erro ao atualizar aparelho: ' . $e->getMessage();
            }
        }
    }

    // Mantém o que foi digitado em caso de erro
    $aparelho = [
        'id'                 => $id,
        'cliente_id'         => $cliente_id,
        'marca'              => $_POST['marca'] ?? '',
        'modelo'             => $_POST['modelo'] ?? '',
        'tipo'               => $_POST['tipo'] ?? '',
        'capacidade_btu'     => $_POST['capacidade_btu'] ?? '',
        'numero_serie'       => $_POST['numero_serie'] ?? '',
        'status'             => $status,
        'proxima_manutencao' => $_POST['proxima_manutencao'] ?? '',
    ];
} else {
    $sql = "SELECT id, cliente_id, marca, modelo, tipo, capacidade_btu,
                   numero_serie, status, proxima_manutencao
            FROM aparelhos
            WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $aparelho = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    if (!$aparelho) {
        header('Location: aparelhos.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Aparelho - AR Link</title>

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
            max-width: 760px;
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

        input,
        select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ccd2d9;
            border-radius: 6px;
            font-size: 15px;
            background: white;
        }

        input:focus,
        select:focus {
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
        <h1>Editar Aparelho</h1>

        <a href="aparelhos.php" class="btn-voltar">← Voltar</a>
    </div>

    <div class="card">

        <?php if ($erro !== ''): ?>
            <div class="alerta"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="aparelho_editar.php?id=<?php echo (int) $aparelho['id']; ?>">

            <input type="hidden" name="id" value="<?php echo (int) $aparelho['id']; ?>">

            <div class="campo">
                <label for="cliente_id">Cliente <span>*</span></label>
                <select id="cliente_id" name="cliente_id" required>
                    <option value="">Selecione um cliente</option>
                    <?php foreach ($clientes as $c): ?>
                        <option
                            value="<?php echo (int) $c['id']; ?>"
                            <?php echo ((int) $aparelho['cliente_id'] === (int) $c['id']) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($c['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="linha">
                <div class="campo">
                    <label for="marca">Marca <span>*</span></label>
                    <input
                        type="text"
                        id="marca"
                        name="marca"
                        value="<?php echo htmlspecialchars($aparelho['marca']); ?>"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="modelo">Modelo <span>*</span></label>
                    <input
                        type="text"
                        id="modelo"
                        name="modelo"
                        value="<?php echo htmlspecialchars($aparelho['modelo']); ?>"
                        required
                    >
                </div>
            </div>

            <div class="linha">
                <div class="campo">
                    <label for="tipo">Tipo</label>
                    <input
                        type="text"
                        id="tipo"
                        name="tipo"
                        list="lista-tipos"
                        value="<?php echo htmlspecialchars($aparelho['tipo'] ?? ''); ?>"
                    >
                    <datalist id="lista-tipos">
                        <option value="Split">
                        <option value="Janela">
                        <option value="Piso Teto">
                        <option value="Cassete">
                        <option value="Portátil">
                    </datalist>
                </div>

                <div class="campo">
                    <label for="capacidade_btu">Capacidade (BTUs)</label>
                    <input
                        type="number"
                        id="capacidade_btu"
                        name="capacidade_btu"
                        min="0"
                        step="1"
                        value="<?php echo htmlspecialchars($aparelho['capacidade_btu'] ?? ''); ?>"
                    >
                </div>
            </div>

            <div class="campo">
                <label for="numero_serie">Nº de Série</label>
                <input
                    type="text"
                    id="numero_serie"
                    name="numero_serie"
                    value="<?php echo htmlspecialchars($aparelho['numero_serie'] ?? ''); ?>"
                >
            </div>

            <div class="linha">
                <div class="campo">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <?php foreach ($statusOpcoes as $valor => $texto): ?>
                            <option
                                value="<?php echo $valor; ?>"
                                <?php echo (strtolower($aparelho['status'] ?? '') === $valor) ? 'selected' : ''; ?>
                            >
                                <?php echo $texto; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="proxima_manutencao">Próxima Manutenção</label>
                    <input
                        type="date"
                        id="proxima_manutencao"
                        name="proxima_manutencao"
                        value="<?php echo htmlspecialchars($aparelho['proxima_manutencao'] ?? ''); ?>"
                    >
                </div>
            </div>

            <div class="botoes">
                <button type="submit" class="btn-salvar">Salvar alterações</button>
                <a href="aparelhos.php" class="btn-cancelar">Cancelar</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>