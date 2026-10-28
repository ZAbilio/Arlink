<?php
// Limites acompanham as colunas do banco. TEXT admite ate 65535 bytes.
function validar_dados($dados, $limites) {
    foreach ($limites as $campo => $limite) {
        $texto = $dados[$campo] ?? '';
        if (mb_strlen($texto, 'UTF-8') > $limite || strlen($texto) > 65535) {
            return 'Texto muito longo no campo: ' . $campo . '.';
        }
    }
    if (!empty($dados['email']) && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        return 'E-mail invalido.';
    }
    foreach (['data_atendimento', 'proxima_visita', 'proxima_manutencao'] as $campo) {
        $data = $dados[$campo] ?? '';
        if ($data === '') continue;
        $partes = explode('-', $data);
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $data)
            || (int)$partes[0] < 1000 || !checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])) {
            return 'Data invalida no campo: ' . $campo . '.';
        }
    }
    if (!empty($dados['proxima_visita']) && $dados['proxima_visita'] < $dados['data_atendimento']) {
        return 'A proxima visita nao pode anteceder o atendimento.';
    }
    if (isset($dados['valor_cobrado']) && $dados['valor_cobrado'] !== ''
        && !preg_match('/^[0-9]{1,8}([.,][0-9]{1,2})?$/D', $dados['valor_cobrado'])) {
        return 'Valor invalido. Use ate 8 digitos e duas casas decimais, sem sinal.';
    }
    if (isset($dados['tipo_servico']) && !in_array($dados['tipo_servico'],
        ['Preventiva','Corretiva','Higienização','Instalação','Desinstalação','Revisão Geral'], true)) {
        return 'Tipo de servico invalido.';
    }
    if (isset($dados['tipo']) && !in_array($dados['tipo'],
        ['', 'Split Hi Wall','Split Cassete','Split Piso Teto','Janela','Portátil'], true)) {
        return 'Tipo de aparelho invalido.';
    }
    return '';
}

function registro_existe($conexao, $tabela, $id) {
    // Somente tabelas fixas do sistema podem compor a consulta.
    if (!in_array($tabela, ['clientes','aparelhos','tecnicos'], true) || $id <= 0) return false;
    $consulta = mysqli_prepare($conexao, "SELECT id FROM $tabela WHERE id=?");
    mysqli_stmt_bind_param($consulta, 'i', $id);
    mysqli_stmt_execute($consulta);
    return mysqli_num_rows(mysqli_stmt_get_result($consulta)) === 1;
}
