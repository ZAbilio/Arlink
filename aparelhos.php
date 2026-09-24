<?php
require_once 'includes/conexao.php';

// A tabela aparelhos só guarda cliente_id, então precisamos JOIN com clientes
// para exibir o nome. Também removidas data_instalacao e data_garantia,
// que não existem na tabela — usamos status e proxima_manutencao no lugar.
$sql = "SELECT 
            a.id, 
            c.nome AS cliente, 
            a.marca, 
            a.modelo, 
            a.tipo, 
            a.capacidade_btu, 
            a.numero_serie, 
            a.status,
            a.proxima_manutencao,
            a.criado_em
        FROM aparelhos a
        INNER JOIN clientes c ON a.cliente_id = c.id
        ORDER BY a.id DESC";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro ao buscar aparelhos: " . mysqli_error($conexao));
}

// Classe visual conforme o status cadastrado
function classeStatus($status) {
    switch (strtolower($status)) {
        case 'em_dia':
            return ['texto' => 'Em dia', 'classe' => 'status-em-dia'];
        case 'atrasado':
            return ['texto' => 'Atrasado', 'classe' => 'status-atrasado'];
        case 'pendente':
            return ['texto' => 'Pendente', 'classe' => 'status-pendente'];
        default:
            return ['texto' => $status, 'classe' => ''];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Aparelhos - AR Link</title>

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
            max-width: 1200px;
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

        .btn-novo {
            background: #198754;
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 6px;
            font-weight: bold;
        }

        .btn-novo:hover {
            background: #157347;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .tabela-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #1e3a5f;
            color: white;
            padding: 14px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 13px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .acoes {
            white-space: nowrap;
        }

        .btn {
            display: inline-block;
            padding: 7px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            color: white;
        }

        .btn-editar {
            background: #0d6efd;
        }

        .btn-editar:hover {
            background: #0b5ed7;
        }

        .btn-excluir {
            background: #dc3545;
        }

        .btn-excluir:hover {
            background: #bb2d3b;
        }

        .vazio {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .contador {
            padding: 15px 20px;
            color: #666;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }

        .badge-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-em-dia {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-atrasado {
            background: #f8d7da;
            color: #842029;
        }

        .status-pendente {
            background: #fff3cd;
            color: #664d03;
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

            th,
            td {
                padding: 10px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="topo">
        <h1>Aparelhos</h1>

        <a href="novos_aparelhos.php" class="btn-novo">
            + Novo Aparelho
        </a>
    </div>

    <div class="card">

        <div class="contador">
            <?php echo mysqli_num_rows($resultado); ?>
            aparelho(s) cadastrado(s)
        </div>

        <div class="tabela-container">

            <?php if (mysqli_num_rows($resultado) > 0): ?>

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Marca / Modelo</th>
                            <th>Tipo</th>
                            <th>BTUs</th>
                            <th>Nº de Série</th>
                            <th>Status</th>
                            <th>Próxima Manutenção</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($aparelho = mysqli_fetch_assoc($resultado)): ?>

                        <?php $status = classeStatus($aparelho['status']); ?>

                        <tr>

                            <td>
                                <?php echo $aparelho['id']; ?>
                            </td>

                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($aparelho['cliente']); ?>
                                </strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($aparelho['marca']); ?>
                                -
                                <?php echo htmlspecialchars($aparelho['modelo']); ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($aparelho['tipo'])
                                    ? htmlspecialchars($aparelho['tipo'])
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($aparelho['capacidade_btu'])
                                    ? htmlspecialchars($aparelho['capacidade_btu']) . ' BTUs'
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($aparelho['numero_serie'])
                                    ? htmlspecialchars($aparelho['numero_serie'])
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <span class="badge-status <?php echo $status['classe']; ?>">
                                    <?php echo htmlspecialchars($status['texto']); ?>
                                </span>
                            </td>

                            <td>
                                <?php
                                echo !empty($aparelho['proxima_manutencao'])
                                    ? date('d/m/Y', strtotime($aparelho['proxima_manutencao']))
                                    : '-';
                                ?>
                            </td>

                            <td class="acoes">

                                <a
                                    href="aparelho_editar.php?id=<?php echo $aparelho['id']; ?>"
                                    class="btn btn-editar"
                                >
                                    Editar
                                </a>

                                <a
                                    href="aparelho_excluir.php?id=<?php echo $aparelho['id']; ?>"
                                    class="btn btn-excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir este aparelho?');"
                                >
                                    Excluir
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="vazio">
                    <h3>Nenhum aparelho cadastrado</h3>
                    <p>Comece cadastrando um novo aparelho.</p>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>