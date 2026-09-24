<?php
require_once 'includes/conexao.php';

$erro = '';

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $aparelho_id          = $_POST['aparelho_id'];
    $tecnico_id           = $_POST['tecnico_id'];
    $data_atendimento     = $_POST['data_atendimento'];
    $tipo_servico         = trim($_POST['tipo_servico']);
    $diagnostico          = trim($_POST['diagnostico']);
    $servico_realizado    = trim($_POST['servico_realizado']);
    $pecas_utilizadas     = trim($_POST['pecas_utilizadas']);
    $valor_cobrado        = $_POST['valor_cobrado'] !== '' ? $_POST['valor_cobrado'] : null;
    $proxima_visita       = $_POST['proxima_visita'] !== '' ? $_POST['proxima_visita'] : null;
    $observacoes_internas = trim($_POST['observacoes_internas']);

    if (empty($aparelho_id) || empty($tecnico_id) || empty($data_atendimento) || empty($tipo_servico) || empty($diagnostico) || empty($servico_realizado)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {

        $sql = "INSERT INTO manutencoes 
                    (aparelho_id, tecnico_id, data_atendimento, tipo_servico, diagnostico, servico_realizado, pecas_utilizadas, valor_cobrado, proxima_visita, observacoes_internas)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conexao, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "iisssssdss",
            $aparelho_id,
            $tecnico_id,
            $data_atendimento,
            $tipo_servico,
            $diagnostico,
            $servico_realizado,
            $pecas_utilizadas,
            $valor_cobrado,
            $proxima_visita,
            $observacoes_internas
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: manutencoes.php");
            exit;
        } else {
            $erro = "Erro ao salvar: " . mysqli_error($conexao);
        }
    }
}

// Busca aparelhos (com nome do cliente) para o select
$sqlAparelhos = "SELECT a.id, a.marca, a.modelo, c.nome AS cliente
                 FROM aparelhos a
                 INNER JOIN clientes c ON a.cliente_id = c.id
                 ORDER BY c.nome";
$resAparelhos = mysqli_query($conexao, $sqlAparelhos);

// Busca técnicos para o select
$sqlTecnicos = "SELECT id, nome FROM tecnicos ORDER BY nome";
$resTecnicos = mysqli_query($conexao, $sqlTecnicos);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nova Manutenção - AR Link</title>

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
        <h1>Nova Manutenção</h1>
        <a href="manutencoes.php" class="btn-voltar">← Voltar</a>
    </div>

    <div class="card">

        <?php if (!empty($erro)): ?>
            <div class="alerta"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="manutencao_cadastro.php">

            <div class="linha">

                <div class="campo">
                    <label>Aparelho <span class="obrigatorio">*</span></label>
                    <select name="aparelho_id" required>
                        <option value="">Selecione...</option>
                        <?php while ($ap = mysqli_fetch_assoc($resAparelhos)): ?>
                            <option value="<?php echo $ap['id']; ?>">
                                <?php echo htmlspecialchars($ap['cliente'] . ' - ' . $ap['marca'] . ' ' . $ap['modelo']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="campo">
                    <label>Técnico <span class="obrigatorio">*</span></label>
                    <select name="tecnico_id" required>
                        <option value="">Selecione...</option>
                        <?php while ($tec = mysqli_fetch_assoc($resTecnicos)): ?>
                            <option value="<?php echo $tec['id']; ?>">
                                <?php echo htmlspecialchars($tec['nome']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

            </div>

            <div class="linha">

                <div class="campo">
                    <label>Data do Atendimento <span class="obrigatorio">*</span></label>
                    <input type="date" name="data_atendimento" required>
                </div>

                <div class="campo">
                    <label>Tipo de Serviço <span class="obrigatorio">*</span></label>
                    <input type="text" name="tipo_servico" placeholder="Ex: Manutenção preventiva" required>
                </div>

            </div>

            <div class="campo">
                <label>Diagnóstico <span class="obrigatorio">*</span></label>
                <textarea name="diagnostico" required></textarea>
            </div>

            <div class="campo">
                <label>Serviço Realizado <span class="obrigatorio">*</span></label>
                <textarea name="servico_realizado" required></textarea>
            </div>

            <div class="campo">
                <label>Peças Utilizadas</label>
                <textarea name="pecas_utilizadas"></textarea>
            </div>

            <div class="linha">

                <div class="campo">
                    <label>Valor Cobrado (R$)</label>
                    <input type="number" step="0.01" name="valor_cobrado" placeholder="Ex: 150.00">
                </div>

                <div class="campo">
                    <label>Próxima Visita</label>
                    <input type="date" name="proxima_visita">
                </div>

            </div>

            <div class="campo">
                <label>Observações Internas</label>
                <textarea name="observacoes_internas"></textarea>
            </div>

            <button type="submit" class="btn-salvar">Salvar Manutenção</button>

        </form>

    </div>

</div>

</body>
</html>