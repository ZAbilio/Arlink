<?php
require_once 'includes/conexao.php';

// Busca todos os técnicos
$sql = "SELECT id, nome, telefone, email, endereco, cidade, criado_em
        FROM tecnicos
        ORDER BY id DESC";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro ao buscar técnicos: " . mysqli_error($conexao));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Técnicos - AR Link</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <div class="topo">
        <h1>Técnicos</h1>

        <a href="tecnico_cadastro.php" class="btn-novo">
            + Novo Técnico
        </a>
    </div>

    <div class="card">

        <div class="contador">
            <?php echo mysqli_num_rows($resultado); ?>
            técnico(s) cadastrado(s)
        </div>

        <div class="tabela-container">

            <?php if (mysqli_num_rows($resultado) > 0): ?>

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>E-mail</th>
                            <th>Endereço</th>
                            <th>Cidade</th>
                            <th>Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($tecnico = mysqli_fetch_assoc($resultado)): ?>

                        <tr>

                            <td>
                                <?php echo $tecnico['id']; ?>
                            </td>

                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($tecnico['nome']); ?>
                                </strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($tecnico['telefone']); ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($tecnico['email'])
                                    ? htmlspecialchars($tecnico['email'])
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($tecnico['endereco'])
                                    ? htmlspecialchars($tecnico['endereco'])
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($tecnico['cidade'])
                                    ? htmlspecialchars($tecnico['cidade'])
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo date(
                                    'd/m/Y H:i',
                                    strtotime($tecnico['criado_em'])
                                );
                                ?>
                            </td>

                            <td class="acoes">

                                <a
                                    href="tecnico_editar.php?id=<?php echo $tecnico['id']; ?>"
                                    class="btn btn-editar"
                                >
                                    Editar
                                </a>

                                <a
                                    href="tecnico_excluir.php?id=<?php echo $tecnico['id']; ?>"
                                    class="btn btn-excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir este técnico?');"
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
                    <h3>Nenhum técnico cadastrado</h3>
                    <p>Comece cadastrando um novo técnico.</p>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>