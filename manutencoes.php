<?php
require_once 'includes/conexao.php';

// Busca todas as manutenções, trazendo nomes via JOIN (a tabela só guarda os IDs)
$sql = "SELECT 
            m.id,
            c.nome AS cliente,
            CONCAT(a.marca, ' - ', a.modelo) AS equipamento,
            m.tipo_servico,
            t.nome AS tecnico,
            m.data_atendimento,
            m.valor_cobrado,
            m.proxima_visita,
            m.criado_em
        FROM manutencoes m
        INNER JOIN aparelhos a ON m.aparelho_id = a.id
        INNER JOIN clientes c ON a.cliente_id = c.id
        INNER JOIN tecnicos t ON m.tecnico_id = t.id
        ORDER BY m.id DESC";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro ao buscar manutenções: " . mysqli_error($conexao));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manutenções - AR Link</title>

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
        <h1>Manutenções</h1>

        <a href="manutencao_cadastro.php" class="btn-novo">
            + Nova Manutenção
        </a>
    </div>

    <div class="card">

        <div class="contador">
            <?php echo mysqli_num_rows($resultado); ?>
            manutenção(ões) registrada(s)
        </div>

        <div class="tabela-container">

            <?php if (mysqli_num_rows($resultado) > 0): ?>

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Equipamento</th>
                            <th>Tipo de Serviço</th>
                            <th>Técnico</th>
                            <th>Data Atendimento</th>
                            <th>Valor</th>
                            <th>Próxima Visita</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($manutencao = mysqli_fetch_assoc($resultado)): ?>

                        <tr>

                            <td>
                                <?php echo $manutencao['id']; ?>
                            </td>

                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($manutencao['cliente']); ?>
                                </strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($manutencao['equipamento']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($manutencao['tipo_servico']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($manutencao['tecnico']); ?>
                            </td>

                            <td>
                                <?php
                                echo date(
                                    'd/m/Y',
                                    strtotime($manutencao['data_atendimento'])
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($manutencao['valor_cobrado'])
                                    ? 'R$ ' . number_format($manutencao['valor_cobrado'], 2, ',', '.')
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($manutencao['proxima_visita'])
                                    ? date('d/m/Y', strtotime($manutencao['proxima_visita']))
                                    : '-';
                                ?>
                            </td>

                            <td class="acoes">

                                
                                    href="manutencao_editar.php?id=<?php echo $manutencao['id']; ?>"
                                    class="btn btn-editar"
                                >
                                    Editar
                                </a>

                                
                                    href="manutencao_excluir.php?id=<?php echo $manutencao['id']; ?>"
                                    class="btn btn-excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir esta manutenção?');"
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
                    <h3>Nenhuma manutenção registrada</h3>
                    <p>Comece registrando uma nova manutenção.</p>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>