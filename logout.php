<?php
require_once 'includes/config.php';
require_once 'includes/sessao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validar_formulario()) {
    http_response_code(403);
    exit('Acao nao autorizada.');
}
$_SESSION = [];
$parametros = session_get_cookie_params();
setcookie(session_name(), '', [
    'expires' => time() - 3600,
    'path' => $parametros['path'],
    'secure' => $parametros['secure'],
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_destroy();
header('Location: login.php');
exit;
