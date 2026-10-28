<?php
// Um token por sessao impede que outro site envie formularios em nome do tecnico.
// sessao.php (ou login.php) ja chama session_start().
function token_formulario() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validar_formulario() {
    $recebido = $_POST['csrf_token'] ?? '';
    return is_string($recebido)
        && hash_equals(token_formulario(), $recebido);
}
