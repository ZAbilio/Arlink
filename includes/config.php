<?php
// Caminho da pasta onde o ARLINK foi instalado (ex.: /arlink/).
// Assim, a pasta pode ter outro nome sem quebrar CSS e links internos.
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/');
date_default_timezone_set('America/Sao_Paulo');
require_once __DIR__ . '/validacao.php';
// Formularios deste sistema recebem somente valores simples.
foreach ([$_GET, $_POST] as $entrada) {
    foreach ($entrada as $campo => $valor) {
        if (!is_string($valor)) {
            http_response_code(400);
            exit('Formato de entrada invalido.');
        }
        if (in_array($campo, ['id', 'cliente_id', 'aparelho_id'], true)
            && !preg_match('/^[0-9]{1,10}$/D', $valor)) {
            http_response_code(400);
            exit('Identificador invalido.');
        }
    }
}
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => BASE_URL
    ]);
}
