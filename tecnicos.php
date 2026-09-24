<?php
require_once 'includes/conexao.php';

// A tabela tecnicos não possui endereco/cidade, por isso não são buscados aqui
$sql = "SELECT id, nome, telefone, email, criado_em
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
        <h1>Técnicos</h1>

        <a href="novos_tecnicos.php" class="btn-novo">
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
                            <th>Criado em</th>
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
                                <?php
                                echo !empty($tecnico['telefone'])
                                    ? htmlspecialchars($tecnico['telefone'])
                                    : '-';
                                ?>
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
                    <p>Comece se cadastrando.</p>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>