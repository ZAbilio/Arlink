<?php
require_once 'includes/conexao.php';

// Busca todos os aparelhos
$sql = "SELECT id, cliente, marca, modelo, tipo, capacidade_btu, numero_serie, data_instalacao, data_garantia, criado_em
        FROM aparelhos
        ORDER BY id DESC";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro ao buscar aparelhos: " . mysqli_error($conexao));
}

// Verifica se a garantia ainda está ativa
function statusGarantia($dataGarantia) {
    if (empty($dataGarantia)) {
        return null;
    }

    $hoje = strtotime(date('Y-m-d'));
    $venc = strtotime($dataGarantia);

    return $venc >= $hoje
        ? ['texto' => 'Em garantia', 'classe' => 'garantia-ativa']
        : ['texto' => 'Garantia vencida', 'classe' => 'garantia-vencida'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Aparelhos - AR Link</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <div class="topo">
        <h1>Aparelhos</h1>

        <a href="aparelho_cadastro.php" class="btn-novo">
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
                            <th>Instalação</th>
                            <th>Garantia</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($aparelho = mysqli_fetch_assoc($resultado)): ?>

                        <?php $garantia = statusGarantia($aparelho['data_garantia']); ?>

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
                                <?php echo htmlspecialchars($aparelho['tipo']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($aparelho['capacidade_btu']); ?>
                                BTUs
                            </td>

                            <td>
                                <?php
                                echo !empty($aparelho['numero_serie'])
                                    ? htmlspecialchars($aparelho['numero_serie'])
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($aparelho['data_instalacao'])
                                    ? date('d/m/Y', strtotime($aparelho['data_instalacao']))
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php if ($garantia): ?>
                                    <span class="badge-garantia <?php echo $garantia['classe']; ?>">
                                        <?php echo $garantia['texto']; ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
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