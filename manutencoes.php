<?php
require_once 'includes/conexao.php';

// Busca todas as manutenções
$sql = "SELECT id, cliente, equipamento, tipo_servico, tecnico, data_manutencao, status, criado_em
        FROM manutencoes
        ORDER BY id DESC";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro ao buscar manutenções: " . mysqli_error($conexao));
}

function classeStatus($status) {
    switch (strtolower($status)) {
        case 'agendado':
            return 'status-agendado';
        case 'em andamento':
            return 'status-andamento';
        case 'concluído':
        case 'concluido':
            return 'status-concluido';
        case 'cancelado':
            return 'status-cancelado';
        default:
            return '';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manutenções - AR Link</title>

    <link rel="stylesheet" href="style.css">
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
                            <th>Data</th>
                            <th>Status</th>
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
                                <?php
                                echo !empty($manutencao['tecnico'])
                                    ? htmlspecialchars($manutencao['tecnico'])
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo date(
                                    'd/m/Y',
                                    strtotime($manutencao['data_manutencao'])
                                );
                                ?>
                            </td>

                            <td>
                                <span class="badge-status <?php echo classeStatus($manutencao['status']); ?>">
                                    <?php echo htmlspecialchars($manutencao['status']); ?>
                                </span>
                            </td>

                            <td class="acoes">

                                <a
                                    href="manutencao_editar.php?id=<?php echo $manutencao['id']; ?>"
                                    class="btn btn-editar"
                                >
                                    Editar
                                </a>

                                <a
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