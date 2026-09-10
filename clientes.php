<?php
require_once 'includes/conexao.php';

// Busca todos os clientes
$sql = "SELECT id, nome, telefone, email, endereco, cidade, criado_em
        FROM clientes
        ORDER BY id DESC";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro ao buscar clientes: " . mysqli_error($conexao));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Clientes - AR Link</title>

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
        <h1>Clientes</h1>

        <a href="cliente_cadastro.php" class="btn-novo">
            + Novo Cliente
        </a>
    </div>

    <div class="card">

        <div class="contador">
            <?php echo mysqli_num_rows($resultado); ?>
            cliente(s) cadastrado(s)
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

                    <?php while ($cliente = mysqli_fetch_assoc($resultado)): ?>

                        <tr>

                            <td>
                                <?php echo $cliente['id']; ?>
                            </td>

                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($cliente['nome']); ?>
                                </strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($cliente['telefone']); ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($cliente['email'])
                                    ? htmlspecialchars($cliente['email'])
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($cliente['endereco'])
                                    ? htmlspecialchars($cliente['endereco'])
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($cliente['cidade'])
                                    ? htmlspecialchars($cliente['cidade'])
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo date(
                                    'd/m/Y H:i',
                                    strtotime($cliente['criado_em'])
                                );
                                ?>
                            </td>

                            <td class="acoes">

                                <a
                                    href="cliente_editar.php?id=<?php echo $cliente['id']; ?>"
                                    class="btn btn-editar"
                                >
                                    Editar
                                </a>

                                <a
                                    href="cliente_excluir.php?id=<?php echo $cliente['id']; ?>"
                                    class="btn btn-excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir este cliente?');"
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
                    <h3>Nenhum cliente cadastrado</h3>
                    <p>Comece cadastrando um novo cliente.</p>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>