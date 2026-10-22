<?php
require_once 'includes/conexao.php';

$paginaLista = 'tecnicos.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: $paginaLista");
    exit;
}

$erros = [];

$stmt = mysqli_prepare($conexao, "SELECT id, nome, telefone, email FROM tecnicos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$tecnico = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$tecnico) {
    die("Técnico não encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email    = trim($_POST['email'] ?? '');

    if ($nome === '') {
        $erros[] = "O nome é obrigatório.";
    } elseif (mb_strlen($nome) > 150) {
        $erros[] = "O nome deve ter no máximo 150 caracteres.";
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Informe um e-mail válido.";
    }

    if ($telefone !== '' && !preg_match('/^[0-9()+\-\s]{8,20}$/', $telefone)) {
        $erros[] = "Informe um telefone válido.";
    }

    $tecnico['nome']     = $nome;
    $tecnico['telefone'] = $telefone;
    $tecnico['email']    = $email;

    if (empty($erros)) {
        $telefoneDb = $telefone !== '' ? $telefone : null;
        $emailDb    = $email !== '' ? $email : null;

        $stmt = mysqli_prepare(
            $conexao,
            "UPDATE tecnicos SET nome = ?, telefone = ?, email = ? WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "sssi", $nome, $telefoneDb, $emailDb, $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: $paginaLista?msg=editado");
            exit;
        }

        $erros[] = "Erro ao atualizar técnico: " . mysqli_error($conexao);
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Técnico - AR Link</title>

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
            max-width: 600px;
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

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 30px;
        }

        .campo {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: bold;
            color: #1e3a5f;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
        }

        .acoes {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-salvar {
            background: #198754;
        }

        .btn-salvar:hover {
            background: #157347;
        }

        .btn-cancelar {
            background: #6c757d;
        }

        .btn-cancelar:hover {
            background: #5c636a;
        }

        .erros {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .erros ul {
            margin-left: 18px;
        }

        @media (max-width: 700px) {
            .topo h1 {
                font-size: 24px;
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
        <h1>Editar Técnico</h1>
    </div>

    <div class="card">

        <?php if (!empty($erros)): ?>
            <div class="erros">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="tecnico_editar.php?id=<?php echo (int) $id; ?>">

            <div class="campo">
                <label for="nome">Nome *</label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    maxlength="150"
                    required
                    value="<?php echo htmlspecialchars($tecnico['nome']); ?>"
                >
            </div>

            <div class="campo">
                <label for="telefone">Telefone</label>
                <input
                    type="tel"
                    id="telefone"
                    name="telefone"
                    maxlength="20"
                    placeholder="(00) 00000-0000"
                    value="<?php echo htmlspecialchars($tecnico['telefone'] ?? ''); ?>"
                >
            </div>

            <div class="campo">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="150"
                    placeholder="exemplo@email.com"
                    value="<?php echo htmlspecialchars($tecnico['email'] ?? ''); ?>"
                >
            </div>

            <div class="acoes">
                <button type="submit" class="btn btn-salvar">Salvar alterações</button>
                <a href="<?php echo $paginaLista; ?>" class="btn btn-cancelar">Cancelar</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>